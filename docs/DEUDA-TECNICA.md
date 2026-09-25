# Deuda Tecnica — laboratorio-pedidos

## Resumen del diagnostico

| # | Síntoma observado | Evidencia (archivo:línea) | Tipo de deuda | Patrón aplicado | Consecuencia asumida |
|---|---|---|---|---|---|
| 1 | El descuento de obra social (`0.7`) esta escrito en **5 archivos** | `Order.php`, `PriceCalculator.php` (x2), `OrderService.php`, `OrderController.php`, `views/orders.php` | Diseno / DRY | Strategy | 3 archivos nuevos por un `switch` de 10 lineas |
| 2 | `Connection::obtener()` crea una conexion nueva por cada llamada | `Database/Connection.php:34` | Recursos | Singleton | Singleton mal aplicado es una variable global disfrazada |
| 3 | `if` por tipo de notificacion repetido en 3 archivos | `NotificationSender.php`, `OrderService.php`, `OrderController.php` | Diseño / OCP | Factory | Agregar WhatsApp obliga a tocar 3 archivos |
| 4 | Clase de terceros modificada (`send()`) + copia/pega (`CopiaDeLegacyEnNuestroSistema`) | `Legacy/LegacyNotifier.php:38-55` | DRY + limites | Adapter | El codigo de terceros se rompe al actualizar |
| 5 | Banderas booleanas en `ReportGenerator::generate()` | `Reports/ReportGenerator.php:29-33` | OCP | Decorator | Con 5 banderas hay 32 combinaciones imposibles de probar |
| 6 | Encadenamiento directo a clases concretas en `OrderEvents::pedidoCreado()` | `Events/OrderEvents.php:30-40` | DIP | Observer | Si un observador falla, los siguientes no se ejecutan |
| 7 | `procesarPedidoCompleto()` tiene 7 responsabilidades en un solo metodo | `Services/OrderService.php:34-89` | SRP | Facade | Clase que nadie quiere tocar, cualquier cambio rompe otra cosa |
| 8 | Controlador mezcla SQL, reglas de negocio y HTML | `Controllers/OrderController.php:30-53` | SRP + MVC | MVC | El "God Controller" crece sin limite |
| 9 | Vista consulta DB, calcula totales y no escapa salida | `views/orders.php:24-49` | MVC + seguridad | MVC | XSS directo, regla de negocio en la capa de presentacion |
| 10 | Requires manuales, credenciales versionadas, ruteo con `if` | `public/index.php:23-74` | Proceso + Diseño | Autoload + Tabla de rutas | Cada clase nueva obliga a editar el punto de entrada |

## Metrica del proyecto

**Antes del refactor:** el descuento `0.7` aparece en **5 archivos**.
**Despues del refactor:** el descuento queda centralizado en `src/Pricing/Strategies/InsuranceStrategy.php` (y `PrepaidStrategy` con el descuento configurable).

**Bajaron de 5 a 1 archivo** para cambiar el valor del descuento de obra social.

## Deuda de Proceso (Unidad 1)

| # | Deuda | Evidencia | Patrón de proceso aplicado |
|---|---|---|---|
| 1 | Credenciales versionadas | `public/index.php:49-52` | Config externo (`config/database.example.php`) |
| 2 | Requires manuales | `public/index.php:23-33` | Autoload PSR-4 |
| 3 | Ruteo con `if` | `public/index.php:66-74` | Tabla de rutas (pendiente de implementar) |
| 4 | Un solo commit con todo | Historial de git | Ramas + PR con revision cruzada |

## Consecuencias negativas asumidas

1. **Strategy**: 3 archivos nuevos por un `switch` de 10 lineas. Para proyectos chicos, el `switch` puede ser suficiente. El costo de Strategy solo se justifica cuando se esperan muchos tipos de paciente nuevos.
2. **Factory**: La abstraccion de la fabrica puede ser excesiva si solo existen 2 tipos de notificacion y no se esperan cambios.
3. **Observer**: Con Observer, leyendo el codigo del pedido **NO** se ve quien se entera del evento. Se pierde trazabilidad visual. Se compensa con log y nombres explicitos en los observadores.
4. **Decorator**: El orden de los decoradores cambia el resultado. Firmar y despues pasar a PDF no es lo mismo que al reves. Esta restriccion debe documentarse.
5. **Facade**: La fachada coordina pero no decide. Si empieza a decidir reglas de negocio, se convierte en la nueva clase que hace todo.
6. **Adapter**: Si el proveedor cambia la interfaz, solo cambia el Adapter. Pero el Adapter puede crecer mucho si la incompatibleidad es compleja.
