<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_stage_id',
        'structure_id',
        'theme_stage_id',
        'date_debut',
        'date_fin',
        'statut',
        'documents_stage',
        'note',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Get the demande attestation that created this stage.
     */
    public function demandeAttestation(): BelongsTo
    {
        return $this->belongsTo(DemandeAttestation::class);
    }

    /**
     * Get the structure where this stage takes place.
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    /**
     * Get the theme of this stage.
     */
    public function themeStage(): BelongsTo
    {
        return $this->belongsTo(ThemeStage::class);
    }

    /**
     * Get the affectation of the maitre de stage for this stage.
     */
    public function affectationMaitreStage(): HasOne
    {
        return $this->hasOne(AffectationMaitreStage::class);
    }

    /**
     * Get the stagiaires participating in this stage (if it's a team stage).
     * This assumes a pivot table 'stage_stagiaire' for a many-to-many relationship.
     * Based on the description, it might be a one-to-one or one-to-many from DemandeStage to Stage.
     * Let's clarify this relationship later if needed.
     */
    // public function stagiaires(): BelongsToMany
    // {
    //     return $this->belongsToMany(Stagiaire::class, 'stage_stagiaire');
    // }

    // Définir d'autres relations Eloquent ici ultérieurement
}