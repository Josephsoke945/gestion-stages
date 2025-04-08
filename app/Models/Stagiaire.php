<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stagiaire extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_stagiaire';
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

    // Définir d'autres relations Eloquent ici ultérieurement
}