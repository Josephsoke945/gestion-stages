<?php

namespace App\Mail;

use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeConfirmationMarkdown extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;
    public $user;
    public $url;

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
        $this->url = route('mes.demandes');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre demande de stage - Code de suivi: ' . $this->demande->code_suivi,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.demande-confirmation-markdown',
            with: [
                'url' => $this->url,
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