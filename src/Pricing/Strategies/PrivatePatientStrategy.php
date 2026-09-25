<?php
class PrivatePatientStrategy implements PricingStrategy
{
    public function calculate(float $amount): float
    {
        return $amount;
    }
}
