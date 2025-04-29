<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesToDemandeStagesTable extends Migration
{
    public function up()
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->date('date_debut')->nullable(); // Ajoute la colonne 'date_debut'
            $table->date('date_fin')->nullable();   // Ajoute la colonne 'date_fin'
        });
    }

    public function down()
    {
        Schema::table('demande_stages', function (Blueprint $table) {
            $table->dropColumn('date_debut'); // Supprime la colonne 'date_debut'
            $table->dropColumn('date_fin');   // Supprime la colonne 'date_fin'
        });
    }
}
