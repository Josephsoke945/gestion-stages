<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'date_envoi',
        'statut',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
    ];

    /**
     * Get the user that the notification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Définir d'autres relations Eloquent ici ultérieurement
}