<?php

namespace App\Filament\Resources\ProjectRequirements\Schemas;

use App\Models\ProjectRequirement;
use App\Models\Requirement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MorphToSelect;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use App\Models\Tool;
use App\Models\QuoteDetail;
use App\Enums\RequirementType;
use App\Enums\ToolType;
use App\Models\Unit;

class ProjectRequirementForm
{
    public static function schema($projectId = null): array
    {
        return [
            TextInput::make('project_id')
                ->required()
                ->numeric()
                ->hidden()
                ->default($projectId),

            TextInput::make('last_id')
                ->hidden()
                ->dehydrated(false),

            Grid::make(12)
                ->schema([
                    Group::make([
                        MorphToSelect::make('requirementable')
                            ->label('Referencia de Origen')
                            ->types([
                                MorphToSelect\Type::make(Requirement::class)
                                    ->label('Catálogo')
                                    ->titleAttribute('product_description')
                                    ->modifyOptionsQueryUsing(fn($query) => $query->with('unit', 'requirementType')),
                                MorphToSelect\Type::make(QuoteDetail::class)
                                    ->label('Cotización')
                                    ->getOptionLabelFromRecordUsing(fn($record) => ($record->pricelist->sat_description ?? 'Sin descripción') . ($record->comment ? ' - ' . $record->comment : ''))
                                    ->modifyOptionsQueryUsing(fn($query) => $query->with('pricelist.unit')),
                                MorphToSelect\Type::make(Tool::class)
                                    ->label('Herramienta/Equipo')
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? 'Sin nombre'),
                            ])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $type = $get('requirementable_type');
                                $id = $get('requirementable_id');
                                $lastId = $get('last_id');

                                // Solo actualizamos si la selección realmente cambió para proteger personalizaciones
                                if (!$id || $id === $lastId) return;

                                $set('last_id', $id);

                                if ($type === Requirement::class) {
                                    $requirement = Requirement::with(['unit', 'requirementType'])->find($id);
                                    if ($requirement) {
                                        $set('product_name', $requirement->product_description);
                                        $set('unit_symbol', $requirement->unit->symbol ?? 'S/');
                                        $set('requirement_type', $requirement->requirementType->name ?? 'Suministro');
                                        $set('unit_name', $requirement->unit->name ?? 'UND');

                                        // Mapeo de Enums optimizado
                                        $reqTypeName = strtolower($requirement->requirementType->name ?? '');
                                        $enumType = \App\Enums\RequirementType::MATERIAL;
                                        if (str_contains($reqTypeName, 'material')) $enumType = \App\Enums\RequirementType::MATERIAL;
                                        elseif (str_contains($reqTypeName, 'consumible') || str_contains($reqTypeName, 'suministro')) $enumType = \App\Enums\RequirementType::CONSUMIBLE;
                                        elseif (str_contains($reqTypeName, 'herramienta')) $enumType = \App\Enums\RequirementType::HERRAMIENTA;
                                        elseif (str_contains($reqTypeName, 'equipo')) $enumType = \App\Enums\RequirementType::EQUIPO;

                                        $set('type', $enumType);
                                    }
                                } elseif ($type === QuoteDetail::class) {
                                    $quoteDetail = QuoteDetail::with('pricelist.unit')->find($id);
                                    if ($quoteDetail) {
                                        $set('product_name', $quoteDetail->pricelist->sat_description);
                                        $set('unit_symbol', $quoteDetail->pricelist->unit->symbol ?? 'S/');
                                        $set('requirement_type', 'Suministro');
                                        $set('unit_name', $quoteDetail->pricelist->unit->name ?? 'UND');
                                        $set('price_unit', $quoteDetail->unit_price);
                                        $set('quantity', $quoteDetail->quantity);
                                        self::updateSubtotal($get, $set);
                                        $set('type', \App\Enums\RequirementType::CONSUMIBLE);
                                    }
                                } elseif ($type === Tool::class) {
                                    $tool = Tool::find($id);
                                    if ($tool) {
                                        $set('product_name', $tool->name);
                                        $set('unit_symbol', 'UND');
                                        $set('requirement_type', $tool->type->value ?? 'Herramienta');
                                        $set('unit_name', 'UND');
                                        $set('type', $tool->type === \App\Enums\ToolType::EQUIPO ? \App\Enums\RequirementType::EQUIPO : \App\Enums\RequirementType::HERRAMIENTA);
                                    }
                                }
                            }),

                        Group::make([
                            TextInput::make('product_name')
                                ->label('Nombre del Producto')
                                ->required()
                                ->columnSpanFull()
                                ->prefixIcon('heroicon-o-pencil-square'),

                            Grid::make(2)
                                ->schema([
                                    Select::make('type')
                                        ->label('Categoría')
                                        ->native(false)
                                        ->options(RequirementType::class)
                                        ->required()
                                        ->prefixIcon('heroicon-o-tag'),

                                    Select::make('unit_name')
                                        ->label('Unidad')
                                        ->required()
                                        ->searchable()
                                        ->options(Unit::all()->pluck('name', 'name'))
                                        ->prefixIcon('heroicon-o-scale'),
                                ]),

                            Textarea::make('comments')
                                ->label('Comentarios')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])->visible(fn(Get $get) => filled($get('requirementable_id'))),
                    ])->columnSpan(7),

                    Group::make([
                        Section::make('Cálculos')
                            ->compact()
                            ->schema([
                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateSubtotal($get, $set)),

                                TextInput::make('price_unit')
                                    ->label('Precio Unit.')
                                    ->required()
                                    ->numeric()
                                    ->prefix(fn(Get $get) => $get('unit_symbol') ?? 'S/')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateSubtotal($get, $set)),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->readOnly()
                                    ->extraInputAttributes(['class' => 'font-bold text-primary-600'])
                                    ->formatStateUsing(fn($state, Get $get) => round((float)$get('quantity') * (float)$get('price_unit'), 2))
                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                        if ($record) {
                                            $component->state(round((float)$record->quantity * (float)$record->price_unit, 2));
                                        }
                                    }),

                                Select::make('requirement_type')
                                    ->label('Tipo (Calc.)')
                                    ->searchable()
                                    ->preload()
                                    ->options(\App\Models\RequirementType::all()->pluck('name', 'name'))
                                    ->native(false)
                                    ->afterStateHydrated(function (Select $component, $state, $record) {
                                        if ($record && empty($state)) {
                                            $component->state($record->requirement_type_calculated);
                                        }
                                    }),
                            ]),
                    ])
                        ->columnSpan(5)
                        ->visible(fn(Get $get) => filled($get('requirementable_id'))),
                ]),

            TextInput::make('unit_symbol')
                ->hidden()
                ->dehydrated(true),
        ];
    }

    protected static function updateSubtotal(Get $get, Set $set)
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $price = (float) ($get('price_unit') ?? 0);
        $set('subtotal', round($quantity * $price, 2));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::schema(request()->route('record')));
    }
}
