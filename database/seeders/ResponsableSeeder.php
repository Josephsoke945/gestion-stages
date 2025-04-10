<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ResponsableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'jean.dupont@example.com')->exists()) {
            User::create([
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'date_de_naissance' => Carbon::create(1978, 11, 20),
                'sexe' => 'Homme',
                'adresse' => '12 Rue du Commerce, Cotonou',
                'email' => 'jean.dupont@example.com',
                'password' => Hash::make('secret123'),
                'telephone' => '+229 90 12 34 56',
                'date_d_inscription' => now(),
                'role' => 'Agent',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!User::where('email', 'amina.sokpon@example.com')->exists()) {
            User::create([
                'nom' => 'Sokpon',
                'prenom' => 'Amina',
                'date_de_naissance' => Carbon::create(1983, 6, 5),
                'sexe' => 'Femme',
                'adresse' => 'Lot 5, Quartier Agla, Cotonou',
                'email' => 'amina.sokpon@example.com',
                'password' => Hash::make('password456'),
                'telephone' => '+229 97 89 01 23',
                'role' => 'université',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Création d'un utilisateur avec le rôle 'Responsable'
        if (!User::where('email', 'responsable.structure@example.com')->exists()) {
            User::create([
                'nom' => 'Responsable',
                'prenom' => 'Structure',
                'date_de_naissance' => Carbon::create(1975, 3, 15),
                'sexe' => 'Homme',
                'adresse' => 'Quelque part à Cotonou',
                'email' => 'responsable.structure@example.com',
                'password' => Hash::make('resp123'),
                'telephone' => '+229 93 45 67 89',
                'role' => 'Responsable',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}