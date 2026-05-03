<?php
<<<<<<< HEAD
session_start();
include '../../controleurs/ProduitController.php';
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();

// GET parameters for search, sort, and filters
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'nom';
$sortOrder = $_GET['sort_order'] ?? 'ASC';
$idCategorie = $_GET['id_categorie'] ?? '';
$origine = $_GET['origine'] ?? '';

// Fetch products with advanced search
$produits = $produitController->advancedSearch($search, $sortBy, $sortOrder, $idCategorie, $origine);

// Get all categories for filter
$allCategories = $produitController->getCategories();

// Get stats (Restore this to fix PHP warnings)
$stats = $produitController->getStats($produits);

// Prepare Real Data for dual charts
$ecoScoreData = [];
$labels = [];
foreach (array_slice($produits, 0, 8) as $p) {
    $labels[] = $p->getNom();
    $ecoScoreData[] = $p->getEcoScore();
}

$priceDist = ['Prix Bas' => 0, 'Prix Moyen' => 0, 'Prix Haut' => 0];
foreach ($produits as $p) {
    if ($p->getPrix() < 10) $priceDist['Prix Bas']++;
    elseif ($p->getPrix() < 50) $priceDist['Prix Moyen']++;
    else $priceDist['Prix Haut']++;
}

// Données pour "Produits les plus commandés" (Courbe)
$db = Config::getConnexion();
$sqlOrders = "SELECT p.nom, SUM(ci.quantite) as total_qty 
              FROM commande_item ci 
              JOIN produit p ON ci.id_produit = p.id_produit 
              GROUP BY ci.id_produit 
              ORDER BY total_qty DESC 
              LIMIT 10";
$orderStats = $db->query($sqlOrders)->fetchAll(PDO::FETCH_ASSOC);
$orderLabels = array_column($orderStats, 'nom');
$orderQtys = array_column($orderStats, 'total_qty');
=======
include '../../controleurs/ProduitController.php';
require_once __DIR__ . '/../../models/produit.php';
<<<<<<< HEAD
session_start();

$produitController = new ProduitController();
$produits = $produitController->listProduits();

// Stats
$totalProduits = count($produits);
$locauxCount = count(array_filter($produits, fn($p) => $p->getOrigine() === 'local'));
$categoriesCount = count(array_unique(array_map(fn($p) => $p->getIdCategorie(), $produits)));
=======

$produitController = new ProduitController();
$produits = $produitController->listProduits();
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Gestion des Produits - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
=======
<<<<<<< HEAD
    <title>Gestion des Produits - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
    <style>
        :root {
            --primary-blue: #003366;
            --success-green: #4CAF50;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
<<<<<<< HEAD
            --accent-purple: #9C27B0;
            --accent-orange: #FF9800;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header h1 { font-size: 1.8rem; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }
        .header h1 i { color: var(--success-green); }

        .btn-group { display: flex; gap: 12px; }
        .btn { padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; }
        .btn-primary { background: var(--success-green); color: white; }
        .btn-secondary { background: var(--primary-blue); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

        /* Notification */
        .notification-success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid var(--success-green); display: flex; align-items: center; gap: 12px; }

        /* Stats Modal */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); align-items: center; justify-content: center; opacity: 0; transition: 0.3s; }
        .modal.show { display: flex; opacity: 1; }
        .modal-content { background: #f8f9fa; width: 90%; max-width: 1100px; max-height: 90vh; border-radius: 24px; padding: 40px; position: relative; overflow-y: auto; transform: translateY(20px); transition: 0.3s; }
        .modal.show .modal-content { transform: translateY(0); }
        .close-modal { position: absolute; right: 25px; top: 20px; font-size: 2rem; cursor: pointer; color: #7f8c8d; }

        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; border: 1px solid rgba(0,0,0,0.03); }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; color: white; }
        .stat-icon.green { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
        .stat-icon.blue { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.orange { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .stat-icon.teal { background: linear-gradient(135deg, #009688, #00796B); }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); }
        .stat-label { font-size: 0.8rem; color: var(--text-gray); text-transform: uppercase; }

        .charts-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .chart-box { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        /* Filters Form */
        .filters-form { background: white; padding: 25px; border-radius: 16px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px; }
        .filter-group label { font-size: 0.85rem; font-weight: 700; color: var(--text-dark); text-transform: uppercase; }
        .filter-group input, .filter-group select { padding: 12px; border: 2px solid #eee; border-radius: 10px; font-size: 0.9rem; transition: 0.3s; background: #fdfdfd; }
        .filter-group input:focus, .filter-group select:focus { border-color: var(--success-green); outline: none; background: white; }
        .btn-filter { background: var(--success-green); color: white; padding: 12px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px; }
        .btn-reset { background: #f1f3f5; color: #495057; padding: 12px 25px; border-radius: 10px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px; }

        /* Table */
        .table-container { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; min-width: 1100px; }
        th { background: #1a1a2e; color: white; padding: 18px 15px; text-align: left; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }
        .product-img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; border: 2px solid #eee; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-local { background: #e8f5e9; color: #2e7d32; }
        .badge-importe { background: #fff3e0; color: #e65100; }
        .highlighted-row { background-color: #fff9c4 !important; border-left: 4px solid var(--accent-orange); }

        .actions { display: flex; gap: 8px; }
        .action-btn { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: 0.2s; }
        .btn-edit { background: #2196F3; }
        .btn-delete { background: #f44336; }
        .action-btn:hover { transform: scale(1.1); filter: brightness(1.1); }

        /* Footer */
        .footer { margin-top: 25px; display: flex; justify-content: space-between; align-items: center; }
        .pagination { display: flex; gap: 8px; }
        .page-btn { width: 40px; height: 40px; border: 1px solid #ddd; background: white; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
        .page-btn.active { background: var(--success-green); color: white; border-color: var(--success-green); }
        .page-btn:hover:not(.active) { background: #f0f0f0; }

        @media (max-width: 992px) {
            .charts-section { grid-template-columns: 1fr; }
=======
            --border-color: #eee;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            padding: 20px;
        }

        .container-list {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Success Message */
        .success-bar {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px 20px;
            border-radius: 12px;
            border-left: 5px solid var(--success-green);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.8em;
            color: var(--text-dark);
            font-weight: 700;
        }

        .header-title i {
            color: var(--success-green);
            font-size: 1.2em;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95em;
        }

        .btn-success { background: var(--success-green); color: white; }
        .btn-primary { background: var(--primary-blue); color: white; }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            filter: brightness(1.1);
        }

        /* Stats Bar */
        .stats-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .stats-items {
            display: flex;
            gap: 30px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            color: var(--text-gray);
        }

        .stat-item i { color: var(--success-green); }
        .stat-item strong { color: var(--text-dark); font-size: 1.1em; }

        .search-container {
            display: flex;
            gap: 0;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .search-container input {
            padding: 8px 15px;
            border: none;
            outline: none;
            width: 250px;
        }

        .btn-search {
            background: var(--success-green);
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
        }

        /* Table Container */
        .table-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #1a1c23;
            color: white;
        }

        th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th i { margin-right: 8px; color: #aaa; }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #f5f5f5;
            color: var(--text-dark);
            font-size: 0.95em;
        }

        tr:last-child td { border-bottom: none; }

        .product-img {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .badge-local { background: #e8f5e9; color: #2e7d32; }
        .badge-importe { background: #fff3e0; color: #e65100; }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: 0.2s;
            color: white;
        }

        .btn-edit { background: #4a90e2; }
        .btn-delete { background: #f44336; }

        /* Footer */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pagination {
            display: flex;
            gap: 10px;
        }

        .page-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            color: var(--text-gray);
            cursor: pointer;
            transition: 0.3s;
        }

        .page-btn.active {
            background: var(--success-green);
            color: white;
            border-color: var(--success-green);
        }

        .page-btn:hover:not(.active) {
            background: #f0f0f0;
        }

        .btn-export {
            background: var(--primary-blue);
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
        }

        .empty-state {
            padding: 80px;
            text-align: center;
            color: #999;
        }

        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            color: #eee;
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        }
    </style>
</head>
<body>
<<<<<<< HEAD
    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification-success">
=======
    <div class="container-list">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-bar">
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

<<<<<<< HEAD
        <div class="header">
            <h1><i class="fas fa-carrot"></i> Gestion des Produits</h1>
            <div class="btn-group">
                <button id="openStatsBtn" class="btn btn-secondary"><i class="fas fa-chart-line"></i> Statistiques</button>
                <a href="addProduit.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Ajouter</a>
                <a href="categorieList.php" class="btn btn-secondary"><i class="fas fa-tags"></i> Catégories</a>
            </div>
        </div>

        <!-- Stats Modal -->
        <div id="statsModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div style="margin-bottom: 25px;">
                    <h2 style="color: var(--text-dark); display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-analytics" style="color: var(--success-green);"></i> Analyse des Produits
                    </h2>
                </div>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-box"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['total'] ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-truck"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['avgDistance'] ?> km</div>
                            <div class="stat-label">Dist. Moyenne</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-leaf"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['origineDistribution']['local'] ?></div>
                            <div class="stat-label">Produits Locaux</div>
                        </div>
                    </div>
                </div>

                <div class="charts-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1rem; color: var(--primary-blue);"><i class="fas fa-leaf"></i> Tendance Eco-Score</h3>
                        <div style="height: 250px;">
                            <canvas id="ecoTrendChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1rem; color: var(--primary-blue);"><i class="fas fa-chart-bar"></i> Segments de Prix</h3>
                        <div style="height: 250px;">
                            <canvas id="priceBarChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="chart-box">
                    <h3 style="margin-bottom: 15px; font-size: 1rem; color: var(--primary-blue);"><i class="fas fa-shopping-cart"></i> Top Produits les plus commandés</h3>
                    <div style="height: 300px;">
                        <canvas id="orderVolumeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <form class="filters-form" method="GET">
            <div class="filter-group">
                <label>Recherche</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nom, origine...">
            </div>
            <div class="filter-group">
                <label>Catégorie</label>
                <select name="id_categorie">
                    <option value="">Toutes</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>" <?= $idCategorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Origine</label>
                <select name="origine">
                    <option value="">Toutes</option>
                    <option value="local" <?= $origine == 'local' ? 'selected' : '' ?>>Local</option>
                    <option value="importe" <?= $origine == 'importe' ? 'selected' : '' ?>>Importé</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Trier par</label>
                <select name="sort_by">
                    <option value="nom" <?= $sortBy == 'nom' ? 'selected' : '' ?>>Nom</option>
                    <option value="distance_transport" <?= $sortBy == 'distance_transport' ? 'selected' : '' ?>>Distance</option>
                    <option value="saison" <?= $sortBy == 'saison' ? 'selected' : '' ?>>Saison</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="produitList.php" class="btn-reset"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>

        <div class="table-container">
            <table id="produitsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Eco-Score</th>
                        <th>Origine</th>
                        <th>Distance</th>
                        <th>Saison</th>
                        <th>Actions</th>
=======
        <!-- Header -->
        <div class="header-section">
            <div class="header-title">
                <i class="fas fa-carrot"></i>
                Gestion des Produits
            </div>
            <div class="header-actions">
                <a href="addProduit.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Ajouter un produit
                </a>
                <a href="categorieList.php" class="btn btn-primary">
                    <i class="fas fa-bullseye"></i> Gérer les Categories
                </a>
            </div>
        </div>

        <!-- Stats & Search -->
        <div class="stats-bar">
            <div class="stats-items">
                <div class="stat-item">
                    <i class="fas fa-apple-alt"></i>
                    <span><strong><?= $totalProduits ?></strong> produits</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-leaf"></i>
                    <span><strong><?= $locauxCount ?></strong> locaux</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-tag"></i>
                    <span><strong><?= $categoriesCount ?></strong> catégories</span>
                </div>
            </div>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                <button class="btn-search"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-card">
            <table id="produitsTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-carrot"></i> Nom</th>
                        <th><i class="fas fa-image"></i> Image</th>
                        <th><i class="fas fa-tag"></i> Cat</th>
                        <th><i class="fas fa-globe"></i> Origine</th>
                        <th><i class="fas fa-truck"></i> Dist</th>
                        <th><i class="fas fa-box-open"></i> Emballage</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produits) > 0): ?>
<<<<<<< HEAD
                        <?php foreach ($produits as $p): ?>
                            <?php $isHighlighted = !empty($search) && (stripos($p->getNom(), $search) !== false) ? 'highlighted-row' : ''; ?>
                            <tr class="<?= $isHighlighted ?>">
                                <td style="font-weight: 700; color: #999;">#<?= $p->getIdProduit() ?></td>
                                <td style="font-weight: 600; color: var(--text-dark);">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <?php if ($p->getImage()): ?>
                                            <img src="../assets/images/<?= htmlspecialchars($p->getImage()) ?>" class="product-img">
                                        <?php else: ?>
                                            <div style="background: #f0f0f0; width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">🍎</div>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($p->getNom()) ?>
                                    </div>
                                </td>
                                <td style="font-weight: 600; color: var(--primary-blue);"><?= number_format($p->getPrix(), 2) ?> DT</td>
                                <td>
                                    <span class="badge" style="background: <?= $p->getStock() > 10 ? '#e3f2fd' : '#ffebee' ?>; color: <?= $p->getStock() > 10 ? '#1976d2' : '#c62828' ?>;">
                                        <?= $p->getStock() ?> en stock
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $score = $p->getEcoScore();
                                        $color = ($score >= 80) ? '#4CAF50' : (($score >= 50) ? '#FF9800' : '#F44336');
                                    ?>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <div style="width: 12px; height: 12px; border-radius: 50%; background: <?= $color ?>"></div>
                                        <span style="font-weight: 700; color: <?= $color ?>"><?= $score ?>/100</span>
                                    </div>
                                </td>
                                <td><span class="badge badge-<?= strtolower($p->getOrigine()) ?>"><?= ucfirst($p->getOrigine()) ?></span></td>
                                <td><?= htmlspecialchars($p->getDistanceTransport()) ?> km</td>
                                <td><?= ucfirst(htmlspecialchars($p->getSaison())) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="editProduit.php?id=<?= $p->getIdProduit() ?>" class="action-btn btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="action-btn btn-delete" title="Supprimer" onclick="confirmDelete(<?= $p->getIdProduit() ?>); return false;"><i class="fas fa-trash"></i></a>
=======
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($produit->getIdProduit()) ?></td>
                                <td style="font-weight: 600;"><?= htmlspecialchars($produit->getNom()) ?></td>
                                <td>
                                    <?php if ($produit->getImage()): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($produit->getImage()) ?>" class="product-img">
                                    <?php else: ?>
                                        <div style="background: #f0f0f0; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5em;">🥕</div>
                                    <?php endif; ?>
                                </td>
                                <td>#<?= htmlspecialchars($produit->getIdCategorie()) ?></td>
                                <td>
                                    <span class="badge <?= $produit->getOrigine() === 'local' ? 'badge-local' : 'badge-importe' ?>">
                                        <?= $produit->getOrigine() === 'local' ? 'Local' : 'Importé' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($produit->getDistanceTransport()) ?> km</td>
                                <td><?= htmlspecialchars($produit->getEmballage()) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="editProduit.php?id=<?= $produit->getIdProduit() ?>" class="action-btn btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="action-btn btn-delete" onclick="confirmDelete(<?= $produit->getIdProduit() ?>); return false;" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
<<<<<<< HEAD
                        <tr><td colspan="8" style="text-align: center; padding: 50px; color: #999;"><i class="fas fa-box-open" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i> Aucun produit trouvé</td></tr>
=======
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <h3>Aucun produit trouvé</h3>
                                    <p>Ajoutez votre premier produit dès maintenant !</p>
                                </div>
                            </td>
                        </tr>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

<<<<<<< HEAD
        <div class="footer">
=======
        <!-- Footer Actions -->
        <div class="footer-section">
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
            <div class="pagination">
                <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
<<<<<<< HEAD
            <div class="btn-group">
                <button onclick="exportToExcel()" class="btn" style="background: #2e7d32; color: white;"><i class="fas fa-file-excel"></i> Excel</button>
                <button onclick="exportToPDF()" class="btn" style="background: #c62828; color: white;"><i class="fas fa-file-pdf"></i> PDF</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        const modal = document.getElementById("statsModal");
        const btn = document.getElementById("openStatsBtn");
        const span = document.querySelector(".close-modal");

        btn.onclick = () => { modal.style.display = "flex"; setTimeout(() => modal.classList.add('show'), 10); renderCharts(); }
        span.onclick = () => { modal.classList.remove('show'); setTimeout(() => modal.style.display = "none", 300); }
        window.onclick = (e) => { if (e.target == modal) { modal.classList.remove('show'); setTimeout(() => modal.style.display = "none", 300); } }

        function renderCharts() {
            // 1. Tendance Eco-Score (Courbe)
            const ctxEco = document.getElementById('ecoTrendChart').getContext('2d');
            new Chart(ctxEco, {
                type: 'line',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Score Écologique',
                        data: <?= json_encode($ecoScoreData) ?>,
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
            });

            // 2. Segments de Prix (Barres)
            const ctxPrice = document.getElementById('priceBarChart').getContext('2d');
            new Chart(ctxPrice, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_keys($priceDist)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($priceDist)) ?>,
                        backgroundColor: ['#81C784', '#64B5F6', '#FFB74D'],
                        borderRadius: 8
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            // 3. Top Produits Commandés (Courbe)
            const ctxOrder = document.getElementById('orderVolumeChart').getContext('2d');
            new Chart(ctxOrder, {
                type: 'line',
                data: {
                    labels: <?= json_encode($orderLabels) ?>,
                    datasets: [{
                        label: 'Volume de Commandes',
                        data: <?= json_encode($orderQtys) ?>,
                        borderColor: '#2196F3',
                        backgroundColor: 'rgba(33, 150, 243, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2196F3'
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }

=======
            <button class="btn-export" onclick="exportTable()">
                <i class="fas fa-file-export"></i> Exporter
=======
    <title>Gestion des Produits - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-list">
        <div class="header">
            <h1>
                <i class="fas fa-apple-alt"></i>
                Gestion des Produits
            </h1>
            <a href="addProduit.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter un produit
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-box"></i>
                    <span>Total: <strong><?= count($produits) ?></strong> produits</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-leaf"></i>
                    <span>Locaux: <strong>
                        <?php
                        $localCount = count(array_filter($produits, fn($p) => $p->getOrigine() === 'local'));
                        echo $localCount;
                        ?>
                    </strong></span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher un produit..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="produitsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-box"></i> Nom</th>
                            <th><i class="fas fa-image"></i> Image</th>
                            <th><i class="fas fa-tag"></i> Catégorie</th>
                            <th><i class="fas fa-globe"></i> Origine</th>
                            <th><i class="fas fa-truck"></i> Distance</th>
                            <th><i class="fas fa-leaf"></i> Transformation</th>
                            <th><i class="fas fa-box-open"></i> Emballage</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($produits) > 0): ?>
                            <?php foreach ($produits as $produit): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($produit->getIdProduit()) ?></td>
                                    <td class="produit-title"><?= htmlspecialchars($produit->getNom()) ?></td>
                                    <td>
                                        <?php if ($produit->getImage()): ?>
                                            <div class="table-image-container">
                                                <img src="../assets/images/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" class="table-image">
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #999;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>#<?= htmlspecialchars($produit->getIdCategorie()) ?></td>
                                    <td>
                                        <?php
                                        $origineClass = $produit->getOrigine() === 'local' ? 'origine-local' : 'origine-importe';
                                        $origineIcon = $produit->getOrigine() === 'local' ? 'fa-leaf' : 'fa-globe';
                                        $origineText = $produit->getOrigine() === 'local' ? 'Local' : 'Importé';
                                        ?>
                                        <span class="origine-badge <?= $origineClass ?>">
                                            <i class="fas <?= $origineIcon ?>"></i>
                                            <?= $origineText ?>
                                        </span>
                                    </td>
                                    <td><i class="fas fa-ruler"></i> <?= htmlspecialchars($produit->getDistanceTransport()) ?> km</td>
                                    <td>
                                        <?php
                                        $transformation = $produit->getTransformation();
                                        $transformationClass = '';
                                        
                                        switch($transformation) {
                                            case 'brut':
                                                $transformationClass = 'transformation-brut';
                                                break;
                                            case 'transforme':
                                                $transformationClass = 'transformation-transforme';
                                                break;
                                            case 'ultra_transforme':
                                                $transformationClass = 'transformation-ultra';
                                                break;
                                        }
                                        ?>
                                        <span class="transformation-badge <?= $transformationClass ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $transformation))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($produit->getEmballage()): ?>
                                            <span class="emballage-badge">
                                                <i class="fas fa-box-open"></i>
                                                <?= htmlspecialchars($produit->getEmballage()) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #999;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="editProduit.php?id=<?= $produit->getIdProduit() ?>" class="action-btn edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <a href="#" class="action-btn delete" onclick="confirmDelete(<?= $produit->getIdProduit() ?>); return false;">
                                                <i class="fas fa-trash"></i> Suppr
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="empty-message">
                                    <i class="fas fa-empty-folder"></i>
                                    <h3>Aucun produit trouvé</h3>
                                    <p>Commencez par ajouter votre premier produit !</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <div class="pagination">
                <button class="page-btn" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active" id="page1">1</button>
                <button class="page-btn" id="page2" style="display:none;">2</button>
                <button class="page-btn" id="page3" style="display:none;">3</button>
                <button class="page-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button class="export-btn" onclick="exportTable()">
                <i class="fas fa-download"></i> Exporter
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
            </button>
        </div>
    </div>

<<<<<<< HEAD
=======
    <script src="../assets/js/recette.js"></script>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
    <script>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                window.location.href = 'deleteProduit.php?id=' + id;
            }
        }
<<<<<<< HEAD

        function exportToExcel() {
            const table = document.getElementById("produitsTable");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Produits" });
            XLSX.writeFile(wb, "Produits_NutriLoop.xlsx");
        }

        function exportToPDF() {
            const element = document.getElementById('produitsTable');
            const opt = { margin: 10, filename: 'Produits_NutriLoop.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' } };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
>
</html>
=======
<<<<<<< HEAD

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('produitsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[1]; // Nom column
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
=======
    </script>

    <style>
        .origine-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .origine-badge.origine-local {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .origine-badge.origine-importe {
            background: #ffe0b2;
            color: #e65100;
        }

        .transformation-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .transformation-badge.transformation-brut {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .transformation-badge.transformation-transforme {
            background: #fff9c4;
            color: #f57f17;
        }

        .transformation-badge.transformation-ultra {
            background: #ffccbc;
            color: #bf360c;
        }

        .emballage-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            background: #e3f2fd;
            color: #1565c0;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .table-image-container {
            display: inline-block;
            width: 50px;
            height: 50px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .table-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
</body>
</html>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
