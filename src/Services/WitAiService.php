<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WitAiService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendMessage(string $message): array
    {
        $accessToken = $_ENV['WIT_AI_ACCESS_TOKEN'] ?? getenv('WIT_AI_ACCESS_TOKEN');
        
        if (!$accessToken) {
            throw new \RuntimeException('Access token not provided.');
        }

        // Construire l'URL de l'API de Wit.ai avec le message encodé
        $url = 'https://api.wit.ai/message?v=20240429&q=' . urlencode($message);
    
        // Envoyer la requête HTTP à l'API de Wit.ai en utilisant HttpClientInterface
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
    
        // Récupérer la réponse sous forme de tableau associatif
        return $response->toArray();
    }
}
    

