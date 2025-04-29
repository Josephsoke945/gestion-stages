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
        Schema::table('affectation_responsable_structures', function (Blueprint $table) {
            $table->foreignId('id_demande_stages')->nullable()->constrained('demande_stages')->onDelete('cascade');
            $table->foreignId('responsable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affectation_responsable_structures', function (Blueprint $table) {
            $table->dropForeign(['id_demande_stages']);
            $table->dropColumn('id_demande_stages');
            $table->foreignId('responsable_id')->nullable(false)->change();
        });
    }
};
