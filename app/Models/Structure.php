<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Structure extends Model
{
    protected $fillable = [
        'nom',
        'sigle',
        'localisation',
        'description',
    ];

    // 🔗 La structure peut avoir plusieurs agents
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    // 🔗 La structure peut recevoir plusieurs demandes de stage
    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeStage::class);
    }

    // 🔗 La structure peut avoir plusieurs stages
    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    // 🔗 La structure peut être concernée par plusieurs affectations
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }
}
