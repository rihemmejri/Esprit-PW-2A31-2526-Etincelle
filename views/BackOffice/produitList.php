<?php
session_start();
include '../../controleurs/ProduitController.php';
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();

// GET parameters for search, sort, and filters
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'nom';
$sortOrder = $_GET['sort_order'] ?? 'ASC';
$idCategorie = $_GET['id_categorie'] ?? '';
$origine = $_GET['origine'] ?? '';

// Fetch products with advanced search
$produits = $produitController->advancedSearch($search, $sortBy, $sortOrder, $idCategorie, $origine);

// Get stats
$stats = $produitController->getStats($produits);

// Get all categories for filter
$allCategories = $produitController->getCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - NutriLoop AI</title>
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
        .modal-content { background: #f8f9fa; width: 90%; max-width: 1100px; max-height: 90vh; border-radius: 24px; padding: 40px; position: relative; overflow-y: auto; transform: translateY(20px); transition: 0.3s; }
        .modal.show .modal-content { transform: translateY(0); }
        .close-modal { position: absolute; right: 25px; top: 20px; font-size: 2rem; cursor: pointer; color: #7f8c8d; }

        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; border: 1px solid rgba(0,0,0,0.03); }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; color: white; }
        .stat-icon.green { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
        .stat-icon.blue { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.orange { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .stat-icon.teal { background: linear-gradient(135deg, #009688, #00796B); }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); }
        .stat-label { font-size: 0.8rem; color: var(--text-gray); text-transform: uppercase; }

        .charts-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .chart-box { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        /* Filters Form */
        .filters-form { background: white; padding: 25px; border-radius: 16px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px; }
        .filter-group label { font-size: 0.85rem; font-weight: 700; color: var(--text-dark); text-transform: uppercase; }
        .filter-group input, .filter-group select { padding: 12px; border: 2px solid #eee; border-radius: 10px; font-size: 0.9rem; transition: 0.3s; background: #fdfdfd; }
        .filter-group input:focus, .filter-group select:focus { border-color: var(--success-green); outline: none; background: white; }
        .btn-filter { background: var(--success-green); color: white; padding: 12px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px; }
        .btn-reset { background: #f1f3f5; color: #495057; padding: 12px 25px; border-radius: 10px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px; }

        /* Table */
        .table-container { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; min-width: 1100px; }
        th { background: #1a1a2e; color: white; padding: 18px 15px; text-align: left; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }
        .product-img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; border: 2px solid #eee; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-local { background: #e8f5e9; color: #2e7d32; }
        .badge-importe { background: #fff3e0; color: #e65100; }
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
            <h1><i class="fas fa-carrot"></i> Gestion des Produits</h1>
            <div class="btn-group">
                <button id="openStatsBtn" class="btn btn-secondary"><i class="fas fa-chart-line"></i> Statistiques</button>
                <a href="addProduit.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Ajouter</a>
                <a href="categorieList.php" class="btn btn-secondary"><i class="fas fa-tags"></i> Catégories</a>
            </div>
        </div>

        <!-- Stats Modal -->
        <div id="statsModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div style="margin-bottom: 25px;">
                    <h2 style="color: var(--text-dark); display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-analytics" style="color: var(--success-green);"></i> Analyse des Produits
                    </h2>
                </div>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-box"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['total'] ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-truck"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['avgDistance'] ?> km</div>
                            <div class="stat-label">Dist. Moyenne</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-leaf"></i></div>
                        <div>
                            <div class="stat-value"><?= $stats['origineDistribution']['local'] ?></div>
                            <div class="stat-label">Produits Locaux</div>
                        </div>
                    </div>
                </div>

                <div class="charts-section">
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1rem;"><i class="fas fa-globe"></i> Répartition par Origine</h3>
                        <canvas id="origineChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3 style="margin-bottom: 15px; font-size: 1rem;"><i class="fas fa-calendar-alt"></i> Disponibilité par Saison</h3>
                        <canvas id="saisonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <form class="filters-form" method="GET">
            <div class="filter-group">
                <label>Recherche</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nom, origine...">
            </div>
            <div class="filter-group">
                <label>Catégorie</label>
                <select name="id_categorie">
                    <option value="">Toutes</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>" <?= $idCategorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Origine</label>
                <select name="origine">
                    <option value="">Toutes</option>
                    <option value="local" <?= $origine == 'local' ? 'selected' : '' ?>>Local</option>
                    <option value="importe" <?= $origine == 'importe' ? 'selected' : '' ?>>Importé</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Trier par</label>
                <select name="sort_by">
                    <option value="nom" <?= $sortBy == 'nom' ? 'selected' : '' ?>>Nom</option>
                    <option value="distance_transport" <?= $sortBy == 'distance_transport' ? 'selected' : '' ?>>Distance</option>
                    <option value="saison" <?= $sortBy == 'saison' ? 'selected' : '' ?>>Saison</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="produitList.php" class="btn-reset"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>

        <div class="table-container">
            <table id="produitsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Image</th>
                        <th>Origine</th>
                        <th>Distance</th>
                        <th>Saison</th>
                        <th>Emballage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produits) > 0): ?>
                        <?php foreach ($produits as $p): ?>
                            <?php $isHighlighted = !empty($search) && (stripos($p->getNom(), $search) !== false) ? 'highlighted-row' : ''; ?>
                            <tr class="<?= $isHighlighted ?>">
                                <td style="font-weight: 700; color: #999;">#<?= $p->getIdProduit() ?></td>
                                <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($p->getNom()) ?></td>
                                <td>
                                    <?php if ($p->getImage()): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($p->getImage()) ?>" class="product-img">
                                    <?php else: ?>
                                        <div style="background: #f0f0f0; width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">🍎</div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-<?= strtolower($p->getOrigine()) ?>"><?= ucfirst($p->getOrigine()) ?></span></td>
                                <td><?= htmlspecialchars($p->getDistanceTransport()) ?> km</td>
                                <td><?= ucfirst(htmlspecialchars($p->getSaison())) ?></td>
                                <td><?= htmlspecialchars($p->getEmballage()) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="editProduit.php?id=<?= $p->getIdProduit() ?>" class="action-btn btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="action-btn btn-delete" title="Supprimer" onclick="confirmDelete(<?= $p->getIdProduit() ?>); return false;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align: center; padding: 50px; color: #999;"><i class="fas fa-box-open" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i> Aucun produit trouvé</td></tr>
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

        btn.onclick = () => { modal.style.display = "flex"; setTimeout(() => modal.classList.add('show'), 10); renderCharts(); }
        span.onclick = () => { modal.classList.remove('show'); setTimeout(() => modal.style.display = "none", 300); }
        window.onclick = (e) => { if (e.target == modal) { modal.classList.remove('show'); setTimeout(() => modal.style.display = "none", 300); } }

        function renderCharts() {
            // Origine Chart
            const ctxOrig = document.getElementById('origineChart').getContext('2d');
            new Chart(ctxOrig, {
                type: 'pie',
                data: {
                    labels: ['Local', 'Importé'],
                    datasets: [{
                        data: [<?= $stats['origineDistribution']['local'] ?>, <?= $stats['origineDistribution']['importe'] ?>],
                        backgroundColor: ['#4CAF50', '#FF9800'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            // Saison Chart
            const ctxSaison = document.getElementById('saisonChart').getContext('2d');
            new Chart(ctxSaison, {
                type: 'bar',
                data: {
                    labels: ['Printemps', 'Été', 'Automne', 'Hiver', 'Année'],
                    datasets: [{
                        label: 'Nombre de produits',
                        data: [
                            <?= $stats['saisonDistribution']['printemps'] ?>, 
                            <?= $stats['saisonDistribution']['ete'] ?>, 
                            <?= $stats['saisonDistribution']['automne'] ?>, 
                            <?= $stats['saisonDistribution']['hiver'] ?>,
                            <?= $stats['saisonDistribution']['toute l\'année'] ?>
                        ],
                        backgroundColor: '#2196F3',
                        borderRadius: 8
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        }

        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                window.location.href = 'deleteProduit.php?id=' + id;
            }
        }

        function exportToExcel() {
            const table = document.getElementById("produitsTable");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Produits" });
            XLSX.writeFile(wb, "Produits_NutriLoop.xlsx");
        }

        function exportToPDF() {
            const element = document.getElementById('produitsTable');
            const opt = { margin: 10, filename: 'Produits_NutriLoop.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' } };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
>
</html>
