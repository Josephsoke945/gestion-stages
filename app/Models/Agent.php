<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use HasFactory;

    const ROLE_DPAF = 'DPAF';
    const ROLE_MS = 'MS';
    const ROLE_RS = 'RS';

    protected $fillable = [
        'user_id',
        'matricule',
        'fonction',
        'role_agent',
        'date_embauche',
        'structure_id', // Ajouté pour gérer la relation avec une structure
    ];

    protected $casts = [
        'date_embauche' => 'datetime',
    ];

    /**
     * Get the user that owns the Agent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all the structures where this agent is the responsable.
     */
    public function structuresResponsable(): HasMany
    {
        return $this->hasMany(Structure::class, 'responsable_id');
    }

    /**
     * Get the universite for which this agent is the responsable (if any).
     */
    public function universiteResponsable(): BelongsTo
    {
        return $this->belongsTo(Universite::class, 'universite_responsable_id');
    }

    /**
     * Get the structure for which this agent is the responsable (if any).
     */
    public function structureResponsable(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    // Définir d'autres relations Eloquent ici ultérieurement (maître de stage, etc.)
}