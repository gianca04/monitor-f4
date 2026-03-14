<?php

namespace App\Filament\Resources\DispatchGuides;

use App\Filament\Resources\DispatchGuides\Pages\CreateDispatchGuide;
use App\Filament\Resources\DispatchGuides\Pages\EditDispatchGuide;
use App\Filament\Resources\DispatchGuides\Pages\ListDispatchGuides;
use App\Filament\Resources\DispatchGuides\Schemas\DispatchGuideForm;
use App\Filament\Resources\DispatchGuides\Tables\DispatchGuidesTable;
use App\Models\DispatchGuide;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DispatchGuideResource extends Resource
{
    protected static ?string $model = DispatchGuide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DispatchGuideForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DispatchGuidesTable::configure($table);
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
            'index' => ListDispatchGuides::route('/'),
            'create' => CreateDispatchGuide::route('/create'),
            'edit' => EditDispatchGuide::route('/{record}/edit'),
        ];
    }
}
