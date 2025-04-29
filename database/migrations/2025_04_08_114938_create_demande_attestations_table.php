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
        Schema::create('demande_attestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires', 'id_stagiaire')->onDelete('cascade'); // Spécifiez la clé primaire de la table stagiaires
            $table->date('date_demande')->nullable();
            $table->string('statut')->default('En attente');
            $table->text('motif_refus')->nullable();
            $table->date('date_validation')->nullable();
            $table->string('document_attestation')->nullable();
            $table->boolean('fait_demande_attestation')->default(false);
            $table->boolean('valide_attestation')->nullable();
            $table->boolean('refuse_attestation')->nullable();
            $table->boolean('generer_attestation_pdf')->default(false);
            $table->boolean('envoyer_attestation_email')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_attestations');
    }
};