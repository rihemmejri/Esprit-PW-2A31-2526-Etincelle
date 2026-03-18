<?php
include '../../controleurs/CategoryController.php';
require_once __DIR__ . '/../../models/Category.php';

$CategoryController = new CategoryController();

// Récupérer l'ID de la catégorie à modifier
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: categoryList.php');
    exit;
}

// Récupérer la catégorie par ID
$category = $CategoryController->getCategoryById($id);

if (!$category) {
    header('Location: categoryList.php');
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    if (!empty($title)) {
        // Mettre à jour l'objet Category
        $category->setTitle($title);
        $category->setDescription($description);
        
        // Mettre à jour dans la BD
        $CategoryController->updateCategory($category);
        
        // Message de succès et redirection
        $success = "Catégorie modifiée avec succès !";
        header('refresh:1; url=categoryList.php');
    } else {
        $error = "Le titre est obligatoire.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Catégorie</title>
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
            max-width: 700px;
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

        .form-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
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

        .header h1 {
            font-size: 2.4em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
            z-index: 1;
        }

        .header h1 i {
            animation: bounce 2s infinite;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.2));
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .header p {
            margin-top: 12px;
            opacity: 0.95;
            font-size: 1.1em;
            position: relative;
            z-index: 1;
        }

        .category-id-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(5px);
            padding: 10px 20px;
            border-radius: 40px;
            display: inline-block;
            margin-top: 15px;
            font-size: 1em;
            border: 1px solid rgba(255,255,255,0.3);
            position: relative;
            z-index: 1;
        }

        .category-id-badge i {
            margin-right: 8px;
        }

        .content {
            padding: 40px 35px;
        }

        .current-values {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 18px;
            margin-bottom: 30px;
            border-left: 6px solid #f5576c;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .current-values h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .current-values h3 i {
            color: #f5576c;
            font-size: 1.3em;
        }

        .value-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .value-tag {
            background: white;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.95em;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .value-tag strong {
            color: #f5576c;
            margin-right: 8px;
        }

        .alert {
            padding: 18px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 6px solid #c62828;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 6px solid #2e7d32;
        }

        .alert i {
            font-size: 1.8em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 1em;
        }

        label i {
            color: #f5576c;
            margin-right: 8px;
        }

        .required {
            color: #f5576c;
            margin-left: 5px;
            font-size: 1.1em;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1em;
            transition: color 0.3s;
        }

        .input-icon input,
        .input-icon textarea {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 1em;
            transition: all 0.3s;
            background: white;
        }

        .input-icon textarea {
            padding: 15px 15px 15px 50px;
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }

        .input-icon input:focus,
        .input-icon textarea:focus {
            outline: none;
            border-color: #f5576c;
            box-shadow: 0 5px 20px rgba(245, 87, 108, 0.2);
        }

        .input-icon input:focus + i,
        .input-icon textarea:focus + i {
            color: #f5576c;
        }

        .input-icon input:hover,
        .input-icon textarea:hover {
            border-color: #999;
        }

        .character-count {
            text-align: right;
            font-size: 0.85em;
            color: #999;
            margin-top: 5px;
        }

        .character-count i {
            margin-right: 5px;
            color: #f5576c;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e0e0e0;
        }

        .btn {
            flex: 1;
            padding: 16px 25px;
            border: none;
            border-radius: 15px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
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

        .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 15px 30px rgba(245, 87, 108, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(245, 87, 108, 0.5);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .btn i {
            font-size: 1.2em;
            position: relative;
            z-index: 1;
        }

        .help-text {
            margin-top: 20px;
            font-size: 0.9em;
            color: #999;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
        }

        .help-text i {
            color: #f5576c;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 1.8em;
                flex-direction: column;
            }
            
            .value-tags {
                flex-direction: column;
            }
            
            .value-tag {
                width: 100%;
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
                    Modifier la catégorie
                </h1>
                <p>Modifiez les informations de la catégorie</p>
                <div class="category-id-badge">
                    <i class="fas fa-hashtag"></i>
                    ID: <?= htmlspecialchars($id) ?>
                </div>
            </div>

            <div class="content">
                <!-- Résumé des valeurs actuelles -->
                <div class="current-values">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        Valeurs actuelles
                    </h3>
                    <div class="value-tags">
                        <span class="value-tag"><strong>Titre:</strong> <?= htmlspecialchars($category->getTitle()) ?></span>
                        <span class="value-tag"><strong>Description:</strong> <?= htmlspecialchars($category->getDescription()) ?: 'Aucune description' ?></span>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($success) ?></span>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" id="editCategoryForm">
                    <!-- Titre -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-heading"></i>
                            Titre <span class="required">*</span>
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="title" value="<?= htmlspecialchars($category->getTitle()) ?>" 
                                   placeholder="Ex: Romans, Science-fiction, etc." required maxlength="100">
                        </div>
                        <div class="character-count">
                            <i class="fas fa-text-width"></i>
                            <span id="titleCount">0</span>/100 caractères
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-align-left"></i>
                            Description
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-pencil-alt"></i>
                            <textarea name="description" placeholder="Description de la catégorie..." maxlength="500"><?= htmlspecialchars($category->getDescription()) ?></textarea>
                        </div>
                        <div class="character-count">
                            <i class="fas fa-text-width"></i>
                            <span id="descCount">0</span>/500 caractères
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                        <a href="categoryList.php" class="btn btn-secondary">
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
        // Compteur de caractères pour le titre
        const titleInput = document.querySelector('input[name="title"]');
        const titleCount = document.getElementById('titleCount');
        
        titleInput.addEventListener('input', function() {
            const count = this.value.length;
            titleCount.textContent = count;
            
            // Changer la couleur si proche de la limite
            if (count > 90) {
                titleCount.style.color = '#f5576c';
            } else if (count > 75) {
                titleCount.style.color = '#ffc107';
            } else {
                titleCount.style.color = '#999';
            }
        });

        // Compteur de caractères pour la description
        const descInput = document.querySelector('textarea[name="description"]');
        const descCount = document.getElementById('descCount');
        
        descInput.addEventListener('input', function() {
            const count = this.value.length;
            descCount.textContent = count;
            
            // Changer la couleur si proche de la limite
            if (count > 450) {
                descCount.style.color = '#f5576c';
            } else if (count > 375) {
                descCount.style.color = '#ffc107';
            } else {
                descCount.style.color = '#999';
            }
        });

        // Initialiser les compteurs
        titleInput.dispatchEvent(new Event('input'));
        descInput.dispatchEvent(new Event('input'));

        // Validation du formulaire
        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            const title = titleInput.value.trim();
            
            if (!title) {
                e.preventDefault();
                alert('Veuillez saisir un titre pour la catégorie.');
                titleInput.focus();
            }
        });

        // Confirmation avant de quitter sans sauvegarder
        let formChanged = false;
        
        document.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('input', function() {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?';
            }
        });

        // Animation des champs
        document.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s';
            });
            
            field.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
<?php
?> 