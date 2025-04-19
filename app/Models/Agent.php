<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'structure_id',
        'fonction',
        'description_poste',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function affectationsDemandes()
    {
        return $this->hasMany(AffectationDemande::class, 'agent_dpaf_id');
    }

    public function affectationsResponsable()
    {
        return $this->hasMany(AffectationDemande::class, 'agent_responsable_id');
    }

    public function affectationsMaitre()
    {
        return $this->hasMany(AffectationMaitreStage::class, 'maitre_stage_id');
    }

    public function affectationsAffecteur()
    {
        return $this->hasMany(AffectationMaitreStage::class, 'agent_responsable_id');
    }

    public function themesProposés()
    {
        return $this->hasMany(Theme::class, 'propose_par_agent_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'agent_evaluateur_id');
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'delivre_par_agent_id');
    }
}