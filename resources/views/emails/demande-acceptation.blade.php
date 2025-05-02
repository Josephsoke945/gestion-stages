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
            color: #2563eb;
            margin: 0;
        }
        .content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        .success-message {
            background-color: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
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
        <h1>Acceptation de votre demande de stage</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $nomUser }},</p>
        
        <div class="success-message">
            <p><strong>Félicitations ! Votre demande de stage a été acceptée.</strong></p>
        </div>
        
        <div class="details">
            <h3>Détails de votre stage :</h3>
            <ul>
                <li><strong>Structure d'accueil :</strong> {{ $structure }}</li>
                <li><strong>Date de début :</strong> {{ $dateDebut }}</li>
                <li><strong>Date de fin :</strong> {{ $dateFin }}</li>
                <li><strong>Code de suivi :</strong> {{ $codeSuivi }}</li>
            </ul>
        </div>

        <p>Prochaines étapes :</p>
        <ol>
            <li>Prenez connaissance des documents nécessaires pour votre stage</li>
            <li>Complétez et signez la convention de stage</li>
            <li>Contactez votre structure d'accueil pour les modalités pratiques</li>
        </ol>
        
        <p>Vous pouvez accéder à tous les détails de votre stage et aux documents nécessaires en cliquant sur le bouton ci-dessous :</p>
        
        <a href="{{ $url }}" class="button">Accéder à mon espace</a>
        
        <p>Nous vous souhaitons un excellent stage !</p>
        
        <p>Cordialement,<br>
        L'équipe des stages</p>
    </div>

    <div class="footer">
        <p>Cet e-mail est envoyé automatiquement, merci de ne pas y répondre.</p>
        <p>Si vous avez des questions, veuillez nous contacter via la plateforme.</p>
    </div>
</body>
</html> 