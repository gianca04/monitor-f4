<?php

namespace App\Filament\Resources\WorkReports\Tables;

use App\Filament\Resources\WorkReports\RelationManagers\PhotosRelationManager;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;

class WorkReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compliance_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('report_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                // Relación de Fotos
                RelationManagerAction::make('photos-relation-manager')
                    ->label('Ver fotografías')
                    ->icon('heroicon-o-photo') // Icono más descriptivo
                    ->slideOver(true)
                    ->relationManager(PhotosRelationManager::make()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
