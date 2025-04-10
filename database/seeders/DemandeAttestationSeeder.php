<?php

namespace Database\Seeders;

use App\Models\DemandeAttestation;
use App\Models\Stagiaire;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemandeAttestationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un stagiaire existant pour lier la demande d'attestation
        $stagiaire = Stagiaire::first();

        if ($stagiaire) {
            DemandeAttestation::create([
                'stagiaire_id' => $stagiaire->id,
                'type_attestation' => 'Fin de stage', // Vous pouvez choisir un type approprié
                'date_demande' => now(),
                'statut' => 'En attente', // Utilisation de la valeur par défaut de la migration
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Demande d'attestation de test créée.\n";
        } else {
            echo "Aucun stagiaire trouvé pour créer une demande d'attestation.\n";
        }
    }
}