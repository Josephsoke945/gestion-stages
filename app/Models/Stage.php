<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_stage_id',
        'date_debut_reelle',
        'date_fin_reelle',
        'statut',
        'bilan_final',
    ];

    protected $casts = [
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
    ];

    // Relations
    public function demandeStage()
    {
        return $this->belongsTo(DemandeStage::class);
    }

    public function affectationsMaitreStage()
    {
        return $this->hasMany(AffectationMaitreStage::class);
    }

    public function maitreStageActuel()
    {
        return $this->hasOneThrough(
            Agent::class, 
            AffectationMaitreStage::class,
            'stage_id', // Clé étrangère sur la table pivot
            'id', // Clé primaire sur la table Agent
            'id', // Clé primaire sur la table Stage
            'maitre_stage_id' // Clé étrangère sur la table pivot
        )->whereHas('affectationsMaitre', function($query) {
            $query->where('est_actif', true);
        });
    }

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Méthode pour récupérer les stagiaires associés au stage
    public function stagiaires()
    {
        return $this->hasManyThrough(
            Stagiaire::class,
            DemandeStage::class,
            'id', // Clé étrangère sur DemandeStage référençant Stage
            'demande_stage_id', // Clé étrangère sur Stagiaire référençant DemandeStage
            'demande_stage_id', // Clé locale sur Stage
            'id' // Clé locale sur DemandeStage
        );
    }
}