<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffectationResponsableStructure extends Model
{
    use HasFactory;

    protected $table = 'affectation_responsable_structures'; // Important si le nom de la table ne suit pas la convention Eloquent

    protected $fillable = [
        'structure_id',
        'responsable_id',
        'date_affectation',
        'date_fin_affectation',
        'poste',
        'id_demande_stages',
    ];

    protected $casts = [
        'date_affectation' => 'date',
        'date_fin_affectation' => 'date',
    ];

    /**
     * Get the structure that this affectation belongs to.
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    /**
     * Get the responsable (user) involved in this affectation.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Get the demande de stage associated with this affectation.
     */
    public function demandeStage(): BelongsTo
    {
        return $this->belongsTo(DemandeStage::class, 'id_demande_stages');
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}