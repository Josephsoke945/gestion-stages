<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThemeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'etat',
        'description',
        'mots_cles',
    ];

    /**
     * Get all the stages that are related to this theme.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    // Définir d'autres relations Eloquent ici ultérieurement (propositions, etc.)
}