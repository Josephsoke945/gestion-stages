<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeStage extends Model
{
    protected $fillable = [
        'stagiaire_id',
        'nature_demande_id',
        'type_demande_id',
        'structure_id',
        'universite_id',
        'date_debut',
        'date_fin',
        'motivation',
        'statut',
    ];

    public function stagiaire(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function natureDemande(): BelongsTo
    {
        return $this->belongsTo(NatureDemande::class);
    }

    public function typeDemande(): BelongsTo
    {
        return $this->belongsTo(typeDemande::class);
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);

    }
    public function universite(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'universite_id');
    }
}
