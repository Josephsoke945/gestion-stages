@component('mail::message')
# Confirmation de votre demande de stage

Bonjour {{ $user->prenom }} {{ $user->nom }},

Nous confirmons la réception de votre demande de stage. Votre demande est actuellement **{{ $demande->statut }}**.

## Votre code de suivi
@component('mail::panel')
**{{ $demande->code_suivi }}**

Conservez ce code précieusement pour suivre l'état de votre demande.
@endcomponent

## Détails de votre demande
**Structure :** {{ $demande->structure->libelle }}  
**Type :** {{ $demande->type }}  
**Nature :** {{ $demande->nature }}  
**Période :** Du {{ \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') }}  
**Statut :** {{ $demande->statut }}  
**Date soumission :** {{ \Carbon\Carbon::parse($demande->date_soumission)->format('d/m/Y H:i') }}  

Vous pouvez suivre l'état de votre demande à tout moment en vous connectant à votre espace personnel sur notre plateforme.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Consulter mes demandes
@endcomponent

Si vous avez des questions concernant votre demande, n'hésitez pas à nous contacter.

Cordialement,  
L'équipe de Gestion des Stages
@endcomponent 