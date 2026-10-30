<?php

declare(strict_types=1);

/**
 * Vista: listado de pedidos.
 * Recibe $orders ya resueltos por el controlador: no consulta ni calcula.
 *
 * @var list<\Laboratorio\Services\OrderResult> $orders
 */
?>
<h1>Pedidos</h1>

<table border="1">
    <tr><th>ID</th><th>Paciente</th><th>Total</th></tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= htmlspecialchars((string) $order->id, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($order->patient, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $order->total, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
