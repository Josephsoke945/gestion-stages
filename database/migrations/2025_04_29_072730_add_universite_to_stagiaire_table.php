<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->string('universite')->nullable(); // Ajoute la colonne 'universite'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('stagiaires', function (Blueprint $table) {
        $table->dropColumn('universite'); // Supprime la colonne 'universite' si la migration est annulée
    });
}
};    
