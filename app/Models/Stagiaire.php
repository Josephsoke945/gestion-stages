<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // N'oubliez pas d'importer HasMany

class Stagiaire extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'niveau_etude',
        'universite_id',
    ];

    /**
     * Get the user that owns the Stagiaire.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the universite that the Stagiaire belongs to.
     */
    public function universite(): BelongsTo
    {
        return $this->belongsTo(Universite::class);
    }

    /**
     * Get all of the demandeStages for the Stagiaire.
     */
    public function demandesStages(): HasMany
    {
        return $this->hasMany(DemandeStage::class, 'stagiaire_id', 'id');
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}