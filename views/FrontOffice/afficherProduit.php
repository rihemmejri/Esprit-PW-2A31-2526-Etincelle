<?php
include '../../controleurs/ProduitController.php';
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Produits - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-client">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-apple-alt"></i>
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
                                <span class="meta-item">
                                    <i class="fas fa-<?= $produit->getOrigine() === 'local' ? 'leaf' : 'globe' ?>"></i> 
                                    <?= $produit->getOrigine() === 'local' ? 'Local' : 'Importé' ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-truck"></i> <?= $produit->getDistanceTransport() ?> km
                                </span>
                            </div>
                            
                            <p class="recipe-description">
                                <?php
                                $details = array_filter([
                                    $produit->getTransformation(),
                                    $produit->getEmballage(),
                                    $produit->getSaison()
                                ]);
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
                                    'emballage' => $produit->getEmballage(),
                                    'transformation' => $produit->getTransformation(),
                                    'saison' => $produit->getSaison()
                                ]) ?>)'>
                                    Voir détails <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-box"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Revenez plus tard pour découvrir nos produits !</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle"></h2>
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
                        <span class="detail-label"><i class="fas fa-leaf"></i> Transformation</span>
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
    </script>
</body>
</html>
