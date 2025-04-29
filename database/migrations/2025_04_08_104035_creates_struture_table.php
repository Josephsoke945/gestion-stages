<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('responsable_id')
                ->nullable()
                ->constrained('users') // Pointe vers la table 'users'
                ->onDelete('set null'); // Si l'utilisateur est supprimé, la valeur est mise à null
            $table->string('sigle')->nullable();
            $table->string('libelle')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structures');
    }
};