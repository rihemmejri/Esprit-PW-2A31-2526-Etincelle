<?php
// controleurs/NotificationController.php
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../config.php';

class NotificationController {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    /**
     * Créer une notification pour un utilisateur spécifique
     */
    public function createNotification($id_user, $type, $title, $message, $icon = null, $color = null) {
        $icons = [
            'login' => 'fa-sign-in-alt',
            'signup' => 'fa-user-plus',
            'suspect' => 'fa-shield-alt',
            'new_device' => 'fa-laptop',
            'new_recipe' => 'fa-utensils',
            'recipe_deleted' => 'fa-trash',
            'recipe_updated' => 'fa-edit',
            'recipe_reported' => 'fa-flag',
            'welcome' => 'fa-smile-wink'
        ];
        
        $colors = [
            'login' => '#4CAF50',
            'signup' => '#2196F3',
            'suspect' => '#FF9800',
            'new_device' => '#9C27B0',
            'new_recipe' => '#4CAF50',
            'recipe_deleted' => '#dc2626',
            'recipe_updated' => '#2196F3',
            'recipe_reported' => '#FF9800',
            'welcome' => '#4CAF50'
        ];
        
        $icon = $icon ?? ($icons[$type] ?? 'fa-bell');
        $color = $color ?? ($colors[$type] ?? '#4CAF50');
        
        $sql = "INSERT INTO notifications (id_user, type, title, message, icon, color, created_at) 
                VALUES (:id_user, :type, :title, :message, :icon, :color, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_user' => $id_user,
            ':type' => $type,
            ':title' => $title,
            ':message' => $message,
            ':icon' => $icon,
            ':color' => $color
        ]);
    }
    
    /**
     * Récupérer les notifications d'un utilisateur
     */
    public function getUserNotifications($id_user, $limit = 50, $onlyUnread = false) {
        $sql = "SELECT * FROM notifications WHERE id_user = :id_user";
        if ($onlyUnread) $sql .= " AND is_read = 0";
        $sql .= " ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Compter les notifications non lues
     */
    public function countUnread($id_user) {
        $sql = "SELECT COUNT(*) as total FROM notifications WHERE id_user = :id_user AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id_notification, $id_user) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id_notification = :id_notification AND id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_notification' => $id_notification, ':id_user' => $id_user]);
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead($id_user) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id_user = :id_user AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_user' => $id_user]);
    }
    
    /**
     * NOTIFICATION : Uniquement pour les ADMINS
     */
    public function notifyAdminsOnly($type, $title, $message) {
        $sql = "SELECT id_user FROM user WHERE role = 'ADMIN' AND statut = 'actif'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($admins as $admin) {
            $this->createNotification($admin['id_user'], $type, $title, $message);
        }
        
        return count($admins);
    }
    
    /**
     * NOTIFICATION : Tentative de connexion suspecte (uniquement admins)
     */
    public function notifySuspiciousLogin($email, $ip, $userName = null) {
        $title = "🚨 TENTATIVE DE HACK DÉTECTÉE";
        $message = "Plusieurs tentatives de connexion échouées pour l'email: {$email} depuis l'IP: {$ip}";
        if ($userName) {
            $message = "Utilisateur: {$userName} - " . $message;
        }
        
        return $this->notifyAdminsOnly('suspect', $title, $message);
    }
    
    /**
     * NOTIFICATION : Nouvel appareil détecté (uniquement admins)
     */
    public function notifyNewDeviceDetected($userName, $email, $deviceName, $ip) {
        $title = "⚠️ NOUVEL APPAREIL DÉTECTÉ";
        $message = "L'utilisateur {$userName} ({$email}) s'est connecté depuis un nouvel appareil : {$deviceName} (IP: {$ip})";
        
        return $this->notifyAdminsOnly('new_device', $title, $message);
    }
    
    /**
     * NOTIFICATION : Nouvel utilisateur inscrit (uniquement admins)
     */
    public function notifyNewUserSignup($prenom, $nom, $email) {
        $title = "📝 NOUVEL UTILISATEUR INSCRIT";
        $message = "Un nouvel utilisateur vient de s'inscrire : {$prenom} {$nom} ({$email})";
        
        return $this->notifyAdminsOnly('signup', $title, $message);
    }
    
    /**
     * NOTIFICATION : Nouvelle recette ajoutée (uniquement admins)
     */
    public function notifyNewRecipeToAdmins($recipeTitre, $addedBy) {
        $title = "🍽️ NOUVELLE RECETTE AJOUTÉE";
        $message = "Une nouvelle recette a été ajoutée : \"{$recipeTitre}\" par {$addedBy}";
        
        return $this->notifyAdminsOnly('new_recipe', $title, $message);
    }
    
    /**
     * NOTIFICATION : Recette supprimée (uniquement admins)
     */
    public function notifyRecipeDeletedToAdmins($recipeTitre, $deletedBy) {
        $title = "🗑️ RECETTE SUPPRIMÉE";
        $message = "La recette \"{$recipeTitre}\" a été supprimée par {$deletedBy}";
        
        return $this->notifyAdminsOnly('recipe_deleted', $title, $message);
    }
    
    /**
     * NOTIFICATION : Recette signalée (uniquement admins)
     */
    public function notifyRecipeReportedToAdmins($recipeTitre, $reportedBy, $reason) {
        $title = "⚠️ RECETTE SIGNALÉE";
        $message = "La recette \"{$recipeTitre}\" a été signalée par {$reportedBy}. Raison : {$reason}";
        
        return $this->notifyAdminsOnly('recipe_reported', $title, $message);
    }
    
    /**
     * API: Récupérer les notifications
     */
    public function apiGetNotifications() {
        session_start();
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non authentifié']);
            return;
        }
        
        $id_user = $_SESSION['user']['id_user'];
        $role = $_SESSION['user']['role'];
        
        // Seuls les admins peuvent voir les notifications
        if ($role !== 'ADMIN') {
            echo json_encode(['success' => true, 'unread_count' => 0, 'notifications' => []]);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'unread_count' => $this->countUnread($id_user),
            'notifications' => $this->getUserNotifications($id_user, 50)
        ]);
    }
    
    /**
     * API: Marquer comme lue
     */
    public function apiMarkAsRead() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id_notification = $data['id_notification'] ?? null;
        
        if ($id_notification) {
            $this->markAsRead($id_notification, $_SESSION['user']['id_user']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    
    /**
     * API: Tout marquer comme lu
     */
    public function apiMarkAllAsRead() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }
        
        $this->markAllAsRead($_SESSION['user']['id_user']);
        echo json_encode(['success' => true]);
    }
}
?>