<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-identification'),

                TextColumn::make('last_name')
                    ->label('Apellidos')
                    ->sortable()
                    ->icon('heroicon-o-identification')
                    ->searchable(),

                TextColumn::make('document_number')
                    ->label('N° Documento')
                    ->searchable()
                    ->icon('heroicon-o-hashtag'),

                TextColumn::make('document_type')
                    ->colors([
                        'success' => 'DNI',
                        'warning' => 'CARNET DE EXTRANJERIA',
                        'info' => 'PASAPORTE',
                    ])
                    ->searchable()
                    ->badge()
                    ->label('Tipo de Doc'),

                TextColumn::make('position.name')
                    ->searchable()
                    ->label('Profesión')
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('date_birth')
                    ->label('Fecha de Nacimiento')
                    ->date()
                    ->icon('heroicon-o-cake')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('date_contract')
                    ->label('Fecha de Contrato')
                    ->date()
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address')
                    ->tooltip(fn($record) => $record->address)
                    ->label('Dirección')
                    ->icon('heroicon-o-map-pin')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('user.email')
                    ->icon('heroicon-o-envelope')
                    ->label('Nombre de ususario')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions(
                [
                    //ExportAction::make()
                    //    ->exporter(EmployeeExporter::class)
                ]
            )

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
