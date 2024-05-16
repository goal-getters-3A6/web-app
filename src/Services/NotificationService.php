<?php

namespace App\Service;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\NotificationMessage;

class NotificationService {
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus) {
        $this->messageBus = $messageBus;
    }

    public function sendNotification(int $userId, string $message) {
        $notification = new NotificationMessage($userId, $message);
        $this->messageBus->dispatch($notification);
    }
}
