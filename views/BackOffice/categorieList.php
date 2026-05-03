<?php
session_start();
include '../../controleurs/CategorieController.php';
require_once __DIR__ . '/../../models/categorie.php';

$categorieController = new CategorieController();

// GET parameters for search and sort
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'date_creation';
$sortOrder = $_GET['sort_order'] ?? 'DESC';

// Fetch categories with advanced search
$categories = $categorieController->advancedSearch($search, $sortBy, $sortOrder);

// Get stats
$stats = $categorieController->getStats($categories);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary-blue: #003366;
            --success-green: #4CAF50;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
            --accent-purple: #9C27B0;
            --accent-orange: #FF9800;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header h1 { font-size: 1.8rem; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }
        .header h1 i { color: var(--success-green); }

        .btn-group { display: flex; gap: 12px; }
        .btn { padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; }
        .btn-primary { background: var(--success-green); color: white; }
        .btn-secondary { background: var(--primary-blue); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

        /* Notification */
        .notification-success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid var(--success-green); display: flex; align-items: center; gap: 12px; }

        /* Stats Modal */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); align-items: center; justify-content: center; opacity: 0; transition: 0.3s; }
        .modal.show { display: flex; opacity: 1; }
        .modal-content { background: #f8f9fa; width: 90%; max-width: 1000px; max-height: 90vh; border-radius: 24px; padding: 40px; position: relative; overflow-y: auto; transform: translateY(20px); transition: 0.3s; }
        .modal.show .modal-content { transform: translateY(0); }
        .close-modal { position: absolute; right: 25px; top: 20px; font-size: 2rem; cursor: pointer; color: #7f8c8d; }

        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; border: 1px solid rgba(0,0,0,0.03); }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; color: white; }
        .stat-icon.green { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
        .stat-icon.blue { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.purple { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); }
        .stat-label { font-size: 0.8rem; color: var(--text-gray); text-transform: uppercase; }

        .charts-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .chart-box { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        /* Filters Form */
        .filters-form { background: white; padding: 25px; border-radius: 16px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 200px; }
        .filter-group label { font-size: 0.85rem; font-weight: 700; color: var(--text-dark); text-transform: uppercase; }
        .filter-group input, .filter-group select { padding: 12px; border: 2px solid #eee; border-radius: 10px; font-size: 0.9rem; transition: 0.3s; background: #fdfdfd; }
        .filter-group input:focus, .filter-group select:focus { border-color: var(--success-green); outline: none; background: white; }
        .btn-filter { background: var(--success-green); color: white; padding: 12px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px; }
        .btn-reset { background: #f1f3f5; color: #495057; padding: 12px 25px; border-radius: 10px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px; }

        /* Table */
        .table-container { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        th { background: #1a1a2e; color: white; padding: 18px 15px; text-align: left; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }
        .cat-img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; border: 2px solid #eee; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-aliment { background: #e3f2fd; color: #1565c0; }
        .badge-boisson { background: #f3e5f5; color: #7b1fa2; }
        .badge-autre { background: #f5f5f5; color: #616161; }
        .highlighted-row { background-color: #fff9c4 !important; border-left: 4px solid var(--accent-orange); }

        .actions { display: flex; gap: 8px; }
        .action-btn { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: 0.2s; }
        .btn-edit { background: #2196F3; }
        .btn-delete { background: #f44336; }
        .action-btn:hover { transform: scale(1.1); filter: brightness(1.1); }

        /* Footer */
        .footer { margin-top: 25px; display: flex; justify-content: space-between; align-items: center; }
        .pagination { display: flex; gap: 8px; }
        .page-btn { width: 40px; height: 40px; border: 1px solid #ddd; background: white; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
        .page-btn.active { background: var(--success-green); color: white; border-color: var(--success-green); }
        .page-btn:hover:not(.active) { background: #f0f0f0; }

        @media (max-width: 992px) {
            .charts-section { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="header">
            <h1><i class="fas fa-tags"></i> Gestion des Catégories</h1>
            <div class="btn-group">
                <button id="openStatsBtn" class="btn btn-secondary"><i class="fas fa-chart-pie"></i> Statistiques</button>
                <a href="addCategorie.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Ajouter</a>
                <a href="produitList.php" class="btn btn-secondary"><i class="fas fa-box"></i> Produits</a>
            </div>
        </div>

        <!-- Stats Modal -->
        <div id="statsModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div style="margin-bottom: 25px;">
                    <h2 style="color: var(--text-dark); display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-chart-bar" style="color: var(--success-green);"></i> Analyse des Catégories
                    </h2>
                </div>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-list-ul"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['total'] ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-utensils"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['distribution']['aliment'] ?></div>
                            <div class="stat-label">Aliments</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fas fa-glass-martini-alt"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['distribution']['boisson'] ?></div>
                            <div class="stat-label">Boissons</div>
                        </div>
                    </div>
                </div>

                <div class="charts-section">
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1rem;"><i class="fas fa-chart-pie"></i> Répartition par Type</h3>
                        <canvas id="typeChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1rem;"><i class="fas fa-history"></i> Dernières Ajouts</h3>
                        <ul style="list-style: none;">
                            <?php foreach ($stats['recent'] as $rc): ?>
                                <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                                    <span><?= htmlspecialchars($rc->getNomCategorie()) ?></span>
                                    <span style="color: #999; font-size: 0.8rem;"><?= date('d/m/Y', strtotime($rc->getDateCreation())) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form class="filters-form" method="GET">
            <div class="filter-group">
                <label>Recherche</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nom, description...">
            </div>
            <div class="filter-group">
                <label>Trier par</label>
                <select name="sort_by">
                    <option value="nom_categorie" <?= $sortBy == 'nom_categorie' ? 'selected' : '' ?>>Nom</option>
                    <option value="type_categorie" <?= $sortBy == 'type_categorie' ? 'selected' : '' ?>>Type</option>
                    <option value="date_creation" <?= $sortBy == 'date_creation' ? 'selected' : '' ?>>Date de création</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Ordre</label>
                <select name="sort_order">
                    <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Croissant</option>
                    <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Décroissant</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="categorieList.php" class="btn-reset"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>

        <div class="table-container">
            <table id="categoriesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $cat): ?>
                            <?php $isHighlighted = !empty($search) && (stripos($cat->getNomCategorie(), $search) !== false) ? 'highlighted-row' : ''; ?>
                            <tr class="<?= $isHighlighted ?>">
                                <td style="font-weight: 700; color: #999;">#<?= $cat->getIdCategorie() ?></td>
                                <td>
                                    <?php if ($cat->getImageCategorie()): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($cat->getImageCategorie()) ?>" class="cat-img">
                                    <?php else: ?>
                                        <div style="background: #f0f0f0; width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">📁</div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($cat->getNomCategorie()) ?></td>
                                <td><span class="badge badge-<?= strtolower($cat->getTypeCategorie()) ?>"><?= ucfirst($cat->getTypeCategorie()) ?></span></td>
                                <td style="color: var(--text-gray); font-size: 0.9rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($cat->getDescription()) ?></td>
                                <td><?= date('d/m/Y', strtotime($cat->getDateCreation())) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="editCategorie.php?id=<?= $cat->getIdCategorie() ?>" class="action-btn btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="action-btn btn-delete" title="Supprimer" onclick="confirmDelete(<?= $cat->getIdCategorie() ?>); return false;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 50px; color: #999;"><i class="fas fa-folder-open" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i> Aucune catégorie trouvée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div class="pagination">
                <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="btn-group">
                <button onclick="exportToExcel()" class="btn" style="background: #2e7d32; color: white;"><i class="fas fa-file-excel"></i> Excel</button>
                <button onclick="exportToPDF()" class="btn" style="background: #c62828; color: white;"><i class="fas fa-file-pdf"></i> PDF</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        const modal = document.getElementById("statsModal");
        const btn = document.getElementById("openStatsBtn");
        const span = document.querySelector(".close-modal");

        btn.onclick = () => { modal.style.display = "flex"; setTimeout(() => modal.classList.add('show'), 10); renderChart(); }
        span.onclick = () => { modal.classList.remove('show'); setTimeout(() => modal.style.display = "none", 300); }
        window.onclick = (e) => { if (e.target == modal) { modal.classList.remove('show'); setTimeout(() => modal.style.display = "none", 300); } }

        function renderChart() {
            const ctx = document.getElementById('typeChart').getContext('2d');
            if (window.myChart) window.myChart.destroy();
            window.myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Aliments', 'Boissons', 'Autres'],
                    datasets: [{
                        data: [<?= $stats['distribution']['aliment'] ?>, <?= $stats['distribution']['boisson'] ?>, <?= $stats['distribution']['autre'] ?>],
                        backgroundColor: ['#4CAF50', '#2196F3', '#9E9E9E'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
            });
        }

        function confirmDelete(id) {
            if (confirm('Supprimer cette catégorie ? Les produits associés n\'auront plus de catégorie.')) {
                window.location.href = 'deleteCategorie.php?id=' + id;
            }
        }

        function exportToExcel() {
            const table = document.getElementById("categoriesTable");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Categories" });
            XLSX.writeFile(wb, "Categories_NutriLoop.xlsx");
        }

        function exportToPDF() {
            const element = document.getElementById('categoriesTable');
            const opt = { margin: 10, filename: 'Categories_NutriLoop.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' } };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
