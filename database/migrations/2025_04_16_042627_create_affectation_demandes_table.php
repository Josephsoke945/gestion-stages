<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('affectation_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_dpaf_id')->constrained('agents')->onDelete('cascade');
            $table->foreignId('structure_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_responsable_id')->constrained('agents')->onDelete('cascade');
            $table->datetime('date_affectation');
            $table->string('statut')->default('en_attente'); // en_attente, traite, refuse
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('affectation_demandes');
    }
};