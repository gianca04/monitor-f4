<?php

namespace App\Filament\Resources\ProjectRequirements\Schemas;

use App\Models\ProjectRequirement;
use App\Models\Requirement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MorphToSelect;
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
                            ->modifyOptionsQueryUsing(fn($query) => $query->with('unit', 'requirementType')),
                        MorphToSelect\Type::make(QuoteDetail::class)
                            ->label('Detalle de Cotización')
                            ->getOptionLabelFromRecordUsing(fn($record) => ($record->pricelist->sat_description ?? 'Sin descripción') . ($record->comment ? ' - ' . $record->comment : ''))
                            ->modifyOptionsQueryUsing(fn($query) => $query->with('pricelist.unit')),
                        MorphToSelect\Type::make(Tool::class)
                            ->label('Herramienta / Equipo')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? 'Sin nombre')
                            ->modifyOptionsQueryUsing(fn($query) => $query),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $type = $get('requirementable_type');
                        $id = $get('requirementable_id');

                        if ($type && $id) {
                            $service = app(\App\Services\ProjectRequirementService::class);
                            $data = $service->mapFromRequirementable($type, $id);
                            foreach ($data as $key => $value) {
                                $set($key, $value);
                            }
                        }
                    }),


                Section::make('Detalles y Clasificación')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('type')
                                    ->label('Categoría Operativa')
                                    ->options(RequirementType::class)
                                    ->required()
                                    ->prefixIcon('heroicon-o-tag')
                                    ->default(RequirementType::MATERIAL),

                                TextInput::make('requirement_type')
                                    ->label('Tipo de Ítem')
                                    ->readOnly()
                                    ->prefixIcon('heroicon-o-puzzle-piece')
                                    ->dehydrated(false),

                                TextInput::make('unit_of_measure')
                                    ->label('Medida')
                                    ->readOnly()
                                    ->prefixIcon('heroicon-o-scale')
                                    ->dehydrated(false),
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
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('subtotal', round((float)$get('quantity') * (float)$get('price_unit'), 2));
                                    }),

                                TextInput::make('price_unit')
                                    ->label('Costo Unitario')
                                    ->required()
                                    ->numeric()
                                    ->prefixIcon('heroicon-o-currency-dollar')
                                    ->prefix(fn(Get $get) => $get('unit_symbol') ?? 'S/')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('subtotal', round((float)$get('quantity') * (float)$get('price_unit'), 2));
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Monto Total')
                                    ->numeric()
                                    ->prefixIcon('heroicon-o-banknotes')
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
