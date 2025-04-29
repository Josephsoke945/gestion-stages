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
        Schema::table('demande_stages', function (Blueprint $table) {
            // Ajouter la colonne sans contrainte unique
            $table->string('code_suivi')->nullable()->after('lettre_cv_path');
        });

        // Remplir la colonne avec des valeurs uniques
        \App\Models\DemandeStage::all()->each(function ($demande) {
            $demande->code_suivi = \Illuminate\Support\Str::uuid(); // Génère un UUID unique
            $demande->save();
        });

        // Ajouter la contrainte unique après avoir rempli les valeurs
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->string('code_suivi')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropUnique(['code_suivi']);
            $table->dropColumn('code_suivi');
        });
    }
};
