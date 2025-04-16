<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'niveau_etude',
        'universite',
        'filiere',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function demandesStages(): HasMany
    {
        return $this->hasMany(DemandeStage::class, 'stagiaire_id', 'id');
    }
}
