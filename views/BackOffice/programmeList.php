<?php
include_once '../../controleurs/ProgrammeController.php';
include_once '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/programme.php';
require_once __DIR__ . '/../../models/repas.php';

$programmeController = new ProgrammeController();
$repasController = new RepasController();

$programmes = $programmeController->listProgrammes();

// Calculer les statistiques
$totalProgrammes = count($programmes);
$totalRepasProgrammes = 0;
$totalCaloriesProgrammes = 0;

foreach ($programmes as $p) {
    $repasDuProgramme = $p->getRepas();
    $totalRepasProgrammes += count($repasDuProgramme);
    foreach ($repasDuProgramme as $r) {
        $totalCaloriesProgrammes += $r['calories'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Programmes - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
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
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
        }

        .header h1 i {
            color: #4CAF50;
            margin-right: 10px;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #45a049;
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

        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            cursor: pointer;
        }

        th.sortable {
            cursor: pointer;
            transition: background 0.2s;
        }
        th.sortable:hover {
            background: #2a2a4e;
        }
        th.sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-left: 8px;
            opacity: 0.5;
        }
        th.sortable.asc::after { content: '\f0de'; opacity: 1; color: #4CAF50; }
        th.sortable.desc::after { content: '\f0dd'; opacity: 1; color: #4CAF50; }

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
            min-width: 1000px;
        }

        th {
            background: #1a1a2e;
            color: white;
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
            vertical-align: top;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Badges */
        .badge-objectif {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .objectif-PERDRE_POIDS {
            background: #ffebee;
            color: #c62828;
        }

        .objectif-PRENDRE_MUSCLE {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .objectif-MAINTENIR {
            background: #e3f2fd;
            color: #1565c0;
        }

        .objectif-EQUILIBRE {
            background: #fff3e0;
            color: #e65100;
        }

        .badge-duree {
            background: #f0f0f0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .repas-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            max-width: 250px;
        }

        .repas-mini {
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .repas-mini i {
            font-size: 0.6rem;
            color: #4CAF50;
        }

        .badge-calories {
            background: #fff3e0;
            color: #e65100;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
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

        .view-btn {
            background: #4CAF50;
            color: white;
        }

        .view-btn:hover {
            background: #45a049;
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

        .tfoot {
            background: #f8f9fa;
            font-weight: bold;
        }

        .tfoot td {
            padding: 15px 12px;
            border-top: 2px solid #ddd;
        }

        .text-center {
            text-align: center;
        }

        /* Modal Styles */
        .modal {
            display: none !important;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-content {
            background: white;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            animation: slideIn 0.3s;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .modal-close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 25px;
            max-height: calc(90vh - 80px);
            overflow-y: auto;
        }

        .program-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .program-stat-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: 0.3s;
            border-top: 4px solid var(--program-color, #4CAF50);
        }

        .program-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .program-stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .program-stat-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .program-stat-count {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .program-stat-percentage {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 0.3rem;
        }

        .program-stat-details {
            font-size: 0.7rem;
            color: #999;
        }

        .program-chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 1rem;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .loading-spinner i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Enhanced Statistics Styles */
        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .stats-title h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .stats-title p {
            margin: 0.5rem 0 0 0;
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .stats-period {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .stats-period i {
            color: #4CAF50;
        }

        .key-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #4CAF50;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .metric-card.highlight {
            border-left-color: #ff6b6b;
            background: linear-gradient(135deg, #fff 0%, #ff6b6b10 100%);
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: #4CAF50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .metric-card.highlight .metric-icon {
            background: #ff6b6b;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .chart-container h3 {
            margin: 0 0 1rem 0;
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .chart-wrapper {
            height: 300px;
            position: relative;
        }

        .detailed-stats {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .detailed-stats h3 {
            margin: 0 0 1.5rem 0;
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .stats-table {
            overflow-x: auto;
        }

        .stats-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .stats-table tr:hover {
            background: #f8f9fa;
        }

        .objective-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .percentage-bar {
            position: relative;
            background: #f1f3f4;
            border-radius: 10px;
            height: 8px;
            min-width: 100px;
        }

        .percentage-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .percentage-text {
            position: absolute;
            top: -20px;
            right: 0;
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
        }

        .trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .trend-up {
            background: #d4edda;
            color: #155724;
        }

        .trend-stable {
            background: #fff3cd;
            color: #856404;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .key-metrics {
                grid-template-columns: 1fr;
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .chart-wrapper {
                height: 250px;
            }

            .stats-table {
                font-size: 0.9rem;
            }

            .stats-table th,
            .stats-table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-calendar-alt"></i>
                Gestion des Programmes
            </h1>
            <div class="header-actions">
                <button class="btn-primary" id="statisticsBtn">
                    <i class="fas fa-chart-pie"></i> Statistiques
                </button>
                <a href="addProgramme.php" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Nouveau programme
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-calendar-alt"></i>
                    <span><strong><?= $totalProgrammes ?></strong> programmes</span>
                </div>
                <div class="stat">
                    <i class="fas fa-utensils"></i>
                    <span><strong><?= $totalRepasProgrammes ?></strong> repas programmés</span>
                </div>
                <div class="stat">
                    <i class="fas fa-fire"></i>
                    <span><strong><?= $totalCaloriesProgrammes ?></strong> kcal total</span>
                </div>
            </div>
            <div class="search-box">
                <select id="filterObjectif" class="filter-select" onchange="searchTable()">
                    <option value="">Tous les objectifs</option>
                    <option value="perdre du poids">Perdre du poids</option>
                    <option value="prendre du muscle">Prendre du muscle</option>
                    <option value="maintenir">Maintenir</option>
                    <option value="équilibre">Équilibre</option>
                </select>
                <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table id="programmeTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)"><i class="fas fa-hashtag"></i> ID</th>
                        <th class="sortable" onclick="sortTable(1)"><i class="fas fa-user"></i> Utilisateur</th>
                        <th class="sortable" onclick="sortTable(2)"><i class="fas fa-bullseye"></i> Objectif</th>
                        <th class="sortable" onclick="sortTable(3)"><i class="fas fa-calendar-alt"></i> Date début</th>
                        <th class="sortable" onclick="sortTable(4)"><i class="fas fa-calendar-check"></i> Date fin</th>
                        <th class="sortable" onclick="sortTable(5)"><i class="fas fa-clock"></i> Durée</th>
                        <th><i class="fas fa-utensils"></i> Repas</th>
                        <th class="sortable" onclick="sortTable(7)"><i class="fas fa-fire"></i> Calories</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalProgrammes > 0): ?>
                        <?php foreach ($programmes as $programme): 
                            $repasListe = $programme->getRepas();
                            $nbRepas = count($repasListe);
                            $caloriesTotal = 0;
                            foreach ($repasListe as $r) {
                                $caloriesTotal += $r['calories'] ?? 0;
                            }
                            
                            // Calculer la durée
                            $dateDebut = new DateTime($programme->getDateDebut());
                            $dateFin = new DateTime($programme->getDateFin());
                            $duree = $dateDebut->diff($dateFin)->days + 1;
                            
                            // Classe CSS pour l'objectif
                            $objectifClass = '';
                            $objectifIcon = '';
                            switch($programme->getObjectif()) {
                                case 'PERDRE_POIDS':
                                    $objectifClass = 'objectif-PERDRE_POIDS';
                                    $objectifIcon = '🔥';
                                    break;
                                case 'PRENDRE_MUSCLE':
                                    $objectifClass = 'objectif-PRENDRE_MUSCLE';
                                    $objectifIcon = '💪';
                                    break;
                                case 'MAINTENIR':
                                    $objectifClass = 'objectif-MAINTENIR';
                                    $objectifIcon = '⚖️';
                                    break;
                                case 'EQUILIBRE':
                                    $objectifClass = 'objectif-EQUILIBRE';
                                    $objectifIcon = '🥗';
                                    break;
                                default:
                                    $objectifClass = '';
                                    $objectifIcon = '🎯';
                            }
                        ?>
                            <tr>
                                <td class="text-center"><strong>#<?= $programme->getIdProgramme() ?></strong></td>
                                <td class="text-center">ID: <?= $programme->getIdUser() ?></td>
                                <td>
                                    <span class="badge-objectif <?= $objectifClass ?>">
                                        <?= $objectifIcon ?> 
                                        <?php 
                                        switch($programme->getObjectif()) {
                                            case 'PERDRE_POIDS': echo 'Perdre du poids'; break;
                                            case 'PRENDRE_MUSCLE': echo 'Prendre du muscle'; break;
                                            case 'MAINTENIR': echo 'Maintenir'; break;
                                            case 'EQUILIBRE': echo 'Équilibre'; break;
                                            default: echo $programme->getObjectif();
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?= $programme->getDateDebut() ?></td>
                                <td><?= $programme->getDateFin() ?></td>
                                <td>
                                    <span class="badge-duree">
                                        <i class="fas fa-calendar-week"></i> <?= $duree ?> jours
                                    </span>
                                </td>
                                <td>
                                    <div class="repas-preview">
                                        <span class="badge-duree" style="background:#4CAF50; color:white;">
                                            <i class="fas fa-utensils"></i> <?= $nbRepas ?> repas
                                        </span>
                                        <?php 
                                        $premiersRepas = array_slice($repasListe, 0, 3);
                                        foreach ($premiersRepas as $item): 
                                        ?>
                                            <span class="repas-mini">
                                                <i class="fas fa-utensils"></i>
                                                <?= htmlspecialchars(substr($item['nom'] ?? 'Repas', 0, 12)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if ($nbRepas > 3): ?>
                                            <span class="repas-mini">
                                                <i class="fas fa-ellipsis-h"></i> +<?= $nbRepas - 3 ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-calories">
                                        <i class="fas fa-fire"></i> <?= $caloriesTotal ?> kcal
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="editProgramme.php?id=<?= $programme->getIdProgramme() ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $programme->getIdProgramme() ?>); return false;">
                                        <i class="fas fa-trash"></i> Suppr
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="empty-message">
                                <i class="fas fa-empty-folder" style="font-size: 3rem;"></i>
                                <p>Aucun programme trouvé</p>
                                <a href="addProgramme.php" class="btn-primary" style="margin-top: 10px;">Créer un programme</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if ($totalProgrammes > 0): ?>
                    <tfoot class="tfoot">
                        <tr>
                            <td colspan="6"><strong>Totaux :</strong></td>
                            <td><strong><?= $totalRepasProgrammes ?> repas</strong></td>
                            <td><strong><?= $totalCaloriesProgrammes ?> kcal</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="pagination">
                <button class="page-btn" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active" id="page1">1</button>
                <button class="page-btn" id="page2" style="display:none;">2</button>
                <button class="page-btn" id="page3" style="display:none;">3</button>
                <button class="page-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div>
                <button class="export-btn" onclick="exportTable()" style="margin-right: 10px;">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button class="export-btn" style="background: #e53935;" onclick="exportPDF()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Supprimer ce programme ? Cette action est irréversible.')) {
                window.location.href = 'deleteProgramme.php?id=' + id;
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filterText = input.value.toLowerCase();
            const filterObjectif = document.getElementById('filterObjectif').value.toLowerCase();
            const rows = document.querySelectorAll('#programmeTable tbody tr');
            
            rows.forEach(row => {
                if (row.querySelector('.empty-message')) return;
                
                const text = row.textContent.toLowerCase();
                const objectifText = row.children[2] ? row.children[2].textContent.toLowerCase() : '';
                
                const matchesText = text.includes(filterText);
                const matchesObjectif = filterObjectif === "" || objectifText.includes(filterObjectif);
                if (matchesText && matchesObjectif) {
                    row.classList.remove('hidden-by-filter');
                } else {
                    row.classList.add('hidden-by-filter');
                }
            });
            showPage(1);
        }

        let sortDirection = false;
        function sortTable(columnIndex) {
            const table = document.getElementById('programmeTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            if (rows.length === 0 || rows[0].querySelector('.empty-message')) return;

            sortDirection = !sortDirection;
            const multiplier = sortDirection ? 1 : -1;

            const allHeaders = table.querySelectorAll('th');
            table.querySelectorAll('th.sortable').forEach(th => { th.classList.remove('asc', 'desc'); });
            allHeaders[columnIndex].classList.add(sortDirection ? 'asc' : 'desc');

            rows.sort((a, b) => {
                if(a.querySelector('.tfoot') || b.querySelector('.tfoot')) return 0;
                
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();

                const aNum = parseFloat(aText.replace(/[^0-9.-]+/g,""));
                const bNum = parseFloat(bText.replace(/[^0-9.-]+/g,""));

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return (aNum - bNum) * multiplier;
                }

                return aText.localeCompare(bText) * multiplier;
            });

            rows.forEach(row => tbody.appendChild(row));
            showPage(1);
        }

        let currentPage = 1;
        const rowsPerPage = 10;

        function showPage(page) {
            const allRows = Array.from(document.querySelectorAll('#programmeTable tbody tr:not(.tfoot)'));
            const visibleRows = allRows.filter(r => !r.classList.contains('hidden-by-filter') && !r.querySelector('.empty-message'));
            const totalPages = Math.ceil(visibleRows.length / rowsPerPage) || 1;
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            allRows.forEach(row => {
                if (!row.classList.contains('tfoot')) {
                    row.style.display = 'none';
                }
            });
            
            visibleRows.forEach((row, index) => {
                if (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) {
                    row.style.display = '';
                }
            });
            
            currentPage = page;
            document.querySelectorAll('.page-btn').forEach(btn => btn.style.display = 'none');
            for (let i = 1; i <= Math.min(totalPages, 3); i++) {
                const btn = document.getElementById('page' + i);
                if (btn) {
                    btn.style.display = 'inline-block';
                    btn.textContent = i;
                    btn.classList.toggle('active', i === page);
                }
            }
        }

        function previousPage() { showPage(currentPage - 1); }
        function nextPage() { showPage(currentPage + 1); }

        function exportTable() {
            let csv = "ID,Utilisateur,Objectif,Date debut,Date fin,Duree,Nb repas,Calories\n";
            <?php foreach ($programmes as $p): 
                $repasListe = $p->getRepas();
                $caloriesTotal = 0;
                foreach ($repasListe as $r) $caloriesTotal += $r['calories'] ?? 0;
                $dateDebut = new DateTime($p->getDateDebut());
                $dateFin = new DateTime($p->getDateFin());
                $duree = $dateDebut->diff($dateFin)->days + 1;
            ?>
                csv += `<?= $p->getIdProgramme() ?>,<?= $p->getIdUser() ?>,<?= $p->getObjectif() ?>,<?= $p->getDateDebut() ?>,<?= $p->getDateFin() ?>,<?= $duree ?> jours,<?= count($repasListe) ?>,<?= $caloriesTotal ?>\n`;
            <?php endforeach; ?>
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'programmes_export.csv';
            a.click();
            URL.revokeObjectURL(url);
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.text("Liste des Programmes Nutritionnels", 14, 15);
            
            const headers = [['ID', 'Utilisateur', 'Objectif', 'Date debut', 'Date fin', 'Duree', 'Calories']];
            const data = [];
            <?php foreach ($programmes as $p): 
                $caloriesTotal = 0;
                foreach ($p->getRepas() as $r) $caloriesTotal += $r['calories'] ?? 0;
                $dateDebut = new DateTime($p->getDateDebut());
                $dateFin = new DateTime($p->getDateFin());
                $duree = $dateDebut->diff($dateFin)->days + 1;
            ?>
            data.push([
                "<?= $p->getIdProgramme() ?>",
                "<?= $p->getIdUser() ?>",
                "<?= $p->getObjectif() ?>",
                "<?= $p->getDateDebut() ?>",
                "<?= $p->getDateFin() ?>",
                "<?= $duree ?> jours",
                "<?= $caloriesTotal ?> kcal"
            ]);
            <?php endforeach; ?>
            
            doc.autoTable({
                head: headers,
                body: data,
                startY: 20
            });
            doc.save('programmes_export.pdf');
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Add statistics button event listener
            const statisticsBtn = document.getElementById('statisticsBtn');
            if (statisticsBtn) {
                statisticsBtn.addEventListener('click', showStatistics);
                console.log('Statistics button found and event listener attached');
            } else {
                console.error('Statistics button not found');
            }

            const rows = document.querySelectorAll('#programmeTable tbody tr');
            if (rows.length > 0) {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                for (let i = 1; i <= Math.min(totalPages, 3); i++) {
                    const btn = document.getElementById('page' + i);
                    if (btn) {
                        btn.style.display = 'inline-block';
                        btn.textContent = i;
                        btn.onclick = () => showPage(i);
                    }
                }
                showPage(1);
            }
        });

        // Statistics Modal Functions
        function showStatistics() {
            console.log('Statistics button clicked');
            const modal = document.getElementById('statisticsModal');
            console.log('Modal element:', modal);
            if (modal) {
                console.log('Adding show class to modal');
                modal.classList.add('show');
                modal.style.display = 'flex'; // Force display
                loadProgramStatistics();
            } else {
                console.error('Modal element not found');
            }
        }

        function closeStatistics() {
            const modal = document.getElementById('statisticsModal');
            modal.classList.remove('show');
        }

        function loadProgramStatistics() {
            console.log('Loading program statistics...');
            const container = document.getElementById('programStatsContent');
            console.log('Container element:', container);
            if (container) {
                container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner"></i><p>Chargement des statistiques...</p></div>';
            } else {
                console.error('Container element not found');
                return;
            }

            // First test database connection, then get statistics
            const testDbAndLoadStats = async () => {
                try {
                    // Test debug endpoint first
                    console.log('Testing debug endpoint...');
                    const debugResponse = await fetch('api/debug.php');
                    const debugText = await debugResponse.text();
                    console.log('Debug response:', debugText);
                    
                    // Test simple stats endpoint
                    console.log('Testing simple stats endpoint...');
                    const statsResponse = await fetch('api/simple_stats.php');
                    const statsText = await statsResponse.text();
                    console.log('Stats response text:', statsText);
                    
                    let statsData;
                    try {
                        statsData = JSON.parse(statsText);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response that failed to parse:', statsText);
                        showDatabaseError('Invalid JSON from API: ' + e.message);
                        return;
                    }
                    
                    console.log('Stats data:', statsData);
                    
                    if (statsData.status === 'error') {
                        showDatabaseError(statsData.message);
                        return;
                    }
                    
                    if (statsData.data.total_programs === 0) {
                        container.innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #666;">
                                <i class="fas fa-database" style="font-size: 3rem; margin-bottom: 20px; color: #27ae60;"></i>
                                <h3>Base de données connectée</h3>
                                <p>La base de données est accessible mais ne contient aucun programme.</p>
                                <p style="font-size: 0.9rem; color: #999;">Ajoutez des programmes pour voir les statistiques.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    // Database is working, render statistics
                    renderProgramStats(statsData.data);
                    
                } catch (error) {
                    console.error('Database test failed:', error);
                    showDatabaseError('Test de connexion échoué: ' + error.message);
                }
            };
            
            const loadStatisticsFromAPI = () => {
                // Try multiple API paths
                const apiPaths = [
                    'api/program_statistics.php',
                    './api/program_statistics.php',
                    '/views/BackOffice/api/program_statistics.php'
                ];

                let currentPathIndex = 0;

                function tryNextPath() {
                    if (currentPathIndex >= apiPaths.length) {
                        console.log('All API paths failed, showing database error');
                        showDatabaseError('Tous les chemins d\'API ont échoué');
                        return;
                    }

                    const currentPath = apiPaths[currentPathIndex];
                    console.log(`Trying API path ${currentPathIndex + 1}: ${currentPath}`);
                    
                    fetch(currentPath)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                console.error('Response text:', text);
                                throw new Error('Invalid JSON response from server');
                            }
                        });
                    })
                    .then(data => {
                        console.log('Received data:', data);
                        if (data.status === 'success') {
                            renderProgramStats(data.data);
                        } else {
                            console.error('API returned error:', data.message);
                            currentPathIndex++;
                            tryNextPath();
                        }
                    })
                    .catch(error => {
                        console.error(`Error with path ${currentPath}:`, error);
                        currentPathIndex++;
                        tryNextPath();
                    });
                }

                // Start trying API paths
                tryNextPath();
            };

            function showDatabaseError(error) {
                console.log('Database connection failed, showing error message');
                const container = document.getElementById('programStatsContent');
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <i class="fas fa-database" style="font-size: 3rem; margin-bottom: 20px; color: #e74c3c;"></i>
                        <h3>Erreur de connexion à la base de données</h3>
                        <p>Impossible de charger les statistiques depuis la base de données.</p>
                        <p style="font-size: 0.9rem; color: #999;">Vérifiez que la base de données est accessible et contient des programmes.</p>
                        <p style="font-size: 0.8rem; color: #999; margin-top: 20px;">Erreur: ${error}</p>
                    </div>
                `;
            }

            // Start with database test
            testDbAndLoadStats();
        }

        function renderProgramStats(data) {
            const container = document.getElementById('programStatsContent');
            
            // Calculate additional metrics
            const totalUsers = data.by_objectif.reduce((sum, stat) => sum + stat.unique_users, 0);
            const avgDuration = data.by_objectif.reduce((sum, stat) => sum + stat.avg_duration, 0) / data.by_objectif.length;
            const mostPopular = data.by_objectif.reduce((max, stat) => stat.count > max.count ? stat : max);
            
            container.innerHTML = `
                <!-- Statistics Header -->
                <div class="stats-header">
                    <div class="stats-title">
                        <h2><i class="fas fa-chart-line"></i> Statistiques des Programmes</h2>
                        <p>Analyse complète des programmes nutritionnels</p>
                    </div>
                    <div class="stats-period">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Période: Tous les programmes</span>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="key-metrics">
                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">${data.total_programs}</div>
                            <div class="metric-label">Total Programmes</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">${totalUsers}</div>
                            <div class="metric-label">Utilisateurs Uniques</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">${Math.round(avgDuration)}</div>
                            <div class="metric-label">Durée Moyenne (jours)</div>
                        </div>
                    </div>
                    <div class="metric-card highlight">
                        <div class="metric-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">${mostPopular.label}</div>
                            <div class="metric-label">Plus Populaire</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-section">
                    <div class="chart-container">
                        <h3><i class="fas fa-chart-pie"></i> Distribution des Programmes</h3>
                        <div class="chart-wrapper">
                            <canvas id="programChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <h3><i class="fas fa-chart-bar"></i> Analyse par Objectif</h3>
                        <div class="chart-wrapper">
                            <canvas id="programBarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Detailed Statistics -->
                <div class="detailed-stats">
                    <h3><i class="fas fa-table"></i> Statistiques Détaillées</h3>
                    <div class="stats-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Objectif</th>
                                    <th>Nombre</th>
                                    <th>Pourcentage</th>
                                    <th>Utilisateurs</th>
                                    <th>Durée Moy.</th>
                                    <th>Tendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.by_objectif.map(stat => `
                                    <tr>
                                        <td>
                                            <span class="objective-badge" style="background-color: ${stat.color}20; color: ${stat.color};">
                                                ${stat.icon} ${stat.label}
                                            </span>
                                        </td>
                                        <td><strong>${stat.count}</strong></td>
                                        <td>
                                            <div class="percentage-bar">
                                                <div class="percentage-fill" style="width: ${stat.percentage}%; background-color: ${stat.color};"></div>
                                                <span class="percentage-text">${stat.percentage}%</span>
                                            </div>
                                        </td>
                                        <td>${stat.unique_users}</td>
                                        <td>${Math.round(stat.avg_duration)}j</td>
                                        <td>
                                            <span class="trend-badge ${stat.percentage > 20 ? 'trend-up' : 'trend-stable'}">
                                                <i class="fas fa-${stat.percentage > 20 ? 'arrow-up' : 'minus'}"></i>
                                                ${stat.percentage > 20 ? 'Populaire' : 'Stable'}
                                            </span>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            // Render doughnut chart
            const chartCanvas = document.getElementById('programChart');
            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
                if (window.programChartModal && typeof window.programChartModal.destroy === 'function') {
                    window.programChartModal.destroy();
                }
                
                window.programChartModal = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.by_objectif.map(stat => stat.label),
                        datasets: [{
                            data: data.by_objectif.map(stat => stat.count),
                            backgroundColor: data.by_objectif.map(stat => stat.color),
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        const stat = data.by_objectif[context.dataIndex];
                                        return [
                                            `${stat.label}: ${stat.count} programmes`,
                                            `${stat.percentage}% du total`,
                                            `${stat.unique_users} utilisateur${stat.unique_users > 1 ? 's' : ''}`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Render bar chart
            const barCanvas = document.getElementById('programBarChart');
            if (barCanvas) {
                const ctx = barCanvas.getContext('2d');
                if (window.programBarChart && typeof window.programBarChart.destroy === 'function') {
                    window.programBarChart.destroy();
                }
                
                window.programBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.by_objectif.map(stat => stat.label),
                        datasets: [
                            {
                                label: 'Nombre de Programmes',
                                data: data.by_objectif.map(stat => stat.count),
                                backgroundColor: data.by_objectif.map(stat => stat.color),
                                borderColor: data.by_objectif.map(stat => stat.color),
                                borderWidth: 2,
                                borderRadius: 8
                            },
                            {
                                label: 'Utilisateurs Uniques',
                                data: data.by_objectif.map(stat => stat.unique_users),
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                borderRadius: 8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('statisticsModal');
            if (e.target === modal) {
                closeStatistics();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeStatistics();
            }
        });
    </script>

    <!-- Statistics Modal -->
    <div id="statisticsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chart-pie"></i> Statistiques des Programmes</h2>
                <button class="modal-close" onclick="closeStatistics()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="programStatsContent">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner"></i>
                        <p>Chargement des statistiques...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>