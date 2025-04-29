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
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_stage_id')->constrained('demande_stages')->onDelete('cascade');
            $table->foreignId('structure_id')->constrained('structures')->onDelete('cascade');
            $table->foreignId('theme_stage_id')->constrained('theme_stages')->onDelete('cascade');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['En cours', 'Terminé'])->default('En cours');
            $table->text('documents_stage')->nullable();
            $table->float('note')->nullable();
            $table->enum('type', ['academique', 'professionnel'])->nullable(); // Ajout de la colonne 'type'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};