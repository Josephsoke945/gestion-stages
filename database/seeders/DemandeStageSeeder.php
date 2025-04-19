<?php

namespace Database\Seeders;

use App\Models\Stagiaire;
use App\Models\Structure;
use App\Models\DemandeStage;
use Illuminate\Database\Seeder;

class DemandeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer qu'il y a au moins un stagiaire et une structure
        $stagiaire = Stagiaire::firstOrCreate(
            ['user_id' => 7], // Exemple : chercher le stagiaire avec l'ID utilisateur 1
            ['niveau_etude' => 'Licence 3']   // Si le stagiaire n'existe pas, le créer avec ces valeurs
        );
        $structure = Structure::firstOrCreate(
            ['nom' => 'Structure A'] // Exemple : chercher la structure avec le nom 'Structure A'
            // Pas de 'adresse' ici
        );

        $naturesPossibles = ['Individuel', 'Groupe', 'Stage conventionné', 'Autre'];
        $demandesStagesData = [
            [
                'stagiaire_id' => $stagiaire->id,
                'structure_id' => $structure->id,
                'nature' => $naturesPossibles[array_rand($naturesPossibles)],
                'statut' => 'Soumise',
                'date_soumission' => now()->subDays(5),
                'structure_souhaitee' => $structure->id,
            ],
            [
                'stagiaire_id' => $stagiaire->id,
                'structure_id' => $structure->id,
                'nature' => $naturesPossibles[array_rand($naturesPossibles)],
                'statut' => 'En attente',
                'date_soumission' => now()->subDays(10),
                'structure_souhaitee' => $structure->id,
            ],
        ];

        foreach ($demandesStagesData as $data) {
            DemandeStage::create($data);
        }
    }
}
