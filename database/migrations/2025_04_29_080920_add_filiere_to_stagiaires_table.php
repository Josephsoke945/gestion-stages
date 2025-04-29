<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiliereToStagiairesTable extends Migration
{
    public function up()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->string('filiere')->nullable(); // Ajoute la colonne 'filiere'
        });
    }

    public function down()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->dropColumn('filiere'); // Supprime la colonne 'filiere' si la migration est annulée
        });
    }
}
