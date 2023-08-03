
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
	<style> .container{ font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; border-radius: 5px; width: 80%; margin: 0 auto; } h1{ font-size: 24px; color: #333; text-align: center; margin-bottom: 20px; } p{ font-size: 16px; color: #555; text-align: justify; } .verification-code{ font-size: 20px; color: #007bff; text-align: center; margin-top: 30px; margin-bottom: 50px; } </style>
</head>
<body>
	<?php
$otp = rand(100000, 999999);
?>
 <div class="container"> <h1>Réinitialisation de mot de passe</h1> <p>Bonjour,</p> <p>Vous avez demandé une réinitialisation de votre mot de passe. Veuillez saisir le code de vérification ci-dessous :</p> <p class="verification-code"> <?=$otp?> </p> <p>Si vous n\'avez pas demandé de réinitialisation de mot de passe, ignorez simplement cet email.</p> </div>
</body>
</html>