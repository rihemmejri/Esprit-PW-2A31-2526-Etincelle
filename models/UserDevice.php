<?php
// models/UserDevice.php
require_once __DIR__ . '/../config.php';

class UserDevice {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    /**
     * Générer une empreinte unique pour l'appareil
     */
    public function generateFingerprint() {
        $fingerprint = md5(
            ($_SERVER['HTTP_USER_AGENT'] ?? '') . 
            ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') . 
            ($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '')
        );
        return $fingerprint;
    }
    
    /**
     * Obtenir le nom de l'appareil à partir du User-Agent
     */
    public function getDeviceName() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($userAgent, 'Windows') !== false) {
            if (strpos($userAgent, 'Windows NT 10.0') !== false) return 'Windows 10/11';
            if (strpos($userAgent, 'Windows NT 6.1') !== false) return 'Windows 7';
            if (strpos($userAgent, 'Windows NT 6.2') !== false) return 'Windows 8';
            if (strpos($userAgent, 'Windows NT 6.3') !== false) return 'Windows 8.1';
            return 'Windows';
        }
        if (strpos($userAgent, 'Mac') !== false) return 'Mac OS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false) return 'iPhone';
        if (strpos($userAgent, 'iPad') !== false) return 'iPad';
        
        return 'Appareil inconnu';
    }
    
    /**
     * Vérifier si l'appareil est connu pour cet utilisateur
     */
    public function isKnownDevice($id_user, $fingerprint) {
        $sql = "SELECT * FROM user_devices WHERE id_user = :id_user AND device_fingerprint = :fingerprint";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_user' => $id_user,
            ':fingerprint' => $fingerprint
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Enregistrer un nouvel appareil
     */
    public function registerDevice($id_user, $fingerprint, $deviceName, $ip) {
        // Vérifier si existe déjà
        $existing = $this->isKnownDevice($id_user, $fingerprint);
        if ($existing) {
            // Mettre à jour last_used
            $sql = "UPDATE user_devices SET last_used = NOW(), ip_address = :ip WHERE id_device = :id_device";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':ip' => $ip,
                ':id_device' => $existing['id_device']
            ]);
            return $existing;
        }
        
        // Insérer nouveau device
        $sql = "INSERT INTO user_devices (id_user, device_fingerprint, device_name, ip_address, user_agent, first_seen, last_used) 
                VALUES (:id_user, :fingerprint, :device_name, :ip, :user_agent, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_user' => $id_user,
            ':fingerprint' => $fingerprint,
            ':device_name' => $deviceName,
            ':ip' => $ip,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        return false;
    }
}
?>