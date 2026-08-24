<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        Institution::create([
            'nom' => 'Complexe Scolaire Kolwezi',
            'code' => 'CSK',
            'type' => 'complexe scolaire',
            'adresse' => 'Kolwezi.manika, Moïse Tshombe, mbembe,48',
            'telephone' => '+243 97 90 99 01',
            'email' => 'csk@college.test',
        ]);

        Institution::create([
            'nom' => 'Groupe Scolaire La Grâce',
            'code' => 'GLG',
            'type' => 'groupe scolaire',
            'adresse' => 'Avenue de la Paix 12, Kolwezi',
            'telephone' => '+243 99 12 34 56',
            'email' => 'glg@college.test',
        ]);
    }
}
