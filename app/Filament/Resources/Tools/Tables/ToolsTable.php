<?php

namespace App\Filament\Resources\Tools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ToolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-qr-code')
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(40),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('brand.name')
                    ->label('Marca')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('serial_number')
                    ->label('N° Serie')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('certification_expiry')
                    ->label('Vence Certificación')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($state) => match (true) {
                        $state === null => 'gray',
                        $state < now() => 'danger',
                        $state < now()->addDays(30) => 'warning',
                        default => 'success',
                    })
                    ->icon(fn($state) => match (true) {
                        $state === null => 'heroicon-m-minus-circle',
                        $state < now() => 'heroicon-m-exclamation-triangle',
                        $state < now()->addDays(30) => 'heroicon-m-clock',
                        default => 'heroicon-m-check-circle',
                    })
                    ->description(fn($state) => match (true) {
                        $state === null => 'Sin certificación',
                        $state < now() => '⚠️ Vencido',
                        $state < now()->addDays(30) => 'Por vencer',
                        default => 'Vigente',
                    }),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Disponible' => 'success',
                        'En Uso' => 'info',
                        'En Mantenimiento' => 'warning',
                        'Dañado' => 'danger',
                        'Baja' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Disponible' => 'heroicon-m-check-circle',
                        'En Uso' => 'heroicon-m-wrench-screwdriver',
                        'En Mantenimiento' => 'heroicon-m-cog-6-tooth',
                        'Dañado' => 'heroicon-m-x-circle',
                        'Baja' => 'heroicon-m-archive-box-x-mark',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'Disponible' => 'Disponible',
                        'En Uso' => 'En Uso',
                        'En Mantenimiento' => 'En Mantenimiento',
                        'Dañado' => 'Dañado',
                        'Baja' => 'Dado de Baja',
                    ]),

                SelectFilter::make('tool_category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tool_brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
