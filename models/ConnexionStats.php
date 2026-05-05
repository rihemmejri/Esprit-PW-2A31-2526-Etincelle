<?php
// models/ConnexionStats.php

require_once __DIR__ . '/../config.php';

class ConnexionStats {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    /**
     * Enregistrer une connexion pour un utilisateur
     */
    public function logConnexion($userId) {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        try {
            // Vérifier si une entrée existe déjà pour aujourd'hui
            $stmt = $this->db->prepare("SELECT * FROM connexion_stats WHERE user_id = ? AND connexion_date = ?");
            $stmt->execute([$userId, $today]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Incrémenter le compteur
                $stmt = $this->db->prepare("UPDATE connexion_stats 
                                           SET connexion_count = connexion_count + 1, 
                                               last_connexion = ? 
                                           WHERE user_id = ? AND connexion_date = ?");
                $stmt->execute([$now, $userId, $today]);
            } else {
                // Créer une nouvelle entrée
                $stmt = $this->db->prepare("INSERT INTO connexion_stats (user_id, connexion_date, connexion_count, last_connexion) 
                                           VALUES (?, ?, 1, ?)");
                $stmt->execute([$userId, $today, $now]);
            }
            return true;
        } catch(PDOException $e) {
            error_log("Erreur logConnexion: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les stats de connexion pour un utilisateur spécifique
     */
    public function getUserStats($userId, $period = 'month') {
        $stats = [];
        
        try {
            switch($period) {
                case 'week':
                    $sql = "SELECT connexion_date, connexion_count 
                           FROM connexion_stats 
                           WHERE user_id = ? 
                           AND connexion_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                           ORDER BY connexion_date DESC";
                    break;
                case 'month':
                    $sql = "SELECT connexion_date, connexion_count 
                           FROM connexion_stats 
                           WHERE user_id = ? 
                           AND connexion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                           ORDER BY connexion_date DESC";
                    break;
                case 'year':
                    $sql = "SELECT connexion_date, connexion_count 
                           FROM connexion_stats 
                           WHERE user_id = ? 
                           AND connexion_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)
                           ORDER BY connexion_date DESC";
                    break;
                default:
                    $sql = "SELECT connexion_date, connexion_count 
                           FROM connexion_stats 
                           WHERE user_id = ? 
                           ORDER BY connexion_date DESC LIMIT 30";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculer le total des connexions
            $total = 0;
            foreach($stats as $stat) {
                $total += $stat['connexion_count'];
            }
            
            return [
                'stats' => $stats,
                'total_connexions' => $total,
                'days_connected' => count($stats)
            ];
            
        } catch(PDOException $e) {
            error_log("Erreur getUserStats: " . $e->getMessage());
            return ['stats' => [], 'total_connexions' => 0, 'days_connected' => 0];
        }
    }
    
    /**
     * Obtenir les statistiques globales (pour admin)
     */
    public function getGlobalStats($period = 'month') {
        try {
            // Nombre total de connexions sur la période
            $sql = "SELECT COUNT(*) as total_connexions, 
                           COUNT(DISTINCT user_id) as total_users_connected,
                           SUM(connexion_count) as total_logins
                    FROM connexion_stats 
                    WHERE connexion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $global = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Top 10 des utilisateurs les plus actifs
            $sqlTop = "SELECT u.id_user, u.nom, u.prenom, u.email, u.role,
                              SUM(cs.connexion_count) as total_connexions,
                              COUNT(DISTINCT cs.connexion_date) as jours_connectes
                       FROM connexion_stats cs
                       JOIN user u ON cs.user_id = u.id_user
                       WHERE cs.connexion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                       GROUP BY u.id_user
                       ORDER BY total_connexions DESC
                       LIMIT 10";
            $stmtTop = $this->db->prepare($sqlTop);
            $stmtTop->execute();
            $topUsers = $stmtTop->fetchAll(PDO::FETCH_ASSOC);
            
            // Connexions par jour (pour le graphique)
            $sqlDaily = "SELECT connexion_date, SUM(connexion_count) as total_connexions
                        FROM connexion_stats 
                        WHERE connexion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY connexion_date
                        ORDER BY connexion_date ASC";
            $stmtDaily = $this->db->prepare($sqlDaily);
            $stmtDaily->execute();
            $dailyStats = $stmtDaily->fetchAll(PDO::FETCH_ASSOC);
            
            // Stats par rôle
            $sqlRole = "SELECT u.role, SUM(cs.connexion_count) as total_connexions
                       FROM connexion_stats cs
                       JOIN user u ON cs.user_id = u.id_user
                       WHERE cs.connexion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                       GROUP BY u.role";
            $stmtRole = $this->db->prepare($sqlRole);
            $stmtRole->execute();
            $roleStats = $stmtRole->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'global' => $global,
                'top_users' => $topUsers,
                'daily_stats' => $dailyStats,
                'role_stats' => $roleStats
            ];
            
        } catch(PDOException $e) {
            error_log("Erreur getGlobalStats: " . $e->getMessage());
            return [
                'global' => ['total_connexions' => 0, 'total_users_connected' => 0, 'total_logins' => 0],
                'top_users' => [],
                'daily_stats' => [],
                'role_stats' => []
            ];
        }
    }
    
    /**
     * Obtenir la moyenne de connexions par utilisateur
     */
    public function getAverageConnexions() {
        try {
            $sql = "SELECT AVG(connexion_count) as avg_connexions 
                    FROM connexion_stats 
                    WHERE connexion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return round($result['avg_connexions'] ?? 0, 2);
        } catch(PDOException $e) {
            return 0;
        }
    }
}
?>