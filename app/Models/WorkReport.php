<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class WorkReport extends Model
{
    use HasFactory;
    protected $table = 'work_reports';

    protected $fillable = [
        'employee_id',
        'project_id',
        'compliance_id',
        'name',
        'supervisor_signature', //NO LOS NECESITO PERO NO LOS BORARRE
        'manager_signature', //NO LOS NECESITO PERO NO LOS BORARRE
        'suggestions',
        'tools',
        'conclusions',
        'personnel',
        'materials', // Asegúrate de que esté aquí
        'work_to_do',      // Trabajos a realizar
        'start_time',  // Hora de inicio del trabajo
        'end_time',    // Hora de finalizacin del trabajo
        'report_date',  // Fecha del reporte (solo fecha)
    ];

    protected $casts = [
        'personnel' => 'array',
        'materials' => 'array', // Asegúrate de que esté aquí
        'tools' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'report_date' => 'date',
    ];

    /**
     * Relacin: Un reporte de trabajo pertenece a un empleado.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relacin: Un reporte de trabajo pertenece a un proyecto.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function compliance()
    {
        return $this->belongsTo(Compliance::class);
    }
    public function getSubClientAttribute()
    {
        return $this->project?->subClient;
    }

    public function photos()
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

    public function getAvailableMaterials()
    {
        // Si el reporte no tiene proyecto, devolvemos colección vacía
        if (!$this->project_id) {
            return collect();
        }

        // Re-intento de Query Builder sólida:
        return DB::table('quote_warehouse_details as qwd')
            ->join('project_requirements as pr', 'qwd.project_requirement_id', '=', 'pr.id')
            ->leftJoin('requirements as r', 'pr.requirement_id', '=', 'r.id')
            ->leftJoin('quote_details as qd', 'pr.quote_detail_id', '=', 'qd.id')
            ->leftJoin('pricelists as p', 'qd.pricelist_id', '=', 'p.id')
            ->leftJoin('units as u_req', 'r.unit_id', '=', 'u_req.id')
            ->leftJoin('units as u_price', 'p.unit_id', '=', 'u_price.id')
            ->where('pr.project_id', $this->project_id) // Filtrar por proyecto directo del requerimiento
            ->where('qwd.attended_quantity', '>', 0)
            ->select([
                'qwd.id',
                DB::raw("COALESCE(r.product_description, p.sat_description, pr.comments) as sat_description"),
                DB::raw("COALESCE(p.sat_line, 'SUMINISTRO') as sat_line"),
                DB::raw("COALESCE(u_req.name, u_price.name, 'Unid') as unit_name"),
                'qwd.attended_quantity'
            ])
            ->get();
    }
}
