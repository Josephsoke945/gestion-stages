<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'stagiaire_id',
        'agent_evaluateur_id',
        'note',
        'est_assidu',
        'est_ponctuel',
        'commentaire',
        'date_evaluation',
    ];

    protected $casts = [
        'est_assidu' => 'boolean',
        'est_ponctuel' => 'boolean',
        'date_evaluation' => 'date',
    ];

    // Relations
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function agentEvaluateur()
    {
        return $this->belongsTo(Agent::class, 'agent_evaluateur_id');
    }
}