<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Participation.php');

class ParticipationController
{
    // ===== Fonction utilitaire pour créer un objet Participation depuis une ligne BD =====
    private function rowToParticipation($row)
    {
        $participation = new Participation(
            $row['id_evenement'],
            $row['id_user'],
            $row['nom'],
            $row['email'],
            $row['telephone'],
            $row['statut'],
            $row['date_inscription'],
            $row['feedback'],
            $row['note'],
            $row['nb_places_reservees']
        );
        $participation->setIdParticipation($row['id_participation']);
        if (isset($row['evenement_titre'])) {
            $participation->setEvenementTitre($row['evenement_titre']);
        }
        return $participation;
    }

    // ===== Ajouter une participation =====
    public function addParticipation(Participation $participation)
    {
        $sql = "INSERT INTO participation 
                    (id_evenement, id_user, nom, email, telephone, statut, feedback, note, nb_places_reservees) 
                VALUES 
                    (:id_evenement, :id_user, :nom, :email, :telephone, :statut, :feedback, :note, :nb_places_reservees)";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_evenement'        => $participation->getIdEvenement(),
                'id_user'             => $participation->getIdUser(),
                'nom'                 => $participation->getNom(),
                'email'               => $participation->getEmail(),
                'telephone'           => $participation->getTelephone(),
                'statut'              => $participation->getStatut(),
                'feedback'            => $participation->getFeedback(),
                'note'                => $participation->getNote(),
                'nb_places_reservees' => $participation->getNbPlacesReservees() ?? 1
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // ===== Lister toutes les participations =====
    public function listParticipations()
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre
                FROM participation pa
                LEFT JOIN event e ON pa.id_evenement = e.id_evenement
                ORDER BY pa.date_inscription DESC";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute();
            $rows = $query->fetchAll();

            $participations = [];
            foreach ($rows as $row) {
                $participations[] = $this->rowToParticipation($row);
            }
            return $participations;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // ===== Participations d'un événement =====
    public function getParticipationsByEvenement($id_evenement)
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre
                FROM participation pa
                LEFT JOIN event e ON pa.id_evenement = e.id_evenement
                WHERE pa.id_evenement = :id_evenement
                ORDER BY pa.date_inscription DESC";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id_evenement' => $id_evenement]);
            $rows = $query->fetchAll();

            $participations = [];
            foreach ($rows as $row) {
                $participations[] = $this->rowToParticipation($row);
            }
            return $participations;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // ===== Récupérer une participation par ID =====
    public function getParticipationById($id)
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre
                FROM participation pa
                LEFT JOIN event e ON pa.id_evenement = e.id_evenement
                WHERE pa.id_participation = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            return $row ? $this->rowToParticipation($row) : null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // ===== Participations d'un user =====
    public function getParticipationsByUser($id_user)
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre
                FROM participation pa
                LEFT JOIN event e ON pa.id_evenement = e.id_evenement
                WHERE pa.id_user = :id_user
                ORDER BY pa.date_inscription DESC";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id_user' => $id_user]);
            $rows = $query->fetchAll();

            $participations = [];
            foreach ($rows as $row) {
                $participations[] = $this->rowToParticipation($row);
            }
            return $participations;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // ===== Mettre à jour une participation =====
    public function updateParticipation(Participation $participation)
    {
        $sql = "UPDATE participation 
                SET nom                 = :nom,
                    email               = :email,
                    telephone           = :telephone,
                    statut              = :statut,
                    feedback            = :feedback,
                    note                = :note,
                    nb_places_reservees = :nb_places_reservees
                WHERE id_participation  = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'                  => $participation->getIdParticipation(),
                'nom'                 => $participation->getNom(),
                'email'               => $participation->getEmail(),
                'telephone'           => $participation->getTelephone(),
                'statut'              => $participation->getStatut(),
                'feedback'            => $participation->getFeedback(),
                'note'                => $participation->getNote(),
                'nb_places_reservees' => $participation->getNbPlacesReservees() ?? 1
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // ===== Supprimer une participation =====
    public function deleteParticipation($id)
    {
        $sql = "DELETE FROM participation WHERE id_participation = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // ===== Tous les événements pour le select =====
    public function getAllEvenements()
    {
        $sql = "SELECT id_evenement, titre FROM event ORDER BY titre ASC";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }
}
?>