<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NatureDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
    ];

    /**
     * Get all the demande stages that have this nature.
     */
    public function demandeStages(): HasMany
    {
        return $this->hasMany(DemandeStage::class);
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}