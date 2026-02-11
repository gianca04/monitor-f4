<?php

namespace App\Filament\Resources\ProjectRequirements;

use App\Filament\Resources\ProjectRequirements\Pages\CreateProjectRequirement;
use App\Filament\Resources\ProjectRequirements\Pages\EditProjectRequirement;
use App\Filament\Resources\ProjectRequirements\Pages\ListProjectRequirements;
use App\Filament\Resources\ProjectRequirements\Schemas\ProjectRequirementForm;
use App\Filament\Resources\ProjectRequirements\Tables\ProjectRequirementsTable;
use App\Models\ProjectRequirement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectRequirementResource extends Resource
{
    protected static ?string $model = ProjectRequirement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'requirement_id';

    public static function form(Schema $schema): Schema
    {
        return ProjectRequirementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectRequirementsTable::configure($table);
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
            'index' => ListProjectRequirements::route('/'),
            'create' => CreateProjectRequirement::route('/create'),
            'edit' => EditProjectRequirement::route('/{record}/edit'),
        ];
    }
}
