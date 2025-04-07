<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    protected $fillable = [
        'demande_stage_id',
        'structure_id',
        'type_stage',
        'note',
        'theme',
        'date_debut',
        'date_fin',
    ];

    // 🔗 Chaque stage est issu d'une demande de stage
    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandeStage::class, 'demande_stage_id');
    }

    // 🔗 Chaque stage est affecté à une structure
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    // 🔗 Le stage peut être lié à plusieurs stagiaires (si en groupe)
    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stage_stagiaire')
                    ->withPivot('note')
                    ->withTimestamps();
    }

    // 🔗 Un stage peut avoir plusieurs affectations à des maîtres de stage
    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }
}
