<?php

declare(strict_types=1);

namespace Laboratorio\Pricing\Strategies;

use Laboratorio\Pricing\PricingStrategy;

final class PrepaidStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.15)
    {
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}
