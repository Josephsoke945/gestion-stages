<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Structure extends Model
{
    use HasFactory;

    protected $fillable = [
        'sigle',          // Ajout de 'sigle'
        'libelle',        // Ajout de 'libelle'
        'nom',
        'responsable_id',
        'description',
    ];

    /**
     * Get the responsable of the structure.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Get all the stages that belong to this structure.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    /**
     * Get all the demande attestation that are directed to this structure (if specified).
     */
    public function demandesAttestation(): HasMany
    {
        return $this->hasMany(DemandeAttestation::class); // Assurez-vous que la clé étrangère 'structure_id' existe dans DemandeAttestation si nécessaire
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}