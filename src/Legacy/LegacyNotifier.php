<?php
/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 *  Patron esperado: ADAPTER (estructural)
 * ============================================================================
 *
 *  CONTEXTO: el laboratorio ya tenia un sistema de avisos hecho por otro
 *  proveedor. No lo podemos modificar: lo usan tambien Recepcion y Facturacion.
 *
 *  ❌ DEUDA SEMBRADA
 *     1. Alguien "resolvio" la incompatibilidad copiando el cuerpo de
 *        sendMessage() dentro de nuestro codigo (ver copiaDeLegacyEnNuestroSistema).
 *     2. Otro alguien agrego el metodo send() DENTRO de la clase heredada,
 *        modificando codigo de un tercero.
 *
 *  ✅ FORMA CORRECTA
 *     Un Adapter que implemente NUESTRO contrato y traduzca la llamada.
 *     La incompatibilidad queda encerrada en un unico archivo.
 * ============================================================================
 */

class LegacyNotifier
{
    /** Metodo original del proveedor. No lo podemos cambiar. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }

    /**
     * ❌ METODO MAL APLICADO: send()
     *    Fue agregado por nosotros DENTRO de una clase de terceros.
     *    En la proxima actualizacion del proveedor este metodo desaparece
     *    y el sistema se rompe sin que nadie sepa por que.
     *    ✅ La adaptacion va afuera, en LegacyNotifierAdapter.
     */
    public function send(string $message): void
    {
        $this->sendMessage($message);
    }
}

/**
 * ❌ CLASE MAL APLICADA
 *    Copia y pega del comportamiento del sistema heredado "para no depender de el".
 *    Resultado: dos implementaciones que hay que mantener sincronizadas a mano.
 */
class CopiaDeLegacyEnNuestroSistema
{
    public function avisar(string $texto): void
    {
        echo "[LEGACY] {$texto}<br>";   // ❌ duplicacion pura
    }
}

/*
 * ============================================================================
 *  ✅ FORMA CORRECTA — ADAPTER
 * ============================================================================
 *
 * final class LegacyNotifierAdapter implements Notification
 * {
 *     // COMPOSICION: el adapter TIENE el sistema viejo adentro
 *     public function __construct(private LegacyNotifier $legacy) {}
 *
 *     public function send(string $message): void
 *     {
 *         $this->legacy->sendMessage($message);   // unica traduccion del sistema
 *     }
 * }
 *
 * Uso:
 *   $notificacion = new LegacyNotifierAdapter(new LegacyNotifier());
 *   $notificacion->send('Pedido creado');
 *
 * Estructura:
 *   Nuestro sistema -> Notification <- Adapter -> LegacyNotifier
 *
 * ----------------------------------------------------------------------------
 * EJERCICIO 3 (TP): borrar el metodo send() de LegacyNotifier y la clase
 * CopiaDeLegacyEnNuestroSistema, y hacer que todo pase por el Adapter.
 *
 * PREGUNTA PARA EL PR: si manana el proveedor cambia sendMessage() por
 * dispatch(), cuantos archivos de tu sistema cambian?
 * ============================================================================
 */
