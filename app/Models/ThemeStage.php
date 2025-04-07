<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeStage extends Model
{
    protected $fillable = [
        'libelle',
        'description',
        'structure_id',
    ];

    // 🔗 Chaque thème appartient à une structure
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }
}
