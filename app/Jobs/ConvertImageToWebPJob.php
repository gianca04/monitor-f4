<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Services\ImageConversionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ConvertImageToWebPJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;
    public $field;
    public $disk;
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model, string $field, string $disk = 'public', ?int $userId = null)
    {
        $this->model = $model;
        $this->field = $field;
        $this->disk = $disk;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $path = $this->model->{$this->field};

        if (!$path) {
            return;
        }

        try {
            $convertedPath = ImageConversionService::convertToWebP($path, $this->disk);

            if ($convertedPath && $convertedPath !== $path) {
                // Update without triggering another save event
                $this->model->updateQuietly([$this->field => $convertedPath]);
                
                // Notify user if userId was provided
                if ($this->userId) {
                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Imagen convertida a WebP')
                            ->body('La imagen se ha convertido exitosamente en segundo plano.')
                            ->success()
                            ->sendToDatabase($user)
                            ->broadcast($user);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("ConvertImageToWebPJob Error: " . $e->getMessage());
            
            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user) {
                    Notification::make()
                        ->title('Error al convertir imagen')
                        ->body('No se pudo convertir la imagen a WebP.')
                        ->danger()
                        ->sendToDatabase($user)
                        ->broadcast($user);
                }
            }
        }
    }
}
