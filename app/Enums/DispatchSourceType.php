<?php

namespace App\Enums;

enum DispatchSourceType: string
{
    /**
     * Atención desde almacén de la empresa
     */
    case WAREHOUSE = 'warehouse';

    /**
     * Atención desde proveedor externo / tercero
     */
    case PROVIDER = 'provider';

    /**
     * Atención desde otro proyecto / existencia externa
     */
    case EXTERNAL = 'external';

    /**
     * Obtiene la etiqueta legible del tipo de fuente
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::WAREHOUSE => 'Almacén',
            self::PROVIDER => 'Proveedor',
            self::EXTERNAL => 'Externo',
        };
    }

    /**
     * Obtiene el color para UI
     */
    public function getColor(): string
    {
        return match ($this) {
            self::WAREHOUSE => 'blue',
            self::PROVIDER => 'amber',
            self::EXTERNAL => 'gray',
        };
    }
}
