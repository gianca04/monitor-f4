<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReport extends Model
{
    use HasFactory;

    protected $table = 'visit_reports';

    protected $fillable = [
        'employee_id',
        'project_id',
        'name',
        'suggestions',
        'conclusions',
        'work_to_do',      // Trabajos a realizar
        'start_time',      // Hora de inicio del trabajo
        'end_time',        // Hora de finalización del trabajo
        'report_date',     // Fecha del reporte (solo fecha)
    ];

    protected $casts = [
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
        'report_date' => 'date',
    ];

    /**
     * Relación: Un reporte de visita pertenece a un empleado.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relación: Un reporte de visita pertenece a un proyecto.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Accessor: Obtener el sub-cliente a través del proyecto.
     */
    public function getSubClientAttribute()
    {
        return $this->project?->subClient;
    }

    /**
     * Relación: Un reporte de visita puede tener muchas fotos.
     */
    public function visitPhotos()
    {
        return $this->hasMany(VisitPhoto::class, 'visit_report_id');
    }
}
