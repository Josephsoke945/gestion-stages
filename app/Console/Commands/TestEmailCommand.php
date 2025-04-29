<?php

namespace App\Console\Commands;

use App\Mail\DemandeConfirmationMarkdown;
use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi d\'email de confirmation de demande';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('À quelle adresse email voulez-vous envoyer le test?');
        }
        
        $this->info('Recherche d\'une demande de stage pour le test...');
        $demande = DemandeStage::first();
        
        if (!$demande) {
            $this->error('Aucune demande de stage trouvée pour le test!');
            return 1;
        }
        
        // Charger la relation structure pour l'email
        $demande->load('structure');
        
        $this->info('Recherche d\'un utilisateur pour le test...');
        $user = User::first();
        
        if (!$user) {
            $this->error('Aucun utilisateur trouvé pour le test!');
            return 1;
        }
        
        $this->info('Configuration email actuelle:');
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
            ]
        );
        
        $this->info("Envoi d'un email de test à {$email}...");
        
        try {
            Mail::to($email)->send(new DemandeConfirmationMarkdown($demande, $user));
            $this->info('✅ Email envoyé avec succès !');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi: ' . $e->getMessage());
            return 1;
        }
    }
} 