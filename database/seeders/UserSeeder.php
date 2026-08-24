<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $csk = Institution::where('code', 'CSK')->first();
        $glg = Institution::where('code', 'GLG')->first();

        // Super-admin du groupe : institution_id null, voit tout.
        User::create([
            'username' => 'admin',
            'email' => 'admin@college.test',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);

        foreach ([['chris', 'directrice', $csk], ['ciella', 'admin', $csk], ['Daniel', 'comptable', $csk], ['rapha', 'prefet', $csk]] as [$username, $role, $institution]) {
            User::create([
                'username' => $username,
                'email' => strtolower($username).'@college.test',
                'password' => 'password',
                'role' => UserRole::from($role),
                'institution_id' => $institution->id,
            ]);
        }

        foreach ([['Divina', 'director', $glg], ['glg-admin', 'admin', $glg], ['glg-compta', 'comptable', $glg]] as [$username, $role, $institution]) {
            User::create([
                'username' => $username,
                'email' => strtolower($username).'@college.test',
                'password' => 'password',
                'role' => UserRole::from($role),
                'institution_id' => $institution->id,
            ]);
        }
    }
}
