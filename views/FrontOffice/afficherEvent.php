<?php
include '../../controleurs/EventController.php';
require_once __DIR__ . '/../../models/event.php';

$eventController = new EventController();
$events = $eventController->listEvents();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Événements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nut-style.css">
</head>

<body>
<div class="container-list">

    <!-- HEADER -->
    <div class="header">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            Gestion des Événements
        </h1>

        <a href="addEvent.php" class="add-btn">
            <i class="fas fa-plus-circle"></i>
            Ajouter un événement
        </a>
    </div>

    <!-- STATS -->
    <div class="stats-bar">
        <div class="stats-info">
            <div class="stat-item">
                <i class="fas fa-calendar"></i>
                <span>Total: <strong><?= count($events) ?></strong> événements</span>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="content">
        <div class="table-container">

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Places</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>#<?= $event->getIdEvenement() ?></td>

                            <td><?= htmlspecialchars($event->getTitre()) ?></td>

                            <td>
                                <?= htmlspecialchars(substr($event->getDescription(), 0, 80)) ?>
                            </td>

                            <td>
                                <span>
                                    <?= $event->getTypeEvenement() ?>
                                </span>
                            </td>

                            <td><?= $event->getDateEvenement() ?></td>

                            <td><?= htmlspecialchars($event->getLieu()) ?></td>

                            <td>
                                <?= $event->getNbPlacesMax() ?>
                            </td>

                            <td>
                                <span>
                                    <?= $event->getStatut() ?>
                                </span>
                            </td>

                            <!-- ACTIONS -->
                            <td>
                                <div class="actions">

                                    <a href="editEvent.php?id=<?= $event->getIdEvenement() ?>"
                                       class="action-btn edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <a href="deleteEvent.php?id=<?= $event->getIdEvenement() ?>"
                                       class="action-btn delete"
                                       onclick="return confirm('Supprimer cet événement ?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">
                            Aucun événement trouvé
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>
</body>
</html>