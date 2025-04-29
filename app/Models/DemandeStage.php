<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeStage extends Model
{
    use HasFactory;
    
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'demande_stages';
    
    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stagiaire_id',
        'structure_id',
        'date_debut',
        'date_fin',
        'type',
        'nature',
        'lettre_cv_path',
        'code_suivi',
        'statut',
        'date_soumission',
    ];
    
    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_soumission' => 'datetime',
        'structure_id' => 'integer',
    ];
    
    /**
     * Relation avec le stagiaire
     */
    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_id', 'id_stagiaire');
    }
    
    /**
     * Relation avec la structure
     */
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }
    
    /**
     * Relation avec les membres du groupe
     */
    public function membres()
    {
        return $this->hasMany(MembreGroupe::class, 'demande_stage_id');
    }
}
