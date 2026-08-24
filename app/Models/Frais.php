<?php

namespace App\Models;

use App\Enums\Section;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Frais extends Model
{
    use BelongsToInstitution;

    protected $fillable = ['montant', 'description', 'section'];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'section' => Section::class,
        ];
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementFrais::class);
    }
}
