<?php

namespace App\Mail;

use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeAnnulationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;
    public $user;
    public $codeSuivi;
    public $url;

    /**
     * Create a new message instance.
     *
     * @param DemandeStage $demande La demande de stage annulée
     * @param User $user L'utilisateur destinataire de l'email
     */
    public function __construct(DemandeStage $demande, User $user)
    {
        $this->demande = $demande;
        $this->user = $user;
        $this->codeSuivi = $demande->code_suivi;
        $this->url = route('mes.demandes');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Annulation de votre demande de stage - ' . $this->codeSuivi,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.demande-annulation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.demande-annulation')
            ->with([
                'nomUser' => $this->user->nom . ' ' . $this->user->prenom,
                'codeSuivi' => $this->codeSuivi,
                'structure' => $this->demande->structure->libelle ?? 'Non spécifiée',
                'dateDemande' => $this->demande->date_soumission->format('d/m/Y'),
                'dateAnnulation' => now()->format('d/m/Y à H:i'),
                'url' => $this->url
            ]);
    }
} 