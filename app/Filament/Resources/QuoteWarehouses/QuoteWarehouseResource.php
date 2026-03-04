<?php

namespace App\Filament\Resources\QuoteWarehouses;

use App\Filament\Resources\QuoteWarehouses\Pages\CreateQuoteWarehouse;
use App\Filament\Resources\QuoteWarehouses\Pages\EditQuoteWarehouse;
use App\Filament\Resources\QuoteWarehouses\Pages\ListQuoteWarehouses;
use App\Filament\Resources\QuoteWarehouses\Schemas\QuoteWarehouseForm;
use App\Filament\Resources\QuoteWarehouses\Tables\QuoteWarehousesTable;
use App\Models\QuoteWarehouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuoteWarehouseResource extends Resource
{
    protected static ?string $model = QuoteWarehouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'quote_id';

    public static function form(Schema $schema): Schema
    {
        return QuoteWarehouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuoteWarehousesTable::configure($table);
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
            'index' => ListQuoteWarehouses::route('/'),
            'create' => CreateQuoteWarehouse::route('/create'),
            'edit' => EditQuoteWarehouse::route('/{record}/edit'),
        ];
    }
}
