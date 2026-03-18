<?php
include '../../controleurs/BookController.php';
include '../../controleurs/CategoryController.php';
require_once __DIR__ . '/../../models/Book.php';

$bookController = new BookController();
$categoryController = new CategoryController();
$categories = $categoryController->listCategories();

// Récupérer l'ID du livre à modifier
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: bookList.php');
    exit;
}

// Récupérer les infos du livre (retourne un objet Book)
$book = $bookController->getBookById($id);

if (!$book) {
    header('Location: bookList.php');
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $publication_date = $_POST['publication_date'];
    $language = $_POST['language'];
    $status = $_POST['status'];
    $number_of_copies = $_POST['number_of_copies'];
    $category_id = $_POST['category_id'];

    if (!empty($titre) && !empty($auteur) && !empty($category_id)) {
        // Mettre à jour l'objet Book
        $book->setTitre($titre);
        $book->setAuteur($auteur);
        $book->setPublicationDate($publication_date);
        $book->setLanguage($language);
        $book->setStatus($status);
        $book->setNumberOfCopies($number_of_copies);
        $book->setCategoryId($category_id);
        
        // Mettre à jour dans la BD
        $bookController->updateBook($book);
        
        // Redirection
        header('Location: bookList.php');
        exit;
    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Livre</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
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

        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header h1 i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .header p {
            margin-top: 10px;
            opacity: 0.9;
            font-size: 1.1em;
        }

        .book-id-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 30px;
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .book-id-badge i {
            margin-right: 5px;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px 20px;
            margin: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #c62828;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message i {
            font-size: 1.5em;
        }

        .form-content {
            padding: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        label i {
            color: #f5576c;
            margin-right: 8px;
        }

        .required {
            color: #f5576c;
            margin-left: 5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #f5576c;
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.2);
        }

        input:hover, select:hover, textarea:hover {
            border-color: #999;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .input-icon input {
            padding-left: 45px;
        }

        .status-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .status-option {
            flex: 1;
            min-width: 100px;
        }

        .status-option input[type="radio"] {
            display: none;
        }

        .status-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0;
        }

        .status-option label i {
            font-size: 1.5em;
            margin-bottom: 5px;
            color: #666;
        }

        .status-option input[type="radio"]:checked + label {
            background: #ffe9ec;
            border-color: #f5576c;
        }

        .status-option input[type="radio"]:checked + label i {
            color: #f5576c;
        }

        .copies-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .copies-input button {
            width: 40px;
            height: 40px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2em;
            color: #666;
        }

        .copies-input button:hover {
            background: #f5576c;
            border-color: #f5576c;
            color: white;
        }

        .copies-input input {
            text-align: center;
            width: 80px;
        }

        .current-values {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #f5576c;
        }

        .current-values h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-values h3 i {
            color: #f5576c;
        }

        .value-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .value-tag {
            background: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.9em;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .value-tag strong {
            color: #f5576c;
            margin-right: 5px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
        }

        .btn-primary:hover {
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

        .btn i {
            font-size: 1.1em;
        }

        .help-text {
            font-size: 0.85em;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .status-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier le livre
                </h1>
                <p>Modifiez les informations du livre #<?= $id ?></p>
                <div class="book-id-badge">
                    <i class="fas fa-book"></i>
                    ID: <?= $id ?>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <!-- Résumé des valeurs actuelles -->
                <div class="current-values">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        Valeurs actuelles
                    </h3>
                    <div class="value-tags">
                        <span class="value-tag"><strong>Titre:</strong> <?= htmlspecialchars($book->getTitre()) ?></span>
                        <span class="value-tag"><strong>Auteur:</strong> <?= htmlspecialchars($book->getAuteur()) ?></span>
                        <span class="value-tag"><strong>Statut:</strong> <?= $book->getStatus() ?></span>
                        <span class="value-tag"><strong>Exemplaires:</strong> <?= $book->getNumberOfCopies() ?></span>
                    </div>
                </div>

                <form action="" method="POST" id="editBookForm">
                    <div class="form-grid">
                        <!-- Titre -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-heading"></i>
                                Titre <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-book"></i>
                                <input type="text" name="titre" value="<?= htmlspecialchars($book->getTitre()) ?>" placeholder="Ex: Le Petit Prince" required>
                            </div>
                        </div>

                        <!-- Auteur -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-user"></i>
                                Auteur <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-pen"></i>
                                <input type="text" name="auteur" value="<?= htmlspecialchars($book->getAuteur()) ?>" placeholder="Ex: Antoine de Saint-Exupéry" required>
                            </div>
                        </div>

                        <!-- Date de publication -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar"></i>
                                Date de publication
                            </label>
                            <input type="date" name="publication_date" value="<?= $book->getPublicationDate() ?>">
                        </div>

                        <!-- Langue -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-language"></i>
                                Langue
                            </label>
                            <select name="language">
                                <option value="">Sélectionner une langue</option>
                                <option value="Français" <?= $book->getLanguage() == 'Français' ? 'selected' : '' ?>>Français</option>
                                <option value="Anglais" <?= $book->getLanguage() == 'Anglais' ? 'selected' : '' ?>>Anglais</option>
                                <option value="Arabe" <?= $book->getLanguage() == 'Arabe' ? 'selected' : '' ?>>Arabe</option>
                                <option value="Espagnol" <?= $book->getLanguage() == 'Espagnol' ? 'selected' : '' ?>>Espagnol</option>
                                <option value="Allemand" <?= $book->getLanguage() == 'Allemand' ? 'selected' : '' ?>>Allemand</option>
                                <option value="Italien" <?= $book->getLanguage() == 'Italien' ? 'selected' : '' ?>>Italien</option>
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-circle"></i>
                                Statut
                            </label>
                            <div class="status-group">
                                <div class="status-option">
                                    <input type="radio" name="status" id="status_disponible" value="disponible" <?= $book->getStatus() == 'disponible' ? 'checked' : '' ?>>
                                    <label for="status_disponible">
                                        <i class="fas fa-check-circle"></i>
                                        Disponible
                                    </label>
                                </div>
                                <div class="status-option">
                                    <input type="radio" name="status" id="status_emprunte" value="emprunté" <?= $book->getStatus() == 'emprunté' ? 'checked' : '' ?>>
                                    <label for="status_emprunte">
                                        <i class="fas fa-clock"></i>
                                        Emprunté
                                    </label>
                                </div>
                                <div class="status-option">
                                    <input type="radio" name="status" id="status_perdu" value="perdu" <?= $book->getStatus() == 'perdu' ? 'checked' : '' ?>>
                                    <label for="status_perdu">
                                        <i class="fas fa-times-circle"></i>
                                        Perdu
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Nombre d'exemplaires -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-copy"></i>
                                Exemplaires
                            </label>
                            <div class="copies-input">
                                <button type="button" onclick="updateCopies(-1)">-</button>
                                <input type="number" name="number_of_copies" id="number_of_copies" min="1" value="<?= $book->getNumberOfCopies() ?>" readonly>
                                <button type="button" onclick="updateCopies(1)">+</button>
                            </div>
                        </div>

                        <!-- Catégorie -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-folder"></i>
                                Catégorie <span class="required">*</span>
                            </label>
                            <select name="category_id" required>
                                <option value="">Choisir une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->getId() ?>" <?= $cat->getId() == $book->getCategoryId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat->getTitle()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="bookList.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </form>
                
                <div class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour mettre à jour le nombre d'exemplaires
        function updateCopies(delta) {
            const input = document.getElementById('number_of_copies');
            let value = parseInt(input.value) + delta;
            if (value < 1) value = 1;
            input.value = value;
        }

        // Validation du formulaire
        document.getElementById('editBookForm').addEventListener('submit', function(e) {
            const titre = document.querySelector('input[name="titre"]').value.trim();
            const auteur = document.querySelector('input[name="auteur"]').value.trim();
            const category = document.querySelector('select[name="category_id"]').value;
            
            if (!titre || !auteur || !category) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });

        // Animation des champs
        document.querySelectorAll('input, select').forEach(field => {
            field.addEventListener('focus', function() {
                this.style.borderColor = '#f5576c';
                this.style.boxShadow = '0 5px 15px rgba(245, 87, 108, 0.2)';
            });
            field.addEventListener('blur', function() {
                this.style.borderColor = '#e0e0e0';
                this.style.boxShadow = 'none';
            });
        });

        // Confirmation avant de quitter sans sauvegarder
        let formChanged = false;
        document.querySelectorAll('input, select').forEach(field => {
            field.addEventListener('change', function() {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?';
            }
        });
    </script>
</body>
</html>