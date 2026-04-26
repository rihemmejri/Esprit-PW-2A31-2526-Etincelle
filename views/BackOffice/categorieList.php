<?php
include '../../controleurs/CategorieController.php';
session_start();

$categorieController = new CategorieController();
$categories = $categorieController->listCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #003366;
            --success-green: #4CAF50;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); padding: 20px; }
        .container-list { max-width: 1300px; margin: 0 auto; }

        .success-bar {
            background: #e8f5e9; color: #2e7d32; padding: 15px 20px; border-radius: 12px;
            border-left: 5px solid var(--success-green); margin-bottom: 20px;
            display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-title { display: flex; align-items: center; gap: 15px; font-size: 1.8em; color: var(--text-dark); font-weight: 700; }
        .header-title i { color: var(--success-green); }

        .btn {
            padding: 10px 20px; border-radius: 10px; font-weight: 600; text-decoration: none;
            display: flex; align-items: center; gap: 8px; transition: 0.3s; border: none; cursor: pointer;
        }
        .btn-success { background: var(--success-green); color: white; }
        .btn-primary { background: var(--primary-blue); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

        .stats-bar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .stat-item { display: flex; align-items: center; gap: 10px; font-weight: 500; color: var(--text-gray); }
        .stat-item i { color: var(--success-green); }

        .table-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1a1c23; color: white; }
        th { padding: 18px 20px; text-align: left; font-size: 0.9em; text-transform: uppercase; }
        td { padding: 15px 20px; border-bottom: 1px solid #f5f5f5; font-size: 0.95em; }

        .cat-img { width: 45px; height: 45px; border-radius: 8px; object-fit: cover; border: 1px solid #eee; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; }
        .badge-aliment { background: #e3f2fd; color: #1565c0; }
        .badge-boisson { background: #f3e5f5; color: #7b1fa2; }
        .badge-autre { background: #f5f5f5; color: #616161; }

        .actions { display: flex; gap: 8px; }
        .action-btn { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; }
        .btn-edit { background: #4a90e2; }
        .btn-delete { background: #f44336; }
    </style>
</head>
<body>
    <div class="container-list">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-bar">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="header-section">
            <div class="header-title"><i class="fas fa-tags"></i> Gestion des Catégories</div>
            <div class="header-actions" style="display: flex; gap: 10px;">
                <a href="addCategorie.php" class="btn btn-success"><i class="fas fa-plus-circle"></i> Ajouter une catégorie</a>
                <a href="produitList.php" class="btn btn-primary"><i class="fas fa-box"></i> Retour aux Produits</a>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stat-item"><i class="fas fa-list"></i> <span><strong><?= count($categories) ?></strong> catégories totales</span></div>
            <div class="search-container" style="display: flex; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;">
                <input type="text" id="searchInput" placeholder="Rechercher..." style="padding: 8px 15px; border: none; outline: none;" onkeyup="searchTable()">
                <button style="background: var(--success-green); color: white; border: none; padding: 8px 15px;"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="table-card">
            <table id="categoriesTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-image"></i> Image</th>
                        <th><i class="fas fa-tag"></i> Nom</th>
                        <th><i class="fas fa-info-circle"></i> Type</th>
                        <th><i class="fas fa-align-left"></i> Description</th>
                        <th><i class="fas fa-calendar-alt"></i> Création</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>#<?= $cat->getIdCategorie() ?></td>
                            <td>
                                <?php if ($cat->getImageCategorie()): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($cat->getImageCategorie()) ?>" class="cat-img">
                                <?php else: ?>
                                    <div style="background: #f0f0f0; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2em;">📁</div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($cat->getNomCategorie()) ?></td>
                            <td><span class="badge badge-<?= $cat->getTypeCategorie() ?>"><?= ucfirst($cat->getTypeCategorie()) ?></span></td>
                            <td style="color: #666; font-size: 0.9em;"><?= htmlspecialchars(substr($cat->getDescription(), 0, 50)) ?>...</td>
                            <td><?= date('d/m/Y', strtotime($cat->getDateCreation())) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="editCategorie.php?id=<?= $cat->getIdCategorie() ?>" class="action-btn btn-edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="action-btn btn-delete" onclick="confirmDelete(<?= $cat->getIdCategorie() ?>); return false;"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Supprimer cette catégorie ? Les produits associés n\'auront plus de catégorie.')) {
                window.location.href = 'deleteCategorie.php?id=' + id;
            }
        }
        function searchTable() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let tr = document.querySelectorAll('#categoriesTable tbody tr');
            tr.forEach(row => {
                let name = row.cells[2].innerText.toLowerCase();
                row.style.display = name.includes(filter) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
