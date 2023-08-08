<?php
// inclure le fichier PHPMailerAutoload.php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    //Load Composer's autoloader
    require './vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
// configurer les paramètres du serveur SMTP de Gmail
 //Server settings
        
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'tiako1998@gmail.com';
$mail->Password = 'upbgrydcfzmadudw';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

// configurer les informations de l'expéditeur et du destinataire support-myeasylight@eneo.cm tiako1998@gmail.com pseffi@opensolutions-it.com
$mail->setFrom('support-myeasylight@eneo.com', 'MyEasyLight - Cameroon State Portal');
$mail->addAddress('ctiako@opensolutions-it.com', 'SEFFI LOUBARD');


$otp = rand(1000000, 9999999);

// ajouter le corps de l'email

 ob_start();
        require "mailcontent.php?otp=" . $otp;
        $body = ob_get_contents();
        ob_end_clean();
$mail->isHTML(true);    
$mail->Subject = 'Reinitialisation de mot de passe';
$mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
 <!-- Ajouter les liens vers les fichiers CSS et JS de Bootstrap -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<title>OTP</title>
</head>
<body>
<!-- Utiliser une div pour créer une disposition responsive -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Appliquer les classes Bootstrap aux éléments HTML -->
            <h1 class="text-center mb-4">Réinitialisation de mot de passe</h1>
            <p class="lead">Bonjour,</p>
            <p class="lead">Vous avez demandé une réinitialisation de votre mot de passe. Veuillez saisir le code de vérification ci-dessous :</p>
            <p class="text-center mb-4">
                <span class="badge bg-success fs-3 p-2"> '.$otp.' </span>
            </p>
            <p class="lead">Si vous n\'avez pas demandé de réinitialisation de mot de passe, ignorez simplement cet email.</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
';


//$mail->Body    = $body;
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

// envoyer l'email
if(!$mail->send()) {
    echo 'Erreur : ' . $mail->ErrorInfo;
} else {
    echo 'Email envoyé avec succès !';
}