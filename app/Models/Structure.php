<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'type',
        'description',
        'est_dpaf',
        'structure_parent_id',
    ];

    // Relations
    public function structureParent()
    {
        return $this->belongsTo(Structure::class, 'structure_parent_id');
    }

    public function sousServices()
    {
        return $this->hasMany(Structure::class, 'structure_parent_id');
    }

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function demandesStage()
    {
        return $this->hasMany(DemandeStage::class);
    }

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class, 'structure_universite_id');
    }

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }
}
