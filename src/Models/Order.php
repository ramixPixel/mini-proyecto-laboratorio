<?php

declare(strict_types=1);

namespace Laboratorio\Models;

use InvalidArgumentException;

/**
 * ============================================================================
 *  MODELO Order
 * ============================================================================
 *
 *  Objeto de dominio inmutable. Valida sus invariantes basicas en el
 *  constructor y no conoce la base de datos ni las reglas de precio.
 * ============================================================================
 */
final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly string $patient,
        public readonly float $amount,
        public readonly string $patientType
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('El id del pedido debe ser positivo');
        }

        if (trim($patient) === '') {
            throw new InvalidArgumentException('El paciente es obligatorio');
        }

        if ($amount < 0) {
            throw new InvalidArgumentException('El importe no puede ser negativo');
        }
    }
}
