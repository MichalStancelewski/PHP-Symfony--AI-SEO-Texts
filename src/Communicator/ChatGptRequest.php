<?php

namespace App\Communicator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatGptRequest
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';
    private string $apiKey;
    private string $organizationKey;
    private string $messageContent;
    private int $textLength;
    private bool $addTitles;
    private array $headers;


    public function __construct(string $apiKey, string $organizationKey, string $messageContent, int $textLength, bool $addTitles)
    {
        $this->apiKey = $apiKey;
        $this->organizationKey = $organizationKey;
        $this->messageContent = $messageContent;
        $this->textLength = $textLength;
        $this->addTitles = $addTitles;
        $this->headers = array(
            "Authorization: Bearer {$this->apiKey}",
            "OpenAI-Organization: {$this->organizationKey}",
            "Content-Type: application/json"
        );
    }

    public function send()
    {
        $messages = array();
        if ($this->addTitles) {
            $messages[] = array(
                "role" => "user",
                "content" => "Napisz mi nowy unikalny tekst o długości około " .
                    $this->textLength .
                    "wyrazów na temat '" .
                    $this->messageContent .
                    "' Nadaj tekstowi tytuł, który ma ponad 7 wyrazów, ale niech nie zawiera frazy '" .
                    $this->messageContent .
                    "'. Tytuł opakuj w znacznik <h1> </h1>"
            );
        } else {
            $messages[] = array(
                "role" => "user",
                "content" => "Napisz mi nowy unikalny tekst o długości około " .
                    $this->textLength .
                    "wyrazów na temat '" .
                    $this->messageContent .
                    "."
            );
        }

        $data = array();
        $data["model"] = "gpt-3.5-turbo";
        $data["messages"] = $messages;
        $data["max_tokens"] = 2000;

        $curl = curl_init(ChatGptRequest::API_URL);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $jsonData = json_decode($result, false);
        if (curl_errno($curl)) {
            //throw error
            curl_close($curl);
            return 'error';
        } else {
            curl_close($curl);
            /*$output = $jsonData['choices'];
            $output = $output[0];
            $output = $output['message'];
            $output = $output['content'];*/
            //return $output;
            return $jsonData->choices[0]->message->content;
        }

    }

}