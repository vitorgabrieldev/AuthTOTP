<?php

namespace AuthTOTP;

use Sonata\GoogleAuthenticator\GoogleAuthenticator as SonataGoogleAuthenticator;

class GoogleAuthenticator
{
    private $googleAuthenticator;

    public function __construct()
    {
        $this->googleAuthenticator = new SonataGoogleAuthenticator();
    }

    public function generateQRCodeUrl($username, $hostname, $secret)
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode("otpauth://totp/$username@$hostname?secret=$secret") . '&size=200x200';
    }

    public function checkCode($secret, $code)
    {
        return $this->googleAuthenticator->checkCode($secret, $code);
    }

    public function getAuthHtml($username, $hostname, $secret)
    {
        $qrCodeUrl = $this->generateQRCodeUrl($username, $hostname, $secret);
        $verificationMessage = '';
        $inputCode = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
            $inputCode = $_POST['code'];
            $isValid = $this->checkCode($secret, $inputCode);

            if ($isValid) {
                $verificationMessage = 'Código válido!';
            } else {
                $verificationMessage = 'Código inválido. Tente novamente.';
            }
        }

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verificação de Código TOTP</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .container {
                    max-width: 400px;
                    margin: 0 auto;
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                h2 {
                    text-align: center;
                }
                img {
                    display: block;
                    margin: 0 auto;
                }
                input[type="text"] {
                    width: 100%;
                    padding: 10px;
                    margin: 10px 0;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }
                form {
                    margin-top: 40px;
                }
                button {
                    width: 100%;
                    padding: 10px;
                    background-color: #28a745;
                    border: none;
                    color: white;
                    border-radius: 4px;
                    cursor: pointer;
                }
                button:hover {
                    background-color: #218838;
                }
                .message {
                    text-align: center;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
        <div class="container">
            <h2>QR Code de Autenticação</h2>
            <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" />
            <form method="POST">
                <input type="text" name="code" placeholder="Digite o código" required value="<?php echo htmlspecialchars($inputCode); ?>">
                <button type="submit">Verificar</button>
            </form>
            <p class="message"><?php echo $verificationMessage; ?></p>
        </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
