<?php

declare(strict_types=1);

namespace Laboratorio\Database;

use InvalidArgumentException;

/**
 * ============================================================================
 *  CONEXION A LA BASE DE DATOS
 *  Patron aplicado: SINGLETON (creacional)
 * ============================================================================
 *
 *  Una unica instancia por request. La configuracion se inyecta desde el
 *  bootstrap y los errores se propagan: fallar rapido y fuerte.
 *
 *  Advertencia: Singleton sirve para conexiones, loggers y configuracion.
 *  NO para guardar el pedido actual ni el usuario logueado; ahi pasa a ser
 *  una variable global disfrazada.
 * ============================================================================
 */
final class Connection
{
    private static ?self $instance = null;

    /** @var array<string, string> */
    private static array $config = [];

    /** Almacen en memoria para poder correr la demo sin MySQL levantado. */
    /** @var list<array{sql: string, params: array<string, int|float|string|null>}> */
    private static array $historial = [];

    private function __construct()
    {
    }

    /**
     * Recibe la configuracion externa una sola vez, desde el bootstrap.
     *
     * @param array<string, string> $config
     */
    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    /** @return array<string, string> */
    public static function config(): array
    {
        return self::$config;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param array<string, int|float|string|null> $params
     */
    public function ejecutar(string $sql, array $params = []): void
    {
        if (trim($sql) === '') {
            throw new InvalidArgumentException('La consulta SQL no puede estar vacia');
        }

        self::$historial[] = ['sql' => $sql, 'params' => $params];
    }

    /** @return list<array{sql: string, params: array<string, int|float|string|null>}> */
    public function consultas(): array
    {
        return self::$historial;
    }
}
