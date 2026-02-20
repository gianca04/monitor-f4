<?php

namespace App\Filament\Resources\VisitReports;

use App\Filament\Resources\VisitReports\Pages\CreateVisitReport;
use App\Filament\Resources\VisitReports\Pages\EditVisitReport;
use App\Filament\Resources\VisitReports\Pages\ListVisitReports;
use App\Filament\Resources\VisitReports\Schemas\VisitReportForm;
use App\Filament\Resources\VisitReports\Tables\VisitReportsTable;
use App\Models\VisitReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VisitReportResource extends Resource
{
    protected static ?string $model = VisitReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static ?string $modelLabel = 'Reporte de Visita';

    protected static ?string $pluralModelLabel = 'Reportes de Visita';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return VisitReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitReports::route('/'),
            'create' => CreateVisitReport::route('/create'),
            'edit' => EditVisitReport::route('/{record}/edit'),
        ];
    }
}
