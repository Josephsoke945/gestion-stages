<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->foreignId('structure_id')->constrained()->onDelete('cascade');
            $table->foreignId('propose_par_agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->foreignId('propose_par_stagiaire_id')->nullable()->constrained('stagiaires')->onDelete('set null');
            $table->boolean('est_valide')->default(false);
            $table->foreignId('stage_id')->nullable()->constrained()->onDelete('set null'); // Pour lier le thème à un stage
            $table->date('date_debut')->nullable(); // Date de début du travail sur ce thème
            $table->date('date_fin')->nullable(); // Date de fin du travail sur ce thème
            $table->string('statut')->default('propose'); // propose, en_cours, termine
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('themes');
    }
};