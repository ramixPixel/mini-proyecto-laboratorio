<?php
class PriceCalculator
{
    public function __construct(private PricingStrategy $strategy) {}

    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }
}
