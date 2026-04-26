<?php
session_start();
include '../../controleurs/SuiviController.php';
require_once __DIR__ . '/../../models/suivi.php';

$SuiviController = new SuiviController();
$suivis = $SuiviController->listSuivis();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Suivis - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #ffffff; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        .header h1 { font-size: 1.8rem; color: #1a1a2e; }
        .header h1 i { color: #4CAF50; margin-right: 10px; }
        .btn-group { display: flex; gap: 12px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; }
        .btn-primary { background: #4CAF50; color: white; }
        .btn-secondary { background: #003366; color: white; }
        .table-container { background: white; border-radius: 16px; overflow: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        th { background: #1a1a2e; color: white; padding: 15px 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 8px; }
        .action-btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600; color: white; }
        .edit-btn { background: #2196F3; }
        .delete-btn { background: #dc3545; }
        .notification-success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Gestion des Suivis</h1>
            <div class="btn-group">
                <a href="addSuivi.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Ajouter un suivi</a>
                <a href="objectifList.php" class="btn btn-secondary"><i class="fas fa-bullseye"></i> Gestion des Objectifs</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Objectif</th>
                        <th>Date</th>
                        <th>Poids</th>
                        <th>Calories Cons.</th>
                        <th>Calories Obj.</th>
                        <th>Eau Bue</th>
                        <th>Eau Obj.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($suivis) > 0): ?>
                        <?php foreach ($suivis as $s): ?>
                            <tr>
                                <td>#<?= $s->getId() ?></td>
                                <td><?= $s->getUserId() ?></td>
                                <td><?= $s->getIdObjectif() ?: 'N/A' ?></td>
                                <td><?= $s->getDate() ?></td>
                                <td><?= $s->getPoids() ?> kg</td>
                                <td><?= $s->getCaloriesConsommees() ?> kcal</td>
                                <td><?= $s->getCaloriesObjectif() ?> kcal</td>
                                <td><?= $s->getEauBue() ?> L</td>
                                <td><?= $s->getEauObjectif() ?> L</td>
                                <td class="actions">
                                    <a href="editSuivi.php?id=<?= $s->getId() ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i></a>
                                    <a href="deleteSuivi.php?id=<?= $s->getId() ?>" class="action-btn delete-btn" onclick="return confirm('Supprimer ce suivi ?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align: center; padding: 40px;">Aucun suivi trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
