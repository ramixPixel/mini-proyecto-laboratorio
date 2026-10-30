<?php
/**
 * ============================================================================
 *  CONEXION A LA BASE DE DATOS
 *  Patron esperado: SINGLETON (creacional)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     1. obtener() crea una conexion NUEVA en cada llamada.
 *     2. Las credenciales llegan por constantes globales definidas en index.php.
 *     3. La excepcion se traga en silencio y el sistema sigue como si nada.
 *
 *  ✅ FORMA CORRECTA
 *     1. Singleton: constructor privado + instancia estatica + getInstance().
 *     2. La configuracion se inyecta, no se lee de constantes globales.
 *     3. Si la conexion falla, el error se propaga: fallar rapido y fuerte.
 *
 *  ⚠️ Advertencia de la clase: Singleton sirve para conexion, logger y
 *     configuracion. NO para guardar el pedido actual ni el usuario logueado.
 *     Ahi deja de ser un patron y pasa a ser una variable global disfrazada.
 * ============================================================================
 */

class Connection
{
    /** Almacen en memoria para poder correr la demo sin MySQL levantado. */
    private static array $tablaEnMemoria = [];

    /**
     * ❌ METODO MAL APLICADO: obtener()
     *    Devuelve una instancia nueva cada vez que se lo llama.
     *    Con 40 consultas por request, el sistema abre 40 conexiones.
     */
    public static function obtener(): Connection
    {
        return new Connection();
    }

    /*
     * ✅ FORMA CORRECTA — Singleton:
     *
     * private static ?Connection $instance = null;
     *
     * private function __construct(private PDO $pdo) {}
     *
     * public static function getInstance(array $config): Connection
     * {
     *     if (self::$instance === null) {
     *         $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4";
     *         self::$instance = new Connection(
     *             new PDO($dsn, $config['user'], $config['pass'], [
     *                 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     *             ])
     *         );
     *     }
     *     return self::$instance;
     * }
     *
     * Ejercicio: por que el constructor tiene que ser private?
     * Respuesta: para que nadie pueda hacer new Connection() desde afuera
     * y romper la garantia de instancia unica.
     */

    public function ejecutar(string $sql): void
    {
        try {
            self::$tablaEnMemoria[] = $sql;
        } catch (Throwable $e) {
            // ❌ MAL APLICADO: la excepcion se traga y el sistema sigue.
            //    El pedido "se guardo" aunque no se guardo nada.
            //    ✅ Loguear y relanzar: throw new RuntimeException('...', 0, $e);
        }
    }

    public function consultas(): array
    {
        return self::$tablaEnMemoria;
    }
}
