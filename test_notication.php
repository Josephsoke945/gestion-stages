<?php

use App\Models\Utilisateur;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = Utilisateur::firstOrCreate(
    ['email' => 'test@example.com'],
    ['nom' => 'Testeur', 'prenom' => 'Jean', 'mot_de_passe' => bcrypt('password')]
);

$user->notifications()->create([
    'titre' => 'Bienvenue!',
    'message' => 'Votre compte a été bien créé',
]);

dd($user->notifications);
