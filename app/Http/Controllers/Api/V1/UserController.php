<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreUserRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\Institution;
use App\Models\User;
use App\Support\CurrentInstitution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizeAdmin($request);

        $users = User::query()
            ->with('institution')
            ->when(
                app(CurrentInstitution::class)->id,
                fn ($q, $id) => $q->where('institution_id', $id),
                fn ($q) => $q->when(
                    $request->filled('institution_id'),
                    fn ($qq) => $qq->where('institution_id', $request->string('institution_id')),
                ),
            )
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%'.$request->search.'%';
                $q->where(fn ($w) => $w
                    ->where('username', 'like', $s)
                    ->orWhere('email', 'like', $s));
            })
            ->orderBy('username')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100))
            ->withQueryString();

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        abort_unless($actor->isAdmin(), 403);

        $data = $request->validated();

        // Résolution de l'institution du nouveau compte :
        // contexte courant (école) > institution_id explicite (super-admin) > groupe.
        if ($institutionId = app(CurrentInstitution::class)->id) {
            $data['institution_id'] = $institutionId;
        } elseif ($actor->isSchoolAdmin()) {
            $data['institution_id'] = $actor->institution_id;
        } elseif ($request->filled('institution_id')) {
            $institution = Institution::find($request->string('institution_id'));
            abort_unless($institution !== null, 422, 'Institution inconnue.');
            $data['institution_id'] = $institution->id;
        }

        $user = User::create($data);
        $user->load('institution');

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function show(Request $request, User $user): UserResource
    {
        $this->authorizeTarget($request, $user);

        return new UserResource($user->load('institution'));
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->authorizeTarget($request, $user);

        $data = collect($request->validated())->except('institution_id')->all();

        // Seul le super-admin peut déplacer un compte vers une autre école.
        if ($request->user()->isGroupAdmin() && $request->filled('institution_id')) {
            $institution = Institution::find($request->string('institution_id'));
            abort_unless($institution !== null, 422, 'Institution inconnue.');
            $data['institution_id'] = $institution->id;
        }

        $this->ensureNotLastAdmin($user, $data['role'] ?? null);

        $user->update($data);
        $user->load('institution');

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeTarget($request, $user);

        if ($user->is($request->user())) {
            return response()->json(['message' => 'Impossible de supprimer votre propre compte.'], 409);
        }

        $this->ensureNotLastAdmin($user);

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Accès réservé aux administrateurs.');
    }

    private function authorizeTarget(Request $request, User $target): void
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Accès réservé aux administrateurs.');
        abort_unless($request->user()->canManageUser($target), 403, 'Vous ne pouvez gérer que les comptes de votre école.');
    }

    /**
     * Bloque la suppression / rétrogradation du dernier admin actif d'une école ou du groupe.
     */
    private function ensureNotLastAdmin(User $target, ?string $newRole = null): void
    {
        if (! $target->isAdmin()) {
            return;
        }

        if ($newRole !== null && $newRole === UserRole::Admin->value) {
            return;
        }

        $autres = User::where('role', UserRole::Admin)
            ->where('id', '!=', $target->id)
            ->where('locked', false)
            ->when(
                $target->institution_id,
                fn ($q, $id) => $q->where('institution_id', $id),
                fn ($q) => $q->whereNull('institution_id'),
            )
            ->exists();

        abort_unless($autres, 409, 'Impossible : cet utilisateur est le dernier administrateur actif.');
    }
}
