<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreNoteRequest;
use App\Http\Resources\V1\NoteResource;
use App\Models\Cours;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        // Parent : seulement ses enfants
        // Professeur : seulement ses cours (filtrage optionnel, pas bloquant V1)
        $notes = Note::query()
            ->with(['eleve', 'cours', 'periode'])
            ->when($request->filled('eleve_id'), fn ($q) => $q->where('eleve_id', $request->eleve_id))
            ->when($request->filled('cours_id'), fn ($q) => $q->where('cours_id', $request->cours_id))
            ->when($request->filled('periode_id'), fn ($q) => $q->where('periode_id', $request->periode_id))
            ->when($request->filled('classe_id'), function ($q) use ($request) {
                $q->whereHas('eleve', fn ($qq) => $qq->where('classe_id', $request->classe_id));
            })
            ->when($user?->isParent(), function ($q) use ($user) {
                $ids = $user->enfants()->pluck('eleves.id');
                $q->whereIn('eleve_id', $ids);
            })
            ->latest()
            ->paginate(min(max($request->integer('per_page', 50), 1), 100))
            ->withQueryString();

        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['max'] = $data['max'] ?? 100;
        $data['coefficient'] = $data['coefficient'] ?? 1;

        // Si professeur, vérifier qu'il enseigne ce cours
        $user = $request->user();
        if ($user->isProfesseur()) {
            $prof = $user->professeur;
            if (! $prof || ! Cours::where('id', $data['cours_id'])->where('professeur_id', $prof->id)->exists()) {
                return response()->json(['message' => 'Vous ne pouvez noter que vos propres cours.'], 403);
            }
            $data['professeur_id'] = $prof->id;
        } else {
            // Pour direction/admin, lier au prof du cours si existe
            $cours = Cours::find($data['cours_id']);
            $data['professeur_id'] = $cours?->professeur_id;
        }
        $data['created_by'] = $user->id;

        // 1 note = 1 eleve/cours/periode : upsert
        $note = Note::updateOrCreate(
            [
                'eleve_id' => $data['eleve_id'],
                'cours_id' => $data['cours_id'],
                'periode_id' => $data['periode_id'],
            ],
            $data,
        );

        return (new NoteResource($note->load(['eleve', 'cours', 'periode'])))->response()->setStatusCode(201);
    }

    public function show(Note $note): NoteResource
    {
        $this->authorizeParent($note);

        return new NoteResource($note->load(['eleve', 'cours', 'periode', 'professeur']));
    }

    public function update(StoreNoteRequest $request, Note $note): NoteResource
    {
        $this->authorizeParent($note);
        $user = $request->user();
        if ($user->isProfesseur()) {
            $prof = $user->professeur;
            if (! $prof || $note->cours->professeur_id !== $prof->id) {
                abort(403, 'Vous ne pouvez modifier que vos propres notes.');
            }
        }

        $data = $request->validated();
        $note->update($data);

        return new NoteResource($note->load(['eleve', 'cours', 'periode']));
    }

    public function destroy(Note $note): JsonResponse
    {
        $this->authorizeParent($note);
        $user = request()->user();
        if ($user->isProfesseur() && $note->cours->professeur_id !== $user->professeur?->id) {
            abort(403);
        }
        $note->delete();

        return response()->json(['message' => 'Note supprimée.']);
    }

    private function authorizeParent(Note $note): void
    {
        $user = request()->user();
        if ($user?->isParent() && ! $user->enfants()->where('eleves.id', $note->eleve_id)->exists()) {
            abort(403, 'Vous ne pouvez voir que les notes de vos enfants.');
        }
    }
}
