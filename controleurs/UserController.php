<?php
// controleurs/UserController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php';

class UserController {

    // Afficher la liste des utilisateurs
    public function listUsers() {
        $sql = "SELECT * FROM user ORDER BY id_user DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // Supprimer un utilisateur
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

    // Ajouter un utilisateur
    public function addUser(User $user) {
        $sql = "INSERT INTO user (nom, prenom, email, mot_de_passe, date_inscription, role, statut) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :date_inscription, :role, :statut)";
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

    // Modifier un utilisateur (sans modifier le mot de passe)
    public function updateUser(User $user, $id_user) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE user SET 
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    date_inscription = :date_inscription,
                    role = :role,
                    statut = :statut
                WHERE id_user = :id_user'
            );
            $query->execute([
                'id_user' => $id_user,
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'date_inscription' => $user->getDateInscription(),
                'role' => $user->getRole(),
                'statut' => $user->getStatut()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Modifier un utilisateur avec mot de passe
    public function updateUserWithPassword(User $user, $id_user, $new_password = null) {
        try {
            $db = config::getConnexion();
            
            if ($new_password) {
                $sql = 'UPDATE user SET 
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    mot_de_passe = :mot_de_passe,
                    date_inscription = :date_inscription,
                    role = :role,
                    statut = :statut
                WHERE id_user = :id_user';
                
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
                $sql = 'UPDATE user SET 
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    date_inscription = :date_inscription,
                    role = :role,
                    statut = :statut
                WHERE id_user = :id_user';
                
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
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Afficher un seul utilisateur
    public function showUser($id_user) {
        $sql = "SELECT * FROM user WHERE id_user = :id_user";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_user', $id_user);

        try {
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch(Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    // Trouver un utilisateur par email
    public function findUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode LOGIN
    public function login($email, $password) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }
    
    // Compter le nombre total d'utilisateurs
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
    
    // Rechercher des utilisateurs
    public function searchUsers($keyword) {
        $sql = "SELECT * FROM user WHERE nom LIKE :keyword OR prenom LIKE :keyword OR email LIKE :keyword";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['keyword' => '%' . $keyword . '%']);
        return $query;
    }
    
    // Changer le statut d'un utilisateur
    public function changeStatus($id_user, $statut) {
        $sql = "UPDATE user SET statut = :statut WHERE id_user = :id_user";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'id_user' => $id_user,
            'statut' => $statut
        ]);
        return true;
    }
}
?>