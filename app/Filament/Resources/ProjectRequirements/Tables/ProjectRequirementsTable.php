<?php

namespace App\Filament\Resources\ProjectRequirements\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectRequirementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Producto / Descripción')
                    ->limit(50)
                    ->tooltip(fn($state): string => $state)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('consumable_type_name')
                    ->label('Tipo'),

                TextColumn::make('unit.name')
                    ->label('Unidad'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->label('Cantidad')
                    ->sortable(),
                TextColumn::make('price_unit')
                    ->numeric()
                    ->prefix('S/')
                    ->label('Precio Unitario')
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->prefix('S/')
                    ->label('Subtotal')
                    ->sortable(),
                TextColumn::make('comments')
                    ->label('Comentarios')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn($state): string => $state),
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
                ActionGroup::make(
                    [
                        ViewAction::make()->slideOver(),
                        EditAction::make()->slideOver(),
                        DeleteAction::make()->requiresConfirmation(),
                    ]
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
