<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class   ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->alignCenter(),

                TextColumn::make('business_name')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office-2')
                    ->weight('bold'),
                TextColumn::make('document_type')
                    ->badge()
                    ->label('Tipo Doc.')
                    ->colors([
                        'primary' => 'RUC',
                        'success' => 'DNI',
                        'warning' => 'CARNET DE EXTRANJERIA',
                        'info' => 'PASAPORTE',
                    ])
                    ->sortable(),
                TextColumn::make('document_number')
                    ->label('N° Documento')
                    ->sortable()
                    ->icon('heroicon-o-hashtag'),
                TextColumn::make('person_type')
                    ->badge()
                    ->label('Tipo Persona')
                    ->colors([
                        'primary' => 'Natural Person',
                        'secondary' => 'Legal Entity',
                    ])
                    ->icons([
                        'heroicon-o-user' => 'Natural Person',
                        'heroicon-o-user-group' => 'Legal Entity',
                    ])
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->address)
                    ->icon('heroicon-o-map-pin'),
                TextColumn::make('contact_phone')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone'),
                TextColumn::make('contact_email')
                    ->label('Correo')
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('success')
                    ->icon('heroicon-o-calendar-days')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('document_type')
                    ->label('Tipo de documento')
                    ->options([
                        'RUC' => 'RUC',
                        'DNI' => 'DNI',
                        'CARNET DE EXTRANJERIA' => 'Carné de Extranjería',
                        'PASAPORTE' => 'Pasaporte',
                    ]),
                SelectFilter::make('person_type')
                    ->label('Tipo de persona')
                    ->options([
                        'Natural Person' => 'Persona Natural',
                        'Legal Entity' => 'Persona Jurídica',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary'),
                    DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
