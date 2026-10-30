<?php
/**
 * ============================================================================
 *  ENVIO DE NOTIFICACIONES
 *  Patron esperado: FACTORY METHOD (creacional)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     El if por tipo de notificacion esta repetido en TRES lugares del sistema:
 *        1. aca, en enviar()
 *        2. en OrderService::procesarPedidoCompleto()
 *        3. en OrderController::create()
 *     Agregar WhatsApp obliga a encontrar y modificar los tres.
 *     Es el sintoma clasico: "la logica de creacion se desparramo".
 *
 *  ✅ FORMA CORRECTA
 *     Una unica fabrica que devuelve objetos que cumplen el contrato Notification.
 *     El resto del sistema deja de conocer las clases concretas.
 * ============================================================================
 */

class NotificationSender
{
    /**
     * ❌ METODO MAL APLICADO: enviar()
     *    1. Decide QUE objeto crear y ademas COMO usarlo (dos responsabilidades).
     *    2. Conoce las clases concretas EmailNotification y SmsNotification.
     *    3. Conoce las firmas distintas de cada una.
     */
    public function enviar(string $tipo, string $destino, string $mensaje): void
    {
        if ($tipo === 'email') {
            $email = new EmailNotification();
            $email->enviarEmail($destino, 'Pedido del laboratorio', $mensaje);
        }

        if ($tipo === 'sms') {
            $sms = new SmsNotification();
            $sms->mandarSms($destino, $mensaje);
        }

        // ❌ Si el tipo no existe, no pasa nada y nadie se entera.
        //    ✅ default => throw new InvalidArgumentException('Tipo no soportado');
    }
}

/*
 * ============================================================================
 *  ✅ FORMA CORRECTA — FACTORY
 * ============================================================================
 *
 * interface Notification
 * {
 *     public function send(string $message): void;
 * }
 *
 * final class EmailNotification implements Notification
 * {
 *     public function __construct(private string $to) {}
 *
 *     public function send(string $message): void
 *     {
 *         echo "[EMAIL] {$this->to}: {$message}";
 *     }
 * }
 *
 * final class NotificationFactory
 * {
 *     public static function create(string $type, string $destino): Notification
 *     {
 *         return match ($type) {
 *             'email' => new EmailNotification($destino),
 *             'sms'   => new SmsNotification($destino),
 *             default => throw new InvalidArgumentException(
 *                 "Tipo de notificacion no soportado: {$type}"
 *             ),
 *         };
 *     }
 * }
 *
 * Uso:
 *   NotificationFactory::create('email', 'juan@mail.com')->send('Su pedido fue creado');
 *
 * ----------------------------------------------------------------------------
 * EJERCICIO 2 (TP): agregar WhatsAppNotification.
 * Regla: solo se permite tocar UN archivo ademas de la clase nueva.
 * Si tuviste que tocar el controlador o el servicio, el Factory no quedo aplicado.
 * ============================================================================
 */
