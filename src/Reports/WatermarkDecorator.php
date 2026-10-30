<?php

declare(strict_types=1);

namespace Laboratorio\Reports;

final class WatermarkDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return $this->report->generate() . ' + marca de agua';
    }
}
