<?php
include '../../controleurs/BookController.php';

$bookController = new BookController();
$id = $_GET['id'] ?? null;

if ($id) {
    // Vérifier si la confirmation est donnée
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $bookController->deleteBook($id);
        header('Location: bookList.php');
        exit;
    }
} else {
    header('Location: bookList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de suppression</title>
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
            max-width: 500px;
            width: 100%;
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

        .confirmation-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            text-align: center;
        }

        .warning-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 40px 20px;
            color: white;
            font-size: 4em;
            animation: pulse 2s infinite;
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

        .warning-icon i {
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
        }

        .content {
            padding: 40px 30px;
        }

        .content h2 {
            color: #333;
            font-size: 2em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .content p {
            color: #666;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .book-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            border-left: 5px solid #f5576c;
            text-align: left;
        }

        .book-info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .book-info-item:last-child {
            border-bottom: none;
        }

        .book-info-item i {
            width: 30px;
            color: #f5576c;
            font-size: 1.2em;
        }

        .book-info-item .label {
            color: #666;
            font-weight: 500;
            min-width: 100px;
        }

        .book-info-item .value {
            color: #333;
            font-weight: 600;
            flex: 1;
        }

        .warning-text {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 25px 0;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #ffc107;
        }

        .warning-text i {
            font-size: 1.5em;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(245, 87, 108, 0.4);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .footer-note {
            margin-top: 20px;
            color: #999;
            font-size: 0.9em;
        }

        .footer-note i {
            color: #f5576c;
        }

        @media (max-width: 480px) {
            .actions {
                flex-direction: column;
            }
            
            .book-info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .book-info-item i {
                width: auto;
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
                <h2>⚠️ Confirmation</h2>
                <p>Êtes-vous sûr de vouloir supprimer ce livre ?</p>
                
                <?php
                // Récupérer les infos du livre pour les afficher
                $bookInfo = $bookController->getBook($id);
                if ($bookInfo):
                ?>
                <div class="book-info">
                    <div class="book-info-item">
                        <i class="fas fa-heading"></i>
                        <span class="label">Titre :</span>
                        <span class="value"><?= htmlspecialchars($bookInfo['titre']) ?></span>
                    </div>
                    <div class="book-info-item">
                        <i class="fas fa-user"></i>
                        <span class="label">Auteur :</span>
                        <span class="value"><?= htmlspecialchars($bookInfo['auteur']) ?></span>
                    </div>
                    <div class="book-info-item">
                        <i class="fas fa-copy"></i>
                        <span class="label">Exemplaires :</span>
                        <span class="value"><?= htmlspecialchars($bookInfo['number_of_copies']) ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="warning-text">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Cette action est irréversible. Toutes les données associées à ce livre seront définitivement supprimées.</span>
                </div>
                
                <div class="actions">
                    <a href="deleteBook.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        Oui, supprimer
                    </a>
                    <a href="bookList.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Non, annuler
                    </a>
                </div>
                
                <div class="footer-note">
                    <i class="fas fa-info-circle"></i>
                    Vous serez redirigé vers la liste des livres après l'action
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animation supplémentaire pour le bouton de suppression
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.querySelector('.btn-danger');
            if (deleteBtn) {
                deleteBtn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });
                deleteBtn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(-2px) scale(1)';
                });
            }
        });
    </script>
</body>
</html>