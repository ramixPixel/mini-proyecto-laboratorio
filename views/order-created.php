<?php

declare(strict_types=1);

/** @var \Laboratorio\Services\OrderResult $result */
?>
<h1>Pedido creado</h1>
<p>Paciente: <?= htmlspecialchars($result->patient, ENT_QUOTES, 'UTF-8') ?></p>
<p>Total: $ <?= htmlspecialchars((string) $result->total, ENT_QUOTES, 'UTF-8') ?></p>
