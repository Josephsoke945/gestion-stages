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
        Schema::create('affectation_maitre_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stage_id');
            $table->unsignedBigInteger('agent_id');
            $table->boolean('valide')->default(false);
            $table->text('motif')->nullable();
            $table->unsignedBigInteger('modifie_par')->nullable();
            $table->boolean('termine')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('modifie_par')->references('id')->on('utilisateur')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectation_maitre_stages');
    }
};
