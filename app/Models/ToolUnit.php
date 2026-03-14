<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Modelo ToolUnit - Unidad de Herramienta
 *
 * Representa una unidad individual de una herramienta con número de serie y certificación.
 * Se utiliza para rastrear herramientas específicas en entregas.
 *
 * Relación con DispatchTransaction:
 * - tool_unit_id en DispatchTransaction señala qué herramienta fue usada en la entrega
 * - Permite auditar el uso de herramientas específicas a nivel de transacción
 *
 * @property int $id Identificador único
 * @property int $tool_id ID de la herramienta
 * @property string|null $internal_code Código interno de la unidad
 * @property string|null $serial_number Número de serie
 * @property string $status Estado (Disponible, En Reparación, Descartado, etc.)
 * @property string|null $certification_document Documento de certificación
 * @property \Illuminate\Support\Carbon|null $certification_expiry Fecha de expiración de certificación
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización
 *
 * @property-read \App\Models\Tool $tool La herramienta base
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectTool> $projectTools Los proyectos que usan esta unidad
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectRequirement> $projectRequirements Los requerimientos vinculados a esta unidad
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchTransactions Las transacciones que utilizaron esta unidad
 */
class ToolUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'internal_code',
        'serial_number',
        'status',
        'certification_document',
        'certification_expiry',
    ];

    protected $casts = [
        'certification_expiry' => 'date',
        'tool_id' => 'integer',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function projectTools(): HasMany
    {
        return $this->hasMany(ProjectTool::class, 'tool_unit_id');
    }

    public function projectRequirements(): MorphMany
    {
        return $this->morphMany(ProjectRequirement::class, 'requirementable');
    }

    /**
     * Obtiene todas las transacciones de entrega que utilizaron esta unidad de herramienta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchTransactions(): HasMany
    {
        return $this->hasMany(DispatchTransaction::class, 'tool_unit_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'Disponible');
    }
}
