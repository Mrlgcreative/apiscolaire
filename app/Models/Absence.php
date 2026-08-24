<?php

namespace App\Models;

use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use BelongsToInstitution;

    protected $fillable = ['eleve_id', 'date_absence', 'motif', 'justifiee'];

    protected function casts(): array
    {
        return [
            'date_absence' => 'date',
            'justifiee' => 'boolean',
        ];
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }
}
