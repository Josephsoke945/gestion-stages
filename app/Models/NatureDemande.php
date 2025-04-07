<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NatureDemande extends Model
{
    // Autorise l’attribut 'libelle' à être rempli automatiquement
    protected $fillable = ['libelle'];

    /**
     * Une nature de demande peut être liée à plusieurs demandes.
     */
    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class);
    }
}
