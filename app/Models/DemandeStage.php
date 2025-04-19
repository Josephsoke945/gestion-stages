<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemandeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id',
        'structure_id',
        'date_debut',
        'date_fin',
        'type',
        'nature',
        'lettre_cv_path',
        'code_suivi',
        'statut',
        'date_soumission',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_soumission' => 'date',
    ];

    /**
     * Get the stagiaire that made the demande.
     */
    public function stagiaire(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_id', 'id_stagiaire');
    }

    /**
     * Get the structure for this demande.
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    /**
     * Get the stage created from this demande (if accepted).
     */
    public function stage(): HasOne
    {
        return $this->hasOne(Stage::class);
    }

    /**
     * Get the membres du groupe for this demande.
     */
    public function membres(): HasMany
    {
        return $this->hasMany(MembreGroupe::class);
    }

    /**
     * Get the user that owns the demande.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// Modèle Stagiaire


// Modèle MembreGroupe
class MembreGroupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_stage_id',
        'user_id',
    ];

    /**
     * Get the demande that owns the membre.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandeStage::class, 'demande_stage_id');
    }

    /**
     * Get the user that is membre.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// Complément pour le modèle User existant
// À ajouter dans app/Models/User.php
/*
public function stagiaire(): HasOne
{
    return $this->hasOne(Stagiaire::class);
}

public function membreGroupes(): HasMany
{
    return $this->hasMany(MembreGroupe::class);
}
*/