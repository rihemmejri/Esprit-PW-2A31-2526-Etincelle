<?php
include_once '../../controleurs/ProgrammeController.php';
include_once '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/programme.php';
require_once __DIR__ . '/../../models/repas.php';

$programmeController = new ProgrammeController();
$repasController = new RepasController();

$programmes = $programmeController->listProgrammes();

// Calculer les statistiques
$totalProgrammes = count($programmes);
$totalRepasProgrammes = 0;
$totalCaloriesProgrammes = 0;

foreach ($programmes as $p) {
    $repasDuProgramme = $p->getRepas();
    $totalRepasProgrammes += count($repasDuProgramme);
    foreach ($repasDuProgramme as $r) {
        $totalCaloriesProgrammes += $r['calories'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Programmes - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
        }

        .header h1 i {
            color: #4CAF50;
            margin-right: 10px;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        /* Stats Bar */
        .stats-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stats {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat i {
            font-size: 1.2rem;
            color: #4CAF50;
        }

        .search-box {
            display: flex;
            gap: 5px;
        }

        .search-box input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
        }

        .search-box button {
            background: #4CAF50;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th {
            background: #1a1a2e;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
        }

        th i {
            margin-right: 8px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Badges */
        .badge-objectif {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .objectif-PERDRE_POIDS {
            background: #ffebee;
            color: #c62828;
        }

        .objectif-PRENDRE_MUSCLE {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .objectif-MAINTENIR {
            background: #e3f2fd;
            color: #1565c0;
        }

        .objectif-EQUILIBRE {
            background: #fff3e0;
            color: #e65100;
        }

        .badge-duree {
            background: #f0f0f0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .repas-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            max-width: 250px;
        }

        .repas-mini {
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .repas-mini i {
            font-size: 0.6rem;
            color: #4CAF50;
        }

        .badge-calories {
            background: #fff3e0;
            color: #e65100;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.2s;
        }

        .edit-btn {
            background: #2196F3;
            color: white;
        }

        .edit-btn:hover {
            background: #1976D2;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .view-btn {
            background: #4CAF50;
            color: white;
        }

        .view-btn:hover {
            background: #45a049;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pagination {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
        }

        .page-btn.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .export-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .empty-message {
            text-align: center;
            padding: 60px;
            color: #999;
        }

        .tfoot {
            background: #f8f9fa;
            font-weight: bold;
        }

        .tfoot td {
            padding: 15px 12px;
            border-top: 2px solid #ddd;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-calendar-alt"></i>
                Gestion des Programmes
            </h1>
            <a href="addProgramme.php" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nouveau programme
            </a>
        </div>

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-calendar-alt"></i>
                    <span><strong><?= $totalProgrammes ?></strong> programmes</span>
                </div>
                <div class="stat">
                    <i class="fas fa-utensils"></i>
                    <span><strong><?= $totalRepasProgrammes ?></strong> repas programmés</span>
                </div>
                <div class="stat">
                    <i class="fas fa-fire"></i>
                    <span><strong><?= $totalCaloriesProgrammes ?></strong> kcal total</span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table id="programmeTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user"></i> Utilisateur</th>
                        <th><i class="fas fa-bullseye"></i> Objectif</th>
                        <th><i class="fas fa-calendar-alt"></i> Date début</th>
                        <th><i class="fas fa-calendar-check"></i> Date fin</th>
                        <th><i class="fas fa-clock"></i> Durée</th>
                        <th><i class="fas fa-utensils"></i> Repas</th>
                        <th><i class="fas fa-fire"></i> Calories</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalProgrammes > 0): ?>
                        <?php foreach ($programmes as $programme): 
                            $repasListe = $programme->getRepas();
                            $nbRepas = count($repasListe);
                            $caloriesTotal = 0;
                            foreach ($repasListe as $r) {
                                $caloriesTotal += $r['calories'] ?? 0;
                            }
                            
                            // Calculer la durée
                            $dateDebut = new DateTime($programme->getDateDebut());
                            $dateFin = new DateTime($programme->getDateFin());
                            $duree = $dateDebut->diff($dateFin)->days + 1;
                            
                            // Classe CSS pour l'objectif
                            $objectifClass = '';
                            $objectifIcon = '';
                            switch($programme->getObjectif()) {
                                case 'PERDRE_POIDS':
                                    $objectifClass = 'objectif-PERDRE_POIDS';
                                    $objectifIcon = '🔥';
                                    break;
                                case 'PRENDRE_MUSCLE':
                                    $objectifClass = 'objectif-PRENDRE_MUSCLE';
                                    $objectifIcon = '💪';
                                    break;
                                case 'MAINTENIR':
                                    $objectifClass = 'objectif-MAINTENIR';
                                    $objectifIcon = '⚖️';
                                    break;
                                case 'EQUILIBRE':
                                    $objectifClass = 'objectif-EQUILIBRE';
                                    $objectifIcon = '🥗';
                                    break;
                                default:
                                    $objectifClass = '';
                                    $objectifIcon = '🎯';
                            }
                        ?>
                            <tr>
                                <td class="text-center"><strong>#<?= $programme->getIdProgramme() ?></strong></td>
                                <td class="text-center">ID: <?= $programme->getIdUser() ?></td>
                                <td>
                                    <span class="badge-objectif <?= $objectifClass ?>">
                                        <?= $objectifIcon ?> 
                                        <?php 
                                        switch($programme->getObjectif()) {
                                            case 'PERDRE_POIDS': echo 'Perdre du poids'; break;
                                            case 'PRENDRE_MUSCLE': echo 'Prendre du muscle'; break;
                                            case 'MAINTENIR': echo 'Maintenir'; break;
                                            case 'EQUILIBRE': echo 'Équilibre'; break;
                                            default: echo $programme->getObjectif();
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?= $programme->getDateDebut() ?></td>
                                <td><?= $programme->getDateFin() ?></td>
                                <td>
                                    <span class="badge-duree">
                                        <i class="fas fa-calendar-week"></i> <?= $duree ?> jours
                                    </span>
                                </td>
                                <td>
                                    <div class="repas-preview">
                                        <span class="badge-duree" style="background:#4CAF50; color:white;">
                                            <i class="fas fa-utensils"></i> <?= $nbRepas ?> repas
                                        </span>
                                        <?php 
                                        $premiersRepas = array_slice($repasListe, 0, 3);
                                        foreach ($premiersRepas as $item): 
                                        ?>
                                            <span class="repas-mini">
                                                <i class="fas fa-utensils"></i>
                                                <?= htmlspecialchars(substr($item['nom'] ?? 'Repas', 0, 12)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if ($nbRepas > 3): ?>
                                            <span class="repas-mini">
                                                <i class="fas fa-ellipsis-h"></i> +<?= $nbRepas - 3 ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-calories">
                                        <i class="fas fa-fire"></i> <?= $caloriesTotal ?> kcal
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="editProgramme.php?id=<?= $programme->getIdProgramme() ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $programme->getIdProgramme() ?>); return false;">
                                        <i class="fas fa-trash"></i> Suppr
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="empty-message">
                                <i class="fas fa-empty-folder" style="font-size: 3rem;"></i>
                                <p>Aucun programme trouvé</p>
                                <a href="addProgramme.php" class="btn-primary" style="margin-top: 10px;">Créer un programme</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if ($totalProgrammes > 0): ?>
                    <tfoot class="tfoot">
                        <tr>
                            <td colspan="6"><strong>Totaux :</strong></td>
                            <td><strong><?= $totalRepasProgrammes ?> repas</strong></td>
                            <td><strong><?= $totalCaloriesProgrammes ?> kcal</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <!-- Footer -->
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
        function confirmDelete(id) {
            if (confirm('Supprimer ce programme ? Cette action est irréversible.')) {
                window.location.href = 'deleteProgramme.php?id=' + id;
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#programmeTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        let currentPage = 1;
        const rowsPerPage = 10;

        function showPage(page) {
            const rows = document.querySelectorAll('#programmeTable tbody tr');
            const totalRows = rows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            rows.forEach((row, index) => {
                row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? '' : 'none';
            });
            
            currentPage = page;
            document.querySelectorAll('.page-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('page' + page)?.classList.add('active');
        }

        function previousPage() { showPage(currentPage - 1); }
        function nextPage() { showPage(currentPage + 1); }

        function exportTable() {
            let csv = "ID,Utilisateur,Objectif,Date debut,Date fin,Duree,Nb repas,Calories\n";
            <?php foreach ($programmes as $p): 
                $repasListe = $p->getRepas();
                $caloriesTotal = 0;
                foreach ($repasListe as $r) $caloriesTotal += $r['calories'] ?? 0;
                $dateDebut = new DateTime($p->getDateDebut());
                $dateFin = new DateTime($p->getDateFin());
                $duree = $dateDebut->diff($dateFin)->days + 1;
            ?>
                csv += `<?= $p->getIdProgramme() ?>,<?= $p->getIdUser() ?>,<?= $p->getObjectif() ?>,<?= $p->getDateDebut() ?>,<?= $p->getDateFin() ?>,<?= $duree ?> jours,<?= count($repasListe) ?>,<?= $caloriesTotal ?>\n`;
            <?php endforeach; ?>
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'programmes_export.csv';
            a.click();
            URL.revokeObjectURL(url);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('#programmeTable tbody tr');
            if (rows.length > 0) {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                for (let i = 1; i <= Math.min(totalPages, 3); i++) {
                    const btn = document.getElementById('page' + i);
                    if (btn) {
                        btn.style.display = 'inline-block';
                        btn.textContent = i;
                        btn.onclick = () => showPage(i);
                    }
                }
                showPage(1);
            }
        });
    </script>
</body>
</html>