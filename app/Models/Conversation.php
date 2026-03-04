<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'name',
        'is_group',
    ];

    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Obtener o crear una conversación 1-a-1 entre dos usuarios.
     */
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

    /**
     * Obtener el último mensaje de la conversación.
     */
    public function latestMessage(): HasMany
    {
        return $this->messages()->latest()->limit(1);
    }
}
