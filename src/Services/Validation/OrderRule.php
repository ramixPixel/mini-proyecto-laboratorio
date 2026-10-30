<?php

declare(strict_types=1);

namespace Laboratorio\Services\Validation;

use InvalidArgumentException;
use Laboratorio\Models\Order;

/**
 * Un eslabon de la cadena de validacion de pedidos.
 *
 * El validador solo conoce este contrato: agregar una regla nueva es
 * implementar esta interfaz, no editar OrderValidator.
 */
interface OrderRule
{
    /**
     * @throws InvalidArgumentException si el pedido no cumple la regla
     */
    public function check(Order $order): void;
}
