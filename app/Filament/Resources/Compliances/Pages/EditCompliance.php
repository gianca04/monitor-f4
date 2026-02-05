<?php

namespace App\Filament\Resources\Compliances\Pages;

use App\Filament\Resources\Compliances\ComplianceResource;
use App\Models\Compliance;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompliance extends EditRecord
{
    protected static string $resource = ComplianceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Acción Vista Previa
            Action::make('previewActaPdf')
                ->label('Vista Rápida')
                ->icon('heroicon-m-magnifying-glass-circle')
                ->color('gray')
                ->url(fn(Compliance $record) => route('actas.preview', $record->id))
                ->openUrlInNewTab(),
            //DeleteAction::make(),
        ];
    }
}
