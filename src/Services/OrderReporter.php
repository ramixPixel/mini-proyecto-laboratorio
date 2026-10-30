<?php

declare(strict_types=1);

namespace Laboratorio\Services;

use Laboratorio\Reports\BasicReport;
use Laboratorio\Reports\DigitalSignatureDecorator;
use Laboratorio\Reports\PdfReportDecorator;
use Laboratorio\Reports\WatermarkDecorator;

final class OrderReporter
{
    public function dailyReport(): string
    {
        $report = new BasicReport('Pedidos del dia');
        $report = new DigitalSignatureDecorator($report);
        $report = new PdfReportDecorator($report);
        $report = new WatermarkDecorator($report);

        return $report->generate();
    }

    public function forOrder(OrderResult $order): string
    {
        $report = new BasicReport("Pedido {$order->id}");
        $report = new DigitalSignatureDecorator($report);
        $report = new PdfReportDecorator($report);

        return $report->generate();
    }
}
