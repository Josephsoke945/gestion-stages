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
        Schema::create('demande_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires', 'id_stagiaire')->onDelete('cascade'); // Spécifie la clé primaire 'id_stagiaire'
            $table->foreignId('structure_id')->nullable()->constrained('structures')->onDelete('set null'); // Clé étrangère vers structures.id
            $table->enum('nature', ['Individuel', 'Groupe',])->default('Individuel'); // Colonne enum pour la nature
            $table->string('statut')->default('En attente'); // Statut de la demande
            $table->timestamp('date_soumission')->useCurrent(); // Définit CURRENT_TIMESTAMP comme valeur par défaut
            $table->integer('structure_souhaitee')->nullable(); // ID de la structure souhaitée
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_stages');
    }
};
