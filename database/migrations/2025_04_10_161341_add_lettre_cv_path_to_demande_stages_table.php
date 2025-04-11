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
            $table->string('lettre_cv_path')->nullable()->after('structure_souhaitee'); // Colonne pour stocker le chemin des fichiers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropColumn('lettre_cv_path');
        });
    }
};
