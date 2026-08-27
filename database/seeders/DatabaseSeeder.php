<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InstitutionSeeder::class,
            ReferentielSeeder::class,
            UserSeeder::class,
            PersonnelSeeder::class,
            PeriodeSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
