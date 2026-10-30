<?php

declare(strict_types=1);

namespace Laboratorio\Legacy;

use Laboratorio\Notifications\Notification;

/**
 * ============================================================================
 *  ADAPTADOR DEL SISTEMA HEREDADO
 *  Patron aplicado: ADAPTER (estructural)
 * ============================================================================
 *
 *  Traduce nuestro contrato Notification al metodo sendMessage() del
 *  proveedor. La incompatibilidad queda encerrada en un unico archivo.
 * ============================================================================
 */
final class LegacyNotifierAdapter implements Notification
{
    public function __construct(private LegacyNotifier $legacy)
    {
    }

    public function send(string $message): void
    {
        $this->legacy->sendMessage($message);
    }
}
