<?php

namespace App\Filament\Resources\Pricelists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricelistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sat_line')
                    ->searchable()
                    ->sortable()
                    ->badge('badge badge-primary')
                    ->label('Línea SAP'),
                TextColumn::make('sat_description')
                    ->searchable()
                    ->sortable()
                    ->label('Descripción SAP')  // Label más corto
                    ->limit(50),
                TextColumn::make('priceType.name')
                    ->numeric()
                    ->grow(false)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tipo')
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->numeric()
                    ->label('Unidad')
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->numeric()
                    ->label('Precio U.')
                    ->prefix('S/ ')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalWidth(fn() => strpos(request()->userAgent(), 'Mobile') !== false ? 'screen' : '7xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
