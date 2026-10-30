<?php

declare(strict_types=1);

namespace Laboratorio\Services;

use Laboratorio\Database\Connection;
use Laboratorio\Models\Order;

final class OrderRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(OrderResult $result): void
    {
        $this->connection->ejecutar(
            'INSERT INTO orders (id, patient, amount) VALUES (:id, :patient, :amount)',
            [
                'id' => $result->id,
                'patient' => $result->patient,
                'amount' => $result->total,
            ]
        );
    }

    /** @return list<Order> */
    public function all(): array
    {
        $rows = [
            ['id' => 1, 'patient' => 'Juan Perez', 'amount' => 15000.0, 'patientType' => 'obra_social'],
            ['id' => 2, 'patient' => 'Ana Gomez', 'amount' => 22000.0, 'patientType' => 'particular'],
        ];

        return array_map(
            static fn (array $row): Order => new Order(
                $row['id'],
                $row['patient'],
                $row['amount'],
                $row['patientType']
            ),
            $rows
        );
    }
}
