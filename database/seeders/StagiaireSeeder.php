<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Stagiaire;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StagiaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Récupérer ou créer les utilisateurs. C'est important de s'assurer que les utilisateurs existent.
        $user1 = User::firstOrCreate(['email' => 'stagiaire1@example.com'], ['nom' => 'Stagiaire', 'prenom' => 'Un', 'password' => Hash::make('password')]);
        $user2 = User::firstOrCreate(['email' => 'stagiaire2@example.com'], ['nom' => 'Stagiaire', 'prenom' => 'Deux', 'password' => Hash::make('password')]);

        // 2. Créer les stagiaires en utilisant Eloquent, et les lier aux utilisateurs créés.
        Stagiaire::create([
            'user_id' => $user1->id, // Utilisez $user1->id
            'niveau_etude' => 'Licence 3',
        ]);

        Stagiaire::create([
            'user_id' => $user2->id, // Utilisez $user2->id
            'niveau_etude' => 'Master 2',
        ]);
    }
}
