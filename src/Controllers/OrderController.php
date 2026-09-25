<?php
/**
 * ============================================================================
 *  CONTROLADOR DE PEDIDOS
 *  Patron aplicado: MVC + SRP
 * ============================================================================
 *
 *  ✅ REFACTORIZADO: el controlador solo recibe entrada, delega a la
 *     fachada y elige vista. Sin SQL, sin reglas de negocio, sin echo.
 * ============================================================================
 */

class OrderController
{
    public function __construct(private OrderService $facade) {}

    public function create(): void
    {
        $order = new Order(
            (int) ($_GET['id'] ?? 1),
            (string) ($_GET['paciente'] ?? 'Juan Perez'),
            (float) ($_GET['monto'] ?? 15000),
            (string) ($_GET['tipo'] ?? 'obra_social')
        );
        $this->facade->createOrder($order, 'email', 'paciente@mail.com');
        require __DIR__ . '/../../views/orders.php';
    }

    public function index(): void
    {
        require __DIR__ . '/../../views/orders.php';
    }

    public function report(): void
    {
        $generador = new ReportGenerator();
        $reporte = new PdfReportDecorator(new WatermarkDecorator(
            $generador->generate('Pedidos del dia')
        ));
        echo $reporte->generate();
    }
}
