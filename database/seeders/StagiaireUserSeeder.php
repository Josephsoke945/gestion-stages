<?php

namespace Database\Seeders;

use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class StagiaireUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si un utilisateur stagiaire existe déjà
        $userStagiaire = User::where('role', 'stagiaire')->first();

        if (!$userStagiaire) {
            $userStagiaire = User::create([
                'nom' => 'Stagiaire',
                'prenom' => 'Un',
                'email' => 'stagiaire.un@example.com',
                'password' => Hash::make('password'),
                'role' => 'stagiaire',
                'date_de_naissance' => Carbon::create(2000, 1, 1),
                'date_d_inscription' => now(),
                'sexe' => 'Autre',
                'adresse' => 'Adresse par défaut',
                'telephone' => '+229 00 00 00 00',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Utilisateur stagiaire créé avec l'ID : " . $userStagiaire->id . "\n";
        } else {
            echo "Utilisateur stagiaire existant trouvé avec l'ID : " . $userStagiaire->id . "\n";
        }

        // Vérifier si un stagiaire lié à cet utilisateur existe déjà
        $stagiaire = Stagiaire::where('user_id', $userStagiaire->id)->first();

        if (!$stagiaire) {
            Stagiaire::create([
                'user_id' => $userStagiaire->id, // Lier le stagiaire à l'utilisateur
                'niveau_etude' => 'Licence', // Exemple de niveau d'étude
                'universite_id' => null, // Peut-être null par défaut ou vous pouvez lier à une université existante
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Stagiaire lié à l'utilisateur (ID: " . $userStagiaire->id . ") créé avec succès.\n";
        } else {
            echo "Un stagiaire est déjà lié à l'utilisateur (ID: " . $userStagiaire->id . ").\n";
        }
    }
}