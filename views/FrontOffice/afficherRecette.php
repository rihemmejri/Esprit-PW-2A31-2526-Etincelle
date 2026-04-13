<?php
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$recetteController = new RecetteController();

// Si c'est une requête AJAX pour les détails
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $recette = $recetteController->getRecetteById($id);
        
        if ($recette) {
            echo json_encode([
                'success' => true,
                'id' => $recette->getIdRecette(),
                'nom' => $recette->getNom(),
                'description' => $recette->getDescription(),
                'temps_preparation' => $recette->getTempsPreparation(),
                'difficulte' => $recette->getDifficulte(),
                'type_repas' => $recette->getTypeRepas(),
                'origine' => $recette->getOrigine(),
                'nb_personne' => $recette->getNbPersonne()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Recette non trouvée']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Sinon, afficher la page normale
$recettes = $recetteController->listRecettes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Recettes - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-client">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-utensils"></i>
                Découvrez nos Recettes
            </h1>
            <p>Des recettes saines, équilibrées et délicieuses pour tous les goûts</p>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                Filtrer les recettes
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Rechercher</label>
                    <input type="text" id="searchRecette" placeholder="Nom de la recette..." onkeyup="filterRecettes()">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-chart-line"></i> Difficulté</label>
                    <select id="filterDifficulte" onchange="filterRecettes()">
                        <option value="">Toutes</option>
                        <option value="FACILE">Facile</option>
                        <option value="MOYEN">Moyen</option>
                        <option value="DIFFICILE">Difficile</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-mug-hot"></i> Type de repas</label>
                    <select id="filterTypeRepas" onchange="filterRecettes()">
                        <option value="">Tous</option>
                        <option value="PETIT_DEJEUNER">Petit déjeuner</option>
                        <option value="DEJEUNER">Déjeuner</option>
                        <option value="DINER">Dîner</option>
                        <option value="DESSERT">Dessert</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grille des recettes -->
        <div class="recipes-grid" id="recipesGrid">
            <?php if (count($recettes) > 0): ?>
                <?php foreach ($recettes as $recette): ?>
                    <div class="recipe-card" 
                         data-id="<?= $recette->getIdRecette() ?>"
                         data-titre="<?= strtolower(htmlspecialchars($recette->getNom())) ?>"
                         data-difficulte="<?= $recette->getDifficulte() ?>"
                         data-type="<?= $recette->getTypeRepas() ?>">
                        
                        <!-- Image avec emoji personnalisé -->
                        <div class="recipe-image">
                            <?php
                            $icone = '';
                            $nom = strtolower($recette->getNom());
                            
                            if (strpos($nom, 'pizza') !== false) $icone = '🍕';
                            elseif (strpos($nom, 'tajine') !== false) $icone = '🍲';
                            elseif (strpos($nom, 'salade') !== false) $icone = '🥗';
                            elseif (strpos($nom, 'gateau') !== false) $icone = '🍰';
                            elseif (strpos($nom, 'poulet') !== false) $icone = '🍗';
                            elseif (strpos($nom, 'poisson') !== false) $icone = '🐟';
                            elseif (strpos($nom, 'glace') !== false) $icone = '🍨';
                            elseif (strpos($nom, 'pasta') !== false) $icone = '🍝';
                            elseif (strpos($nom, 'riz') !== false) $icone = '🍚';
                            elseif ($recette->getTypeRepas() == 'PETIT_DEJEUNER') $icone = '☕';
                            elseif ($recette->getTypeRepas() == 'DESSERT') $icone = '🍰';
                            else $icone = '🍽️';
                            ?>
                            <span style="font-size: 5em;"><?= $icone ?></span>
                            <div class="recipe-badge">
                                ⭐ NutriLoop
                            </div>
                        </div>
                        
                        <div class="recipe-content">
                            <h3 class="recipe-title"><?= htmlspecialchars($recette->getNom()) ?></h3>
                            
                            <div class="recipe-meta">
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i> <?= $recette->getTempsPreparation() ?> min
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-users"></i> <?= $recette->getNbPersonne() ?> pers
                                </span>
                            </div>
                            
                            <p class="recipe-description">
                                <?= htmlspecialchars(substr($recette->getDescription(), 0, 120)) . (strlen($recette->getDescription()) > 120 ? '...' : '') ?>
                            </p>
                            
                            <div class="recipe-footer">
                                <div class="recipe-difficulte <?= $recette->getDifficulte() ?>">
                                    <i class="fas fa-chart-line"></i>
                                    <span>
                                        <?php 
                                        switch($recette->getDifficulte()) {
                                            case 'FACILE': echo 'Facile'; break;
                                            case 'MOYEN': echo 'Moyen'; break;
                                            case 'DIFFICILE': echo 'Difficile'; break;
                                        }
                                        ?>
                                    </span>
                                </div>
                                <button class="btn-details" onclick='showDetails(<?= json_encode([
                                    'id' => $recette->getIdRecette(),
                                    'nom' => $recette->getNom(),
                                    'temps_preparation' => $recette->getTempsPreparation(),
                                    'difficulte' => $recette->getDifficulte(),
                                    'type_repas' => $recette->getTypeRepas(),
                                    'nb_personne' => $recette->getNbPersonne(),
                                    'origine' => $recette->getOrigine(),
                                    'description' => $recette->getDescription()
                                ]) ?>)'>
                                    Voir détails <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-empty-folder"></i>
                    <h3>Aucune recette disponible</h3>
                    <p>Revenez plus tard pour découvrir nos délicieuses recettes !</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Message aucun résultat -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>Aucune recette trouvée</h3>
            <p>Essayez de modifier vos critères de recherche</p>
        </div>
    </div>

    <script src="../assets/js/recette.js"></script>
</body>
</html>