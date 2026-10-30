<?php

declare(strict_types=1);

use Laboratorio\Controllers\OrderController;
use Laboratorio\Database\Connection;
use Laboratorio\Events\DashboardObserver;
use Laboratorio\Events\EmailObserver;
use Laboratorio\Events\OrderEvents;
use Laboratorio\Notifications\NotificationSender;
use Laboratorio\Services\OrderPricer;
use Laboratorio\Services\OrderRepository;
use Laboratorio\Services\OrderReporter;
use Laboratorio\Services\OrderService;
use Laboratorio\Services\OrderValidator;

/**
 * ============================================================================
 *  laboratorio-pedidos — PUNTO DE ENTRADA
 *  Metodología de Sistemas II — UTN FRRe (sede Formosa)
 * ============================================================================
 *
 *  Composition root: autoload PSR-4, configuracion externa, construccion de
 *  dependencias y tabla de rutas. Ninguna regla de negocio vive aca.
 * ============================================================================
 */

require dirname(__DIR__) . '/bootstrap/autoload.php';

$configFile = dirname(__DIR__) . '/config/database.php';
$config = require is_file($configFile)
    ? $configFile
    : dirname(__DIR__) . '/config/database.example.php';

Connection::configure($config);
$connection = Connection::getInstance();

$events = new OrderEvents();
$events->subscribe(new EmailObserver(new NotificationSender()));
$events->subscribe(new DashboardObserver());

$controller = new OrderController(
    new OrderService(
        new OrderValidator(),
        new OrderPricer(),
        new OrderRepository($connection),
        $events
    ),
    new OrderReporter()
);

$accion = $_GET['accion'] ?? 'crear';

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
