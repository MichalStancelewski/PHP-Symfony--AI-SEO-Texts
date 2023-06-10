<?php

namespace App\Communicator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatGptRequest
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';
    private const MAX_TOKENS_LOW = 2000;
    private const MAX_TOKENS_HIGH = 4000;
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

    public function sendAndGetNewArticle(string $language)
    {
        $messages = array();

        switch (strtoupper($language)) {
            case 'POL':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Napisz mi nowy unikalny tekst o długości około " .
                            $this->textLength .
                            " wyrazów na temat '" .
                            $this->messageContent .
                            "'. Nadaj tekstowi tytuł, który ma ponad 7 wyrazów, ale niech nie zawiera frazy '" .
                            $this->messageContent .
                            "'. Tytuł opakuj w znacznik <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Napisz mi nowy unikalny tekst o długości około " .
                            $this->textLength .
                            " wyrazów na temat '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'ENG':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Write me a new unique text of about " .
                            $this->textLength .
                            " letters on the topic '" .
                            $this->messageContent .
                            "'. Give the text a title that is more than 7 words long, but do not contain a phrase '" .
                            $this->messageContent .
                            "'. Wrap the title in a tag <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Write me a new unique text of about " .
                            $this->textLength .
                            " letters on the topic  '" .
                            $this->messageContent .
                            "'."
                    );
                }
                break;
            case 'CZE':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Napište mi nový unikátní text cca " .
                            $this->textLength .
                            " dopisů na téma '" .
                            $this->messageContent .
                            "'. Dejte textu název, který je delší než 7 slov, ale neobsahuje frázi '" .
                            $this->messageContent .
                            "'. Zabalte název do značky <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Napište mi nový unikátní text cca " .
                            $this->textLength .
                            " dopisů na téma  '" .
                            $this->messageContent .
                            "'."
                    );
                }
                break;
            case 'FRE':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Écrivez-moi un nouveau texte unique d'environ " .
                            $this->textLength .
                            " lettres sur le thème '" .
                            $this->messageContent .
                            "'. Donnez au texte un titre de plus de 7 mots, mais ne contenant pas de phrase '" .
                            $this->messageContent .
                            "'. Enveloppez le titre dans la balise <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Écrivez-moi un nouveau texte unique d'environ " .
                            $this->textLength .
                            " lettres sur le thème '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'SPA':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Escríbeme un nuevo texto único de unas " .
                            $this->textLength .
                            " letras sobre el tema '" .
                            $this->messageContent .
                            "'. Dale al texto un título que tenga más de 7 palabras, pero que no contenga una frase'" .
                            $this->messageContent .
                            "'. Envuelva el título en la etiqueta <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Escríbeme un nuevo texto único de unas " .
                            $this->textLength .
                            " letras sobre el tema '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'DUT':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Schrijf me een nieuwe unieke tekst van ongeveer " .
                            $this->textLength .
                            " letters over het onderwerp '" .
                            $this->messageContent .
                            "'. Geef de tekst een titel die langer is dan 7 woorden, maar geen zin bevat '" .
                            $this->messageContent .
                            "'. Wikkel de titel in de <h1> </h1> tag."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Schrijf me een nieuwe unieke tekst van ongeveer " .
                            $this->textLength .
                            " letters over het onderwerp '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'GER':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Schreiben Sie mir einen neuen, einzigartigen Text aus ca. " .
                            $this->textLength .
                            " Buchstaben zum Thema '" .
                            $this->messageContent .
                            "'. Geben Sie dem Text einen Titel, der mehr als 7 Wörter lang ist, aber die Phrase '" .
                            $this->messageContent .
                            "' nicht einschließt. Wickeln Sie den Titel in das <h1> </h1>-Tag ein."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Schreiben Sie mir einen neuen, einzigartigen Text aus ca. " .
                            $this->textLength .
                            " Buchstaben zum Thema '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'RUS':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Напишите мне новый уникальный текст около " .
                            $this->textLength .
                            " букв на тему '" .
                            $this->messageContent .
                            "'. Дайте тексту заголовок длиной более 7 слов, но не содержащий словосочетания. '" .
                            $this->messageContent .
                            "'. Оберните заголовок тегом <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Напишите мне новый уникальный текст около " .
                            $this->textLength .
                            " букв на тему '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'UKR':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Напишіть мені новий унікальний текст близько " .
                            $this->textLength .
                            " букв на тему '" .
                            $this->messageContent .
                            "'. Дайте тексту назву, яка містить більше 7 слів, але не включайте фразу '" .
                            $this->messageContent .
                            "'. Загорніть назву в тег <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Напишіть мені новий унікальний текст близько " .
                            $this->textLength .
                            " букв на тему '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;
            case 'ITA':
                if ($this->addTitles) {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Scrivimi un nuovo testo unico di circa " .
                            $this->textLength .
                            " lettere sul tema '" .
                            $this->messageContent .
                            "'. Assegna al testo un titolo di più di 7 parole, ma non includere la frase '" .
                            $this->messageContent .
                            "'. Avvolgi il titolo nel tag <h1> </h1>."
                    );
                } else {
                    $messages[] = array(
                        "role" => "user",
                        "content" => "Scrivimi un nuovo testo unico di circa " .
                            $this->textLength .
                            " lettere sul tema '" .
                            $this->messageContent .
                            "."
                    );
                }
                break;

        }

        $data = array();
        $data["model"] = "gpt-3.5-turbo";
        $data["messages"] = $messages;
        $data["max_tokens"] = ChatGptRequest::MAX_TOKENS_LOW;

        $curl = curl_init(ChatGptRequest::API_URL);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        $jsonData = json_decode($result, false);
        if (curl_errno($curl)) {
            curl_close($curl);
            //throw error
            return 'error code 1';
        } else {
            curl_close($curl);
            if (!isset($jsonData->choices[0]->message->content)) {
                return $jsonData;
            }
            return $jsonData->choices[0]->message->content;
        }

    }

    public function sendAndGetWithHtml(string $content, string $language)
    {
        $messages = array();

        switch (strtoupper($language)) {
            case 'POL':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Opatrz powyższy tekst znacznikami HTML, aby był ostylowany do publikacji na blogu internetowym (ale nie korzystaj ze znacznika <h1>!)."
                );
                break;
            case 'ENG':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Mark the above text with HTML tags to make it styled for posting on a blog (but don't use the <h1> tag!)."
                );
                break;
            case 'CZE':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Označte výše uvedený text značkami HTML, aby byl stylizovaný pro zveřejňování na blogu (nepoužívejte však značku <h1>!)."
                );
                break;
            case 'FRE':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Marquez le texte ci-dessus avec des balises HTML pour le mettre en forme pour la publication sur un blog (mais n'utilisez pas la balise <h1>!)."
                );
                break;
            case 'SPA':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Marque el texto anterior con etiquetas HTML para diseñarlo para publicarlo en un blog (¡pero no use la etiqueta <h1>!)."
                );
                break;
            case 'DUT':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Markeer de bovenstaande tekst met HTML-tags om deze op te maken voor plaatsing op een blog (maar gebruik de <h1>-tag niet!)."
                );
                break;
            case 'GER':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Markieren Sie den obigen Text mit HTML-Tags, um ihn für die Veröffentlichung in einem Blog zu formatieren (verwenden Sie jedoch nicht das <h1>-Tag!)."
                );
                break;
            case 'RUS':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Отметьте приведенный выше текст HTML-тегами, чтобы оформить его для публикации в блоге (но не используйте тег <h1>!)."
                );
                break;
            case 'UKR':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Позначте наведений вище текст тегами HTML, щоб створити його для публікації в блозі (але не використовуйте тег <h1>!)."
                );
                break;
            case 'ITA':
                $messages[] = array(
                    "role" => "user",
                    "content" => "'" . $content . "' Contrassegna il testo sopra con tag HTML per renderlo adatto alla pubblicazione su un blog (ma non utilizzare il tag <h1>!)."
                );
                break;
        }

        $data = array();
        $data["model"] = "gpt-3.5-turbo";
        $data["messages"] = $messages;
        $data["max_tokens"] = ChatGptRequest::MAX_TOKENS_HIGH;

        $curl = curl_init(ChatGptRequest::API_URL);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $jsonData = json_decode($result, false);
        if (curl_errno($curl)) {
            curl_close($curl);
            //throw error
            return 'error';
        } else {
            curl_close($curl);
            if (!isset($jsonData->choices[0]->message->content)) {
                return $jsonData;
            }
            return str_replace("'", "", $jsonData->choices[0]->message->content);
        }

    }

}