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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
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
                TextInput::make('photo_path')
                    ->default(null),
                TextInput::make('before_work_photo_path')
                    ->default(null),
                Textarea::make('descripcion')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('before_work_descripcion')
                    ->default(null)
                    ->columnSpanFull(),
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
