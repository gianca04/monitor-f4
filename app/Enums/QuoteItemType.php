<?php

namespace App\Enums;

enum QuoteItemType: string
{
    case SERVICIO = 'SERVICIO';
    case VIATICOS = 'VIATICOS';
    case SUMINISTRO = 'SUMINISTRO';
    case MANO_DE_OBRA = 'MANO DE OBRA';
    case CONSUMIBLE = 'CONSUMIBLE';
    case TRANSPORTE = 'TRANSPORTE';
    case OTROS = 'OTROS';

    public function label(): string
    {
        return match ($this) {
            self::SERVICIO => 'Servicio',
            self::VIATICOS => 'Viáticos',
            self::SUMINISTRO => 'Suministro',
            self::MANO_DE_OBRA => 'Mano de Obra',
            self::CONSUMIBLE => 'Consumible',
            self::TRANSPORTE => 'Transporte',
            self::OTROS => 'Otros',
        };
    }
}
