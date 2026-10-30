<?php

declare(strict_types=1);

namespace Laboratorio\Reports;

interface Report
{
    public function generate(): string;
}
