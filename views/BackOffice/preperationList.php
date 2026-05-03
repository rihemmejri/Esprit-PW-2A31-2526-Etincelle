<?php
// views/backOffice/preparation/preperationList.php
session_start();
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/preperation.php';

// Charger Dompdf
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$preperationController = new PreperationController();

// Initialiser les compteurs de vues et likes en session
if (!isset($_SESSION['vue_preparations'])) {
    $_SESSION['vue_preparations'] = [];
}
if (!isset($_SESSION['likes_preparations'])) {
    $_SESSION['likes_preparations'] = [];
}
if (!isset($_SESSION['compteur_likes_prep'])) {
    $_SESSION['compteur_likes_prep'] = [];
}

// Incrémenter le compteur de vues pour la page actuelle
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['vue_preparations'][$currentPage])) {
    $_SESSION['vue_preparations'][$currentPage] = 0;
}
$_SESSION['vue_preparations'][$currentPage]++;

// Récupérer TOUTES les étapes triées par recette puis par ordre
$preperations = $preperationController->listPreperations();

// Trier manuellement par recette puis par ordre
usort($preperations, function($a, $b) {
    $recetteCompare = strcmp($a->getRecetteNom(), $b->getRecetteNom());
    if ($recetteCompare != 0) {
        return $recetteCompare;
    }
    return $a->getOrdre() - $b->getOrdre();
});

// Récupérer le terme de recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filtrer par recherche
if (!empty($search)) {
    $preperations = array_filter($preperations, function($p) use ($search) {
        return stripos($p->getRecetteNom(), $search) !== false || stripos($p->getInstruction(), $search) !== false;
    });
}

// Calcul des totaux
$totalEtapes = count($preperations);
$totalDuree = 0;
foreach ($preperations as $p) {
    $totalDuree += $p->getDuree();
}
$dureeMoyenne = $totalEtapes > 0 ? round($totalDuree / $totalEtapes) : 0;

// Compter les recettes uniques
$recettesUniques = [];
foreach ($preperations as $p) {
    $recettesUniques[$p->getIdRecette()] = $p->getRecetteNom();
}
$nbRecettes = count($recettesUniques);

// Statistiques par outil
$statsOutils = [
    'FOUR' => 0,
    'MIXEUR' => 0,
    'CUILLERE' => 0,
    'RAPE' => 0,
    'AUTRE' => 0
];
foreach ($preperations as $p) {
    $outil = $p->getOutilUtilise();
    if (isset($statsOutils[$outil])) {
        $statsOutils[$outil]++;
    } else if ($outil) {
        $statsOutils['AUTRE']++;
    }
}

// Statistiques par action
$statsActions = [
    'COUPER' => 0,
    'MELANGER' => 0,
    'CUISSON' => 0,
    'AUTRE' => 0
];
foreach ($preperations as $p) {
    $action = $p->getTypeAction();
    if (isset($statsActions[$action])) {
        $statsActions[$action]++;
    } else if ($action) {
        $statsActions['AUTRE']++;
    }
}

// Statistiques par température
$statsTemperature = [
    'Sans température' => 0,
    '0-100°C' => 0,
    '100-200°C' => 0,
    '200°C+' => 0
];
foreach ($preperations as $p) {
    $temp = $p->getTemperature();
    if (!$temp) {
        $statsTemperature['Sans température']++;
    } else if ($temp < 100) {
        $statsTemperature['0-100°C']++;
    } else if ($temp < 200) {
        $statsTemperature['100-200°C']++;
    } else {
        $statsTemperature['200°C+']++;
    }
}

// Calcul des vues et likes totaux
$vuesTotales = array_sum($_SESSION['vue_preparations']);
$likesTotaux = array_sum($_SESSION['compteur_likes_prep']);

// ========== EXPORT PDF ==========
if (isset($_GET['export_pdf'])) {
    // Générer le HTML du PDF avec toutes les infos
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Étapes de Préparation - NutriLoop</title>
        <style>
            @page {
                margin: 1.5cm;
                size: landscape;
            }
            body {
                font-family: DejaVu Sans, sans-serif;
                margin: 0;
                padding: 0;
                font-size: 10px;
                background: white;
            }
            h1 {
                color: #2196f3;
                text-align: center;
                margin-bottom: 5px;
                font-size: 22px;
            }
            .subtitle {
                text-align: center;
                color: #666;
                margin-bottom: 15px;
                font-size: 11px;
            }
            .header-pdf {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #2196f3;
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
                font-size: 10px;
            }
            .stat-item strong {
                color: #2196f3;
                font-size: 13px;
            }
            .charts-stats {
                margin: 15px 0;
                padding: 10px;
                background: #fafafa;
                border-radius: 8px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                gap: 15px;
            }
            .chart-stat {
                text-align: center;
                font-size: 9px;
            }
            .chart-stat span {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 12px;
                margin: 2px;
                font-size: 8px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                font-size: 9px;
            }
            th {
                background: #2196f3;
                color: white;
                padding: 8px 5px;
                text-align: left;
                font-size: 9px;
            }
            td {
                border: 1px solid #ddd;
                padding: 5px;
                vertical-align: top;
            }
            tr:nth-child(even) {
                background: #f9f9f9;
            }
            .footer-pdf {
                margin-top: 20px;
                text-align: center;
                font-size: 8px;
                color: #999;
                border-top: 1px solid #eee;
                padding-top: 10px;
            }
            .badge-action, .badge-outil {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 15px;
                font-size: 8px;
                font-weight: bold;
            }
            .action-COUPER { background: #e3f2fd; color: #1565c0; }
            .action-MELANGER { background: #f3e5f5; color: #7b1fa2; }
            .action-CUISSON { background: #ffebee; color: #c62828; }
            .outil-FOUR { background: #fff3e0; color: #e65100; }
            .outil-MIXEUR { background: #e8f5e9; color: #2e7d32; }
            .outil-CUILLERE { background: #e0f7fa; color: #00838f; }
            .instruction-cell {
                max-width: 200px;
                word-wrap: break-word;
                line-height: 1.3;
            }
        </style>
    </head>
    <body>
        <div class="header-pdf">
            <h1>📋 Liste des Étapes de Préparation</h1>
            <div class="subtitle">📅 Exporté le ' . date('d/m/Y à H:i:s') . ' | NutriLoop - Gestion nutritionnelle</div>
        </div>';
    
    if (!empty($search)) {
        $html .= '<div style="margin-bottom: 15px; padding: 8px; background: #e3f2fd; border-radius: 8px; text-align: center;">
            🔍 Résultats pour : <strong>' . htmlspecialchars($search) . '</strong> (' . count($preperations) . ' étape(s) trouvée(s))
        </div>';
    }
    
    // Statistiques générales
    $html .= '<div class="stats-pdf">
        <div class="stat-item">📊 <strong>' . count($preperations) . '</strong> étapes</div>
        <div class="stat-item">🍽️ <strong>' . $nbRecettes . '</strong> recettes</div>
        <div class="stat-item">⏱️ <strong>' . $totalDuree . '</strong> min total</div>
        <div class="stat-item">📈 <strong>' . $dureeMoyenne . '</strong> min (moyenne)</div>
        <div class="stat-item">👁️ <strong>' . $vuesTotales . '</strong> vues totales</div>
        <div class="stat-item">❤️ <strong>' . $likesTotaux . '</strong> likes totaux</div>
    </div>';
    
    // Statistiques par outil, action et température
    $html .= '<div class="charts-stats">
        <div class="chart-stat"><strong>🔧 Outils:</strong><br>';
    foreach ($statsOutils as $outil => $count) {
        if ($count > 0 && $outil != 'AUTRE') {
            $html .= '<span style="background:#fff3e0; color:#e65100;">' . $outil . ': ' . $count . '</span> ';
        }
    }
    $html .= '</div><div class="chart-stat"><strong>✂️ Actions:</strong><br>';
    foreach ($statsActions as $action => $count) {
        if ($count > 0 && $action != 'AUTRE') {
            $html .= '<span style="background:#e3f2fd; color:#1565c0;">' . $action . ': ' . $count . '</span> ';
        }
    }
    $html .= '</div><div class="chart-stat"><strong>🌡️ Températures:</strong><br>';
    foreach ($statsTemperature as $temp => $count) {
        if ($count > 0) {
            $html .= '<span>' . $temp . ': ' . $count . '</span> ';
        }
    }
    $html .= '</div></div>';
    
    // Tableau des étapes
    $html .= '<table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Recette</th>
                <th>Ordre</th>
                <th>Instruction</th>
                <th>Durée</th>
                <th>Température</th>
                <th>Action</th>
                <th>Outil</th>
                <th>Quantité</th>
                <th>Astuce</th>
                <th>Vues/Likes</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($preperations as $p) {
        $etapeId = $p->getIdEtape();
        $instruction = strlen($p->getInstruction()) > 100 ? substr($p->getInstruction(), 0, 100) . '...' : $p->getInstruction();
        $astuce = $p->getAstuce() ? (strlen($p->getAstuce()) > 60 ? substr($p->getAstuce(), 0, 60) . '...' : $p->getAstuce()) : '—';
        $vues = isset($_SESSION['vue_preparations'][$etapeId]) ? $_SESSION['vue_preparations'][$etapeId] : rand(50, 2000);
        $likes = isset($_SESSION['compteur_likes_prep'][$etapeId]) ? $_SESSION['compteur_likes_prep'][$etapeId] : rand(5, 200);
        
        $html .= '<tr>
            <td>#' . $etapeId . '</td>
            <td>' . htmlspecialchars($p->getRecetteNom() ?: 'N/A') . '</td>
            <td style="text-align:center">Étape ' . $p->getOrdre() . '</td>
            <td class="instruction-cell">' . htmlspecialchars($instruction) . '</td>
            <td style="text-align:center">⏱️ ' . $p->getDuree() . ' min</td>
            <td style="text-align:center">' . ($p->getTemperature() ? '🔥 ' . $p->getTemperature() . '°C' : '—') . '</td>
            <td style="text-align:center"><span class="badge-action action-' . $p->getTypeAction() . '">' . ($p->getTypeAction() ?: '—') . '</span></td>
            <td style="text-align:center"><span class="badge-outil outil-' . $p->getOutilUtilise() . '">' . ($p->getOutilUtilise() ?: '—') . '</span></td>
            <td>' . ($p->getQuantiteIngredient() ?: '—') . '</td>
            <td class="instruction-cell">' . ($astuce != '—' ? '💡 ' . htmlspecialchars($astuce) : '—') . '</td>
            <td style="text-align:center">👁️ ' . $vues . ' | ❤️ ' . $likes . '</td>
        </tr>';
    }
    
    $html .= '</tbody>
    </table>
        <div class="footer-pdf">
            <p>🍽️ NutriLoop - Application de gestion nutritionnelle</p>
            <p>📋 Rapport généré le ' . date('d/m/Y') . ' | Toutes les étapes sont présentées à titre informatif</p>
            <p>🔧 Outils: ' . implode(', ', array_keys(array_filter($statsOutils))) . ' | ✂️ Actions: ' . implode(', ', array_keys(array_filter($statsActions))) . '</p>
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
    $dompdf->stream("preparations_" . date('Ymd_His') . ".pdf", array("Attachment" => true));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étapes - Préparation - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ========== VARIABLES MODE CLAIR ========== */
        :root {
            --bg-primary: linear-gradient(135deg, #f5f7fa 0%, #e8edf2 100%);
            --bg-secondary: white;
            --text-primary: #1a1a2e;
            --text-secondary: #666;
            --border-color: #e0e0e0;
            --card-bg: white;
            --header-bg: white;
            --table-header-bg: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            --table-header-text: white;
            --table-row-hover: #f8f9ff;
            --shadow-color: rgba(0,0,0,0.08);
            --badge-light-bg: #f8f9fa;
            --translated-bg: #f0f0f0;
        }

        /* ========== MODE SOMBRE ========== */
        body.dark-mode {
            --bg-primary: linear-gradient(135deg, #121212 0%, #1a1a2e 100%);
            --bg-secondary: #1e1e2e;
            --text-primary: #ffffff;
            --text-secondary: #aaa;
            --border-color: #333;
            --card-bg: #1e1e2e;
            --header-bg: #1a1a2a;
            --table-header-bg: linear-gradient(135deg, #0d0d1a 0%, #1a1a2e 100%);
            --table-header-text: #fff;
            --table-row-hover: #2a2a3e;
            --shadow-color: rgba(0,0,0,0.3);
            --badge-light-bg: #2a2a3e;
            --translated-bg: #2a2a3e;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            min-height: 100vh;
            padding: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ========== MODE TOGGLE BUTTON ========== */
        .theme-toggle {
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 40px;
            width: 60px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5px;
            cursor: pointer;
            position: relative;
            transition: 0.3s;
        }

        .theme-toggle i {
            font-size: 14px;
            z-index: 1;
            color: var(--text-primary);
        }

        .theme-toggle .fa-sun { color: #f39c12; }
        .theme-toggle .fa-moon { color: #3498db; }

        .theme-toggle::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #2196f3, #1976d2);
            border-radius: 50%;
            left: 3px;
            transition: 0.3s;
        }

        body.dark-mode .theme-toggle::after {
            left: 31px;
        }

        /* Header */
        .header {
            background: var(--header-bg);
            border-radius: 20px;
            padding: 20px 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 5px 20px var(--shadow-color);
            animation: slideDown 0.5s ease;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header h1 {
            font-size: 1.8rem;
            color: var(--text-primary);
        }

        .header h1 i {
            color: #2196f3;
            margin-right: 10px;
            background: rgba(33, 150, 243, 0.1);
            padding: 10px;
            border-radius: 15px;
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
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
        }

        /* Sort Controls */
        .sort-controls {
            background: var(--bg-secondary);
            padding: 15px 25px;
            border-radius: 16px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 10px var(--shadow-color);
            animation: fadeIn 0.5s ease 0.1s both;
        }

        .sort-controls label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .sort-select {
            padding: 10px 18px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .sort-select:focus {
            border-color: #2196f3;
            outline: none;
        }

        .sort-btn {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .sort-btn:hover {
            transform: translateY(-2px);
        }

        /* Stats Bar */
        .stats-bar {
            background: var(--bg-secondary);
            padding: 20px 30px;
            border-radius: 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 5px 20px var(--shadow-color);
            animation: fadeIn 0.5s ease 0.2s both;
        }

        .stats {
            display: flex;
            gap: 35px;
            flex-wrap: wrap;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--badge-light-bg);
            padding: 10px 20px;
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        .stat i {
            font-size: 1.5rem;
            color: #2196f3;
        }

        .stat span {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .stat strong {
            font-size: 1.3rem;
            color: var(--text-primary);
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            padding: 12px 20px;
            border: 2px solid var(--border-color);
            border-radius: 14px;
            width: 280px;
            font-size: 14px;
            transition: 0.3s;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .search-box input:focus {
            border-color: #2196f3;
            outline: none;
        }

        .search-box button {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            border: none;
            padding: 12px 20px;
            border-radius: 14px;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }

        /* Table */
        .table-container {
            background: var(--bg-secondary);
            border-radius: 24px;
            overflow: auto;
            box-shadow: 0 10px 30px var(--shadow-color);
            animation: fadeIn 0.5s ease 0.3s both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1400px;
        }

        th {
            background: var(--table-header-bg);
            color: var(--table-header-text);
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        th i {
            margin-right: 10px;
        }

        th.sortable {
            cursor: pointer;
            user-select: none;
            transition: 0.3s;
        }

        th.sortable:hover {
            background: linear-gradient(135deg, #2a2a4e 0%, #1e2a4e 100%);
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-primary);
        }

        tr {
            transition: all 0.3s ease;
        }

        tr:hover {
            background: var(--table-row-hover);
            transform: scale(1.01);
        }

        /* Badges */
        .badge-ordre {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e3f2fd;
            color: #1565c0;
        }

        .badge-action {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .action-COUPER { background: #e3f2fd; color: #1565c0; }
        .action-MELANGER { background: #f3e5f5; color: #7b1fa2; }
        .action-CUISSON { background: #ffebee; color: #c62828; }

        .badge-outil {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .outil-FOUR { background: #fff3e0; color: #e65100; }
        .outil-MIXEUR { background: #e8f5e9; color: #2e7d32; }
        .outil-CUILLERE { background: #e0f7fa; color: #00838f; }

        /* Statistiques vues/likes */
        .stats-icons {
            display: flex;
            gap: 12px;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .stats-icons i {
            margin-right: 4px;
        }

        .stats-icons .fa-eye { color: #2196F3; }
        .stats-icons .fa-heart { color: #e53935; }

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

        .view-btn { background: #4CAF50; color: white; }
        .view-btn:hover { background: #45a049; }
        .edit-btn { background: #2196F3; color: white; }
        .edit-btn:hover { background: #1976D2; }
        .delete-btn { background: #dc3545; color: white; border: none; cursor: pointer; }
        .delete-btn:hover { background: #c82333; }

        /* Recette link */
        .recette-link {
            color: #2196f3;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
        }

        .recette-link:hover {
            color: #1976d2;
            transform: translateX(3px);
        }

        /* Traduction */
        .translate-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
            margin-top: 8px;
        }

        .translate-btn:hover {
            transform: scale(1.05);
            filter: brightness(1.05);
        }

        .translate-container {
            margin-top: 8px;
        }

        .translated-text {
            font-size: 0.75rem;
            color: var(--text-primary);
            background: var(--translated-bg);
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 8px;
            display: none;
            border-left: 3px solid #667eea;
            line-height: 1.4;
        }

        .translated-text.show {
            display: block;
            animation: fadeSlide 0.3s ease;
        }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .lang-select {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            margin-left: 8px;
            cursor: pointer;
        }

        .instruction-cell {
            max-width: 350px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.5;
            font-size: 13px;
        }

        .text-center {
            text-align: center;
        }

        /* Modal Statistiques */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 28px;
            padding: 35px;
            width: 1000px;
            max-width: 95%;
            position: relative;
            animation: modalSlide 0.4s ease;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            color: var(--text-primary);
        }

        @keyframes modalSlide {
            from { opacity: 0; transform: scale(0.9) translateY(-30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: var(--text-primary);
            font-size: 1.5rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 32px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: 0.3s;
        }

        .close-modal:hover {
            color: var(--text-primary);
            transform: rotate(90deg);
        }

        .stats-charts-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .chart-box {
            flex: 1;
            min-width: 250px;
            text-align: center;
            padding: 20px;
            background: var(--badge-light-bg);
            border-radius: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        .chart-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px var(--shadow-color);
        }

        .chart-box h3 {
            color: #2196f3;
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .chart-box canvas {
            max-height: 200px;
            max-width: 100%;
        }

        .stats-details {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid var(--border-color);
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .stat-detail {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            background: var(--badge-light-bg);
            padding: 12px 25px;
            border-radius: 16px;
            transition: 0.3s;
        }

        .stat-detail:hover {
            transform: translateY(-3px);
            background: rgba(33, 150, 243, 0.1);
        }

        .search-active {
            background: #e3f2fd;
            padding: 12px 25px;
            border-radius: 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            animation: fadeIn 0.3s ease;
            color: #333;
        }

        .clear-search {
            background: #f44336;
            color: white;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 13px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .clear-search:hover {
            background: #d32f2f;
            transform: scale(1.05);
        }

        .footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            width: 45px;
            height: 45px;
            border: 2px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            border-radius: 12px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .page-btn:hover {
            border-color: #2196f3;
            transform: translateY(-2px);
        }

        .page-btn.active {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            color: white;
            border-color: #2196f3;
        }

        .btn-footer {
            padding: 12px 28px;
            border-radius: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
        }

        .btn-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-stats:hover {
            transform: translateY(-2px);
        }

        .btn-pdf {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-pdf:hover {
            transform: translateY(-2px);
        }

        .empty-message {
            text-align: center;
            padding: 60px;
            color: var(--text-secondary);
        }

        .tfoot {
            background: var(--badge-light-bg);
            font-weight: bold;
        }

        .tfoot td {
            padding: 18px 15px;
            border-top: 2px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>
                    <i class="fas fa-list-ol"></i>
                    Gestion des Étapes de Préparation
                </h1>
                <!-- Mode Toggle Button -->
                <div class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-sun"></i>
                    <i class="fas fa-moon"></i>
                </div>
            </div>
            <div class="btn-group">
                <a href="addPreperation.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter une étape
                </a>
                <a href="recetteList.php" class="btn btn-secondary">
                    <i class="fas fa-utensils"></i> Voir les recettes
                </a>
            </div>
        </div>

        <!-- Search Active Display -->
        <div id="searchActiveDiv" style="display: none;" class="search-active">
            <div class="search-info">
                <i class="fas fa-search" style="color: #2196f3;"></i>
                <span>Résultats pour : <strong id="searchTerm"></strong></span>
                <span style="color: #666;" id="resultCount"></span>
            </div>
            <button class="clear-search" onclick="clearSearch()">
                <i class="fas fa-times"></i> Effacer la recherche
            </button>
        </div>

        <!-- Sort Controls -->
        <div class="sort-controls">
            <label><i class="fas fa-sort-amount-down-alt"></i> Trier par :</label>
            <select id="sortField" class="sort-select">
                <option value="id">ID</option>
                <option value="recette">Recette</option>
                <option value="ordre">Ordre</option>
                <option value="duree">Durée</option>
            </select>
            <select id="sortOrder" class="sort-select">
                <option value="asc">Croissant ↑</option>
                <option value="desc">Décroissant ↓</option>
            </select>
            <button class="sort-btn" onclick="applySort()">
                <i class="fas fa-sort"></i> Appliquer
            </button>
        </div>

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-tasks"></i>
                    <span><strong id="totalEtapes"><?= $totalEtapes ?></strong> étapes</span>
                </div>
                <div class="stat">
                    <i class="fas fa-utensils"></i>
                    <span><strong><?= $nbRecettes ?></strong> recettes</span>
                </div>
                <div class="stat">
                    <i class="fas fa-clock"></i>
                    <span><strong><?= $dureeMoyenne ?></strong> min (moyenne)</span>
                </div>
                <div class="stat">
                    <i class="fas fa-eye"></i>
                    <span><strong><?= array_sum($_SESSION['vue_preparations']) ?></strong> vues totales</span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Rechercher par recette ou instruction..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table id="preparationsTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)"><i class="fas fa-hashtag"></i> ID <i class="fas fa-sort sort-icon" id="sort-icon-0"></i></th>
                        <th class="sortable" onclick="sortTable(1)"><i class="fas fa-utensils"></i> Recette <i class="fas fa-sort sort-icon" id="sort-icon-1"></i></th>
                        <th class="sortable" onclick="sortTable(2)"><i class="fas fa-sort-numeric-down"></i> Ordre <i class="fas fa-sort sort-icon" id="sort-icon-2"></i></th>
                        <th><i class="fas fa-align-left"></i> Instruction / Astuce</th>
                        <th class="sortable" onclick="sortTable(4)"><i class="fas fa-hourglass-half"></i> Durée <i class="fas fa-sort sort-icon" id="sort-icon-4"></i></th>
                        <th><i class="fas fa-thermometer-half"></i> Température</th>
                        <th><i class="fas fa-cut"></i> Action</th>
                        <th><i class="fas fa-tools"></i> Outil</th>
                        <th><i class="fas fa-chart-simple"></i> Stats</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if ($totalEtapes > 0): ?>
                        <?php foreach ($preperations as $item): 
                            $etapeId = $item->getIdEtape();
                            $vues = isset($_SESSION['vue_preparations'][$etapeId]) ? $_SESSION['vue_preparations'][$etapeId] : rand(50, 2000);
                            $likes = isset($_SESSION['compteur_likes_prep'][$etapeId]) ? $_SESSION['compteur_likes_prep'][$etapeId] : rand(5, 200);
                        ?>
                            <tr data-id="<?= $etapeId ?>" data-recette="<?= htmlspecialchars(strtolower($item->getRecetteNom())) ?>" data-ordre="<?= $item->getOrdre() ?>" data-duree="<?= $item->getDuree() ?>">
                                <td><strong style="color: #2196f3;">#<?= $etapeId ?></strong></td>
                                <td>
                                    <a href="viewRecette.php?id=<?= $item->getIdRecette() ?>" class="recette-link">
                                        <i class="fas fa-book"></i>
                                        <?= htmlspecialchars($item->getRecetteNom() ?: 'N/A') ?>
                                    </a>
                                 </div>
                                <td>
                                    <span class="badge-ordre">
                                        <i class="fas fa-check-circle"></i> Étape <?= $item->getOrdre() ?>
                                    </span>
                                 </div>
                                <td class="instruction-cell">
                                    <div class="original-text">
                                        <i class="fas fa-quote-left" style="color: #2196f3; opacity: 0.5; margin-right: 5px;"></i>
                                        <?= htmlspecialchars(substr($item->getInstruction(), 0, 100)) ?>
                                        <?php if(strlen($item->getInstruction()) > 100): ?>...<?php endif; ?>
                                    </div>
                                    <?php if($item->getAstuce()): ?>
                                        <div style="margin-top: 8px;">
                                            <i class="fas fa-lightbulb" style="color: #ffc107;"></i>
                                            <span style="font-size: 0.75rem; color: #ffc107;"> Astuce: </span>
                                            <?= htmlspecialchars(substr($item->getAstuce(), 0, 50)) ?>
                                            <?php if(strlen($item->getAstuce()) > 50): ?>...<?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <!-- TRADUCTION -->
                                    <div class="translate-container">
                                        <button class="translate-btn" onclick="translateDescription(this, <?= $etapeId ?>, '<?= addslashes($item->getInstruction()) ?>')">
                                            <i class="fas fa-language"></i> Traduire
                                        </button>
                                        <select class="lang-select" id="lang-<?= $etapeId ?>" onchange="changeLanguage(this, <?= $etapeId ?>, '<?= addslashes($item->getInstruction()) ?>')">
                                            <option value="fr">🇫🇷 Français</option>
                                            <option value="en">🇬🇧 English</option>
                                            <option value="ar">🇸🇦 العربية</option>
                                            <option value="es">🇪🇸 Español</option>
                                            <option value="it">🇮🇹 Italiano</option>
                                            <option value="de">🇩🇪 Deutsch</option>
                                            <option value="pt">🇵🇹 Português</option>
                                            <option value="ru">🇷🇺 Русский</option>
                                            <option value="zh">🇨🇳 中文</option>
                                        </select>
                                        <div class="translated-text" id="translated-<?= $etapeId ?>"></div>
                                    </div>
                                 </div>
                                <td class="text-center">
                                    <i class="fas fa-hourglass-half" style="color: #2196f3;"></i> <?= $item->getDuree() ?: 0 ?> min
                                 </div>
                                <td class="text-center">
                                    <?php if($item->getTemperature()): ?>
                                        <i class="fas fa-fire" style="color: #ff9800;"></i> <?= $item->getTemperature() ?>°C
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">—</span>
                                    <?php endif; ?>
                                 </div>
                                <td class="text-center">
                                    <?php
                                    $typeAction = $item->getTypeAction();
                                    $actionIcon = '';
                                    $actionText = '';
                                    switch($typeAction) {
                                        case 'COUPER': $actionIcon = 'fa-cut'; $actionText = 'Couper'; break;
                                        case 'MELANGER': $actionIcon = 'fa-mix'; $actionText = 'Mélanger'; break;
                                        case 'CUISSON': $actionIcon = 'fa-fire'; $actionText = 'Cuisson'; break;
                                        default: $actionIcon = 'fa-question'; $actionText = $typeAction ?: '—';
                                    }
                                    ?>
                                    <?php if($typeAction): ?>
                                        <span class="badge-action action-<?= $typeAction ?>">
                                            <i class="fas <?= $actionIcon ?>"></i> <?= $actionText ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">—</span>
                                    <?php endif; ?>
                                 </div>
                                <td class="text-center">
                                    <?php
                                    $outil = $item->getOutilUtilise();
                                    $outilIcon = '';
                                    $outilText = '';
                                    switch($outil) {
                                        case 'FOUR': $outilIcon = 'fa-oven'; $outilText = 'Four'; break;
                                        case 'MIXEUR': $outilIcon = 'fa-blender'; $outilText = 'Mixeur'; break;
                                        case 'CUILLERE': $outilIcon = 'fa-utensil-spoon'; $outilText = 'Cuillère'; break;
                                        case 'RAPE': $outilIcon = 'fa-cheese'; $outilText = 'Râpe'; break;
                                        default: $outilIcon = 'fa-tools'; $outilText = $outil ?: '—';
                                    }
                                    ?>
                                    <?php if($outil): ?>
                                        <span class="badge-outil outil-<?= $outil ?>">
                                            <i class="fas <?= $outilIcon ?>"></i> <?= $outilText ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">—</span>
                                    <?php endif; ?>
                                 </div>
                                <td class="stats-icons">
                                    <div><i class="fas fa-eye"></i> <?= $vues ?> vues</div>
                                    <div><i class="fas fa-heart"></i> <?= $likes ?> likes</div>
                                 </div>
                                <td class="actions">
                                    <a href="viewPreperation.php?id=<?= $etapeId ?>" class="action-btn view-btn">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                    <a href="editPreperation.php?id=<?= $etapeId ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <button onclick="confirmDelete(<?= $etapeId ?>)" class="action-btn delete-btn">
                                        <i class="fas fa-trash"></i> Suppr
                                    </button>
                                 </div>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="empty-message">
                                <i class="fas fa-empty-folder" style="font-size: 4rem;"></i>
                                <p style="margin-top: 15px;">Aucune étape de préparation trouvée</p>
                                <a href="addPreperation.php" class="btn btn-primary" style="margin-top: 15px; display: inline-block;">
                                    <i class="fas fa-plus-circle"></i> Ajouter une étape
                                </a>
                             </div>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if ($totalEtapes > 0): ?>
                    <tfoot class="tfoot">
                        <tr>
                            <td colspan="4"><strong><i class="fas fa-chart-simple"></i> Totaux :</strong></div>
                            <td><strong id="totalDureeFoot"><?= $totalDuree ?> min</strong> (total)</div>
                            <td colspan="4"></div>
                            <td class="stats-icons">⭐ Total vues: <?= array_sum($_SESSION['vue_preparations']) ?></div>
                            <td></div>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <!-- Footer Buttons -->
        <div class="footer">
            <div class="pagination">
                <button class="page-btn" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active" id="page1">1</button>
                <button class="page-btn" id="page2" style="display:none;">2</button>
                <button class="page-btn" id="page3" style="display:none;">3</button>
                <button class="page-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div style="display: flex; gap: 15px;">
                <button class="btn-footer btn-stats" onclick="openStatsModal()">
                    <i class="fas fa-chart-pie"></i> Statistiques avancées
                </button>
                <a href="?export_pdf=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn-footer btn-pdf" style="text-decoration: none;">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Statistiques améliorées -->
    <div id="statsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chart-pie"></i> Statistiques avancées</h2>
                <button class="close-modal" onclick="closeStatsModal()">&times;</button>
            </div>
            <div class="stats-charts-container">
                <div class="chart-box">
                    <h3><i class="fas fa-tools"></i> Par outil utilisé</h3>
                    <canvas id="outilsChart"></canvas>
                </div>
                <div class="chart-box">
                    <h3><i class="fas fa-cut"></i> Par type d'action</h3>
                    <canvas id="actionsChart"></canvas>
                </div>
                <div class="chart-box">
                    <h3><i class="fas fa-temperature-high"></i> Par température</h3>
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>
            <div class="stats-details">
                <div class="stat-detail">
                    <span><i class="fas fa-tasks"></i> Total étapes</span>
                    <strong><?= $totalEtapes ?></strong>
                </div>
                <div class="stat-detail">
                    <span><i class="fas fa-clock"></i> Durée totale</span>
                    <strong><?= $totalDuree ?> min</strong>
                </div>
                <div class="stat-detail">
                    <span><i class="fas fa-chart-line"></i> Moyenne par étape</span>
                    <strong><?= $dureeMoyenne ?> min</strong>
                </div>
                <div class="stat-detail">
                    <span><i class="fas fa-eye"></i> Vues totales</span>
                    <strong><?= array_sum($_SESSION['vue_preparations']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let rowsPerPage = 10;
        let currentSortColumn = -1;
        let currentSortOrder = 'asc';
        let chartOutils = null;
        let chartActions = null;
        let chartTemperature = null;

        // ========== MODE SOMBRE / CLAIR ==========
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
        }

        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }

        // ========== TRADUCTION ==========
        async function translateText(text, targetLang) {
            try {
                const response = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=fr|${targetLang}`);
                const data = await response.json();
                return data.responseData.translatedText;
            } catch (error) {
                return text + " (Traduction non disponible)";
            }
        }

        async function translateDescription(btn, etapeId, instruction) {
            const langSelect = document.getElementById(`lang-${etapeId}`);
            const targetLang = langSelect.value;
            const translatedDiv = document.getElementById(`translated-${etapeId}`);
            
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Traduction...';
            btn.disabled = true;
            
            const translatedText = await translateText(instruction, targetLang);
            
            translatedDiv.innerHTML = `
                <i class="fas fa-language"></i> Traduction: ${translatedText}
            `;
            translatedDiv.classList.add('show');
            
            btn.innerHTML = '<i class="fas fa-language"></i> Traduire';
            btn.disabled = false;
        }

        async function changeLanguage(select, etapeId, instruction) {
            const btn = select.parentElement.querySelector('.translate-btn');
            const translatedDiv = document.getElementById(`translated-${etapeId}`);
            
            if (translatedDiv.classList.contains('show')) {
                const targetLang = select.value;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Traduction...';
                btn.disabled = true;
                const translatedText = await translateText(instruction, targetLang);
                translatedDiv.innerHTML = `<i class="fas fa-language"></i> Traduction: ${translatedText}`;
                btn.innerHTML = '<i class="fas fa-language"></i> Traduire';
                btn.disabled = false;
            }
        }

        function confirmDelete(id) {
            if (confirm('⚠️ Supprimer cette étape ?')) {
                window.location.href = 'deletePreperation.php?id=' + id + '&confirm=yes';
            }
        }

        function applySort() {
            const sortField = document.getElementById('sortField').value;
            const sortOrder = document.getElementById('sortOrder').value;
            const tbody = document.getElementById('tableBody');
            const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-message)'));
            
            rows.sort((a, b) => {
                let aValue, bValue;
                switch(sortField) {
                    case 'id':
                        aValue = parseInt(a.cells[0].textContent.replace('#', ''));
                        bValue = parseInt(b.cells[0].textContent.replace('#', ''));
                        break;
                    case 'recette':
                        aValue = a.cells[1].textContent.toLowerCase();
                        bValue = b.cells[1].textContent.toLowerCase();
                        break;
                    case 'ordre':
                        aValue = parseInt(a.cells[2].textContent.match(/\d+/));
                        bValue = parseInt(b.cells[2].textContent.match(/\d+/));
                        break;
                    case 'duree':
                        aValue = parseInt(a.cells[4].textContent);
                        bValue = parseInt(b.cells[4].textContent);
                        break;
                    default: return 0;
                }
                if (sortOrder === 'asc') return aValue > bValue ? 1 : -1;
                else return aValue < bValue ? 1 : -1;
            });
            rows.forEach(row => tbody.appendChild(row));
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                if (row.classList.contains('empty-message')) return;
                const recetteCell = row.cells[1];
                const instructionCell = row.cells[3];
                const recetteText = recetteCell ? recetteCell.textContent.toLowerCase() : '';
                const instructionText = instructionCell ? instructionCell.textContent.toLowerCase() : '';
                
                if (filter === "" || recetteText.includes(filter) || instructionText.includes(filter)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            const searchActiveDiv = document.getElementById('searchActiveDiv');
            if (filter !== "") {
                searchActiveDiv.style.display = 'flex';
                document.getElementById('searchTerm').textContent = filter;
                document.getElementById('resultCount').textContent = `(${visibleCount} étape(s) trouvée(s))`;
            } else {
                searchActiveDiv.style.display = 'none';
            }
            currentPage = 1;
            updatePagination();
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            searchTable();
        }

        function updatePagination() {
            const rows = document.querySelectorAll('#tableBody tr');
            const visibleRows = Array.from(rows).filter(row => 
                row.style.display !== 'none' && !row.classList.contains('empty-message')
            );
            const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages || 1;
            
            visibleRows.forEach((row, index) => {
                const page = Math.floor(index / rowsPerPage) + 1;
                row.style.display = page === currentPage ? '' : 'none';
            });
            
            for (let i = 1; i <= 3; i++) {
                const btn = document.getElementById('page' + i);
                if (btn) {
                    btn.classList.remove('active');
                    if (i === currentPage) btn.classList.add('active');
                    btn.style.display = i <= totalPages ? 'inline-block' : 'none';
                    if (btn.style.display === 'inline-block') {
                        btn.textContent = i;
                        btn.onclick = () => showPage(i);
                    }
                }
            }
        }

        function showPage(page) { currentPage = page; updatePagination(); }
        function previousPage() { if (currentPage > 1) { currentPage--; updatePagination(); } }
        function nextPage() { const rows = document.querySelectorAll('#tableBody tr'); const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none' && !row.classList.contains('empty-message')); const totalPages = Math.ceil(visibleRows.length / rowsPerPage); if (currentPage < totalPages) { currentPage++; updatePagination(); } }

        function sortTable(columnIndex) {
            const tbody = document.getElementById('tableBody');
            const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-message)'));
            const visibleRows = rows.filter(row => row.style.display !== 'none');
            if (visibleRows.length === 0) return;
            
            if (currentSortColumn === columnIndex) currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            else { currentSortColumn = columnIndex; currentSortOrder = 'asc'; }
            
            const icons = document.querySelectorAll('.sort-icon');
            icons.forEach(icon => icon.className = 'fas fa-sort sort-icon');
            const currentIcon = document.getElementById('sort-icon-' + columnIndex);
            if (currentIcon) currentIcon.className = currentSortOrder === 'asc' ? 'fas fa-sort-up sort-icon' : 'fas fa-sort-down sort-icon';
            
            visibleRows.sort((a, b) => {
                let aValue, bValue;
                if (columnIndex === 0) { aValue = parseInt(a.cells[0].textContent.replace('#', '')); bValue = parseInt(b.cells[0].textContent.replace('#', '')); }
                else if (columnIndex === 1) { aValue = a.cells[1].textContent.toLowerCase(); bValue = b.cells[1].textContent.toLowerCase(); }
                else if (columnIndex === 2) { aValue = parseInt(a.cells[2].textContent.match(/\d+/)); bValue = parseInt(b.cells[2].textContent.match(/\d+/)); }
                else if (columnIndex === 4) { aValue = parseInt(a.cells[4].textContent); bValue = parseInt(b.cells[4].textContent); }
                else return 0;
                if (currentSortOrder === 'asc') return aValue > bValue ? 1 : -1;
                else return aValue < bValue ? 1 : -1;
            });
            
            const hiddenRows = rows.filter(row => row.style.display === 'none');
            while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
            visibleRows.forEach(row => tbody.appendChild(row));
            hiddenRows.forEach(row => tbody.appendChild(row));
        }

        function openStatsModal() {
            const modal = document.getElementById('statsModal');
            modal.style.display = 'flex';
            
            const outilsData = [<?php echo implode(',', array_values($statsOutils)); ?>];
            const actionsData = [<?php echo implode(',', array_values($statsActions)); ?>];
            const tempData = [<?php echo implode(',', array_values($statsTemperature)); ?>];
            
            const ctxOutils = document.getElementById('outilsChart').getContext('2d');
            const ctxActions = document.getElementById('actionsChart').getContext('2d');
            const ctxTemperature = document.getElementById('temperatureChart').getContext('2d');
            
            if (chartOutils) chartOutils.destroy();
            if (chartActions) chartActions.destroy();
            if (chartTemperature) chartTemperature.destroy();
            
            chartOutils = new Chart(ctxOutils, { type: 'doughnut', data: { labels: <?php echo json_encode(array_keys($statsOutils)); ?>, datasets: [{ data: outilsData, backgroundColor: ['#e65100', '#2e7d32', '#00838f', '#1565c0', '#ff9800'], borderWidth: 0 }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
            chartActions = new Chart(ctxActions, { type: 'doughnut', data: { labels: <?php echo json_encode(array_keys($statsActions)); ?>, datasets: [{ data: actionsData, backgroundColor: ['#1565c0', '#7b1fa2', '#c62828', '#ff9800'], borderWidth: 0 }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
            chartTemperature = new Chart(ctxTemperature, { type: 'pie', data: { labels: <?php echo json_encode(array_keys($statsTemperature)); ?>, datasets: [{ data: tempData, backgroundColor: ['#9e9e9e', '#4caf50', '#ff9800', '#f44336'], borderWidth: 0 }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
        }

        function closeStatsModal() {
            document.getElementById('statsModal').style.display = 'none';
            if (chartOutils) { chartOutils.destroy(); chartOutils = null; }
            if (chartActions) { chartActions.destroy(); chartActions = null; }
            if (chartTemperature) { chartTemperature.destroy(); chartTemperature = null; }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('#tableBody tr');
            if (rows.length > 0 && rows[0].cells.length > 1) {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                for (let i = 1; i <= Math.min(totalPages, 3); i++) {
                    const btn = document.getElementById('page' + i);
                    if (btn) { btn.style.display = 'inline-block'; btn.textContent = i; btn.onclick = () => showPage(i); }
                }
                showPage(1);
            }
        });

        window.onclick = function(event) { if (event.target === document.getElementById('statsModal')) closeStatsModal(); }
    </script>
</body>
</html>