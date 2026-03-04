<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Canal privado de chat.
 * Un usuario solo puede escuchar si pertenece a la conversación.
 */
Broadcast::channel('chat.{conversationId}', function ($user, int $conversationId) {
    return Conversation::query()
        ->whereKey($conversationId)
        ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
        ->exists();
});
