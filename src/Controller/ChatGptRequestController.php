<?php

namespace App\Controller;

use App\Communicator\ChatGptRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChatGptRequestController extends AbstractController
{
    private string $apiKey;
    private string $organizationKey;

    #[Route('/api', name: 'app_chat_gpt_request')]
    public function index(): JsonResponse
    {
        $msg = "Usługi ślusarskie w Poznaniu'";

        $request = new ChatGptRequest($this->getApiKey(), $this->getOrganizationKey(), $msg, 70, true);
        $response = $request->send();

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
