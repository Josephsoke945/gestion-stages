<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediteur_id',
        'stage_id',
        'contenu',
        'fichier_path',
    ];

    // Relations
    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function destinataires()
    {
        return $this->belongsToMany(User::class, 'message_destinataires', 'message_id', 'destinataire_id')
            ->withPivot('est_lu', 'date_lecture')
            ->withTimestamps();
    }
}
