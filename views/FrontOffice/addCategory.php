<?php
include '../../controleurs/CategoryController.php';
require_once __DIR__ . '/../../models/Category.php';

$error = "";
$categoryController = new CategoryController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["title"]) && isset($_POST["description"])) {
        if (!empty($_POST["title"])) {
            $title = $_POST['title'];
            $description = $_POST['description'] ?? '';

            $category = new Category($title, $description);
            $categoryController->addCategory($category);

            // Redirection vers la liste des catégories
            header('Location: ../BackOffice/categoryList.php');
            exit;
        } else {
            $error = "Le titre est obligatoire.";
        }
    } else {
        $error = "Informations manquantes.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie</title>
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
            max-width: 650px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            font-size: 2.3em;
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

        .content {
            padding: 40px 35px;
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
            color: #667eea;
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
            min-height: 130px;
            resize: vertical;
            font-family: inherit;
        }

        .input-icon input:focus,
        .input-icon textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
        }

        .input-icon input:focus + i,
        .input-icon textarea:focus + i {
            color: #667eea;
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
            color: #667eea;
        }

        .preview-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 18px;
            margin: 25px 0;
            border-left: 6px solid #667eea;
        }

        .preview-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-section h3 i {
            color: #667eea;
        }

        .preview-content {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .preview-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #667eea;
        }

        .preview-description {
            color: #666;
            line-height: 1.6;
            min-height: 60px;
        }

        .preview-placeholder {
            color: #999;
            font-style: italic;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.5);
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
            color: #667eea;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Nouvelle Catégorie
                </h1>
                <p>Créez une nouvelle catégorie pour organiser vos livres</p>
            </div>

            <div class="content">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Section de prévisualisation en direct -->
                <div class="preview-section">
                    <h3>
                        <i class="fas fa-eye"></i>
                        Aperçu en direct
                    </h3>
                    <div class="preview-content">
                        <div class="preview-title" id="previewTitle">Titre de la catégorie</div>
                        <div class="preview-description" id="previewDescription">
                            <span class="preview-placeholder">Aperçu de la description...</span>
                        </div>
                    </div>
                </div>

                <form action="" method="POST" id="addCategoryForm">
                    <!-- Titre -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-heading"></i>
                            Titre <span class="required">*</span>
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-tag"></i>
                            <input type="text" id="title" name="title" 
                                   placeholder="Ex: Romans, Science-fiction, Biographies..." 
                                   required maxlength="100" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
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
                            <textarea id="description" name="description" placeholder="Description de la catégorie..." maxlength="500"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                        </div>
                        <div class="character-count">
                            <i class="fas fa-text-width"></i>
                            <span id="descCount">0</span>/500 caractères
                        </div>
                    </div>

                    <!-- Champ action (caché car inutilisé) -->
                    <input type="hidden" name="action" value="add">

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Ajouter
                        </button>
                        <button type="reset" class="btn btn-secondary" onclick="resetPreview()">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>
                        <a href="../BackOffice/categoryList.php" class="btn btn-secondary">
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
        // Mise à jour de l'aperçu en direct
        const titleInput = document.getElementById('title');
        const descInput = document.getElementById('description');
        const previewTitle = document.getElementById('previewTitle');
        const previewDesc = document.getElementById('previewDescription');

        function updatePreview() {
            // Mettre à jour le titre
            const titleValue = titleInput.value.trim();
            previewTitle.textContent = titleValue || 'Titre de la catégorie';
            
            // Mettre à jour la description
            const descValue = descInput.value.trim();
            if (descValue) {
                previewDesc.innerHTML = descValue;
                previewDesc.classList.remove('preview-placeholder');
            } else {
                previewDesc.innerHTML = '<span class="preview-placeholder">Aperçu de la description...</span>';
            }
        }

        // Compteurs de caractères
        const titleCount = document.getElementById('titleCount');
        const descCount = document.getElementById('descCount');

        function updateCounts() {
            // Compteur titre
            const titleLength = titleInput.value.length;
            titleCount.textContent = titleLength;
            
            if (titleLength > 90) {
                titleCount.style.color = '#f5576c';
            } else if (titleLength > 75) {
                titleCount.style.color = '#ffc107';
            } else {
                titleCount.style.color = '#999';
            }

            // Compteur description
            const descLength = descInput.value.length;
            descCount.textContent = descLength;
            
            if (descLength > 450) {
                descCount.style.color = '#f5576c';
            } else if (descLength > 375) {
                descCount.style.color = '#ffc107';
            } else {
                descCount.style.color = '#999';
            }
        }

        // Événements pour la mise à jour en direct
        titleInput.addEventListener('input', () => {
            updatePreview();
            updateCounts();
        });
        
        descInput.addEventListener('input', () => {
            updatePreview();
            updateCounts();
        });

        // Fonction de réinitialisation de l'aperçu
        function resetPreview() {
            setTimeout(() => {
                updatePreview();
                updateCounts();
            }, 10);
        }

        // Initialisation
        updatePreview();
        updateCounts();

        // Validation du formulaire
        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            const title = titleInput.value.trim();
            
            if (!title) {
                e.preventDefault();
                alert('Veuillez saisir un titre pour la catégorie.');
                titleInput.focus();
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