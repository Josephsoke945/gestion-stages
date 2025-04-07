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
        Schema::create('agents', function (Blueprint $table) {
            $table->id('utilisateur_id'); // Utilise aussi comme clé étrangère vers utilisateurs
            $table->string('matricule')->unique();
            $table->foreignId('structure_id')->nullable()->constrained('structures')->onDelete('set null');
            $table->boolean('est_responsable_structure')->default(false);
            $table->boolean('est_dpa')->default(false); // DPAF = Agent aussi
            $table->boolean('est_maitre_stage')->default(false);
            $table->timestamps();

            // 🔗 Clé étrangère vers la table utilisateurs
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
