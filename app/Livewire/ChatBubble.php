<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Floating chat bubble component.
 *
 * Se inyecta en el layout de Filament vía render hook y persiste
 * en todas las pantallas del panel. Permite:
 *  - Ver lista de conversaciones del usuario autenticado.
 *  - Abrir una conversación existente o iniciar una nueva.
 *  - Enviar y recibir mensajes en tiempo real vía Laravel Reverb.
 */
class ChatBubble extends Component
{
    // ──────────────────────────────────────────────
    // State
    // ──────────────────────────────────────────────

    /** Panel abierto / cerrado. */
    public bool $isOpen = false;

    /** Vista activa: 'list' (conversaciones) | 'chat' (mensajes). */
    public string $view = 'list';

    /** ID de la conversación activa. */
    public ?int $conversationId = null;

    /** Texto del input de mensaje. */
    public string $messageBody = '';

    /** Texto de búsqueda de usuarios (nueva conversación). */
    public string $searchUsers = '';

    /** Número de mensajes no leídos total. */
    public int $unreadCount = 0;

    // ──────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────

    public function mount(): void
    {
        $this->computeUnreadCount();
    }

    // ──────────────────────────────────────────────
    // Auth guard helper
    // ──────────────────────────────────────────────

    /** Devuelve el usuario autenticado o null si no hay sesión. */
    private function currentUser(): ?User
    {
        return auth()->user();
    }

    // ──────────────────────────────────────────────
    // Computed properties
    // ──────────────────────────────────────────────

    /** Conversaciones del usuario autenticado, ordenadas por último mensaje. */
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

    /** Mensajes de la conversación activa. */
    #[Computed]
    public function messages(): Collection
    {
        if (! $this->conversationId || ! $this->currentUser()) {
            return collect();
        }

        return Message::where('conversation_id', $this->conversationId)
            ->with('user:id,name')
            ->oldest()
            ->limit(100)
            ->get();
    }

    /** Usuarios disponibles para iniciar nueva conversación. */
    #[Computed]
    public function availableUsers(): Collection
    {
        $user = $this->currentUser();

        if (blank($this->searchUsers) || ! $user) {
            return collect();
        }

        return User::query()
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->where('name', 'like', "%{$this->searchUsers}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);
    }

    /** Información del otro participante (conversación 1-a-1). */
    #[Computed]
    public function otherUser(): ?User
    {
        $user = $this->currentUser();

        if (! $this->conversationId || ! $user) {
            return null;
        }

        $conversation = Conversation::with('users')->find($this->conversationId);

        return $conversation?->users->firstWhere('id', '!=', $user->id);
    }

    // ──────────────────────────────────────────────
    // Actions
    // ──────────────────────────────────────────────

    public function chatBubbleToggle(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->view = 'list';
            $this->conversationId = null;
            $this->computeUnreadCount();
        }
    }

    /** Abrir una conversación existente. */
    public function chatBubbleOpenConversation(int $conversationId): void
    {
        if (! $this->currentUser()) {
            return;
        }

        $this->conversationId = $conversationId;
        $this->view = 'chat';
        $this->messageBody = '';

        $this->markAsRead();

        // Limpiar cache de computed properties para refrescar datos.
        unset($this->messages, $this->otherUser);
    }

    /** Iniciar (o retomar) conversación 1-a-1 con un usuario. */
    public function chatBubbleStartConversation(int $userId): void
    {
        $user = $this->currentUser();

        if (! $user) {
            return;
        }

        $otherUser = User::findOrFail($userId);
        $conversation = Conversation::between($user, $otherUser);

        $this->chatBubbleOpenConversation($conversation->id);
        $this->searchUsers = '';
    }

    /** Enviar un mensaje en la conversación activa. */
    public function chatBubbleSendMessage(): void
    {
        $user = $this->currentUser();

        if (blank($this->messageBody) || ! $this->conversationId || ! $user) {
            return;
        }

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'user_id'         => $user->id,
            'body'            => trim($this->messageBody),
        ]);

        $this->messageBody = '';

        // Broadcast en tiempo real a los demás participantes.
        broadcast(new MessageSent($message))->toOthers();

        // Refrescar mensajes localmente.
        unset($this->messages);

        // Emitir evento JS para hacer scroll al final.
        $this->dispatch('chat-bubble:message-sent');
    }

    /** Volver a la lista de conversaciones. */
    public function chatBubbleBackToList(): void
    {
        $this->view = 'list';
        $this->conversationId = null;
        $this->searchUsers = '';

        unset($this->conversations);
        $this->computeUnreadCount();
    }

    // ──────────────────────────────────────────────
    // Echo listeners (invocado desde Alpine.js)
    // ──────────────────────────────────────────────

    /**
     * Se invoca desde Alpine/Echo cuando llega un mensaje nuevo
     * por WebSocket al canal de la conversación activa.
     */
    public function chatBubbleOnMessageReceived(): void
    {
        if (! $this->currentUser()) {
            return;
        }

        // Refrescar mensajes renderizados.
        unset($this->messages);

        $this->markAsRead();
        $this->dispatch('chat-bubble:message-sent');
    }

    /**
     * Recuento global de no leídos (llamado por polling o al abrir).
     */
    public function computeUnreadCount(): void
    {
        $user = $this->currentUser();

        if (! $user) {
            $this->unreadCount = 0;

            return;
        }

        $this->unreadCount = $user->conversations()
            ->get()
            ->sum(function (Conversation $conversation) use ($user) {
                $lastRead = $conversation->pivot->last_read_at;

                return $conversation->messages()
                    ->where('user_id', '!=', $user->id)
                    ->when($lastRead, fn ($q) => $q->where('created_at', '>', $lastRead))
                    ->count();
            });
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function markAsRead(): void
    {
        $user = $this->currentUser();

        if (! $this->conversationId || ! $user) {
            return;
        }

        $user->conversations()
            ->updateExistingPivot($this->conversationId, [
                'last_read_at' => now(),
            ]);

        $this->computeUnreadCount();
    }

    // ──────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────

    public function render()
    {
        return view('livewire.chat-bubble');
    }
}
