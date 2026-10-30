<?php

declare(strict_types=1);

namespace Laboratorio\Pricing;

use Laboratorio\Pricing\Strategies\InsuranceStrategy;
use Laboratorio\Pricing\Strategies\PrepaidStrategy;
use Laboratorio\Pricing\Strategies\PrivatePatientStrategy;
use Laboratorio\Pricing\Strategies\RetiredStrategy;

final class PricingStrategyFactory
{
    public static function forPatientType(string $patientType): PricingStrategy
    {
        return match ($patientType) {
            'obra_social' => new InsuranceStrategy(),
            'jubilado' => new RetiredStrategy(),
            'prepaga' => new PrepaidStrategy(),
            default => new PrivatePatientStrategy(),
        };
    }
}
