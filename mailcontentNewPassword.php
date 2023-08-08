<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .password-message {
            margin-bottom: 30px;
        }
        .password-label {
            font-weight: bold;
        }
        .password-value {
            font-size: 18px;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
        }
        @media (max-width: 576px) {
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-message">
            <p>Bonjour,</p>
            <p>Votre nouveau mot de passe est :</p>
            <p class="password-value">{{newPassword}}</p>
            <p>Nous vous recommandons de le changer dès que possible.</p>
        </div>
        <p class="signature">Cordialement,<br>Votre équipe.</p>
    </div>
</body>
</html>