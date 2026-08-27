<?php

namespace App\Services;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\Periode;
use App\Models\SessionScolaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Réinscription d'un élève pour la nouvelle année scolaire :
 * même matricule conservé, création d'un nouveau dossier lié à la session
 * active et promotion automatique en classe supérieure si la moyenne
 * annuelle atteint le seuil de passage.
 */
class ReinscriptionService
{
    /** Moyenne annuelle (sur 100) requise pour passer en classe supérieure. */
    public const SEUIL_PROMOTION = 50;

    /**
     * @return array{eleve: Eleve, promu: bool, pourcentage: ?float, classe_base: ?string}
     */
    public function reinscrire(Eleve $eleve): array
    {
        if ($eleve->statut !== 'actif') {
            throw ValidationException::withMessages([
                'eleve' => ["L'élève {$eleve->matricule} n'est plus actif."],
            ]);
        }

        $sessionActive = SessionScolaire::active()->first();
        if ($sessionActive === null) {
            throw ValidationException::withMessages([
                'eleve' => ['Aucune session scolaire active.'],
            ]);
        }

        if ($eleve->session_scolaire_id === $sessionActive->id) {
            throw ValidationException::withMessages([
                'eleve' => ['Cet élève est déjà inscrit pour l\'année en cours.'],
            ]);
        }

        $existant = Eleve::query()
            ->where('matricule', $eleve->matricule)
            ->where('session_scolaire_id', $sessionActive->id)
            ->first();
        if ($existant !== null) {
            throw ValidationException::withMessages([
                'eleve' => ["Déjà réinscrit cette année (matricule {$eleve->matricule})."],
            ]);
        }

        $pourcentage = $this->pourcentageAnnee($eleve);
        $promu = $pourcentage !== null && $pourcentage >= self::SEUIL_PROMOTION;
        $classeCible = $this->classeCible($eleve, $promu);

        $nouveau = DB::transaction(function () use ($eleve, $sessionActive, $classeCible) {
            $eleve->update(['statut' => 'reinscrit']);

            return Eleve::create([
                'matricule' => $eleve->matricule,
                'nom' => $eleve->nom,
                'post_nom' => $eleve->post_nom,
                'prenom' => $eleve->prenom,
                'date_naissance' => $eleve->date_naissance,
                'sexe' => $eleve->sexe,
                'lieu_naissance' => $eleve->lieu_naissance,
                'adresse' => $eleve->adresse,
                'section' => $eleve->section->value,
                'option_id' => $eleve->option_id,
                'classe_id' => $classeCible?->id,
                'session_scolaire_id' => $sessionActive->id,
                'nom_pere' => $eleve->nom_pere,
                'nom_mere' => $eleve->nom_mere,
                'contact_pere' => $eleve->contact_pere,
                'contact_mere' => $eleve->contact_mere,
                'photo' => $eleve->photo,
                'est_reinscrit' => true,
                'institution_id' => $eleve->institution_id,
            ]);
        });

        $nouveau->load(['classe', 'option', 'sessionScolaire']);

        return [
            'eleve' => $nouveau,
            'promu' => $promu,
            'pourcentage' => $pourcentage,
            'classe_base' => $classeCible?->nom,
        ];
    }

    /**
     * Moyenne annuelle (sur 100) de l'élève sur toutes les périodes de sa session.
     * Retourne null si aucune note exploitable.
     */
    public function pourcentageAnnee(Eleve $eleve): ?float
    {
        if ($eleve->session_scolaire_id === null) {
            return null;
        }

        $periodesFeuilles = Periode::query()
            ->where('session_scolaire_id', $eleve->session_scolaire_id)
            ->where('type', 'periode')
            ->orderBy('ordre')
            ->get();

        $notes = Note::where('eleve_id', $eleve->id)
            ->whereIn('periode_id', $periodesFeuilles->pluck('id'))
            ->get();

        if ($notes->isEmpty()) {
            return null;
        }

        $total = $notes->sum(fn (Note $n) => (float) $n->note);
        $max = $notes->sum(fn (Note $n) => (float) ($n->max ?: 100));

        if ($max <= 0) {
            return null;
        }

        return round($total / $max * 100, 2);
    }

    /**
     * Classe de destination : classe supérieure de la même section si l'élève
     * est promu, sinon sa classe actuelle (redoublement). Le passage d'une
     * section à la suivante (primaire → secondaire…) envoie vers la première
     * classe de la nouvelle section.
     */
    public function classeCible(Eleve $eleve, bool $promu): ?Classe
    {
        $classe = $eleve->classe;
        if ($classe === null) {
            return null;
        }

        if (! $promu) {
            return $classe;
        }

        $memeSection = Classe::query()
            ->where('section', $classe->section?->value)
            ->get()
            ->sortBy(fn (Classe $c) => [$this->niveauRang($c->niveau), $c->nom]);

        $idx = $memeSection->search(fn (Classe $c) => $c->id === $classe->id);
        $suivante = $idx !== false ? $memeSection->get($idx + 1) : null;
        if ($suivante !== null) {
            return $suivante;
        }

        return $this->premiereClasseSectionSuivante($classe)
            ?? $classe;
    }

    /**
     * @return int Rang numérique du niveau (« 1ère » → 1, « 6eme » → 6…)
     */
    public function niveauRang(?string $niveau): int
    {
        if ($niveau === null) {
            return 9999;
        }

        $ascii = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $niveau));

        return (int) preg_replace('/\D.*$/', '', $ascii) ?: 0;
    }

    private function premiereClasseSectionSuivante(Classe $classe): ?Classe
    {
        $suivante = match ($classe->section?->value) {
            'maternelle' => 'primaire',
            'primaire' => 'secondaire',
            default => null,
        };

        if ($suivante === null) {
            return null;
        }

        return Classe::query()
            ->where('section', $suivante)
            ->get()
            ->sortBy(fn (Classe $c) => [$this->niveauRang($c->niveau), $c->nom])
            ->first();
    }
}