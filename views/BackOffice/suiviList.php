<?php
session_start();
include '../../controleurs/SuiviController.php';
require_once __DIR__ . '/../../models/suivi.php';

$SuiviController = new SuiviController();

$selectedUserId = $_GET['user_id'] ?? '';
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'date';
$sortOrder = $_GET['sort_order'] ?? 'DESC';
$dateMin = $_GET['date_min'] ?? '';
$dateMax = $_GET['date_max'] ?? '';

// If a specific user is selected, force it in the search logic or filter results
$suivis = $SuiviController->advancedSearch($search, $sortBy, $sortOrder, $dateMin, $dateMax);
if ($selectedUserId) {
    $suivis = array_filter($suivis, function($s) use ($selectedUserId) {
        return $s->getUserId() == $selectedUserId;
    });
}

$stats = $SuiviController->getStats($suivis);
$allUsers = $SuiviController->getUsers();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Suivis - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
        /* Dashboard Stats Cards */
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #ffffff, #f8f9fa); padding: 20px; border-radius: 16px; box-shadow: 0 10px 20px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 20px; border: 1px solid rgba(0,0,0,0.04); transition: transform 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
        .stat-icon { width: 60px; height: 60px; border-radius: 14px; display: flex; justify-content: center; align-items: center; font-size: 1.8rem; color: white; }
        .stat-icon.blue { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.orange { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .stat-icon.teal { background: linear-gradient(135deg, #009688, #00796B); }
        .stat-icon.purple { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }
        .stat-details { display: flex; flex-direction: column; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: #2c3e50; line-height: 1.2; }
        .stat-label { font-size: 0.85rem; font-weight: 700; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px; }
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
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* Heatmap Styles */
        .heatmap-container { margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); }
        .heatmap-grid { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
        .heatmap-day { width: 15px; height: 15px; border-radius: 3px; position: relative; }
        .day-green { background: #4CAF50; }
        .day-orange { background: #FF9800; }
        .day-red { background: #F44336; }
        .heatmap-day:hover::after { content: attr(data-date); position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); background: #333; color: white; padding: 2px 5px; border-radius: 3px; font-size: 10px; white-space: nowrap; z-index: 100; }

        .insights-section { margin-top: 30px; background: #fdfdfd; padding: 20px; border-radius: 12px; border-left: 4px solid #2196F3; }
        .insight-item { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 0.95rem; color: #444; }
        .insight-item i { color: #2196F3; }

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
        .empty-notifications { text-align: center; color: #999; padding: 20px 0; font-size: 0.9rem; }

        /* AI Chatbot Widget Styles */
        .ai-chatbot-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 5000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .chatbot-button {
            background: linear-gradient(135deg, #1a1a2e, #003366);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid rgba(255,255,255,0.1);
        }

        .chatbot-button:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .chatbot-icon {
            width: 35px;
            height: 35px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .chatbot-label {
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .chatbot-popup {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 380px;
            height: 500px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            animation: popupSlideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popupSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chatbot-popup.show {
            display: flex;
        }

        .chatbot-header {
            background: #1a1a2e;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-header h3 {
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .close-chatbot {
            cursor: pointer;
            opacity: 0.7;
            transition: 0.3s;
        }

        .close-chatbot:hover { opacity: 1; }

        .chatbot-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #f8f9fa;
        }

        .chat-msg {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 0.9rem;
            line-height: 1.4;
            animation: msgFadeIn 0.3s ease;
        }

        @keyframes msgFadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chat-msg.ai {
            align-self: flex-start;
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .chat-msg.user {
            align-self: flex-end;
            background: #4CAF50;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .chatbot-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chatbot-input-area input {
            flex-grow: 1;
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 0.9rem;
        }

        .chatbot-input-area input:focus { border-color: #4CAF50; }

        .chatbot-input-area button {
            background: #4CAF50;
            color: white;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chatbot-input-area button:hover { transform: scale(1.1); }
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
                    <button id="openStatsBtn" class="btn btn-secondary"><i class="fas fa-chart-line"></i> Statistiques</button>
                    <a href="addSuivi.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Ajouter</a>
                    <a href="objectifList.php" class="btn btn-secondary"><i class="fas fa-bullseye"></i> Objectifs</a>
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
                        <div class="stat-icon blue">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-value"><?= $stats['globalScore'] ?>/100</span>
                            <span class="stat-label">Score Global</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-check-circle"></i>
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
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #2c3e50;"><i class="fas fa-chart-area"></i> Tendance Nutrition</h3>
                        <div class="chart-container">
                            <canvas id="caloriesChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #2c3e50;"><i class="fas fa-chart-pie"></i> Volume d'Eau</h3>
                        <div class="chart-container">
                            <canvas id="eauChart"></canvas>
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
                    <option value="date" <?= $sortBy == 'date' ? 'selected' : '' ?>>Date</option>
                    <option value="poids" <?= $sortBy == 'poids' ? 'selected' : '' ?>>Poids</option>
                    <option value="calories_consommees" <?= $sortBy == 'calories_consommees' ? 'selected' : '' ?>>Calories</option>
                    <option value="eau_bue" <?= $sortBy == 'eau_bue' ? 'selected' : '' ?>>Eau</option>
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
                <a href="suiviList.php" class="btn-reset"><i class="fas fa-sync"></i> Réinit.</a>
            </div>
        </form>

        <div class="table-container">
            <table id="suivisTable">
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
                            <?php $isHighlighted = !empty($search) ? 'highlighted-row' : ''; ?>
                            <tr class="<?= $isHighlighted ?>">
                                <td>#<?= $s->getId() ?></td>
                                <td><?= htmlspecialchars($s->getUserName()) ?></td>
                                <td><?= $s->getPoidsCible() !== 'N/A' ? $s->getPoidsCible() . ' kg' : 'N/A' ?></td>
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

        <div class="footer" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div class="pagination" style="display: flex; gap: 8px;">
                <button class="page-btn" style="width: 40px; height: 40px; border: 1px solid #ddd; background: white; border-radius: 8px; cursor: pointer;"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active" style="width: 40px; height: 40px; border: 1px solid #4CAF50; background: #4CAF50; color: white; border-radius: 8px; cursor: pointer;">1</button>
                <button class="page-btn" style="width: 40px; height: 40px; border: 1px solid #ddd; background: white; border-radius: 8px; cursor: pointer;"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="btn-group">
                <button onclick="exportToExcel()" class="btn" style="background: #2e7d32; color: white;">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </button>
                <button onclick="exportToPDF()" class="btn" style="background: #c62828; color: white;">
                    <i class="fas fa-file-pdf"></i>
                    PDF
                </button>
            </div>
        </div>
    </div>

            <div class="chatbot-input-area">
                <input type="text" id="chatbotInput" placeholder="Ex: 2 oeufs + café..." autocomplete="off">
                <button id="chatbotSendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <div class="chatbot-button" id="openChatbot">
            <div class="chatbot-icon">
                <i class="fas fa-robot"></i>
            </div>
            <span class="chatbot-label">Calcul de calories</span>
        </div>
    </div>
    </div>
    <script>
        // Modal Logic ...
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

        // Calories Chart
        const ctxCal = document.getElementById('caloriesChart').getContext('2d');
        new Chart(ctxCal, {
            type: 'bar',
            data: {
                labels: ['Consommées', 'Objectif'],
                datasets: [{
                    label: 'Calories (kcal)',
                    data: [<?= $stats['avgCaloriesConsommees'] ?>, <?= $stats['avgCaloriesObjectif'] ?? 2000 ?>],
                    backgroundColor: ['#FF9800', '#4CAF50'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Eau Chart
        const ctxEau = document.getElementById('eauChart').getContext('2d');
        new Chart(ctxEau, {
            type: 'doughnut',
            data: {
                labels: ['Bue', 'Restante (Est.)'],
                datasets: [{
                    data: [<?= $stats['avgEauBue'] ?>, <?= max(0, ($stats['avgEauObjectif'] ?? 2) - $stats['avgEauBue']) ?>],
                    backgroundColor: ['#00BCD4', '#E0E0E0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%'
            }
        });
        // Export Logic
        function exportToExcel() {
            const table = document.getElementById("suivisTable");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Suivis" });
            XLSX.writeFile(wb, "Suivis_Nutrition.xlsx");
        }

        function exportToPDF() {
            const element = document.getElementById('suivisTable');
            const opt = {
                margin: 10,
                filename: 'Suivis_Nutrition.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save();
        }

        // --- AI Chatbot Widget Logic ---
        const chatbotPopup = document.getElementById('chatbotPopup');
        const openChatbotBtn = document.getElementById('openChatbot');
        const closeChatbotBtn = document.getElementById('closeChatbot');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSendBtn = document.getElementById('chatbotSendBtn');
        const chatbotMessages = document.getElementById('chatbotMessages');

        openChatbotBtn.onclick = () => {
            chatbotPopup.classList.add('show');
            openChatbotBtn.style.opacity = '0';
            openChatbotBtn.style.pointerEvents = 'none';
        };

        closeChatbotBtn.onclick = () => {
            chatbotPopup.classList.remove('show');
            openChatbotBtn.style.opacity = '1';
            openChatbotBtn.style.pointerEvents = 'auto';
        };

        async function sendChatbotMessage() {
            const message = chatbotInput.value.trim();
            if (!message) return;

            chatbotInput.value = '';
            
            // Add user message
            const userMsgDiv = document.createElement('div');
            userMsgDiv.className = 'chat-msg user';
            userMsgDiv.textContent = message;
            chatbotMessages.appendChild(userMsgDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            // Add typing indicator (simple)
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat-msg ai';
            typingDiv.innerHTML = '<i class="fas fa-ellipsis-h fa-beat"></i>';
            chatbotMessages.appendChild(typingDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            try {
                const response = await fetch('../../controleurs/ChatbotController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                chatbotMessages.removeChild(typingDiv);

                const aiMsgDiv = document.createElement('div');
                aiMsgDiv.className = 'chat-msg ai';
                aiMsgDiv.innerHTML = (data.response || 'Désolé, une erreur est survenue.').replace(/\n/g, '<br>');
                chatbotMessages.appendChild(aiMsgDiv);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            } catch (error) {
                chatbotMessages.removeChild(typingDiv);
                console.error('Chatbot error:', error);
            }
        }

        chatbotSendBtn.onclick = sendChatbotMessage;
        chatbotInput.onkeypress = (e) => { if (e.key === 'Enter') sendChatbotMessage(); };
    </script>
</body>
</html>
