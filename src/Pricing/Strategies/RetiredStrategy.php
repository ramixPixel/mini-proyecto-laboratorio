<?php

declare(strict_types=1);

namespace Laboratorio\Pricing\Strategies;

use Laboratorio\Pricing\PricingStrategy;

final class RetiredStrategy implements PricingStrategy
{
    public function calculate(float $amount): float
    {
        return $amount * 0.5;
    }
}
