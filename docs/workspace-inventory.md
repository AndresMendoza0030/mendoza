# Adaptador de lectura de Inventario para Mendoza Workspace

Base revisada: `main` en `8830ab596d237d84082e290c8ae4a0a9bff78a28`.

Este cambio conserva la aplicación Laravel, sus usuarios, roles, operaciones y base de datos. Añade una API de consulta para el backend de Workspace. No migra MySQL, no replica saldos, no crea usuarios ni asigna roles y no añade operaciones de compra, aprobación, despacho o ajuste.

## Arquitectura y límites de confianza

1. El navegador consulta el backend de Workspace con su sesión habitual.
2. Workspace comprueba identidad y concesión individual de Inventario.
3. El backend llama a Laravel por HTTPS con el token de integración y el correo verificado del usuario.
4. Laravel resuelve la cuenta existente por correo, exige `estatus=0` y vuelve a consultar el permiso vigente en Workspace.
5. Solo después aplica los roles/permisos locales y consulta campos explícitos.
6. Workspace vuelve a comprobar la concesión antes de entregar la respuesta al navegador.

El encabezado `X-Workspace-Actor` solo es fiable junto con el token del gateway. Workspace debe fijarlo desde su identidad verificada; nunca debe reenviar este encabezado, cookies de Laravel ni un correo arbitrario recibido del navegador. Ambos tokens son secretos de servidor.

## Configuración

Configurar en Laravel, mediante el mecanismo de secretos del despliegue:

| Variable Laravel | Uso |
| --- | --- |
| `WORKSPACE_INVENTORY_TOKEN` | Secreto recibido del Worker en `Authorization: Bearer …`; al menos 32 caracteres, sin espacios exteriores ni saltos de línea. |
| `WORKSPACE_ACCESS_URL` | URL HTTPS completa del callback; ruta exacta `/api/integrations/inventory/access`, sin query ni fragmento. |
| `WORKSPACE_PERMISSION_TOKEN` | Secreto distinto, de al menos 32 caracteres, para llamar al callback de permisos. |
| `WORKSPACE_ACCESS_CLIENT_ID` | Opcional: identificador del service token de Cloudflare Access para el callback. |
| `WORKSPACE_ACCESS_CLIENT_SECRET` | Opcional: secreto correspondiente; configurar ambos valores o ninguno. |
| `WORKSPACE_INVENTORY_ENFORCE_WEB_ACCESS` | `false` durante la preparación; `true` para controlar también sesiones y URLs directas de Laravel. |

No hay valores predeterminados para los secretos. Los identificadores y secretos de Cloudflare se envían como `CF-Access-Client-Id` y `CF-Access-Client-Secret`.

Correspondencia con Workspace:

| Workspace | Laravel |
| --- | --- |
| `INVENTORY_API_BASE_URL` | URL HTTPS terminada en `/api/workspace-inventory` |
| `INVENTORY_API_TOKEN` | Mismo valor que `WORKSPACE_INVENTORY_TOKEN` |
| `INVENTORY_PERMISSION_TOKEN` | Mismo valor que `WORKSPACE_PERMISSION_TOKEN` |
| `INVENTORY_ACCESS_CLIENT_ID/SECRET` | Credenciales opcionales para el trayecto Workspace → Laravel; pueden ser diferentes de las del callback. |

El callback recibe:

```json
{"email":"usuario@example.test"}
```

Y debe responder HTTP 200, `Content-Type: application/json`, con tipos JSON estrictos:

```json
{"allowed":true,"version":4}
```

`allowed` debe ser booleano y `version` un entero no negativo. Se hace una llamada nueva por petición; no hay caché ni reintentos. El timeout total es de 5 segundos y el de conexión de 3. No se siguen redirecciones y la verificación TLS permanece activada. Una respuesta HTML, una respuesta mal formada, un timeout o cualquier estado distinto de 200 deniegan la consulta con 503. Una decisión `allowed:false` devuelve 403.

### HTTPS y proxy

Laravel exige `Request::isSecure()`. Preferir HTTPS también entre el proxy y el origen. Si TLS termina antes de Laravel, configurar `TrustProxies` para los proxies reales y revisar los encabezados reenviados; no confiar indiscriminadamente en encabezados del cliente para satisfacer esta comprobación.

No se modifica CORS: el navegador no llama al adaptador directamente. Las respuestas del adaptador son privadas y `no-store`.

## Roles y alcance local

La concesión de Workspace habilita el módulo, pero no sustituye los permisos Laravel:

| Rol o permiso existente del guard `web` | Catálogo | Existencias | Movimientos | Solicitudes |
| --- | --- | --- | --- | --- |
| `gerente` o `bodega` | Sí | Sí | Sí | Todas |
| `solicitante` | No | No | No | Solo las propias |
| Ninguno de los anteriores | No | No | No | No |

La cuenta debe existir de forma inequívoca por correo y tener estado explícito 0. Un estado ausente o nulo no se interpreta como activo. No hay alta automática, promoción por el encabezado ni traducción automática de un rol Workspace a un rol Laravel.

En el bootstrap, un solicitante recibe únicamente las bodegas asociadas a sus propias solicitudes. Los filtros `user_id` u otros parámetros no amplían ese alcance.

## Contrato HTTP

Todos los recursos son GET bajo `/api/workspace-inventory`; no se añaden rutas de escritura. La ruta heredada `GET /api/user` permanece intacta.

### `GET /bootstrap`

```json
{
  "schema_version": 1,
  "source": "mendoza",
  "user": {"email":"usuario@example.test","name":"Usuario de ejemplo"},
  "capabilities": {"catalog":true,"stocks":true,"movements":true,"requests":true,"write":false},
  "warehouses": [{"id":1,"name":"Bodega de ejemplo"}]
}
```

### Listados

`GET /products`, `/stocks`, `/movements` y `/requests` responden:

```json
{"items":[],"page":1,"per_page":20,"total":0,"pages":1}
```

Filtros:

- `q`: texto de hasta 100 caracteres.
- `page`: entero 1–10000; predeterminado 1.
- `per_page`: entero 1–50; predeterminado 20.
- `warehouse_id`: entero positivo en existencias, movimientos y solicitudes.
- `product_id`: entero positivo en movimientos.
- `from` y `to`: fechas `YYYY-MM-DD` en movimientos; si se indican ambas, `to` no puede preceder a `from`.

Los filtros inválidos devuelven 422. El total se calcula después de aplicar el alcance de usuario y los filtros. Una página fuera del resultado puede estar vacía sin alterar el total. Las búsquedas usan parámetros SQL enlazados y columnas fijas.

| Recurso | Campos de cada elemento |
| --- | --- |
| Productos | `id, code, name, presentation, active` |
| Existencias | `product_id, product_name, code, warehouse_id, warehouse_name, quantity` |
| Movimientos | `id, date, type, direction, product_id, product_name, warehouse_id, warehouse_name, quantity, document_id` |
| Solicitudes | `id, date, type, warehouse_id, warehouse_name, requester_name, status` |

Los IDs son enteros. `code` y `presentation` pueden ser nulos. La bodega de una solicitud puede ser nula si falta la relación. Las cantidades se devuelven como cadenas decimales numéricas para evitar pérdida de precisión al convertir el saldo fuente a float.

El catálogo contiene solo `tipo=PRODUCTO`; `active` expresa si `estatus=0`. No se exponen costos, proveedores, pacientes, notas privadas ni credenciales.

### Semántica de saldos e historial

`quantity` en existencias procede exclusivamente de `productstocks.existencia`. No representa disponibilidad después de reservas. No se fabrican filas con saldo cero cuando no existe un registro de stock. Un saldo nulo o no numérico produce un error 503.

Los movimientos son el historial registrado en `movdetails`, sin reconstruir saldos ni inferir reversión a partir de columnas ausentes en las migraciones. `type` y `direction` conservan el valor de la fuente. La UI no debe convertir este listado en un total disponible.

Estado de solicitudes:

| Valor | Interpretación |
| --- | --- |
| `pending` | `estatus=0` y no despachada |
| `authorized` | `estatus=1` y no despachada |
| `dispatched` | `despachado=S` |
| `other` | Otro valor registrado |

No se consulta `reqhead.autorizado`, ausente de las migraciones originales. La API tampoco utiliza la lógica ambigua de stock comprometido de las alertas.

### Errores

Los errores llevan `{error,code}`, nunca una lista vacía sustitutiva:

- 400: HTTPS requerido.
- 401: token del gateway incorrecto o sesión web ausente.
- 403: cuenta inexistente/inactiva, rol insuficiente o concesión revocada.
- 422: filtros inválidos; incluye `errors`.
- 503: configuración incompleta, callback no verificable o fuente no disponible.

Un fallo de consulta SQL se convierte en 503, sin exponer SQL ni trazas del origen. El Worker debe distinguir revocación de un fallo de servicio y retirar datos anteriores cuando se pierde el permiso.

## Acceso directo y sesiones existentes

El middleware `WorkspaceInventoryWebAccess` se añade al grupo `web`, después de iniciar la sesión. Cuando el flag está activo, consulta la misma decisión de Workspace en cada petición autenticada. Esto incluye las rutas de PDF y, con la configuración predeterminada de Livewire 2.7, mensajes, uploads y previews de Livewire.

Se conservan las páginas de login, recuperación de contraseña y POST logout. Logout sigue disponible durante una revocación o caída del callback. El estado activo también se exige para sesiones antiguas. Se conservan las comprobaciones de rol y CSRF existentes.

**Con el flag en false, el acceso directo de Laravel conserva el comportamiento anterior. No se puede declarar acceso unificado ni revocación completa mientras permanezca desactivado.**

El flag no vuelve privados archivos que ya fueran públicos y servidos directamente por el servidor web. Si el despliegue expone documentos sensibles como archivos estáticos, deben protegerse en el origen. No se cambian esos directorios en este PR.

## Activación conservadora

1. Revisar el PR y completar lint y Feature tests en CI.
2. Confirmar que el despliegue y esquema reales corresponden al código base; este repositorio no contiene todas las migraciones del sistema operativo.
3. Configurar los dos tokens distintos y el callback HTTPS en ambos servidores. Actualizar la caché de configuración por el procedimiento habitual del despliegue.
4. Asignar explícitamente los usuarios permitidos en Workspace y verificar las cuentas/roles existentes en Laravel.
5. Probar desde el Worker el bootstrap y consultas con un gerente/bodega y un solicitante, incluyendo 403 y caída del callback.
6. Activar `WORKSPACE_INVENTORY_ENFORCE_WEB_ACCESS=true`. Comprobar que una sesión Laravel ya abierta queda bloqueada tras la revocación, también en PDF y Livewire, y que logout funciona.
7. Revisar el origen y los proxies para evitar un despliegue alternativo que siga ejecutando código sin la guarda.

Mantener `APP_DEBUG=false` en producción. El grupo API heredado conserva `throttle:60,1`; al entrar varias personas a través del mismo gateway, comprobar el límite agregado por IP. Este cambio no relaja ese middleware.

## Pruebas y validación

`tests/Feature/WorkspaceInventoryTest.php` arranca Laravel con SQLite en memoria y crea fixtures propias. No ejecuta migraciones ni usa registros de negocio. Cubre:

- HTTPS, secreto y actor obligatorios, sin provisioning ni promoción de roles.
- Cuentas inactivas/ausentes y permisos Spatie existentes.
- Alcance propio del solicitante, incluidos intentos de ampliar filtros.
- Campos explícitos, paginación, filtros, consultas parametrizadas y cantidades reales.
- Revocación entre peticiones, timeout, HTML, JSON inválido y redirects.
- Opciones reales del cliente HTTP: TLS, timeout, no redirecciones y credenciales privadas.
- Ausencia de cambios en tablas tras consultas y rechazo de rutas POST.
- Flag web, sesión ya abierta, PDF, Livewire y logout.
- Error de esquema sin SQL expuesto ni resultado vacío engañoso.

El workflow `.github/workflows/workspace-inventory.yml` instala el lockfile existente con PHP 7.4, ejecuta `php -l` y la suite Feature. Las variables SQLite están fijadas antes del bootstrap y se eliminan caches generadas heredadas. Las Actions están fijadas por commit.

No se modifica `composer.json` ni `composer.lock`, ni se ignoran requisitos de plataforma o bloqueos de seguridad del instalador. El runtime de CI reproduce la compatibilidad del proyecto heredado; no constituye una actualización de Laravel/PHP ni una auditoría global de dependencias. Cualquier bloqueo real de instalación debe informarse y resolverse sin bypass.

La preparación local no contó con PHP/Composer. Los resultados de ejecución deben obtenerse del workflow; la inspección estática y el parseo YAML no sustituyen las pruebas PHP. La suite de fixtures tampoco acredita el esquema de producción ni ejecuta operaciones de compra/despacho.

## Referencias de implementación

- Laravel 7 HTTP client: https://laravel.com/docs/7.x/http-client
- Laravel 7.30.4 PendingRequest: https://github.com/laravel/framework/blob/v7.30.4/src/Illuminate/Http/Client/PendingRequest.php
- Livewire 2.7.2 rutas y grupo web: https://github.com/livewire/livewire/blob/v2.7.2/src/LivewireServiceProvider.php
