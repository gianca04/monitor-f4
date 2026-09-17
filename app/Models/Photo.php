<?php

namespace App\Models;

use App\Services\ImageConversionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_report_id',
        'photo_path',
        'descripcion',
        'before_work_photo_path', // Nueva columna para la foto antes del trabajo
        'before_work_descripcion' // Nueva columna para la descripción antes del trabajo
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($photo) {
            // Convert photo_path to WebP in background
            if ($photo->photo_path && !str_ends_with(strtolower($photo->photo_path), '.webp')) {
                \App\Jobs\ConvertImageToWebPJob::dispatch(
                    $photo, 
                    'photo_path', 
                    config('filesystems.default'), 
                    auth()->id()
                );
            }

            // Convert before_work_photo_path to WebP in background
            if ($photo->before_work_photo_path && !str_ends_with(strtolower($photo->before_work_photo_path), '.webp')) {
                \App\Jobs\ConvertImageToWebPJob::dispatch(
                    $photo, 
                    'before_work_photo_path', 
                    config('filesystems.default'), 
                    auth()->id()
                );
            }
        });
    }

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function workReport()
    {
        return $this->belongsTo(WorkReport::class, 'work_report_id');
    }

    // Accessor para obtener la URL completa de la imagen
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }

    // Accessor para verificar si la imagen existe
    public function getPhotoExistsAttribute()
    {
        return $this->photo_path ? Storage::exists($this->photo_path) : false;
    }
}
