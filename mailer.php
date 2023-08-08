<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require './vendor/autoload.php';

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

// Configure the SMTP server settings
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'tiako1998@gmail.com';
$mail->Password = 'upbgrydcfzmadudw';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

// Set the sender and recipient information
$mail->setFrom('support-myeasylight@eneo.com', 'MyEasyLight - Cameroon State Portal');
$mail->addAddress('ctiako@opensolutions-it.com', 'SEFFI LOUBARD');

// Generate the OTP (One-Time Password)
$otp = generateOTP(); // Replace this with your actual OTP generation logic
echo $otp;
// Load the content of "mailcontent.php" and replace the placeholder with the OTP
ob_start();
require "mailcontent.php";
$body = ob_get_contents();
ob_end_clean();

// Replace the placeholder with the actual OTP in the body content
$body = str_replace('{{otp}}', $otp, $body);
echo $body;

$mail->isHTML(true);
$mail->Subject = 'Réinitialisation de mot de passe';
$mail->Body = $body;

// Send the email
if (!$mail->send()) {
    echo 'Erreur : ' . $mail->ErrorInfo;
} else {
    echo 'Email envoyé avec succès !';
}

// Example function to generate OTP (replace this with your actual OTP generation logic)
function generateOTP() {
    // Generate a random OTP (e.g., using random number generation or any other method you prefer)
    $otp = rand(100000, 999999);
    return $otp;
}
?>