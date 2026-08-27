<?php

use App\Models\Mois;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Mois::whereIn('nom', ['juillet', 'aout'])->delete();

        $ordre = [
            'septembre' => 1,
            'octobre' => 2,
            'novembre' => 3,
            'decembre' => 4,
            'janvier' => 5,
            'fevrier' => 6,
            'mars' => 7,
            'avril' => 8,
            'mai' => 9,
            'juin' => 10,
        ];

        foreach ($ordre as $nom => $o) {
            Mois::where('nom', $nom)->update(['ordre' => $o]);
        }
    }

    public function down(): void
    {
        $mois = [
            ['janvier', 1], ['fevrier', 2], ['mars', 3], ['avril', 4],
            ['mai', 5], ['juin', 6], ['juillet', 7], ['aout', 8],
            ['septembre', 9], ['octobre', 10], ['novembre', 11], ['decembre', 12],
        ];

        foreach ($mois as [$nom, $ordre]) {
            Mois::updateOrCreate(['nom' => $nom], ['ordre' => $ordre]);
        }
    }
};