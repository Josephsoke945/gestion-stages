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
        Schema::create('affectation_maitre_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('stages')->onDelete('cascade');
            $table->foreignId('maitre_stage_id')->constrained('users')->onDelete('cascade'); // Le maître de stage est un utilisateur (probablement un agent)
            $table->date('date_affectation')->nullable();
            $table->string('statut')->default('En cours'); // Statut de l'affectation (En cours, Terminé, ...)
            $table->text('motif_refus')->nullable();
            $table->timestamps();

            $table->unique(['stage_id', 'maitre_stage_id']); // Un maître de stage ne peut être affecté qu'une seule fois à un stage
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectation_maitre_stages');
    }
};