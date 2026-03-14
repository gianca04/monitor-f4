<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo RequirementList - Lista de Requerimientos
 *
 * Representa una lista agrupada de requerimientos de materiales para un proyecto.
 * Permite gestionar el despacho y la atención de materiales de forma consolidada.
 *
 * @property int $id Identificador único
 * @property int $project_id ID del proyecto asociado
 * @property int|null $dispatcher_id ID del usuario de almacén que marcó el envío de la guía
 * @property string|null $name Nombre o descripción de la lista (ej: Guia #01)
 * @property string $status Estado de la atención (attended, partial, pending)
 * @property string|null $tracking_number Número de seguimiento o guía
 * @property \Illuminate\Support\Carbon|null $required_shipping_date Fecha requerida de envío
 * @property \Illuminate\Support\Carbon|null $attended_at Fecha y hora en que se realizó la atención
 * @property string|null $observations Observaciones o notas adicionales sobre la atención
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\User|null $dispatcher
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProjectRequirement[] $projectRequirements
 */
class RequirementList extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->hasMany(ProjectRequirement::class);
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
