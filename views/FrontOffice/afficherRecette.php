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
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles spécifiques pour la page recettes qui complètent style.css */
        .hero-section {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            padding: 80px 20px;
            text-align: center;
            border-radius: 30px;
            margin: 40px 20px;
        }
        
        .hero-section h1 {
            font-size: 2.5rem;
            color: #2e7d32;
            margin-bottom: 15px;
        }
        
        .hero-section p {
            font-size: 1.1rem;
            color: #555;
        }
        
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 20px;
            margin: 0 20px 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .filters-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2e7d32;
            margin-bottom: 20px;
        }
        
        .filters-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .recipes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 0 20px 60px;
        }
        
        .recipe-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .recipe-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .recipe-image {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .recipe-emoji {
            font-size: 5rem;
        }
        
        .recipe-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #4CAF50;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .recipe-content {
            padding: 20px;
        }
        
        .recipe-title {
            font-size: 1.3rem;
            color: #2e7d32;
            margin-bottom: 10px;
        }
        
        .recipe-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .recipe-meta span {
            font-size: 0.8rem;
            color: #666;
        }
        
        .recipe-meta i {
            margin-right: 5px;
            color: #4CAF50;
        }
        
        .recipe-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .recipe-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .difficulte-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
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
        
        .btn-details {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 25px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-details:hover {
            background: #388e3c;
            transform: scale(1.02);
        }
        
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
            max-width: 550px;
            width: 90%;
            border-radius: 20px;
            overflow: hidden;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
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
            background: #4CAF50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .modal-detail {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-detail strong {
            display: inline-block;
            width: 120px;
            color: #2e7d32;
        }
        
        .btn-etapes {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .btn-etapes:hover {
            background: #388e3c;
        }
        
        .no-results {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            grid-column: 1 / -1;
        }
        
        .no-results i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .recipes-grid {
                grid-template-columns: 1fr;
            }
            .filters-grid {
                flex-direction: column;
            }
            .hero-section h1 {
                font-size: 1.8rem;
            }
        }
        /* Style pour que le header soit comme dans l'image */
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
    padding: 15px 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-img {
    width: 40px;
    height: 40px;
    border-radius: 10px;
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2e7d32;
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: 30px;
}

.nav-menu li a {
    text-decoration: none;
    color: var(--dark-blue);
    font-weight: 500;
    transition: 0.3s;
}

.nav-menu li a:hover,
.nav-menu li a.active {
    color: #4CAF50;
}

.btn-dashboard {
    background: var(--dark-blue);
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
.recipe-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recipe-image {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.recipe-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.recipe-card:hover .recipe-image img {
    transform: scale(1.05);
}
    </style>
</head>
<body>

    <!-- ========== HEADER (IDENTIQUE À INDEX.HTML) ========== -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
                <span class="logo-text">NutriLoop</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">Accueil</a></li>
                <li><a href="index.html#features">Fonctionnalités</a></li>
                <li><a href="index.html#modules">Modules</a></li>
                <li><a href="about.html">À propos</a></li>
                <li><a href="contact.html">Contact</a></li>
                
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- ========== CONTENU PRINCIPAL ========== -->
    <div class="hero-section">
        <h1><i class="fas fa-utensils"></i> Nos Recettes Anti-Gaspi</h1>
        <p>Découvrez des recettes saines, équilibrées et délicieuses pour tous les goûts</p>
    </div>

    <div class="filters-section">
        <div class="filters-title">
            <i class="fas fa-filter"></i> Filtrer les recettes
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

    <div class="recipes-grid" id="recipesGrid">
        <?php if (count($recettes) > 0): ?>
            <?php foreach ($recettes as $recette): ?>
                <div class="recipe-card" 
                     data-id="<?= $recette->getIdRecette() ?>"
                     data-titre="<?= strtolower(htmlspecialchars($recette->getNom())) ?>"
                     data-difficulte="<?= $recette->getDifficulte() ?>"
                     data-type="<?= $recette->getTypeRepas() ?>">
                    
                  <div class="recipe-image">
   <?php
    $nom = strtolower($recette->getNom());
    $imageUrl = '';
    
    if (strpos($nom, 'pizza') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'tajine') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'salade') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'gateau') !== false || strpos($nom, 'cake') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'pasta') !== false || strpos($nom, 'spaghetti') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'riz') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'poulet') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'poisson') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'glace') !== false || strpos($nom, 'ice cream') !== false) {
        // Image pour glace / ice cream
        $imageUrl = 'https://images.unsplash.com/photo-1576506295286-5cda18df43e7?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'soupe') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'burger') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'crepe') !== false || strpos($nom, 'crêpe') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1519676867248-6e4f6f4f0a1c?w=400&h=250&fit=crop';
    } elseif (strpos($nom, 'couscous') !== false) {
        $imageUrl = 'https://images.unsplash.com/photo-1617098900591-3f4c9b9f5a1a?w=400&h=250&fit=crop';
    } else {
        // Image par défaut
        $imageUrl = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=250&fit=crop';
    }
?>
    <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($recette->getNom()) ?>" class="recipe-img">
    <div class="recipe-badge">⭐ NutriLoop</div>
</div>
                    
                    <div class="recipe-content">
                        <h3 class="recipe-title"><?= htmlspecialchars($recette->getNom()) ?></h3>
                        
                        <div class="recipe-meta">
                            <span><i class="fas fa-clock"></i> <?= $recette->getTempsPreparation() ?> min</span>
                            <span><i class="fas fa-users"></i> <?= $recette->getNbPersonne() ?> pers</span>
                        </div>
                        
                        <p class="recipe-description">
                            <?= htmlspecialchars(substr($recette->getDescription(), 0, 100)) . (strlen($recette->getDescription()) > 100 ? '...' : '') ?>
                        </p>
                        
                        <div class="recipe-footer">
                            <div class="difficulte-badge difficulte-<?= $recette->getDifficulte() ?>">
                                <i class="fas fa-chart-line"></i>
                                <?php 
                                switch($recette->getDifficulte()) {
                                    case 'FACILE': echo 'Facile'; break;
                                    case 'MOYEN': echo 'Moyen'; break;
                                    case 'DIFFICILE': echo 'Difficile'; break;
                                }
                                ?>
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

    <div id="noResults" class="no-results" style="display: none;">
        <i class="fas fa-search"></i>
        <h3>Aucune recette trouvée</h3>
        <p>Essayez de modifier vos critères de recherche</p>
    </div>

    <!-- Modal Détails -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle"></i> Détails de la recette</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <!-- ========== FOOTER (IDENTIQUE À INDEX.HTML) ========== -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>NutriLoop</h3>
                <p>L'intelligence artificielle au service de votre assiette pour une meilleure santé et un monde plus durable.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="index.html#features">Fonctionnalités</a></li>
                    <li><a href="index.html#modules">Modules</a></li>
                    <li><a href="about.html">À propos</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Modules</h4>
                <ul>
                    <li><a href="#">Utilisateurs</a></li>
                    <li><a href="#">Nutrition</a></li>
                    <li><a href="#">Produits</a></li>
                    <li><a href="afficherRecette.php">Recettes Anti-Gaspi</a></li>
                    <li><a href="#">Suivi</a></li>
                    <li><a href="#">Événements</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> Tunis, Tunisie</li>
                    <li><i class="fas fa-envelope"></i> contact@nutriloop.ai</li>
                    <li><i class="fas fa-phone"></i> +216 70 000 000</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 NutriLoop - Tous droits réservés | Projet étudiant - Ryhem Mejri</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        function filterRecettes() {
            const searchTerm = document.getElementById('searchRecette').value.toLowerCase();
            const difficulte = document.getElementById('filterDifficulte').value;
            const typeRepas = document.getElementById('filterTypeRepas').value;
            
            const cards = document.querySelectorAll('.recipe-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const titre = card.getAttribute('data-titre') || '';
                const cardDifficulte = card.getAttribute('data-difficulte');
                const cardType = card.getAttribute('data-type');
                
                let match = true;
                if (searchTerm && !titre.includes(searchTerm)) match = false;
                if (difficulte && cardDifficulte !== difficulte) match = false;
                if (typeRepas && cardType !== typeRepas) match = false;
                
                if (match) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
        }
        
        function showDetails(recette) {
            const modal = document.getElementById('detailsModal');
            const modalBody = document.getElementById('modalBody');
            
            const typeRepasText = {
                'PETIT_DEJEUNER': 'Petit déjeuner',
                'DEJEUNER': 'Déjeuner',
                'DINER': 'Dîner',
                'DESSERT': 'Dessert'
            };
            
            const difficulteText = {
                'FACILE': 'Facile',
                'MOYEN': 'Moyen',
                'DIFFICILE': 'Difficile'
            };
            
            modalBody.innerHTML = `
                <div class="modal-detail"><strong><i class="fas fa-utensils"></i> Nom :</strong> ${recette.nom}</div>
                <div class="modal-detail"><strong><i class="fas fa-align-left"></i> Description :</strong><br>${recette.description}</div>
                <div class="modal-detail"><strong><i class="fas fa-clock"></i> Temps :</strong> ${recette.temps_preparation} minutes</div>
                <div class="modal-detail"><strong><i class="fas fa-users"></i> Personnes :</strong> ${recette.nb_personne}</div>
                <div class="modal-detail"><strong><i class="fas fa-chart-line"></i> Difficulté :</strong> ${difficulteText[recette.difficulte] || recette.difficulte}</div>
                <div class="modal-detail"><strong><i class="fas fa-mug-hot"></i> Type :</strong> ${typeRepasText[recette.type_repas] || recette.type_repas}</div>
                ${recette.origine ? `<div class="modal-detail"><strong><i class="fas fa-globe"></i> Origine :</strong> ${recette.origine}</div>` : ''}
                <div style="text-align: center;"><a href="afficherPreperation.php?id=${recette.id}" class="btn-etapes"><i class="fas fa-list-ol"></i> Voir les étapes</a></div>
            `;
            modal.style.display = 'flex';
        }
        
        function closeModal() { document.getElementById('detailsModal').style.display = 'none'; }
        window.onclick = function(event) { if (event.target === document.getElementById('detailsModal')) closeModal(); }
        
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            if (hamburger && navMenu) {
                hamburger.addEventListener('click', function() {
                    hamburger.classList.toggle('active');
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>