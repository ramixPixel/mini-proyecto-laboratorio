<?php

declare(strict_types=1);

namespace Laboratorio\Notifications;

/**
 * ============================================================================
 *  ENVIO DE NOTIFICACIONES
 *  Patron aplicado: FACTORY METHOD (creacional)
 * ============================================================================
 *
 *  El remitente depende de la interfaz Notification. La creacion concreta
 *  vive en NotificationFactory: agregar WhatsApp no toca a los consumidores.
 * ============================================================================
 */
final class NotificationSender
{
    public function enviar(string $tipo, string $destino, string $mensaje): void
    {
        NotificationFactory::create($tipo, $destino)->send($mensaje);
    }
}
