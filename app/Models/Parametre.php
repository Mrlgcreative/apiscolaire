<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parametre extends Model
{
    use HasUuids;

    protected $fillable = ['institution_id', 'cle', 'valeur'];

    protected function casts(): array
    {
        return [
            'valeur' => 'json',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
