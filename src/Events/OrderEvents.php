<?php

declare(strict_types=1);

namespace Laboratorio\Events;

use Laboratorio\Models\Order;

/**
 * ============================================================================
 *  AVISOS AL CREARSE UN PEDIDO
 *  Patron aplicado: OBSERVER (comportamiento)
 * ============================================================================
 *
 *  El sujeto mantiene una coleccion de observadores y les avisa. Conoce la
 *  interfaz OrderObserver, no las clases concretas. Agregar SmsObserver no
 *  requiere tocar esta clase.
 * ============================================================================
 */
final class OrderEvents
{
    /** @var list<OrderObserver> */
    private array $observers = [];

    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order);
        }
    }

    public function pedidoCreado(Order $order): void
    {
        $this->notify($order);
    }
}
