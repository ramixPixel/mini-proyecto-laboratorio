# laboratorio-pedidos — proyecto de práctica

Sistema de gestión de pedidos de un laboratorio de análisis clínicos.
**PHP Vanilla, sin frameworks, sin Composer.** Corre en XAMPP tal cual está.

> ⚠️ **Este proyecto fue refactorizado aplicando 8 patrones de diseño.**
> El historial de git muestra cada patrón en su propia rama con PR propio.
> Ver `docs/DEUDA-TECNICA.md` para el diagnóstico completo.

---

## Cómo levantarlo

1. Copiar la carpeta dentro de `C:\xampp\htdocs\`.
2. Iniciar Apache desde el panel de XAMPP (MySQL **no** hace falta).
3. Abrir: `http://localhost/mini-proyecto-laboratorio/public/index.php`

Acciones disponibles:

| URL | Qué hace |
|---|---|
| `public/index.php?accion=crear` | Crea un pedido pasando por toda la arquitectura |
| `public/index.php?accion=listar` | Lista pedidos desde la vista |
| `public/index.php?accion=reporte` | Genera un reporte con decoradores |

La persistencia está simulada en memoria para que el proyecto arranque sin
configurar MySQL. Eso **no** es parte de la deuda a corregir.

---

## Arquitectura después del refactor

| Patrón aplicado | Archivos |
|---|---|
| **Autoload + Config** | `public/index.php`, `config/database.example.php` |
| **Singleton** | `src/Database/Connection.php` |
| **Strategy** | `src/Pricing/Strategies/`, `src/Pricing/PriceCalculator.php` |
| **Factory** | `src/Notifications/NotificationFactory.php`, `src/Notifications/Notification.php` |
| **Adapter** | `src/Legacy/LegacyNotifierAdapter.php`, `src/Legacy/LegacyNotifier.php` |
| **Decorator** | `src/Reports/Contracts/`, `src/Reports/Decorators/` |
| **Observer** | `src/Events/OrderObserver.php`, `src/Events/SmsObserver.php`, `src/Events/EmailObserver.php` |
| **Facade** | `src/Services/OrderService.php` + servicios cohesivos |
| **MVC + SRP** | `src/Controllers/OrderController.php` |
| **MVC (vista)** | `views/orders.php` |

---

## Mapa de deudas original

| Archivo | Síntoma sembrado | Patrón / principio | Ejercicio |
|---|---|---|---|
| `public/index.php` | Requires manuales, credenciales en el código, ruteo con `if` | Autoload + config externa + tabla de rutas | — |
| `src/Database/Connection.php` | Una conexión nueva por consulta; excepción silenciada | **Singleton** | — |
| `src/Notifications/NotificationSender.php` | `if` por tipo repetido en 3 archivos | **Factory** | Ej. 2 |
| `src/Pricing/PriceCalculator.php` | `switch` con todos los algoritmos + lógica duplicada | **Strategy** | Ej. 1 |
| `src/Events/OrderEvents.php` | Avisos encadenados a mano a clases concretas | **Observer** | Ej. 5 |
| `src/Legacy/LegacyNotifier.php` | Clase de terceros modificada + copia y pega | **Adapter** | Ej. 3 |
| `src/Reports/ReportGenerator.php` | Parámetros booleanos (`boolean trap`) | **Decorator** | Ej. 4 |
| `src/Services/OrderService.php` | Método que hace de todo | **Facade** + SRP | Ej. 6 |
| `src/Controllers/OrderController.php` | SQL + negocio + HTML en el controlador | **MVC** + SRP | Ej. 7 |
| `src/Models/Order.php` | Modelo que se persiste y calcula precios | **Repository** + Strategy | — |
| `views/orders.php` | Consulta, calcula y no escapa la salida | **MVC** | Ej. 8 |

---

## La medida de la deuda de este proyecto

**Antes del refactor:** el descuento de obra social (**0.7**) estaba escrito en **5 archivos distintos**.

```
src/Models/Order.php
src/Pricing/PriceCalculator.php   (dos veces)
src/Services/OrderService.php
src/Controllers/OrderController.php
views/orders.php
```

**Después del refactor:** el descuento queda centralizado en
`src/Pricing/Strategies/InsuranceStrategy.php`. El cambio ahora toca **1 archivo**.

---

## Mecánica de la clase práctica

```
1. Cada grupo toma UN archivo del mapa de deudas.       (5 min)
2. Lee los comentarios ❌ y ✅.                          (5 min)
3. Refactoriza en una rama propia: feat/patron-<nombre>  (20 min)
4. Abre un Pull Request que responda tres cosas:
     - qué deuda encontró (con la línea exacta)
     - qué patrón aplicó y por qué ese y no otro
     - qué consecuencia negativa tiene su propia solución
5. Otro grupo revisa el PR y comenta.                    (10 min)
```

El PR revisado por otro grupo es evidencia del TP Integrador.

---

## Reglas del refactor

- **No se rompe funcionalidad.** Antes y después, la app hace lo mismo.
- **Un patrón por rama.** Nada de una rama con seis cambios mezclados.
- **Se borra el código viejo.** Dejar el método anterior "por las dudas" es
  deuda nueva.
- **Se documenta la consecuencia negativa.** Un patrón sin contras analizadas
  es sobreingeniería esperando su turno.

## Integrantes

- **Ramiro Corrales**

---

## Historia de commits

```
feat(pricing): reemplazar switch por Strategy con interfaz y 3 implementaciones
feat(database): aplicar Singleton a Connection, constructor privado e instancia unica
feat(bootstrap): agregar autoload PSR-4 y config externo
```

Cada patrón vive en su propia rama: `feat/patron-<nombre>`.
Ver `git log --oneline` para el historial completo.
