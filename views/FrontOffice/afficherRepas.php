<?php
include '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/repas.php';

$repasController = new RepasController();

// Si c'est une requête AJAX pour les détails
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $repas = $repasController->getRepasById($id);
        
        if ($repas) {
            echo json_encode([
                'success' => true,
                'id' => $repas->getIdRepas(),
                'nom' => $repas->getNom(),
                'type' => $repas->getType(),
                'calories' => $repas->getCalories(),
                'proteines' => $repas->getProteines(),
                'glucides' => $repas->getGlucides(),
                'lipides' => $repas->getLipides()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Repas non trouvé']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Sinon, afficher la page normale
$repas = $repasController->listRepas();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Repas - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        /* Styles spécifiques pour l'affichage des repas */
        .container-client {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .hero-section {
            text-align: center;
            background: linear-gradient(135deg, #4CAF50, #003366);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
        }
        
        .hero-section h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .hero-section h1 i {
            margin-right: 10px;
        }
        
        .filters-section {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .filters-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #003366;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 0.9rem;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        /* Grille des repas */
        .repas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .repas-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .repas-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        
        .repas-image {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            text-align: center;
            padding: 30px;
            position: relative;
        }
        
        .repas-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #4CAF50;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .repas-content {
            padding: 20px;
        }
        
        .repas-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #003366;
        }
        
        .repas-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        
        .type-petit-dejeuner {
            background: #e3f2fd;
            color: #1565c0;
        }
        
        .type-dejeuner {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .type-diner {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .type-collation {
            background: #fff3e0;
            color: #e65100;
        }
        
        /* Grille nutritionnelle */
        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 15px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 15px;
        }
        
        .nutrition-item {
            text-align: center;
        }
        
        .nutrition-value {
            font-size: 1.3rem;
            font-weight: bold;
        }
        
        .nutrition-label {
            font-size: 0.75rem;
            color: #666;
            margin-top: 5px;
        }
        
        .nutrition-item.calories .nutrition-value {
            color: #e65100;
        }
        
        .nutrition-item.proteines .nutrition-value {
            color: #4CAF50;
        }
        
        .nutrition-item.glucides .nutrition-value {
            color: #FF9800;
        }
        
        .nutrition-item.lipides .nutrition-value {
            color: #2196F3;
        }
        
        .repas-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .btn-details {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        
        .btn-details:hover {
            background: #45a049;
        }
        
        .btn-details i {
            margin-left: 5px;
        }
        
        .total-calories-badge {
            font-size: 0.8rem;
            color: #e65100;
        }
        
        .no-results {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #4CAF50, #003366);
            color: white;
            padding: 20px;
            border-radius: 20px 20px 0 0;
        }
        
        .modal-header h2 {
            margin: 0;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-detail-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-detail-label {
            font-weight: 600;
            color: #003366;
            width: 120px;
            display: inline-block;
        }
        
        .modal-detail-value {
            color: #555;
        }
        
        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }
        
        .btn-fermer {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container-client">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-utensils"></i>
                Découvrez nos Repas
            </h1>
            <p>Des repas sains, équilibrés et délicieux pour prendre soin de votre santé</p>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                Filtrer les repas
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Rechercher</label>
                    <input type="text" id="searchRepas" placeholder="Nom du repas..." onkeyup="filterRepas()">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-mug-hot"></i> Type de repas</label>
                    <select id="filterType" onchange="filterRepas()">
                        <option value="">Tous</option>
                        <option value="PETIT_DEJEUNER">Petit déjeuner</option>
                        <option value="DEJEUNER">Déjeuner</option>
                        <option value="DINER">Dîner</option>
                        <option value="COLLATION">Collation</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-fire"></i> Calories max</label>
                    <select id="filterCalories" onchange="filterRepas()">
                        <option value="">Toutes</option>
                        <option value="300">Moins de 300 kcal</option>
                        <option value="500">Moins de 500 kcal</option>
                        <option value="800">Moins de 800 kcal</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grille des repas -->
        <div class="repas-grid" id="repasGrid">
            <?php if (count($repas) > 0): ?>
                <?php foreach ($repas as $repasItem): ?>
                    <div class="repas-card" 
                         data-id="<?= $repasItem->getIdRepas() ?>"
                         data-nom="<?= strtolower(htmlspecialchars($repasItem->getNom())) ?>"
                         data-type="<?= $repasItem->getType() ?>"
                         data-calories="<?= $repasItem->getCalories() ?>">
                        
                        <div class="repas-image">
                            <?php
                            $icone = '';
                            $nom = strtolower($repasItem->getNom());
                            $type = $repasItem->getType();
                            
                            if (strpos($nom, 'salade') !== false) $icone = '🥗';
                            elseif (strpos($nom, 'poulet') !== false) $icone = '🍗';
                            elseif (strpos($nom, 'poisson') !== false) $icone = '🐟';
                            elseif (strpos($nom, 'pasta') !== false) $icone = '🍝';
                            elseif (strpos($nom, 'riz') !== false) $icone = '🍚';
                            elseif (strpos($nom, 'soupe') !== false) $icone = '🥣';
                            elseif (strpos($nom, 'porridge') !== false) $icone = '🥣';
                            elseif (strpos($nom, 'bowl') !== false) $icone = '🥣';
                            elseif ($type == 'PETIT_DEJEUNER') $icone = '☕';
                            elseif ($type == 'COLLATION') $icone = '🍎';
                            else $icone = '🍽️';
                            ?>
                            <span style="font-size: 4em;"><?= $icone ?></span>
                            <div class="repas-badge">
                                <i class="fas fa-leaf"></i> NutriLoop
                            </div>
                        </div>
                        
                        <div class="repas-content">
                            <h3 class="repas-title"><?= htmlspecialchars($repasItem->getNom()) ?></h3>
                            
                            <span class="repas-type <?= strtolower(str_replace('_', '-', $repasItem->getType())) ?>">
                                <?php 
                                switch($repasItem->getType()) {
                                    case 'PETIT_DEJEUNER': echo '☕ Petit déjeuner'; break;
                                    case 'DEJEUNER': echo '🍽️ Déjeuner'; break;
                                    case 'DINER': echo '🌙 Dîner'; break;
                                    case 'COLLATION': echo '🍎 Collation'; break;
                                    default: echo $repasItem->getType();
                                }
                                ?>
                            </span>
                            
                            <!-- Grille nutritionnelle -->
                            <div class="nutrition-grid">
                                <div class="nutrition-item calories">
                                    <div class="nutrition-value"><?= $repasItem->getCalories() ?> <span style="font-size: 0.8rem;">kcal</span></div>
                                    <div class="nutrition-label"><i class="fas fa-fire"></i> Calories</div>
                                </div>
                                <div class="nutrition-item proteines">
                                    <div class="nutrition-value"><?= $repasItem->getProteines() ?> <span style="font-size: 0.8rem;">g</span></div>
                                    <div class="nutrition-label"><i class="fas fa-dumbbell"></i> Protéines</div>
                                </div>
                                <div class="nutrition-item glucides">
                                    <div class="nutrition-value"><?= $repasItem->getGlucides() ?> <span style="font-size: 0.8rem;">g</span></div>
                                    <div class="nutrition-label"><i class="fas fa-bread-slice"></i> Glucides</div>
                                </div>
                                <div class="nutrition-item lipides">
                                    <div class="nutrition-value"><?= $repasItem->getLipides() ?> <span style="font-size: 0.8rem;">g</span></div>
                                    <div class="nutrition-label"><i class="fas fa-oil-can"></i> Lipides</div>
                                </div>
                            </div>
                            
                            <div class="repas-footer">
                                <div class="total-calories-badge">
                                    <i class="fas fa-chart-pie"></i>
                                    Total: <?= $repasItem->getCalories() ?> kcal
                                </div>
                                <button class="btn-details" onclick='showDetails(<?= json_encode([
                                    'id' => $repasItem->getIdRepas(),
                                    'nom' => $repasItem->getNom(),
                                    'type' => $repasItem->getType(),
                                    'calories' => $repasItem->getCalories(),
                                    'proteines' => $repasItem->getProteines(),
                                    'glucides' => $repasItem->getGlucides(),
                                    'lipides' => $repasItem->getLipides()
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
                    <h3>Aucun repas disponible</h3>
                    <p>Revenez plus tard pour découvrir nos délicieux repas !</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Message aucun résultat -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>Aucun repas trouvé</h3>
            <p>Essayez de modifier vos critères de recherche</p>
        </div>
    </div>

    <!-- Modal Détails -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Détails du repas</h2>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer">
                <button class="btn-fermer" onclick="closeModal()">Fermer</button>
            </div>
        </div>
    </div>

    <script>
        // Fonction de filtrage
        function filterRepas() {
            const searchTerm = document.getElementById('searchRepas').value.toLowerCase();
            const filterType = document.getElementById('filterType').value;
            const filterCalories = parseInt(document.getElementById('filterCalories').value);
            
            const cards = document.querySelectorAll('.repas-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const nom = card.getAttribute('data-nom');
                const type = card.getAttribute('data-type');
                const calories = parseInt(card.getAttribute('data-calories'));
                
                let show = true;
                
                if (searchTerm && !nom.includes(searchTerm)) {
                    show = false;
                }
                
                if (show && filterType && type !== filterType) {
                    show = false;
                }
                
                if (show && filterCalories && calories > filterCalories) {
                    show = false;
                }
                
                if (show) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            const noResults = document.getElementById('noResults');
            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
        
        // Afficher les détails dans la modal
        function showDetails(repas) {
            const modal = document.getElementById('detailsModal');
            const modalBody = document.getElementById('modalBody');
            
            // Déterminer l'icône du type
            let typeIcon = '';
            switch(repas.type) {
                case 'PETIT_DEJEUNER': typeIcon = '☕'; break;
                case 'DEJEUNER': typeIcon = '🍽️'; break;
                case 'DINER': typeIcon = '🌙'; break;
                case 'COLLATION': typeIcon = '🍎'; break;
                default: typeIcon = '🍴';
            }
            
            modalBody.innerHTML = `
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-utensils"></i> Nom :</span>
                    <span class="modal-detail-value">${repas.nom}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-mug-hot"></i> Type :</span>
                    <span class="modal-detail-value">${typeIcon} ${repas.type}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-fire"></i> Calories :</span>
                    <span class="modal-detail-value"><strong style="color:#e65100;">${repas.calories} kcal</strong></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-dumbbell"></i> Protéines :</span>
                    <span class="modal-detail-value">${repas.proteines} g</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-bread-slice"></i> Glucides :</span>
                    <span class="modal-detail-value">${repas.glucides} g</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-oil-can"></i> Lipides :</span>
                    <span class="modal-detail-value">${repas.lipides} g</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-chart-pie"></i> Répartition :</span>
                    <div style="margin-top: 10px;">
                        <div style="background: #e0e0e0; border-radius: 10px; overflow: hidden;">
                            <div style="display: flex;">
                                <div style="background: #4CAF50; width: ${(repas.proteines * 4 / repas.calories * 100)}%; height: 10px;"></div>
                                <div style="background: #FF9800; width: ${(repas.glucides * 4 / repas.calories * 100)}%; height: 10px;"></div>
                                <div style="background: #2196F3; width: ${(repas.lipides * 9 / repas.calories * 100)}%; height: 10px;"></div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; margin-top: 8px; font-size: 0.7rem;">
                            <span><span style="color:#4CAF50;">●</span> Protéines (${Math.round(repas.proteines * 4 / repas.calories * 100)}%)</span>
                            <span><span style="color:#FF9800;">●</span> Glucides (${Math.round(repas.glucides * 4 / repas.calories * 100)}%)</span>
                            <span><span style="color:#2196F3;">●</span> Lipides (${Math.round(repas.lipides * 9 / repas.calories * 100)}%)</span>
                        </div>
                    </div>
                </div>
            `;
            
            modal.style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }
        
        // Fermer la modal en cliquant en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Fonction AJAX pour récupérer les détails (optionnelle)
        function loadDetailsViaAjax(id) {
            fetch(`afficherRepas.php?ajax=details&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showDetails(data);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }
    </script>
</body>
</html>