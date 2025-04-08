<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffectationMaitreStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'maitre_stage_id',
        'date_affectation',
        'statut',
        'motif_refus',
    ];

    protected $casts = [
        'date_affectation' => 'date',
    ];

    /**
     * Get the stage that this affectation belongs to.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Get the maitre de stage (user) involved in this affectation.
     */
    public function maitreStage(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}