<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('affectation_maitre_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_responsable_id')->constrained('agents')->onDelete('cascade');
            $table->foreignId('maitre_stage_id')->constrained('agents')->onDelete('cascade');
            $table->datetime('date_affectation');
            $table->boolean('est_actif')->default(true); // Pour gérer le cas où un maître de stage serait remplacé
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('affectation_maitre_stages');
    }
};