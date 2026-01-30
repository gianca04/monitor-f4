<?php

namespace App\Filament\Resources\Quotes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('request_number')
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subClient.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quoteCategory.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('energy_sci_manager')
                    ->searchable(),
                TextColumn::make('ceco')
                    ->searchable(),
                TextColumn::make('status'),
                TextColumn::make('quote_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('execution_date')
                    ->date()
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
