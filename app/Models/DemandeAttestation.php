<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DemandeAttestation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id',
        'date_demande',
        'statut',
        'motif_refus',
        'date_validation',
        'document_attestation',
        'fait_demande_attestation',
        'valide_attestation',
        'refuse_attestation',
        'generer_attestation_pdf',
        'envoyer_attestation_email',
    ];

    protected $casts = [
        'date_demande' => 'date',
        'date_validation' => 'date',
        'fait_demande_attestation' => 'boolean',
        'valide_attestation' => 'boolean',
        'refuse_attestation' => 'boolean',
        'generer_attestation_pdf' => 'boolean',
        'envoyer_attestation_email' => 'boolean',
    ];

    /**
     * Get the stagiaire that made the demande.
     */
    public function stagiaire(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class);
    }

    /**
     * Get the stage associated with the demande (if accepted).
     */
    public function stage(): HasOne
    {
        return $this->hasOne(Stage::class);
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}