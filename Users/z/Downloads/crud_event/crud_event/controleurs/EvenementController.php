<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Evenement.php');

class EvenementController
{
    private function rowToEvenement($row)
    {
        $ev = new Evenement(
            $row['titre'],
            $row['description'],
            $row['type_evenement'],
            $row['date_evenement'],
            $row['lieu'],
            $row['nb_places_max'],
            $row['statut'],
            $row['prix'] ?? 0.00   // NOUVEAU
        );
        $ev->setIdEvenement($row['id_evenement']);
        return $ev;
    }

    public function listEvenements()
    {
        $sql = "SELECT * FROM event ORDER BY date_evenement DESC";
        $db  = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute();
            $rows = $q->fetchAll();
            $result = [];
            foreach ($rows as $row) $result[] = $this->rowToEvenement($row);
            return $result;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    public function getEvenementById($id)
    {
        $sql = "SELECT * FROM event WHERE id_evenement = :id";
        $db  = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute(['id' => $id]);
            $row = $q->fetch();
            return $row ? $this->rowToEvenement($row) : null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function addEvenement(Evenement $ev)
    {
        $sql = "INSERT INTO event 
                    (titre, description, type_evenement, date_evenement, lieu, nb_places_max, statut, prix)
                VALUES 
                    (:titre, :description, :type_evenement, :date_evenement, :lieu, :nb_places_max, :statut, :prix)";
        $db  = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute([
                'titre'          => $ev->getTitre(),
                'description'    => $ev->getDescription(),
                'type_evenement' => $ev->getTypeEvenement(),
                'date_evenement' => $ev->getDateEvenement(),
                'lieu'           => $ev->getLieu(),
                'nb_places_max'  => $ev->getNbPlacesMax(),
                'statut'         => $ev->getStatut(),
                'prix'           => $ev->getPrix(),
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function updateEvenement(Evenement $ev)
    {
        $sql = "UPDATE event SET
                    titre          = :titre,
                    description    = :description,
                    type_evenement = :type_evenement,
                    date_evenement = :date_evenement,
                    lieu           = :lieu,
                    nb_places_max  = :nb_places_max,
                    statut         = :statut,
                    prix           = :prix
                WHERE id_evenement = :id";
        $db  = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute([
                'id'             => $ev->getIdEvenement(),
                'titre'          => $ev->getTitre(),
                'description'    => $ev->getDescription(),
                'type_evenement' => $ev->getTypeEvenement(),
                'date_evenement' => $ev->getDateEvenement(),
                'lieu'           => $ev->getLieu(),
                'nb_places_max'  => $ev->getNbPlacesMax(),
                'statut'         => $ev->getStatut(),
                'prix'           => $ev->getPrix(),
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function deleteEvenement($id)
    {
        $sql = "DELETE FROM event WHERE id_evenement = :id";
        $db  = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
}
?>