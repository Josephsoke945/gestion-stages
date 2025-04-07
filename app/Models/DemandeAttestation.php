<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeAttestation extends Model
{
    protected $table = 'demande_attestations';

    protected $fillable = [
        'stagiaire_id',
        'type_attestation',
        'etat',
        'motif',
        'cree_par',
        'modifie_par',
        'valide_par',
    ];

    /**
     * Relations
     */

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }

    public function modificateur()
    {
        return $this->belongsTo(Utilisateur::class, 'modifie_par');
    }

    public function valideur()
    {
        return $this->belongsTo(Utilisateur::class, 'valide_par');
    }
}
