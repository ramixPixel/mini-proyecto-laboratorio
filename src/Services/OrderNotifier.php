<?php
class OrderNotifier
{
    public function send(Order $order, string $tipo, string $destino): void
    {
        $notification = NotificationFactory::create($tipo, $destino);
        $notification->send("Pedido {$order->id} por $ {$order->amount}");
    }
}
