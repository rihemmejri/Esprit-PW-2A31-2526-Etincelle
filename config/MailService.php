<?php
// config/MailService.php
// Service d'envoi d'emails avec code de vérification

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';

class MailService {
    
    // Configuration SMTP
    private static function getHost() { return getenv('SMTP_HOST') ?: 'smtp.gmail.com'; }
    private static function getPort() { return (int)(getenv('SMTP_PORT') ?: 587); }
    private static function getUser() { return getenv('SMTP_USER') ?: 'moemen.kochbati222@gmail.com'; }
    private static function getPass() { return getenv('SMTP_PASS') ?: 'wfdm xyth atwv qxbs'; }
    
    /**
     * Envoyer un code de vérification à 4 chiffres
     */
    public static function sendVerificationCode($toEmail, $toName, $code) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = self::getHost();
            $mail->SMTPAuth   = true;
            $mail->Username   = self::getUser();
            $mail->Password   = self::getPass();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::getPort();
            
            // Désactiver SSL verify (pour localhost)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Expéditeur et destinataire
            $mail->setFrom(self::getUser(), 'NutriLoop AI');
            $mail->addAddress($toEmail, $toName);
            
            // Contenu email
            $mail->isHTML(true);
            $mail->Subject = '🔐 Code de réinitialisation - NutriLoop';
            $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; background: #f5f7fa; padding: 20px; }
                    .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 20px; padding: 30px; text-align: center; }
                    .code { font-size: 32px; letter-spacing: 5px; background: #f0f2f5; padding: 15px; border-radius: 12px; font-weight: bold; margin: 20px 0; }
                    .btn { background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1>🍽️ NutriLoop AI</h1>
                    <h2>Réinitialisation du mot de passe</h2>
                    <p>Bonjour <strong>{$toName}</strong>,</p>
                    <p>Voici votre code de vérification :</p>
                    <div class='code'>{$code}</div>
                    <p>Ce code expirera dans <strong>10 minutes</strong>.</p>
                    <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
                    <hr>
                    <p style='font-size: 12px; color: #999;'>© 2025 NutriLoop AI</p>
                </div>
            </body>
            </html>
            ";
            
            $mail->AltBody = "Bonjour {$toName},\n\nVotre code de réinitialisation: {$code}\n\nCe code expirera dans 10 minutes.\n\nL'équipe NutriLoop";
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("MailService Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>