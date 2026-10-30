<?php

declare(strict_types=1);

namespace Laboratorio\Reports;

final class DigitalSignatureDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return $this->report->generate() . ' + firma digital';
    }
}
