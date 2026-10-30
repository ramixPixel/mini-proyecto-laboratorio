<?php

declare(strict_types=1);

namespace Laboratorio\Reports;

final class BasicReport implements Report
{
    public function __construct(private string $content)
    {
    }

    public function generate(): string
    {
        return "Reporte: {$this->content}";
    }
}
