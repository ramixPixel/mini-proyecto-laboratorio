<?php

declare(strict_types=1);

namespace Laboratorio\Notifications;

use InvalidArgumentException;

final class NotificationFactory
{
    public static function create(string $type, string $destination): Notification
    {
        return match ($type) {
            'email' => new EmailNotification($destination),
            'sms' => new SmsNotification($destination),
            'whatsapp' => new WhatsAppNotification($destination),
            default => throw new InvalidArgumentException(
                "Tipo de notificacion no soportado: {$type}"
            ),
        };
    }
}
