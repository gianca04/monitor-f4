<?php

namespace App\Filament\Resources\VisitReports\Pages;

use App\Filament\Resources\VisitReports\VisitReportResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisitReport extends EditRecord
{
    protected static string $resource = VisitReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_evidence_report')
                ->label('')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn($record) => route('visit-report-evidence.pdf', ['visitReport' => $record->id, 'inline' => true]))
                ->openUrlInNewTab()
                ->visible(fn($record) => $record->visitPhotos()->exists())
                ->tooltip('Generar informe PDF con evidencias fotográficas'),
            //DeleteAction::make(),
        ];
    }
}
