<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matricule',
        'fonction',
        'date_embauche',
        'universite_responsable_id', // Assurez-vous d'ajouter ce champ au fillable
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

    // Définir d'autres relations Eloquent ici ultérieurement (maître de stage, etc.)
}