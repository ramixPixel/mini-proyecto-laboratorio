<?php
class OrderReporter
{
    public function generate(Order $order): string
    {
        $generador = new ReportGenerator();
        return $generador->generate("Pedido {$order->id}") . '<br>';
    }
}
