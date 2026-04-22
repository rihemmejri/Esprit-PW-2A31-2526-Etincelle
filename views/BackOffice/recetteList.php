<?php
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

// Charger Dompdf
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$RecetteController = new RecetteController();

// Récupérer toutes les recettes
$allRecettes = $RecetteController->listRecettes();

// Récupérer le terme de recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filtrer par recherche
if (!empty($search)) {
    $recettes = array_filter($allRecettes, function($r) use ($search) {
        return stripos($r->getNom(), $search) !== false;
    });
} else {
    $recettes = $allRecettes;
}

// Calcul des statistiques par type de repas
$statsByType = [
    'PETIT_DEJEUNER' => 0,
    'DEJEUNER' => 0,
    'DINER' => 0,
    'DESSERT' => 0
];

foreach ($recettes as $r) {
    $type = $r->getTypeRepas();
    if (isset($statsByType[$type])) {
        $statsByType[$type]++;
    }
}

// Calcul des totaux
$totalTemps = 0;
$totalPersonnes = 0;
foreach ($recettes as $r) {
    $totalTemps += $r->getTempsPreparation();
    $totalPersonnes += $r->getNbPersonne();
}

// Export PDF si demandé
if (isset($_GET['export_pdf'])) {
    // Générer le HTML du PDF avec les mêmes symboles
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Recettes</title>
        <style>
            @page {
                margin: 1.5cm;
                size: landscape;
            }
            body {
                font-family: DejaVu Sans, sans-serif;
                margin: 0;
                padding: 0;
                font-size: 11px;
            }
            h1 {
                color: #4CAF50;
                text-align: center;
                margin-bottom: 5px;
                font-size: 24px;
            }
            .subtitle {
                text-align: center;
                color: #666;
                margin-bottom: 15px;
                font-size: 12px;
            }
            .header-pdf {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #4CAF50;
                padding-bottom: 10px;
            }
            .stats-pdf {
                margin: 15px 0;
                padding: 10px;
                background: #f5f5f5;
                border-radius: 8px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                gap: 15px;
            }
            .stat-item {
                text-align: center;
                font-size: 11px;
            }
            .stat-item strong {
                color: #4CAF50;
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                font-size: 10px;
            }
            th {
                background: #4CAF50;
                color: white;
                padding: 8px 6px;
                text-align: left;
                font-size: 10px;
            }
            td {
                border: 1px solid #ddd;
                padding: 6px;
                vertical-align: top;
            }
            tr:nth-child(even) {
                background: #f9f9f9;
            }
            .footer-pdf {
                margin-top: 20px;
                text-align: center;
                font-size: 9px;
                color: #999;
                border-top: 1px solid #eee;
                padding-top: 10px;
            }
            .badge-difficulte {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 9px;
                font-weight: bold;
                white-space: nowrap;
            }
            .difficulte-FACILE { background: #e8f5e9; color: #2e7d32; }
            .difficulte-MOYEN { background: #fff3e0; color: #e65100; }
            .difficulte-DIFFICILE { background: #ffebee; color: #c62828; }
            .badge-type {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 9px;
                font-weight: bold;
                white-space: nowrap;
            }
            .type-PETIT_DEJEUNER { background: #e3f2fd; color: #1565c0; }
            .type-DEJEUNER { background: #e8f5e9; color: #2e7d32; }
            .type-DINER { background: #f3e5f5; color: #7b1fa2; }
            .type-DESSERT { background: #fce4ec; color: #c2185b; }
            .description-cell {
                max-width: 200px;
                word-wrap: break-word;
                line-height: 1.3;
            }
        </style>
    </head>
    <body>
        <div class="header-pdf">
            <h1>🍽️ Liste des Recettes</h1>
            <div class="subtitle">📅 Exporté le ' . date('d/m/Y à H:i:s') . '</div>
        </div>';
    
    if (!empty($search)) {
        $html .= '<div style="margin-bottom: 15px; padding: 8px; background: #e8f5e9; border-radius: 8px; text-align: center;">
            🔍 Résultats pour : <strong>' . htmlspecialchars($search) . '</strong> (' . count($recettes) . ' recette(s) trouvée(s))
        </div>';
    }
    
    $html .= '<div class="stats-pdf">
        <div class="stat-item">📊 <strong>' . count($recettes) . '</strong> recettes</div>
        <div class="stat-item">⏱️ <strong>' . $totalTemps . '</strong> min total</div>
        <div class="stat-item">👥 <strong>' . $totalPersonnes . '</strong> personnes</div>
        <div class="stat-item">☕ Petit déj: <strong>' . $statsByType['PETIT_DEJEUNER'] . '</strong></div>
        <div class="stat-item">🍽️ Déjeuner: <strong>' . $statsByType['DEJEUNER'] . '</strong></div>
        <div class="stat-item">🌙 Dîner: <strong>' . $statsByType['DINER'] . '</strong></div>
        <div class="stat-item">🍰 Dessert: <strong>' . $statsByType['DESSERT'] . '</strong></div>
    </div>';
    
    $html .= '<table>
            <thead>
                <tr>
                    <th style="width:5%">ID</th>
                    <th style="width:15%">Nom</th>
                    <th style="width:25%">Description</th>
                    <th style="width:8%">⏱️ Temps</th>
                    <th style="width:10%">📊 Difficulté</th>
                    <th style="width:12%">🍽️ Type</th>
                    <th style="width:12%">📍 Origine</th>
                    <th style="width:8%">👥 Personnes</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($recettes as $r) {
        // Garder les mêmes symboles que dans le tableau
        $description = strlen($r->getDescription()) > 120 ? substr($r->getDescription(), 0, 120) . '...' : $r->getDescription();
        
        // Symboles pour le type de repas (identiques au tableau)
        switch($r->getTypeRepas()) {
            case 'PETIT_DEJEUNER': $typeIcon = '☕'; $typeText = 'Petit déjeuner'; break;
            case 'DEJEUNER': $typeIcon = '🍽️'; $typeText = 'Déjeuner'; break;
            case 'DINER': $typeIcon = '🌙'; $typeText = 'Dîner'; break;
            case 'DESSERT': $typeIcon = '🍰'; $typeText = 'Dessert'; break;
            default: $typeIcon = ''; $typeText = $r->getTypeRepas();
        }
        
        // Symboles pour la difficulté (identiques au tableau)
        switch($r->getDifficulte()) {
            case 'FACILE': $difficulteIcon = '😊'; $difficulteText = 'Facile'; break;
            case 'MOYEN': $difficulteIcon = '😐'; $difficulteText = 'Moyen'; break;
            case 'DIFFICILE': $difficulteIcon = '😣'; $difficulteText = 'Difficile'; break;
            default: $difficulteIcon = ''; $difficulteText = $r->getDifficulte();
        }
        
        // Symbole pour l'origine
        $origineIcon = $r->getOrigine() ? '📍 ' : '';
        
        $html .= '<tr>
                    <td style="text-align:center"><strong>#' . $r->getIdRecette() . '</strong></td>
                    <td><strong>' . htmlspecialchars($r->getNom()) . '</strong></td>
                    <td class="description-cell">' . htmlspecialchars($description) . '</td>
                    <td>⏱️ ' . $r->getTempsPreparation() . ' min</td>
                    <td><span class="badge-difficulte difficulte-' . $r->getDifficulte() . '">' . $difficulteIcon . ' ' . $difficulteText . '</span></td>
                    <td><span class="badge-type type-' . $r->getTypeRepas() . '">' . $typeIcon . ' ' . $typeText . '</span></td>
                    <td>' . ($r->getOrigine() ? $origineIcon . htmlspecialchars($r->getOrigine()) : '—') . '</td>
                    <td>👥 ' . $r->getNbPersonne() . '</td>
                 </tr>';
    }
    
    $html .= '</tbody>
        </table>';
    
    $html .= '<div class="footer-pdf">
        <p>🍽️ NutriLoop - Application de gestion nutritionnelle</p>
        <p>📋 Rapport généré le ' . date('d/m/Y') . ' | Toutes les recettes sont présentées à titre informatif</p>
    </div>
    </body>
    </html>';
    
    // Configuration de Dompdf
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    // Téléchargement direct
    $dompdf->stream("recettes_" . date('Ymd_His') . ".pdf", array("Attachment" => true));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Recettes - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .header h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
        }

        .header h1 i {
            color: #4CAF50;
            margin-right: 10px;
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
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        /* Sort Controls */
        .sort-controls {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .sort-controls label {
            font-weight: 600;
            color: #333;
        }

        .sort-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
        }

        .sort-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .sort-btn:hover {
            background: #45a049;
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
            min-width: 1100px;
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

        th.sortable {
            cursor: pointer;
            user-select: none;
        }

        th.sortable:hover {
            background: #2a2a4e;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Badges */
        .badge-difficulte {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .difficulte-FACILE {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .difficulte-MOYEN {
            background: #fff3e0;
            color: #e65100;
        }

        .difficulte-DIFFICILE {
            background: #ffebee;
            color: #c62828;
        }

        .badge-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .type-PETIT_DEJEUNER {
            background: #e3f2fd;
            color: #1565c0;
        }

        .type-DEJEUNER {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .type-DINER {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .type-DESSERT {
            background: #fce4ec;
            color: #c2185b;
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

        .view-btn {
            background: #4CAF50;
            color: white;
        }

        .edit-btn {
            background: #2196F3;
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
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

        .description-cell {
            max-width: 400px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.4;
        }

        /* Footer buttons */
        .footer-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-footer {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 500px;
            max-width: 90%;
            position: relative;
            animation: modalSlide 0.3s ease;
        }

        @keyframes modalSlide {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        canvas {
            max-height: 300px;
            margin: 20px 0;
        }

        .stats-details {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .stat-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .search-active {
            background: #e8f5e9;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .clear-search {
            background: #f44336;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-utensils"></i>
                Gestion des Recettes
            </h1>
            <a href="addRecette.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Ajouter une recette
            </a>
        </div>

        <!-- Search Active Display -->
        <div id="searchActiveDiv" style="display: none;" class="search-active">
            <div class="search-info">
                <i class="fas fa-search" style="color: #4CAF50;"></i>
                <span>Résultats pour : <strong id="searchTerm"></strong></span>
                <span style="color: #666;" id="resultCount"></span>
            </div>
            <button class="clear-search" onclick="clearSearch()">
                <i class="fas fa-times"></i> Effacer la recherche
            </button>
        </div>

        <!-- Sort Controls -->
        <div class="sort-controls">
            <label><i class="fas fa-sort"></i> Trier par :</label>
            <select id="sortField" class="sort-select">
                <option value="id">ID</option>
                <option value="nom">Nom</option>
            </select>
            <select id="sortOrder" class="sort-select">
                <option value="asc">Croissant ↑</option>
                <option value="desc">Décroissant ↓</option>
            </select>
            <button class="sort-btn" onclick="applySort()">
                <i class="fas fa-sort"></i> Appliquer le tri
            </button>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-utensils"></i>
                    <span><strong id="totalRecettes"><?= count($recettes) ?></strong> recettes</span>
                </div>
                <div class="stat">
                    <i class="fas fa-clock"></i>
                    <span><strong><?= $totalTemps ?></strong> min (total)</span>
                </div>
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <span><strong><?= $totalPersonnes ?></strong> personnes</span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher par nom..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table id="recettesTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)">
                            <i class="fas fa-hashtag"></i> ID
                            <i class="fas fa-sort sort-icon" id="sort-icon-0"></i>
                        </th>
                        <th class="sortable" onclick="sortTable(1)">
                            <i class="fas fa-utensils"></i> Nom
                            <i class="fas fa-sort sort-icon" id="sort-icon-1"></i>
                        </th>
                        <th><i class="fas fa-align-left"></i> Description</th>
                        <th><i class="fas fa-clock"></i> Temps (min)</th>
                        <th><i class="fas fa-chart-line"></i> Difficulté</th>
                        <th><i class="fas fa-mug-hot"></i> Type de repas</th>
                        <th><i class="fas fa-globe"></i> Origine</th>
                        <th><i class="fas fa-users"></i> Personnes</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (count($recettes) > 0): ?>
                        <?php foreach ($recettes as $item): ?>
                            <tr>
                                <td><strong>#<?= $item->getIdRecette() ?></strong></td>
                                <td><strong><?= htmlspecialchars($item->getNom()) ?></strong></td>
                                <td class="description-cell"><?= nl2br(htmlspecialchars(substr($item->getDescription(), 0, 120))) ?><?= strlen($item->getDescription()) > 120 ? '...' : '' ?></td>
                                <td><i class="fas fa-hourglass-half"></i> <?= $item->getTempsPreparation() ?> min</td>
                                <td>
                                    <span class="badge-difficulte difficulte-<?= $item->getDifficulte() ?>">
                                        <?php
                                        switch($item->getDifficulte()) {
                                            case 'FACILE': echo '😊 Facile'; break;
                                            case 'MOYEN': echo '😐 Moyen'; break;
                                            case 'DIFFICILE': echo '😣 Difficile'; break;
                                            default: echo $item->getDifficulte();
                                        }
                                        ?>
                                    </span>
                                 </td>
                                <td>
                                    <span class="badge-type type-<?= $item->getTypeRepas() ?>">
                                        <?php
                                        switch($item->getTypeRepas()) {
                                            case 'PETIT_DEJEUNER': echo '☕ Petit déjeuner'; break;
                                            case 'DEJEUNER': echo '🍽️ Déjeuner'; break;
                                            case 'DINER': echo '🌙 Dîner'; break;
                                            case 'DESSERT': echo '🍰 Dessert'; break;
                                            default: echo $item->getTypeRepas();
                                        }
                                        ?>
                                    </span>
                                 </td>
                                <td>
                                    <?php if ($item->getOrigine()): ?>
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($item->getOrigine()) ?>
                                    <?php else: ?>
                                        <span style="color: #999;">Non spécifiée</span>
                                    <?php endif; ?>
                                 </td>
                                <td><i class="fas fa-users"></i> <?= $item->getNbPersonne() ?></td>
                                <td class="actions">
                                    <a href="viewRecette.php?id=<?= $item->getIdRecette() ?>" class="action-btn view-btn">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                    <a href="editRecette.php?id=<?= $item->getIdRecette() ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $item->getIdRecette() ?>); return false;">
                                        <i class="fas fa-trash"></i> Suppr
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="empty-row">
                            <td colspan="9" class="empty-message">
                                <i class="fas fa-empty-folder" style="font-size: 3rem;"></i>
                                <p>Aucune recette trouvée</p>
                                <a href="addRecette.php" class="btn btn-primary" style="margin-top: 10px;">Ajouter une recette</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="tfoot">
                    <tr>
                        <td colspan="3"><strong>Totaux / Moyennes :</strong></td>
                        <td><strong id="avgTime"><?= count($recettes) > 0 ? round($totalTemps / count($recettes)) : 0 ?> min (moy.)</strong></td>
                        <td colspan="2"></td>
                        <td colspan="2"><strong id="avgPers"><?= count($recettes) > 0 ? round($totalPersonnes / count($recettes)) : 0 ?> pers. (moy.)</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer Buttons -->
        <div class="footer-buttons">
            <button class="btn-footer btn-stats" onclick="openStatsModal()">
                <i class="fas fa-chart-pie"></i> Voir les statistiques
            </button>
            <a href="?export_pdf=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn-footer btn-pdf" style="text-decoration: none;">
                <i class="fas fa-file-pdf"></i> Exporter PDF
            </a>
        </div>
    </div>

    <!-- Modal Statistiques -->
    <div id="statsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chart-pie"></i> Statistiques par type de repas</h2>
                <button class="close-modal" onclick="closeStatsModal()">&times;</button>
            </div>
            <canvas id="statsChart"></canvas>
            <div class="stats-details">
                <div class="stat-detail">
                    <span><i class="fas fa-coffee" style="color: #1565c0;"></i> Petit déjeuner</span>
                    <span><strong id="statPetitDej"><?= $statsByType['PETIT_DEJEUNER'] ?></strong> recette(s)</span>
                </div>
                <div class="stat-detail">
                    <span><i class="fas fa-utensils" style="color: #2e7d32;"></i> Déjeuner</span>
                    <span><strong id="statDejeuner"><?= $statsByType['DEJEUNER'] ?></strong> recette(s)</span>
                </div>
                <div class="stat-detail">
                    <span><i class="fas fa-moon" style="color: #7b1fa2;"></i> Dîner</span>
                    <span><strong id="statDiner"><?= $statsByType['DINER'] ?></strong> recette(s)</span>
                </div>
                <div class="stat-detail">
                    <span><i class="fas fa-cake-candles" style="color: #c2185b;"></i> Dessert</span>
                    <span><strong id="statDessert"><?= $statsByType['DESSERT'] ?></strong> recette(s)</span>
                </div>
                <hr>
                <div class="stat-detail" style="font-weight: bold;">
                    <span>Total</span>
                    <span><strong id="statTotal"><?= array_sum($statsByType) ?></strong> recette(s)</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSortColumn = -1;
        let currentSortOrder = 'asc';
        let chart = null;

        function confirmDelete(id) {
            if (confirm('Supprimer cette recette ? Cette action est irréversible.')) {
                window.location.href = 'deleteRecette.php?id=' + id;
            }
        }

        function applySort() {
            const sortField = document.getElementById('sortField').value;
            const sortOrder = document.getElementById('sortOrder').value;
            const rows = Array.from(document.querySelectorAll('#tableBody tr:not(.empty-row)'));
            
            rows.sort((a, b) => {
                let aValue, bValue;
                if (sortField === 'id') {
                    aValue = parseInt(a.cells[0].textContent.replace('#', ''));
                    bValue = parseInt(b.cells[0].textContent.replace('#', ''));
                } else {
                    aValue = a.cells[1].textContent.toLowerCase();
                    bValue = b.cells[1].textContent.toLowerCase();
                }
                
                if (sortOrder === 'asc') {
                    return aValue > bValue ? 1 : -1;
                } else {
                    return aValue < bValue ? 1 : -1;
                }
            });
            
            const tbody = document.getElementById('tableBody');
            rows.forEach(row => tbody.appendChild(row));
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                if (row.classList.contains('empty-row')) return;
                const nomCell = row.cells[1];
                if (nomCell) {
                    const nomValue = nomCell.textContent.toLowerCase();
                    if (filter === "" || nomValue.indexOf(filter) > -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            const searchActiveDiv = document.getElementById('searchActiveDiv');
            if (filter !== "") {
                searchActiveDiv.style.display = 'flex';
                document.getElementById('searchTerm').textContent = filter;
                document.getElementById('resultCount').textContent = `(${visibleCount} recette(s) trouvée(s))`;
            } else {
                searchActiveDiv.style.display = 'none';
            }
            
            updateTotals();
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            searchTable();
        }

        function updateTotals() {
            const rows = document.querySelectorAll('#tableBody tr');
            let totalTemps = 0;
            let totalPersonnes = 0;
            let count = 0;
            
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                if (row.classList.contains('empty-row')) return;
                
                const tempsCell = row.cells[3];
                const personnesCell = row.cells[7];
                
                if (tempsCell) {
                    const tempsText = tempsCell.textContent;
                    const tempsMatch = tempsText.match(/(\d+)/);
                    if (tempsMatch) totalTemps += parseInt(tempsMatch[0]);
                }
                if (personnesCell) {
                    const personnesText = personnesCell.textContent;
                    const personnesMatch = personnesText.match(/(\d+)/);
                    if (personnesMatch) totalPersonnes += parseInt(personnesMatch[0]);
                }
                count++;
            });
            
            const avgTime = count > 0 ? Math.round(totalTemps / count) : 0;
            const avgPers = count > 0 ? Math.round(totalPersonnes / count) : 0;
            
            document.getElementById('avgTime').textContent = avgTime + ' min (moy.)';
            document.getElementById('avgPers').textContent = avgPers + ' pers. (moy.)';
            document.getElementById('totalRecettes').textContent = count;
        }

        function sortTable(columnIndex) {
            const tbody = document.getElementById('tableBody');
            const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-row)'));
            const visibleRows = rows.filter(row => row.style.display !== 'none');
            
            if (visibleRows.length === 0) return;
            
            if (currentSortColumn === columnIndex) {
                currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = columnIndex;
                currentSortOrder = 'asc';
            }
            
            const icons = document.querySelectorAll('.sort-icon');
            icons.forEach(icon => icon.className = 'fas fa-sort sort-icon');
            const currentIcon = document.getElementById('sort-icon-' + columnIndex);
            if (currentIcon) {
                currentIcon.className = currentSortOrder === 'asc' ? 'fas fa-sort-up sort-icon' : 'fas fa-sort-down sort-icon';
            }
            
            visibleRows.sort((a, b) => {
                let aValue, bValue;
                if (columnIndex === 0) {
                    aValue = parseInt(a.cells[0].textContent.replace('#', ''));
                    bValue = parseInt(b.cells[0].textContent.replace('#', ''));
                } else {
                    aValue = a.cells[1].textContent.toLowerCase();
                    bValue = b.cells[1].textContent.toLowerCase();
                }
                
                if (currentSortOrder === 'asc') {
                    return aValue > bValue ? 1 : -1;
                } else {
                    return aValue < bValue ? 1 : -1;
                }
            });
            
            const hiddenRows = rows.filter(row => row.style.display === 'none');
            while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
            visibleRows.forEach(row => tbody.appendChild(row));
            hiddenRows.forEach(row => tbody.appendChild(row));
        }

        function openStatsModal() {
            const modal = document.getElementById('statsModal');
            modal.style.display = 'flex';
            
            const rows = document.querySelectorAll('#tableBody tr');
            let stats = { 'PETIT_DEJEUNER': 0, 'DEJEUNER': 0, 'DINER': 0, 'DESSERT': 0 };
            
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                if (row.classList.contains('empty-row')) return;
                const typeCell = row.cells[5];
                if (typeCell) {
                    const typeText = typeCell.textContent;
                    if (typeText.includes('Petit déjeuner')) stats['PETIT_DEJEUNER']++;
                    else if (typeText.includes('Déjeuner')) stats['DEJEUNER']++;
                    else if (typeText.includes('Dîner')) stats['DINER']++;
                    else if (typeText.includes('Dessert')) stats['DESSERT']++;
                }
            });
            
            document.getElementById('statPetitDej').textContent = stats['PETIT_DEJEUNER'];
            document.getElementById('statDejeuner').textContent = stats['DEJEUNER'];
            document.getElementById('statDiner').textContent = stats['DINER'];
            document.getElementById('statDessert').textContent = stats['DESSERT'];
            document.getElementById('statTotal').textContent = Object.values(stats).reduce((a,b) => a+b, 0);
            
            const ctx = document.getElementById('statsChart').getContext('2d');
            if (chart) chart.destroy();
            
            chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Petit déjeuner', 'Déjeuner', 'Dîner', 'Dessert'],
                    datasets: [{
                        data: [stats['PETIT_DEJEUNER'], stats['DEJEUNER'], stats['DINER'], stats['DESSERT']],
                        backgroundColor: ['#1565c0', '#2e7d32', '#7b1fa2', '#c2185b'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = Object.values(stats).reduce((a,b) => a+b, 0);
                                    const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${context.raw} recette(s) (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function closeStatsModal() {
            document.getElementById('statsModal').style.display = 'none';
            if (chart) { chart.destroy(); chart = null; }
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('statsModal')) closeStatsModal();
        }
    </script>
</body>
</html>