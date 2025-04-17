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
        Schema::table('stagiaires', function (Blueprint $table) {
            // Ajouter la colonne niveau_etude
            $table->string('niveau_etude', 50)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            // Supprimer la colonne niveau_etude si elle existe
            $table->dropColumn('niveau_etude');
        });
    }
};