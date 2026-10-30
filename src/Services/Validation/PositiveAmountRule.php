<?php

declare(strict_types=1);

namespace Laboratorio\Services\Validation;

use InvalidArgumentException;
use Laboratorio\Models\Order;

final class PositiveAmountRule implements OrderRule
{
    public function check(Order $order): void
    {
        if ($order->amount <= 0) {
            throw new InvalidArgumentException('El importe debe ser mayor a cero');
        }
    }
}
