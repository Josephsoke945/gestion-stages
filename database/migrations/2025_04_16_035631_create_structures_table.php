<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('type'); // ministere, universite, sous_service
            $table->text('description')->nullable();
            $table->boolean('est_dpaf')->default(false); // Pour identifier la structure DPAF
            $table->foreignId('structure_parent_id')->nullable()->constrained('structures')->onDelete('cascade'); // Pour les sous-services
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('structures');
    }
};