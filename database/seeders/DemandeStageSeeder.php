<?php

namespace Database\Seeders;

use App\Models\Stagiaire;
use App\Models\Structure;
use App\Models\NatureDemande;
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
        $natureIndividuel = NatureDemande::where('libelle', 'Individuel')->first();
        $natureEquipe = NatureDemande::where('libelle', 'En équipe')->first();

        if ($stagiaire1 && $structure1 && $natureIndividuel) {
            DB::table('demande_stages')->insert([
                'stagiaire_id' => $stagiaire1->id_stagiaire,
                'structure_id' => $structure1->id,
                'nature_demande_id' => $natureIndividuel->id,
                'statut' => 'Soumise',
                'date_soumission' => now()->subDays(5),
                'structure_souhaitee' => $structure1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($stagiaire1 && $structure1 && $natureEquipe) {
            DB::table('demande_stages')->insert([
                'stagiaire_id' => $stagiaire1->id_stagiaire,
                'structure_id' => $structure1->id,
                'nature_demande_id' => $natureEquipe->id,
                'statut' => 'En attente',
                'date_soumission' => now()->subDays(10),
                'structure_souhaitee' => $structure1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajouter d'autres demandes de stages si nécessaire en utilisant des IDs existants
    }
}