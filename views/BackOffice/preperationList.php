<?php
// views/backOffice/preparation/preperationList.php
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/preperation.php';

$preperationController = new PreperationController();

// Récupérer TOUTES les étapes triées par recette puis par ordre
$preperations = $preperationController->listPreperations();

// Trier manuellement par recette puis par ordre
usort($preperations, function($a, $b) {
    $recetteCompare = strcmp($a->getRecetteNom(), $b->getRecetteNom());
    if ($recetteCompare != 0) {
        return $recetteCompare;
    }
    return $a->getOrdre() - $b->getOrdre();
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étapes - Préparation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .container-list {
        max-width: 100%;
        margin: 0 auto;
        padding: 20px;
    }
    
    /* Header */
    .header {
        background: white;
        padding: 25px 35px;
        border-radius: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .header h1 {
        color: #2e7d32;
        font-size: 1.8rem;
        margin: 0;
    }
    
    .add-btn {
        background: #2e7d32;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }
    
    .add-btn:hover {
        background: #1b5e20;
        transform: scale(1.02);
    }
    
    /* Stats bar */
    .stats-bar {
        background: white;
        padding: 18px 30px;
        border-radius: 16px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.05);
    }
    
    .stats-info {
        display: flex;
        gap: 35px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        font-size: 15px;
        color: #333;
    }
    
    .stat-item i {
        color: #2e7d32;
        margin-right: 8px;
        font-size: 16px;
    }
    
    .stat-item strong {
        font-size: 18px;
        color: #2e7d32;
    }
    
    .search-box {
        display: flex;
        gap: 8px;
    }
    
    .search-box input {
        padding: 10px 18px;
        border: 1px solid #ddd;
        border-radius: 10px;
        width: 280px;
        font-size: 14px;
    }
    
    .search-box button {
        background: #2e7d32;
        border: none;
        color: white;
        padding: 10px 18px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 14px;
    }
    
    /* TABLEAU */
    .table-container {
        background: white;
        border-radius: 20px;
        overflow-x: auto;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }
    
    table {
        width: 100%;
        min-width: 2100px;
        border-collapse: collapse;
    }
    
    th {
        background: #2196f3;
        color: white;
        padding: 16px 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }
    
    th i {
        margin-right: 8px;
        font-size: 15px;
    }
    
    td {
        padding: 15px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
        font-size: 13px;
    }
    
    tr:hover {
        background: #f8f9fa;
    }
    
    /* LARGEURS SPÉCIFIQUES DES COLONNES */
    th:nth-child(1), td:nth-child(1) { width: 70px; text-align: center; }      /* ID */
    th:nth-child(2), td:nth-child(2) { width: 180px; }      /* Recette */
    th:nth-child(3), td:nth-child(3) { width: 90px; text-align: center; }      /* Ordre */
    th:nth-child(4), td:nth-child(4) { width: 450px; }      /* Instruction */
    th:nth-child(5), td:nth-child(5) { width: 85px; text-align: center; }      /* Durée */
    th:nth-child(6), td:nth-child(6) { width: 95px; text-align: center; }      /* Température */
    th:nth-child(7), td:nth-child(7) { width: 105px; text-align: center; }     /* Action */
    th:nth-child(8), td:nth-child(8) { width: 105px; text-align: center; }     /* Outil */
    th:nth-child(9), td:nth-child(9) { width: 90px; text-align: center; }      /* Quantité */
    th:nth-child(10), td:nth-child(10) { width: 220px; }     /* Astuce */
    th:nth-child(11), td:nth-child(11) { width: 240px; text-align: center; }   /* Actions */
    
    /* Cellule instruction */
    .instruction-cell {
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.5;
    }
    
    /* Badge ordre - PLUS PETIT */
    .ordre-badge {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
        display: inline-block;
        white-space: nowrap;
    }
    
    .ordre-badge i {
        font-size: 10px;
        margin-right: 4px;
    }
    
    /* Badges action et outil */
    .type-action-badge, .outil-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .type-action-COUPER { background: #e3f2fd; color: #1565c0; }
    .type-action-MELANGER { background: #f3e5f5; color: #7b1fa2; }
    .type-action-CUISSON { background: #ffebee; color: #c62828; }
    
    .outil-FOUR { background: #fff3e0; color: #e65100; }
    .outil-MIXEUR { background: #e8f5e9; color: #2e7d32; }
    .outil-CUILLERE { background: #e0f7fa; color: #00838f; }
    .outil-RAPE { background: #fce4ec; color: #ad1457; }
    
    /* Lien recette */
    .recette-link {
        color: #2e7d32;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        white-space: nowrap;
    }
    
    .recette-link i {
        font-size: 14px;
    }
    
    .recette-link:hover {
        text-decoration: underline;
    }
    
    /* Actions buttons */
    .actions {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
        justify-content: center;
    }
    
    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .action-btn.view {
        background: #2196f3;
        color: white;
    }
    
    .action-btn.edit {
        background: #ff9800;
        color: white;
    }
    
    .action-btn.delete {
        background: #f44336;
        color: white;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        filter: brightness(0.95);
    }
    
    /* Footer */
    .footer {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .pagination {
        display: flex;
        gap: 10px;
    }
    
    .page-btn {
        background: white;
        border: 1px solid #ddd;
        padding: 10px 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }
    
    .page-btn.active {
        background: #2196f3;
        color: white;
        border-color: #2196f3;
    }
    
    .page-btn:hover:not(.active) {
        background: #f0f0f0;
    }
    
    .export-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .empty-message {
        text-align: center;
        padding: 80px !important;
        color: #999;
    }
    
    .empty-message i {
        font-size: 80px;
        margin-bottom: 20px;
    }
    
    .id-cell {
        font-weight: 700;
        color: #2196f3;
        font-size: 14px;
        white-space: nowrap;
    }
    
    /* Alignement centre pour les cellules numériques */
    .text-center {
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .container-list {
            padding: 10px;
        }
        .stats-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .search-box input {
            width: 100%;
        }
    }
</style>
</head>
<body>
    <div class="container-list">
        <div class="header">
            <h1>
                <i class="fas fa-list-ol"></i>
                Gestion des Étapes de Préparation
            </h1>
            <a href="addPreperation.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter une étape
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-tasks"></i>
                    <span>Total: <strong><?= count($preperations) ?></strong> étapes</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock"></i>
                    <span>Durée moyenne: <strong>
                        <?php 
                        if (count($preperations) > 0) {
                            $totalDuree = 0;
                            foreach ($preperations as $p) {
                                $totalDuree += $p->getDuree();
                            }
                            echo round($totalDuree / count($preperations)) . ' min';
                        } else {
                            echo '0 min';
                        }
                        ?>
                    </strong></span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher par recette ou instruction..." onkeyup="filterTable()">
                <button onclick="filterTable()"><i class="fas fa-search"></i> Rechercher</button>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="preperationsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-utensils"></i> Recette</th>
                            <th><i class="fas fa-sort-numeric-down"></i> Ordre</th>
                            <th><i class="fas fa-align-left"></i> Instruction</th>
                            <th><i class="fas fa-clock"></i> Durée</th>
                            <th><i class="fas fa-thermometer-half"></i> Température</th>
                            <th><i class="fas fa-cut"></i> Action</th>
                            <th><i class="fas fa-tools"></i> Outil</th>
                            <th><i class="fas fa-weight-hanging"></i> Quantité</th>
                            <th><i class="fas fa-lightbulb"></i> Astuce</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (count($preperations) > 0): ?>
                            <?php foreach ($preperations as $prep): ?>
                                <tr class="etape-row">
                                    <td class="id-cell">#<?= htmlspecialchars($prep->getIdEtape()) ?></td>
                                    <td>
                                        <a href="viewRecette.php?id=<?= $prep->getIdRecette() ?>" class="recette-link">
                                            <i class="fas fa-book"></i>
                                            <?= htmlspecialchars($prep->getRecetteNom() ?: 'N/A') ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="ordre-badge">
                                            <i class="fas fa-check-circle"></i> Étape <?= htmlspecialchars($prep->getOrdre()) ?>
                                        </span>
                                    </td>
                                    <td class="instruction-cell">
                                        <?= htmlspecialchars(substr($prep->getInstruction(), 0, 100)) ?>
                                        <?php if(strlen($prep->getInstruction()) > 100): ?>...<?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <i class="fas fa-hourglass-half"></i> <?= htmlspecialchars($prep->getDuree() ?: 0) ?> min
                                    </td>
                                    <td class="text-center">
                                        <?php if($prep->getTemperature()): ?>
                                            <i class="fas fa-fire"></i> <?= htmlspecialchars($prep->getTemperature()) ?>°C
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $typeAction = $prep->getTypeAction();
                                        $actionClass = '';
                                        $icon = 'fa-question';
                                        switch($typeAction) {
                                            case 'COUPER': $actionClass = 'type-action-COUPER'; $icon = 'fa-cut'; break;
                                            case 'MELANGER': $actionClass = 'type-action-MELANGER'; $icon = 'fa-mix'; break;
                                            case 'CUISSON': $actionClass = 'type-action-CUISSON'; $icon = 'fa-fire'; break;
                                        }
                                        ?>
                                        <?php if($typeAction): ?>
                                            <span class="type-action-badge <?= $actionClass ?>">
                                                <i class="fas <?= $icon ?>"></i> <?= $typeAction ?>
                                            </span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $outil = $prep->getOutilUtilise();
                                        $outilClass = '';
                                        $outilIcon = '';
                                        switch($outil) {
                                            case 'FOUR': $outilClass = 'outil-FOUR'; $outilIcon = 'fa-oven'; break;
                                            case 'MIXEUR': $outilClass = 'outil-MIXEUR'; $outilIcon = 'fa-blender'; break;
                                            case 'CUILLERE': $outilClass = 'outil-CUILLERE'; $outilIcon = 'fa-utensil-spoon'; break;
                                            case 'RAPE': $outilClass = 'outil-RAPE'; $outilIcon = 'fa-cheese'; break;
                                        }
                                        ?>
                                        <?php if($outil): ?>
                                            <span class="outil-badge <?= $outilClass ?>">
                                                <i class="fas <?= $outilIcon ?>"></i> <?= $outil ?>
                                            </span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($prep->getQuantiteIngredient() ?: '—') ?>
                                    </td>
                                    <td class="instruction-cell">
                                        <?php if($prep->getAstuce()): ?>
                                            <i class="fas fa-lightbulb" style="color: #ffc107;"></i>
                                            <?= htmlspecialchars(substr($prep->getAstuce(), 0, 60)) ?>
                                            <?php if(strlen($prep->getAstuce()) > 60): ?>...<?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="viewPreperation.php?id=<?= $prep->getIdEtape() ?>" class="action-btn view">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="editPreperation.php?id=<?= $prep->getIdEtape() ?>" class="action-btn edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <button onclick="confirmDelete(<?= $prep->getIdEtape() ?>)" class="action-btn delete">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 80px;">
                                    <i class="fas fa-empty-folder" style="font-size: 80px; color: #ccc;"></i>
                                    <h3 style="margin-top: 20px;">Aucune étape trouvée</h3>
                                    <p>Commencez par ajouter votre première étape de préparation !</p>
                                    <a href="addPreperation.php" class="add-btn" style="display: inline-block; margin-top: 15px;">
                                        <i class="fas fa-plus-circle"></i> Ajouter une étape
                                    </a>
                                 </td>
                             </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

     <div class="footer">
    <div class="pagination">
        <button class="page-btn" onclick="changePage(-1)"><i class="fas fa-chevron-left"></i> Précédent</button>
        <span id="pageInfo" style="padding: 10px 20px; background: white; border-radius: 10px; font-weight: 500;">Page 1</span>
        <button class="page-btn" onclick="changePage(1)">Suivant <i class="fas fa-chevron-right"></i></button>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="recetteList.php" style="background: #6c757d; color: white; padding: 10px 22px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Retour aux recettes
        </a>
        <button class="export-btn" onclick="exportToCSV()">
            <i class="fas fa-download"></i> Exporter CSV
        </button>
    </div>
</div>

    <script>
        let currentPage = 1;
        let rowsPerPage = 10;
        let allRows = [];
        
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('tableBody');
            allRows = Array.from(tbody.querySelectorAll('.etape-row'));
            updatePagination();
        });
        
        function updatePagination() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            
            allRows.forEach(row => row.style.display = 'none');
            
            for(let i = start; i < end && i < allRows.length; i++) {
                if(allRows[i]) allRows[i].style.display = '';
            }
            
            const totalPages = Math.ceil(allRows.length / rowsPerPage);
            document.getElementById('pageInfo').innerHTML = `Page ${currentPage} / ${totalPages || 1} <span style="color:#999;">(${allRows.length} étapes)</span>`;
        }
        
       function changePage(direction) {
    // Si on clique sur Précédent (direction = -1) et qu'on est à la page 1
    if (direction === -1 && currentPage === 1) {
        // Rediriger vers la liste des recettes
        window.location.href = 'recetteList.php';
        return;
    }
    
    const totalPages = Math.ceil(allRows.length / rowsPerPage);
    let newPage = currentPage + direction;
    if(newPage < 1) newPage = 1;
    if(newPage > totalPages) newPage = totalPages;
    if(newPage !== currentPage) {
        currentPage = newPage;
        updatePagination();
    }
}
        
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            
            const tbody = document.getElementById('tableBody');
            allRows = Array.from(tbody.querySelectorAll('.etape-row'));
            
            allRows.forEach(row => {
                const recetteCell = row.cells[1];
                const instructionCell = row.cells[3];
                
                let recetteText = recetteCell ? recetteCell.textContent.toLowerCase() : '';
                let instructionText = instructionCell ? instructionCell.textContent.toLowerCase() : '';
                
                if(filter === "" || recetteText.indexOf(filter) > -1 || instructionText.indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            allRows = Array.from(tbody.querySelectorAll('.etape-row')).filter(row => row.style.display !== 'none');
            if(allRows.length === 0) {
                allRows = Array.from(tbody.querySelectorAll('.etape-row'));
            }
            currentPage = 1;
            updatePagination();
        }
        
        function confirmDelete(id) {
            if(confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette étape ?\n\nCette action est irréversible.')) {
                window.location.href = 'deletePreperation.php?id=' + id + '&confirm=yes';
            }
        }
        
        function exportToCSV() {
            let csv = [];
            let headers = ['ID', 'Recette', 'Ordre', 'Instruction', 'Durée(min)', 'Température(°C)', 'Type Action', 'Outil', 'Quantité', 'Astuce'];
            csv.push(headers.join(','));
            
            const visibleRows = Array.from(document.querySelectorAll('#tableBody .etape-row')).filter(row => row.style.display !== 'none');
            visibleRows.forEach(row => {
                let rowData = [];
                for(let i = 0; i < row.cells.length - 1; i++) {
                    let text = row.cells[i].innerText.replace(/,/g, ';');
                    rowData.push('"' + text + '"');
                }
                csv.push(rowData.join(','));
            });
            
            const blob = new Blob([csv.join('\n')], {type: 'text/csv'});
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'preparations_export.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        }
    </script>
</body>
</html>