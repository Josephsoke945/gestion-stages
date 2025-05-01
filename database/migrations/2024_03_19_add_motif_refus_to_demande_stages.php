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
            $table->text('motif_refus')->nullable()->after('statut');
            $table->timestamp('date_traitement')->nullable()->after('date_soumission');
            $table->foreignId('traite_par')->nullable()->after('date_traitement')
                ->constrained('agents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropForeign(['traite_par']);
            $table->dropColumn(['motif_refus', 'date_traitement', 'traite_par']);
        });
    }
}; 