<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\ProjectRequirements\Schemas\ProjectRequirementForm;
use Illuminate\Support\Str;

class DispatchGuideRelationManager extends RelationManager
{
    protected static string $relationship = 'dispatchGuides';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('General Information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('name'),
                                        TextInput::make('tracking_number'),
                                        DatePicker::make('required_shipping_date'),
                                    ]),
                            ]),
                        Tab::make('Requirement Items')
                            ->icon('heroicon-m-list-bullet')
                            ->schema([
                                Repeater::make('projectRequirements')
                                    ->hiddenLabel()
                                    ->relationship('projectRequirements')
                                    ->schema(ProjectRequirementForm::schema($this->getOwnerRecord()->id))
                                    ->itemLabel(function (array $state): ?string {
                                        $product = $state['product_name'] ?? 'Nuevo Ítem';
                                        $unit = $state['unit_name'] ?? '';
                                        $quantity = $state['quantity'] ?? '';


                                        // Formateamos: "Nombre del Producto - UND"
                                        $label = $unit ? "{$quantity} {$unit} - {$product}" : $product;

                                        // Limitamos a 50 caracteres para mantener la estética
                                        return Str::limit($label, 50, '...');
                                    })
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->collapsed()
                                    ->cloneable()
                                    ->columnSpanFull()
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        $data['project_id'] = $this->getOwnerRecord()->id;
                                        return $data;
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                        $data['project_id'] = $this->getOwnerRecord()->id;
                                        return $data;
                                    })
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('tracking_number')
                    ->searchable(),
                TextColumn::make('required_shipping_date')
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
            ->headerActions([
                CreateAction::make()
                    ->slideOver(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
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
