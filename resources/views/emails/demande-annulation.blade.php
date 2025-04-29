<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Annulation de demande de stage</title>
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
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 3px solid #e53e3e;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            margin-top: 20px;
        }
        .code {
            display: inline-block;
            background-color: #edf2f7;
            font-family: monospace;
            padding: 10px 15px;
            margin: 10px 0;
            font-weight: bold;
            border-radius: 4px;
            font-size: 16px;
            color: #2d3748;
            border: 1px solid #cbd5e0;
        }
        .alert {
            background-color: #fed7d7;
            border-left: 4px solid #e53e3e;
            padding: 15px;
            margin: 15px 0;
            color: #c53030;
        }
        .button {
            display: inline-block;
            background-color: #4a5568;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Annulation de votre demande de stage</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $nomUser }},</p>
        
        <div class="alert">
            <p><strong>Votre demande de stage a été annulée avec succès.</strong></p>
        </div>
        
        <p>Nous vous confirmons que votre demande de stage avec le code de suivi suivant a été annulée :</p>
        
        <div class="code">{{ $codeSuivi }}</div>
        
        <p><strong>Détails de la demande :</strong></p>
        <ul>
            <li>Structure demandée : {{ $structure }}</li>
            <li>Date de la demande : {{ $dateDemande }}</li>
            <li>Date d'annulation : {{ $dateAnnulation }}</li>
        </ul>
        
        <p>Si vous souhaitez soumettre une nouvelle demande, vous pouvez le faire à tout moment depuis votre espace personnel :</p>
        
        <a href="{{ $url }}" class="button">Accéder à mon espace</a>
        
        <p>Si vous n'êtes pas à l'origine de cette annulation, veuillez nous contacter immédiatement.</p>
        
        <p>Cordialement,<br>
        L'équipe des stages</p>
    </div>

    <div class="footer">
        <p>Cet e-mail est envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html> 