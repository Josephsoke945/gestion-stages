<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $table = 'agents';
    protected $primaryKey = 'utilisateur_id';
    public $incrementing = false;

    protected $fillable = [
        'utilisateur_id',
        'matricule',
        'structure_id',
        'est_responsable_structure',
        'est_dpa',
        'est_maitre_stage',
    ];

    // 🔗 L’agent est un utilisateur
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    // 🔗 L’agent peut appartenir à une structure
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    // 🔗 L’agent peut être maître de stage sur plusieurs affectations
    public function affectationsStages(): HasMany
    {
        return $this->hasMany(Affectation::class, 'maitre_stage_id');
    }

    // 🔗 L’agent peut être responsable de plusieurs affectations de demande
    public function affectationsDemandes(): HasMany
    {
        return $this->hasMany(Affectation::class, 'agent_responsable_id');
    }
}
