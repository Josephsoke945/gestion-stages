<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des rôles par défaut
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrateur',
                'description' => 'Administrateur du système avec tous les droits',
            ],
            [
                'name' => 'stagiaire',
                'display_name' => 'Stagiaire',
                'description' => 'Utilisateur pouvant soumettre des demandes de stage',
            ],
            [
                'name' => 'agent',
                'display_name' => 'Agent',
                'description' => 'Agent responsable des stages dans une structure',
            ],
            // Vous pouvez ajouter d'autres rôles ici
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
