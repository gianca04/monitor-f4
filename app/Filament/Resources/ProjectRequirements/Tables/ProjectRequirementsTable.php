<?php

namespace App\Filament\Resources\ProjectRequirements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectRequirementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('product_name')
                    ->label('Producto / Descripción')
                    ->limit(50)
                    ->tooltip(fn($state): string => $state)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph(
                            'requirementable',
                            [\App\Models\Requirement::class, \App\Models\QuoteDetail::class, \App\Models\ToolUnit::class],
                            function (Builder $query, string $type) use ($search) {
                                if ($type === \App\Models\Requirement::class) {
                                    $query->where('product_description', 'like', "%{$search}%");
                                } elseif ($type === \App\Models\QuoteDetail::class) {
                                    $query->whereHas('pricelist', function ($q) use ($search) {
                                        $q->where('sat_description', 'like', "%{$search}%");
                                    });
                                } elseif ($type === \App\Models\ToolUnit::class) {
                                    $query->whereHas('tool', function ($q) use ($search) {
                                        $q->where('name', 'like', "%{$search}%")
                                            ->orWhere('internal_code', 'like', "%{$search}%");
                                    });
                                }
                            }
                        );
                    })
                    ->sortable(),

                TextColumn::make('consumable_type_name')
                    ->label('Tipo'),

                TextColumn::make('unit_name')
                    ->label('Unidad'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->label('Cantidad')
                    ->sortable(),
                TextColumn::make('price_unit')
                    ->numeric()
                    ->label('Precio Unitario')
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->label('Subtotal')
                    ->sortable(),
                TextColumn::make('comments')
                    ->label('Comentarios')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Fecha de Actualización')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
