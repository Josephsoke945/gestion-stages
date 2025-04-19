<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'structure_universite_id',
        'niveau_etude',
        'diplome',
        'competences',
        'demande_stage_id',
        'est_demandeur',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function universite()
    {
        return $this->belongsTo(Structure::class, 'structure_universite_id');
    }

    public function demandeStage()
    {
        return $this->belongsTo(DemandeStage::class);
    }

    public function demandesInitiees()
    {
        return $this->hasMany(DemandeStage::class, 'stagiaire_demandeur_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class);
    }

    public function themesProposés()
    {
        return $this->hasMany(Theme::class, 'propose_par_stagiaire_id');
    }

    // Relation pour récupérer les membres de l'équipe
    public function equipe()
    {
        return $this->hasMany(Stagiaire::class, 'demande_stage_id', 'demande_stage_id')
            ->where('id', '!=', $this->id);
    }
}