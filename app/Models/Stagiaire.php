<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stagiaire extends Model
{
    use HasFactory;

    protected $table = 'stagiaires';
    protected $primaryKey = 'id_stagiaire';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'est_stagiaire_academique',
        'utilisateur_id',
    ];

    // 🔁 Relation avec Utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    // 🔁 Un stagiaire peut faire plusieurs demandes de stage
    public function demandesStage()
    {
        return $this->hasMany(DemandeStage::class, 'stagiaire_id');
    }

    // 🔁 Un stagiaire peut être lié à plusieurs stages via DemandeUneMediation (si concerné)
    public function stages()
    {
        return $this->belongsToMany(Stage::class, 'demande_une_mediation', 'stagiaire_id', 'stage_id');
    }
}
