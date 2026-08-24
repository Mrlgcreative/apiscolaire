<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mois extends Model
{
    protected $table = 'mois';

    protected $fillable = ['nom', 'ordre'];

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementFrais::class, 'moi_id');
    }
}
