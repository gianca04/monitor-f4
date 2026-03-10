<?php

namespace App\Filament\Resources\ProjectRequirements\Schemas;

use App\Models\ProjectRequirement;
use App\Models\Requirement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MorphToSelect;
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
                    ->hidden(), // Usually handled automatically in relations
                MorphToSelect::make('requirementable')
                    ->label('Origen del Requerimiento')
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
                    ->columnSpanFull()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $type = $get('requirementable_type');
                        $id = $get('requirementable_id');

                        if ($type && $id) {
                            if ($type === Requirement::class) {
                                $requirement = Requirement::find($id);
                                if ($requirement) {
                                    $set('unit_symbol', $requirement->unit->symbol ?? '$');
                                    $set('requirement_type', $requirement->requirementType->name ?? 'Suministro');
                                    $set('unit_of_measure', $requirement->unit->name ?? 'UND');
                                    // Set type based on requirementType
                                    $reqTypeName = strtolower($requirement->requirementType->name ?? '');
                                    if (str_contains($reqTypeName, 'material')) {
                                        $set('type', \App\Enums\RequirementType::MATERIAL);
                                    } elseif (str_contains($reqTypeName, 'consumible') || str_contains($reqTypeName, 'suministro')) {
                                        $set('type', \App\Enums\RequirementType::CONSUMIBLE);
                                    } elseif (str_contains($reqTypeName, 'herramienta')) {
                                        $set('type', \App\Enums\RequirementType::HERRAMIENTA);
                                    } elseif (str_contains($reqTypeName, 'equipo')) {
                                        $set('type', \App\Enums\RequirementType::EQUIPO);
                                    } else {
                                        $set('type', \App\Enums\RequirementType::MATERIAL); // default
                                    }
                                }
                            } elseif ($type === QuoteDetail::class) {
                                $quoteDetail = QuoteDetail::find($id);
                                if ($quoteDetail) {
                                    $set('unit_symbol', $quoteDetail->pricelist->unit->symbol ?? '$');
                                    $set('requirement_type', 'Suministro');
                                    $set('unit_of_measure', $quoteDetail->pricelist->unit->name ?? 'UND');
                                    $set('price_unit', $quoteDetail->unit_price); // Set price from QuoteDetail
                                    $set('quantity', $quoteDetail->quantity); // Set quantity from QuoteDetail
                                    $set('subtotal', round((float)$quoteDetail->quantity * (float)$quoteDetail->unit_price, 2)); // Calculate subtotal
                                    $set('type', \App\Enums\RequirementType::CONSUMIBLE); // Default for quotes
                                }
                            } elseif ($type === Tool::class) {
                                $tool = Tool::find($id);
                                if ($tool) {
                                    $set('unit_symbol', 'UND');
                                    $set('requirement_type', 'Herramienta');
                                    $set('unit_of_measure', 'UND');
                                    // Set type based on tool type
                                    if ($tool->type === \App\Enums\ToolType::HERRAMIENTA) {
                                        $set('type', \App\Enums\RequirementType::HERRAMIENTA);
                                    } elseif ($tool->type === \App\Enums\ToolType::EQUIPO) {
                                        $set('type', \App\Enums\RequirementType::EQUIPO);
                                    } else {
                                        $set('type', \App\Enums\RequirementType::HERRAMIENTA); // default
                                    }
                                }
                            }
                        }
                    }),

                Select::make('type')
                    ->label('Tipo')
                    ->options(RequirementType::class)
                    ->required()
                    ->default(RequirementType::MATERIAL),

                TextInput::make('requirement_type')
                    ->label('Tipo de Requisito')
                    ->readOnly()
                    ->dehydrated(false)
                    ->formatStateUsing(fn(?ProjectRequirement $record) => $record?->consumable_type_name),

                TextInput::make('unit_of_measure')
                    ->label('Unidad de Medida')
                    ->readOnly()
                    ->dehydrated(false)
                    ->formatStateUsing(fn(?ProjectRequirement $record) => $record?->unit_name),

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
