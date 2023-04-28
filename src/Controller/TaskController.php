<?php

namespace App\Controller;

use App\Communicator\ChatGptRequest;
use App\Communicator\DatabaseInsert;
use App\Entity\Article;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskRepository         $taskRepository
    )
    {
    }

    private string $apiKey;
    private string $organizationKey;

    #[Route('/tasks', name: 'app_tasks')]
    public function index(): JsonResponse
    {
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

            if ($minutes > 10) {
                $taskRepository->setStatusFailed($pT);
                unset($pT);
            }
        }

        if (count($pendingTasks) >= 3) {
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

        if (count($tasks) >= 3) {
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

    private function sendGptRequest(Task $task): void
    {
        $taskRepository = $this->taskRepository;

        $chatGptRequest = new ChatGptRequest(
            $this->getApiKey(),
            $this->getOrganizationKey(),
            $task->getTheme(),
            $task->getLength(),
            $task->isWithTitle());
        $receivedData = $chatGptRequest->send();

        $article = new Article();
        $article->setProject($task->getProject());
        $article->setIsUsed(false);
        $article->setContent($receivedData);
        if ($task->isWithTitle() === true) {
            $article->setTitle($article->getTitleFromString());
        }
        $article->setContent($article->getFormatedContentFromString());

        $entityManager = $this->entityManager;
        $databaseInsert = new DatabaseInsert($entityManager, $task->getProject());
        $databaseInsert->saveArticle($article);

        $taskRepository->remove($task, true);
    }

}
