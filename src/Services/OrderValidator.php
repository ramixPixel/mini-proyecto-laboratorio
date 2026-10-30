<?php

declare(strict_types=1);

namespace Laboratorio\Services;

use Laboratorio\Models\Order;
use Laboratorio\Services\Validation\OrderRule;
use Laboratorio\Services\Validation\PatientRequiredRule;
use Laboratorio\Services\Validation\PatientTypeRequiredRule;
use Laboratorio\Services\Validation\PositiveAmountRule;

/**
 * ============================================================================
 *  VALIDACION DE PEDIDOS
 *  Patron aplicado: CHAIN OF RESPONSIBILITY (comportamiento)
 * ============================================================================
 *
 *  DEUDA QUE RESUELVE
 *     validate() encadenaba tres if y cada regla nueva obligaba a editar
 *     esta clase (viola OCP). El recorrido y las reglas vivian mezclados.
 *
 *  ESTRUCTURA
 *     OrderRule               -> contrato de una regla
 *     PatientRequiredRule     -> el paciente no puede estar vacio
 *     PositiveAmountRule      -> el importe debe ser mayor a cero
 *     PatientTypeRequiredRule -> el tipo de paciente es obligatorio
 *     OrderValidator          -> recorre la cadena y corta en la primera que falla
 *
 *  CONSECUENCIA ASUMIDA
 *     Mas archivos para tres reglas cortas. La cadena se detiene en el primer
 *     error: no acumula todos los mensajes. Y el orden importa, porque la
 *     primera regla que falla define el mensaje que ve el usuario.
 * ============================================================================
 */
final class OrderValidator
{
    /** @var list<OrderRule> */
    private array $rules;

    /**
     * @param list<OrderRule>|null $rules reglas a aplicar; null usa las de defecto
     */
    public function __construct(?array $rules = null)
    {
        $this->rules = $rules ?? [
            new PatientRequiredRule(),
            new PositiveAmountRule(),
            new PatientTypeRequiredRule(),
        ];
    }

    public function validate(Order $order): void
    {
        foreach ($this->rules as $rule) {
            // POLIMORFISMO: el validador no conoce las reglas concretas
            $rule->check($order);
        }
    }
}
