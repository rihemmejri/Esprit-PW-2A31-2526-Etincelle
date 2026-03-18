<?php
include '../../controleurs/CategoryController.php';
include '../../controleurs/BookController.php';

$CategoryController = new CategoryController();
$BookController = new BookController();
$id = $_GET['id'] ?? null;

// Récupérer les infos de la catégorie pour les afficher
$category = null;
if ($id) {
    $categories = $CategoryController->listCategories();
    foreach ($categories as $cat) {
        if ($cat->getId() == $id) {
            $category = $cat;
            break;
        }
    }
    // Récupérer le nombre de livres dans cette catégorie
    $booksInCategory = $BookController->getBooksByCategory($id);
    $bookCount = count($booksInCategory);
}

// Si confirmation est donnée
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && $id) {
    $CategoryController->deleteCategory($id);
    header('Location: categoryList.php');
    exit;
}

// Si pas de confirmation, afficher la page de confirmation
if ($id && $category) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de suppression - Catégorie</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 550px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .confirmation-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.4);
            overflow: hidden;
            text-align: center;
            transform-origin: center;
        }

        .warning-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 40px 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .warning-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .warning-icon i {
            font-size: 5em;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
            animation: pulse 2s infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .content {
            padding: 40px 35px;
        }

        .content h2 {
            color: #333;
            font-size: 2.2em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .content p {
            color: #666;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .category-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 25px;
            margin: 25px 0;
            border-left: 6px solid #f5576c;
            text-align: left;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .category-info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .category-info-item:last-child {
            border-bottom: none;
        }

        .category-info-item i {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f5576c;
            font-size: 1.2em;
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.2);
        }

        .category-info-item .label {
            color: #666;
            font-weight: 500;
            min-width: 100px;
        }

        .category-info-item .value {
            color: #333;
            font-weight: 700;
            flex: 1;
            font-size: 1.1em;
        }

        .warning-text {
            background: #fff3cd;
            color: #856404;
            padding: 18px 20px;
            border-radius: 15px;
            margin: 25px 0;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 6px solid #ffc107;
            text-align: left;
        }

        .warning-text i {
            font-size: 2em;
            color: #ffc107;
        }

        .warning-text strong {
            color: #856404;
            font-size: 1.1em;
        }

        .book-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 15px;
            border-radius: 12px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
        }

        .book-count i {
            font-size: 1.3em;
        }

        .book-count .number {
            font-size: 1.5em;
            margin: 0 5px;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 16px 20px;
            border: none;
            border-radius: 15px;
            font-size: 1.1em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 15px 30px rgba(245, 87, 108, 0.4);
        }

        .btn-danger:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 40px rgba(245, 87, 108, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .btn-secondary:hover {
            transform: translateY(-5px) scale(1.05);
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .btn i {
            font-size: 1.2em;
            position: relative;
            z-index: 1;
        }

        .footer-note {
            margin-top: 25px;
            color: #999;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 30px;
        }

        .footer-note i {
            color: #f5576c;
        }

        @media (max-width: 480px) {
            .actions {
                flex-direction: column;
            }
            
            .category-info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .category-info-item i {
                width: 35px;
                height: 35px;
                font-size: 1em;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .content h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <div class="content">
                <h2>⚠️ Attention !</h2>
                <p>Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
                
                <div class="category-info">
                    <div class="category-info-item">
                        <i class="fas fa-hashtag"></i>
                        <span class="label">ID :</span>
                        <span class="value">#<?= htmlspecialchars($category->getId()) ?></span>
                    </div>
                    <div class="category-info-item">
                        <i class="fas fa-heading"></i>
                        <span class="label">Titre :</span>
                        <span class="value"><?= htmlspecialchars($category->getTitle()) ?></span>
                    </div>
                    <div class="category-info-item">
                        <i class="fas fa-align-left"></i>
                        <span class="label">Description :</span>
                        <span class="value"><?= htmlspecialchars($category->getDescription()) ?: 'Aucune description' ?></span>
                    </div>
                </div>

                <div class="book-count">
                    <i class="fas fa-book"></i>
                    <span>Cette catégorie contient</span>
                    <span class="number"><?= $bookCount ?></span>
                    <span>livre(s)</span>
                </div>
                
                <?php if ($bookCount > 0): ?>
                    <div class="warning-text">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>⚠️ Attention !</strong><br>
                            Cette catégorie contient <?= $bookCount ?> livre(s).<br>
                            La suppression de cette catégorie affectera ces livres.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="warning-text">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>ℹ️ Information</strong><br>
                            Cette catégorie ne contient aucun livre.<br>
                            La suppression est sans risque.
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="actions">
                    <a href="deleteCategory.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        Oui, supprimer
                    </a>
                    <a href="categoryList.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Non, annuler
                    </a>
                </div>
                
                <div class="footer-note">
                    <i class="fas fa-info-circle"></i>
                    Cette action est irréversible
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animation supplémentaire
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.querySelector('.btn-danger');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    if (!confirm('Dernière chance ! Êtes-vous absolument sûr de vouloir supprimer cette catégorie ?')) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php
} else {
    header('Location: categoryList.php');
    exit;
}
?>