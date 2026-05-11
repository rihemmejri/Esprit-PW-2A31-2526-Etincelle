<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../models/programme.php');
include_once(__DIR__ . '/../models/repas.php');

if (!class_exists('ProgrammeController')) {
    class ProgrammeController
    {
        // ========== LISTER TOUS LES PROGRAMMES ==========
        public function listProgrammes()
        {
            $sql = "SELECT * FROM programme ORDER BY date_debut DESC";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute();
                $programmesData = $query->fetchAll(PDO::FETCH_ASSOC);

                $programmes = [];
                foreach ($programmesData as $row) {
                    $programme = new programme(
                        $row['id_user'],
                        $row['objectif'],
                        $row['date_debut'],
                        $row['date_fin']
                    );
                    $programme->setIdProgramme($row['id_programme']);
                    $programme->setRepas($this->getRepasByProgramme($row['id_programme']));
                    $programmes[] = $programme;
                }
                return $programmes;
            } catch (Exception $e) {
                error_log("Erreur listProgrammes: " . $e->getMessage());
                return [];
            }
        }

        // ========== RÉCUPÉRER LES REPAS D'UN PROGRAMME ==========
        public function getRepasByProgramme($id_programme)
        {
            $sql = "SELECT r.*, pr.jour_semaine, pr.type_repas 
                    FROM programme_repas pr
                    JOIN repas r ON pr.id_repas = r.id_repas
                    WHERE pr.id_programme = :id_programme
                    ORDER BY pr.jour_semaine, pr.type_repas";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['id_programme' => $id_programme]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Erreur getRepasByProgramme: " . $e->getMessage());
                return [];
            }
        }

        // ========== RÉCUPÉRER UN PROGRAMME PAR ID ==========
        public function getProgrammeById($id)
        {
            $sql = "SELECT * FROM programme WHERE id_programme = :id";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['id' => $id]);
                $row = $query->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $programme = new programme(
                        $row['id_user'],
                        $row['objectif'],
                        $row['date_debut'],
                        $row['date_fin']
                    );
                    $programme->setIdProgramme($row['id_programme']);
                    $programme->setRepas($this->getRepasByProgramme($id));
                    return $programme;
                }
                return null;
            } catch (Exception $e) {
                error_log("Erreur getProgrammeById: " . $e->getMessage());
                return null;
            }
        }

        // ========== AJOUTER UN PROGRAMME ==========
        public function addProgramme(programme $programme)
        {
            $sql = "INSERT INTO programme (id_user, objectif, date_debut, date_fin) 
                    VALUES (:id_user, :objectif, :date_debut, :date_fin)";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute([
                    'id_user' => $programme->getIdUser(),
                    'objectif' => $programme->getObjectif(),
                    'date_debut' => $programme->getDateDebut(),
                    'date_fin' => $programme->getDateFin()
                ]);
                
                $id_programme = $db->lastInsertId();
                $programme->setIdProgramme($id_programme);
                
                if (!empty($programme->getRepas())) {
                    $this->addRepasToProgramme($id_programme, $programme->getRepas());
                }
                
                return true;
            } catch (Exception $e) {
                error_log("Erreur addProgramme: " . $e->getMessage());
                return false;
            }
        }

        // ========== AJOUTER DES REPAS À UN PROGRAMME ==========
        public function addRepasToProgramme($id_programme, $repasList)
        {
            $sql = "INSERT INTO programme_repas (id_programme, id_repas, jour_semaine, type_repas)
                    VALUES (:id_programme, :id_repas, :jour_semaine, :type_repas)";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                foreach ($repasList as $item) {
                    $query->execute([
                        'id_programme' => $id_programme,
                        'id_repas' => $item['id_repas'],
                        'jour_semaine' => $item['jour_semaine'],
                        'type_repas' => $item['type_repas']
                    ]);
                }
                return true;
            } catch (Exception $e) {
                error_log("Erreur addRepasToProgramme: " . $e->getMessage());
                return false;
            }
        }

        // ========== METTRE À JOUR UN PROGRAMME ==========
        public function updateProgramme(programme $programme)
        {
            $sql = "UPDATE programme 
                    SET id_user = :id_user,
                        objectif = :objectif,
                        date_debut = :date_debut,
                        date_fin = :date_fin
                    WHERE id_programme = :id";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute([
                    'id' => $programme->getIdProgramme(),
                    'id_user' => $programme->getIdUser(),
                    'objectif' => $programme->getObjectif(),
                    'date_debut' => $programme->getDateDebut(),
                    'date_fin' => $programme->getDateFin()
                ]);
                
                $this->deleteRepasFromProgramme($programme->getIdProgramme());
                
                if (!empty($programme->getRepas())) {
                    $this->addRepasToProgramme($programme->getIdProgramme(), $programme->getRepas());
                }
                
                return true;
            } catch (Exception $e) {
                error_log("Erreur updateProgramme: " . $e->getMessage());
                return false;
            }
        }

        // ========== SUPPRIMER LES REPAS D'UN PROGRAMME ==========
        public function deleteRepasFromProgramme($id_programme)
        {
            $sql = "DELETE FROM programme_repas WHERE id_programme = :id_programme";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['id_programme' => $id_programme]);
                return true;
            } catch (Exception $e) {
                error_log("Erreur deleteRepasFromProgramme: " . $e->getMessage());
                return false;
            }
        }

        // ========== SUPPRIMER UN PROGRAMME ==========
        public function deleteProgramme($id)
        {
            $this->deleteRepasFromProgramme($id);
            
            $sql = "DELETE FROM programme WHERE id_programme = :id";
            $db = Config::getConnexion();
            
            try {
                $query = $db->prepare($sql);
                $query->execute(['id' => $id]);
                return true;
            } catch (Exception $e) {
                error_log("Erreur deleteProgramme: " . $e->getMessage());
                return false;
            }
        }
    }
}
?>