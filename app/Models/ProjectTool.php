<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ProjectTool - Asignación de Herramientas a Proyectos
 *
 * @property int $id
 * @property int $project_id
 * @property int $tool_id
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property \Illuminate\Support\Carbon|null $returned_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\Tool $tool
 */
class ProjectTool extends Model
{
    use HasFactory;

    protected $table = 'project_tools';

    protected $fillable = [
        'project_id',
        'tool_id',
        'assigned_at',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'returned_at' => 'date',
        'project_id' => 'integer',
        'tool_id' => 'integer',
    ];

    /**
     * Relación: Una asignación pertenece a un proyecto.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación: Una asignación pertenece a una herramienta.
     */
    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }
}
