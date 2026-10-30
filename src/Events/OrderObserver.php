<?php

declare(strict_types=1);

namespace Laboratorio\Events;

use Laboratorio\Models\Order;

interface OrderObserver
{
    public function update(Order $order): void;
}
