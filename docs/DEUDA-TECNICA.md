# Deuda técnica — laboratorio-pedidos

Documento de cierre del TP Integrador (Unidades 1 y 2). Registra la deuda
encontrada, la evidencia concreta, el patrón aplicado y la consecuencia
asumida.

> **Sobre la evidencia:** las líneas citadas corresponden al commit base
> `c657de9` (`chore: initialize design patterns project`), que es el estado
> anterior al refactor. En `main` esos archivos ya no existen o fueron
> reemplazados. Para verificarlas:
>
> ```bash
> git show c657de9:src/Pricing/PriceCalculator.php
> git log --oneline c657de9
> ```

## Tabla de deuda

| Síntoma observado | Evidencia (archivo:línea) | Tipo de deuda | Patrón aplicado | Consecuencia asumida |
|---|---|---|---|---|
| El descuento de obra social (`0.7`) está escrito en 5 archivos y 6 lugares; si cambia, hay que acordarse de todos | `src/Models/Order.php:103`, `src/Pricing/PriceCalculator.php:34`, `src/Pricing/PriceCalculator.php:53`, `src/Services/OrderService.php:57`, `src/Controllers/OrderController.php:39`, `views/orders.php:49` | Diseño — DRY / SRP | **Strategy** (`PricingStrategy`, `InsuranceStrategy`, `PricingStrategyFactory`) | Cinco clases nuevas y una factory para un cálculo de dos líneas. Si el descuento depende del convenio, hay que tocar la estrategia y su test, no un `if` suelto. |
| El `if` por tipo de notificación está repetido en tres capas: sender, servicio y eventos | `src/Notifications/NotificationSender.php:33-42`, `src/Services/OrderService.php:71-77`, `src/Events/OrderEvents.php:31-36` | Diseño — OCP / DIP | **Factory** (`NotificationFactory`, `EmailNotification`, `SmsNotification`, `WhatsAppNotification`) | Toda alta de canal pasa por la factory. Si un canal necesita credenciales o plantillas propias, la factory empieza a parecerse a un contenedor de dependencias. |
| Los avisos internos están encadenados a clases concretas dentro del sujeto | `src/Events/OrderEvents.php:31-39` | Diseño — DIP | **Observer** (`OrderEvents`, `EmailObserver`, `SmsObserver`, `DashboardObserver`) | El orden de los observadores importa y una excepción en uno corta la cadena. No hay reintentos ni cola de eventos. |
| Se modificó una clase de terceros para agregarle `send()` y se duplicó su comportamiento en una copia propia | `src/Legacy/LegacyNotifier.php:38-52` | Diseño — DRY / límites del sistema | **Adapter** (`LegacyNotifierAdapter`) | Una indirección más. El adapter queda acoplado a la versión actual del proveedor; si cambia `sendMessage()`, se toca solo ahí. |
| El reporte usa tres banderas booleanas (`firma`, `pdf`, `marca de agua`) y crece con cada combinación | `src/Reports/ReportGenerator.php:28-50` | Diseño — OCP | **Decorator** (`BasicReport`, `DigitalSignatureDecorator`, `PdfReportDecorator`, `WatermarkDecorator`) | El orden de los decoradores cambia el resultado y la composición es manual. Para tres banderas puede ser más código que un `if`, pero agregar una cuarta no obliga a tocar las anteriores. |
| Un único método valida, calcula, persiste, notifica, reporta e imprime HTML | `src/Services/OrderService.php:34-89` | Diseño — SRP | **Facade** (`OrderService`, `OrderValidator`, `OrderPricer`, `OrderRepository`, `OrderReporter`) | Se multiplican los archivos y hay que navegar por el facade. La complejidad no desaparece: se distribuye en colaboradores más chicos. |
| El controlador tiene SQL, regla de negocio, `echo` y arma HTML | `src/Controllers/OrderController.php:30-53` | Diseño — SRP / MVC | **MVC** con inyección de dependencias y composition root | El controlador sigue leyendo y casteando `$_GET`. Una capa de request/validación de entrada sería el próximo paso. |
| La vista consulta, calcula el descuento y muestra datos sin escapar | `views/orders.php:34-51` | Diseño + seguridad — MVC / XSS | **MVC** + `htmlspecialchars` en las vistas | Las vistas siguen siendo PHP embebido. Un motor de plantillas sería otra dependencia que el TP no pide. |
| El modelo se persiste a sí mismo y además calcula precios | `src/Models/Order.php:62-68`, `src/Models/Order.php:96-108` | Diseño — SRP | **Repository** + **Strategy**; `Order` inmutable | El repositorio en memoria no prueba SQL real. El modelo inmutable obliga a reconstruir el pedido para modificarlo. |
| Cada consulta crea una conexión nueva y la excepción de guardado se traga | `src/Database/Connection.php:34-37`, `src/Database/Connection.php:66-72` | Diseño — gestión de recursos / errores | **Singleton** (`Connection::configure`, `getInstance`, `ejecutar`) | Estado global difícil de aislar en tests. Una sola conexión por proceso limita escenarios con réplicas o múltiples bases. |
| El arranque depende de 11 `require` manuales, credenciales en el código y un `if/elseif` de rutas | `public/index.php:23-33`, `public/index.php:49-52`, `public/index.php:60-74` | Proceso — arranque/configuración + Diseño | Autoload PSR-4, `config/database.example.php` y tabla de rutas | El autoloader es propio y hay que mantenerlo. Una configuración incompleta falla recién al conectar, no al arrancar. |
| Todo el proyecto entró en un único commit inicial, sin ramas ni PR | `git log --oneline c657de9` (un solo commit) | Proceso — historial | Una rama y un PR por patrón, con descripción de tres preguntas | Más overhead de ramas y merges. El historial queda más ruidoso, pero cada cambio es auditable y reversible. |
| No había tests ni forma reproducible de verificar el comportamiento | No existe `tests/` en `c657de9` | Proceso — verificación | Runner propio sin dependencias + smoke tests HTTP | Runner casero sin mocks, data providers ni cobertura. Los smoke tests lanzan un proceso PHP por acción. |

## Métrica: el `0.7`

| Antes | Después |
|---|---|
| 5 archivos, 6 apariciones del descuento | 1 archivo: `src/Pricing/Strategies/InsuranceStrategy.php` |

- **Cambiar el descuento de obra social:** antes 5 archivos; ahora 1 archivo
  (`InsuranceStrategy`) más su test.
- **Agregar un tipo de paciente nuevo:** antes había que tocar modelo,
  calculador, servicio, controlador y vista (5 archivos); ahora se agrega la
  estrategia y se registra en `PricingStrategyFactory` (2 archivos) más el
  test.

## Patrones y PRs

| Patrón | Rama | PR | Archivos clave |
|---|---|---|---|
| Bootstrap (autoload, config, rutas) | `refactor/bootstrap-autoload-config-router` | [#2](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/2) | `bootstrap/autoload.php`, `config/`, `public/index.php` |
| Singleton | `feat/patron-singleton` | [#3](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/3) | `src/Database/Connection.php` |
| Strategy | `feat/patron-strategy` | [#4](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/4) | `src/Pricing/` |
| Factory | `feat/patron-factory` | [#5](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/5) | `src/Notifications/` |
| Observer | `feat/patron-observer` | [#6](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/6) | `src/Events/` |
| Adapter | `feat/patron-adapter` | [#7](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/7) | `src/Legacy/` |
| Decorator | `feat/patron-decorator` | [#8](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/8) | `src/Reports/` |
| Facade | `feat/patron-facade` | [#9](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/9) | `src/Services/` |
| MVC | `feat/patron-mvc` | [#10](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/10) | `src/Controllers/`, `views/` |
| Tests | `test/runner-sin-dependencias` | [#11](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/11) | `tests/` |

El PR [#1](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/1)
restauró la línea base antes de empezar la serie.

## Verificación

```bash
php tests/run.php
# 19 pruebas correctas, 0 fallidas.
```

Las tres acciones conservan su salida:

```bash
php -S localhost:8000 -t public
# http://localhost:8000/index.php?accion=crear
# http://localhost:8000/index.php?accion=listar
# http://localhost:8000/index.php?accion=reporte
```

## Deuda que queda (a propósito)

- La persistencia sigue simulada en memoria. El TP no pide conectar MySQL y
  hacerlo sin una base disponible rompería la reproducibilidad.
- No hay CI. Los tests se corren a mano con `php tests/run.php`.
- El autoloader y el runner de tests son propios; si el proyecto crece, el
  reemplazo natural es Composer + PHPUnit.
- `Singleton` conserva estado global. Para esta demo es aceptable; en un
  sistema con múltiples conexiones habría que inyectar la conexión.
- **Revisión cruzada pendiente:** los PR de esta serie fueron creados y
  mergeados por el autor para poder avanzar. Antes de la entrega, otro
  integrante debe revisar al menos los PR de los patrones y dejar comentarios
  en GitHub. El historial ya está preparado para esa revisión.
