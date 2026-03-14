<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Location - Lugares o Estaciones
 *
 * Representa un lugar físico, estación, sector o almacén donde se despachan o reciben materiales.
 * Se utiliza para rastrear el origen y destino de las transacciones de entrega.
 *
 * Ejemplos: Almacén Central, Sitio de Obra, Oficina, Almacén Temporal, etc.
 *
 * Relación con DispatchTransaction:
 * - location_origin_id → De dónde se remite la transacción
 * - location_destination_id → Hacia dónde se envía
 *
 * @property int $id Identificador único
 * @property string $name Nombre del lugar (ej: "Almacén Central", "Sitio de Obra")
 * @property string|null $description Descripción opcional del lugar
 * @property bool $is_active Indica si el lugar está activo
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchesAsOrigin Las transacciones que se despachan desde esta ubicación
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchesAsDestination Las transacciones que llegan a esta ubicación
 */
class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtiene todas las transacciones que se despachan DESDE esta ubicación.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchesAsOrigin()
    {
        return $this->hasMany(DispatchTransaction::class, 'location_origin_id');
    }

    /**
     * Obtiene todas las transacciones que llegan A esta ubicación.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchesAsDestination()
    {
        return $this->hasMany(DispatchTransaction::class, 'location_destination_id');
    }
}
