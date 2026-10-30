<?php

declare(strict_types=1);

namespace Laboratorio\Services;

use Laboratorio\Events\OrderEvents;
use Laboratorio\Models\Order;

/**
 * ============================================================================
 *  SERVICIO DE PEDIDOS
 *  Patron aplicado: FACADE (estructural) + separacion en servicios cohesivos
 * ============================================================================
 *
 *  La fachada coordina validacion, precio, persistencia y eventos. No decide
 *  reglas de negocio ni imprime HTML: cada paso vive en su propio servicio.
 * ============================================================================
 */
final class OrderService
{
    public function __construct(
        private OrderValidator $validator,
        private OrderPricer $pricer,
        private OrderRepository $repository,
        private OrderEvents $events
    ) {
    }

    public function createOrder(Order $order): OrderResult
    {
        $this->validator->validate($order);

        $result = new OrderResult(
            $order->id,
            $order->patient,
            $this->pricer->calculate($order)
        );

        $this->repository->save($result);
        $this->events->notify($order);

        return $result;
    }

    /** @return list<OrderResult> */
    public function listOrders(): array
    {
        return array_map(
            fn (Order $order): OrderResult => new OrderResult(
                $order->id,
                $order->patient,
                $this->pricer->calculate($order)
            ),
            $this->repository->all()
        );
    }
}
