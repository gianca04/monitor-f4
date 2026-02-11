<?php

namespace App\Filament\Resources\ProjectRequirements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProjectRequirementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('project_id')
                    ->required()
                    ->numeric()
                    ->hidden(), // Usually handled automatically in relations
                Select::make('requirement_id')
                    ->label('Requerimiento')
                    ->relationship('requirement', 'product_description')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $requirement = \App\Models\Requirement::find($state);
                            if ($requirement && $requirement->unit) {
                                $set('unit_symbol', $requirement->unit->symbol ?? '$');
                            }
                        }
                    })
                    ->createOptionForm([
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
                    ]),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('subtotal', round((float)$get('quantity') * (float)$get('price_unit'), 2));
                    }),
                TextInput::make('price_unit')
                    ->label('Precio Unitario')
                    ->required()
                    ->numeric()
                    ->prefix(fn(Get $get) => $get('unit_symbol') ?? 'S/')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('subtotal', round((float)$get('quantity') * (float)$get('price_unit'), 2));
                    }),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->prefix('S/')
                    ->readOnly()
                    ->dehydrated(false)
                    ->formatStateUsing(fn($state, Get $get) => round((float)$get('quantity') * (float)$get('price_unit'), 2)),

                // Campo oculto para almacenar el símbolo de la unidad
                TextInput::make('unit_symbol')
                    ->hidden()
                    ->dehydrated(false),
                Textarea::make('comments')
                    ->label('Comentarios')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
