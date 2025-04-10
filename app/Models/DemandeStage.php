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
        'nature_demande_id',
        'statut',
        'date_soumission',
        'structure_souhaitee',
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
        return $this->belongsTo(Structure::class); // La clé étrangère 'structure_id' est conventionnelle
    }

    /**
     * Get the nature of this demande.
     */
    public function natureDemande(): BelongsTo
    {
        return $this->belongsTo(NatureDemande::class); // La clé étrangère 'nature_demande_id' est conventionnelle
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