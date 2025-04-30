<?php
use App\Models\User;

// Créer un utilisateur administrateur
User::create([
    'nom' => 'Admin',
    'prenom' => 'Super',
    'email' => 'admin@gmail.com',
    'password' => bcrypt('password123'), // Remplacez par un mot de passe sécurisé
    'role' => 'admin', // Assurez-vous que le champ "role" existe dans votre table "users"
]);