<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RequirementType: string implements HasLabel
{
    case MATERIAL = 'Material';
    case CONSUMIBLE = 'Consumible';
    case HERRAMIENTA = 'Herramienta';
    case EQUIPO = 'Equipo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MATERIAL => 'Material',
            self::CONSUMIBLE => 'Consumible',
            self::HERRAMIENTA => 'Herramienta',
            self::EQUIPO => 'Equipo',
        };
    }
}