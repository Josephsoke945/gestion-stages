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
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id('id_stagiaire');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users
            $table->string('niveau_etude');
            $table->foreignId('universite_id')->nullable()->constrained('universites')->onDelete('set null');
            $table->timestamps();

            $table->unique('user_id'); // Assurez-vous qu'un utilisateur ne soit lié qu'à un seul enregistrement stagiaire (si c'est la logique métier)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};