<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne; // N'oubliez pas d'importer HasOne

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'date_de_naissance',
        'sexe',
        'adresse',
        'email',
        'password',
        'telephone',
        'date_d_inscription',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_de_naissance' => 'date',
        'date_d_inscription' => 'date',
        'role' => 'string',
    ];

    /**
     * Get the stagiaire associated with the user.
     */
   // Ajouter ceci pour la vérification de rôle

public function isStagiaire()
{
    return $this->role === 'stagiaire';
}

public function isAdmin()
{
    return $this->role === 'admin';
}

public function isAgent()
{
    return $this->role === 'agent';
}

public function stagiaire()
{
    return $this->hasOne(Stagiaire::class);
}

public function membreGroupes()
{
    return $this->hasMany(MembreGroupe::class);
}

}