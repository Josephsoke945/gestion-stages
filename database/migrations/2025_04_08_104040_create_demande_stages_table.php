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
            $table->foreignId('stagiaire_id')->constrained('stagiaires', 'id_stagiaire')->onDelete('cascade'); // Clé étrangère vers stagiaires.id_stagiaire
            $table->foreignId('structure_id')->nullable()->constrained('structures')->onDelete('set null'); // Clé étrangère vers structures.id (nullable)
            $table->foreignId('nature_demande_id')->constrained('nature_demandes')->onDelete('cascade'); // Clé étrangère vers nature_demandes.id
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