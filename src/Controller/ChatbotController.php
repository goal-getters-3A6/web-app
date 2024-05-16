<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WitAiService;

class ChatbotController extends AbstractController
{
    
    #[Route('/chatbot/test', name: 'chatbot_endpoint', methods: ['POST'])]
    public function handleMessage(Request $request, WitAiService $witAiService): JsonResponse
    {
        
        $content = json_decode($request->getContent(), true);
        $message = $content['message'] ?? '';

        if (!$message) {
            return new JsonResponse(['error' => 'Message not provided'], 400);
        }

       
        $response = $witAiService->sendMessage($message);

        return new JsonResponse($response);
    }
}
