<?php

namespace App\Models;

use App\Services\ConsumptionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo WorkReport (Reporte de Trabajo)
 *
 * Representa un reporte de actividades realizadas en un proyecto,
 * incluyendo materiales consumidos, personal involucrado y evidencias fotográficas.
 *
 * @property int $id Identificador único
 * @property int $employee_id ID del empleado (supervisor/técnico)
 * @property int $project_id ID del proyecto asociado
 * @property int|null $compliance_id ID de la conformidad asociada
 * @property string $name Nombre del reporte
 * @property string|null $suggestions Recomendaciones
 * @property string|null $conclusions Conclusiones
 * @property array|null $personnel Personal que realizó el trabajo (JSON)
 * @property array|null $materials Materiales utilizados (JSON)
 * @property string|null $work_to_do Trabajos a realizar
 * @property \Illuminate\Support\Carbon|null $start_time Hora de inicio
 * @property \Illuminate\Support\Carbon|null $end_time Hora de finalización
 * @property \Illuminate\Support\Carbon|null $report_date Fecha del reporte
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\Compliance|null $compliance
 * @property-read \App\Models\SubClient|null $subClient (accessor)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Photo[] $photos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProjectConsumption[] $projectConsumptions
 */
class WorkReport extends Model
{
    use HasFactory;
    protected $table = 'work_reports';

    protected $fillable = [
        'employee_id',
        'project_id',
        'compliance_id',
        'name',
        'suggestions',
        'conclusions',
        'personnel',
        'materials',
        'work_to_do',
        'start_time',
        'end_time',
        'report_date',
    ];

    protected $casts = [
        'personnel' => 'array',
        'materials' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'report_date' => 'date',
    ];

    /**
     * Relación: Un reporte de trabajo pertenece a un empleado.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relación: Un reporte de trabajo pertenece a un proyecto.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación: Un reporte de trabajo pertenece a una conformidad.
     */
    public function compliance(): BelongsTo
    {
        return $this->belongsTo(Compliance::class);
    }

    /**
     * Accessor: Obtiene el sub-cliente a través del proyecto.
     */
    public function getSubClientAttribute()
    {
        return $this->project?->subClient;
    }

    /**
     * Relación: Un reporte de trabajo tiene muchas fotos.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'work_report_id');
    }

    /**
     * Relación: Un reporte de trabajo puede tener muchos consumos de proyecto.
     */
    public function projectConsumptions(): HasMany
    {
        return $this->hasMany(ProjectConsumption::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($workReport) {
            // Eliminar en cascada los consumos asociados
            $workReport->projectConsumptions()->delete();
        });
    }

    /**
     * Obtiene los materiales disponibles para el proyecto.
     * Delega la lógica al ConsumptionService.
     */
    public function getAvailableMaterials()
    {
        if (!$this->project_id) {
            return collect();
        }

        return app(ConsumptionService::class)
            ->getAvailableMaterials($this->project_id);
    }
}
