<?php
include '../../controleurs/BookController.php';
include '../../controleurs/CategoryController.php';

$bookController = new BookController();
$categoryController = new CategoryController();

$category_id = $_GET['id'] ?? null;

if (!$category_id) {
    header('Location: categoryList.php');
    exit;
}

// Récupérer les infos de la catégorie
$category = $categoryController->getCategoryById($category_id);

if (!$category) {
    header('Location: categoryList.php');
    exit;
}

// Récupérer les livres de cette catégorie
$books = $bookController->getBooksByCategory($category_id);

// Récupérer toutes les catégories pour les noms
$categories = $categoryController->listCategories();
$categoriesMap = [];
foreach ($categories as $cat) {
    $categoriesMap[$cat->getId()] = $cat->getTitle();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégorie : <?= htmlspecialchars($category->getTitle()) ?></title>
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
            max-width: 1300px;
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
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .header i {
            margin-right: 10px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .category-info {
            background: #f8f9fa;
            padding: 25px;
            border-bottom: 3px solid #667eea;
        }

        .info-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-item {
            flex: 1;
            min-width: 200px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .info-item:hover {
            transform: translateY(-5px);
        }

        .info-item i {
            font-size: 2em;
            color: #667eea;
            margin-bottom: 10px;
        }

        .info-item .label {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-item .value {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }

        .content {
            padding: 30px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .book-count {
            background: #e8f4fd;
            color: #1976d2;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: bold;
        }

        .book-count i {
            margin-right: 8px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
            font-size: 0.9em;
            letter-spacing: 1px;
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

        .book-title {
            font-weight: bold;
            color: #333;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-block;
            text-transform: capitalize;
        }

        .status-disponible {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .status-emprunté {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-perdu {
            background: #ffebee;
            color: #c62828;
        }

        .action-btn {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s;
            display: inline-block;
        }

        .action-btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .action-btn i {
            margin-right: 5px;
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

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 25px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            border: 2px solid #667eea;
            transition: all 0.3s;
        }

        .nav-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .nav-btn i {
            margin: 0 8px;
        }

        .nav-btn.primary {
            background: #667eea;
            color: white;
        }

        .nav-btn.primary:hover {
            background: #5a67d8;
            border-color: #5a67d8;
        }

        @media (max-width: 768px) {
            .info-card {
                flex-direction: column;
            }
            
            .stats {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            .navigation {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-folder-open"></i>
                <?= htmlspecialchars($category->getTitle()) ?>
            </h1>
        </div>

        <div class="category-info">
            <div class="info-card">
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <div class="label">Description</div>
                    <div class="value"><?= htmlspecialchars($category->getDescription()) ?: 'Aucune description' ?></div>
                </div>
                <div class="info-item">
                    <i class="fas fa-books"></i>
                    <div class="label">Nombre de livres</div>
                    <div class="value"><?= count($books) ?></div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="stats">
                <div class="book-count">
                    <i class="fas fa-book"></i>
                    <?= count($books) ?> livre(s) dans cette catégorie
                </div>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Rechercher dans cette catégorie..." onkeyup="searchTable()">
                    <button onclick="searchTable()"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <?php if (count($books) > 0): ?>
                <table id="booksTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-heading"></i> Titre</th>
                            <th><i class="fas fa-user"></i> Auteur</th>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-language"></i> Langue</th>
                            <th><i class="fas fa-circle"></i> Statut</th>
                            <th><i class="fas fa-copy"></i> Exemplaires</th>
                            <th><i class="fas fa-cog"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($book->getId()) ?></td>
                                <td class="book-title"><?= htmlspecialchars($book->getTitre()) ?></td>
                                <td><?= htmlspecialchars($book->getAuteur()) ?></td>
                                <td><?= htmlspecialchars($book->getPublicationDate()) ?: 'Non spécifiée' ?></td>
                                <td><?= htmlspecialchars($book->getLanguage()) ?: 'Non spécifiée' ?></td>
                                <td>
                                    <?php 
                                    $status = $book->getStatus();
                                    $statusClass = '';
                                    $statusIcon = '';
                                    
                                    switch($status) {
                                        case 'disponible':
                                            $statusClass = 'status-disponible';
                                            $statusIcon = 'fa-check-circle';
                                            break;
                                        case 'emprunté':
                                            $statusClass = 'status-emprunté';
                                            $statusIcon = 'fa-clock';
                                            break;
                                        case 'perdu':
                                            $statusClass = 'status-perdu';
                                            $statusIcon = 'fa-times-circle';
                                            break;
                                        default:
                                            $statusClass = '';
                                            $statusIcon = 'fa-circle';
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <i class="fas <?= $statusIcon ?>"></i>
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($book->getNumberOfCopies()) ?></strong>
                                </td>
                                <td>
                                    <a href="../FrontOffice/show.php?id=<?= $book->getId() ?>" class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-message">
                    <i class="fas fa-empty-folder"></i>
                    <h3>Aucun livre trouvé dans cette catégorie</h3>
                    <p>Il n'y a pas encore de livres dans cette catégorie.</p>
                </div>
            <?php endif; ?>

            <div class="navigation">
                <a href="../BackOffice/categoryList.php" class="nav-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux catégories
                </a>
                <a href="../BackOffice/bookList.php" class="nav-btn primary">
                    <i class="fas fa-books"></i>
                    Tous les livres
                </a>
            </div>
        </div>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('booksTable');
            
            if (!table) return;
            
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) { // Exclure la dernière colonne (Action)
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

        // Animation supplémentaire au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animation = `slideIn 0.3s ease-out ${index * 0.05}s both`;
            });
        });
    </script>
</body>
</html>