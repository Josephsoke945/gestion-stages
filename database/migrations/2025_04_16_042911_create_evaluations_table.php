<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_evaluateur_id')->constrained('agents')->onDelete('cascade');
            $table->integer('note')->nullable();
            $table->boolean('est_assidu')->default(true);
            $table->boolean('est_ponctuel')->default(true);
            $table->text('commentaire')->nullable();
            $table->date('date_evaluation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};