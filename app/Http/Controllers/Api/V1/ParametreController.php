<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use App\Support\CurrentInstitution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    /** Clés connues et valeurs par défaut (niveau groupe). */
    private const DEFAUTS = [
        'bulletin.afficher_drapeau' => true,
        'bulletin.afficher_embleme' => true,
        'bulletin.ministere' => 'MINISTERE DE L\'EDUCATION NATIONALE ET NOUVELLE CITOYENNETE',
        'bulletin.annee_scolaire' => '2024 - 2025',
    ];

    public function index(Request $request): JsonResponse
    {
        $scope = app(CurrentInstitution::class)->id;

        $rows = Parametre::where('institution_id', $scope)->pluck('valeur', 'cle');

        $data = [];
        foreach (self::DEFAUTS as $cle => $defaut) {
            $data[$cle] = $rows->has($cle) ? $rows[$cle] : $defaut;
        }

        return response()->json(['data' => $data]);
    }

    public function update(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Accès réservé aux administrateurs.');

        $validated = $request->validate([
            'parametres' => ['required', 'array'],
            'parametres.*.cle' => ['required', 'string', 'in:'.implode(',', array_keys(self::DEFAUTS))],
            'parametres.*.valeur' => ['nullable'],
        ]);

        $scope = app(CurrentInstitution::class)->id;

        foreach ($validated['parametres'] as $p) {
            Parametre::updateOrCreate(
                ['institution_id' => $scope, 'cle' => $p['cle']],
                ['valeur' => $p['valeur']],
            );
        }

        return response()->json(['message' => 'Paramètres enregistrés.', 'data' => $this->resolved($scope)]);
    }

    /** Reconstruit le même objet que index() pour le scope donné. */
    private function resolved(?string $scope): array
    {
        $rows = Parametre::where('institution_id', $scope)->pluck('valeur', 'cle');

        $data = [];
        foreach (self::DEFAUTS as $cle => $defaut) {
            $data[$cle] = $rows->has($cle) ? $rows[$cle] : $defaut;
        }

        return $data;
    }
}
