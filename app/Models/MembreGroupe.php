<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembreGroupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_stage_id',
        'user_id',
    ];

    /**
     * Get the demande that owns the membre.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandeStage::class, 'demande_stage_id');
    }

    /**
     * Get the user that is membre.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 