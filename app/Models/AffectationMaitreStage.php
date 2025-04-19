<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffectationMaitreStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'agent_responsable_id',
        'maitre_stage_id',
        'date_affectation',
        'est_actif',
        'commentaire',
    ];

    protected $casts = [
        'date_affectation' => 'datetime',
        'est_actif' => 'boolean',
    ];

    // Relations
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function agentResponsable()
    {
        return $this->belongsTo(Agent::class, 'agent_responsable_id');
    }

    public function maitreStage()
    {
        return $this->belongsTo(Agent::class, 'maitre_stage_id');
    }
}
