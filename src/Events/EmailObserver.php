<?php
class EmailObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "[EMAIL] pedido {$order->id} creado<br>";
    }
}
