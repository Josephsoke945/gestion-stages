<?php

namespace App\Mail;

use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class DemandeConfirmationMarkdown extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * La demande de stage associée à cet email.
     *
     * @var DemandeStage
     */
    public $demande;

    /**
     * L'utilisateur qui reçoit l'email.
     *
     * @var User
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param DemandeStage $demande La demande de stage
     * @param User $user L'utilisateur qui reçoit l'email
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
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Confirmation de demande de stage #' . $this->demande->code_suivi,
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
                'demande' => $this->demande,
                'user' => $this->user,
                'url' => url('/mes-demandes'),
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
