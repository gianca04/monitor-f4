<?php

namespace App\Filament\Resources\WorkReports;

use App\Filament\Resources\WorkReports\Pages\CreateWorkReport;
use App\Filament\Resources\WorkReports\Pages\EditWorkReport;
use App\Filament\Resources\WorkReports\Pages\ListWorkReports;
use App\Filament\Resources\WorkReports\Schemas\WorkReportForm;
use App\Filament\Resources\WorkReports\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\WorkReports\Tables\WorkReportsTable;
use App\Models\WorkReport;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkReportResource extends Resource
{
    protected static ?string $model = WorkReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Reportes de trabajo';
    protected static ?string $title = 'Reportes de trabajo';
    protected static ?string $modelLabel = 'Reportes de trabajo';
    protected static ?string $pluralModelLabel = 'Reportes de trabajo';
    protected static ?string $singularModelLabel = 'Reportes de trabajo';
    public static function form(Schema $schema): Schema
    {
        return WorkReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkReports::route('/'),
            'create' => CreateWorkReport::route('/create'),
            'edit' => EditWorkReport::route('/{record}/edit'),
        ];
    }
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Reportes de trabajo')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->group('Operaciones')
                ->url(static::getUrl())
                ->sort(3),
        ];
    }
}
