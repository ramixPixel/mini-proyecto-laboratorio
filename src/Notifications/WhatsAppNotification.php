<?php

declare(strict_types=1);

namespace Laboratorio\Notifications;

final class WhatsAppNotification implements Notification
{
    public function __construct(private string $to)
    {
    }

    public function send(string $message): void
    {
        echo "[WHATSAPP] a {$this->to}: {$message}<br>";
    }
}
