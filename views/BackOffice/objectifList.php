<?php
session_start();
include '../../controleurs/ObjectifController.php';
require_once __DIR__ . '/../../models/objectif.php';

$ObjectifController = new ObjectifController();
$objectifs = $ObjectifController->listObjectifs();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Objectifs - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-list">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="header">
            <h1>
                <i class="fas fa-chart-line"></i>
                Gestion des Objectifs
            </h1>
            <a href="addObjectif.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter un objectif
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Total: <strong><?= count($objectifs) ?></strong> objectifs</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-fire"></i>
                    <span>Calories moyennes: <strong>
                        <?php 
                        if (count($objectifs) > 0) {
                            $totalCalories = 0;
                            foreach ($objectifs as $o) {
                                $totalCalories += $o->getCaloriesObjectif();
                            }
                            echo round($totalCalories / count($objectifs)) . ' kcal';
                        } else {
                            echo '0 kcal';
                        }
                        ?>
                    </strong></span>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="objectifsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> User ID</th>
                            <th><i class="fas fa-weight"></i> Poids Cible (kg)</th>
                            <th><i class="fas fa-fire"></i> Calories Objectif</th>
                            <th><i class="fas fa-water"></i> Eau Objectif (L)</th>
                            <th><i class="fas fa-calendar"></i> Date Début</th>
                            <th><i class="fas fa-calendar"></i> Date Fin</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($objectifs) > 0): ?> 
                            <?php foreach ($objectifs as $objectif): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($objectif->getId()) ?></td>
                                    <td><?= htmlspecialchars($objectif->getUserId()) ?></td>
                                    <td><?= htmlspecialchars($objectif->getPoidsCible()) ?></td>
                                    <td><?= htmlspecialchars($objectif->getCaloriesObjectif()) ?></td>
                                    <td><?= htmlspecialchars($objectif->getEauObjectif()) ?></td>
                                    <td><?= htmlspecialchars($objectif->getDateDebut()) ?></td>
                                    <td><?= htmlspecialchars($objectif->getDateFin()) ?></td>
                                    <td class="actions">
                                        <a href="editObjectif.php?id=<?= $objectif->getId() ?>" class="edit-btn" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="deleteObjectif.php?id=<?= $objectif->getId() ?>" class="delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Aucun objectif trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('objectifsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const row = tr[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>
