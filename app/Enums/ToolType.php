<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ToolType: string implements HasLabel
{
    case HERRAMIENTA = 'Herramienta';
    case EQUIPO = 'Equipo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HERRAMIENTA => 'Herramienta',
            self::EQUIPO => 'Equipo',
        };
    }
}
