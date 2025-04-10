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
            $table->foreignId('stagiaire_id')->constrained('stagiaires', 'id')->onDelete('cascade'); // Clé étrangère vers stagiaires.id (après notre changement précédent)
            $table->foreignId('structure_id')->nullable()->constrained('structures')->onDelete('set null'); // Clé étrangère vers structures.id (nullable)
            // Suppression de la clé étrangère vers nature_demandes
            // $table->foreignId('nature_demande_id')->constrained('nature_demandes')->onDelete('cascade');
            $table->enum('nature', ['Individuel', 'Groupe', 'Stage conventionné', 'Autre'])->default('Individuel'); // Ajout d'une colonne enum pour la nature
            $table->string('statut')->default('En attente'); // Statut de la demande
            $table->date('date_soumission')->nullable();
            $table->integer('structure_souhaitee')->nullable(); // ID de la structure souhaitée (peut-être redondant avec structure_id ?)
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