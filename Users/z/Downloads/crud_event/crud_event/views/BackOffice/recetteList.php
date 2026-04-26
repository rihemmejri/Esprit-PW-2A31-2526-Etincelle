<?php
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$RecetteController = new RecetteController();

// Récupérer le terme de recherche depuis l'URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    // Récupérer toutes les recettes et filtrer par nom
    $allRecettes = $RecetteController->listRecettes();
    $recettes = array_filter($allRecettes, function($r) use ($search) {
        return stripos($r->getNom(), $search) !== false;
    });
} else {
    $recettes = $RecetteController->listRecettes();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Recettes - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
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

        /* Style pour la barre de recherche active */
        .search-active {
            background: #e3f2fd;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .search-active .search-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .clear-search {
            background: #f44336;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.3s;
        }
        .clear-search:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
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

        <!-- Affichage de la recherche active -->
        <?php if (!empty($search)): ?>
        <div class="search-active">
            <div class="search-info">
                <i class="fas fa-search" style="color: #1976d2;"></i>
                <span>Résultats pour : <strong><?= htmlspecialchars($search) ?></strong></span>
                <span style="color: #666;">(<?= count($recettes) ?> recette(s) trouvée(s))</span>
            </div>
            <a href="recetteList.php" class="clear-search">
                <i class="fas fa-times"></i> Effacer la recherche
            </a>
        </div>
        <?php endif; ?>

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
                <input type="text" id="searchInput" placeholder="Rechercher une recette..." onkeyup="searchTable()" value="<?= htmlspecialchars($search) ?>">
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
                                    <td class="description-full"><?= nl2br(htmlspecialchars($recette->getDescription())) ?>
                                    <td class="text-center"><i class="fas fa-hourglass-half"></i> <?= htmlspecialchars($recette->getTempsPreparation()) ?> min\n                                    <td>
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
                                            <a href="viewRecette.php?id=<?= $recette->getIdRecette() ?>" class="action-btn view">
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
                                    <?php if (!empty($search)): ?>
                                        <p style="margin-top: 10px;">
                                            <a href="recetteList.php" style="color: #2e7d32;">← Effacer la recherche</a>
                                        </p>
                                    <?php endif; ?>
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
        // Fonction pour la confirmation de suppression
        function confirmDelete(id) {
            if(confirm('Êtes-vous sûr de vouloir supprimer cette recette ? Cette action est irréversible.')) {
                window.location.href = 'deleteRecette.php?id=' + id + '&confirm=yes';
            }
        }

        // Fonction pour la recherche dans le tableau
        function searchTable() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let table = document.getElementById('recettesTable');
            let tr = table.getElementsByTagName('tr');
            
            for(let i = 1; i < tr.length; i++) {
                let tdNom = tr[i].getElementsByTagName('td')[1];
                if(tdNom) {
                    let nomValue = tdNom.textContent || tdNom.innerText;
                    if(nomValue.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Pagination
        let currentPage = 1;
        let rowsPerPage = 10;
        
        function paginateTable() {
            let table = document.getElementById('recettesTable');
            let rows = table.getElementsByTagName('tr');
            let totalRows = rows.length - 1;
            let totalPages = Math.ceil(totalRows / rowsPerPage);
            
            for(let i = 1; i < rows.length; i++) {
                let page = Math.ceil((i - 1) / rowsPerPage) + 1;
                rows[i].style.display = page === currentPage ? '' : 'none';
            }
            
            for(let i = 1; i <= 3; i++) {
                let btn = document.getElementById('page' + i);
                if(btn) {
                    btn.classList.remove('active');
                    if(i === currentPage) btn.classList.add('active');
                    btn.style.display = i <= totalPages ? 'inline-block' : 'none';
                }
            }
        }
        
        function previousPage() {
            if(currentPage > 1) {
                currentPage--;
                paginateTable();
            }
        }
        
        function nextPage() {
            let table = document.getElementById('recettesTable');
            let rows = table.getElementsByTagName('tr');
            let totalRows = rows.length - 1;
            let totalPages = Math.ceil(totalRows / rowsPerPage);
            if(currentPage < totalPages) {
                currentPage++;
                paginateTable();
            }
        }
        
        function exportTable() {
            let csv = [];
            let rows = document.querySelectorAll('#recettesTable tr');
            for(let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll('td, th');
                for(let j = 0; j < cols.length - 1; j++) {
                    let text = cols[j].innerText.replace(/,/g, ';');
                    row.push('"' + text + '"');
                }
                csv.push(row.join(','));
            }
            let link = document.createElement('a');
            link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv.join('\n'));
            link.download = 'recettes_export.csv';
            link.click();
        }
        
        window.onload = function() {
            paginateTable();
        };
    </script>
</body>
</html>