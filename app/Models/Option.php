<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    use HasUuids;

    protected $fillable = ['nom'];

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class);
    }
}
