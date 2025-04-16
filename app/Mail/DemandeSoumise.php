<?php
namespace App\Mail;
use App\Models\DemandeStage;

class DemandeSoumise
{
    public $demande;

    public function __construct(DemandeStage $demande)
    {
        $this->demande = $demande;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre demande de stage')
                    ->view('emails.demande_soumise');
    }
}
