<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un utilisateur existant pour lier la notification
        $user = User::first();

        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => 'Ceci est un message de test pour la notification.',
                'date_envoi' => now(),
                'statut' => 'non_lu', // Vous pouvez choisir un statut approprié
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Notification de test créée.\n";
        } else {
            echo "Aucun utilisateur trouvé pour créer une notification.\n";
        }
    }
}