<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffectationDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_stage_id',
        'agent_dpaf_id',
        'structure_id',
        'agent_responsable_id',
        'date_affectation',
        'statut',
        'commentaire',
    ];

    protected $casts = [
        'date_affectation' => 'datetime',
    ];

    // Relations
    public function demandeStage()
    {
        return $this->belongsTo(DemandeStage::class);
    }

    public function agentDpaf()
    {
        return $this->belongsTo(Agent::class, 'agent_dpaf_id');
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function agentResponsable()
    {
        return $this->belongsTo(Agent::class, 'agent_responsable_id');
    }
}