<?php
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$RecetteController = new RecetteController();
$recettes = $RecetteController->listRecettes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Recettes - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* ========== HEADER AVEC MENU (COMME FRONT OFFICE) ========== */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2E7D32;
        }

        .logo-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-menu li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-menu li a:hover,
        .nav-menu li a.active {
            color: #4CAF50;
        }

        .btn-dashboard {
            background: #2e7d32;
            color: white !important;
            padding: 8px 20px;
            border-radius: 25px;
        }

        .btn-dashboard:hover {
            background: #388e3c;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #333;
            margin: 3px 0;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background: white;
                width: 100%;
                text-align: center;
                transition: 0.3s;
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                padding: 20px 0;
                gap: 15px;
            }
            .nav-menu.active {
                left: 0;
            }
            .hamburger {
                display: flex;
            }
        }

        /* ========== DASHBOARD CONTAINER ========== */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* ========== SIDEBAR (EXACT DASHBOARD STYLES) ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #003366 0%, #001a33 100%);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: all 0.3s;
            z-index: 100;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .logo-text small {
            font-size: 0.7rem;
            opacity: 0.7;
            font-weight: normal;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
        }

        .sidebar-nav ul {
            list-style: none;
        }

        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            cursor: pointer;
            transition: 0.3s;
            color: rgba(255,255,255,0.7);
            position: relative;
        }

        .sidebar-nav .nav-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        .sidebar-nav .nav-item:hover {
            background: rgba(76,175,80,0.1);
            color: #4CAF50;
        }

        .sidebar-nav .nav-item.active {
            background: rgba(76,175,80,0.2);
            color: #4CAF50;
            border-left: 3px solid #4CAF50;
        }

        .sidebar-nav .nav-item .badge {
            margin-left: auto;
            background: rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .user-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #4CAF50;
        }

        .user-info h4 {
            color: white;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        .user-info p {
            font-size: 0.7rem;
            opacity: 0.7;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }

        .content-area {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .container-list {
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 70px;
                z-index: 999;
                transition: left 0.3s;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .content-area {
                padding: 1rem;
            }
        }
    </style>
    <style>
        /* Style supplémentaire pour garantir l'affichage complet */
        .description-full {
            white-space: normal !important;
            word-wrap: break-word !important;
            line-height: 1.5 !important;
        }
        
        /* Pour que le tableau soit large */
        .table-container {
            overflow-x: auto !important;
        }
        
        table {
            min-width: 1800px !important;
        }
        
        td:nth-child(3) {
            min-width: 450px !important;
            max-width: none !important;
        }
    </style>
</head>
<body>
    <!-- ========== HEADER AVEC MENU (COMME FRONT OFFICE) ========== -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
                <span class="logo-text">NutriLoop</span>
            </div>
            <ul class="nav-menu">
                <li><a href="../FrontOffice/index.html">Accueil</a></li>
                <li><a href="../FrontOffice/index.html#features">Fonctionnalités</a></li>
                <li><a href="../FrontOffice/index.html#modules">Modules</a></li>
                <li><a href="index.html" class="btn-dashboard active">Dashboard</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- ========== DASHBOARD CONTAINER ========== -->
<div class="dashboard-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../FrontOffice/image/logo.PNG" alt="Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
                <span class="logo-text">NutriLoop<br><small>Admin</small></span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="index.html" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item" data-module="users">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                    <span class="badge">Module 1</span>
                </li>
                <li class="nav-item">
                    <a href="repasList.php" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrition Smart</span>
                    </a>
                    <span class="badge">Module 2</span>
                </li>
                <li class="nav-item" data-module="products">
                    <i class="fas fa-boxes"></i>
                    <span>Gestion Produits</span>
                    <span class="badge">Module 3</span>
                </li>
                <li class="nav-item active">
                    <a href="recetteList.php" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-utensils"></i>
                        <span>Recettes Anti-Gaspi</span>
                    </a>
                    <span class="badge">Module 4</span>
                </li>
                <li class="nav-item" data-module="tracking">
                    <i class="fas fa-chart-simple"></i>
                    <span>Suivi & Objectifs</span>
                    <span class="badge">Module 5</span>
                </li>
                <li class="nav-item" data-module="events">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Gestion Événements</span>
                    <span class="badge">Module 6</span>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <img src="../FrontOffice/image/ryhem.PNG" alt="Ryhem Mejri" onerror="this.src='https://randomuser.me/api/portraits/women/68.jpg'">
                <div>
                    <h4>Ryhem Mejri</h4>
                    <p>Administrateur Principal</p>
                </div>
            </div>
            <a href="../FrontOffice/index.html" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container-list">
        <div class="header">
            <h1>
                <i class="fas fa-utensils"></i>
                Gestion des Recettes
            </h1>
            <a href="addRecette.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter une recette
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-utensils"></i>
                    <span>Total: <strong><?= count($recettes) ?></strong> recettes</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock"></i>
                    <span>Temps moyen: <strong>
                        <?php 
                        if (count($recettes) > 0) {
                            $totalTemps = 0;
                            foreach ($recettes as $r) {
                                $totalTemps += $r->getTempsPreparation();
                            }
                            echo round($totalTemps / count($recettes)) . ' min';
                        } else {
                            echo '0 min';
                        }
                        ?>
                    </strong></span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher une recette..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="recettesTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-utensils"></i> Nom</th>
                            <th><i class="fas fa-align-left"></i> Description</th>
                            <th><i class="fas fa-clock"></i> Temps (min)</th>
                            <th><i class="fas fa-chart-line"></i> Difficulté</th>
                            <th><i class="fas fa-mug-hot"></i> Type de repas</th>
                            <th><i class="fas fa-globe"></i> Origine</th>
                            <th><i class="fas fa-users"></i> Personnes</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recettes) > 0): ?> 
                            <?php foreach ($recettes as $recette): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($recette->getIdRecette()) ?></td>
                                    <td class="recette-title"><?= htmlspecialchars($recette->getNom()) ?></td>
                                    <td class="description-full"><?= nl2br(htmlspecialchars($recette->getDescription())) ?> <!-- DESCRIPTION COMPLÈTE -->
                                    <td class="text-center"><i class="fas fa-hourglass-half"></i> <?= htmlspecialchars($recette->getTempsPreparation()) ?> min</td>
                                    <td>
                                        <?php 
                                        $difficulte = $recette->getDifficulte();
                                        $difficulteClass = '';
                                        $difficulteIcon = '';
                                        
                                        switch($difficulte) {
                                            case 'FACILE':
                                                $difficulteClass = 'difficulte-FACILE';
                                                $difficulteIcon = 'fa-smile';
                                                break;
                                            case 'MOYEN':
                                                $difficulteClass = 'difficulte-MOYEN';
                                                $difficulteIcon = 'fa-meh';
                                                break;
                                            case 'DIFFICILE':
                                                $difficulteClass = 'difficulte-DIFFICILE';
                                                $difficulteIcon = 'fa-frown';
                                                break;
                                        }
                                        ?>
                                        <span class="difficulte-badge <?= $difficulteClass ?>">
                                            <i class="fas <?= $difficulteIcon ?>"></i>
                                            <?= $difficulte ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $typeRepas = $recette->getTypeRepas();
                                        $typeIcon = '';
                                        $typeText = '';
                                        
                                        switch($typeRepas) {
                                            case 'PETIT_DEJEUNER':
                                                $typeIcon = 'fa-coffee';
                                                $typeText = 'Petit déjeuner';
                                                break;
                                            case 'DEJEUNER':
                                                $typeIcon = 'fa-utensils';
                                                $typeText = 'Déjeuner';
                                                break;
                                            case 'DINER':
                                                $typeIcon = 'fa-moon';
                                                $typeText = 'Dîner';
                                                break;
                                            case 'DESSERT':
                                                $typeIcon = 'fa-cake-candles';
                                                $typeText = 'Dessert';
                                                break;
                                        }
                                        ?>
                                        <span class="type-repas-badge">
                                            <i class="fas <?= $typeIcon ?>"></i>
                                            <?= $typeText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($recette->getOrigine()): ?>
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= htmlspecialchars($recette->getOrigine()) ?>
                                        <?php else: ?>
                                            <span style="color: #999;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><i class="fas fa-users"></i> <?= htmlspecialchars($recette->getNbPersonne()) ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="recetteList.php?id=<?= $recette->getIdRecette() ?>" class="action-btn view">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="editRecette.php?id=<?= $recette->getIdRecette() ?>" class="action-btn edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
<a href="#" class="action-btn delete" onclick="confirmDelete(<?= $recette->getIdRecette() ?>); return false;">
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
                                    <h3>Aucune recette trouvée</h3>
                                    <p>Commencez par ajouter votre première recette !</p>
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
            </button>
        </div>
    </div>

            </div>
        </main>
    </div>

    </main>
</div>

    <script src="../assets/js/recette.js"></script>
</body>
</html>