<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Director = 'director';
    case Directrice = 'directrice';
    case Prefet = 'prefet';
    case Comptable = 'comptable';
    case Professeur = 'professeur';
    case Parent = 'parent';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
