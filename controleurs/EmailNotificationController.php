<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/AlertController.php';

require_once __DIR__ . '/../PHPMailer/PHPMailer-6.9.1/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer-6.9.1/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer-6.9.1/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $host;
    private $username;
    private $password;
    private $port;
    private $fromEmail;
    private $fromName;

    public function __construct() {
        $this->host = $_ENV['SMTP_HOST'] ?? 'smtp-relay.brevo.com';
        $this->username = $_ENV['SMTP_USER'] ?? '';
        $this->password = $_ENV['SMTP_PASS'] ?? '';
        $this->port = 587;
        $this->fromEmail = $_ENV['SMTP_FROM'] ?? 'no-reply@nutriloop.com';
        $this->fromName = 'NutriLoop AI';
    }

    public function sendEmail($to, $subject, $message)
    {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->port;
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}

class EmailNotificationController
{
    private $emailService;
    private $alertController;

    public function __construct()
    {
        $this->emailService = new EmailService();
        $this->alertController = new AlertController();
    }

    /**
     * Obtenir l'email de l'utilisateur
     */
    private function getUserEmail($userId)
    {
        $db = Config::getConnexion();
        $stmt = $db->prepare("SELECT email FROM user WHERE id_user = :id");
        $stmt->execute(['id' => $userId]);
        $res = $stmt->fetch();
        return $res ? $res['email'] : null;
    }

    /**
     * Vérifie si un email a déjà été envoyé aujourd'hui
     */
    public function hasEmailBeenSentToday($userId)
    {
        $db = Config::getConnexion();
        $date = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) FROM alert WHERE user_id = :id AND categorie = 'EMAIL_SENT' AND DATE(date) = :date");
        $stmt->execute(['id' => $userId, 'date' => $date]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Marquer l'email comme envoyé
     */
    private function markEmailAsSent($userId, $subject, $message)
    {
        $alert = new Alert($userId, 'INFO', 'EMAIL_SENT', "Email envoyé: $subject", date('Y-m-d H:i:s'));
        $this->alertController->addAlert($alert);
    }

    /**
     * Obtenir l'historique des 3 derniers scores
     */
    private function getLast3Scores($userId)
    {
        $db = Config::getConnexion();
        $stmt = $db->prepare("SELECT score, calories_consommees, date FROM score_journalier WHERE user_id = :id ORDER BY date DESC, id DESC LIMIT 3");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Process daily trigger
     */
    public function processDailyScore($userId, $score, $eau_bue, $objectif_eau)
    {
        // Temporarily disabled for testing so you can receive multiple emails!
        // if ($this->hasEmailBeenSentToday($userId)) {
        //     return false; // Already sent
        // }

        $email = $this->getUserEmail($userId);
        if (!$email) return false;

        $subject = null;
        $message = null;

        $history = $this->getLast3Scores($userId);

        // Check Rule 4: 3 BAD DAYS
        if (count($history) === 3 && $history[0]['score'] < 70 && $history[1]['score'] < 70 && $history[2]['score'] < 70) {
            $subject = "🚨 Action requise";
            $message = "Vous avez dépassé votre objectif 3 jours de suite. Il est temps de se recentrer.";
        }
        // Check Rule 5: IMPROVEMENT (decreasing consumption 3 days)
        elseif (count($history) === 3 && 
                $history[0]['calories_consommees'] < $history[1]['calories_consommees'] && 
                $history[1]['calories_consommees'] < $history[2]['calories_consommees']) {
            $subject = "📈 Superbe progrès !";
            $message = "Vous vous améliorez chaque jour. Continuez sur cette lancée !";
        }
        // Rule 1: PERFECT DAY
        elseif ($score >= 100 && $eau_bue >= $objectif_eau) {
            $subject = "🔥 Journée Parfaite !";
            $message = "Excellent travail ! Vous avez respecté vos objectifs de calories et d'hydratation aujourd'hui. Continuez comme ça !";
        }
        // Rule 2: MEDIUM / WARNING (If score is between 70 and 100, or perfect score but bad hydration)
        elseif ($score >= 70) {
            $subject = "⚠️ Bilan de votre journée";
            $message = "Votre journée a été correcte, mais attention à bien respecter tous vos objectifs (calories et hydratation). Vous ferez mieux demain !";
        }
        // Rule 3: BAD DAY
        elseif ($score < 70) {
            $subject = "😬 Objectif dépassé";
            $message = "Vous avez dépassé votre objectif de calories aujourd'hui. Reprenons les bonnes habitudes demain.";
        }

        if ($subject && $message) {
            $sent = $this->emailService->sendEmail($email, $subject, $message);
            if ($sent) {
                $this->markEmailAsSent($userId, $subject, $message);
                return true;
            }
        }

        return false;
    }

    /**
     * Process NO INPUT trigger
     * Checks if it's past 20:00 and no suivi exists for today
     */
    public function processNoInputTrigger($userId)
    {
        // Only run after 20:00
        if (date('H') < 20) {
            return false;
        }

        if ($this->hasEmailBeenSentToday($userId)) {
            return false;
        }

        // Check if there is a suivi for today
        $db = Config::getConnexion();
        $date = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) FROM suivi WHERE user_id = :id AND date = :date");
        $stmt->execute(['id' => $userId, 'date' => $date]);
        $hasSuivi = $stmt->fetchColumn() > 0;

        if (!$hasSuivi) {
            $email = $this->getUserEmail($userId);
            
            if ($email) {
                $subject = "👀 N'oubliez pas votre suivi";
                $message = "Vous avez oublié d'enregistrer votre journée. Une petite mise à jour avant de dormir ?";
                
                $sent = $this->emailService->sendEmail($email, $subject, $message);
                if ($sent) {
                    $this->markEmailAsSent($userId, $subject, $message);
                    return true;
                }
            }
        }

        return false;
    }
}
?>
