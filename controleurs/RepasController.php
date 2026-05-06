<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../models/repas.php');

// Vérifier si la classe n'existe pas déjà
if (!class_exists('RepasController')) {
    class RepasController
    {
        // ========== AJOUTER UN REPAS ==========
        public function addRepas(repas $repas)
        {
            $sql = "INSERT INTO repas (nom, type, calories, proteines, glucides, lipides) 
                    VALUES (:nom, :type, :calories, :proteines, :glucides, :lipides)";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute([
                    'nom' => $repas->getNom(),
                    'type' => $repas->getType(),
                    'calories' => $repas->getCalories(),
                    'proteines' => $repas->getProteines(),
                    'glucides' => $repas->getGlucides(),
                    'lipides' => $repas->getLipides()
                ]);
                
                $id_repas = $db->lastInsertId();
                $repas->setIdRepas($id_repas);
                
                return true;
            } catch (Exception $e) {
                error_log("Erreur addRepas: " . $e->getMessage());
                return false;
            }
        }

        // ========== LISTER TOUS LES REPAS ==========
        public function listRepas()
        {
            $sql = "SELECT * FROM repas ORDER BY nom ASC";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute();
                $repasData = $query->fetchAll(PDO::FETCH_ASSOC);

                $repas = [];
                foreach ($repasData as $row) {
                    $repasObj = new repas(
                        $row['nom'], 
                        $row['type'], 
                        $row['calories'],
                        $row['proteines'],
                        $row['glucides'],
                        $row['lipides']
                    );
                    $repasObj->setIdRepas($row['id_repas']);
                    $repas[] = $repasObj;
                }
                return $repas;
            } catch (Exception $e) {
                error_log("Erreur listRepas: " . $e->getMessage());
                return [];
            }
        }

        // ========== RÉCUPÉRER UN REPAS PAR ID ==========
        public function getRepasById($id)
        {
            $sql = "SELECT * FROM repas WHERE id_repas = :id";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['id' => $id]);
                $row = $query->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $repas = new repas(
                        $row['nom'],
                        $row['type'],
                        $row['calories'],
                        $row['proteines'],
                        $row['glucides'],
                        $row['lipides']
                    );
                    $repas->setIdRepas($row['id_repas']);
                    return $repas;
                }
                return null;
            } catch (Exception $e) {
                error_log("Erreur getRepasById: " . $e->getMessage());
                return null;
            }
        }

        // ========== METTRE À JOUR UN REPAS ==========
        public function updateRepas(repas $repas)
        {
            $sql = "UPDATE repas 
                    SET nom = :nom, 
                        type = :type, 
                        calories = :calories,
                        proteines = :proteines,
                        glucides = :glucides,
                        lipides = :lipides
                    WHERE id_repas = :id";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute([
                    'id' => $repas->getIdRepas(),
                    'nom' => $repas->getNom(),
                    'type' => $repas->getType(),
                    'calories' => $repas->getCalories(),
                    'proteines' => $repas->getProteines(),
                    'glucides' => $repas->getGlucides(),
                    'lipides' => $repas->getLipides()
                ]);
                return true;
            } catch (Exception $e) {
                error_log("Erreur updateRepas: " . $e->getMessage());
                return false;
            }
        }

        // ========== SUPPRIMER UN REPAS ==========
        public function deleteRepas($id)
        {
            $sql = "DELETE FROM repas WHERE id_repas = :id";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['id' => $id]);
                return true;
            } catch (Exception $e) {
                error_log("Erreur deleteRepas: " . $e->getMessage());
                return false;
            }
        }

        // ========== RÉCUPÉRER LES REPAS PAR TYPE ==========
        public function getRepasByType($type)
        {
            $sql = "SELECT * FROM repas WHERE type = :type ORDER BY nom ASC";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['type' => $type]);
                $repasData = $query->fetchAll(PDO::FETCH_ASSOC);

                $repas = [];
                foreach ($repasData as $row) {
                    $repasObj = new repas(
                        $row['nom'],
                        $row['type'],
                        $row['calories'],
                        $row['proteines'],
                        $row['glucides'],
                        $row['lipides']
                    );
                    $repasObj->setIdRepas($row['id_repas']);
                    $repas[] = $repasObj;
                }
                return $repas;
            } catch (Exception $e) {
                error_log("Erreur getRepasByType: " . $e->getMessage());
                return [];
            }
        }

        // ========== RÉCUPÉRER LES REPAS EN FORMAT JSON ==========
        public function getRepasJson()
        {
            $repas = $this->listRepas();
            $data = [];
            foreach ($repas as $r) {
                $data[] = [
                    'id_repas' => $r->getIdRepas(),
                    'nom' => $r->getNom(),
                    'type' => $r->getType(),
                    'calories' => $r->getCalories(),
                    'proteines' => $r->getProteines(),
                    'glucides' => $r->getGlucides(),
                    'lipides' => $r->getLipides()
                ];
            }
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }
}
?>