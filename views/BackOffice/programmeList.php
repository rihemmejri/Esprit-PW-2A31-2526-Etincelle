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

// Statistiques détaillées pour les graphiques
$objectifStats = [];
$objectifUsers = [];
$objectifDurations = [];

foreach ($programmes as $p) {
    $repasDuProgramme = $p->getRepas();
    $nbRepas = count($repasDuProgramme);
    $totalRepasProgrammes += $nbRepas;
    
    $caloriesProgramme = 0;
    foreach ($repasDuProgramme as $r) {
        $caloriesProgramme += $r['calories'] ?? 0;
    }
    $totalCaloriesProgrammes += $caloriesProgramme;
    
    // Stats par objectif
    $obj = $p->getObjectif();
    $objectifStats[$obj] = ($objectifStats[$obj] ?? 0) + 1;
    $objectifUsers[$obj][$p->getIdUser()] = true;
    
    // Durée
    $dateDebut = new DateTime($p->getDateDebut());
    $dateFin = new DateTime($p->getDateFin());
    $duree = $dateDebut->diff($dateFin)->days + 1;
    $objectifDurations[$obj][] = $duree;
}

// Préparer les données pour les graphiques
$chartLabels = [];
$chartData = [];
$chartColors = [];
$chartIcons = [];
$chartUsers = [];
$chartAvgDuration = [];
$chartPercentages = [];

$objectifLabels = [
    'PERDRE_POIDS' => 'Perdre du poids',
    'PRENDRE_MUSCLE' => 'Prendre du muscle',
    'MAINTENIR' => 'Maintenir',
    'EQUILIBRE' => 'Équilibre'
];
$objectifColors = [
    'PERDRE_POIDS' => '#ff6b6b',
    'PRENDRE_MUSCLE' => '#4CAF50',
    'MAINTENIR' => '#2196F3',
    'EQUILIBRE' => '#ff9800'
];
$objectifIcons = [
    'PERDRE_POIDS' => '🔥',
    'PRENDRE_MUSCLE' => '💪',
    'MAINTENIR' => '⚖️',
    'EQUILIBRE' => '🥗'
];

foreach ($objectifStats as $obj => $count) {
    $chartLabels[] = $objectifLabels[$obj];
    $chartData[] = $count;
    $chartColors[] = $objectifColors[$obj];
    $chartIcons[] = $objectifIcons[$obj];
    $chartUsers[] = count($objectifUsers[$obj]);
    $chartAvgDuration[] = round(array_sum($objectifDurations[$obj]) / count($objectifDurations[$obj]), 1);
    $chartPercentages[] = round(($count / $totalProgrammes) * 100, 1);
}

$totalUsers = 0;
foreach ($objectifUsers as $users) {
    $totalUsers += count($users);
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .header h1 { font-size: 1.8rem; color: #1a1a2e; }
        .header h1 i { color: #4CAF50; margin-right: 10px; }
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
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover { background: #45a049; transform: translateY(-2px); }

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
        .stats { display: flex; gap: 25px; flex-wrap: wrap; }
        .stat { display: flex; align-items: center; gap: 8px; }
        .stat i { font-size: 1.2rem; color: #4CAF50; }
        .search-box { display: flex; gap: 5px; }
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

        /* Table */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        th {
            background: #1a1a2e;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
        }
        th i { margin-right: 8px; }
        th.sortable { cursor: pointer; transition: background 0.2s; }
        th.sortable:hover { background: #2a2a4e; }
        th.sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-left: 8px;
            opacity: 0.5;
        }
        th.sortable.asc::after { content: '\f0de'; opacity: 1; color: #4CAF50; }
        th.sortable.desc::after { content: '\f0dd'; opacity: 1; color: #4CAF50; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:hover { background: #f8f9fa; }

        /* Badges */
        .badge-objectif {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .objectif-PERDRE_POIDS { background: #ffebee; color: #c62828; }
        .objectif-PRENDRE_MUSCLE { background: #e8f5e9; color: #2e7d32; }
        .objectif-MAINTENIR { background: #e3f2fd; color: #1565c0; }
        .objectif-EQUILIBRE { background: #fff3e0; color: #e65100; }
        .badge-duree { background: #f0f0f0; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; }
        .repas-preview { display: flex; flex-wrap: wrap; gap: 5px; max-width: 250px; }
        .repas-mini {
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-calories {
            background: #fff3e0;
            color: #e65100;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .actions { display: flex; gap: 8px; }
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.2s;
        }
        .edit-btn { background: #2196F3; color: white; }
        .edit-btn:hover { background: #1976D2; }
        .delete-btn { background: #dc3545; color: white; }
        .delete-btn:hover { background: #c82333; }

        /* Footer */
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pagination { display: flex; gap: 8px; }
        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
        }
        .page-btn.active { background: #4CAF50; color: white; border-color: #4CAF50; }
        .export-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        .empty-message { text-align: center; padding: 60px; color: #999; }
        .tfoot { background: #f8f9fa; font-weight: bold; }
        .tfoot td { padding: 15px 12px; border-top: 2px solid #ddd; }
        .text-center { text-align: center; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal.show { display: flex; }
        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 1000px;
            max-height: 90vh;
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 { margin: 0; font-size: 1.5rem; }
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .modal-body { padding: 25px; max-height: calc(90vh - 80px); overflow-y: auto; }

        /* Statistics Styles */
        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        .stats-title h2 { margin: 0; color: #2c3e50; font-size: 1.8rem; font-weight: 600; }
        .stats-title p { margin: 0.5rem 0 0 0; color: #7f8c8d; font-size: 0.95rem; }
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
            transition: transform 0.3s;
        }
        .metric-card:hover { transform: translateY(-2px); }
        .metric-card.highlight { border-left-color: #ff6b6b; background: linear-gradient(135deg, #fff 0%, #ff6b6b10 100%); }
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
        .metric-card.highlight .metric-icon { background: #ff6b6b; }
        .metric-value { font-size: 2rem; font-weight: 700; color: #2c3e50; margin-bottom: 0.5rem; }
        .metric-label { color: #7f8c8d; font-size: 0.9rem; font-weight: 500; }
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
        .chart-container h3 { margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.2rem; }
        .chart-wrapper { height: 300px; position: relative; }
        .detailed-stats {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .detailed-stats h3 { margin: 0 0 1.5rem 0; color: #2c3e50; }
        .stats-table { overflow-x: auto; }
        .stats-table table { width: 100%; border-collapse: collapse; }
        .stats-table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        .stats-table td { padding: 1rem; border-bottom: 1px solid #f1f3f4; }
        .percentage-bar {
            position: relative;
            background: #f1f3f4;
            border-radius: 10px;
            height: 8px;
            min-width: 100px;
        }
        .percentage-fill { height: 100%; border-radius: 10px; transition: width 0.5s ease; }
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
        .trend-up { background: #d4edda; color: #155724; }
        .trend-stable { background: #fff3cd; color: #856404; }
        .loading-spinner { text-align: center; padding: 40px; color: #666; }
        .loading-spinner i { font-size: 2rem; animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        @media (max-width: 768px) {
            .charts-section { grid-template-columns: 1fr; }
            .chart-wrapper { height: 250px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Gestion des Programmes</h1>
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
                    <span><strong><?= number_format($totalCaloriesProgrammes) ?></strong> kcal total</span>
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
                            $dateDebut = new DateTime($programme->getDateDebut());
                            $dateFin = new DateTime($programme->getDateFin());
                            $duree = $dateDebut->diff($dateFin)->days + 1;
                            
                            $objectifClass = '';
                            $objectifIcon = '';
                            $objectifText = '';
                            switch($programme->getObjectif()) {
                                case 'PERDRE_POIDS': $objectifClass = 'objectif-PERDRE_POIDS'; $objectifIcon = '🔥'; $objectifText = 'Perdre du poids'; break;
                                case 'PRENDRE_MUSCLE': $objectifClass = 'objectif-PRENDRE_MUSCLE'; $objectifIcon = '💪'; $objectifText = 'Prendre du muscle'; break;
                                case 'MAINTENIR': $objectifClass = 'objectif-MAINTENIR'; $objectifIcon = '⚖️'; $objectifText = 'Maintenir'; break;
                                case 'EQUILIBRE': $objectifClass = 'objectif-EQUILIBRE'; $objectifIcon = '🥗'; $objectifText = 'Équilibre'; break;
                                default: $objectifClass = ''; $objectifIcon = '🎯'; $objectifText = $programme->getObjectif();
                            }
                        ?>
                            <tr>
                                <td class="text-center"><strong>#<?= $programme->getIdProgramme() ?></strong></td>
                                <td class="text-center">ID: <?= $programme->getIdUser() ?></td>
                                <td><span class="badge-objectif <?= $objectifClass ?>"><?= $objectifIcon ?> <?= $objectifText ?></span></td>
                                <td><?= $programme->getDateDebut() ?></td>
                                <td><?= $programme->getDateFin() ?></td>
                                <td><span class="badge-duree"><i class="fas fa-calendar-week"></i> <?= $duree ?> jours</span></td>
                                <td>
                                    <div class="repas-preview">
                                        <span class="badge-duree" style="background:#4CAF50; color:white;"><i class="fas fa-utensils"></i> <?= $nbRepas ?> repas</span>
                                        <?php 
                                        $premiersRepas = array_slice($repasListe, 0, 3);
                                        foreach ($premiersRepas as $item): ?>
                                            <span class="repas-mini"><i class="fas fa-utensils"></i> <?= htmlspecialchars(substr($item['nom'] ?? 'Repas', 0, 12)) ?></span>
                                        <?php endforeach; ?>
                                        <?php if ($nbRepas > 3): ?>
                                            <span class="repas-mini"><i class="fas fa-ellipsis-h"></i> +<?= $nbRepas - 3 ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><span class="badge-calories"><i class="fas fa-fire"></i> <?= number_format($caloriesTotal) ?> kcal</span></td>
                                <td class="actions">
                                    <a href="editProgramme.php?id=<?= $programme->getIdProgramme() ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $programme->getIdProgramme() ?>); return false;"><i class="fas fa-trash"></i> Suppr</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="empty-message"><i class="fas fa-folder-open" style="font-size: 3rem;"></i><p>Aucun programme trouvé</p><a href="addProgramme.php" class="btn-primary" style="margin-top: 10px;">Créer un programme</a></td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if ($totalProgrammes > 0): ?>
                    <tfoot class="tfoot">
                        <tr><td colspan="6"><strong>Totaux :</strong></td><td><strong><?= $totalRepasProgrammes ?> repas</strong></td><td><strong><?= number_format($totalCaloriesProgrammes) ?> kcal</strong></td><td></td></tr>
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
                <button class="export-btn" onclick="exportTable()" style="margin-right: 10px;"><i class="fas fa-file-csv"></i> CSV</button>
                <button class="export-btn" style="background: #e53935;" onclick="exportPDF()"><i class="fas fa-file-pdf"></i> PDF</button>
            </div>
        </div>
    </div>

    <!-- Statistics Modal -->
    <div id="statisticsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chart-pie"></i> Statistiques des Programmes</h2>
                <button class="modal-close" onclick="closeStatistics()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="programStatsContent">
                <!-- Les statistiques seront chargées ici -->
            </div>
        </div>
    </div>

    <script>
        // Variables pour la pagination
        let currentPage = 1;
        const rowsPerPage = 10;
        let sortDirection = false;

        // Données PHP passées à JavaScript pour les graphiques
        const chartLabels = <?= json_encode($chartLabels) ?>;
        const chartData = <?= json_encode($chartData) ?>;
        const chartColors = <?= json_encode($chartColors) ?>;
        const chartIcons = <?= json_encode($chartIcons) ?>;
        const chartUsers = <?= json_encode($chartUsers) ?>;
        const chartAvgDuration = <?= json_encode($chartAvgDuration) ?>;
        const chartPercentages = <?= json_encode($chartPercentages) ?>;
        const totalProgrammes = <?= $totalProgrammes ?>;
        const totalUsers = <?= $totalUsers ?>;

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
                if(a.classList.contains('tfoot') || b.classList.contains('tfoot')) return 0;
                const aText = a.children[columnIndex]?.textContent.trim() || '';
                const bText = b.children[columnIndex]?.textContent.trim() || '';
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

        function showPage(page) {
            const allRows = Array.from(document.querySelectorAll('#programmeTable tbody tr:not(.tfoot)'));
            const visibleRows = allRows.filter(r => !r.classList.contains('hidden-by-filter') && !r.querySelector('.empty-message'));
            const totalPages = Math.ceil(visibleRows.length / rowsPerPage) || 1;
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            allRows.forEach(row => row.style.display = 'none');
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
                csv += `<?= $p->getIdProgramme() ?>,<?= $p->getIdUser() ?>,<?= addcslashes($p->getObjectif(), '\\') ?>,<?= $p->getDateDebut() ?>,<?= $p->getDateFin() ?>,<?= $duree ?> jours,<?= count($repasListe) ?>,<?= $caloriesTotal ?>\n`;
            <?php endforeach; ?>
            const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'programmes_export.csv';
            a.click();
            URL.revokeObjectURL(url);
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');
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
            data.push(["<?= $p->getIdProgramme() ?>", "<?= $p->getIdUser() ?>", "<?= addcslashes($p->getObjectif(), '\\') ?>", "<?= $p->getDateDebut() ?>", "<?= $p->getDateFin() ?>", "<?= $duree ?> j", "<?= $caloriesTotal ?> kcal"]);
            <?php endforeach; ?>
            doc.autoTable({ head: headers, body: data, startY: 20, styles: { fontSize: 8 }, headStyles: { fillColor: [41, 128, 185] } });
            doc.save('programmes_export.pdf');
        }

        // Statistics Modal Functions - Version PHP directe sans API
        function showStatistics() {
            const modal = document.getElementById('statisticsModal');
            modal.classList.add('show');
            renderProgramStats();
        }

        function closeStatistics() {
            const modal = document.getElementById('statisticsModal');
            modal.classList.remove('show');
        }

        function renderProgramStats() {
            const container = document.getElementById('programStatsContent');
            
            if (totalProgrammes === 0) {
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

            const mostPopularIndex = chartData.indexOf(Math.max(...chartData));
            const mostPopularLabel = chartLabels[mostPopularIndex];
            
            container.innerHTML = `
                <div class="stats-header">
                    <div class="stats-title">
                        <h2><i class="fas fa-chart-line"></i> Statistiques des Programmes</h2>
                        <p>Analyse complète des programmes nutritionnels</p>
                    </div>
                </div>

                <div class="key-metrics">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="metric-value">${totalProgrammes}</div>
                        <div class="metric-label">Total Programmes</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-users"></i></div>
                        <div class="metric-value">${totalUsers}</div>
                        <div class="metric-label">Utilisateurs Uniques</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-clock"></i></div>
                        <div class="metric-value">${Math.round(chartAvgDuration.reduce((a,b) => a+b, 0) / chartAvgDuration.length)}</div>
                        <div class="metric-label">Durée Moyenne (jours)</div>
                    </div>
                    <div class="metric-card highlight">
                        <div class="metric-icon"><i class="fas fa-trophy"></i></div>
                        <div class="metric-value">${mostPopularLabel}</div>
                        <div class="metric-label">Plus Populaire</div>
                    </div>
                </div>

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

                <div class="detailed-stats">
                    <h3><i class="fas fa-table"></i> Statistiques Détaillées</h3>
                    <div class="stats-table">
                        <table>
                            <thead>
                                <tr><th>Objectif</th><th>Nombre</th><th>Pourcentage</th><th>Utilisateurs</th><th>Durée Moy.</th><th>Tendance</th></tr>
                            </thead>
                            <tbody>
                                ${chartLabels.map((label, i) => `
                                    <tr>
                                        <td><span style="color: ${chartColors[i]};">${chartIcons[i]} ${label}</span></td>
                                        <td><strong>${chartData[i]}</strong></td>
                                        <td><div class="percentage-bar"><div class="percentage-fill" style="width: ${chartPercentages[i]}%; background-color: ${chartColors[i]};"></div><span class="percentage-text">${chartPercentages[i]}%</span></div></td>
                                        <td>${chartUsers[i]}</td>
                                        <td>${Math.round(chartAvgDuration[i])}j</td>
                                        <td><span class="trend-badge ${chartPercentages[i] > 20 ? 'trend-up' : 'trend-stable'}"><i class="fas fa-${chartPercentages[i] > 20 ? 'arrow-up' : 'minus'}"></i> ${chartPercentages[i] > 20 ? 'Populaire' : 'Stable'}</span></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            // Doughnut Chart
            const chartCanvas = document.getElementById('programChart');
            if (chartCanvas) {
                if (window.programChartModal) window.programChartModal.destroy();
                window.programChartModal = new Chart(chartCanvas, {
                    type: 'doughnut',
                    data: { labels: chartLabels, datasets: [{ data: chartData, backgroundColor: chartColors, borderWidth: 3, borderColor: '#fff' }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                });
            }

            // Bar Chart
            const barCanvas = document.getElementById('programBarChart');
            if (barCanvas) {
                if (window.programBarChart) window.programBarChart.destroy();
                window.programBarChart = new Chart(barCanvas, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            { label: 'Programmes', data: chartData, backgroundColor: chartColors, borderRadius: 8 },
                            { label: 'Utilisateurs', data: chartUsers, backgroundColor: 'rgba(54, 162, 235, 0.8)', borderRadius: 8 }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const statisticsBtn = document.getElementById('statisticsBtn');
            if (statisticsBtn) {
                statisticsBtn.addEventListener('click', showStatistics);
            }

            const rows = document.querySelectorAll('#programmeTable tbody tr');
            if (rows.length > 0 && !rows[0].querySelector('.empty-message')) {
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

        // Close modal on outside click
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('statisticsModal');
            if (e.target === modal) closeStatistics();
        });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeStatistics(); });
    </script>
</body>
</html>