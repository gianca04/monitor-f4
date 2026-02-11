<?php

namespace App\Filament\Resources\Requirements\Schemas;

use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RequirementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_description')
                    ->label('Descripción del Producto')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
                Select::make('requirement_type_id')
                    ->label('Tipo de Requisito')
                    ->relationship('requirementType', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                    ]),
                Select::make('unit_id')
                    ->label('Unidad de Medida')
                    ->relationship('unit', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('symbol')
                            ->label('Símbolo'),
                        TextInput::make('category')
                            ->label('Categoría'),
                    ]),
            ]);
    }
}
