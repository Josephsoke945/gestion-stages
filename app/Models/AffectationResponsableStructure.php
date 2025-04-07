<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffectationResponsableStructure extends Model
{
    protected $table = 'affectation_responsable_structures';

    protected $fillable = [
        'demande_stage_id',
        'agent_id',
        'date_affectation',
        'valide',
        'motif',
        'modifie_par',
        'termine',
    ];

    /**
     * Relations
     */

    public function demandeStage()
    {
        return $this->belongsTo(DemandeStage::class);
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
