<?php

namespace App\Models;

use App\Enums\StatutPaiement;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementFrais extends Model
{
    use BelongsToInstitution;

    protected $table = 'paiements_frais';

    protected $fillable = [
        'eleve_id', 'frais_id', 'moi_id', 'classe_id',
        'amount_paid', 'payment_date', 'statut', 'session_scolaire_id', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount_paid' => 'decimal:2',
            'statut' => StatutPaiement::class,
        ];
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function frais(): BelongsTo
    {
        return $this->belongsTo(Frais::class);
    }

    public function mois(): BelongsTo
    {
        return $this->belongsTo(Mois::class, 'moi_id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
