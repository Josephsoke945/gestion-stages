<?php

namespace Database\Seeders;

use App\Models\User;
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

        $stagiaires = [
            ['user_id' => $user1->id, 'niveau_etude' => 'Licence 3'],
            ['user_id' => $user2->id, 'niveau_etude' => 'Master 2'],
            // Ajoutez d'autres stagiaires si nécessaire en liant à des user_id existants
        ];

        foreach ($stagiaires as $stagiaire) {
            if (!DB::table('stagiaires')->where('user_id', $stagiaire['user_id'])->exists()) {
                DB::table('stagiaires')->insert([
                    'user_id' => $stagiaire['user_id'],
                    'niveau_etude' => $stagiaire['niveau_etude'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}