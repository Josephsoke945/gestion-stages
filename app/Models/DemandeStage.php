<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_suivi',
        'stagiaire_demandeur_id',
        'structure_id',
        'type_stage',
        'nature_stage',
        'date_debut_souhaitee',
        'date_fin_souhaitee',
        'niveau_etude',
        'cv',
        'lettre_motivation',
        'lettre_recommandation',
        'diplomes_attestations',
        'statut',
        'motif_refus',
    ];

    protected $casts = [
        'date_debut_souhaitee' => 'date',
        'date_fin_souhaitee' => 'date',
    ];

    // Relations
    public function stagiaireDemandeur()
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_demandeur_id');
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class);
    }

    public function affectationDemande()
    {
        return $this->hasOne(AffectationDemande::class);
    }

    public function stage()
    {
        return $this->hasOne(Stage::class);
    }

    // Boot method pour générer le code de suivi automatiquement
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($demande) {
            $demande->code_suivi = 'DEM-' . time() . '-' . rand(1000, 9999);
        });
    }
}
