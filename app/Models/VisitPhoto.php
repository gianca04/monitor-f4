<?php

namespace App\Models;

use App\Services\ImageConversionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VisitPhoto extends Model
{
    use HasFactory;

    protected $table = 'visit_photos';

    protected $fillable = [
        'visit_report_id',
        'photo_path',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($visitPhoto) {
            // Convert photo_path to WebP if exists
            if ($visitPhoto->photo_path) {
                $convertedPath = ImageConversionService::convertToWebP($visitPhoto->photo_path);
                if ($convertedPath && $convertedPath !== $visitPhoto->photo_path) {
                    $visitPhoto->updateQuietly(['photo_path' => $convertedPath]);
                }
            }
        });
    }

    /**
     * Relación: Una foto de visita pertenece a un reporte de visita.
     */
    public function visitReport()
    {
        return $this->belongsTo(VisitReport::class, 'visit_report_id');
    }

    /**
     * Accessor para obtener la URL completa de la imagen.
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }

    /**
     * Accessor para verificar si la imagen existe.
     */
    public function getPhotoExistsAttribute()
    {
        return $this->photo_path ? Storage::exists($this->photo_path) : false;
    }
}
