<?php

declare(strict_types=1);

namespace Laboratorio\Events;

use Laboratorio\Models\Order;
use Laboratorio\Notifications\NotificationSender;

final class EmailObserver implements OrderObserver
{
    public function __construct(
        private NotificationSender $sender,
        private string $destination = 'paciente@mail.com'
    ) {
    }

    public function update(Order $order): void
    {
        $this->sender->enviar('email', $this->destination, "Pedido {$order->id} creado");
    }
}
