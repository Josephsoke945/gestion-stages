<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un email de test pour vérifier la configuration SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        
        $this->info('Envoi d\'un email de test à ' . $email);
        
        try {
            Mail::raw('Ceci est un email de test pour vérifier la configuration SMTP.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test de configuration email');
            });
            
            $this->info('Email envoyé avec succès!');
            
            $this->info('Configuration SMTP actuelle:');
            $this->info('Driver: ' . config('mail.default'));
            $this->info('Host: ' . config('mail.mailers.smtp.host'));
            $this->info('Port: ' . config('mail.mailers.smtp.port'));
            $this->info('Username: ' . config('mail.mailers.smtp.username'));
            $this->info('From Address: ' . config('mail.from.address'));
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
            $this->error('Vérifiez votre configuration SMTP dans le fichier .env');
            
            return 1;
        }
    }
}
