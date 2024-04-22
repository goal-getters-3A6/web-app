<?php
// Créez un nouveau service pour la détection d'émotion
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmotionDetectionService
{
    private $flaskEndpoint;

    public function __construct(string $flaskEndpoint)
    {
        $this->flaskEndpoint = $flaskEndpoint;
    }

    public function detectEmotion(string $comment): string
    {
        $client = HttpClientInterface::create();
        $response = $client->request('POST', $this->flaskEndpoint . '/detect_emotion', [
            'json' => ['comment' => $comment]
        ]);

        $data = $response->toArray();
        
        if (isset($data['emotion_bert'])) {
            return $data['emotion_bert'];
        } else {
            return 'Erreur lors de la détection de l\'émotion';
        }
    }
}
