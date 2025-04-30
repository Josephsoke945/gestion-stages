<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->date('date_de_naissance')->nullable();
            $table->enum('sexe', ['Homme', 'Femme'])->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->unique(); // L'email est requis par défaut dans Laravel
            $table->string('password'); // Le mot de passe est requis par défaut dans Laravel
            $table->string('telephone')->nullable();
            $table->date('date_d_inscription')->nullable();
            $table->enum('role', ['stagiaire', 'agent', 'université', 'admin'])->default('stagiaire');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};