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
                ->icon('heroicon-m-eye')
                ->color('gray')
                ->url(fn(Compliance $record) => route('actas.preview', $record->id))
                ->openUrlInNewTab(),

            // Descargar PDF (Acta sola o Acta + Reportes según existan)
            Action::make('downloadPdfOrWithReports')
                ->label(
                    fn(Compliance $record) =>
                    $record->workReports()->count() > 0
                        ? 'PDF | Acta + Reportes'
                        : 'PDF | Descargar Acta'
                )
                ->icon('heroicon-m-document-arrow-down')
                ->color(
                    fn(Compliance $record) =>
                    $record->workReports()->count() > 0
                        ? 'danger'
                        : 'danger'
                )
                ->url(
                    fn(Compliance $record) =>
                    $record->workReports()->count() > 0
                        ? route('actas.pdf-with-reports', $record->id)
                        : route('actas.pdf', $record->id)
                )
                ->openUrlInNewTab(),

            //DeleteAction::make(),
        ];
    }
}
