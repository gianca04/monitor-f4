<?php

namespace App\Filament\Resources\Pricelists;

use App\Filament\Resources\Pricelists\Pages\CreatePricelist;
use App\Filament\Resources\Pricelists\Pages\EditPricelist;
use App\Filament\Resources\Pricelists\Pages\ListPricelists;
use App\Filament\Resources\Pricelists\Schemas\PricelistForm;
use App\Filament\Resources\Pricelists\Tables\PricelistsTable;
use App\Models\Pricelist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PricelistResource extends Resource
{
    protected static ?string $model = Pricelist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;
    protected static ?string $navigationLabel = 'Preciario';
    protected static ?string $pluralModelLabel = 'Preciario';
    protected static ?string $modelLabel = 'Preciario';
    protected static ?string $recordTitleAttribute = 'sat_line';

    public static function form(Schema $schema): Schema
    {
        return PricelistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricelistsTable::configure($table);
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
            'index' => ListPricelists::route('/'),
            //'create' => CreatePricelist::route('/create'),
            //'edit' => EditPricelist::route('/{record}/edit'),
        ];
    }
}
