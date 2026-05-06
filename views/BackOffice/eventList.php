<?php
include '../../controleurs/EventController.php';
require_once __DIR__ . '/../../models/event.php';

$EventController = new EventController();
$events = $EventController->listEvents();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Events - NutriLoop</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nut-style.css">

    <style>
        .table-container {
            overflow-x: auto;
        }

        table {
            min-width: 1400px;
        }

        .event-title {
            font-weight: bold;
            color: #003366;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
            color: white;
        }

        .ACTIF { background: #28a745; }
        .CANCELLED { background: #dc3545; }
        .COMPLETED { background: #6c757d; }

        .actions a {
            margin-right: 5px;
        }
    </style>
</head>

<body>

<div class="container-list">

    <!-- HEADER -->
    <div class="header">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            Gestion des Events
        </h1>

        <a href="addEvent.php" class="add-btn">
            <i class="fas fa-plus-circle"></i>
            Ajouter un event
        </a>
    </div>

    <!-- STATS -->
    <div class="stats-bar">
        <div class="stats-info">
            <div class="stat-item">
                <i class="fas fa-calendar"></i>
                Total: <strong><?= count($events) ?></strong> events
            </div>
        </div>
    </div>

    <div style="margin-bottom:15px;">
    <input 
        type="text" 
        id="searchEvent" 
        placeholder="🔍 Rechercher un événement..." 
        style="padding:10px;width:300px;border-radius:8px;border:1px solid #ccc;"
    >
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

                            <td class="event-title">
                                <?= htmlspecialchars($event->getTitre()) ?>
                            </td>

                            <td>
                                <?= nl2br(htmlspecialchars($event->getDescription())) ?>
                            </td>

                            <td>
                                <i class="fas fa-tag"></i>
                                <?= $event->getTypeEvenement() ?>
                            </td>

                            <td>
                                <i class="fas fa-calendar"></i>
                                <?= $event->getDateEvenement() ?>
                            </td>

                            <td>
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($event->getLieu()) ?>
                            </td>

                            <td>
                                <i class="fas fa-users"></i>
                                <?= $event->getNbPlacesMax() ?>
                            </td>

                            <td>
                                <span class="badge <?= $event->getStatut() ?>">
                                    <?= $event->getStatut() ?>
                                </span>
                            </td>

                            <td>
                                <div class="actions">

                                    <a href="editEvent.php?id=<?= $event->getIdEvenement() ?>"
                                       class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="deleteEvent.php?id=<?= $event->getIdEvenement() ?>"
                                       class="action-btn delete"
                                       onclick="return confirm('Supprimer cet event ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">
                            <i class="fas fa-calendar-times"></i>
                            Aucun event trouvé
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
   <script>
const searchInput = document.getElementById("searchEvent");
const rows = document.querySelectorAll("tbody tr");

searchInput.addEventListener("input", function () {
    let value = this.value.toLowerCase();

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();

        if (text.includes(value)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>