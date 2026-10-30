<?php

declare(strict_types=1);

namespace Laboratorio\Legacy;

/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 * ============================================================================
 *
 *  Clase provista por un tercero. No se modifica: la usan tambien Recepcion
 *  y Facturacion. La adaptacion a nuestro contrato vive en
 *  LegacyNotifierAdapter.
 * ============================================================================
 */
final class LegacyNotifier
{
    /** Metodo original del proveedor. No lo podemos cambiar. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}
