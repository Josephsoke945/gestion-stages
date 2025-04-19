<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // demande_stage, affectation, evaluation, attestation
            $table->text('contenu');
            $table->string('lien')->nullable(); // Lien vers la ressource concernée
            $table->boolean('est_lu')->default(false);
            $table->datetime('date_lecture')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};