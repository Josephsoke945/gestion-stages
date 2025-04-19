<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('structure_universite_id')->nullable()->constrained('structures')->onDelete('set null');
            $table->string('niveau_etude')->nullable();
            $table->string('diplome')->nullable();
            $table->text('competences')->nullable();
            $table->boolean('est_demandeur')->default(false); // Pour indiquer si c'est le stagiaire qui a fait la demande
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stagiaires');
    }
};