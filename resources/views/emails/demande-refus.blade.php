<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc2626;
            margin: 0;
        }
        .content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        .alert-message {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .details {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Réponse à votre demande de stage</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $nomUser }},</p>
        
        <div class="alert-message">
            <p><strong>Nous regrettons de vous informer que votre demande de stage n'a pas pu être acceptée.</strong></p>
        </div>
        
        <div class="details">
            <h3>Rappel de votre demande :</h3>
            <ul>
                <li><strong>Structure demandée :</strong> {{ $structure }}</li>
                <li><strong>Période demandée :</strong> du {{ $dateDebut }} au {{ $dateFin }}</li>
                <li><strong>Code de suivi :</strong> {{ $codeSuivi }}</li>
            </ul>
        </div>

        @if($motifRefus)
        <div class="details">
            <h3>Motif du refus :</h3>
            <p>{{ $motifRefus }}</p>
        </div>
        @endif
        
        <p>Vous pouvez soumettre une nouvelle demande de stage en tenant compte des éléments suivants :</p>
        <ul>
            <li>Vérifier la disponibilité des places dans d'autres structures</li>
            <li>Proposer des dates alternatives</li>
            <li>S'assurer que votre dossier est complet</li>
        </ul>
        
        <p>Pour soumettre une nouvelle demande, vous pouvez accéder à votre espace personnel :</p>
        
        <a href="{{ $url }}" class="button">Accéder à mon espace</a>
        
        <p>Nous vous remercions de votre compréhension.</p>
        
        <p>Cordialement,<br>
        L'équipe des stages</p>
    </div>

    <div class="footer">
        <p>Cet e-mail est envoyé automatiquement, merci de ne pas y répondre.</p>
        <p>Si vous avez des questions, veuillez nous contacter via la plateforme.</p>
    </div>
</body>
</html> 