<?php

namespace Database\Seeders;

use App\Models\AffectationResponsableStructure;
use App\Models\Structure;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AffectationResponsableStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer une structure existante
        $structure = Structure::first();

        // Récupérer l'utilisateur responsable de la structure
        $userResponsable = User::where('role', 'Responsable')->first();

        if ($structure && $userResponsable) {
            // Récupérer l'agent correspondant à cet utilisateur
            $agentResponsable = Agent::where('user_id', $userResponsable->id)->first();

            if ($agentResponsable) {
                $dateAffectation = now()->subDays(30)->toDateString();

                // Vérifier si une affectation existe déjà
                $affectationExistante = AffectationResponsableStructure::where('structure_id', $structure->id)
                    ->where('responsable_id', $agentResponsable->id)
                    ->where('date_affectation', $dateAffectation)
                    ->first();

                if (!$affectationExistante) {
                    AffectationResponsableStructure::create([
                        'structure_id' => $structure->id,
                        'responsable_id' => $agentResponsable->id,
                        'date_affectation' => $dateAffectation,
                        'date_fin_affectation' => null,
                        'poste' => 'Directeur',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    echo "Affectation responsable structure créée pour la structure (ID: " . $structure->id . ") et l'agent responsable (ID: " . $agentResponsable->id . ").\n";
                } else {
                    echo "Affectation responsable structure existante pour la structure (ID: " . $structure->id . ") et l'agent responsable (ID: " . $agentResponsable->id . ") à la date du " . $dateAffectation . ".\n";
                }
            } else {
                echo "Impossible de créer l'affectation : aucun agent trouvé pour l'utilisateur responsable.\n";
            }
        } else {
            echo "Impossible de créer l'affectation responsable structure : aucune structure ou responsable trouvé.\n";
        }
    }
}