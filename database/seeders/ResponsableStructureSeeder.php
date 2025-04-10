<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ResponsableStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $responsableEmail = 'responsable.structure@example.com';

        // Créer l'utilisateur responsable s'il n'existe pas
        $userResponsable = User::firstOrCreate(
            ['email' => $responsableEmail],
            [
                'nom' => 'Responsable',
                'prenom' => 'Structure',
                'date_de_naissance' => Carbon::create(1975, 3, 15),
                'sexe' => 'Homme',
                'adresse' => 'Quelque part à Cotonou',
                'password' => Hash::make('resp123'),
                'telephone' => '+229 93 45 67 89',
                'role' => 'Responsable',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        echo "Utilisateur responsable de structure créé ou existant (ID: " . $userResponsable->id . ").\n";

        // Créer l'agent correspondant s'il n'existe pas
        $agentResponsable = Agent::firstOrCreate(
            ['user_id' => $userResponsable->id],
            [
                'matricule' => 'RESP-001', // Vous pouvez changer cette matricule si nécessaire
                'fonction' => 'Responsable de Structure',
                'date_prise_fonction' => now()->subYears(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        echo "Agent responsable de structure créé ou existant (ID: " . $agentResponsable->id . ").\n";
    }
}