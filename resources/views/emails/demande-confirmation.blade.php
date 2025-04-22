<x-mail::message>
# Confirmation de votre demande de stage

Bonjour **{{ $user->prenom }} {{ $user->nom }}**,

Nous vous confirmons la bonne réception de votre demande de stage.

## Votre code de suivi
<div style="text-align: center; margin: 25px 0;">
    <div style="display: inline-block; background-color: #f0f9ff; border: 2px solid #0ea5e9; border-radius: 8px; padding: 15px 25px; font-family: monospace; font-size: 20px; font-weight: bold; color: #0284c7;">
        {{ $demande->code_suivi }}
    </div>
</div>

## Détails de votre demande
- **Type de demande :** {{ $demande->type }}
- **Nature :** {{ $demande->nature }}
- **Structure demandée :** {{ $demande->structure->libelle }}
- **Période :** du {{ \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') }}

Vous pouvez suivre l'état de votre demande en vous connectant à votre espace personnel et en consultant la page de suivi des demandes.

<x-mail::button :url="$url" color="primary">
Suivre mes demandes
</x-mail::button>

Nous vous remercions de votre confiance et vous tiendrons informé de l'évolution de votre demande.

Cordialement,<br>
{{ config('app.name') }}

<div style="margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center;">
    <p>Ce message est généré automatiquement, merci de ne pas y répondre.</p>
    <p>Si vous n'êtes pas à l'origine de cette demande, veuillez nous contacter immédiatement.</p>
</div>
</x-mail::message>
