<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->enum('nature', ['Individuel', 'Groupe'])->default('Individuel');
            $table->enum('type', ['Académique', 'Professionnelle'])->default('Académique');
        });
    }

    public function down(): void
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropColumn(['nature', 'type']);
        });
    }
};
