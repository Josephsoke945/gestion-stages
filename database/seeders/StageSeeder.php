<?php

namespace Database\Seeders;

use App\Models\DemandeStage;
use App\Models\Structure;
use App\Models\ThemeStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demande1 = DemandeStage::first();
        $structure1 = Structure::first();
        $theme1 = ThemeStage::where('intitule', 'Développement d\'une application web avec Laravel')->first();
        $theme2 = ThemeStage::where('intitule', 'Analyse de données avec Python et Pandas')->first();

        if ($demande1 && $structure1 && $theme1) {
            DB::table('stages')->insert([
                'demande_stage_id' => $demande1->id,
                'structure_id' => $structure1->id,
                'theme_stage_id' => $theme1->id,
                'date_debut' => now()->addMonths(1),
                'date_fin' => now()->addMonths(3),
                'statut' => 'En cours',
                'type' => 'academique',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($demande1 && $structure1 && $theme2) {
            DB::table('stages')->insert([
                'demande_stage_id' => $demande1->id,
                'structure_id' => $structure1->id,
                'theme_stage_id' => $theme2->id,
                'date_debut' => now()->addMonths(2),
                'date_fin' => now()->addMonths(4),
                'statut' => 'En cours',
                'type' => 'professionnel',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajoutez d'autres stages si nécessaire en utilisant des IDs existants
    }
}