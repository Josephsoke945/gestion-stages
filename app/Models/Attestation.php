<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attestation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_suivi',
        'stage_id',
        'stagiaire_id',
        'delivre_par_agent_id',
        'date_rendez_vous',
        'statut',
        'fichier_path',
        'date_demande',
        'date_delivrance',
    ];

    protected $casts = [
        'date_rendez_vous' => 'date',
        'date_demande' => 'date',
        'date_delivrance' => 'date',
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

    public function delivrePar()
    {
        return $this->belongsTo(Agent::class, 'delivre_par_agent_id');
    }

    // Boot method pour générer le code de suivi automatiquement
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($attestation) {
            $attestation->code_suivi = 'ATT-' . time() . '-' . rand(1000, 9999);
        });
    }
}