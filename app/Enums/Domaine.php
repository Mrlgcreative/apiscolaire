<?php

namespace App\Enums;

enum Domaine: string
{
    case Langues = 'langues';
    case Sciences = 'sciences';
    case UniversSocialEtEnvironnement = 'univers_social_et_environnement';
    case Arts = 'arts';
    case DeveloppementPersonnel = 'developpement_personnel';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Langues => 'DOMAINE DES LANGUES',
            self::Sciences => 'DOMAINE DES SCIENCES',
            self::UniversSocialEtEnvironnement => "DOMAINE DE L'UNIVERS SOCIAL ET DE L'ENVIRONNEMENT",
            self::Arts => 'DOMAINE DES ARTS',
            self::DeveloppementPersonnel => 'DOMAINE DU DÉVELOPPEMENT PERSONNEL',
        };
    }
}
