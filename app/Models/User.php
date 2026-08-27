<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'username', 'email', 'password', 'role', 'image', 'telephone',
    'adresse', 'institution_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked' => 'boolean',
            'lock_expiry' => 'datetime',
            'password_change_date' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isProfesseur(): bool
    {
        return $this->role === UserRole::Professeur;
    }

    public function isParent(): bool
    {
        return $this->role === UserRole::Parent;
    }

    public function isDirection(): bool
    {
        return in_array($this->role, [UserRole::Director, UserRole::Directrice, UserRole::Prefet], true);
    }

    /**
     * Super-admin du groupe : institution_id null, gère toutes les écoles.
     */
    public function isGroupAdmin(): bool
    {
        return $this->isAdmin() && $this->institution_id === null;
    }

    /**
     * Admin d'une école : ne gère que sa propre institution.
     */
    public function isSchoolAdmin(): bool
    {
        return $this->isAdmin() && $this->institution_id !== null;
    }

    public function canManageUser(self $target): bool
    {
        if ($this->isGroupAdmin()) {
            return true;
        }

        return $this->isSchoolAdmin()
            && $target->institution_id !== null
            && $target->institution_id === $this->institution_id;
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function professeur(): HasOne
    {
        return $this->hasOne(Professeur::class);
    }

    public function enfants(): BelongsToMany
    {
        return $this->belongsToMany(Eleve::class, 'parent_eleve', 'parent_id', 'eleve_id')
            ->withPivot('lien')
            ->withTimestamps();
    }
}
