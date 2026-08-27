<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\Periode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'eleve_id' => ['required', 'uuid', 'exists:eleves,id'],
            'periode_id' => ['required', 'uuid', 'exists:periodes,id'],
        ]);

        $eleve = Eleve::with(['classe', 'institution'])->findOrFail($request->eleve_id);
        $periode = Periode::with(['enfants', 'parent'])->findOrFail($request->periode_id);

        // Parent ne voit que ses enfants
        $user = $request->user();
        if ($user->isParent() && ! $user->enfants()->where('eleves.id', $eleve->id)->exists()) {
            abort(403, 'Accès refusé.');
        }

        // Résoudre les périodes feuilles (1P,2P...) selon semestre/trimestre demandé
        $periodesFeuilles = $this->resolveFeuilles($periode);

        // Tous les cours de la classe de l'élève (ou tous les cours de l'école si pas de classe)
        $coursList = Cours::where('institution_id', $eleve->institution_id)
            ->when($eleve->classe_id, fn ($q) => $q->where('classe_id', $eleve->classe_id))
            ->orderBy('titre')
            ->get();

        // Notes de l'élève pour ces périodes
        $notes = Note::where('eleve_id', $eleve->id)
            ->whereIn('periode_id', $periodesFeuilles->pluck('id'))
            ->get()
            ->keyBy(fn ($n) => $n->cours_id.'_'.$n->periode_id);

        $lignes = [];
        $totalGeneral = 0;
        $maxGeneral = 0;

        foreach ($coursList as $cours) {
            $notesCours = [];
            $total = 0;
            $maxTotal = 0;
            foreach ($periodesFeuilles as $p) {
                $key = $cours->id.'_'.$p->id;
                $n = $notes->get($key);
                $val = $n ? (float) $n->note : null;
                $max = $n ? (float) $n->max : 100;
                $notesCours[] = [
                    'periode_id' => $p->id,
                    'periode_code' => $p->code,
                    'note' => $val,
                    'max' => $max,
                ];
                if ($val !== null) {
                    $total += $val;
                    $maxTotal += $max;
                }
            }
            $lignes[] = [
                'cours' => ['id' => $cours->id, 'titre' => $cours->titre, 'coefficient' => $cours->coefficient],
                'notes' => $notesCours,
                'total' => round($total, 2),
                'maxTotal' => $maxTotal,
                'moyenne' => $maxTotal > 0 ? round($total / $maxTotal * 100, 2) : null,
            ];
            $totalGeneral += $total;
            $maxGeneral += $maxTotal;
        }

        $pourcentage = $maxGeneral > 0 ? round($totalGeneral / $maxGeneral * 100, 2) : 0;

        // Rang dans la classe pour cette période (moyenne pondérée)
        $rang = null;
        $totalEleves = null;
        if ($eleve->classe_id) {
            $elevesClasse = Eleve::where('classe_id', $eleve->classe_id)->where('statut', 'actif')->get();
            $totalEleves = $elevesClasse->count();
            $moyennes = [];
            foreach ($elevesClasse as $e) {
                $ns = Note::where('eleve_id', $e->id)->whereIn('periode_id', $periodesFeuilles->pluck('id'))->get();
                $t = $ns->sum(fn ($n) => (float) $n->note);
                $m = $ns->sum(fn ($n) => (float) $n->max);
                $moyennes[$e->id] = $m > 0 ? $t / $m * 100 : 0;
            }
            arsort($moyennes);
            $rang = array_search($eleve->id, array_keys($moyennes)) + 1;
        }

        return response()->json([
            'data' => [
                'eleve' => $eleve,
                'periode' => $periode,
                'periodes_feuilles' => $periodesFeuilles,
                'lignes' => $lignes,
                'totalGeneral' => round($totalGeneral, 2),
                'maxGeneral' => $maxGeneral,
                'pourcentage' => $pourcentage,
                'rang' => $rang,
                'totalEleves' => $totalEleves,
            ],
        ]);
    }

    private function resolveFeuilles(Periode $periode)
    {
        // Si la période a des enfants (semestre/trimestre), on prend les feuilles (1P-4P)
        if ($periode->enfants()->exists()) {
            return $periode->enfants()->orderBy('ordre')->get();
        }

        // Sinon c'est déjà une feuille
        return collect([$periode]);
    }
}
