<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\ProjectRequirements\Schemas;
use App\Filament\Resources\ProjectRequirements\Schemas\ProjectRequirementForm;
use App\Filament\Resources\ProjectRequirements\Tables;
use App\Filament\Resources\ProjectRequirements\Tables\ProjectRequirementsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;


class ProjectRequirementsRelationManager extends RelationManager
{
    protected static ?string $title = 'Requerimientos';
    protected static ?string $pluralModelLabel = 'Requerimientos';
    protected static ?string $modelLabel = 'Requerimiento';
    protected static string $relationship = 'ProjectRequirements';

    public function form(Schema $schema): Schema
    {
        return ProjectRequirementForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ProjectRequirementsTable::configure($table)->headerActions([
            CreateAction::make(),
        ]);
    }
}
