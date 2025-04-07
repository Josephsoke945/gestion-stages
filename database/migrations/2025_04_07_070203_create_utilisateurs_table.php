<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_de_naissance')->nullable();
            $table->enum('sexe', ['Homme', 'Femme'])->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('mot_de_passe');
            $table->date('date_de_inscription')->nullable();
            $table->enum('role', ['stagiaire', 'agent', 'secretaire', 'maitre_stage', 'chef_stage']);
            $table->boolean('is_authentifie')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('utilisateurs');
    }
};

?>
