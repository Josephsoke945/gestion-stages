<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un utilisateur avec le rôle 'Responsable'
        $responsable = User::where('role', 'Responsable')->first();

        $structures = [
            [
                'nom' => 'Entreprise A',
                'description' => 'Description de l\'entreprise A',
                'responsable_id' => $responsable ? $responsable->id : null, // Lier le responsable si trouvé
            ],
            [
                'nom' => 'Organisation B',
                'description' => 'Description de l\'organisation B',
                'responsable_id' => $responsable ? $responsable->id : null, // Lier le responsable si trouvé
            ],
            // Ajoutez d'autres structures si nécessaire
        ];

        foreach ($structures as $structure) {
            if (!DB::table('structures')->where('nom', $structure['nom'])->exists()) {
                DB::table('structures')->insert($structure);
            }
        }
    }
}