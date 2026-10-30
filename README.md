# laboratorio-pedidos

Sistema de gestión de pedidos de un laboratorio de análisis clínicos.
**PHP vanilla, sin frameworks y sin Composer.** Está pensado para correr en
XAMPP o con el servidor embebido de PHP.

El proyecto nació con deuda técnica sembrada a propósito. Este repositorio es
el resultado del refactor: se aplicaron ocho patrones de diseño, se separaron
las capas y se agregó una suite de pruebas sin dependencias externas.

- **Estado:** 8/8 patrones aplicados, 19 pruebas pasando.
- **Entrega:** TP Integrador — Metodología de Sistemas II — UTN FRRe (sede Formosa).
- **Integrante:** Ramiro Corrales.

---

## Requisitos

- PHP **8.1 o superior** (se usa `readonly`, `match`, `str_starts_with` y tipos union).
- Un servidor web con PHP (XAMPP, Laragon) o el servidor embebido.
- No hace falta Composer, MySQL ni extensiones especiales.

Verificar la versión:

```bash
php -v
```

---

## Cómo levantar el proyecto

### Opción 1: servidor embebido (la más rápida)

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t public
```

Abrir:

```
http://localhost:8000/index.php?accion=crear
```

### Opción 2: XAMPP

1. Copiar la carpeta dentro de `C:\xampp\htdocs\`.
2. Iniciar Apache desde el panel de XAMPP (MySQL **no** hace falta).
3. Abrir:

```
http://localhost/mini-proyecto-laboratorio/public/index.php?accion=crear
```

### Acciones disponibles

| URL | Qué hace |
|---|---|
| `public/index.php?accion=crear` | Crea un pedido de ejemplo, calcula el total, notifica y muestra la confirmación. |
| `public/index.php?accion=listar` | Lista los pedidos desde la vista. |
| `public/index.php?accion=reporte` | Genera el reporte con firma digital, PDF y marca de agua. |
| Cualquier otra acción | Responde `404 Accion no encontrada`. |

La persistencia está simulada en memoria para que el proyecto arranque sin
MySQL. **Eso no es deuda a corregir en este TP.**

---

## Configuración

La configuración real no se versiona. Todo `config/` está ignorado salvo
`config/database.example.php`. El arranque usa el ejemplo si no existe
`config/database.php`.

Si se quiere preparar una configuración local:

```bash
cp config/database.example.php config/database.php
```

En PowerShell:

```powershell
Copy-Item config/database.example.php config/database.php
```

Todo `config/` está en `.gitignore` salvo el ejemplo. No commitear credenciales.

> La demo actual no abre una conexión PDO: `Connection` guarda las consultas
> en memoria. La configuración queda lista para cuando se conecte una base real.

---

## Estructura

```
bootstrap/autoload.php        Autoload PSR-4 para el prefijo Laboratorio\
config/                       Configuración externa (local ignorada; ejemplo versionado)
public/index.php              Composition root: dependencias y tabla de rutas
src/Controllers/              Controladores delgados (MVC)
src/Database/                 Connection (Singleton)
src/Events/                   OrderEvents + observadores (Observer)
src/Legacy/                   LegacyNotifier + adapter (Adapter)
src/Models/                   Order inmutable
src/Notifications/            Notification + canales + factory (Factory)
src/Pricing/                  Estrategias y calculador de precios (Strategy)
src/Reports/                  Report + decoradores (Decorator)
src/Services/                 Facade y colaboradores (Facade + SRP)
tests/                        Runner propio y smoke tests HTTP
views/                        Vistas con salida escapada
```

### Patrones aplicados

| Patrón | Dónde | Para qué |
|---|---|---|
| **Strategy** | `src/Pricing/` | Cambiar el descuento según el tipo de paciente sin tocar el calculador. |
| **Factory** | `src/Notifications/` | Crear el canal de notificación sin repetir `if` en cada capa. |
| **Observer** | `src/Events/` | Suscribir email, SMS y dashboard sin modificar `OrderEvents`. |
| **Adapter** | `src/Legacy/` | Usar `LegacyNotifier::sendMessage()` sin modificar la clase de terceros. |
| **Decorator** | `src/Reports/` | Combinar firma, PDF y marca de agua sin banderas booleanas. |
| **Facade** | `src/Services/` | Una fachada simple sobre validación, precio, persistencia y reporte. |
| **Singleton** | `src/Database/Connection.php` | Una sola conexión por request. |
| **MVC** | `src/Controllers/` + `views/` | Separar controlador, servicio y presentación. |

Cada patrón tiene su rama y su PR. El detalle de la deuda, la evidencia y la
consecuencia negativa de cada decisión está en
[`docs/DEUDA-TECNICA.md`](docs/DEUDA-TECNICA.md).

---

## Pruebas

No se usa PHPUnit para no agregar una dependencia que el proyecto no tenía.
El runner es propio y prueba los patrones y las tres acciones reales.

```bash
php tests/run.php
```

Salida esperada:

```
19 pruebas correctas, 0 fallidas.
```

Qué cubre:

- Strategy: obra social, particular, jubilado y prepaga.
- Factory: email, WhatsApp y error de tipo desconocido.
- Decorator: combinación y orden de decoradores.
- Observer: SMS y dashboard sin tocar el sujeto.
- Adapter: traducción al proveedor legacy.
- Singleton: instancia única.
- Facade/Repository: cálculo, persistencia con parámetros y listado.
- Invariantes de `Order`.
- Smoke tests HTTP de `crear`, `listar` y `reporte`.
- Escape de salida (XSS) en la confirmación.
- Error de validación mostrado sin fatal (paciente vacío).

Los smoke tests lanzan `tests/http-smoke.php` en un proceso aparte, setean
`$_GET` y ejecutan `public/index.php` como lo haría el servidor.

---

## Cómo extender el proyecto

### Agregar un tipo de paciente nuevo

1. Crear la estrategia en `src/Pricing/Strategies/` implementando
   `PricingStrategy`.
2. Registrarla en `PricingStrategyFactory::forPatientType()`.
3. Agregar un caso en `tests/run.php`.

Son **2 archivos de producción** más el test. Antes del refactor había que
tocar modelo, calculador, servicio, controlador y vista.

### Cambiar el descuento de obra social

Se edita `src/Pricing/Strategies/InsuranceStrategy.php` y su prueba. Es
**1 archivo**. Antes el `0.7` estaba repetido en 5 archivos.

### Agregar un canal de notificación

1. Implementar `Notification` en `src/Notifications/`.
2. Agregar el caso en `NotificationFactory::create()`.
3. Agregar la prueba.

Los consumidores (`OrderService`, `OrderEvents`, controlador) no cambian.

### Agregar un observador

Implementar `OrderObserver` y suscribirlo en `public/index.php`. No se toca
`OrderEvents`.

---

## Flujo de trabajo y PRs

La serie respeta una rama y un PR por patrón:

| PR | Rama | Cambio |
|---|---|---|
| [#1](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/1) | `chore/restaurar-linea-base` | Restaura la línea base antes del refactor. |
| [#2](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/2) | `refactor/bootstrap-autoload-config-router` | Autoload, configuración externa y tabla de rutas. |
| [#3](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/3) | `feat/patron-singleton` | Singleton de conexión. |
| [#4](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/4) | `feat/patron-strategy` | Estrategias de precio. |
| [#5](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/5) | `feat/patron-factory` | Factory de notificaciones. |
| [#6](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/6) | `feat/patron-observer` | Observadores de pedidos. |
| [#7](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/7) | `feat/patron-adapter` | Adapter del proveedor legacy. |
| [#8](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/8) | `feat/patron-decorator` | Decoradores de reporte. |
| [#9](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/9) | `feat/patron-facade` | Facade y servicios cohesivos. |
| [#10](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/10) | `feat/patron-mvc` | Controlador delgado y vistas. |
| [#11](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/11) | `test/runner-sin-dependencias` | Runner de pruebas y smoke tests. |
| [#12](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/12) | `docs/deuda-tecnica-readme` | README reproducible y `docs/DEUDA-TECNICA.md`. |
| [#13](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/13) | `fix/cumplimiento-consigna` | `createOrder` en menos de 10 líneas y `config/` ignorado salvo el ejemplo. |
| [#14](https://github.com/ramixPixel/mini-proyecto-laboratorio/pull/14) | `fix/validacion-crear` | Error de validación mostrado sin fatal. |

La serie se mergeó en orden; no hubo conflictos que resolver en Git.

Cada PR de patrón responde en su descripción:

1. Qué deuda se encontró, con archivo y línea.
2. Qué patrón se aplicó y por qué ese y no otro.
3. Qué consecuencia negativa tiene la solución.

**Revisión cruzada pendiente:** los PR fueron creados y mergeados por el autor
para poder avanzar con la serie. Antes de la entrega, otro integrante debe
revisar al menos los PR de los patrones y dejar comentarios en GitHub.

---

## Decisiones y consecuencias

Ningún patrón es gratis. Las consecuencias asumidas están documentadas en
`docs/DEUDA-TECNICA.md`; las principales:

- **Strategy:** más clases para un cálculo chico, pero el descuento vive en un
  solo lugar.
- **Factory:** centraliza la creación, pero la factory crece con cada canal.
- **Observer:** desacopla, pero una excepción en un observador corta la cadena.
- **Adapter:** aísla al proveedor, pero agrega una indirección.
- **Decorator:** composición flexible, pero el orden importa y es manual.
- **Facade:** simplifica al consumidor, pero multiplica archivos.
- **Singleton:** garantiza una instancia, pero es estado global.
- **MVC:** separa responsabilidades, pero las vistas siguen siendo PHP embebido.

## Deuda que queda

- Persistencia simulada en memoria (no se conecta MySQL).
- Sin CI: las pruebas se corren a mano.
- Autoloader y runner propios; el siguiente paso sería Composer + PHPUnit.
- El controlador todavía castea `$_GET`; falta una capa de request.
- La revisión cruzada de los PR queda pendiente de otro integrante.

---

## Pregunta de cierre

> ¿Cuántos archivos hay que tocar para agregar un tipo de paciente nuevo?

Antes: **5 archivos** (modelo, calculador, servicio, controlador y vista).
Después: **2 archivos de producción** (nueva estrategia y registro en la
factory) más el test. El cambio de un descuento existente bajó de 5 archivos a
1.
