{{--
    Floating Chat Bubble Component.

    Componente Livewire inyectado vía render hook (panels::body.end).
    Burbuja flotante en la esquina inferior derecha con:
    - Lista de conversaciones
    - Vista de chat con mensajes en tiempo real (Laravel Reverb)
    - Búsqueda de usuarios para iniciar nuevas conversaciones
--}}

<div
    x-data="chatBubble"
    class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3"
>
    {{-- ═══════════════════════════════════════════
         CHAT PANEL
    ═══════════════════════════════════════════ --}}
    <div
        x-show="$wire.isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        x-cloak
        class="mb-2 flex h-[32rem] w-80 flex-col overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    >
        {{-- ─── VISTA: LISTA DE CONVERSACIONES ─── --}}
        @if ($view === 'list')
            {{-- Header --}}
            <header class="flex items-center justify-between bg-primary-600 px-4 py-3 text-white">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="h-5 w-5" />
                    <span class="text-sm font-semibold">Chat</span>
                </div>
                <button
                    type="button"
                    wire:click="chatBubbleToggle"
                    class="rounded-lg p-1 transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50"
                    aria-label="Cerrar chat"
                >
                    <x-filament::icon icon="heroicon-m-x-mark" class="h-4 w-4" />
                </button>
            </header>

            {{-- Buscador de usuarios --}}
            <div class="border-b border-gray-200 p-3 dark:border-white/10">
                <div class="relative">
                    <x-filament::icon
                        icon="heroicon-m-magnifying-glass"
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                    />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchUsers"
                        placeholder="Buscar usuario…"
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            </div>

            {{-- Resultados de búsqueda --}}
            @if (filled($searchUsers))
                <div class="flex-1 overflow-y-auto">
                    @forelse ($this->availableUsers as $user)
                        <button
                            type="button"
                            wire:click="chatBubbleStartConversation({{ $user->id }})"
                            class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-white/5"
                        >
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-semibold text-primary-700 dark:bg-primary-500/20 dark:text-primary-400">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                            <x-filament::icon icon="heroicon-m-chat-bubble-left" class="h-4 w-4 text-gray-400" />
                        </button>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            No se encontraron usuarios.
                        </div>
                    @endforelse
                </div>
            @else
                {{-- Lista de conversaciones --}}
                <div class="flex-1 overflow-y-auto">
                    @forelse ($this->conversations as $conversation)
                        @php
                            $other = $conversation->users->firstWhere('id', '!=', auth()->id());
                            $lastMessage = $conversation->messages->first();
                            $lastRead = $conversation->pivot->last_read_at;
                            $hasUnread = $lastMessage
                                && $lastMessage->user_id !== auth()->id()
                                && (! $lastRead || $lastMessage->created_at->gt($lastRead));
                        @endphp
                        <button
                            type="button"
                            wire:click="chatBubbleOpenConversation({{ $conversation->id }})"
                            class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-white/5"
                        >
                            <span class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-semibold text-primary-700 dark:bg-primary-500/20 dark:text-primary-400">
                                {{ strtoupper(mb_substr($other?->name ?? '?', 0, 1)) }}
                                @if ($hasUnread)
                                    <span class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900"></span>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $other?->name ?? 'Conversación' }}
                                </p>
                                @if ($lastMessage)
                                    <p class="truncate text-xs {{ $hasUnread ? 'font-semibold text-gray-700 dark:text-gray-200' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $lastMessage->body }}
                                    </p>
                                @endif
                            </div>
                            @if ($lastMessage)
                                <span class="shrink-0 text-[10px] text-gray-400 dark:text-gray-500">
                                    {{ $lastMessage->created_at->shortRelativeDiffForHumans() }}
                                </span>
                            @endif
                        </button>
                    @empty
                        <div class="flex flex-1 flex-col items-center justify-center p-4 text-center">
                            <x-filament::icon icon="heroicon-o-chat-bubble-bottom-center-text" class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Aún no hay conversaciones.</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Busca un usuario para iniciar un chat.</p>
                        </div>
                    @endforelse
                </div>
            @endif
        @endif

        {{-- ─── VISTA: CHAT (MENSAJES) ─── --}}
        @if ($view === 'chat')
            {{-- Header --}}
            <header class="flex items-center gap-2 bg-primary-600 px-4 py-3 text-white">
                <button
                    type="button"
                    wire:click="chatBubbleBackToList"
                    class="rounded-lg p-1 transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50"
                    aria-label="Volver"
                >
                    <x-filament::icon icon="heroicon-m-arrow-left" class="h-4 w-4" />
                </button>
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/20 text-xs font-semibold">
                    {{ strtoupper(mb_substr($this->otherUser?->name ?? '?', 0, 1)) }}
                </span>
                <span class="min-w-0 flex-1 truncate text-sm font-semibold">
                    {{ $this->otherUser?->name ?? 'Chat' }}
                </span>
                <button
                    type="button"
                    wire:click="chatBubbleToggle"
                    class="rounded-lg p-1 transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50"
                    aria-label="Cerrar chat"
                >
                    <x-filament::icon icon="heroicon-m-x-mark" class="h-4 w-4" />
                </button>
            </header>

            {{-- Mensajes --}}
            <div
                x-ref="messagesContainer"
                class="flex flex-1 flex-col gap-2 overflow-y-auto p-3"
                wire:poll.30s
            >
                @forelse ($this->messages as $message)
                    @php $isMine = $message->user_id === auth()->id(); @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-2xl px-3 py-2 text-sm {{ $isMine
                            ? 'rounded-br-md bg-primary-600 text-white'
                            : 'rounded-bl-md bg-gray-100 text-gray-900 dark:bg-white/10 dark:text-white' }}">
                            @unless ($isMine)
                                <p class="mb-0.5 text-[10px] font-semibold {{ $isMine ? 'text-primary-200' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $message->user?->name }}
                                </p>
                            @endunless
                            <p class="whitespace-pre-wrap break-words">{{ $message->body }}</p>
                            <p class="mt-0.5 text-right text-[10px] {{ $isMine ? 'text-primary-200' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-1 items-center justify-center text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500">Envía el primer mensaje 👋</p>
                    </div>
                @endforelse
            </div>

            {{-- Input de mensaje --}}
            <footer class="border-t border-gray-200 p-3 dark:border-white/10">
                <form wire:submit="chatBubbleSendMessage" class="flex items-center gap-2">
                    <input
                        type="text"
                        wire:model="messageBody"
                        placeholder="Escribe un mensaje…"
                        autocomplete="off"
                        class="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        x-on:keydown.enter.prevent="$wire.chatBubbleSendMessage()"
                    />
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-primary-600 p-2 text-white transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!$wire.messageBody?.trim()"
                        aria-label="Enviar mensaje"
                    >
                        <x-filament::icon icon="heroicon-m-paper-airplane" class="h-4 w-4" />
                    </button>
                </form>
            </footer>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════
         FLOATING ACTION BUTTON
    ═══════════════════════════════════════════ --}}
    <button
        type="button"
        wire:click="chatBubbleToggle"
        class="group relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg transition-all duration-200 hover:bg-primary-500 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary-500/40 active:scale-95"
        :aria-expanded="$wire.isOpen"
        aria-label="Abrir chat"
    >
        {{-- Badge de no leídos --}}
        @if ($unreadCount > 0)
            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif

        {{-- Chat icon (visible when closed) --}}
        <x-filament::icon
            icon="heroicon-o-chat-bubble-left-right"
            x-show="! $wire.isOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="rotate-90 scale-0 opacity-0"
            x-transition:enter-end="rotate-0 scale-100 opacity-100"
            class="h-6 w-6"
        />

        {{-- Close icon (visible when open) --}}
        <x-filament::icon
            icon="heroicon-m-x-mark"
            x-show="$wire.isOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="-rotate-90 scale-0 opacity-0"
            x-transition:enter-end="rotate-0 scale-100 opacity-100"
            class="h-6 w-6"
        />
    </button>
</div>

@script
<script>
Alpine.data('chatBubble', () => ({
    echoChannel: null,

    init() {
        // Auto-scroll al final cuando se envía o recibe un mensaje.
        Livewire.on('chat-bubble:message-sent', () => {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        });

        // Observar cambios en conversationId para suscribirse/desuscribirse del canal Echo.
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
</script>
@endscript
