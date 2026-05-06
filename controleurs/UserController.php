<?php
// controleurs/UserController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ConnexionStats.php';
require_once __DIR__ . '/../config.php';

class UserController {

    public function listUsers() {
        $sql = "SELECT * FROM user ORDER BY id_user DESC";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteUser($id_user) {
        $sql = "DELETE FROM user WHERE id_user = :id_user";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_user', $id_user);
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addUser(User $user) {
        $sql = "INSERT INTO user (nom, prenom, email, mot_de_passe, date_inscription, role, statut, failed_attempts, is_locked, is_banned) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :date_inscription, :role, :statut, 0, 0, 0)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'mot_de_passe' => password_hash($user->getMotDePasse(), PASSWORD_DEFAULT),
                'date_inscription' => $user->getDateInscription(),
                'role' => $user->getRole(),
                'statut' => $user->getStatut()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function updateUser(User $user, $id_user) {
        try {
            $db = config::getConnexion();
            $sql = "UPDATE user SET nom = :nom, prenom = :prenom, email = :email, role = :role, statut = :statut WHERE id_user = :id_user";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':nom' => $user->getNom(),
                ':prenom' => $user->getPrenom(),
                ':email' => $user->getEmail(),
                ':role' => $user->getRole(),
                ':statut' => $user->getStatut(),
                ':id_user' => $id_user
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateUser: " . $e->getMessage());
            return false;
        }
    }

    public function updateUserWithPassword(User $user, $id_user, $new_password = null) {
        try {
            $db = config::getConnexion();
            if ($new_password) {
                $sql = 'UPDATE user SET nom = :nom, prenom = :prenom, email = :email, mot_de_passe = :mot_de_passe, date_inscription = :date_inscription, role = :role, statut = :statut WHERE id_user = :id_user';
                $query = $db->prepare($sql);
                $query->execute([
                    'id_user' => $id_user,
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'mot_de_passe' => password_hash($new_password, PASSWORD_DEFAULT),
                    'date_inscription' => $user->getDateInscription(),
                    'role' => $user->getRole(),
                    'statut' => $user->getStatut()
                ]);
            } else {
                $sql = 'UPDATE user SET nom = :nom, prenom = :prenom, email = :email, date_inscription = :date_inscription, role = :role, statut = :statut WHERE id_user = :id_user';
                $query = $db->prepare($sql);
                $query->execute([
                    'id_user' => $id_user,
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'date_inscription' => $user->getDateInscription(),
                    'role' => $user->getRole(),
                    'statut' => $user->getStatut()
                ]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error updateUserWithPassword: " . $e->getMessage());
            return false;
        }
    }

    public function showUser($id_user) {
        $sql = "SELECT * FROM user WHERE id_user = :id_user";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_user', $id_user);
        try {
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    public function findUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // ========== FONCTIONS DE VERROUILLAGE (par EMAIL) ==========
    
    public function isAccountLocked($email) {
        $user = $this->findUserByEmail($email);
        if (!$user) return false;
        return isset($user['is_locked']) && $user['is_locked'] == 1;
    }
    
    private function lockAccountByEmail($email) {
        $sql = "UPDATE user SET is_locked = 1, locked_at = NOW(), failed_attempts = 3 WHERE email = :email";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }
    
    public function unlockAccountByEmail($email) {
        $sql = "UPDATE user SET is_locked = 0, locked_at = NULL, failed_attempts = 0 WHERE email = :email";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }
    
    public function getLockedAccounts() {
        $sql = "SELECT * FROM user WHERE is_locked = 1 ORDER BY locked_at DESC";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ========== FONCTIONS DE BANNISSEMENT ==========
    
    public function banUserByEmail($email, $adminName, $reason = null) {
        $user = $this->findUserByEmail($email);
        if (!$user) return false;
        
        $reasonText = $reason ?: "Violation des conditions d'utilisation";
        
        $sql = "UPDATE user SET is_banned = 1, banned_at = NOW(), banned_reason = :reason, banned_by = :admin WHERE email = :email";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':reason' => $reasonText,
            ':admin' => $adminName,
            ':email' => $email
        ]);
        
        if ($result) {
            $this->createNotification(
                $user['id_user'],
                'user_banned',
                '🚫 Compte banni',
                "Votre compte a été banni par l'administrateur {$adminName}. Raison : {$reasonText}\n\nContactez l'administration pour plus d'informations."
            );
            $this->notifyAllAdmins(
                'user_banned',
                '🚫 Utilisateur banni',
                "L'administrateur {$adminName} a banni l'utilisateur {$user['prenom']} {$user['nom']} ({$email}) pour : {$reasonText}"
            );
        }
        
        return $result;
    }
    
    public function unbanUserByEmail($email, $adminName) {
        $user = $this->findUserByEmail($email);
        if (!$user) return false;
        
        $sql = "UPDATE user SET is_banned = 0, banned_at = NULL, banned_reason = NULL, banned_by = NULL WHERE email = :email";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([':email' => $email]);
        
        if ($result) {
            $this->createNotification(
                $user['id_user'],
                'user_unbanned',
                '✅ Compte débanni',
                "Votre compte a été débanni par l'administrateur {$adminName}. Vous pouvez maintenant vous reconnecter."
            );
            $this->notifyAllAdmins(
                'user_unbanned',
                '✅ Utilisateur débanni',
                "L'administrateur {$adminName} a débanni l'utilisateur {$user['prenom']} {$user['nom']} ({$email})"
            );
        }
        
        return $result;
    }
    
    public function isUserBanned($email) {
        $user = $this->findUserByEmail($email);
        if (!$user) return false;
        return isset($user['is_banned']) && $user['is_banned'] == 1;
    }
    
    public function getBannedUsers() {
        $sql = "SELECT * FROM user WHERE is_banned = 1 ORDER BY banned_at DESC";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ========== FONCTIONS DE NOTIFICATION ==========
    
    private function createNotification($id_user, $type, $title, $message, $lien = null) {
        $sql = "INSERT INTO notifications (id_user, type, title, message, lien, created_at) 
                VALUES (:id_user, :type, :title, :message, :lien, NOW())";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id_user' => $id_user,
            ':type' => $type,
            ':title' => $title,
            ':message' => $message,
            ':lien' => $lien
        ]);
    }
    
    private function notifyAllAdmins($type, $title, $message, $lien = null) {
        $sql = "SELECT id_user FROM user WHERE role = 'ADMIN' AND statut = 'actif'";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($admins as $admin) {
            $this->createNotification($admin['id_user'], $type, $title, $message, $lien);
        }
        return count($admins);
    }
    
    private function registerFailedAttempt($email, $ip) {
        $user = $this->findUserByEmail($email);
        
        if ($user) {
            $currentAttempts = $user['failed_attempts'] ?? 0;
            $failedAttempts = $currentAttempts + 1;
            
            $sql = "UPDATE user SET failed_attempts = :failed_attempts WHERE email = :email";
            $db = config::getConnexion();
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':failed_attempts' => $failedAttempts,
                ':email' => $email
            ]);
            
            if ($failedAttempts >= 3) {
                $this->lockAccountByEmail($email);
                $lien = '../BackOffice/locked_accounts.php';
                $this->notifyAllAdmins(
                    'account_locked',
                    '🔒 COMPTE VERROUILLÉ',
                    "Le compte de {$user['prenom']} {$user['nom']} ({$user['email']}) a été verrouillé après 3 tentatives de connexion échouées depuis l'IP: {$ip}",
                    $lien
                );
            }
        }
        
        $sql = "INSERT INTO connexion_logs (email, success, ip_address, attempt_time) VALUES (:email, 0, :ip, NOW())";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email, ':ip' => $ip]);
    }
    
    private function resetFailedAttempts($email) {
        $sql = "UPDATE user SET failed_attempts = 0 WHERE email = :email";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
    }

    // ========== LOGIN PRINCIPAL ==========
    
    public function login($email, $password) {
        $user = $this->findUserByEmail($email);
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        if (!$user) {
            $_SESSION['login_error'] = "Email ou mot de passe incorrect";
            return false;
        }
        
        // Vérifier si l'utilisateur est banni
        if ($this->isUserBanned($email)) {
            $reason = $user['banned_reason'] ?? "Violation des conditions d'utilisation";
            $_SESSION['login_error'] = "🚫 COMPTE BANNI !\n\nVotre compte a été banni.\nRaison : {$reason}\n\nContactez l'administration pour plus d'informations.";
            return false;
        }
        
        if ($this->isAccountLocked($email)) {
            $_SESSION['login_error'] = "🔒 COMPTE VERROUILLE !\n\nContactez un administrateur pour débloquer votre compte.";
            return false;
        }
        
        if (password_verify($password, $user['mot_de_passe'])) {
            $this->resetFailedAttempts($email);
            
            $stats = new ConnexionStats();
            $stats->logConnexion($user['id_user']);
            
            return $user;
        } else {
            $currentAttempts = $user['failed_attempts'] ?? 0;
            $newAttempts = $currentAttempts + 1;
            $remaining = 3 - $newAttempts;
            
            $this->registerFailedAttempt($email, $ip);
            
            if ($newAttempts >= 3) {
                $_SESSION['login_error'] = "❌ COMPTE VERROUILLE !\n\n3 tentatives échouées.\nVotre compte est maintenant verrouillé.\nContactez un administrateur pour le débloquer.";
            } else {
                $_SESSION['login_error'] = "❌ Mot de passe incorrect\n\nTentative {$newAttempts}/3\n⚠️ Plus que {$remaining} tentative(s) avant verrouillage définitif.";
            }
            
            return false;
        }
    }
    
    public function countUsers() {
        $sql = "SELECT COUNT(*) as total FROM user";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            return $data['total'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function searchUsers($keyword) {
        $sql = "SELECT * FROM user WHERE nom LIKE :keyword OR prenom LIKE :keyword OR email LIKE :keyword";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['keyword' => '%' . $keyword . '%']);
        return $query;
    }

    public function changeStatus($id_user, $statut) {
        $sql = "UPDATE user SET statut = :statut WHERE id_user = :id_user";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id_user' => $id_user, 'statut' => $statut]);
        return true;
    }
    
    public function adminUnlockAccountByEmail($email, $adminName) {
        $user = $this->findUserByEmail($email);
        if (!$user) return false;
        
        $this->unlockAccountByEmail($email);
        
        $this->createNotification(
            $user['id_user'],
            'account_unlocked',
            '🔓 Compte déverrouillé',
            "L'administrateur {$adminName} a déverrouillé votre compte. Vous pouvez maintenant vous reconnecter."
        );
        
        $this->notifyAllAdmins(
            'account_unlocked',
            '🔓 Compte déverrouillé par admin',
            "L'administrateur {$adminName} a déverrouillé le compte de {$user['prenom']} {$user['nom']} ({$email})"
        );
        
        return true;
    }
}
?>