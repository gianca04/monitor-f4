<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Location - Lugares o Estaciones
 *
 * Representa un lugar físico, estación o sector donde se despachan o reciben materiales.
 *
 * @property int $id Identificador único
 * @property string $name Nombre del lugar
 * @property string|null $description Descripción opcional del lugar
 * @property bool $is_active Indica si el lugar está activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
