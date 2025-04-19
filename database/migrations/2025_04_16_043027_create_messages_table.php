<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediteur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stage_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('contenu');
            $table->string('fichier_path')->nullable(); // Pour les pièces jointes
            $table->timestamps();
        });

        Schema::create('message_destinataires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade');
            $table->boolean('est_lu')->default(false);
            $table->datetime('date_lecture')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_destinataires');
        Schema::dropIfExists('messages');
    }
};