<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'date_de_naissance',
        'sexe',
        'adresse',
        'email',
        'telephone',
        'mot_de_passe',
        'date_de_inscription',
        'role',
        'is_authentifie',
    ];

    protected $hidden = ['mot_de_passe'];

    // 🔔 Relation avec les notifications
    public function notifications() {
        return $this->hasMany(Notification::class, 'utilisateur_id');
    }

}
   


?>