<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'intitule' => 'Développement d\'une application web avec Laravel',
                'etat' => 'Validé',
                'description' => 'Conception et développement d\'une application web en utilisant le framework PHP Laravel.',
                'mots_cles' => 'Laravel, PHP, Web, Application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'intitule' => 'Analyse de données avec Python et Pandas',
                'etat' => 'Validé',
                'description' => 'Collecte, nettoyage et analyse de données en utilisant Python et la librairie Pandas.',
                'mots_cles' => 'Python, Pandas, Analyse de données, Data Science',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'intitule' => 'Introduction à l\'intelligence artificielle',
                'etat' => 'Proposé',
                'description' => 'Présentation des concepts fondamentaux de l\'intelligence artificielle et du machine learning.',
                'mots_cles' => 'IA, Intelligence Artificielle, Machine Learning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($themes as $theme) {
            if (!DB::table('theme_stages')->where('intitule', $theme['intitule'])->exists()) {
                DB::table('theme_stages')->insert($theme);
            }
        }
    }
}