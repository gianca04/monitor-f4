# Documentación del Sistema de WebSockets y Notificaciones

## 1. Introducción

El sistema implementa capacidades de mensajería en tiempo real y notificaciones push mediante el uso de **Laravel Reverb** como servidor WebSocket, integrado de forma nativa con **Laravel Broadcasting** y **Laravel Echo**. Adicionalmente, cuenta con un sistema de notificaciones enriquecido que soporta persistencia en base de datos (panel de administración) y notificaciones Push (WebPush) directas a los dispositivos de los usuarios.

---

## 2. Sistema de WebSockets (Laravel Reverb)

Laravel Reverb proporciona una infraestructura de WebSockets nativa, rápida y escalable.

### 2.1. Arquitectura y Componentes

- **Servidor WebSocket**: Laravel Reverb (`config/reverb.php`).
- **Driver de Broadcast**: Reverb (`config/broadcasting.php`). Establecido vía `BROADCAST_CONNECTION=reverb`.
- **Cliente Frontend**: Laravel Echo y Pusher.js (`resources/js/echo.js`).
- **Autenticación de Canales**: Gestionada de forma segura en `routes/channels.php`.

### 2.2. Flujo y Funcionamiento del Broadcast

1. **Emisión de Eventos**: Cuando ocurre una acción en el sistema (ej. envío de un mensaje en el chat general o actualización de un registro), el sistema despacha un evento en backend que implementa la interfaz `ShouldBroadcastNow` (síncrono) o `ShouldBroadcast` (por medio de colas).
2. **Distribución**: Laravel encapsula el payload del evento y direcciona la carga útil de transmisión hacia el servidor central Reverb.
3. **Recepción del Cliente**: El cliente web (navegador) posee una instancia de Laravel Echo conectada de forma persistente. Cuando Reverb notifica la llegada de nueva data en los canales suscritos, Echo atrapa este mensaje en tiempo real, lo que permite accionar lógica JavaScript o componentes Alpine.js/Livewire de forma inmediatamente reactiva para el usuario.

### 2.3. Ejecución del Servidor Reverb

Para que todo el ecosistema WebSocket logre funcionar, el servidor Reverb debe encontrarse desplegado y escuchando de forma activa:

- **En Desarrollo Local**:
  Se inicia como proceso en terminal:
  ```bash
  php artisan reverb:start
  ```
- **En Producción**: 
  Reverb debe configurarse como un servicio persistente o demonio en segundo plano (usualmente mantenido por `Supervisor` o un servicio nativo del sistema operativo como `systemd`) a fin de garantizar alta disponibilidad y autoreinicio ante caídas inesperadas.

---

## 3. Sistema de Notificaciones y WebPush

El sistema unifica las alertas operativas integrando canales de persistencia en Base de Datos (Database) y canales WebPush. Esto significa que un evento importante notifica visualmente dentro de la misma plataforma y, si el usuario tiene permiso en su navegador, lanza una notificación push nativa a nivel operativo telefónico o de escritorio.

### 3.1. Arquitectura de Modulación de Notificaciones

- **Clase Notificable Maestra** (`GeneralPushNotification`): Las notificaciones extienden formalmente de `Illuminate\Notifications\Notification` y son enfilables (`ShouldQueue`).
- **Ruteo de Canales (método `via`)**: La clase verifica el estatus del destinatario. Añade por defecto el canal de base de datos `['database']` y, en caso de validar que el usuario cuente con una suscripción push activa y válida (`$notifiable->pushSubscriptions()->exists()`), agrega complementariamente `WebPushChannel::class`.
- **Integración Visual con el Panel**: El formato transaccional de la notificación devuelta en el método `toDatabase()` es generado según el esquema que dicta y espera consumir Filament v4 (título, cuerpo, iconos integrados y acciones de un click).
- **Procesamiento de Carga Útil Push**: Mediante el método `toWebPush()` se configuran propiedades específicas para que el navegador resuelva el modal nativo a mostrar, como el icono SVG general del proyecto y la ruta (URL) al accionar el click.

### 3.2. Servicio Administrador Automático (`PushNotificationService`)

A modo de facilitador para orquestar la comunicación, el sistema dispone del servicio localizado en `App\Services\PushNotificationService`. Provee métodos unificados para evitar la redundancia al codificar la construcción de alertas en controladores o eventos.

#### Métodos Principales del Servicio

- `sendToUser(User $user, ...)`: Envía la notificación puntual a un usuario objetivo de forma individual.
- `sendToMany(Collection|array $users, ...)`: Envía alertas simultáneas iterando sobre una colección finita de usuarios.
- `broadcast(...)`: Notifica a toda instancia de usuario en el sistema cuyo estatus permanezca catalogado como activo (`is_active = true`).
- `sendToRole(string $role, ...)`: Rutea la notificación únicamente a un abanico de usuarios que coincidan con la posesión de un rol jerárquico específico.

#### Atributos y Personalización

Por cada alerta expedida se pueden particularizar parámetros cosméticos o informacionales:
- `title`: Encabezado descriptivo de la notificación.
- `body`: Razonamiento extendido del cuerpo.
- `url` (opcional): Vinculo para redireccionar al visitar visualmente la notificación.
- `tag` (opcional): Clave identificadora usada por WebPush.
- `icon`: Metadato SVG del catálogo Heroicons.
- `iconColor`: Rango de color de advertencia asociado.
- `status`: Estatus condicional primario para paleta visual (`info`, `success`, `warning`, `danger`).

---

## 4. Requisitos de Configuración

Para garantizar el despliegue funcional integral de estos sistemas, resulta mandatorio definir en el archivo de entorno general `.env` las siguientes estructuras y variables:

### 4.1. Variables de Reverb

```dotenv
# Establecimiento de Driver
BROADCAST_CONNECTION=reverb

# Datos e identificadores de seguridad del canal websocket
REVERB_APP_ID=id_de_aplicacion
REVERB_APP_KEY=llave_publica
REVERB_APP_SECRET=secreto_de_autenticacion
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http # Actualizar siempre a 'https' en etapa productiva
```

Las variables referentes al frontend (`VITE_REVERB_HOST`, `VITE_REVERB_PORT`, etc.) se alimentarán indirectamente del listado anterior en el proceso de empaquetado y construcción web.
