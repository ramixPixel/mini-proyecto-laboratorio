<?php
/**
 * ============================================================================
 *  laboratorio-pedidos — PUNTO DE ENTRADA
 *  Metodología de Sistemas II — UTN FRRe (sede Formosa)
 * ============================================================================
 *
 *  ✅ REFACTORIZADO: autoload PSR-4 + config externo.
 *
 *  Unidad 1: la configuración externalizada y el autoload son deuda de
 *  PROCESO además de diseño. Un compañero puede levantar el proyecto
 *  sin pedir claves.
 * ============================================================================
 */

spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$config = require __DIR__ . '/../config/database.example.php';

$accion = $_GET['accion'] ?? 'crear';

$controller = new OrderController();

if ($accion === 'crear') {
    $controller->create();
} elseif ($accion === 'listar') {
    $controller->index();
} elseif ($accion === 'reporte') {
    $controller->report();
} else {
    echo 'Accion no encontrada';
}
