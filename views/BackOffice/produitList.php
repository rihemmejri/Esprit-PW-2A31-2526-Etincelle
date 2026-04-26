<?php
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
    <style>
        :root {
            --primary-blue: #003366;
            --success-green: #4CAF50;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
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
        }
    </style>
</head>
<body>
    <div class="container-list">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-bar">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

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
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produits) > 0): ?>
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
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <h3>Aucun produit trouvé</h3>
                                    <p>Ajoutez votre premier produit dès maintenant !</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Actions -->
        <div class="footer-section">
            <div class="pagination">
                <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
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
        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                window.location.href = 'deleteProduit.php?id=' + id;
            }
        }
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
