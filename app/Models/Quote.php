<?php

namespace App\Models;

use App\Observers\QuoteObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Quote - Cotizaciones
 *
 * Este modelo representa las cotizaciones emitidas a los subclientes.
 * Cada cotización está asociada a un empleado cotizador, un subcliente y una categoría.
 *
 * @property int $id Identificador único de la cotización
 * @property string|null $request_number Número de solicitud de referencia
 * @property int|null $employee_id ID del empleado cotizador
 * @property int|null $sub_client_id ID del subcliente al que se le cotiza
 * @property int|null $quote_category_id ID de la categoría de cotización
 * @property string|null $energy_sci_manager Nombre del Jefe de Energía/SCI o contacto responsable
 * @property string|null $ceco Centro de Costos asociado a la cotización
 * @property string $status Estado de la cotización (Pendiente, Enviado, Aprobado, Anulado)
 * @property \Illuminate\Support\Carbon|null $quote_date Fecha en que se emite el documento
 * @property \Illuminate\Support\Carbon|null $execution_date Fecha estimada de ejecución del servicio
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación del registro
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización del registro
 *
 * @property-read \App\Models\Employee|null $employee Empleado cotizador asignado
 * @property-read \App\Models\SubClient|null $subClient Subcliente al que se le cotiza
 * @property-read \App\Models\QuoteCategory|null $quoteCategory Categoría de la cotización
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects Proyectos generados desde esta cotización
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Visit[] $visits Visitas asociadas a esta cotización
 */
class Quote extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::observe(QuoteObserver::class);
    }

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'quotes';

    /**
     * Los atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 'service_name', // Removed as per refactor
        'request_number',
        'project_id',
        'employee_id',
        'sub_client_id',
        'quote_category_id',
        'energy_sci_manager',
        'ceco',
        'status',
        'quote_date',
        'execution_date',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quote_date' => 'date',
        'execution_date' => 'date',
        'energy_sci_manager' => 'string',
        'ceco' => 'string',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtiene el empleado cotizador asignado a esta cotización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Employee, \App\Models\Quote>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Obtiene el subcliente al que se le emite la cotización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SubClient, \App\Models\Quote>
     */
    public function subClient(): BelongsTo
    {
        return $this->belongsTo(SubClient::class, 'sub_client_id');
    }

    /**
     * Obtiene la categoría de la cotización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\QuoteCategory, \App\Models\Quote>
     */
    public function quoteCategory(): BelongsTo
    {
        return $this->belongsTo(QuoteCategory::class, 'quote_category_id');
    }

    /**
     * Obtiene los proyectos generados a partir de esta cotización.
     *
     * Una cotización puede generar múltiples proyectos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Project>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'quote_id');
    }

    /**
     * Obtiene los detalles/ítems de esta cotización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\QuoteDetail>
     */
    public function details(): HasMany
    {
        return $this->hasMany(QuoteDetail::class, 'quote_id');
    }

    /**
     * Alias para obtener los detalles/ítems de esta cotización (compatibilidad con el controlador).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\QuoteDetail>
     */
    public function quoteDetails(): HasMany
    {
        return $this->details();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function quoteWarehouse(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(QuoteWarehouse::class, 'quote_id');
    }

    /**
     * Obtiene las visitas asociadas a esta cotización.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Visit>
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'quote_id');
    }

    /**
     * Obtiene el monto total de la cotización (suma de todos los detalles).
     *
     * @return float
     */
    public function getTotalAmountAttribute(): float
    {
        // Suma el subtotal de cada detalle (más preciso si hay descuentos o cálculos especiales)
        return (float) round($this->details->sum(function ($detail) {
            return $detail->subtotal ?? ($detail->quantity * $detail->unit_price);
        }), 1);
    }

    /**
     * Scope para búsqueda avanzada.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('request_number', 'LIKE', "%{$search}%")
                ->orWhereHas('project', function ($pq) use ($search) {
                    $pq->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('subClient', function ($sq) use ($search) {
                    $sq->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('employee', function ($sq) use ($search) {
                    // Buscamos en ambos campos del empleado
                    $sq->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
        });
    }

    public static function createWithProject(array $data)
    {
        // Si ya viene un ID de cotización, NO crear una nueva, retornar la existente
        if (!empty($data['id'])) {
            $existingQuote = self::find($data['id']);
            if ($existingQuote) {
                // Actualizar en lugar de crear
                unset($data['id']);
                unset($data['request_number']); // No cambiar el request_number
                $existingQuote->update($data);
                return $existingQuote;
            }
        }

        // Si ya existe project_id, verificar si ya hay cotización para este proyecto
        if (!empty($data['project_id'])) {
            // Buscar cotización existente para este proyecto creada recientemente
            $existingQuote = self::where('project_id', $data['project_id'])
                ->orderBy('created_at', 'desc')
                ->first();

            // Si existe y fue creada en los últimos 30 segundos, actualizar en lugar de crear
            if ($existingQuote && $existingQuote->created_at->diffInSeconds(now()) < 30) {
                unset($data['request_number']);
                $existingQuote->update($data);
                return $existingQuote;
            }
        }

        // Si no hay project_id, creamos un nuevo proyecto
        if (empty($data['project_id'])) {
            // Creamos el proyecto con los datos mínimos requeridos
            $project = new Project();
            $project->sub_client_id = $data['sub_client_id'] ?? null;
            $project->name = $data['project_name'] ?? null;
            $project->service_code = null; // Se asigna después
            $project->save();

            // Asignamos el service_code tipo COT-$ID (sin padding)
            $project->service_code = 'COT-' . $project->id;
            // Si el proyecto tiene fecha de envío de cotización, la usamos para la cotización
            if (!empty($data['quote_sent_at'])) {
                $project->quote_sent_at = $data['quote_sent_at'];
            }
            $project->save();

            // Si hay fecha de envío en el proyecto, la cotización toma esa fecha
            if ($project->quote_sent_at) {
                $data['quote_date'] = $project->quote_sent_at;
            }

            $data['project_id'] = $project->id;

            // Creamos la visita asociada al proyecto con el cotizador
            if (!empty($data['employee_id'])) {
                Visit::create([
                    'project_id' => $project->id,
                    'quoted_by_id' => $data['employee_id'],
                    'visit_date' => now()->toDateString(),
                ]);
            }
        }

        // Generamos el request_number para la cotización SOLO si no existe
        if (empty($data['request_number'])) {
            $data['request_number'] = self::generateNextRequestNumber($data['project_id']);
        }

        // Verificar una última vez que no exista ya con este request_number
        $existingByRequestNumber = self::where('request_number', $data['request_number'])->first();
        if ($existingByRequestNumber) {
            unset($data['request_number']);
            $existingByRequestNumber->update($data);
            return $existingByRequestNumber;
        }

        // Creamos la cotización
        return self::create($data);
    }


    /**
     * Scope to include the total calculated by the database function.
     */
    public function scopeWithTotal($query)
    {
        return $query->addSelect(['quotes.*', \Illuminate\Support\Facades\DB::raw('calculate_quote_total(quotes.id) as total_cost')]);
    }
    public function scopeCountApproved($query)
    {
        return $query->where('status', 'Aprobado')->count();
    }


    // Modifica generateNextRequestNumber para que funcione igual
    public static function generateNextRequestNumber($projectId)
    {
        $project = \App\Models\Project::find($projectId);
        if (!$project) return 'COT-00000-A';

        // Usar el ID del proyecto sin padding de ceros
        $baseNumber = $project->service_code ?? $project->request_number ?? (string) $project->id;

        // Si ya empieza con 'COT-', no lo agregamos de nuevo
        if (stripos($baseNumber, 'COT-') === 0) {
            $base = $baseNumber;
        } else {
            $base = 'COT-' . $baseNumber;
        }

        // Contamos cuántas cotizaciones existen para este proyecto
        $count = self::where('project_id', $projectId)->count();

        // Convertimos el número a letra (0 = A, 1 = B, 2 = C...)
        $letter = chr(65 + $count);

        return "{$base}-{$letter}";
    }

    /**
     * Obtiene los detalles con relaciones cargadas.
     */
    protected $appends = ['total_amount'];
}
