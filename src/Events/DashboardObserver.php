<?php

declare(strict_types=1);

namespace Laboratorio\Events;

use Laboratorio\Models\Order;

final class DashboardObserver implements OrderObserver
{
    /** @var list<string> */
    private array $events = [];

    public function update(Order $order): void
    {
        $this->events[] = "Pedido {$order->id} actualizado en el dashboard";
    }

    /** @return list<string> */
    public function events(): array
    {
        return $this->events;
    }
}
