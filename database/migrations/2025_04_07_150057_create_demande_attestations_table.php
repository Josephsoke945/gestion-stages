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
            $table->unsignedBigInteger('stagiaire_id');
            $table->string('type_attestation');
            $table->string('etat')->default('en attente');
            $table->text('motif')->nullable();
            $table->unsignedBigInteger('cree_par');
            $table->unsignedBigInteger('modifie_par')->nullable();
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('stagiaire_id')->references('id')->on('stagiaires')->onDelete('cascade');
            $table->foreign('cree_par')->references('id')->on('utilisateur')->onDelete('cascade');
            $table->foreign('modifie_par')->references('id')->on('utilisateur')->onDelete('set null');
            $table->foreign('valide_par')->references('id')->on('utilisateur')->onDelete('set null');
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
