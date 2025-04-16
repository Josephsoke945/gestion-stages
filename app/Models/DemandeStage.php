<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DemandeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id',
        'structure_id',
        'nature',
        'statut',
        'lettre_cv_path',
    ];

    protected $casts = [
        'date_soumission' => 'date',
    ];

    /**
     * Get the stagiaire that made the demande.
     */
    public function stagiaire(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_id', 'id'); // Ajout des clés étrangères
    }

    /**
     * Get the structure for this demande.
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    /**
     * Get the stage created from this demande (if accepted).
     */
    public function stage(): HasOne
    {
        return $this->hasOne(Stage::class);
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}