<?php 
namespace App\Message;

use App\Message\NotificationMessage;
use App\Service\NotificationService; // Importer votre service de notification push
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationMessageHandler implements MessageHandlerInterface
{
    private $notificationPushService;

    public function __construct(NotificationService $notificationPushService)
    {
        $this->notificationPushService = $notificationPushService;
    }

    public function __invoke(NotificationMessage $message)
    {
        $userId = $message->getUserId();
        $notificationMessage = $message->getMessage();

        // Supposons que vous ayez une méthode dans votre service de notification push pour envoyer une notification
        $this->notificationPushService->sendNotification($userId, $notificationMessage);
    }
}
