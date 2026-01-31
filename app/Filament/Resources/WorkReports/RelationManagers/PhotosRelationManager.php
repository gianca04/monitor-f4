<?php

namespace App\Filament\Resources\WorkReports\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    FileUpload::make('before_work_photo_path')
                        ->label('Evidencia Inicial')
                        ->image()
                        ->downloadable()
                        ->directory('work-reports/photos')
                        ->visibility('public')
                        ->acceptedFileTypes(types: ['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(25600) // 25MB
                        ->extraInputAttributes(['capture' => 'user'])
                        ->columnSpanFull()
                        ->helperText('Formatos soportados: JPEG, PNG, WebP. Se convertirá automáticamente a WebP. Tamaño máximo: 25MB.'),

                    Textarea::make('before_work_descripcion')
                        ->label('Descripción de la evidencia inicial')
                        ->maxLength(500)
                        ->placeholder('Describe brevemente lo que se muestra...')
                        ->helperText('Máximo 500 caracteres')

                ])->from('md')
                    ->columnSpanFull()
                    ->columns(2),

                Flex::make([

                    FileUpload::make('photo_path')
                        ->label('Evidencia del Trabajo Realizado')
                        ->image()

                        ->downloadable()
                        ->directory('work-reports/photos')
                        ->visibility('public')
                        ->acceptedFileTypes(types: ['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(25600) // 25MB
                        ->extraInputAttributes(['capture' => 'user'])
                        ->helperText('Formatos soportados: JPEG, PNG, WebP. Se convertirá automáticamente a WebP. Tamaño máximo: 25MB.'),

                    Textarea::make('descripcion')
                        ->label('Descripción de la evidencia del trabajo realizado')
                        ->maxLength(500)
                        ->placeholder('Describe brevemente lo que se muestra...')
                        ->helperText('Máximo 500 caracteres'),
                ])->from('md')
                    ->columnSpanFull()
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('work_report_id')
            ->columns([
                TextColumn::make('photo_path')
                    ->searchable(),
                TextColumn::make('before_work_photo_path')
                    ->searchable(),
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
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->extraModalWindowAttributes(['x-init' => 'isOpen = true']),
                AssociateAction::make()
                    ->slideOver()
                    ->extraModalWindowAttributes(['x-init' => 'isOpen = true']),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->extraModalWindowAttributes(['x-init' => 'isOpen = true']),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
