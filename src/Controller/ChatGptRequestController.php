<?php

namespace App\Controller;

use App\Communicator\ChatGptRequest;
use App\Communicator\DatabaseInsert;
use App\Entity\Article;
use App\Entity\Project;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChatGptRequestController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    private string $apiKey;
    private string $organizationKey;

    #[Route('/api', name: 'app_chat_gpt_request')]
    public function index(): JsonResponse
    {
        $msg = "Usługi ślusarskie w Poznaniu'";
        $response = [];

        $entityManager = $this->entityManager;

        $project = new Project('Test project', $msg, 2, 50, true);
        $databaseInsert = new DatabaseInsert($entityManager, $project);
        $databaseInsert->saveProject();

        /*
                $request = new ChatGptRequest($this->getApiKey(), $this->getOrganizationKey(), $msg, 70, true);
                $response = $request->send();
        */
        for ($i = 0; $i < $project->getNumberOfArticles(); $i++) {
            $chatGptRequest = new ChatGptRequest(
                $this->getApiKey(),
                $this->getOrganizationKey(),
                $project->getTheme(),
                $project->getTextsLength(),
                $project->isWithTitle());
            $receivedData = $chatGptRequest->send();

            $article = new Article();
            $article->setProject($project);
            $article->setContent($receivedData);
            $article->setIsUsed(false);

            if ($project->isWithTitle() === true) {
                $article->setTitle($article->getTitleFromString());
            }

            $databaseInsert->saveArticle($article);
            $response[] = $receivedData;

            sleep(5);
        }
        return $this->json([
            $response
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
}
