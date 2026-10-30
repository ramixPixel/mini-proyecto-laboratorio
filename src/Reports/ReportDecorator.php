<?php

declare(strict_types=1);

namespace Laboratorio\Reports;

abstract class ReportDecorator implements Report
{
    public function __construct(protected Report $report)
    {
    }
}
