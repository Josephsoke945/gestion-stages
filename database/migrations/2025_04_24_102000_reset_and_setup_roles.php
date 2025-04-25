<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Supprimer toutes les tables liées aux rôles
        Schema::disableForeignKeyConstraints();
        
        if (Schema::hasTable('model_has_permissions')) {
            Schema::drop('model_has_permissions');
        }
        if (Schema::hasTable('model_has_roles')) {
            Schema::drop('model_has_roles');
        }
        if (Schema::hasTable('role_has_permissions')) {
            Schema::drop('role_has_permissions');
        }
        if (Schema::hasTable('permissions')) {
            Schema::drop('permissions');
        }
        if (Schema::hasTable('role_user')) {
            Schema::drop('role_user');
        }
        if (Schema::hasTable('roles')) {
            Schema::drop('roles');
        }

        Schema::enableForeignKeyConstraints();

        // 2. Créer la table des permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // 3. Créer la table des rôles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // 4. Créer la table pivot role_has_permissions
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // 5. Créer la table model_has_permissions
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        // 6. Créer la table model_has_roles
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        // 7. Insérer les rôles de base
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'agent',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'stagiaire',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        Schema::enableForeignKeyConstraints();
    }
}; 