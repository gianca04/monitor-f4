<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\QuotesRelationManager;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
// AGREGA ESTOS DOS para las acciones de los inputs:
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;
    protected static ?string $pluralModelLabel = 'Solicitudes de trabajo';
    protected static ?string $modelLabel = 'Solicitud de trabajo';
    protected static ?string $recordTitleAttribute = 'Proyectos';

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->allowedForUser(Auth::user());
    }


    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            QuotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Solicitudes de trabajo')
                ->icon(Heroicon::OutlinedPuzzlePiece)
                ->group('Operaciones')
                ->url(static::getUrl())
                ->sort(2),
        ];
    }
}
