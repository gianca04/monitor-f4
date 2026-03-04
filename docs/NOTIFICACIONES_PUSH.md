# Sistema de Notificaciones Push - Monitor F4

## Descripcion General

El sistema de notificaciones implementa un mecanismo dual de entrega:

- **Notificaciones en base de datos**: se muestran en el icono de campana del panel Filament v4.
- **Notificaciones push al navegador**: se entregan como notificaciones nativas del sistema operativo mediante la Web Push API.

Ambos canales se activan de forma simultanea al enviar una notificacion. Si el usuario no tiene una suscripcion push activa, solo se registra en base de datos.

---

## Arquitectura

```
Origen (codigo PHP / Artisan CLI)
    |
    v
PushNotificationService
    |
    v
GeneralPushNotification (implements ShouldQueue)
    |
    +--> Canal 'database'    --> tabla 'notifications'  --> Campana Filament v4
    |
    +--> Canal 'WebPush'     --> Web Push API (VAPID)   --> Service Worker (sw.js)
                                                             |
                                                             v
                                                        Notificacion nativa del navegador
```

### Componentes

| Archivo | Responsabilidad |
|---|---|
| `app/Services/PushNotificationService.php` | Servicio principal. Expone metodos de envio de alto nivel. |
| `app/Notifications/GeneralPushNotification.php` | Clase Notification. Define el formato para base de datos (Filament) y Web Push. |
| `app/Http/Controllers/PushSubscriptionController.php` | Controlador REST. Registra y elimina suscripciones push del navegador. |
| `app/Console/Commands/SendPushNotification.php` | Comando Artisan `push:send`. Permite enviar notificaciones desde terminal. |
| `public/sw.js` | Service Worker. Recibe eventos push y muestra notificaciones nativas. |
| `resources/views/components/push-notification-subscriber.blade.php` | Componente Blade + Alpine.js. Se inyecta automaticamente en el panel para registrar el Service Worker y suscribir al usuario. |

---

## Configuracion

### Variables de entorno

Las siguientes variables deben estar definidas en el archivo `.env`:

```dotenv
VAPID_SUBJECT=mailto:admin@ejemplo.com
VAPID_PUBLIC_KEY=<clave_publica_base64url>
VAPID_PRIVATE_KEY=<clave_privada_base64url>
```

Las claves VAPID se generan una unica vez. En entornos donde el comando `php artisan webpush:vapid` no funcione (ej. Windows con Laragon/XAMPP), se pueden generar mediante un script PHP que use OpenSSL directamente.

**Importante**: las claves VAPID no deben cambiar una vez desplegadas. Si se regeneran, todas las suscripciones existentes quedaran invalidas.

### Conexion de cola

Para desarrollo local se recomienda:

```dotenv
QUEUE_CONNECTION=sync
```

Para produccion:

```dotenv
QUEUE_CONNECTION=database
```

Con `database`, es necesario mantener un worker activo:

```bash
php artisan queue:work --sleep=3 --tries=3
```

---

## Uso desde Codigo PHP

### Inyeccion del servicio

El servicio `PushNotificationService` esta registrado como singleton en el contenedor de Laravel. Se puede inyectar en cualquier controlador, job, observer o comando.

```php
use App\Services\PushNotificationService;

class MiControlador extends Controller
{
    public function __construct(
        private readonly PushNotificationService $pushService,
    ) {}
}
```

### Enviar a un usuario especifico

```php
$this->pushService->sendToUser(
    user: $usuario,
    title: 'Nuevo reporte asignado',
    body: 'Se te ha asignado el reporte RT-0042.',
    url: '/dashboard/work-reports/42',
    icon: 'heroicon-o-document-text',
    iconColor: 'success',
    status: 'success',
);
```

### Enviar a multiples usuarios

```php
$usuarios = User::whereIn('id', [1, 5, 13])->get();

$this->pushService->sendToMany(
    users: $usuarios,
    title: 'Reunion programada',
    body: 'Reunion de coordinacion manana a las 09:00.',
    url: '/dashboard',
);
```

### Enviar a todos los usuarios activos (broadcast)

```php
$this->pushService->broadcast(
    title: 'Mantenimiento programado',
    body: 'El sistema estara en mantenimiento el domingo de 22:00 a 23:00.',
);
```

### Enviar a usuarios con un rol especifico

```php
$this->pushService->sendToRole(
    role: 'admin',
    title: 'Alerta de sistema',
    body: 'Se ha detectado un error en el modulo de cotizaciones.',
    url: '/dashboard/quotes',
    icon: 'heroicon-o-exclamation-triangle',
    iconColor: 'danger',
    status: 'danger',
);
```

### Parametros disponibles

| Parametro | Tipo | Requerido | Valor por defecto | Descripcion |
|---|---|---|---|---|
| `title` | `string` | Si | - | Titulo que se muestra en la campana y en la push nativa. |
| `body` | `string` | Si | - | Texto descriptivo de la notificacion. |
| `url` | `?string` | No | `null` | URL de destino al hacer clic. Si se proporciona, se genera un boton "Ver" en Filament. |
| `tag` | `?string` | No | `'general'` | Etiqueta para agrupar notificaciones push (reemplaza anteriores con el mismo tag). |
| `icon` | `?string` | No | `'heroicon-o-bell'` | Icono de Heroicons para la campana de Filament. |
| `iconColor` | `?string` | No | `'primary'` | Color del icono en Filament (`primary`, `success`, `danger`, `warning`, `info`, `gray`). |
| `status` | `?string` | No | `'info'` | Estado visual de la notificacion en Filament (`success`, `danger`, `warning`, `info`). |

---

## Uso desde Linea de Comandos

El comando `push:send` permite enviar notificaciones sin escribir codigo.

### Sintaxis

```bash
php artisan push:send [opciones]
```

### Opciones

| Opcion | Descripcion |
|---|---|
| `--user=ID` | Enviar a un usuario especifico por su ID. |
| `--role=NOMBRE` | Enviar a todos los usuarios activos con el rol indicado. |
| `--title=TEXTO` | Titulo de la notificacion. Si se omite, se solicita interactivamente. |
| `--body=TEXTO` | Cuerpo de la notificacion. Si se omite, se solicita interactivamente. |
| `--url=URL` | URL de destino al hacer clic. Por defecto: `/dashboard`. |

### Ejemplos

Enviar a un usuario especifico:

```bash
php artisan push:send --user=13 --title="Reporte listo" --body="Tu reporte ha sido aprobado"
```

Enviar a todos los administradores:

```bash
php artisan push:send --role=admin --title="Alerta" --body="Nueva solicitud pendiente"
```

Enviar a todos los usuarios activos:

```bash
php artisan push:send --title="Aviso general" --body="Actualizacion disponible"
```

---

## Suscripcion del Navegador

### Proceso automatico

Cuando un usuario autenticado accede al panel de Filament, el componente `push-notification-subscriber` se ejecuta automaticamente:

1. Verifica que el navegador soporte Service Workers, Push API y Notifications API.
2. Registra el Service Worker (`/sw.js`).
3. Si el permiso de notificaciones es `default` (no decidido), solicita permiso despues de 3 segundos.
4. Si el usuario acepta, se crea una suscripcion push y se envia al endpoint `POST /push/subscribe`.

No se requiere ninguna accion manual por parte del usuario mas alla de aceptar el permiso del navegador.

### Endpoints REST

| Metodo | Ruta | Descripcion |
|---|---|---|
| `POST` | `/push/subscribe` | Registra o actualiza la suscripcion push del usuario autenticado. |
| `DELETE` | `/push/unsubscribe` | Elimina la suscripcion push del usuario autenticado. |

Ambas rutas estan protegidas con el middleware `auth`.

---

## Requisitos por Entorno

### Desarrollo local

- HTTPS no es necesario. Los navegadores tratan `localhost` y `127.0.0.1` como contexto seguro.
- Se recomienda `QUEUE_CONNECTION=sync` para recibir las notificaciones de forma inmediata.
- En Windows (Laragon/XAMPP), el sistema configura automaticamente la ruta de `openssl.cnf` a traves de `AppServiceProvider::configureOpenSsl()`.

### Produccion

- HTTPS es obligatorio para que la Push API funcione.
- Usar `QUEUE_CONNECTION=database` con un worker activo (`php artisan queue:work`).
- Las claves VAPID deben configurarse como variables de entorno del servidor.

---

## Notas Tecnicas

- La notificacion implementa `ShouldQueue`. Con `sync`, se ejecuta inmediatamente. Con `database`, se procesa en segundo plano.
- El canal WebPush solo se activa si el usuario tiene al menos una suscripcion push registrada en la tabla `push_subscriptions`.
- Filament v4 requiere que el campo `format` en el JSON de la notificacion tenga el valor `filament`. Sin esta clave, las notificaciones no aparecen en el dropdown de la campana.
- El Service Worker maneja el evento `notificationclick` para redirigir al usuario a la URL asociada.
- Si una suscripcion push expira o falla, la libreria `laravel-notification-channels/webpush` la elimina automaticamente.
