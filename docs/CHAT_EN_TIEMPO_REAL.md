# Sistema de Chat en Tiempo Real - Monitor F4

## Descripción General

El sistema de chat implementa mensajería en tiempo real entre usuarios del panel Filament v4 utilizando **Laravel Reverb** como servidor WebSocket. Se presenta como una **burbuja flotante** persistente en todas las pantallas del panel, sin interferir con la navegación ni con otros componentes Livewire.

### Características

- Conversaciones 1-a-1 entre usuarios del sistema.
- Envío y recepción de mensajes en tiempo real vía WebSocket.
- Búsqueda de usuarios activos para iniciar nuevas conversaciones.
- Indicador de mensajes no leídos (badge en la burbuja).
- Polling de respaldo (30s) cuando la conexión WebSocket no está disponible.
- Protección de canales privados con autorización por participante.
- Seguro ante contextos sin autenticación (pantalla de login).

---

## Arquitectura

```
Usuario A (navegador)                          Usuario B (navegador)
    │                                               │
    │  wire:click                                    │
    ▼                                                │
ChatBubble (Livewire)                                │
    │                                                │
    ├── Message::create()                            │
    │                                                │
    ├── broadcast(MessageSent)->toOthers()           │
    │         │                                      │
    │         ▼                                      │
    │   Laravel Reverb (WebSocket Server)            │
    │         │                                      │
    │         └──── PrivateChannel('chat.{id}') ────►│
    │                                                │
    │                                    Alpine.js Echo listener
    │                                         │
    │                                         ▼
    │                                  $wire.chatBubbleOnMessageReceived()
    │                                         │
    │                                         ▼
    │                                  Livewire re-render
    │                                  (mensajes actualizados)
    ▼
Livewire re-render local
(mensaje propio visible)
```

### Stack Tecnológico

| Capa | Tecnología |
|---|---|
| WebSocket Server | Laravel Reverb v1.8 |
| Backend | Laravel 12 + Livewire 3 |
| Frontend | Alpine.js + Laravel Echo + Pusher.js |
| Panel | Filament v4 |
| Protocolo | Pusher Protocol (compatible con Reverb) |

---

## Componentes del Sistema

| Archivo | Responsabilidad |
|---|---|
| `app/Livewire/ChatBubble.php` | Componente Livewire principal. Gestiona estado, acciones, computed properties y recepción de mensajes. |
| `resources/views/livewire/chat-bubble.blade.php` | Vista Blade con Alpine.js. UI de la burbuja flotante, lista de conversaciones y vista de chat. |
| `app/Models/Conversation.php` | Modelo Eloquent. Relaciones con usuarios y mensajes. Helper `between()` para conversaciones 1-a-1. |
| `app/Models/Message.php` | Modelo Eloquent. Representa un mensaje individual con relación a conversación y usuario. |
| `app/Events/MessageSent.php` | Evento broadcast (`ShouldBroadcastNow`). Se emite por canal privado cuando se envía un mensaje. |
| `routes/channels.php` | Autorización de canales. Valida que el usuario pertenezca a la conversación. |
| `config/broadcasting.php` | Configuración del driver de broadcast (Reverb). |
| `config/reverb.php` | Configuración del servidor Reverb (host, puerto, aplicación). |
| `config/filament.php` | Configuración de Echo para Filament (conecta el frontend con Reverb). |
| `resources/js/echo.js` | Inicialización de Laravel Echo con Pusher.js apuntando a Reverb. |
| `database/migrations/2026_03_04_000001_create_chat_tables.php` | Migración de tablas `conversations`, `conversation_user`, `messages`. |

---

## Modelo de Datos

### Diagrama Entidad-Relación

```
┌──────────────┐       ┌─────────────────────┐       ┌──────────────────┐
│    users     │       │  conversation_user   │       │  conversations   │
├──────────────┤       ├─────────────────────┤       ├──────────────────┤
│ id (PK)      │◄──────│ user_id (FK)         │──────►│ id (PK)          │
│ name         │       │ conversation_id (FK) │       │ name (nullable)  │
│ email        │       │ last_read_at         │       │ is_group         │
│ is_active    │       │ created_at           │       │ created_at       │
│ ...          │       │ updated_at           │       │ updated_at       │
└──────────────┘       └─────────────────────┘       └──────────────────┘
                                                              │
                                                              │ 1:N
                                                              ▼
                                                     ┌──────────────────┐
                                                     │    messages      │
                                                     ├──────────────────┤
                                                     │ id (PK)          │
                                                     │ conversation_id  │
                                                     │ user_id (FK)     │
                                                     │ body (text)      │
                                                     │ created_at       │
                                                     │ updated_at       │
                                                     └──────────────────┘
```

### Tablas

#### `conversations`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint unsigned` PK | Identificador único. |
| `name` | `varchar(255)` nullable | Nombre de la conversación (para grupos futuros). |
| `is_group` | `boolean` default `false` | Indica si es conversación grupal. |
| `created_at` | `timestamp` | Fecha de creación. |
| `updated_at` | `timestamp` | Fecha de última actualización. |

#### `conversation_user` (pivot)

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint unsigned` PK | Identificador único. |
| `conversation_id` | `bigint unsigned` FK | Referencia a `conversations.id` (cascade delete). |
| `user_id` | `bigint unsigned` FK | Referencia a `users.id` (cascade delete). |
| `last_read_at` | `timestamp` nullable | Marca temporal del último mensaje leído por este usuario. |
| `created_at` | `timestamp` | Fecha de creación. |
| `updated_at` | `timestamp` | Fecha de última actualización. |

**Restricción**: `UNIQUE(conversation_id, user_id)`.

#### `messages`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint unsigned` PK | Identificador único. |
| `conversation_id` | `bigint unsigned` FK | Referencia a `conversations.id` (cascade delete). |
| `user_id` | `bigint unsigned` FK | Referencia a `users.id` (cascade delete). |
| `body` | `text` | Contenido del mensaje. |
| `created_at` | `timestamp` | Fecha de envío. |
| `updated_at` | `timestamp` | Fecha de última actualización. |

**Índice compuesto**: `(conversation_id, created_at)` para consultas ordenadas.

---

## Configuración

### Variables de Entorno

Las siguientes variables deben estar definidas en `.env`:

```dotenv
# Driver de broadcast
BROADCAST_CONNECTION=reverb

# Credenciales de la aplicación Reverb
REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Variables expuestas al frontend (Vite)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

> **Nota**: En producción, `REVERB_SCHEME` debe ser `https` y `REVERB_PORT` debe ser `443` (o el puerto configurado con TLS).

### Dependencias

#### PHP (Composer)

```bash
composer require laravel/reverb
```

Esto instala automáticamente:

- `laravel/reverb` — servidor WebSocket
- `pusher/pusher-php-server` — driver de broadcast compatible

#### JavaScript (NPM)

```bash
npm install laravel-echo pusher-js
```

---

## Flujo de Funcionamiento

### 1. Inyección del Componente

El componente `ChatBubble` se inyecta globalmente en el panel Filament mediante un **render hook** en `AppServiceProvider`:

```php
FilamentView::registerRenderHook(
    'panels::body.end',
    fn(): string => Blade::render('@livewire(\'chat-bubble\')'),
);
```

Esto asegura que la burbuja esté presente en **todas las pantallas** del panel, incluyendo la de login (donde el componente detecta la ausencia de autenticación y no ejecuta queries).

### 2. Montaje Seguro

Al montarse, el componente verifica si existe un usuario autenticado antes de ejecutar cualquier consulta:

```php
public function mount(): void
{
    $this->computeUnreadCount();
}

private function currentUser(): ?User
{
    return auth()->user();
}

public function computeUnreadCount(): void
{
    $user = $this->currentUser();

    if (! $user) {
        $this->unreadCount = 0;
        return;
    }
    // ...
}
```

Todos los métodos públicos incluyen esta protección.

### 3. Lista de Conversaciones

Al abrir la burbuja, se renderiza la vista `list` con las conversaciones del usuario ordenadas por último mensaje:

```php
#[Computed]
public function conversations(): Collection
{
    $user = $this->currentUser();

    if (! $user) {
        return collect();
    }

    return $user
        ->conversations()
        ->with(['users', 'messages' => fn ($q) => $q->latest()->limit(1)])
        ->get()
        ->sortByDesc(fn (Conversation $c) => $c->messages->first()?->created_at);
}
```

### 4. Iniciar Conversación

Cuando se busca y selecciona un usuario, se utiliza el helper `Conversation::between()` que busca o crea una conversación 1-a-1:

```php
public static function between(User $userA, User $userB): self
{
    $conversation = static::query()
        ->where('is_group', false)
        ->whereHas('users', fn ($q) => $q->where('users.id', $userA->id))
        ->whereHas('users', fn ($q) => $q->where('users.id', $userB->id))
        ->first();

    if (! $conversation) {
        $conversation = static::create(['is_group' => false]);
        $conversation->users()->attach([$userA->id, $userB->id]);
    }

    return $conversation;
}
```

### 5. Envío de Mensaje

```php
public function chatBubbleSendMessage(): void
{
    $message = Message::create([
        'conversation_id' => $this->conversationId,
        'user_id'         => $user->id,
        'body'            => trim($this->messageBody),
    ]);

    // Broadcast a los demás participantes (excluye al emisor)
    broadcast(new MessageSent($message));

    // Refresca la vista local
    unset($this->messages);
    $this->dispatch('chat-bubble:message-sent');
}
```

### 6. Broadcast del Evento

El evento `MessageSent` implementa `ShouldBroadcastNow` para entrega inmediata sin necesidad de queue worker:

```php
class MessageSent implements ShouldBroadcastNow
{
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->message->conversation_id}");
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'user_id'         => $this->message->user_id,
            'user_name'       => $this->message->user->name,
            'body'            => $this->message->body,
            'created_at'      => $this->message->created_at->toISOString(),
        ];
    }
}
```

### 7. Autorización de Canal

Solo los participantes de una conversación pueden escuchar su canal privado:

```php
Broadcast::channel('chat.{conversationId}', function ($user, int $conversationId) {
    return Conversation::query()
        ->whereKey($conversationId)
        ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
        ->exists();
});
```

### 8. Recepción en Tiempo Real (Alpine.js + Echo)

La suscripción al canal Echo se gestiona desde Alpine.js, observando cambios en `conversationId`:

```javascript
Alpine.data('chatBubble', () => ({
    echoChannel: null,

    init() {
        Livewire.on('chat-bubble:message-sent', () => {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        });

        this.$watch('$wire.conversationId', (newId, oldId) => {
            this.leaveChannel(oldId);
            this.joinChannel(newId);
        });
    },

    joinChannel(conversationId) {
        if (!conversationId || !window.Echo) return;
        this.echoChannel = window.Echo.private(`chat.${conversationId}`)
            .listen('MessageSent', () => {
                this.$wire.chatBubbleOnMessageReceived();
            });
    },

    leaveChannel(conversationId) {
        if (!conversationId || !window.Echo) return;
        window.Echo.leave(`chat.${conversationId}`);
        this.echoChannel = null;
    },

    destroy() {
        this.leaveChannel(this.$wire.conversationId);
    },
}));
```

> **Nota**: No se usa el atributo `#[On('echo-private:...')]` de Livewire porque requiere que el placeholder del canal tenga un valor al momento del montaje. Al ser `null` inicialmente, la suscripción se delega a Alpine.js.

---

## Convenciones de Nombres

Todos los métodos públicos y eventos del componente usan el prefijo `chatBubble` para evitar colisiones con otros componentes Livewire del panel:

| Método / Evento | Propósito |
|---|---|
| `chatBubbleToggle()` | Abrir/cerrar la burbuja. |
| `chatBubbleOpenConversation(int $id)` | Abrir una conversación existente. |
| `chatBubbleStartConversation(int $userId)` | Iniciar o retomar conversación con un usuario. |
| `chatBubbleSendMessage()` | Enviar un mensaje en la conversación activa. |
| `chatBubbleBackToList()` | Volver a la lista de conversaciones. |
| `chatBubbleOnMessageReceived()` | Callback invocado al recibir un mensaje por WebSocket. |
| `chat-bubble:message-sent` | Evento Livewire para auto-scroll. |

---

## Ejecución

### Desarrollo Local

Se necesitan **tres procesos** ejecutándose simultáneamente:

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Servidor WebSocket Reverb
php artisan reverb:start

# Terminal 3: Compilación de assets (Vite)
npm run dev
```

### Producción

```bash
# Reverb como servicio (supervisor, systemd, etc.)
php artisan reverb:start --host=0.0.0.0 --port=8080

# O con el flag de producción
php artisan reverb:start --hostname=tu-dominio.com --port=443
```

Asegurarse de que:

- `BROADCAST_CONNECTION=reverb` en `.env`.
- `REVERB_SCHEME=https` y puertos correctos.
- El servidor Reverb esté detrás de un proxy con TLS (Nginx, Cloudflare, etc.).

---

## Seguridad

| Aspecto | Implementación |
|---|---|
| Autenticación | Todas las acciones verifican `currentUser()` antes de ejecutar. |
| Autorización de canal | `routes/channels.php` valida pertenencia a la conversación. |
| Canales privados | Se usa `PrivateChannel` (requiere token de autenticación para suscribirse). |
| XSS | Los mensajes se renderizan con `{{ }}` (escape automático de Blade). |
| Contexto sin auth | El componente retorna colecciones vacías / `$unreadCount = 0` en login. |
| Exclusión del emisor | `broadcast()->toOthers()` evita que el emisor reciba su propio mensaje duplicado. |

---

## Requisitos por Entorno

### Desarrollo Local

- HTTPS no es necesario (Reverb funciona con `ws://` en localhost).
- `REVERB_SCHEME=http`, `REVERB_PORT=8080` (por defecto).
- `BROADCAST_CONNECTION=reverb`.
- No se requiere queue worker (`ShouldBroadcastNow` ejecuta de forma síncrona).

### Producción

- HTTPS obligatorio para WebSockets seguros (`wss://`).
- `REVERB_SCHEME=https`.
- Reverb detrás de un reverse proxy con TLS.
- Supervisor o systemd para mantener `php artisan reverb:start` activo.

---

## Extensibilidad Futura

El sistema está diseñado para escalar. La tabla `conversations` incluye:

- **`is_group`**: preparado para conversaciones grupales (actualmente solo 1-a-1).
- **`name`**: para nombrar grupos en el futuro.
- **`last_read_at`** en pivot: base para indicadores de lectura por participante.

Posibles ampliaciones:

- Conversaciones grupales.
- Indicadores de "escribiendo..." (typing indicators vía Whisper).
- Adjuntos y archivos.
- Búsqueda dentro de mensajes.
- Notificaciones push al navegador cuando llega un mensaje y el chat está cerrado.
- Paginación de mensajes (actualmente limitado a los últimos 100).
