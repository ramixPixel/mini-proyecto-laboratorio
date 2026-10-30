<?php

declare(strict_types=1);

namespace Laboratorio\Controllers;

use InvalidArgumentException;
use Laboratorio\Models\Order;
use Laboratorio\Services\OrderReporter;
use Laboratorio\Services\OrderService;

/**
 * ============================================================================
 *  CONTROLADOR DE PEDIDOS
 *  Patron aplicado: MVC + SRP
 * ============================================================================
 *
 *  Recibe la entrada, delega en la fachada y elige la vista. No arma SQL,
 *  no calcula precios y no imprime HTML.
 * ============================================================================
 */
final class OrderController
{
    public function __construct(
        private OrderService $service,
        private OrderReporter $reporter
    ) {
    }

    public function create(): void
    {
        try {
            $order = new Order(
                (int) ($_GET['id'] ?? 1),
                (string) ($_GET['paciente'] ?? 'Juan Perez'),
                (float) ($_GET['monto'] ?? 15000),
                (string) ($_GET['tipo'] ?? 'obra_social')
            );
            $result = $this->service->createOrder($order);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            $error = $e->getMessage();
            require __DIR__ . '/../../views/error.php';
            return;
        }

        require __DIR__ . '/../../views/order-created.php';
    }

    public function index(): void
    {
        $orders = $this->service->listOrders();

        require __DIR__ . '/../../views/orders.php';
    }

    public function report(): void
    {
        $report = $this->reporter->dailyReport();

        require __DIR__ . '/../../views/report.php';
    }
}
