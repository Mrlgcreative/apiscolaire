<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mois extends Model
{
    use HasUuids;

    protected $table = 'mois';

    protected $fillable = ['nom', 'ordre', 'session_scolaire_id'];

    public function sessionScolaire(): BelongsTo
    {
        return $this->belongsTo(SessionScolaire::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementFrais::class, 'moi_id');
    }
}
