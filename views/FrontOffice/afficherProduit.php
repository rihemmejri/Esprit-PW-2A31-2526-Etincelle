<?php
include '../../controleurs/ProduitController.php';
<<<<<<< HEAD
include '../../controleurs/CategorieController.php';
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();
$categorieController = new CategorieController();
$allCategories = $categorieController->listCategories();
$categoryMapping = [];
foreach ($allCategories as $cat) {
    $categoryMapping[$cat->getIdCategorie()] = $cat->getNomCategorie();
}
=======
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c

// Si c'est une requête AJAX pour les détails
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $produit = $produitController->getProduitById($id);
        
        if ($produit) {
            echo json_encode([
                'success' => true,
                'id' => $produit->getIdProduit(),
                'nom' => $produit->getNom(),
                'origine' => $produit->getOrigine(),
                'distance_transport' => $produit->getDistanceTransport(),
                'type_transport' => $produit->getTypeTransport(),
                'emballage' => $produit->getEmballage(),
                'transformation' => $produit->getTransformation(),
                'saison' => $produit->getSaison()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Sinon, afficher la page normale
$produits = $produitController->listProduits();
<<<<<<< HEAD

// Calcul des statistiques
$totalProduits = count($produits);
$locauxCount = count(array_filter($produits, fn($p) => $p->getOrigine() === 'local'));
$categoriesCount = count(array_unique(array_map(fn($p) => $p->getIdCategorie(), $produits)));
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Nos Produits - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2E7D32, #1565C0);
            --success-green: #2E7D32;
            --info-blue: #1565C0;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container-client {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        /* Hero Section Styling */
        .hero-section {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 50px 30px;
            text-align: center;
            color: white;
            margin: 20px 0 60px;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .hero-nav {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 12px;
        }

        .hero-nav a {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 22px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(5px);
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.9em;
        }

        .hero-nav a:hover {
            background: white;
            color: var(--success-green);
            transform: translateY(-3px);
        }

        .hero-section h1 {
            font-size: 2.8em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .hero-section p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-top: -40px;
            margin-bottom: 40px;
            padding: 0 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 18px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-card h3 {
            font-size: 2.5em;
            color: var(--success-green);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text-gray);
            font-weight: 500;
            font-size: 0.95em;
        }

        /* Search & Filter Bar */
        .filter-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group label {
            display: block;
            font-size: 0.75em;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--success-green);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-group input, .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 10px;
            font-size: 1em;
            outline: none;
            transition: 0.3s;
        }

        .filter-group input:focus, .filter-group select:focus {
            border-color: var(--success-green);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .product-image {
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            font-size: 5.5em;
            overflow: hidden;
        }

        .product-badge {
position: absolute;
top: 6px;
right: 6px;

background: var(--success-green);
color: white;

padding: 1px 4px;        
font-size: 0.45em;      
font-weight: 600;        
border-radius: 10px;    
line-height: 1;         
box-shadow: 0 1px 3px rgba(0,0,0,0.08);
z-index: 10;

letter-spacing: 0.2px;
text-transform: uppercase;
        }

        .product-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.4em;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .product-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: var(--text-gray);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .product-description {
            font-size: 0.95em;
            color: var(--text-gray);
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        .btn-details {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-details:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(21, 101, 192, 0.3);
        }

        .no-results {
            grid-column: 1 / -1;
            background: white;
            padding: 80px 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .no-results i {
            font-size: 4em;
            color: #ddd;
            margin-bottom: 20px;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            width: 90%;
            max-width: 600px;
            border-radius: 25px;
            padding: 35px;
            position: relative;
            animation: modalIn 0.4s ease-out;
        }

        @keyframes modalIn {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close {
            position: absolute;
            right: 25px;
            top: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #ccc;
            transition: 0.3s;
        }

        .close:hover { color: var(--text-dark); }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 25px;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 15px;
        }

        .detail-label {
            display: block;
            font-size: 0.8em;
            color: var(--success-green);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Global Footer */
        .simple-footer {
            background: var(--success-green);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
        }

        .simple-footer p {
            font-size: 0.95em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Categories icons mapping */
        .origine-local { color: #2e7d32; font-weight: 700; }
        .origine-importe { color: #1565c0; font-weight: 700; }
    </style>
=======
    <title>Nos Produits - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
</head>
<body>
    <div class="container-client">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-apple-alt"></i>
<<<<<<< HEAD
                Nos Produits
            </h1>
            <p>Découvrez notre sélection d'aliments sains, durables et locaux pour votre bien-être.</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $totalProduits ?></h3>
                <p>Produits enregistrés</p>
            </div>
            <div class="stat-card">
                <h3><?= $locauxCount ?></h3>
                <p>Produits locaux 🌱</p>
            </div>
            <div class="stat-card">
                <h3><?= $categoriesCount ?></h3>
                <p>Catégories d'aliments</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-container">
            <div class="filter-group">
                <label for="searchInput"><i class="fas fa-search"></i> Rechercher</label>
                <input type="text" id="searchInput" placeholder="Nom du produit..." onkeyup="filterProducts()">
            </div>
            <div class="filter-group">
                <label for="categoryFilter"><i class="fas fa-filter"></i> Catégorie</label>
                <select id="categoryFilter" onchange="filterProducts()">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= $cat->getIdCategorie() ?>"><?= htmlspecialchars($cat->getNomCategorie()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid" id="produitsGrid">
            <?php if (count($produits) > 0): ?>
                <?php foreach ($produits as $produit): ?>
                    <div class="product-card" 
                         data-id="<?= $produit->getIdProduit() ?>"
                         data-nom="<?= strtolower(htmlspecialchars($produit->getNom())) ?>"
                         data-cat="<?= $produit->getIdCategorie() ?>">
                        
                        <div class="product-image">
                            <?php if ($produit->getImage()): ?>
                                <img src="../assets/images/<?= htmlspecialchars($produit->getImage()) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?php
                                $icone = '';
                                $nom = strtolower($produit->getNom());
                                if (strpos($nom, 'tomate') !== false) $icone = '🍅';
                                elseif (strpos($nom, 'salade') !== false) $icone = '🥗';
                                elseif (strpos($nom, 'carotte') !== false) $icone = '🥕';
                                elseif (strpos($nom, 'pomme') !== false) $icone = '🍎';
                                elseif (strpos($nom, 'orange') !== false) $icone = '🍊';
                                elseif (strpos($nom, 'banane') !== false) $icone = '🍌';
                                elseif (strpos($nom, 'fraise') !== false) $icone = '🍓';
                                elseif (strpos($nom, 'raisin') !== false) $icone = '🍇';
                                elseif (strpos($nom, 'oignon') !== false) $icone = '🧅';
                                elseif (strpos($nom, 'ail') !== false) $icone = '🧄';
                                elseif (strpos($nom, 'poivron') !== false) $icone = '🫑';
                                elseif (strpos($nom, 'concombre') !== false) $icone = '🥒';
                                elseif (strpos($nom, 'broccoli') !== false) $icone = '🥦';
                                elseif (strpos($nom, 'chou') !== false) $icone = '🥬';
                                elseif (strpos($nom, 'avocat') !== false) $icone = '🥑';
                                elseif (strpos($nom, 'citron') !== false) $icone = '🍋';
                                elseif (strpos($nom, 'lait') !== false) $icone = '🥛';
                                elseif (strpos($nom, 'viande') !== false) $icone = '🥩';
                                elseif (strpos($nom, 'poulet') !== false) $icone = '🍗';
                                elseif (strpos($nom, 'poisson') !== false) $icone = '🐟';
                                else $icone = '🥘';
                                ?>
                                <span><?= $icone ?></span>
                            <?php endif; ?>
                            
                            <?php if ($produit->getOrigine() === 'local'): ?>
                                <div class="product-badge">🌱 Local</div>
                            <?php else: ?>
                                <div class="product-badge" style="background: #1565C0;">🌍 Importé</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title"><?= htmlspecialchars($produit->getNom()) ?></h3>
                            
                            <div class="product-meta">
=======
                Découvrez nos Produits
            </h1>
            <p>Une sélection de produits locaux et durables pour une alimentation respectueuse de l'environnement</p>
        </div>

        <!-- Grille des produits -->
        <div class="recipes-grid" id="produitsGrid">
            <?php if (count($produits) > 0): ?>
                <?php foreach ($produits as $produit): ?>
                    <div class="recipe-card" 
                         data-id="<?= $produit->getIdProduit() ?>"
                         data-titre="<?= strtolower(htmlspecialchars($produit->getNom())) ?>">
                        
                        <!-- Image avec emoji personnalisé -->
                        <div class="recipe-image">
                            <?php
                            $icone = '';
                            $nom = strtolower($produit->getNom());
                            
                            if (strpos($nom, 'tomate') !== false) $icone = '🍅';
                            elseif (strpos($nom, 'salade') !== false) $icone = '🥗';
                            elseif (strpos($nom, 'carotte') !== false) $icone = '🥕';
                            elseif (strpos($nom, 'pomme') !== false) $icone = '🍎';
                            elseif (strpos($nom, 'orange') !== false) $icone = '🍊';
                            elseif (strpos($nom, 'banane') !== false) $icone = '🍌';
                            elseif (strpos($nom, 'fraise') !== false) $icone = '🍓';
                            elseif (strpos($nom, 'raisin') !== false) $icone = '🍇';
                            elseif (strpos($nom, 'oignon') !== false) $icone = '🧅';
                            elseif (strpos($nom, 'ail') !== false) $icone = '🧄';
                            elseif (strpos($nom, 'poivron') !== false) $icone = '🫑';
                            elseif (strpos($nom, 'concombre') !== false) $icone = '🥒';
                            elseif (strpos($nom, 'broccoli') !== false) $icone = '🥦';
                            elseif (strpos($nom, 'chou') !== false) $icone = '🥬';
                            elseif (strpos($nom, 'avocat') !== false) $icone = '🥑';
                            elseif (strpos($nom, 'citron') !== false) $icone = '🍋';
                            else $icone = '🥘';
                            ?>
                            <span style="font-size: 5em;"><?= $icone ?></span>
                            <div class="recipe-badge">
                                🌱 Local
                            </div>
                        </div>
                        
                        <div class="recipe-content">
                            <h3 class="recipe-title"><?= htmlspecialchars($produit->getNom()) ?></h3>
                            
                            <div class="recipe-meta">
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                <span class="meta-item">
                                    <i class="fas fa-<?= $produit->getOrigine() === 'local' ? 'leaf' : 'globe' ?>"></i> 
                                    <?= $produit->getOrigine() === 'local' ? 'Local' : 'Importé' ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-truck"></i> <?= $produit->getDistanceTransport() ?> km
                                </span>
                            </div>
                            
<<<<<<< HEAD
                            <p class="product-description">
=======
                            <p class="recipe-description">
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                <?php
                                $details = array_filter([
                                    $produit->getTransformation(),
                                    $produit->getEmballage(),
                                    $produit->getSaison()
                                ]);
<<<<<<< HEAD
                                echo htmlspecialchars(implode(' • ', $details)) ?: 'Produit de qualité supérieure sélectionné pour vous.';
                                ?>
                            </p>
                            
                            <div class="product-footer">
                                <span style="font-size: 0.85em; color: #999; font-style: italic;">
                                    <?= isset($categoryMapping[$produit->getIdCategorie()]) ? htmlspecialchars($categoryMapping[$produit->getIdCategorie()]) : 'Sans catégorie' ?>
                                </span>
                                <button class="btn-details" onclick='showProduitDetails(<?= json_encode([
                                    'nom' => $produit->getNom(),
                                    'origine' => $produit->getOrigine(),
                                    'distance' => $produit->getDistanceTransport(),
                                    'transport' => $produit->getTypeTransport(),
=======
                                echo htmlspecialchars(implode(' • ', $details)) ?: 'Produit de qualité';
                                ?>
                            </p>
                            
                            <div class="recipe-footer">
                                <div class="recipe-difficulte origine-<?= $produit->getOrigine() ?>">
                                    <i class="fas fa-<?= $produit->getOrigine() === 'local' ? 'leaf' : 'plane' ?>"></i>
                                    <span>
                                        <?= $produit->getOrigine() === 'local' ? 'Local' : 'Importé' ?>
                                    </span>
                                </div>
                                <button class="btn-details" onclick='showProduitDetails(<?= json_encode([
                                    'id' => $produit->getIdProduit(),
                                    'nom' => $produit->getNom(),
                                    'origine' => $produit->getOrigine(),
                                    'distance_transport' => $produit->getDistanceTransport(),
                                    'type_transport' => $produit->getTypeTransport(),
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                    'emballage' => $produit->getEmballage(),
                                    'transformation' => $produit->getTransformation(),
                                    'saison' => $produit->getSaison()
                                ]) ?>)'>
<<<<<<< HEAD
                                    Détails <i class="fas fa-arrow-right"></i>
=======
                                    Voir détails <i class="fas fa-arrow-right"></i>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
<<<<<<< HEAD
                <div class="no-results" id="emptyState">
                    <i class="fas fa-search"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Revenez plus tard pour découvrir nos nouveaux produits !</p>
                </div>
            <?php endif; ?>
            
            <div class="no-results" id="noMatch" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>Aucun résultat trouvé</h3>
                <p>Essayez de modifier vos critères de recherche.</p>
            </div>
=======
                <div class="no-results">
                    <i class="fas fa-box"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Revenez plus tard pour découvrir nos produits !</p>
                </div>
            <?php endif; ?>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
        </div>
    </div>

    <!-- Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
<<<<<<< HEAD
                <h2 id="modalTitle" style="color: var(--success-green); font-size: 1.8em;"></h2>
=======
                <h2 id="modalTitle"></h2>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
            </div>
            <div class="modal-body">
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-globe"></i> Origine</span>
                        <span class="detail-value" id="detailOrigine"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-truck"></i> Distance</span>
                        <span class="detail-value" id="detailDistance"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-shipping-fast"></i> Transport</span>
                        <span class="detail-value" id="detailTransport"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-box-open"></i> Emballage</span>
                        <span class="detail-value" id="detailEmballage"></span>
                    </div>
                    <div class="detail-item">
<<<<<<< HEAD
                        <span class="detail-label"><i class="fas fa-industry"></i> Transformation</span>
=======
                        <span class="detail-label"><i class="fas fa-leaf"></i> Transformation</span>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                        <span class="detail-value" id="detailTransformation"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar"></i> Saison</span>
                        <span class="detail-value" id="detailSaison"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <footer class="simple-footer">
        <p><i class="fas fa-heart" style="color: #ff4757;"></i> NutriLoop AI - Manger sainement pour une vie meilleure</p>
    </footer>

    <script>
        function filterProducts() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            const cards = document.querySelectorAll('.product-card');
            let matchCount = 0;

            cards.forEach(card => {
                const nom = card.getAttribute('data-nom');
                const cat = card.getAttribute('data-cat');
                
                const matchesSearch = nom.includes(query);
                const matchesCat = category === '' || cat === category;

                if (matchesSearch && matchesCat) {
                    card.style.display = 'flex';
                    matchCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noMatch').style.display = matchCount === 0 && cards.length > 0 ? 'block' : 'none';
        }

        function showProduitDetails(p) {
            const modal = document.getElementById('detailsModal');
            document.getElementById('modalTitle').textContent = p.nom;
            document.getElementById('detailOrigine').textContent = p.origine === 'local' ? 'Local 🌱' : 'Importé 🌍';
            document.getElementById('detailDistance').textContent = p.distance + ' km';
            document.getElementById('detailTransport').textContent = p.transport;
            document.getElementById('detailEmballage').textContent = p.emballage;
            document.getElementById('detailTransformation').textContent = p.transformation;
            document.getElementById('detailSaison').textContent = p.saison || 'Toute l\'année';
            modal.style.display = 'block';
        }

        // Close Modal
        document.querySelector('.close').onclick = () => document.getElementById('detailsModal').style.display = 'none';
        window.onclick = (e) => { if (e.target == document.getElementById('detailsModal')) document.getElementById('detailsModal').style.display = 'none'; }
=======
    <script src="../assets/js/recette.js"></script>
    <script>
        function showProduitDetails(produit) {
            const modal = document.getElementById('detailsModal');
            document.getElementById('modalTitle').textContent = produit.nom;
            document.getElementById('detailOrigine').textContent = produit.origine === 'local' ? 'Local' : 'Importé';
            document.getElementById('detailDistance').textContent = produit.distance_transport + ' km';
            document.getElementById('detailTransport').textContent = produit.type_transport;
            document.getElementById('detailEmballage').textContent = produit.emballage;
            document.getElementById('detailTransformation').textContent = produit.transformation;
            document.getElementById('detailSaison').textContent = produit.saison || 'N/A';
            modal.style.display = 'block';
        }

        // Fermeture du modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
    </script>
</body>
</html>
