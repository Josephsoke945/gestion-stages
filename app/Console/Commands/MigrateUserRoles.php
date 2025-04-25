<?php

namespace App\Console\Commands;

use App\Services\RoleMigrationService;
use Illuminate\Console\Command;

class MigrateUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:migrate-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrer les utilisateurs de l\'ancien système de rôles (champ enum) vers le nouveau (table de rôles)';

    /**
     * Execute the console command.
     */
    public function handle(RoleMigrationService $migrationService)
    {
        $this->info('Début de la migration des rôles utilisateurs...');
        
        try {
            $count = $migrationService->migrateAllUsers();
            
            $this->info("Migration terminée avec succès. $count utilisateurs ont été migrés.");
        } catch (\Exception $e) {
            $this->error('Erreur lors de la migration : ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
