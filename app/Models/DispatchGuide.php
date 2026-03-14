<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo DispatchGuide - Guía de Despacho
 *
 * Representa una guía consolidada de despacho de materiales para un proyecto.
 * Permite gestionar el envío y la atención de materiales de forma formal (Guías).
 *
 * Esta guía agrupa múltiples ProjectRequirements que serán despachados juntos.
 * Cada ProjectRequirement puede tener múltiples DispatchTransactions (entregas individuales).
 *
 * Relación:
 * DispatchGuide (1) → ProjectRequirement (*) → DispatchTransaction (*)
 *
 * @property int $id Identificador único
 * @property int $project_id ID del proyecto asociado
 * @property int|null $dispatcher_id ID del usuario de almacén que marcó el envío de la guía
 * @property string|null $name Nombre o descripción de la guía (ej: Guía #01)
 * @property string $status Estado de la atención (attended, partial, pending)
 * @property string|null $tracking_number Número de seguimiento o guía física
 * @property \Illuminate\Support\Carbon|null $required_shipping_date Fecha requerida de envío
 * @property \Illuminate\Support\Carbon|null $attended_at Fecha y hora en que se realizó la atención
 * @property string|null $observations Observaciones o notas adicionales sobre la atención
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización
 *
 * @property-read \App\Models\Project $project El proyecto asociado
 * @property-read \App\Models\User|null $dispatcher El usuario que despachó la guía
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectRequirement> $projectRequirements Los requerimientos incluidos en esta guía
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchTransactions Las transacciones de entrega derivadas de esta guía
 */
class DispatchGuide extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'dispatch_guides';

    protected $fillable = [
        'project_id',
        'dispatcher_id',
        'name',
        'status',
        'tracking_number',
        'required_shipping_date',
        'attended_at',
        'observations',
    ];

    protected $casts = [
        'required_shipping_date' => 'date',
        'attended_at' => 'datetime',
    ];

    public function projectRequirements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectRequirement::class, 'dispatch_guide_id');
    }

    /**
     * Obtiene todas las transacciones de entrega asociadas a los requerimientos de esta guía.
     *
     * Una guía puede tener múltiples requerimientos, y cada requerimiento puede tener
     * múltiples transacciones de entrega desde diferentes fuentes.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function dispatchTransactions()
    {
        return DispatchTransaction::whereHas('projectRequirement', function ($query) {
            $query->where('dispatch_guide_id', $this->id);
        });
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Obtiene el usuario que marcó el envío de la guía.
     */
    public function dispatcher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }
}
