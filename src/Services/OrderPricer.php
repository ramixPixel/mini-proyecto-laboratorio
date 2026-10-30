<?php
class OrderPricer
{
    public function calculate(Order $order): float
    {
        // Seleccionamos la estrategia según el tipo de paciente
        $strategy = match ($order->patientType) {
            'obra_social' => new InsuranceStrategy(),
            'jubilado'    => new RetiredStrategy(),
            'prepaga'     => new PrepaidStrategy(),
            default       => new PrivatePatientStrategy(),
        };

        // Usamos el PriceCalculator que ya aplica el patrón
        $calculator = new PriceCalculator($strategy);
        
        return $calculator->calculate($order->amount);
    }
}