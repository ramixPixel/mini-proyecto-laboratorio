<?php
/**
 * ============================================================================
 *  CONEXION A LA BASE DE DATOS
 *  Patron aplicado: SINGLETON (creacional)
 * ============================================================================
 *
 *  ✅ REFACTORIZADO: constructor privado + instancia estatica.
 *     Unica conexion por request. Falla rapida y fuerte.
 * ============================================================================
 */

class Connection
{
    private static ?Connection $instance = null;

    /** Almacen en memoria para poder correr la demo sin MySQL levantado. */
    private static array $tablaEnMemoria = [];

    private function __construct() {}

    public static function obtener(): Connection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function ejecutar(string $sql): void
    {
        try {
            self::$tablaEnMemoria[] = $sql;
        } catch (Throwable $e) {
            throw new RuntimeException('Error al ejecutar: ' . $e->getMessage(), 0, $e);
        }
    }

    public function consultas(): array
    {
        return self::$tablaEnMemoria;
    }
}
