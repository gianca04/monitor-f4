<?php

namespace App\Filament\Resources\Pricelists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;

class PricelistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    TextInput::make('sat_line')
                        ->label('Línea SAT')
                        ->required(),
                    Select::make('unit_id')
                        ->label('Unidad')
                        ->relationship('unit', 'name')
                        ->required(),
                    Select::make('price_type_id')
                        ->label('Tipo de Precio')
                        ->relationship('priceType', 'name')
                        ->default(null),
                    TextInput::make('unit_price')
                        ->label('Precio Unitario')
                        ->required()
                        ->numeric(),
                ]),
                Textarea::make('sat_description')
                    ->label('Descripción SAT')
                    ->required()
                    ->columnSpan(4),
            ]);
    }
}
