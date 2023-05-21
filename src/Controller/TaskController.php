<?php

namespace App\Controller;

use App\Communicator\ChatGptRequest;
use App\Communicator\DatabaseInsert;
use App\Entity\Article;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private const MAX_MINUTES_JOB = 1;
    private const MAX_CONCURRENT_JOB = 1;
    private string $apiKey;
    private string $organizationKey;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskRepository         $taskRepository,
        private ProjectRepository      $projectRepository
    )
    {
    }


    #[Route('/tasks', name: 'app_tasks')] /* set to '/tasks-cron/ in production */
    public function index(): JsonResponse
    {
        $this->checkProjects();
        $tasks = $this->checkTasks();

        $taskRepository = $this->taskRepository;
        foreach ($tasks as $t) {
            $taskRepository->setStatusPending($t);
        }

        for ($i = 0; $i < count($tasks); $i++) {
            $this->sendGptRequest($tasks[$i]);
        }

        return $this->json([
            dd($tasks)
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

        foreach ($pendingProjects as $project){
            if (count($project->getArticles()) == $project->getNumberOfArticles()){
                $projectsRepository->setStatusDone($project);
            }
        }
    }

    private function checkTasks(): array
    {
        $tasks = [];
        $maxSpots = 3;
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
        sleep(3);
        $failedTasks = $taskRepository->findAllFailed();
        if (count($failedTasks) > 0) {
            for ($i = 0; $i < count($failedTasks); $i++) {
                $tasks[] = $failedTasks[$i];
                if (count($tasks) == $maxSpots) {
                    return $tasks;
                }
            }
        }

        if (count($tasks) >= 3) {
            return $tasks;
        }
        sleep(3);
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

    private function sendGptRequest(Task $task): void
    {
        $taskRepository = $this->taskRepository;

        $chatGptRequest = new ChatGptRequest(
            $this->getApiKey(),
            $this->getOrganizationKey(),
            $task->getTheme(),
            $task->getLength(),
            $task->isWithTitle());
        $receivedData = $chatGptRequest->sendAndGetNewArticle();

        $article = new Article();
        $article->setProject($task->getProject());
        $article->setIsUsed(false);
        $article->setContent($receivedData);
        if ($task->isWithTitle() === true) {
            $article->setTitle($article->getTitleFromString());
        }
        $article->setContent($article->getFormatedContentFromString());
        $article->setContent($chatGptRequest->sendAndGetWithHtml($article->getContent()));

        $entityManager = $this->entityManager;
        $databaseInsert = new DatabaseInsert($entityManager, $task->getProject());
        $databaseInsert->saveArticle($article);

        $taskRepository->remove($task, true);
    }

}
