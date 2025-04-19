<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->foreignId('demande_stage_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->dropForeign(['demande_stage_id']);
            $table->dropColumn('demande_stage_id');
        });
    }
};