<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Agent;

class AssignRoles extends Command
{
    protected $signature = 'roles:assign';
    protected $description = 'Assigne les rôles aux utilisateurs existants';

    public function handle()
    {
        $this->info('Début de l\'assignation des rôles...');

        // Assigner le rôle stagiaire aux utilisateurs qui ont un stagiaire associé
        $stagiaires = Stagiaire::with('user')->get();
        foreach ($stagiaires as $stagiaire) {
            if ($stagiaire->user) {
                $stagiaire->user->assignRole('stagiaire');
                $this->info("Rôle 'stagiaire' assigné à {$stagiaire->user->email}");
            }
        }

        // Assigner le rôle agent aux utilisateurs qui ont un agent associé
        $agents = Agent::with('user')->get();
        foreach ($agents as $agent) {
            if ($agent->user) {
                $agent->user->assignRole('agent');
                $this->info("Rôle 'agent' assigné à {$agent->user->email}");
            }
        }

        $this->info('Assignation des rôles terminée !');
    }
} 