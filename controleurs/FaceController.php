<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class FaceController {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    // Vérifier si un visage existe déjà (retourne user_id ou false)
    private function findFace($descriptor) {
        $users = $this->db->query("SELECT u.id_user, u.nom, u.prenom, u.email, u.role, f.face_descriptor 
                                   FROM user u 
                                   JOIN face_data f ON u.id_user = f.user_id")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            $stored = json_decode($user['face_descriptor'], true);
            $similarity = $this->cosineSimilarity($descriptor, $stored);
            if ($similarity > 0.6) {
                return $user;
            }
        }
        return null;
    }
    
    // Créer un nouveau compte automatiquement
    private function createAutoAccount($descriptor, $nom, $prenom, $email) {
        // Générer mot de passe aléatoire
        $tempPassword = bin2hex(random_bytes(6)); // 12 caractères
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
        
        // Vérifier si email existe déjà
        $check = $this->db->prepare("SELECT id_user FROM user WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $email = strtolower($prenom . '.' . $nom . rand(100, 999) . '@nutriloop.tn');
        }
        
        // Insérer user
        $stmt = $this->db->prepare("INSERT INTO user (nom, prenom, email, mot_de_passe, date_inscription, role, statut) 
                                     VALUES (?, ?, ?, ?, NOW(), 'USER', 'actif')");
        $stmt->execute([$nom, $prenom, $email, $hashedPassword]);
        $userId = $this->db->lastInsertId();
        
        // Enregistrer le visage
        $stmt2 = $this->db->prepare("INSERT INTO face_data (user_id, face_descriptor) VALUES (?, ?)");
        $stmt2->execute([$userId, json_encode($descriptor)]);
        
        // Envoyer email avec mot de passe
        $this->sendWelcomeEmail($email, $prenom, $tempPassword);
        
        return ['id_user' => $userId, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'role' => 'USER'];
    }
    
    private function sendWelcomeEmail($to, $name, $tempPassword) {
        $subject = "Bienvenue sur NutriLoop - Vos identifiants de connexion";
        $message = "Bonjour $name,\n\n";
        $message .= "Votre compte a été créé automatiquement via la reconnaissance faciale.\n\n";
        $message .= "Email: $to\n";
        $message .= "Mot de passe temporaire: $tempPassword\n\n";
        $message .= "Connectez-vous ici: http://localhost:8000/views/FrontOffice/login.php\n\n";
        $message .= "Après connexion, vous pouvez modifier votre mot de passe dans votre profil.\n\n";
        $message .= "Cordialement,\nL'équipe NutriLoop";
        
        mail($to, $subject, $message);
    }
    
    // Action principale: scanner + auto-login ou auto-register
    public function autoLoginOrRegister() {
        $input = json_decode(file_get_contents('php://input'), true);
        $descriptor = $input['face_descriptor'];
        $nom = $input['nom'] ?? 'Utilisateur';
        $prenom = $input['prenom'] ?? 'Face';
        $email = $input['email'] ?? null;
        
        if (!$email) {
            $email = strtolower($prenom . '.' . $nom . '@temp.nutriloop.tn');
        }
        
        // Chercher si visage existe déjà
        $existingUser = $this->findFace($descriptor);
        
        if ($existingUser) {
            // Connexion directe
            session_start();
            $_SESSION['user'] = $existingUser;
            echo json_encode(['success' => true, 'user' => $existingUser, 'new_account' => false]);
            return;
        }
        
        // Créer nouveau compte
        $newUser = $this->createAutoAccount($descriptor, $nom, $prenom, $email);
        session_start();
        $_SESSION['user'] = $newUser;
        echo json_encode(['success' => true, 'user' => $newUser, 'new_account' => true, 'email_sent' => true]);
    }
    
    private function cosineSimilarity($vec1, $vec2) {
        $dot = $mag1 = $mag2 = 0;
        for ($i=0; $i<count($vec1); $i++) {
            $dot += $vec1[$i] * $vec2[$i];
            $mag1 += $vec1[$i] * $vec1[$i];
            $mag2 += $vec2[$i] * $vec2[$i];
        }
        return $mag1 && $mag2 ? $dot / (sqrt($mag1)*sqrt($mag2)) : 0;
    }
    
    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        if ($action === 'auto') {
            $this->autoLoginOrRegister();
        }
    }
}

$controller = new FaceController();
$controller->handleRequest();
?>