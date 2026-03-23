<?php

namespace App\Filament\Resources\ProjectRequirements\Schemas;

use App\Models\ProjectRequirement;
use App\Models\Requirement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use App\Models\ToolUnit;
use App\Models\Tool;
use App\Models\QuoteDetail;
use App\Enums\RequirementType;
use App\Enums\ToolType;
use App\Models\RequirementType as ModelsRequirementType;
use App\Models\Unit;

class ProjectRequirementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('project_id')
                    ->required()
                    ->numeric()
                    ->hidden(),

                MorphToSelect::make('requirementable')
                    ->label('Referencia')
                    ->native(false)
                    ->types([
                        MorphToSelect\Type::make(Requirement::class)
                            ->label('Catálogo de Requerimientos')
                            ->titleAttribute('product_description')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->title)
                            ->modifyOptionsQueryUsing(fn($query) => $query->with('unit', 'requirementType')),
                        MorphToSelect\Type::make(QuoteDetail::class)
                            ->label('Detalle de Cotización')
                            ->titleAttribute('name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->title)
                            ->modifyOptionsQueryUsing(fn($query) => $query->with('pricelist.unit')),
                        MorphToSelect\Type::make(Tool::class)
                            ->label('Herramienta / Equipo')
                            ->titleAttribute('name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->title)
                            ->modifyOptionsQueryUsing(fn($query) => $query),
                    ])
                    ->preload()
                    ->required()
                    ->searchable()
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $type = $get('requirementable_type');
                        $id = $get('requirementable_id');

                        if (blank($type) || blank($id)) {
                            return;
                        }

                        $currentRef = $type . '-' . $id;
                        if ($get('_last_requirementable') === $currentRef) {
                            return; // Ya se mapearon los valores para este requerimiento, evitamos resets continuos
                        }

                        $service = app(\App\Services\ProjectRequirementService::class);
                        $mapped = $service->mapFromRequirementable($type, (int) $id);

                        foreach (['name', 'type', 'unit_id', 'requirement_type', 'price_unit', 'quantity', 'subtotal', 'unit_symbol'] as $field) {
                            if (array_key_exists($field, $mapped)) {
                                $set($field, $mapped[$field]);
                            }
                        }

                        $set('_last_requirementable', $currentRef);
                    }),


                Section::make('Detalles y Clasificación')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Requerimiento')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        Grid::make(3)
                            ->schema([
                                Select::make('type')
                                    ->label('Categoría Operativa')
                                    ->options(RequirementType::class)
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-tag'),
                                Select::make('requirement_type')
                                    ->label('Tipo de Ítem')
                                    ->options(ModelsRequirementType::pluck('name', 'id'))
                                    ->native(false)

                                    ->prefixIcon('heroicon-o-puzzle-piece')
                                    ->dehydrated(false),

                                Select::make('unit_id')
                                    ->label('Medida')
                                    ->options(Unit::pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-scale'),
                            ]),
                    ]),

                Section::make('Cantidades y Costos')
                    ->icon('heroicon-o-calculator')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('quantity')
                                    ->label('Cantidad Solicitada')
                                    ->required()
                                    ->numeric()
                                    ->prefix(fn(Get $get) => $get('unit_symbol'))
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('subtotal', round((float)$get('quantity') * (float)$get('price_unit'), 2));
                                    }),

                                TextInput::make('price_unit')
                                    ->label('Costo Unitario')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S/')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('subtotal', round((float)$get('quantity') * (float)$get('price_unit'), 2));
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Monto Total')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->readOnly()
                                    ->extraInputAttributes(['class' => 'font-bold text-primary-600'])
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn($state, Get $get) => round((float)$get('quantity') * (float)$get('price_unit'), 2)),
                            ]),
                    ]),

                TextInput::make('unit_symbol')
                    ->hidden()
                    ->dehydrated(false),

                Hidden::make('_last_requirementable')
                    ->dehydrated(false),

                Section::make('Adicionales')
                    ->schema([
                        Textarea::make('comments')
                            ->label('Notas / Observaciones')
                            ->placeholder('Escriba aquí cualquier detalle relevante...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->collapsible()
                    ->columnSpanFull(),

            ]);
    }
}
