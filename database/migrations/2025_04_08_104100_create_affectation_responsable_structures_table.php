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
        Schema::create('affectation_responsable_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('structure_id')->constrained('structures')->onDelete('cascade');
            $table->foreignId('responsable_id')->constrained('agents')->onDelete('cascade');
            $table->date('date_affectation')->nullable();
            $table->date('date_fin_affectation')->nullable();
            $table->string('poste')->nullable();
            $table->timestamps();

            $table->unique(['structure_id', 'responsable_id', 'date_affectation'], 'unique_resp_struct_affectation'); // Utilisation de date_affectation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
     {
        Schema::dropIfExists('affectation_responsable_structures');
    }
};