<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compliance extends Model
{
    use HasFactory;

    protected $table = 'compliance';

    /**
     * Scope para filtrar compliances por inspector (empleado asignado al proyecto).
     * 
     * Relación: Compliance -> Project -> employee_project
     */
    public function scopeForInspector(Builder $query, int $employeeId): Builder
    {
        return $query->whereHas('project.employees', function (Builder $q) use ($employeeId) {
            $q->where('employee_project.employee_id', $employeeId);
        });
    }

    protected $fillable = [
        'project_id',
        'assets',
        'maintenance_observations',
        'fullname_cliente',
        'document_type',
        'document_number',
        'client_signature', //firma cliente
        'employee_signature', //firma empleado
        'state'
    ];

    protected $casts = [
        'assets' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'project_id' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function workReports()
    {
        // Accede a los WorkReports a través del Project asociado
        return $this->hasManyThrough(
            WorkReport::class,
            Project::class,
            'id',
            'project_id',
            'project_id',
            'id'
        );
    }
}
