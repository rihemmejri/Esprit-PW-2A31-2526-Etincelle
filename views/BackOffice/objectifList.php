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
</head>
<body>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff; /* User requested white background */
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
            background: #ffffff !important;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
        }

        .header h1 i {
            color: #4CAF50;
            margin-right: 10px;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #003366;
            color: white;
        }

        .btn-secondary:hover {
            background: #002244;
            transform: translateY(-2px);
        }

        /* Stats Bar */
        .stats-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stats {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat i {
            font-size: 1.2rem;
            color: #4CAF50;
        }

        .search-box {
            display: flex;
            gap: 5px;
        }

        .search-box input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
        }

        .search-box button {
            background: #4CAF50;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #1a1a2e !important;
            color: white !important;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
        }

        th i {
            margin-right: 8px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.2s;
        }

        .edit-btn {
            background: #2196F3;
            color: white;
        }

        .edit-btn:hover {
            background: #1976D2;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pagination {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
        }

        .page-btn.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .export-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .empty-message {
            text-align: center;
            padding: 60px;
            color: #999;
        }

        .notification-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 5px solid #4CAF50;
        }
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
            <h1>
                <i class="fas fa-bullseye"></i>
                Gestion des Objectifs
            </h1>
            <div class="btn-group">
                <a href="addObjectif.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un objectif
                </a>
                <a href="suiviList.php" class="btn btn-secondary">
                    <i class="fas fa-tasks"></i>
                    Gestion des Suivi
                </a>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-bullseye"></i>
                    <span><strong><?= count($objectifs) ?></strong> objectif<?= count($objectifs) > 1 ? 's' : '' ?></span>
                </div>
                <div class="stat">
                    <i class="fas fa-fire"></i>
                    <span><strong>
                        <?php 
                        if (count($objectifs) > 0) {
                            $totalCalories = 0;
                            foreach ($objectifs as $o) {
                                $totalCalories += $o->getCaloriesObjectif();
                            }
                            echo round($totalCalories / count($objectifs));
                        } else {
                            echo '0';
                        }
                        ?>
                    </strong> kcal (moy.)</span>
                </div>
                <div class="stat">
                    <i class="fas fa-tint"></i>
                    <span><strong>
                        <?php 
                        $totalEau = 0;
                        foreach ($objectifs as $o) {
                            $totalEau += $o->getEauObjectif();
                        }
                        echo number_format($totalEau, 1);
                        ?>
                    </strong> eau (moy.)</span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="table-container">
            <table id="objectifsTable">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th><i class="fas fa-user"></i> USER</th>
                        <th><i class="fas fa-weight"></i> POIDS (KG)</th>
                        <th><i class="fas fa-fire"></i> CALORIES</th>
                        <th><i class="fas fa-tint"></i> EAU (L)</th>
                        <th><i class="fas fa-calendar"></i> DÉBUT</th>
                        <th><i class="fas fa-calendar-check"></i> FIN</th>
                        <th><i class="fas fa-cog"></i> ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($objectifs) > 0): ?> 
                        <?php foreach ($objectifs as $objectif): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($objectif->getId()) ?></td>
                                <td><?= htmlspecialchars($objectif->getUserId()) ?></td>
                                <td><?= htmlspecialchars($objectif->getPoidsCible()) ?> kg</td>
                                <td><?= htmlspecialchars($objectif->getCaloriesObjectif()) ?> kcal</td>
                                <td><?= htmlspecialchars($objectif->getEauObjectif()) ?> L</td>
                                <td><?= htmlspecialchars($objectif->getDateDebut()) ?></td>
                                <td><?= htmlspecialchars($objectif->getDateFin()) ?></td>
                                <td class="actions">
                                    <a href="editObjectif.php?id=<?= $objectif->getId() ?>" class="action-btn edit-btn" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="deleteObjectif.php?id=<?= $objectif->getId() ?>" class="action-btn delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-message">
                                <i class="fas fa-inbox"></i>
                                <p>Aucun objectif trouvé</p>
                                <a href="addObjectif.php" class="btn btn-primary" style="margin-top: 20px;">
                                    Ajouter un objectif
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div class="pagination">
                <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button class="export-btn">
                <i class="fas fa-file-export"></i>
                Exporter
            </button>
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
