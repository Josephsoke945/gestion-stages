<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'nom' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $this->call([
            ResponsableStructureSeeder::class, // Crée l'utilisateur responsable ET l'agent responsable structure
            ResponsableSeeder::class, // Les autres responsables (agents, universités, etc.)
            StructureSeeder::class,
            UniversiteSeeder::class, // Les universités doivent être créées avant les stagiaires
            StagiaireSeeder::class, // Les stagiaires doivent être créés en premier
            StagiaireUserSeeder::class, // Lie les stagiaires aux utilisateurs après la création des stagiaires
            DemandeStageSeeder::class, // Les demandes de stage viennent après les stagiaires et les structures
            ThemeStageSeeder::class,
            StageSeeder::class,
            AffectationMaitreStageSeeder::class,
            AffectationResponsableStructureSeeder::class, // Utilise l'agent responsable structure créé
            NotificationSeeder::class,
            DemandeAttestationSeeder::class, // Les demandes d'attestation viennent APRÈS la création des stagiaires
        ]);
    }
}