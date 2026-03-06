<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuoteType: string implements HasLabel
{
    case PREVENTIVO = 'Preventivo';
    case CORRECTIVO = 'Correctivo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PREVENTIVO => 'Preventivo',
            self::CORRECTIVO => 'Correctivo',
        };
    }
}
