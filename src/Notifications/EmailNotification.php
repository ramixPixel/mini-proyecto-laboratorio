<?php
/**
 * ============================================================================
 *  NOTIFICACION POR EMAIL
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     Esta clase NO implementa ninguna interfaz.
 *     Quien la usa depende de la clase concreta => acoplamiento fuerte.
 *     El metodo se llama enviarEmail(); el de SMS se llama mandarSms().
 *     Nombres distintos para la misma responsabilidad: imposible intercambiarlos.
 *
 *  ✅ FORMA CORRECTA
 *     interface Notification { public function send(string $message): void; }
 *     class EmailNotification implements Notification { public function send(...) }
 *     Mismo contrato => polimorfismo => el cliente no conoce la clase concreta.
 * ============================================================================
 */

class EmailNotification
{
    /**
     * ❌ METODO MAL APLICADO: enviarEmail()
     *    El nombre y la firma son propios de esta clase.
     *    ✅ Deberia llamarse send(string $message): void, definido por la interfaz.
     */
    public function enviarEmail(string $destinatario, string $asunto, string $cuerpo): void
    {
        echo "[EMAIL] para {$destinatario} | {$asunto}: {$cuerpo}<br>";
    }
}
