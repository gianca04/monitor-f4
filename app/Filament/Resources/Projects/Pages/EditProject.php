<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    // Esto hace que el formulario se refresque cuando llega el evento
    protected function getListeners(): array
    {
        return [
            'update-parent-form' => 'refreshForm'
        ];
    }

    public function refreshForm(): void
    {
        // Refresca el modelo desde la base de datos
        $this->record->refresh();

        // Rellena el formulario con los nuevos datos del modelo
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }
}
