<?php

declare(strict_types=1);

use Laboratorio\Controllers\OrderController;
use Laboratorio\Database\Connection;

/**
 * ============================================================================
 *  laboratorio-pedidos — PUNTO DE ENTRADA
 *  Metodología de Sistemas II — UTN FRRe (sede Formosa)
 * ============================================================================
 *
 *  La carga de clases se resuelve con un autoloader PSR-4 y la configuración
 *  se lee desde /config, fuera del repositorio.
 * ============================================================================
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'Laboratorio\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/src/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

$configFile = dirname(__DIR__) . '/config/database.php';
$config = require is_file($configFile)
    ? $configFile
    : dirname(__DIR__) . '/config/database.example.php';

Connection::configure($config);

$accion = $_GET['accion'] ?? 'crear';
$controller = new OrderController();

$rutas = [
    'crear' => static function () use ($controller): void {
        $controller->create();
    },
    'listar' => static function () use ($controller): void {
        $controller->index();
    },
    'reporte' => static function () use ($controller): void {
        $controller->report();
    },
];

$handler = $rutas[$accion] ?? null;

if ($handler === null) {
    http_response_code(404);
    echo 'Accion no encontrada';
    return;
}

$handler();
