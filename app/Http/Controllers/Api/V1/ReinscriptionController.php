<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EleveResource;
use App\Models\Eleve;
use App\Services\ReinscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReinscriptionController extends Controller
{
    public function __construct(private readonly ReinscriptionService $reinscription)
    {
    }

    public function single(Eleve $eleve): JsonResponse
    {
        $result = $this->reinscription->reinscrire($eleve);

        return (new EleveResource($result['eleve']))
            ->additional([
                'reinscription' => [
                    'promu' => $result['promu'],
                    'pourcentage' => $result['pourcentage'],
                    'classe' => $result['classe_base'],
                ],
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function enMasse(Request $request): JsonResponse
    {
        $request->validate([
            'eleve_ids' => ['required', 'array', 'min:1', 'max:200'],
            'eleve_ids.*' => ['uuid', 'exists:eleves,id'],
        ]);

        $resultats = [];
        $resume = [
            'reinscrits' => 0,
            'promus' => 0,
            'redoublements' => 0,
            'non_reinscrits' => 0,
        ];

        foreach ($request->eleve_ids as $id) {
            try {
                $r = $this->reinscription->reinscrire(Eleve::findOrFail($id));

                $resultats[] = [
                    'eleve_id' => $id,
                    'matricule' => $r['eleve']->matricule,
                    'ok' => true,
                    'promu' => $r['promu'],
                    'pourcentage' => $r['pourcentage'],
                    'classe' => $r['classe_base'],
                    'message' => $r['promu']
                        ? 'Réinscrit, promu en '.$r['classe_base']
                        : 'Réinscrit, redouble ('.$r['classe_base'].')',
                ];

                $resume['reinscrits']++;
                $r['promu'] ? $resume['promus']++ : $resume['redoublements']++;
            } catch (ValidationException $e) {
                $message = collect($e->errors())->flatten()->first();

                $resultats[] = [
                    'eleve_id' => $id,
                    'ok' => false,
                    'message' => $message,
                ];
                $resume['non_reinscrits']++;
            }
        }

        return response()->json([
            'data' => [
                'resultats' => $resultats,
                'resume' => $resume,
            ],
        ]);
    }
}