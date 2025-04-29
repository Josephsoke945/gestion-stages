<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NatureDemandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $natures = [
            ['libelle' => 'Individuel'],
            ['libelle' => 'En équipe'],
            // Ajoutez d'autres natures de demandes si nécessaire
        ];

        foreach ($natures as $nature) {
            if (!DB::table('nature_demandes')->where('libelle', $nature['libelle'])->exists()) {
                DB::table('nature_demandes')->insert([
                    'libelle' => $nature['libelle'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}