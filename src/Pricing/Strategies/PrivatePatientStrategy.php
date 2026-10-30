<?php

declare(strict_types=1);

namespace Laboratorio\Pricing\Strategies;

use Laboratorio\Pricing\PricingStrategy;

final class PrivatePatientStrategy implements PricingStrategy
{
    public function calculate(float $amount): float
    {
        return $amount;
    }
}
