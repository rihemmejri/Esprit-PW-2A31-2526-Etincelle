<?php
include_once '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/repas.php';

$RepasController = new RepasController();
$repas = $RepasController->listRepas();

// Calculer les totaux
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Repas - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
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

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #003366;
            color: white;
        }

        .btn-secondary:hover {
            background: #002244;
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

        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            cursor: pointer;
        }

        th.sortable {
            cursor: pointer;
            transition: background 0.2s;
        }
        th.sortable:hover {
            background: #2a2a4e;
        }
        th.sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-left: 8px;
            opacity: 0.5;
        }
        th.sortable.asc::after { content: '\f0de'; opacity: 1; color: #4CAF50; }
        th.sortable.desc::after { content: '\f0dd'; opacity: 1; color: #4CAF50; }

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
            min-width: 900px;
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
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Badges */
        .badge-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .type-PETIT_DEJEUNER {
            background: #e3f2fd;
            color: #1565c0;
        }

        .type-DEJEUNER {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .type-DINER {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .type-COLLATION {
            background: #fff3e0;
            color: #e65100;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-utensils"></i>
                Gestion des Repas
            </h1>
            <div class="btn-group">
                <a href="addRepas.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter un repas
                </a>
                <a href="programmeList.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt"></i> Gérer les programmes
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-utensils"></i>
                    <span><strong><?= count($repas) ?></strong> repas</span>
                </div>
                <div class="stat">
                    <i class="fas fa-fire"></i>
                    <span><strong><?= $totalCalories ?></strong> kcal</span>
                </div>
                <div class="stat">
                    <i class="fas fa-dumbbell"></i>
                    <span><strong><?= round($totalProteines, 1) ?></strong> g protéines</span>
                </div>
            </div>
            <div class="search-box">
                <select id="filterType" class="filter-select" onchange="searchTable()">
                    <option value="">Tous les types</option>
                    <option value="petit déjeuner">Petit déjeuner</option>
                    <option value="déjeuner">Déjeuner</option>
                    <option value="dîner">Dîner</option>
                    <option value="collation">Collation</option>
                </select>
                <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table id="repasTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)"><i class="fas fa-hashtag"></i> ID</th>
                        <th class="sortable" onclick="sortTable(1)"><i class="fas fa-utensils"></i> Nom</th>
                        <th class="sortable" onclick="sortTable(2)"><i class="fas fa-mug-hot"></i> Type</th>
                        <th class="sortable" onclick="sortTable(3)"><i class="fas fa-fire"></i> Calories</th>
                        <th class="sortable" onclick="sortTable(4)"><i class="fas fa-dumbbell"></i> Protéines</th>
                        <th class="sortable" onclick="sortTable(5)"><i class="fas fa-bread-slice"></i> Glucides</th>
                        <th class="sortable" onclick="sortTable(6)"><i class="fas fa-oil-can"></i> Lipides</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($repas) > 0): ?>
                        <?php foreach ($repas as $item): ?>
                            <tr>
                                <td>#<?= $item->getIdRepas() ?></td>
                                <td><strong><?= htmlspecialchars($item->getNom()) ?></strong></td>
                                <td>
                                    <span class="badge-type type-<?= $item->getType() ?>">
                                        <?php
                                        switch($item->getType()) {
                                            case 'PETIT_DEJEUNER': echo '☕ Petit déjeuner'; break;
                                            case 'DEJEUNER': echo '🍽️ Déjeuner'; break;
                                            case 'DINER': echo '🌙 Dîner'; break;
                                            case 'COLLATION': echo '🍎 Collation'; break;
                                            default: echo $item->getType();
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><span class="badge-calories"><?= $item->getCalories() ?> kcal</span></td>
                                <td><?= $item->getProteines() ?> g</td>
                                <td><?= $item->getGlucides() ?> g</td>
                                <td><?= $item->getLipides() ?> g</td>
                                <td class="actions">
                                    <a href="editRepas.php?id=<?= $item->getIdRepas() ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $item->getIdRepas() ?>); return false;">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-message">
                                <i class="fas fa-empty-folder" style="font-size: 3rem;"></i>
                                <p>Aucun repas trouvé</p>
                                <a href="addRepas.php" class="btn btn-primary" style="margin-top: 10px;">Ajouter un repas</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (count($repas) > 0): ?>
                    <tfoot class="tfoot">
                        <tr>
                            <td colspan="3"><strong>Totaux :</strong></td>
                            <td><strong><?= $totalCalories ?> kcal</strong></td>
                            <td><strong><?= round($totalProteines, 1) ?> g</strong></td>
                            <td><strong><?= round($totalGlucides, 1) ?> g</strong></td>
                            <td><strong><?= round($totalLipides, 1) ?> g</strong></td>
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
            <div>
                <button class="export-btn" onclick="exportTable()" style="margin-right: 10px;">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button class="export-btn" style="background: #e53935;" onclick="exportPDF()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Supprimer ce repas ?')) {
                window.location.href = 'deleteRepas.php?id=' + id;
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filterText = input.value.toLowerCase();
            const filterType = document.getElementById('filterType').value.toLowerCase();
            const rows = document.querySelectorAll('#repasTable tbody tr');
            
            rows.forEach(row => {
                if (row.querySelector('.empty-message')) return;
                
                const text = row.textContent.toLowerCase();
                const typeText = row.children[2] ? row.children[2].textContent.toLowerCase() : '';
                
                const matchesText = text.includes(filterText);
                const matchesType = filterType === "" || typeText.includes(filterType);
                if (matchesText && matchesType) {
                    row.classList.remove('hidden-by-filter');
                } else {
                    row.classList.add('hidden-by-filter');
                }
            });
            showPage(1);
        }

        let sortDirection = false;
        function sortTable(columnIndex) {
            const table = document.getElementById('repasTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            if (rows.length === 0 || rows[0].querySelector('.empty-message')) return;

            sortDirection = !sortDirection;
            const multiplier = sortDirection ? 1 : -1;

            const allHeaders = table.querySelectorAll('th');
            table.querySelectorAll('th.sortable').forEach(th => th.classList.remove('asc', 'desc'));
            allHeaders[columnIndex].classList.add(sortDirection ? 'asc' : 'desc');

            rows.sort((a, b) => {
                if(a.querySelector('.tfoot') || b.querySelector('.tfoot')) return 0;
                
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();

                const aNum = parseFloat(aText.replace(/[^0-9.-]+/g,""));
                const bNum = parseFloat(bText.replace(/[^0-9.-]+/g,""));

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return (aNum - bNum) * multiplier;
                }

                return aText.localeCompare(bText) * multiplier;
            });

            rows.forEach(row => tbody.appendChild(row));
            showPage(1);
        }

        let currentPage = 1;
        const rowsPerPage = 10;

        function showPage(page) {
            const allRows = Array.from(document.querySelectorAll('#repasTable tbody tr:not(.tfoot)'));
            const visibleRows = allRows.filter(r => !r.classList.contains('hidden-by-filter') && !r.querySelector('.empty-message'));
            const totalPages = Math.ceil(visibleRows.length / rowsPerPage) || 1;
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            allRows.forEach(row => {
                if (!row.classList.contains('tfoot')) {
                    row.style.display = 'none';
                }
            });
            
            visibleRows.forEach((row, index) => {
                if (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) {
                    row.style.display = '';
                }
            });
            
            currentPage = page;
            document.querySelectorAll('.page-btn').forEach(btn => btn.style.display = 'none');
            for (let i = 1; i <= Math.min(totalPages, 3); i++) {
                const btn = document.getElementById('page' + i);
                if (btn) {
                    btn.style.display = 'inline-block';
                    btn.textContent = i;
                    btn.classList.toggle('active', i === page);
                }
            }
        }

        function previousPage() { showPage(currentPage - 1); }
        function nextPage() { showPage(currentPage + 1); }

        function exportTable() {
            let csv = "ID,Nom,Type,Calories,Proteines,Glucides,Lipides\n";
            <?php foreach ($repas as $item): ?>
                csv += `<?= $item->getIdRepas() ?>,<?= addslashes($item->getNom()) ?>,<?= $item->getType() ?>,<?= $item->getCalories() ?>,<?= $item->getProteines() ?>,<?= $item->getGlucides() ?>,<?= $item->getLipides() ?>\n`;
            <?php endforeach; ?>
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'repas_export.csv';
            a.click();
            URL.revokeObjectURL(url);
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.text("Liste des Repas Nutritionnels", 14, 15);
            
            const headers = [['ID', 'Nom', 'Type', 'Calories', 'Protéines', 'Glucides', 'Lipides']];
            const data = [];
            <?php foreach ($repas as $item): ?>
            data.push([
                "<?= $item->getIdRepas() ?>",
                "<?= addslashes(str_replace('"', '\"', $item->getNom())) ?>",
                "<?= $item->getType() ?>",
                "<?= $item->getCalories() ?> kcal",
                "<?= $item->getProteines() ?> g",
                "<?= $item->getGlucides() ?> g",
                "<?= $item->getLipides() ?> g"
            ]);
            <?php endforeach; ?>
            
            doc.autoTable({
                head: headers,
                body: data,
                startY: 20
            });
            doc.save('repas_export.pdf');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('#repasTable tbody tr');
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