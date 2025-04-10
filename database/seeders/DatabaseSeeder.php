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
            ResponsableStructureSeeder::class, // Créer l'utilisateur responsable ET l'agent
            ResponsableSeeder::class, // Les autres responsables (agents, universités, etc.)
            NatureDemandeSeeder::class,
            StructureSeeder::class,
            StagiaireSeeder::class,
            DemandeStageSeeder::class,
            ThemeStageSeeder::class,
            StageSeeder::class,
            StagiaireUserSeeder::class,
            UniversiteSeeder::class,
            AffectationMaitreStageSeeder::class,
            AffectationResponsableStructureSeeder::class, // Utilise l'agent responsable créé
            NotificationSeeder::class,
            DemandeAttestationSeeder::class,
        ]);
    }
}