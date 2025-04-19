<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('demande_stages', function (Blueprint $table) {
            $table->id();
            $table->string('code_suivi')->unique();
            $table->foreignId('stagiaire_demandeur_id')->constrained('stagiaires')->onDelete('cascade');
            $table->foreignId('structure_id')->constrained()->onDelete('cascade');
            $table->string('type_stage'); // academique, professionnel
            $table->string('nature_stage'); // individuel, equipe
            $table->date('date_debut_souhaitee');
            $table->date('date_fin_souhaitee');
            $table->string('niveau_etude');
            $table->string('cv')->nullable(); // chemin vers le fichier
            $table->string('lettre_motivation')->nullable(); // chemin vers le fichier
            $table->string('lettre_recommandation')->nullable(); // chemin vers le fichier (pour académique)
            $table->string('diplomes_attestations')->nullable(); // chemin vers le fichier (pour professionnel)
            $table->string('statut')->default('en_attente'); // en_attente, validee, refusee
            $table->text('motif_refus')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('demande_stages');
    }
};