<?php
/**
 * ============================================================================
 *  NOTIFICACION POR SMS
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     Misma responsabilidad que EmailNotification pero con OTRA firma:
 *        EmailNotification::enviarEmail($destinatario, $asunto, $cuerpo)
 *        SmsNotification::mandarSms($numero, $texto)
 *     Al no compartir contrato, el cliente necesita un if por cada tipo.
 *     Ese if es exactamente la deuda que despues repara Factory.
 *
 *  ✅ FORMA CORRECTA
 *     Ambas implementan Notification::send(string $message): void.
 * ============================================================================
 */

class SmsNotification
{
    /**
     * ❌ METODO MAL APLICADO: mandarSms()
     *    ✅ send(string $message): void, igual que el resto de las notificaciones.
     */
    public function mandarSms(string $numero, string $texto): void
    {
        echo "[SMS] a {$numero}: {$texto}<br>";
    }
}
