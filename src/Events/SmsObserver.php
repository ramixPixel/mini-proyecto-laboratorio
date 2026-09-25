<?php
class SmsObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "[SMS] pedido {$order->id} creado<br>";
    }
}
