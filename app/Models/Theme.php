<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'structure_id',
        'propose_par_agent_id',
        'propose_par_stagiaire_id',
        'est_valide',
        'stage_id',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'est_valide' => 'boolean',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Relations
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function proposePar()
    {
        // Retourne soit l'agent soit le stagiaire qui a proposé le thème
        if ($this->propose_par_agent_id) {
            return $this->agentProposant();
        }
        return $this->stagiaireProposant();
    }

    public function agentProposant()
    {
        return $this->belongsTo(Agent::class, 'propose_par_agent_id');
    }

    public function stagiaireProposant()
    {
        return $this->belongsTo(Stagiaire::class, 'propose_par_stagiaire_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    // Méthode pour récupérer tous les stagiaires qui ont travaillé sur ce thème
    public function stagiaires()
    {
        return $this->hasManyThrough(
            Stagiaire::class,
            Stage::class,
            'id', // clé étrangère sur Theme référençant Stage
            'demande_stage_id', // clé étrangère sur Stagiaire référençant DemandeStage
            'stage_id', // clé locale sur Theme
            'demande_stage_id' // clé locale sur Stage
        );
    }
}
