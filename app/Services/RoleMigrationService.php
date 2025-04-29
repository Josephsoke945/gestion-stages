<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class RoleMigrationService
{
    /**
     * Migrer tous les utilisateurs vers le nouveau système de rôles
     */
    public function migrateAllUsers(): int
    {
        $count = 0;
        
        // Vérifier si la colonne 'role' existe encore dans la table 'users'
        if (Schema::hasColumn('users', 'role')) {
            Log::info('Début de la migration des utilisateurs vers le nouveau système de rôles');
            
            try {
                // Obtenir tous les utilisateurs avec leur ancien rôle
                $users = DB::table('users')->select(['id', 'role'])->get();
                
                foreach ($users as $user) {
                    $this->migrateUserRole($user->id, $user->role);
                    $count++;
                }
                
                Log::info("Migration des rôles terminée. $count utilisateurs migrés.");
            } catch (\Exception $e) {
                Log::error('Erreur lors de la migration des rôles : ' . $e->getMessage());
                throw $e;
            }
        } else {
            Log::info('La colonne role n\'existe plus dans la table users. Pas de migration nécessaire.');
        }
        
        return $count;
    }
    
    /**
     * Migrer un utilisateur spécifique vers le nouveau système de rôles
     */
    public function migrateUserRole(int $userId, string $oldRole): bool
    {
        try {
            $user = User::findOrFail($userId);
            
            // Convertir l'ancien rôle en nouveau format si nécessaire
            $roleName = $this->normalizeRoleName($oldRole);
            
            // Vérifier si le rôle existe
            $role = Role::where('name', $roleName)->first();
            
            if (!$role) {
                // Créer le rôle s'il n'existe pas
                $role = Role::create([
                    'name' => $roleName,
                    'display_name' => ucfirst($roleName),
                    'description' => 'Rôle créé automatiquement lors de la migration',
                ]);
            }
            
            // Assigner le rôle à l'utilisateur
            $user->assignRole($role);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la migration du rôle pour l'utilisateur $userId : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Normaliser le nom du rôle
     */
    private function normalizeRoleName(string $roleName): string
    {
        // Convertir les noms de rôle vers un format standard si nécessaire
        switch (strtolower($roleName)) {
            case 'admin':
            case 'administrateur':
                return 'admin';
            case 'stagiaire':
            case 'etudiant':
            case 'élève':
                return 'stagiaire';
            case 'agent':
            case 'responsable':
                return 'agent';
            default:
                return strtolower($roleName);
        }
    }
} 