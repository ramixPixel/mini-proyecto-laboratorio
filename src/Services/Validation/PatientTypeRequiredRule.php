<?php

declare(strict_types=1);

namespace Laboratorio\Services\Validation;

use InvalidArgumentException;
use Laboratorio\Models\Order;

final class PatientTypeRequiredRule implements OrderRule
{
    public function check(Order $order): void
    {
        if (trim($order->patientType) === '') {
            throw new InvalidArgumentException('El tipo de paciente es obligatorio');
        }
    }
}
