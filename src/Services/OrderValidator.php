<?php
class OrderValidator
{
    public function validate(Order $order): void
    {
        if (trim($order->patient) === '') {
            throw new InvalidArgumentException('Falta el paciente');
        }
        if ($order->amount <= 0) {
            throw new InvalidArgumentException('Monto invalido');
        }
    }
}
