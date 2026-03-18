<?php
include '../../controleurs/CategoryController.php';
require_once __DIR__ . '/../../models/Category.php';
$CategoryController = new CategoryController();
$categories = $CategoryController->listCategories(); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Catégories</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            font-size: 2.2em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header h1 i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .add-btn {
            background: white;
            color: #667eea;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            background: #f8f9fa;
        }

        .stats-bar {
            background: #f8f9fa;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 3px solid #667eea;
        }

        .stats-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-item {
            background: white;
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-item i {
            color: #667eea;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 30px;
            width: 250px;
            font-size: 0.9em;
            transition: border-color 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-box button {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-box button:hover {
            background: #5a67d8;
        }

        .content {
            padding: 30px;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            min-width: 800px;
        }

        thead tr {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }

        th i {
            margin-right: 8px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #333;
        }

        tbody tr {
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .category-id {
            font-weight: 600;
            color: #667eea;
        }

        .category-title {
            font-weight: 600;
            color: #333;
        }

        .category-description {
            color: #666;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            color: white;
        }

        .action-btn.view {
            background: #17a2b8;
        }

        .action-btn.edit {
            background: #ffc107;
        }

        .action-btn.delete {
            background: #dc3545;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        .action-btn.view:hover {
            background: #138496;
        }

        .action-btn.edit:hover {
            background: #e0a800;
        }

        .action-btn.delete:hover {
            background: #c82333;
        }

        .empty-message {
            text-align: center;
            padding: 50px !important;
            color: #666;
        }

        .empty-message i {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 15px;
            display: block;
        }

        .footer {
            padding: 20px 30px;
            border-top: 2px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            padding: 8px 15px;
            border: 1px solid #e0e0e0;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .page-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .page-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .export-btn {
            padding: 8px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .export-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-bar {
                flex-direction: column;
            }
            
            .stats-info {
                justify-content: center;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .footer {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-folder-open"></i>
                Gestion des Catégories
            </h1>
            <a href="../FrontOffice/addCategory.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter une catégorie
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-tags"></i>
                    <span>Total: <strong><?= count($categories) ?></strong> catégories</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-book"></i>
                    <span>avec livres: <strong>0</strong></span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher une catégorie..." onkeyup="searchTable()">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="categoriesTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-heading"></i> Titre</th>
                            <th><i class="fas fa-align-left"></i> Description</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0): ?> 
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="category-id">#<?= htmlspecialchars($category->getId()) ?></td>
                                    <td class="category-title"><?= htmlspecialchars($category->getTitle()) ?></td>
                                    <td class="category-description" title="<?= htmlspecialchars($category->getDescription()) ?>">
                                        <?= htmlspecialchars($category->getDescription()) ?: 'Aucune description' ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="../BackOffice/bookList.php?id=<?= $category->getId() ?>" class="action-btn view" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editCategory.php?id=<?= $category->getId() ?>" class="action-btn edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="deleteCategory.php?id=<?= $category->getId() ?>" class="action-btn delete" title="Supprimer" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?> 
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-message">
                                    <i class="fas fa-empty-folder"></i>
                                    <h3>Aucune catégorie trouvée</h3>
                                    <p>Commencez par ajouter votre première catégorie !</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <div class="pagination">
                <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button class="export-btn" onclick="exportTable()">
                <i class="fas fa-download"></i>
                Exporter
            </button>
        </div>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('categoriesTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) { // Exclure la dernière colonne (Actions)
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
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

        function exportTable() {
            alert('Fonctionnalité d\'export à venir !');
        }

        // Animation des lignes
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animation = `slideIn 0.3s ease-out ${index * 0.03}s both`;
            });
        });
    </script>
</body>
</html>