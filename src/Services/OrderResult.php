<?php

declare(strict_types=1);

namespace Laboratorio\Services;

final class OrderResult
{
    public function __construct(
        public readonly int $id,
        public readonly string $patient,
        public readonly float $total
    ) {
    }
}
