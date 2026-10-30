<?php

declare(strict_types=1);

namespace Laboratorio\Pricing;

/**
 * ============================================================================
 *  CALCULO DE PRECIOS
 *  Patron aplicado: STRATEGY (comportamiento)
 * ============================================================================
 *
 *  La calculadora compone una estrategia y delega el algoritmo. Agregar un
 *  tipo de paciente nuevo es agregar una clase, no modificar esta clase.
 * ============================================================================
 */
final class PriceCalculator
{
    public function __construct(private PricingStrategy $strategy)
    {
    }

    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }
}
