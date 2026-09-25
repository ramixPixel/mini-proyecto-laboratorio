<?php
class NotificationSender
{
    public function enviar(string $tipo, string $destino, string $mensaje): void
    {
        $notification = NotificationFactory::create($tipo, $destino);
        $notification->send($mensaje);
    }
}
