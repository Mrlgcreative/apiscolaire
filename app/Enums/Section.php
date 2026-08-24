<?php

namespace App\Enums;

enum Section: string
{
    case Maternelle = 'maternelle';
    case Primaire = 'primaire';
    case Secondaire = 'secondaire';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
