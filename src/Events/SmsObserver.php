<?php

declare(strict_types=1);

namespace Laboratorio\Events;

use Laboratorio\Models\Order;
use Laboratorio\Notifications\NotificationSender;

final class SmsObserver implements OrderObserver
{
    public function __construct(
        private NotificationSender $sender,
        private string $destination = '3704000000'
    ) {
    }

    public function update(Order $order): void
    {
        $this->sender->enviar('sms', $this->destination, "Pedido {$order->id} creado");
    }
}
