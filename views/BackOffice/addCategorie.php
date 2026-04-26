<?php
include '../../controleurs/CategorieController.php';
session_start();

$error = "";
$categorieController = new CategorieController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["nom_categorie"]) && !empty($_POST["type_categorie"])) {
        $image = "";
        if (isset($_FILES["image_categorie"]) && $_FILES["image_categorie"]["error"] == 0) {
            $target_dir = "../assets/images/";
            $target_file = $target_dir . basename($_FILES["image_categorie"]["name"]);
            if (move_uploaded_file($_FILES["image_categorie"]["tmp_name"], $target_file)) {
                $image = basename($_FILES["image_categorie"]["name"]);
            }
        }

        $categorie = new Categorie(htmlspecialchars($_POST['nom_categorie']));
        $categorie->setDescription(htmlspecialchars($_POST['description']));
        $categorie->setImageCategorie($image);
        $categorie->setTypeCategorie(htmlspecialchars($_POST['type_categorie']));

        $categorieController->addCategorie($categorie);
        $_SESSION['success_message'] = 'Catégorie ajoutée avec succès';
        header('Location: categorieList.php');
        exit;
    } else {
        $error = "Le nom et le type sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px;">
                <div>
                    <h1><i class="fas fa-plus-circle"></i> Ajouter une catégorie</h1>
                    <p>Définissez un nouveau groupe de produits</p>
                </div>
                <a href="categorieList.php" class="btn btn-secondary" style="background: #003366; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none;"><i class="fas fa-list"></i> Liste</a>
            </div>

            <?php if ($error): ?>
                <div class="error-message" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addCategorieForm" enctype="multipart/form-data" onsubmit="return validateCategorieForm()">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label><i class="fas fa-tag"></i> Nom de la catégorie <span class="required">*</span></label>
                            <input type="text" name="nom_categorie" id="nom_categorie" placeholder="Ex: Fruits exotiques">
                            <small class="error-text" id="nom_categorieError"></small>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-image"></i> Image</label>
                            <input type="file" name="image_categorie" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-filter"></i> Type <span class="required">*</span></label>
                            <select name="type_categorie" id="type_categorie">
                                <option value="">Choisir un type</option>
                                <option value="aliment">Aliment</option>
                                <option value="boisson">Boisson</option>
                                <option value="autre">Autre</option>
                            </select>
                            <small class="error-text" id="type_categorieError"></small>
                        </div>

                        <div class="form-group full-width">
                            <label><i class="fas fa-align-left"></i> Description <span class="required">*</span></label>
                            <textarea name="description" id="description" rows="4" placeholder="Description de la catégorie..."></textarea>
                            <small class="error-text" id="descriptionError"></small>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px; display: flex; gap: 15px;">
                        <button type="reset" class="btn btn-secondary" style="background: #eee; color: #666; padding: 12px 25px; border-radius: 10px; border: none; cursor: pointer;">Réinitialiser</button>
                        <button type="submit" class="btn btn-primary" style="background: #4CAF50; color: white; padding: 12px 25px; border-radius: 10px; border: none; cursor: pointer;">Créer la catégorie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/categorie.js"></script>
</body>
</html>
