<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->string('diplomes_path')->nullable()->after('lettre_cv_path');
        });
    }

    public function down()
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropColumn('diplomes_path');
        });
    }
}; 