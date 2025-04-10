<?php

namespace Database\Seeders;

use App\Models\Universite;
use App\Models\Stagiaire;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UniversiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'Université A si elle n'existe pas déjà
        $universite1 = Universite::firstOrCreate(['nom_universite' => 'Université A'], ['adresse' => 'Adresse de l\'université A']);
        echo "Université A (ID: " . $universite1->id . ") créée ou existante.\n";

        // Créer l'Université B si elle n'existe pas déjà
        $universite2 = Universite::firstOrCreate(['nom_universite' => 'Université B'], ['adresse' => 'Adresse de l\'université B']);
        echo "Université B (ID: " . $universite2->id . ") créée ou existante.\n";

        // Lier des stagiaires à ces universités (en supposant que des stagiaires existent déjà)
        $stagiairesUniA = Stagiaire::take(2)->get(); // Récupérer les 2 premiers stagiaires
        foreach ($stagiairesUniA as $stagiaire) {
            $stagiaire->update(['universite_id' => $universite1->id]);
        }
        echo count($stagiairesUniA) . " stagiaires liés à l'Université A.\n";

        $stagiairesUniB = Stagiaire::skip(2)->take(2)->get(); // Récupérer les 2 stagiaires suivants
        foreach ($stagiairesUniB as $stagiaire) {
            $stagiaire->update(['universite_id' => $universite2->id]);
        }
        echo count($stagiairesUniB) . " stagiaires liés à l'Université B.\n";

        // Créer un utilisateur pour l'agent responsable de l'Université A s'il n'existe pas déjà
        $userAgentUniA = User::firstOrCreate(['email' => 'agent.a@example.com'], [
            'nom' => 'Agent',
            'prenom' => 'A',
            'password' => Hash::make('password'),
            'role' => 'Agent',
            'date_de_naissance' => Carbon::create(1985, 7, 15),
            'date_d_inscription' => now(),
            'sexe' => 'Homme',
            'adresse' => 'Adresse de l\'agent A',
            'telephone' => '+229 99 88 77 66',
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Utilisateur agent (ID: " . $userAgentUniA->id . ") créé ou existant pour l'université (ID: " . $universite1->id . ").\n";

        // Créer l'agent responsable lié à cet utilisateur s'il n'existe pas déjà
        $agentUniA = Agent::firstOrCreate(['user_id' => $userAgentUniA->id], [
            'matricule' => 'AG001',
            'fonction' => 'Responsable Universitaire',
            'date_embauche' => now(),
            'universite_responsable_id' => $universite1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Agent responsable (ID: " . $agentUniA->id . ") lié à l'utilisateur (ID: " . $userAgentUniA->id . ") et à l'université (ID: " . $universite1->id . ") créé ou existant.\n";
    }
}