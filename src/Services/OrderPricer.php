<?php
class OrderPricer
{
    public function calculate(Order $order): float
    {
        if ($order->patientType === 'obra_social') {
            return $order->amount * 0.7;
        }
        if ($order->patientType === 'jubilado') {
            return $order->amount * 0.5;
        }
        return $order->amount;
    }
}
