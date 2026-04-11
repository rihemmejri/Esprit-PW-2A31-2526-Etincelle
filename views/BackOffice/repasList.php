<?php
include '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/repas.php';

$RepasController = new RepasController();
$repas = $RepasController->listRepas();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Repas - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        /* Style supplémentaire pour garantir l'affichage complet */
        .table-container {
            overflow-x: auto !important;
        }
        
        table {
            min-width: 1200px !important;
        }
        
        /* Badge pour les calories */
        .calories-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #fff3e0;
            color: #e65100;
        }
        
        .calories-badge i {
            margin-right: 5px;
        }
        
        /* Badge pour le type de repas */
        .type-repas-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
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
        
        /* Valeurs nutritionnelles */
        .nutri-value {
            font-weight: 600;
        }
        
        .nutri-value i {
            margin-right: 5px;
            font-size: 0.8rem;
        }
        
        /* Total calories dans le footer */
        .total-calories {
            background: linear-gradient(135deg, #4CAF50, #003366);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: bold;
        }
        
        /* Style pour le résumé nutritionnel */
        .summary-bar {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .summary-stat {
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .summary-stat i {
            margin-right: 8px;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container-list">
        <div class="header">
            <h1>
                <i class="fas fa-utensils"></i>
                Gestion des Repas
            </h1>
            <a href="addRepas.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter un repas
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-utensils"></i>
                    <span>Total: <strong><?= count($repas) ?></strong> repas</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-fire"></i>
                    <span>Calories totales: <strong>
                        <?php 
                        if (count($repas) > 0) {
                            $totalCalories = 0;
                            $totalProteines = 0;
                            $totalGlucides = 0;
                            $totalLipides = 0;
                            foreach ($repas as $r) {
                                $totalCalories += $r->getCalories();
                                $totalProteines += $r->getProteines();
                                $totalGlucides += $r->getGlucides();
                                $totalLipides += $r->getLipides();
                            }
                            echo $totalCalories . ' kcal';
                        } else {
                            echo '0 kcal';
                        }
                        ?>
                    </strong></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Moyenne protéines: <strong>
                        <?php 
                        if (count($repas) > 0) {
                            echo round($totalProteines / count($repas), 1) . ' g';
                        } else {
                            echo '0 g';
                        }
                        ?>
                    </strong></span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher un repas..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="repasTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-utensils"></i> Nom</th>
                            <th><i class="fas fa-mug-hot"></i> Type</th>
                            <th><i class="fas fa-fire"></i> Calories (kcal)</th>
                            <th><i class="fas fa-dumbbell"></i> Protéines (g)</th>
                            <th><i class="fas fa-bread-slice"></i> Glucides (g)</th>
                            <th><i class="fas fa-oil-can"></i> Lipides (g)</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($repas) > 0): ?> 
                            <?php foreach ($repas as $repasItem): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($repasItem->getIdRepas()) ?></td>
                                    <td class="repas-title"><?= htmlspecialchars($repasItem->getNom()) ?></td>
                                    <td>
                                        <?php 
                                        $typeRepas = $repasItem->getType();
                                        $typeIcon = '';
                                        $typeText = '';
                                        $typeClass = '';
                                        
                                        switch($typeRepas) {
                                            case 'PETIT_DEJEUNER':
                                                $typeIcon = 'fa-coffee';
                                                $typeText = 'Petit déjeuner';
                                                $typeClass = 'type-petit-dejeuner';
                                                break;
                                            case 'DEJEUNER':
                                                $typeIcon = 'fa-utensils';
                                                $typeText = 'Déjeuner';
                                                $typeClass = 'type-dejeuner';
                                                break;
                                            case 'DINER':
                                                $typeIcon = 'fa-moon';
                                                $typeText = 'Dîner';
                                                $typeClass = 'type-diner';
                                                break;
                                            case 'COLLATION':
                                                $typeIcon = 'fa-apple-alt';
                                                $typeText = 'Collation';
                                                $typeClass = 'type-collation';
                                                break;
                                            default:
                                                $typeIcon = 'fa-question';
                                                $typeText = $typeRepas;
                                                $typeClass = '';
                                        }
                                        ?>
                                        <span class="type-repas-badge <?= $typeClass ?>">
                                            <i class="fas <?= $typeIcon ?>"></i>
                                            <?= $typeText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="calories-badge">
                                            <i class="fas fa-fire"></i>
                                            <?= htmlspecialchars($repasItem->getCalories()) ?> kcal
                                        </span>
                                    </td>
                                    <td class="nutri-value">
                                        <i class="fas fa-dumbbell" style="color: #4CAF50;"></i>
                                        <?= htmlspecialchars($repasItem->getProteines()) ?> g
                                    </td>
                                    <td class="nutri-value">
                                        <i class="fas fa-bread-slice" style="color: #FF9800;"></i>
                                        <?= htmlspecialchars($repasItem->getGlucides()) ?> g
                                    </td>
                                    <td class="nutri-value">
                                        <i class="fas fa-oil-can" style="color: #2196F3;"></i>
                                        <?= htmlspecialchars($repasItem->getLipides()) ?> g
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="editRepas.php?id=<?= $repasItem->getIdRepas() ?>" class="action-btn edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <a href="#" class="action-btn delete" onclick="confirmDelete(<?= $repasItem->getIdRepas() ?>); return false;">
                                                <i class="fas fa-trash"></i> Suppr
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?> 
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-message">
                                    <i class="fas fa-empty-folder"></i>
                                    <h3>Aucun repas trouvé</h3>
                                    <p>Commencez par ajouter votre premier repas !</p>
                                    <a href="addRepas.php" class="add-btn-small">
                                        <i class="fas fa-plus-circle"></i>
                                        Ajouter un repas
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (count($repas) > 0): ?>
                    <tfoot style="background: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="3" style="text-align: right;">
                                <i class="fas fa-calculator"></i> <strong>Totaux:</strong>
                            </td>
                            <td>
                                <span class="total-calories">
                                    <i class="fas fa-fire"></i>
                                    <?= $totalCalories ?? 0 ?> kcal
                                </span>
                            </td>
                            <td><i class="fas fa-dumbbell"></i> <?= round($totalProteines ?? 0, 1) ?> g</td>
                            <td><i class="fas fa-bread-slice"></i> <?= round($totalGlucides ?? 0, 1) ?> g</td>
                            <td><i class="fas fa-oil-can"></i> <?= round($totalLipides ?? 0, 1) ?> g</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
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

    <script>
        // Fonction de confirmation de suppression
        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce repas ? Cette action est irréversible.')) {
                window.location.href = 'deleteRepas.php?id=' + id;
            }
        }
        
        // Fonction de recherche dans le tableau
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('repasTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
        
        // Pagination simple
        let currentPage = 1;
        const rowsPerPage = 10;
        
        function showPage(page) {
            const table = document.getElementById('repasTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            const totalRows = rows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            for (let i = 0; i < totalRows; i++) {
                if (i >= (page - 1) * rowsPerPage && i < page * rowsPerPage) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
            
            currentPage = page;
            
            // Mettre à jour l'affichage des boutons de page
            for (let i = 1; i <= 3; i++) {
                const btn = document.getElementById('page' + i);
                if (btn) {
                    if (i === page) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                }
            }
        }
        
        function previousPage() {
            showPage(currentPage - 1);
        }
        
        function nextPage() {
            showPage(currentPage + 1);
        }
        
        // Exportation du tableau en CSV
        function exportTable() {
            const table = document.getElementById('repasTable');
            const rows = table.getElementsByTagName('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('th') || row.getElementsByTagName('td');
                let rowData = [];
                
                for (let j = 0; j < cells.length; j++) {
                    let cellText = cells[j].textContent || cells[j].innerText;
                    cellText = cellText.replace(/,/g, ';');
                    rowData.push(cellText);
                }
                csv.push(rowData.join(','));
            }
            
            const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'repas_export.csv';
            a.click();
            URL.revokeObjectURL(url);
        }
        
        // Initialiser la pagination au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('repasTable');
            const rows = table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');
            if (rows && rows.length > 0) {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                for (let i = 1; i <= Math.min(totalPages, 3); i++) {
                    const btn = document.getElementById('page' + i);
                    if (btn) {
                        btn.style.display = 'inline-block';
                        btn.textContent = i;
                        btn.onclick = (function(pageNum) {
                            return function() { showPage(pageNum); };
                        })(i);
                    }
                }
                showPage(1);
            }
        });
    </script>
</body>
</html>