<?php

namespace Database\Seeders;

use App\Models\Stagiaire;
use App\Models\Structure;
use App\Models\DemandeStage; // Import du modèle DemandeStage
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemandeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer des enregistrements existants pour les clés étrangères
        $stagiaire1 = Stagiaire::first();
        $structure1 = Structure::first();
        $naturesPossibles = ['Individuel', 'Groupe', 'Stage conventionné', 'Autre'];

        if ($stagiaire1 && $structure1) {
            $demandesStagesData = [
                [
                    'stagiaire_id' => $stagiaire1->id,
                    'structure_id' => $structure1->id,
                    'nature' => $naturesPossibles[array_rand($naturesPossibles)],
                    'statut' => 'Soumise',
                    'date_soumission' => now()->subDays(5),
                    'structure_souhaitee' => $structure1->id,
                ],
                [
                    'stagiaire_id' => $stagiaire1->id,
                    'structure_id' => $structure1->id,
                    'nature' => $naturesPossibles[array_rand($naturesPossibles)],
                    'statut' => 'En attente',
                    'date_soumission' => now()->subDays(10),
                    'structure_souhaitee' => $structure1->id,
                ],
            ];

            foreach ($demandesStagesData as $demandeStageData) {
                // Utilisation de Eloquent pour créer les demandes de stage
                DemandeStage::create($demandeStageData);
            }
        }
    }
}