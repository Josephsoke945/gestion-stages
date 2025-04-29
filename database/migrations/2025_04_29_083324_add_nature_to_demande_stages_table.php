<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNatureToDemandeStagesTable extends Migration
{
    public function up()
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->enum('nature', ['Individuel', 'Groupe'])->default('Individuel'); // Ajoute la colonne 'nature'
        });
    }

    public function down()
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropColumn('nature'); // Supprime la colonne 'nature' si la migration est annulée
        });
    }
}
