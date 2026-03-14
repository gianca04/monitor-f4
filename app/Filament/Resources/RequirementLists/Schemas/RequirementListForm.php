<?php

namespace App\Filament\Resources\RequirementLists\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RequirementListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                TextInput::make('name'),
                TextInput::make('tracking_number'),
                DatePicker::make('required_shipping_date'),
            ]);
    }
}
