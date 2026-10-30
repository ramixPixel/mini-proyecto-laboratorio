<?php

declare(strict_types=1);

namespace Laboratorio\Pricing;

interface PricingStrategy
{
    public function calculate(float $amount): float;
}
