<?php

declare(strict_types=1);

namespace Laboratorio\Notifications;

final class EmailNotification implements Notification
{
    public function __construct(
        private string $to,
        private string $subject = 'Pedido del laboratorio'
    ) {
    }

    public function send(string $message): void
    {
        echo "[EMAIL] para {$this->to} | {$this->subject}: {$message}<br>";
    }
}
