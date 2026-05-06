<?php
// controleurs/AuthController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/MailService.php';

class AuthController {
    
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    /**
     * Nettoyer les entrées utilisateur
     */
    private function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Rediriger vers une page
     */
    private function redirect($path) {
        header("Location: ../views/FrontOffice/{$path}.php");
        exit();
    }
    
    /**
     * Définir un message flash
     */
    private function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
    
    /**
     * Trouver utilisateur par email
     */
    public function findUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Stocker le token de réinitialisation (code à 4 chiffres)
     */
    public function setPasswordResetToken($userId, $token, $expiry) {
        $sql = "UPDATE user SET reset_token = :token, reset_expires = :expiry WHERE id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':token' => $token,
            ':expiry' => $expiry,
            ':id_user' => $userId
        ]);
    }
    
    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword($userId, $hashedPassword) {
        $sql = "UPDATE user SET mot_de_passe = :password, reset_token = NULL, reset_expires = NULL WHERE id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id_user' => $userId
        ]);
    }
    
    /**
     * Demander réinitialisation (envoi code 4 chiffres)
     */
    public function requestPasswordReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('forgot_password');
            return;
        }
        
        $email = $this->sanitize($_POST['email'] ?? '');
        
        // Validation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'Veuillez entrer un email valide');
            $this->redirect('forgot_password');
            return;
        }
        
        // Chercher l'utilisateur
        $user = $this->findUserByEmail($email);
        if (!$user) {
            $this->setFlash('info', 'Si un compte existe avec cet email, vous recevrez un code de vérification.');
            $this->redirect('forgot_password');
            return;
        }
        
        // Générer code 4 chiffres
        $verificationCode = sprintf('%04d', random_int(1000, 9999));
        $codeExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Stocker dans DB
        $success = $this->setPasswordResetToken($user['id_user'], $verificationCode, $codeExpiry);
        
        if (!$success) {
            $this->setFlash('danger', 'Erreur technique, réessayez plus tard');
            $this->redirect('forgot_password');
            return;
        }
        
        // Envoyer email
        $emailSent = MailService::sendVerificationCode(
            $user['email'], 
            $user['prenom'] . ' ' . $user['nom'], 
            $verificationCode
        );
        
        if ($emailSent) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_user_id'] = $user['id_user'];
            $this->setFlash('success', '✅ Code de vérification envoyé à ' . htmlspecialchars($email) . '. Le code expire dans 10 minutes.');
            $this->redirect('verify-code');
        } else {
            $this->setFlash('danger', 'Erreur lors de l\'envoi de l\'email. Réessayez plus tard.');
            $this->redirect('forgot_password');
        }
    }
    
    /**
     * Vérifier le code saisi par l'utilisateur
     */
    public function verifyResetCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('forgot_password');
            return;
        }
        
        $code = $_POST['code'] ?? '';
        $email = $_SESSION['reset_email'] ?? '';
        
        if (empty($code) || empty($email)) {
            $this->setFlash('danger', 'Code invalide');
            $this->redirect('forgot_password');
            return;
        }
        
        $user = $this->findUserByEmail($email);
        
        if (!$user) {
            $this->setFlash('danger', 'Utilisateur non trouvé');
            $this->redirect('forgot_password');
            return;
        }
        
        // Vérifier le code
        if ($user['reset_token'] !== $code) {
            $this->setFlash('danger', '❌ Code de vérification invalide');
            $this->redirect('verify-code');
            return;
        }
        
        // Vérifier expiration
        if (strtotime($user['reset_expires']) < time()) {
            $this->setFlash('danger', '⏰ Le code de vérification a expiré');
            $this->redirect('forgot_password');
            return;
        }
        
        // Code valide
        $_SESSION['reset_verified'] = true;
        $_SESSION['reset_user_id'] = $user['id_user'];
        
        $this->setFlash('success', '✅ Code validé ! Veuillez entrer votre nouveau mot de passe.');
        $this->redirect('reset-password');
    }
    
    /**
     * Changer le mot de passe après validation
     */
    public function processResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('reset-password');
            return;
        }
        
        // Vérifier que l'utilisateur a validé le code
        if (!isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
            $this->setFlash('danger', 'Veuillez d\'abord vérifier votre code');
            $this->redirect('forgot_password');
            return;
        }
        
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $userId = $_SESSION['reset_user_id'] ?? null;
        
        if (!$userId) {
            $this->setFlash('danger', 'Session invalide');
            $this->redirect('forgot_password');
            return;
        }
        
        // Validation du mot de passe
        if (strlen($password) < 6) {
            $this->setFlash('danger', 'Le mot de passe doit contenir au moins 6 caractères');
            $this->redirect('reset-password');
            return;
        }
        
        if ($password !== $confirmPassword) {
            $this->setFlash('danger', 'Les mots de passe ne correspondent pas');
            $this->redirect('reset-password');
            return;
        }
        
        // Hasher et mettre à jour
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $success = $this->updatePassword($userId, $hashedPassword);
        
        if ($success) {
            // Nettoyer session
            unset($_SESSION['reset_verified'], $_SESSION['reset_user_id'], $_SESSION['reset_email']);
            
            $this->setFlash('success', '✅ Mot de passe réinitialisé avec succès ! Veuillez vous connecter.');
            $this->redirect('login');
        } else {
            $this->setFlash('danger', 'Erreur lors de la réinitialisation');
            $this->redirect('reset-password');
        }
    }
    
    /**
     * Exécuter l'action demandée
     */
    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'request':
                $this->requestPasswordReset();
                break;
            case 'verify':
                $this->verifyResetCode();
                break;
            case 'reset':
                $this->processResetPassword();
                break;
            default:
                $this->redirect('forgot_password');
        }
    }
}

// Exécuter si appelé directement
if (basename($_SERVER['SCRIPT_FILENAME']) == 'AuthController.php') {
    session_start();
    $controller = new AuthController();
    $controller->handleRequest();
}
?>