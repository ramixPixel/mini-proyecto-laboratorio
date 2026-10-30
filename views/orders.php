<?php

declare(strict_types=1);

/**
 * Vista: listado de pedidos.
 * Recibe $pedidos ya resueltos por el controlador: no consulta ni calcula.
 *
 * @var list<array{id: int, paciente: string, total: float}> $pedidos
 */
?>
<h1>Pedidos</h1>

<table border="1">
    <tr><th>ID</th><th>Paciente</th><th>Total</th></tr>
    <?php foreach ($pedidos as $pedido): ?>
        <tr>
            <td><?= htmlspecialchars((string) $pedido['id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($pedido['paciente'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $pedido['total'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
