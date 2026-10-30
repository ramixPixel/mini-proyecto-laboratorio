<?php

declare(strict_types=1);

namespace Laboratorio\Reports;

final class PdfReportDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return $this->report->generate() . ' + PDF';
    }
}
