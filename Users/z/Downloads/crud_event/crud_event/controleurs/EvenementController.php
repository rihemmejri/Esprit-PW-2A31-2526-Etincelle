<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Evenement.php');

class EvenementController
{
    // =============================================
    // CRUD EVENEMENT
    // =============================================

    // Ajouter un événement
    public function addEvenement(Evenement $evenement)
    {
        $sql = "INSERT INTO event (titre, description, type_evenement, date_evenement, lieu, nb_places_max, statut) 
                VALUES (:titre, :description, :type_evenement, :date_evenement, :lieu, :nb_places_max, :statut)";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre'          => $evenement->getTitre(),
                'description'    => $evenement->getDescription(),
                'type_evenement' => $evenement->getTypeEvenement(),
                'date_evenement' => $evenement->getDateEvenement(),
                'lieu'           => $evenement->getLieu(),
                'nb_places_max'  => $evenement->getNbPlacesMax(),
                'statut'         => $evenement->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Lister tous les événements
    public function listEvenements()
    {
        $sql = "SELECT * FROM event ORDER BY date_evenement ASC";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute();
            $evenementsData = $query->fetchAll();

            $evenements = [];
            foreach ($evenementsData as $row) {
                $evenement = new Evenement(
                    $row['titre'],
                    $row['description'],
                    $row['type_evenement'],
                    $row['date_evenement'],
                    $row['lieu'],
                    $row['nb_places_max'],
                    $row['statut']
                );
                $evenement->setIdEvenement($row['id_evenement']);
                $evenements[] = $evenement;
            }
            return $evenements;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer un événement par ID
    public function getEvenementById($id)
    {
        $sql = "SELECT * FROM event WHERE id_evenement = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $evenement = new Evenement(
                    $row['titre'],
                    $row['description'],
                    $row['type_evenement'],
                    $row['date_evenement'],
                    $row['lieu'],
                    $row['nb_places_max'],
                    $row['statut']
                );
                $evenement->setIdEvenement($row['id_evenement']);
                return $evenement;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour un événement
    public function updateEvenement(Evenement $evenement)
    {
        $sql = "UPDATE event 
                SET titre          = :titre,
                    description    = :description,
                    type_evenement = :type_evenement,
                    date_evenement = :date_evenement,
                    lieu           = :lieu,
                    nb_places_max  = :nb_places_max,
                    statut         = :statut
                WHERE id_evenement = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'             => $evenement->getIdEvenement(),
                'titre'          => $evenement->getTitre(),
                'description'    => $evenement->getDescription(),
                'type_evenement' => $evenement->getTypeEvenement(),
                'date_evenement' => $evenement->getDateEvenement(),
                'lieu'           => $evenement->getLieu(),
                'nb_places_max'  => $evenement->getNbPlacesMax(),
                'statut'         => $evenement->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Supprimer un événement
    public function deleteEvenement($id)
    {
        $sql = "DELETE FROM event WHERE id_evenement = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

 
    
}
?>