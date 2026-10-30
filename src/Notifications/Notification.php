<?php

declare(strict_types=1);

namespace Laboratorio\Notifications;

interface Notification
{
    public function send(string $message): void;
}
