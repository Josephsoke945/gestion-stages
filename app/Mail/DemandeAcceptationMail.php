<?php

namespace App\Mail;

use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeAcceptationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;
    public $user;
    public $codeSuivi;
    public $url;
    public $structure;
    public $dateDebut;
    public $dateFin;
    public $nomUser;

    /**
     * Create a new message instance.
     */
    public function __construct(DemandeStage $demande, User $user)
    {
        $this->demande = $demande;
        $this->user = $user;
        $this->codeSuivi = $demande->code_suivi;
        $this->url = route('mes.demandes');
        $this->structure = $demande->structure->libelle ?? 'Non spécifiée';
        $this->dateDebut = $demande->date_debut->format('d/m/Y');
        $this->dateFin = $demande->date_fin->format('d/m/Y');
        $this->nomUser = $user->nom . ' ' . $user->prenom;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acceptation de votre demande de stage - ' . $this->codeSuivi,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.demande-acceptation',
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
} 