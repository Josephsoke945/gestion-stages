<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'contenu',
        'lien',
        'est_lu',
        'date_lecture',
    ];

    protected $casts = [
        'est_lu' => 'boolean',
        'date_lecture' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}