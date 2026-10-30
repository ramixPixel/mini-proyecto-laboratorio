<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';

use Laboratorio\Database\Connection;
use Laboratorio\Events\DashboardObserver;
use Laboratorio\Events\OrderEvents;
use Laboratorio\Events\SmsObserver;
use Laboratorio\Legacy\LegacyNotifier;
use Laboratorio\Legacy\LegacyNotifierAdapter;
use Laboratorio\Models\Order;
use Laboratorio\Notifications\NotificationFactory;
use Laboratorio\Notifications\NotificationSender;
use Laboratorio\Notifications\WhatsAppNotification;
use Laboratorio\Pricing\PriceCalculator;
use Laboratorio\Pricing\PricingStrategyFactory;
use Laboratorio\Reports\BasicReport;
use Laboratorio\Reports\DigitalSignatureDecorator;
use Laboratorio\Reports\PdfReportDecorator;
use Laboratorio\Reports\WatermarkDecorator;
use Laboratorio\Services\OrderPricer;
use Laboratorio\Services\OrderRepository;
use Laboratorio\Services\OrderService;
use Laboratorio\Services\OrderValidator;

$passed = 0;
$failed = 0;

function test(string $name, callable $test): void
{
    global $passed, $failed;

    try {
        $test();
        $passed++;
        echo "PASS  {$name}\n";
    } catch (Throwable $e) {
        $failed++;
        echo "FAIL  {$name}: {$e->getMessage()}\n";
    }
}

function assertSame(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message !== '' ? $message : 'Valores distintos');
    }
}

function assertTrue(bool $actual, string $message = ''): void
{
    assertSame(true, $actual, $message);
}

function assertFloat(float $expected, float $actual, string $message = ''): void
{
    if (abs($expected - $actual) > 0.0001) {
        throw new RuntimeException(
            $message !== '' ? $message : "Se esperaba {$expected}, se obtuvo {$actual}"
        );
    }
}

function assertInstanceOf(string $class, mixed $actual, string $message = ''): void
{
    if (!$actual instanceof $class) {
        throw new RuntimeException($message !== '' ? $message : "No es instancia de {$class}");
    }
}

function assertThrows(callable $callback, string $exceptionClass, string $message = ''): void
{
    try {
        $callback();
    } catch (Throwable $e) {
        if ($e instanceof $exceptionClass) {
            return;
        }

        throw new RuntimeException(
            $message !== '' ? $message : 'Se lanzo ' . $e::class . ' en vez de ' . $exceptionClass
        );
    }

    throw new RuntimeException($message !== '' ? $message : 'No se lanzo ninguna excepcion');
}

function capture(callable $callback): string
{
    ob_start();

    try {
        $callback();
    } finally {
        $output = ob_get_clean();
    }

    return (string) $output;
}

function runAction(array $params): string
{
    $descriptors = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open(
        [PHP_BINARY, __DIR__ . '/http-smoke.php', json_encode($params, JSON_THROW_ON_ERROR)],
        $descriptors,
        $pipes
    );

    if (!is_resource($process)) {
        throw new RuntimeException('No se pudo ejecutar la accion HTTP');
    }

    $output = (string) stream_get_contents($pipes[1]);
    $error = (string) stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        throw new RuntimeException("La accion HTTP fallo: {$error}");
    }

    return $output;
}

test('Strategy: obra social aplica 30%', function (): void {
    $calculator = new PriceCalculator(PricingStrategyFactory::forPatientType('obra_social'));
    assertFloat(7000.0, $calculator->calculate(10000.0));
});

test('Strategy: particular no aplica descuento', function (): void {
    $calculator = new PriceCalculator(PricingStrategyFactory::forPatientType('particular'));
    assertFloat(10000.0, $calculator->calculate(10000.0));
});

test('Strategy: jubilado aplica 50%', function (): void {
    $calculator = new PriceCalculator(PricingStrategyFactory::forPatientType('jubilado'));
    assertFloat(5000.0, $calculator->calculate(10000.0));
});

test('Strategy: prepaga usa un descuento distinto de obra social', function (): void {
    $calculator = new PriceCalculator(PricingStrategyFactory::forPatientType('prepaga'));
    assertFloat(8500.0, $calculator->calculate(10000.0));
});

test('Factory: WhatsApp se crea sin tocar consumidores', function (): void {
    assertInstanceOf(WhatsAppNotification::class, NotificationFactory::create('whatsapp', '370'));
});

test('Factory: tipo desconocido falla rapido', function (): void {
    assertThrows(
        static fn (): mixed => NotificationFactory::create('paloma', '370'),
        InvalidArgumentException::class
    );
});

test('Factory: email conserva el asunto por defecto', function (): void {
    $output = capture(function (): void {
        NotificationFactory::create('email', 'paciente@mail.com')->send('Pedido 1 creado');
    });

    assertSame(
        '[EMAIL] para paciente@mail.com | Pedido del laboratorio: Pedido 1 creado<br>',
        $output
    );
});

test('Decorator: el encadenamiento produce el reporte esperado', function (): void {
    $report = new BasicReport('Pedidos del dia');
    $report = new DigitalSignatureDecorator($report);
    $report = new PdfReportDecorator($report);
    $report = new WatermarkDecorator($report);

    assertSame(
        'Reporte: Pedidos del dia + firma digital + PDF + marca de agua',
        $report->generate()
    );
});

test('Decorator: el orden de los decoradores cambia el resultado', function (): void {
    $report = new DigitalSignatureDecorator(new PdfReportDecorator(new BasicReport('Pedido 1')));

    assertSame('Reporte: Pedido 1 + PDF + firma digital', $report->generate());
});

test('Observer: SmsObserver se suscribe sin modificar el sujeto', function (): void {
    $dashboard = new DashboardObserver();
    $events = new OrderEvents();
    $events->subscribe($dashboard);
    $events->subscribe(new SmsObserver(new NotificationSender(), '3704000000'));

    $order = new Order(7, 'Ana Gomez', 1000.0, 'particular');

    $output = capture(function () use ($events, $order): void {
        $events->notify($order);
    });

    assertSame('[SMS] a 3704000000: Pedido 7 creado<br>', $output);
    assertSame(['Pedido 7 actualizado en el dashboard'], $dashboard->events());
});

test('Adapter: traduce send() al metodo del proveedor', function (): void {
    $adapter = new LegacyNotifierAdapter(new LegacyNotifier());

    $output = capture(function () use ($adapter): void {
        $adapter->send('Pedido creado');
    });

    assertSame('[LEGACY] Pedido creado<br>', $output);
});

test('Singleton: getInstance() devuelve siempre la misma instancia', function (): void {
    assertTrue(Connection::getInstance() === Connection::getInstance());
});

test('Facade: crea, persiste con parametros y lista pedidos', function (): void {
    $connection = Connection::getInstance();
    $before = count($connection->consultas());

    $service = new OrderService(
        new OrderValidator(),
        new OrderPricer(),
        new OrderRepository($connection),
        new OrderEvents()
    );

    $result = $service->createOrder(new Order(11, 'Juan Perez', 10000.0, 'obra_social'));

    assertFloat(7000.0, $result->total);
    assertSame($before + 1, count($connection->consultas()));

    $last = $connection->consultas()[count($connection->consultas()) - 1];
    assertSame(
        'INSERT INTO orders (id, patient, amount) VALUES (:id, :patient, :amount)',
        $last['sql']
    );
    assertSame(['id' => 11, 'patient' => 'Juan Perez', 'amount' => 7000.0], $last['params']);

    $orders = $service->listOrders();
    assertSame(2, count($orders));
    assertFloat(10500.0, $orders[0]->total);
    assertFloat(22000.0, $orders[1]->total);
});

test('Order: valida invariantes en el constructor', function (): void {
    assertThrows(
        static fn (): Order => new Order(0, 'Juan', 100.0, 'particular'),
        InvalidArgumentException::class
    );
    assertThrows(
        static fn (): Order => new Order(1, '  ', 100.0, 'particular'),
        InvalidArgumentException::class
    );
    assertThrows(
        static fn (): Order => new Order(1, 'Juan', -1.0, 'particular'),
        InvalidArgumentException::class
    );
});

test('HTTP crear: conserva email y HTML de confirmacion', function (): void {
    $output = runAction(['accion' => 'crear']);

    assertTrue(str_contains($output, '[EMAIL] para paciente@mail.com | Pedido del laboratorio: Pedido 1 creado<br>'));
    assertTrue(str_contains($output, '<h1>Pedido creado</h1>'));
    assertTrue(str_contains($output, 'Paciente: Juan Perez'));
    assertTrue(str_contains($output, 'Total: $ 10500'));
});

test('HTTP listar: conserva los dos pedidos y sus totales', function (): void {
    $output = runAction(['accion' => 'listar']);

    assertTrue(str_contains($output, 'Juan Perez'));
    assertTrue(str_contains($output, '10500'));
    assertTrue(str_contains($output, 'Ana Gomez'));
    assertTrue(str_contains($output, '22000'));
});

test('HTTP reporte: conserva el texto exacto', function (): void {
    $output = runAction(['accion' => 'reporte']);

    assertSame('Reporte: Pedidos del dia + firma digital + PDF + marca de agua', trim($output));
});

test('HTTP crear: escapa la salida del paciente', function (): void {
    $output = runAction([
        'accion' => 'crear',
        'id' => '5',
        'paciente' => '<script>alert(1)</script>',
        'monto' => '1000',
        'tipo' => 'particular',
    ]);

    assertTrue(str_contains($output, '&lt;script&gt;alert(1)&lt;/script&gt;'));
    assertTrue(!str_contains($output, '<script>alert(1)</script>'));
});

echo "\n{$passed} pruebas correctas, {$failed} fallidas.\n";

exit($failed === 0 ? 0 : 1);
