<?php

require 'vendor/autoload.php';

use AuthTOTP\GoogleAuthenticator;

$username = 'Autenticação';
$hostname = 'Exemplo';
$secret = 'NTX7KO6OGGOIEKJ';

$googleAuthenticator = new GoogleAuthenticator();
$htmlContent = $googleAuthenticator->getAuthHtml($username, $hostname, $secret);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação TOTP</title>
</head>
<body>
    <div class="container">
        <?php echo $htmlContent; ?>
    </div>
</body>
</html>
