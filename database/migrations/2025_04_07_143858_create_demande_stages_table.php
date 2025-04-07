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
        Schema::create('demande_stages', function (Blueprint $table) {
            $table->id();

            // 🔗 Lien avec le stagiaire demandeur
            $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade');

            // 🔗 Type de demande : individuelle ou en équipe
            $table->foreignId('nature_demande_id')->constrained()->onDelete('restrict');

            // 🔗 Type de stage : académique ou professionnel
            $table->foreignId('type_demande_id')->constrained()->onDelete('restrict');

            // 🔗 Structure souhaitée (peut être null si le stagiaire ne précise pas)
            $table->foreignId('structure_id')->nullable()->constrained()->onDelete('set null');

            // 🔗 Université (si stage académique)
            $table->foreignId('universite_id')->nullable()->constrained('agents')->onDelete('set null');

            $table->date('date_debut');
            $table->date('date_fin');
            $table->text('motivation')->nullable();

            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_stages');
    }
};
