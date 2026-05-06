<?php
session_start();
include '../../controleurs/ObjectifController.php';
require_once __DIR__ . '/../../models/objectif.php';

$ObjectifController = new ObjectifController();

$selectedUserId = $_GET['user_id'] ?? '';
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'date_debut';
$sortOrder = $_GET['sort_order'] ?? 'DESC';
$dateMin = $_GET['date_min'] ?? '';
$dateMax = $_GET['date_max'] ?? '';

$objectifs = $ObjectifController->advancedSearch($search, $sortBy, $sortOrder, $dateMin, $dateMax);
if ($selectedUserId) {
    $objectifs = array_filter($objectifs, function($o) use ($selectedUserId) {
        return $o->getUserId() == $selectedUserId;
    });
}

$stats = $ObjectifController->getStats($objectifs);
$allUsers = $ObjectifController->getUsers();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Objectifs - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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

        /* Dashboard Stats Cards */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.8rem;
            color: white;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.orange { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .stat-icon.teal { background: linear-gradient(135deg, #009688, #00796B); }
        .stat-icon.purple { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }

        .stat-details {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2c3e50;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        /* Advanced Filters Form */
        .filters-form {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03), 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .filters-form:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.05), 0 3px 10px rgba(0,0,0,0.07);
            transform: translateY(-2px);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-width: 150px;
        }

        .filter-group label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group input, .filter-group select {
            padding: 10px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 0.95rem;
            color: #34495e;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .filter-group input:focus, .filter-group select:focus {
            border-color: #4CAF50;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
            outline: none;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            flex: 1;
            min-width: 200px;
            justify-content: flex-end;
        }

        .btn-filter {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        }

        .btn-reset {
            background: #f1f3f5;
            color: #495057;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-reset:hover {
            background: #e9ecef;
            color: #212529;
            transform: translateY(-2px);
        }

        /* Highlight Row CSS */
        .highlighted-row {
            background-color: #fff9c4 !important; /* Soft yellow */
            transition: background-color 0.5s ease;
        }
        
        .highlighted-row:hover {
            background-color: #fff59d !important;
        }
        .highlighted-row td {
            border-top: 1px solid #fbc02d;
            border-bottom: 1px solid #fbc02d;
        }
        .highlighted-row td:first-child {
            border-left: 4px solid #fbc02d;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s ease;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: #f8f9fa;
            width: 90%;
            max-width: 1200px;
            max-height: 90vh;
            border-radius: 24px;
            padding: 40px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow-y: auto;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .close-modal {
            position: absolute;
            right: 25px;
            top: 20px;
            font-size: 2rem;
            cursor: pointer;
            color: #7f8c8d;
            transition: color 0.3s;
            line-height: 1;
        }

        .close-modal:hover {
            color: #2c3e50;
        }

        .modal-header {
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding-bottom: 15px;
        }

        .modal-header h2 {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1a1a2e;
        }

        /* Chart Section (Inside Modal) */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        @media (max-width: 992px) {
            .charts-section { grid-template-columns: 1fr; }
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

        /* Notification Bell Styles */
        .notification-bell-container { position: relative; }
        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 1.4rem;
            color: #1a1a2e;
            padding: 10px;
            border-radius: 50%;
            background: #f8f9fa;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .notification-bell:hover { background: #e9ecef; }
        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #4CAF50;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 800;
            border: 2px solid white;
        }
        .notifications-dropdown {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 3000;
            padding: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            animation: fadeIn 0.3s ease;
        }
        .notifications-dropdown.show { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .notifications-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .notifications-header h3 { font-size: 1rem; color: #1a1a2e; margin: 0; }
        .insight-item { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 0.95rem; color: #444; }
        .insight-item i { color: #2196F3; }
        .empty-notifications { text-align: center; color: #999; padding: 20px 0; font-size: 0.9rem; }

        /* Heatmap Styles */
        .heatmap-container { margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); }
        .heatmap-grid { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 10px; }
        .heatmap-day { width: 14px; height: 14px; border-radius: 3px; position: relative; }
        .heatmap-day.day-green { background: #4CAF50; }
        .heatmap-day.day-orange { background: #FF9800; }
        .heatmap-day.day-red { background: #f44336; }
        .heatmap-day:hover::after { content: attr(data-date); position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); background: #333; color: white; padding: 2px 5px; border-radius: 3px; font-size: 10px; white-space: nowrap; z-index: 100; }
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
            <div style="display: flex; align-items: center; gap: 20px;">
                <!-- Notification Bell -->
                <form method="GET" style="display: flex; align-items: center; gap: 10px;">
                    <label style="font-weight: 600; font-size: 0.9rem;">Utilisateur:</label>
                    <select name="user_id" onchange="this.form.submit()" style="padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                        <option value="">Tous les utilisateurs</option>
                        <?php foreach ($allUsers as $u): ?>
                            <option value="<?= $u['id_user'] ?>" <?= $selectedUserId == $u['id_user'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <div class="btn-group">
                    <button id="openStatsBtn" class="btn btn-secondary">
                        <i class="fas fa-chart-line"></i>
                        Statistiques
                    </button>
                    <a href="addObjectif.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        Ajouter
                    </a>
                    <a href="suiviList.php" class="btn btn-secondary">
                        <i class="fas fa-tasks"></i>
                        Suivis
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Modal -->
        <div id="statsModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div class="modal-header">
                    <h2><i class="fas fa-chart-pie"></i> Tableau de Bord Analytique</h2>
                </div>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-weight"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-value"><?= $stats['weightProgress'] ?>%</span>
                            <span class="stat-label">Progression Poids</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-value"><?= $stats['calRate'] ?>%</span>
                            <span class="stat-label">Succès Calories</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon teal">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-value"><?= $stats['waterRate'] ?>%</span>
                            <span class="stat-label">Succès Eau</span>
                        </div>
                    </div>
                </div>

                <div class="charts-section">
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #2c3e50;"><i class="fas fa-chart-line"></i> Calories (Performance)</h3>
                        <div class="chart-container">
                            <canvas id="objectifCaloriesChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #2c3e50;"><i class="fas fa-tint"></i> Eau (Performance)</h3>
                        <div class="chart-container">
                            <canvas id="objectifEauChart"></canvas>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
                    <div class="chart-box" style="background: #f8f9fa; border: none;">
                        <h3 style="font-size: 1rem; color: #2c3e50; margin-bottom: 15px;"><i class="fas fa-balance-scale"></i> Écarts Moyens</h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 8px;">
                                <span style="font-size: 0.9rem;">Calories vs Objectif</span>
                                <span style="font-weight: 700; color: <?= $stats['avgCalDiff'] > 0 ? '#e74c3c' : '#2ecc71' ?>;">
                                    <?= $stats['avgCalDiff'] > 0 ? '+' : '' ?><?= $stats['avgCalDiff'] ?> kcal
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 8px;">
                                <span style="font-size: 0.9rem;">Eau vs Objectif</span>
                                <span style="font-weight: 700; color: <?= $stats['avgWaterDiff'] < 0 ? '#e74c3c' : '#2ecc71' ?>;">
                                    <?= $stats['avgWaterDiff'] > 0 ? '+' : '' ?><?= $stats['avgWaterDiff'] ?> L
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="heatmap-container">
                    <h3 style="font-size: 1rem; color: #2c3e50; margin-bottom: 10px;"><i class="fas fa-th"></i> Calendrier d'adhérence</h3>
                    <div class="heatmap-grid">
                        <?php foreach ($stats['heatmap'] as $day): ?>
                            <div class="heatmap-day day-<?= $day['status'] ?>" data-date="<?= $day['date'] ?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="heatmap-legend" style="display: flex; gap: 15px; margin-top: 15px; font-size: 0.85rem; color: #666; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 14px; height: 14px; background: #4CAF50; border-radius: 3px;"></div> <strong>Parfait</strong> (Calories & Eau OK)
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 14px; height: 14px; background: #FF9800; border-radius: 3px;"></div> <strong>Partiel</strong> (Calories ou Eau OK)
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 14px; height: 14px; background: #f44336; border-radius: 3px;"></div> <strong>Échec</strong> (Aucun objectif)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="filters-form" method="GET">
            <div class="filter-group">
                <label for="search">Recherche</label>
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nom, Poids...">
            </div>
            <div class="filter-group">
                <label for="sort_by">Trier par</label>
                <select name="sort_by" id="sort_by">
                    <option value="date_debut" <?= $sortBy == 'date_debut' ? 'selected' : '' ?>>Date de début</option>
                    <option value="poids_cible" <?= $sortBy == 'poids_cible' ? 'selected' : '' ?>>Poids cible</option>
                    <option value="calories_objectif" <?= $sortBy == 'calories_objectif' ? 'selected' : '' ?>>Calories</option>
                    <option value="eau_objectif" <?= $sortBy == 'eau_objectif' ? 'selected' : '' ?>>Eau</option>
                    <option value="nom" <?= $sortBy == 'nom' ? 'selected' : '' ?>>Utilisateur</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort_order">Ordre</label>
                <select name="sort_order" id="sort_order">
                    <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Décroissant</option>
                    <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Croissant</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="date_min">Date Min</label>
                <input type="date" name="date_min" id="date_min" value="<?= htmlspecialchars($dateMin) ?>">
            </div>
            <div class="filter-group">
                <label for="date_max">Date Max</label>
                <input type="date" name="date_max" id="date_max" value="<?= htmlspecialchars($dateMax) ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="objectifList.php" class="btn-reset"><i class="fas fa-sync"></i> Réinit.</a>
            </div>
        </form>

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
                            <?php $isHighlighted = !empty($search) ? 'highlighted-row' : ''; ?>
                            <tr class="<?= $isHighlighted ?>">
                                <td>#<?= htmlspecialchars($objectif->getId()) ?></td>
                                <td><?= htmlspecialchars($objectif->getUserName()) ?></td>
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
            <div class="btn-group">
                <button onclick="exportToExcel()" class="export-btn" style="background: #2e7d32;">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </button>
                <button onclick="exportToPDF()" class="export-btn" style="background: #c62828;">
                    <i class="fas fa-file-pdf"></i>
                    PDF
                </button>
            </div>
        </div>
    </div>
    <script>

        // Modal Logic
        const modal = document.getElementById("statsModal");
        const btn = document.getElementById("openStatsBtn");
        const span = document.getElementsByClassName("close-modal")[0];

        btn.onclick = function() {
            modal.style.display = "flex";
            setTimeout(() => modal.classList.add('show'), 10);
        }

        span.onclick = function() {
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = "none", 300);
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('show');
                setTimeout(() => modal.style.display = "none", 300);
            }
        }

        // Charts Logic (SMART Performance)
        const ctxCal = document.getElementById('objectifCaloriesChart').getContext('2d');
        new Chart(ctxCal, {
            type: 'line',
            data: {
                labels: <?= json_encode($stats['trend']['labels']) ?>,
                datasets: [{
                    label: 'Calories Consommées',
                    data: <?= json_encode($stats['trend']['cal']) ?>,
                    borderColor: '#FF9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const ctxEau = document.getElementById('objectifEauChart').getContext('2d');
        new Chart(ctxEau, {
            type: 'line',
            data: {
                labels: <?= json_encode($stats['trend']['labels']) ?>,
                datasets: [{
                    label: 'Eau Bue (L)',
                    data: <?= json_encode($stats['trend']['water']) ?>,
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
        // Export Logic
        function exportToExcel() {
            const table = document.getElementById("objectifsTable");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Objectifs" });
            XLSX.writeFile(wb, "Objectifs_Nutrition.xlsx");
        }

        function exportToPDF() {
            const element = document.getElementById('objectifsTable');
            const opt = {
                margin: 10,
                filename: 'Objectifs_Nutrition.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
