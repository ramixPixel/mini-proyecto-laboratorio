<?php

declare(strict_types=1);

namespace Laboratorio\Services;

use Laboratorio\Models\Order;
use Laboratorio\Pricing\PriceCalculator;
use Laboratorio\Pricing\PricingStrategyFactory;

final class OrderPricer
{
    public function calculate(Order $order): float
    {
        $calculator = new PriceCalculator(
            PricingStrategyFactory::forPatientType($order->patientType)
        );

        return $calculator->calculate($order->amount);
    }
}
