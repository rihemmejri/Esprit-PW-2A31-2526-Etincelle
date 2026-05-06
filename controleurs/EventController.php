<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/event.php');

class EventController
{
    // Ajouter event
    public function addEvent(Event $event)
    {
        $sql = "INSERT INTO event 
        (titre, description, type_evenement, date_evenement, lieu, nb_places_max, statut)
        VALUES 
        (:titre, :description, :type_evenement, :date_evenement, :lieu, :nb_places_max, :statut)";

        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $event->getTitre(),
                'description' => $event->getDescription(),
                'type_evenement' => $event->getTypeEvenement(),
                'date_evenement' => $event->getDateEvenement(),
                'lieu' => $event->getLieu(),
                'nb_places_max' => $event->getNbPlacesMax(),
                'statut' => $event->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Liste events
    public function listEvents()
    {
        $sql = "SELECT * FROM event ORDER BY date_evenement DESC";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute();
            $rows = $query->fetchAll();

            $events = [];

            foreach ($rows as $row) {
                $event = new Event(
                    $row['titre'],
                    $row['description'],
                    $row['type_evenement'],
                    $row['date_evenement'],
                    $row['lieu'],
                    $row['nb_places_max'],
                    $row['statut']
                );

                $event->setIdEvenement($row['id_evenement']);
                $events[] = $event;
            }

            return $events;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Get by ID
    public function getEventById($id)
    {
        $sql = "SELECT * FROM event WHERE id_evenement = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $event = new Event(
                    $row['titre'],
                    $row['description'],
                    $row['type_evenement'],
                    $row['date_evenement'],
                    $row['lieu'],
                    $row['nb_places_max'],
                    $row['statut']
                );

                $event->setIdEvenement($row['id_evenement']);
                return $event;
            }

            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Update event
    public function updateEvent(Event $event)
    {
        $sql = "UPDATE event SET
            titre = :titre,
            description = :description,
            type_evenement = :type_evenement,
            date_evenement = :date_evenement,
            lieu = :lieu,
            nb_places_max = :nb_places_max,
            statut = :statut
            WHERE id_evenement = :id";

        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $event->getIdEvenement(),
                'titre' => $event->getTitre(),
                'description' => $event->getDescription(),
                'type_evenement' => $event->getTypeEvenement(),
                'date_evenement' => $event->getDateEvenement(),
                'lieu' => $event->getLieu(),
                'nb_places_max' => $event->getNbPlacesMax(),
                'statut' => $event->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Delete event
    public function deleteEvent($id)
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