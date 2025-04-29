<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Universite extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_universite',
        'adresse',
    ];

    /**
     * Get all the stagiaires that belong to this universite.
     */
    public function stagiaires(): HasMany
    {
        return $this->hasMany(Stagiaire::class);
    }

    /**
     * Get the agent that is the responsable for this universite (if any).
     */
    public function responsable(): HasOne
    {
        return $this->hasOne(Agent::class, 'universite_responsable_id');
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}