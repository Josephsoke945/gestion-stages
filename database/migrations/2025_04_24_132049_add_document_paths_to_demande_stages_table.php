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
            $table->string('lettre_cv_path')->nullable();
            $table->string('lettre_motivation_path')->nullable();
            $table->string('releve_notes_path')->nullable();
            $table->string('convention_stage_path')->nullable();
            $table->string('assurance_path')->nullable();
            $table->string('code_suivi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropColumn([
                'lettre_cv_path',
                'lettre_motivation_path',
                'releve_notes_path',
                'convention_stage_path',
                'assurance_path',
                'code_suivi'
            ]);
        });
    }
};
