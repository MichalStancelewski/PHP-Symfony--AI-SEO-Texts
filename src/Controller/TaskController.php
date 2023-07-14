<?php

namespace App\Controller;

use App\Communicator\ChatGptRequest;
use App\Communicator\DatabaseInsert;
use App\Entity\Article;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\ExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;

class TaskController extends AbstractController
{
    private const MAX_MINUTES_JOB = 3;
    private const MAX_CONCURRENT_JOB = 8;
    private string $apiKey;
    private string $organizationKey;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskRepository         $taskRepository,
        private ProjectRepository      $projectRepository
    )
    {
    }


    #[Route('/tasks-cron/', name: 'app_tasks')]
    public function index(LoggerInterface $logger): JsonResponse
    {
        set_time_limit(600);

        $this->logger = $logger;
        $this->deleteExportedOlderThan(7);

        $this->checkProjects();
        $tasks = $this->checkTasks();

        $taskRepository = $this->taskRepository;
        foreach ($tasks as $t) {
            $taskRepository->setStatusPending($t);
        }

        for ($i = 0; $i < count($tasks); $i++) {
            $this->sendGptRequest($tasks[$i], $logger);
            sleep(1);
        }

        return $this->json([
        ]);
    }


    private function getApiKey(): string
    {
        return strval($this->getParameter('CHAT_GPT_API'));
    }

    private function getOrganizationKey(): string
    {
        return strval($this->getParameter('CHAT_GPT_ORGANIZATION'));
    }

    private function checkProjects()
    {
        $projectsRepository = $this->projectRepository;
        $pendingProjects = $projectsRepository->findAllPending();

        foreach ($pendingProjects as $project) {
            if (count($project->getArticles()) == $project->getNumberOfArticles()) {
                $projectsRepository->setStatusDone($project);
            }
        }
    }

    private function checkTasks(): array
    {
        $tasks = [];
        $maxSpots = TaskController::MAX_CONCURRENT_JOB;
        $taskRepository = $this->taskRepository;
        $currentTime = new \DateTime('now', new \DateTimeZone('Europe/Warsaw'));

        $pendingTasks = $taskRepository->findAllPending();
        foreach ($pendingTasks as $pT) {
            $sinceStart = $pT->getLastChangedDate()->diff($currentTime);
            $minutes = $sinceStart->days * 24 * 60;
            $minutes += $sinceStart->h * 60;
            $minutes += $sinceStart->i;

            if ($minutes > TaskController::MAX_MINUTES_JOB) {
                $taskRepository->setStatusFailed($pT);
                unset($pT);
            }
        }

        if (count($pendingTasks) >= TaskController::MAX_CONCURRENT_JOB) {
            return $tasks;
        } else {
            $maxSpots = $maxSpots - count($pendingTasks);
        }

        $failedTasks = $taskRepository->findAllFailed();
        if (count($failedTasks) > 0) {
            for ($i = 0; $i < count($failedTasks); $i++) {
                $tasks[] = $failedTasks[$i];
                if (count($tasks) == $maxSpots) {
                    return $tasks;
                }
            }
        }

        if (count($tasks) >= TaskController::MAX_CONCURRENT_JOB) {
            return $tasks;
        }

        $newTasks = $taskRepository->findAllNew();
        if (count($newTasks) > 0) {
            for ($i = 0; $i < count($newTasks); $i++) {
                $tasks[] = $newTasks[$i];
                if (count($tasks) == $maxSpots) {
                    return $tasks;
                }
            }
        }

        return $tasks;
    }

    private function sendGptRequest(Task $task, LoggerInterface $logger): void
    {
        $taskRepository = $this->taskRepository;

        $chatGptRequest = new ChatGptRequest(
            $this->getApiKey(),
            $this->getOrganizationKey(),
            $task->getTheme(),
            $task->getLength(),
            $task->isWithTitle());
        $receivedData = $chatGptRequest->sendAndGetNewArticle($task->getLanguage());

        if (!is_string($receivedData)) {
            $logger->error('Error code 1: {error}', [
                'error' => json_encode($receivedData, JSON_PRETTY_PRINT),
            ]);
            return;
        }

        $article = new Article();
        $article->setProject($task->getProject());
        $article->setIsUsed(false);
        $article->setContent($receivedData);
        if ($task->isWithTitle() === true) {
            $article->setTitle($article->getTitleFromString());
        }
        $article->setContent($article->getFormatedContentFromString());

        $articleWithHtml = $chatGptRequest->sendAndGetWithHtml($article->getContent(), $article->getProject()->getLanguage());
        if (!is_string($articleWithHtml)) {
            $logger->error('Error code 2: {error}', [
                'error' => json_encode($articleWithHtml, JSON_PRETTY_PRINT),
            ]);
        } else {
            $article->setContent($articleWithHtml);
        }

        $entityManager = $this->entityManager;
        $databaseInsert = new DatabaseInsert($entityManager, $task->getProject());
        $databaseInsert->saveArticle($article);

        $taskRepository->remove($task, true);
    }

    private function deleteExportedOlderThan(int $days): void
    {
        $filesystem = new Filesystem();
        $dateCurrent = date("Y-m-d");

        $dir = 'uploads/export/';
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);

        foreach ($files as $file) {
            $dateOfFile = date("Y-m-d", filectime($dir . $file));
            $dateOfExpiration = date("Y-m-d", strtotime($dateOfFile . '+ ' . $days . ' Days'));

            $path = './' . $dir . $file;

            if (strtotime($dateOfExpiration) < strtotime($dateCurrent)) {
                if ($filesystem->exists([$path])) {
                    try {
                        $filesystem->remove($path);
                    }
                    catch(ExceptionInterface $exception) {
                        $this->logger->error('Delete files | Files delete error: ' . $path . ' | ' . $exception->getMessage());
                    }
                }
            }
        }
    }

}
