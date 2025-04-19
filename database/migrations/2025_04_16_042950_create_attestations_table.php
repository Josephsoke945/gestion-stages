<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->string('code_suivi')->unique();
            $table->foreignId('stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivre_par_agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->date('date_rendez_vous')->nullable();
            $table->string('statut')->default('en_attente'); // en_attente, prete, remise
            $table->string('fichier_path')->nullable(); // Chemin vers le fichier généré
            $table->date('date_demande');
            $table->date('date_delivrance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attestations');
    }
};