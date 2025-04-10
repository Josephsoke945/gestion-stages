<?php

namespace Database\Seeders;

use App\Models\AffectationMaitreStage;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AffectationMaitreStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un stage et un utilisateur (qui sera le maître de stage) existants
        $stage = Stage::first();
        $maitreStage = User::where('role', 'Agent')->first(); // Assumons que les agents peuvent être maîtres de stage

        if ($stage && $maitreStage) {
            AffectationMaitreStage::firstOrCreate(
                [
                    'stage_id' => $stage->id,
                    'maitre_stage_id' => $maitreStage->id,
                ],
                [
                    'date_affectation' => now(),
                    'statut' => 'En cours',
                    'motif_refus' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            echo "Affectation maître de stage créée ou existante pour le stage (ID: " . $stage->id . ") et le maître de stage (ID: " . $maitreStage->id . ").\n";
        } else {
            echo "Impossible de créer l'affectation : aucun stage ou maître de stage trouvé.\n";
        }
    }
}