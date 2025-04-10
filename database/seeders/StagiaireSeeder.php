<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Stagiaire;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StagiaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer des utilisateurs existants ou en créer un pour lier les stagiaires
        $user1 = User::firstOrCreate(['email' => 'stagiaire1@example.com'], ['nom' => 'Stagiaire', 'prenom' => 'Un', 'password' => bcrypt('password')]);
        $user2 = User::firstOrCreate(['email' => 'stagiaire2@example.com'], ['nom' => 'Stagiaire', 'prenom' => 'Deux', 'password' => bcrypt('password')]);

        $stagiairesData = [
            ['user_id' => $user1->id, 'niveau_etude' => 'Licence 3'],
            ['user_id' => $user2->id, 'niveau_etude' => 'Master 2'],
        ];

        foreach ($stagiairesData as $stagiaireData) {
            // Utilisation de Eloquent pour créer les stagiaires
            Stagiaire::create($stagiaireData);
        }
    }
}