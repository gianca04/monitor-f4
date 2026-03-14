<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modelo User - Usuarios del Sistema
 *
 * Representa un usuario del sistema con permisos, roles y relaciones de seguimiento.
 * Los usuarios (empleados) registran y ejecutan transacciones de entrega de materiales.
 *
 * Relación con DispatchTransaction:
 * - employee_id en DispatchTransaction señala el usuario que ejecutó la entrega
 * - Permite auditar quién, cuándo y cómo se despachó cada material
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchTransactions Las transacciones de entrega ejecutadas por este usuario
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasPushSubscriptions, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',

        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    /**
     * Obtiene todas las transacciones de entrega ejecutadas por este usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchTransactions()
    {
        return $this->hasMany(DispatchTransaction::class, 'employee_id');
    }
}
