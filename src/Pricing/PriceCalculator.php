<?php
/**
 * ============================================================================
 *  CALCULO DE PRECIOS
 *  Patron esperado: STRATEGY (comportamiento)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     Un switch concentra TODOS los algoritmos de precio.
 *     Cada tipo de paciente nuevo obliga a modificar esta clase.
 *     Los porcentajes estan escritos a mano y repetidos en otros archivos.
 *
 *  ✅ FORMA CORRECTA
 *     Interfaz PricingStrategy + una clase por algoritmo + composicion.
 *     Agregar "prepaga" pasa a ser AGREGAR una clase, no MODIFICAR esta.
 * ============================================================================
 */

class PriceCalculator
{
    /**
     * ❌ METODO MAL APLICADO: calcular()
     *    Viola SRP  : la clase tiene tantos motivos de cambio como tipos de paciente.
     *    Viola OCP  : para extender hay que modificar.
     *    Sintoma    : el switch crece en cada sprint y nadie borra ramas viejas.
     */
    public function calcular(float $monto, string $tipoPaciente): float
    {
        switch ($tipoPaciente) {
            case 'particular':
                return $monto;

            case 'obra_social':
                return $monto * 0.7;      // ❌ 0.7 hardcodeado, repetido en Order.php

            case 'jubilado':
                return $monto * 0.5;

            // ❌ El proximo requerimiento agrega otro case aca.
            default:
                return $monto;
        }
    }

    /**
     * ❌ METODO MAL APLICADO: calcularConIva()
     *    Duplica la logica de arriba "para no tocar lo que ya anda".
     *    Cuando cambie el descuento de obra social, hay que acordarse de los DOS lugares.
     */
    public function calcularConIva(float $monto, string $tipoPaciente): float
    {
        if ($tipoPaciente === 'obra_social') {
            return ($monto * 0.7) * 1.21;
        }

        return $monto * 1.21;
    }
}

/*
 * ============================================================================
 *  ✅ FORMA CORRECTA — STRATEGY
 * ============================================================================
 *
 * interface PricingStrategy
 * {
 *     public function calculate(float $amount): float;
 * }
 *
 * final class PrivatePatientStrategy implements PricingStrategy
 * {
 *     public function calculate(float $amount): float
 *     {
 *         return $amount;
 *     }
 * }
 *
 * final class InsuranceStrategy implements PricingStrategy
 * {
 *     public function __construct(private float $discount = 0.30) {}
 *
 *     public function calculate(float $amount): float
 *     {
 *         return $amount * (1 - $this->discount);
 *     }
 * }
 *
 * final class PriceCalculator
 * {
 *     // COMPOSICION: PriceCalculator TIENE una estrategia (no hereda de ella)
 *     public function __construct(private PricingStrategy $strategy) {}
 *
 *     public function calculate(float $amount): float
 *     {
 *         return $this->strategy->calculate($amount);   // POLIMORFISMO
 *     }
 * }
 *
 * Uso:
 *   $calculadora = new PriceCalculator(new InsuranceStrategy());
 *   echo $calculadora->calculate(10000);
 *
 * ----------------------------------------------------------------------------
 * EJERCICIO 1 (TP): implementar PrepaidStrategy con un descuento distinto,
 * SIN modificar PriceCalculator. Si tuviste que tocarla, el patron no quedo bien.
 *
 * CONSECUENCIA A JUSTIFICAR EN EL PR: Strategy agrega 3 archivos donde antes
 * habia un switch de 10 lineas. Cuando ese costo NO se justifica?
 * ============================================================================
 */
