<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffectationMaitreStage extends Model
{
    protected $table = 'affectation_maitre_stages';

    protected $fillable = [
        'stage_id',
        'agent_id',
        'valide',
        'motif',
        'modifie_par',
        'termine',
    ];

    /**
     * Relations
     */

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function modificateur()
    {
        return $this->belongsTo(Utilisateur::class, 'modifie_par');
    }
}
