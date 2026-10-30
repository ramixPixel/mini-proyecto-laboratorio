<?php

declare(strict_types=1);

namespace Laboratorio\Services;

use InvalidArgumentException;
use Laboratorio\Models\Order;

final class OrderValidator
{
    public function validate(Order $order): void
    {
        if (trim($order->patient) === '') {
            throw new InvalidArgumentException('El paciente es obligatorio');
        }

        if ($order->amount <= 0) {
            throw new InvalidArgumentException('El importe debe ser mayor a cero');
        }

        if (trim($order->patientType) === '') {
            throw new InvalidArgumentException('El tipo de paciente es obligatorio');
        }
    }
}
