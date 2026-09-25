<?php
class NotificationFactory
{
    public static function create(string $type, string $destino): Notification
    {
        return match ($type) {
            'email' => new EmailNotification($destino),
            'sms'   => new SmsNotification($destino),
            default => throw new InvalidArgumentException("Tipo de notificacion no soportado: {$type}"),
        };
    }
}
