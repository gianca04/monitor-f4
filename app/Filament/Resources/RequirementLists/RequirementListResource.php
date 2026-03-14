<?php

namespace App\Filament\Resources\RequirementLists;

use App\Filament\Resources\RequirementLists\Pages\CreateRequirementList;
use App\Filament\Resources\RequirementLists\Pages\EditRequirementList;
use App\Filament\Resources\RequirementLists\Pages\ListRequirementLists;
use App\Filament\Resources\RequirementLists\Schemas\RequirementListForm;
use App\Filament\Resources\RequirementLists\Tables\RequirementListsTable;
use App\Models\RequirementList;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RequirementListResource extends Resource
{
    protected static ?string $model = RequirementList::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RequirementListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequirementListsTable::configure($table);
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
            'index' => ListRequirementLists::route('/'),
            'create' => CreateRequirementList::route('/create'),
            'edit' => EditRequirementList::route('/{record}/edit'),
        ];
    }
}
