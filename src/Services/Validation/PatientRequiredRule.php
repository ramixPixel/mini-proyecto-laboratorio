<?php

declare(strict_types=1);

namespace Laboratorio\Services\Validation;

use InvalidArgumentException;
use Laboratorio\Models\Order;

final class PatientRequiredRule implements OrderRule
{
    public function check(Order $order): void
    {
        if (trim($order->patient) === '') {
            throw new InvalidArgumentException('El paciente es obligatorio');
        }
    }
}
