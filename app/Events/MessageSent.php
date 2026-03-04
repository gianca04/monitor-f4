<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se emite cuando un usuario envía un mensaje en una conversación.
 *
 * Broadcast en tiempo real por un canal privado de la conversación
 * para que todos los participantes lo reciban al instante vía Reverb.
 */
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message,
    ) {}

    /**
     * Canal privado de la conversación.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->message->conversation_id}");
    }

    /**
     * Datos que se envían al cliente.
     */
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
