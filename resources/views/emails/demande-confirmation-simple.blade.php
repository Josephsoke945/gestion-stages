<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation de demande de stage</title>
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
            border-bottom: 3px solid #4a5568;
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
        <h1>Confirmation de votre demande de stage</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $nomUser }},</p>
        
        <p>Nous vous confirmons que votre demande de stage a bien été enregistrée dans notre système.</p>
        
        <p>Votre code de suivi est :</p>
        
        <div class="code">{{ $codeSuivi }}</div>
        
        <p>Conservez précieusement ce code. Il vous permettra de suivre l'avancement de votre demande.</p>
        
        <p>Vous pouvez consulter votre demande à tout moment en vous connectant à votre espace personnel :</p>
        
        <a href="{{ $url }}" class="button">Voir ma demande</a>
        
        <p>Nous vous remercions pour votre confiance et reviendrons vers vous dans les plus brefs délais.</p>
        
        <p>Cordialement,<br>
        L'équipe des stages</p>
    </div>

    <div class="footer">
        <p>Cet e-mail est envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html> 