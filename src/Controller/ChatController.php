<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends AbstractController
{
    #[Route('/api/chatbot', name: 'api_chatbot')]
    public function chatbot(Request $request): Response
    {
        // Obtenez l'entrée utilisateur depuis la requête
        $userInput = $request->getContent();

        // Envoyez une requête HTTP au serveur Flask (votre chatbot Python)
        $chatbotResponse = file_get_contents('http://localhost:5000/chatbot?user_input=' . urlencode($userInput));

        // Retournez la réponse du chatbot au format JSON
        return $this->json(['response' => $chatbotResponse]);
    }

    #[Route('/chatbot', name: 'app_chatbot')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }
}
