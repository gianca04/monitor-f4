<?php

namespace App\Filament\Resources\Requirements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\RequirementType;

class RequirementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_description')
                    ->label('Descripción del Producto')
                    ->placeholder('Ej: Cemento Portland Tipo I')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('requirement_type_id')
                    ->label('Tipo de Requerimiento')
                    ->relationship('requirementType', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre del Tipo')
                            ->placeholder('Ej: Material, Consumible, EPP')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_reusable')
                            ->label('¿Es reutilizable?')
                            ->helperText('Ej: Herramientas no se descuentan del stock')
                            ->default(false),
                    ])
                    ->createOptionUsing(fn(array $data) => RequirementType::create($data)->id),
                Select::make('unit_id')
                    ->label('Unidad de Medida')
                    ->relationship('unit', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),
            ]);
    }
}
