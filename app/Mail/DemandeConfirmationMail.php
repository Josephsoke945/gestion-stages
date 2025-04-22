<?php

namespace App\Mail;

use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param DemandeStage $demande La demande de stage soumise
     * @param User $user L'utilisateur destinataire de l'email
     */
    public function __construct(DemandeStage $demande, User $user)
    {
        $this->demande = $demande;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre demande de stage - Code de suivi',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.demande-confirmation',
            with: [
                'demande' => $this->demande,
                'user' => $this->user,
                'url' => route('mes.demandes'),
            ],
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
