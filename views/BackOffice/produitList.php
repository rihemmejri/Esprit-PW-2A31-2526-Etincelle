<?php
include '../../controleurs/ProduitController.php';
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();
$produits = $produitController->listProduits();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            </button>
        </div>
    </div>

    <script src="../assets/js/recette.js"></script>
    <script>
        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                window.location.href = 'deleteProduit.php?id=' + id;
            }
        }
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
</body>
</html>
