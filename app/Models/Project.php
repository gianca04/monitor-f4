<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


/**
 * Model Project
 *
 * Represents a project entity with location and scheduling data.
 */
class Project extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(function (Project $project) {
            // Verificar si no existe ninguna cotización asociada a este proyecto
            if (!$project->quotes()->exists()) {
                Quote::create([
                    'project_id' => $project->id,
                    'sub_client_id' => $project->sub_client_id,
                    'status' => 'Pendiente',
                    'energy_sci_manager' => 'Raul Quispe',
                    'request_number' => Quote::generateNextRequestNumber($project->id),
                ]);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 1. DATOS GENERALES / SOLICITUD
        'name',             // Descripción de la solicitud
        'service_code',     // Código de Servicio Correlativo
        'request_number',   // N° de Solicitud
        'service_start_date',       // Fecha solicitud
        'sub_client_id',    // Cliente (ID)
        'location',         // Tienda (JSON)
        'comment',          // Comentario

        // 2. SERVICE (EXECUTION)
        'work_order_number',    // Antes: ot
        'service_start_date',   // Antes: fecha_inicio_servicio
        'service_end_date',     // Antes: fecha_fin_servicio
        'service_days',         // Antes: dias
        'task_type',            // Antes: tarea
        'has_report',           // Antes: informe

        //3. BILLING
        'fracttal_status', // Antes: fracttal
        'purchase_order', // Antes: orden_compra
        'migo_code', // Antes: migo

        //4. TRACKING DATA
        'status',             // estado: Pendiente, Enviada, Aprobado...
        'quote_sent_at',      // fecha_cot_enviada
        'quote_approved_at',  // fecha_cot_aprobada
        'wo_review_at',       // fecha_ot_revision
        'wo_completed_at',    // fecha_ot_finalizado
        'days_to_completion', // dias_hasta_finalizacion
        'final_comments',     // comentario_observación: Observaciones finales
        'end_date',


        //Supervisor
        'supervisor_id',
        'employee_id',
    ];

    protected $casts = [
        // 1. DATOS GENERALES
        'start_date' => 'date',
        'end_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'location' => 'array',
        'sub_client_id' => 'integer',

        // 2. SERVICE (EXECUTION)
        'service_start_date' => 'date',
        'service_end_date' => 'date',
        'service_days' => 'integer',
        'has_report' => 'string',

        'fracttal_status' => 'string',

        'status' => 'string',

        // 4. TRACKING DATA (Fechas y Números)
        'quote_sent_at' => 'datetime',
        'quote_approved_at' => 'datetime',
        'wo_review_at' => 'datetime',
        'wo_completed_at' => 'datetime',
        'days_to_completion' => 'integer',
        // Otros
        'quote_id' => 'integer',
        'supervisor_id' => 'integer',
        // Se eliminaron tools, personnel y materials por no estar en fillable
    ];

    public function visit()
    {
        return $this->hasOne(Visit::class);
    }
    // Simplificación de relación para evitar duplicidad
    public function workReports()
    {
        // Si es una relación directa de muchos:
        return $this->hasMany(WorkReport::class, 'project_id');

        // O si es Muchos a Muchos (como sugería tu método Work_reports):
        // return $this->belongsToMany(WorkReport::class, 'work_report_project')->withTimestamps();
    }
    // En tu modelo Project
    public function compliance()
    {
        // Relación 1 a 1: Un proyecto tiene un (o ningún) acta de conformidad
        return $this->hasOne(Compliance::class, 'project_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'project_id');
    }
    public function quote()
    {
        return $this->hasOne(Quote::class, 'project_id');
    }
    public function latestQuote(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quote::class, 'project_id')->latestOfMany();
    }

    public function scopeHasQuote(Builder $query)
    {
        return $query->has('quotes');
    }

    public function scopeAllowedForUser(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // 1. Superusuarios ven todo
        if ($user->hasRole(['Administrador', 'Gerencial'])) {
            return $query;
        }

        $employeeId = $user->employee_id;

        if (!$employeeId) {
            return $query->whereRaw('1 = 0');
        }

        // 2. Filtro: Es el creador O es un inspector asignado
        return $query->where(function (Builder $q) use ($employeeId) {
            $q->where('employee_id', $employeeId) // Es el creador
                ->orWhereHas('inspectors', function (Builder $pivotQuery) use ($employeeId) {
                    $pivotQuery->where('employee_id', $employeeId); // Está asignado como inspector
                });
        });
    }
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_project');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }


    public function photos()
    {
        return $this->hasManyThrough(Photo::class, WorkReport::class, 'project_id', 'work_report_id');
    }

    public function getLocationLatitudeAttribute()
    {
        if (!$this->location || !is_array($this->location))
            return null;
        return $this->location['latitude'] ?? null;
    }

    public function getLocationLongitudeAttribute()
    {
        if (!$this->location || !is_array($this->location))
            return null;
        return $this->location['longitude'] ?? null;
    }

    public function Work_reports()
    {
        return $this->belongsToMany(WorkReport::class, 'work_report_project')
            ->withTimestamps();
    }

    public function inspectors()
    {
        // Esto apunta al modelo intermedio EmployeeProject
        return $this->hasMany(EmployeeProject::class, 'project_id');
    }

    // Relación BelongsTo para el supervisor único
    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    // Mantenemos esto por si se usaba, pero advirtiendo que ahora hay un supervisor principal
    public function supervisors()
    {
        return $this->belongsToMany(Employee::class, 'employee_project');
    }

    public function getHasQuoteAttribute(): string
    {
        return $this->quotes()->exists() ? 'SI' : 'NO';
    }


    public function subClient()
    {
        return $this->belongsTo(SubClient::class, 'sub_client_id');
    }

    public function client()
    {
        return $this->hasOneThrough(Client::class, Quote::class, 'id', 'id', 'quote_id', 'client_id');
    }
    public function timesheetEmployees()
    {
        return $this->belongsToMany(Employee::class, 'timesheets');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_project')
            ->withTimestamps();
        // Si usas el modelo pivote personalizado:
        // ->using(EmployeeProject::class);
    }
    public function getLocationAddressAttribute()
    {
        if (!$this->location || !is_array($this->location))
            return null;
        return $this->location['location'] ?? null;
    }

    /**
     * Get formatted coordinates as string
     */
    public function getCoordinatesAttribute()
    {
        $lat = $this->location_latitude;
        $lng = $this->location_longitude;

        if ($lat && $lng) {
            return sprintf('%.6f, %.6f', $lat, $lng);
        }

        return null;
    }

    /**
     * Check if project is currently active (within date range)
     */
    public function getIsActiveAttribute()
    {
        $now = now()->toDateString();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    /**
     * Relación: Un proyecto tiene muchos consumos.
     */
    public function consumptions()
    {
        return $this->hasMany(ProjectConsumption::class);
    }
}
