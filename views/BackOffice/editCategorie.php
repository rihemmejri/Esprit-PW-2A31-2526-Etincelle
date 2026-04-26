<?php
include '../../controleurs/CategorieController.php';
session_start();

$error = "";
$categorieController = new CategorieController();
$categorie = null;

if (isset($_GET['id'])) {
    $categorie = $categorieController->getCategorieById($_GET['id']);
    if (!$categorie) {
        header('Location: categorieList.php');
        exit;
    }
} else {
    header('Location: categorieList.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["nom_categorie"]) && !empty($_POST["type_categorie"])) {
        $image = $categorie->getImageCategorie();
        if (isset($_FILES["image_categorie"]) && $_FILES["image_categorie"]["error"] == 0) {
            $target_dir = "../assets/images/";
            $target_file = $target_dir . basename($_FILES["image_categorie"]["name"]);
            if (move_uploaded_file($_FILES["image_categorie"]["tmp_name"], $target_file)) {
                $image = basename($_FILES["image_categorie"]["name"]);
            }
        }

        $categorie->setNomCategorie(htmlspecialchars($_POST['nom_categorie']));
        $categorie->setDescription(htmlspecialchars($_POST['description']));
        $categorie->setImageCategorie($image);
        $categorie->setTypeCategorie(htmlspecialchars($_POST['type_categorie']));

        $categorieController->updateCategorie($categorie);
        $_SESSION['success_message'] = 'Catégorie modifiée avec succès';
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
    <title>Modifier une Catégorie - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px;">
                <div>
                    <h1><i class="fas fa-edit"></i> Modifier la catégorie</h1>
                    <p>Mettez à jour les informations de #<?= $categorie->getIdCategorie() ?></p>
                </div>
                <a href="categorieList.php" class="btn btn-secondary" style="background: #003366; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none;"><i class="fas fa-list"></i> Liste</a>
            </div>

            <?php if ($error): ?>
                <div class="error-message" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="editCategorieForm" enctype="multipart/form-data" onsubmit="return validateCategorieForm()">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label><i class="fas fa-tag"></i> Nom de la catégorie <span class="required">*</span></label>
                            <input type="text" name="nom_categorie" id="nom_categorie" value="<?= htmlspecialchars($categorie->getNomCategorie()) ?>">
                            <small class="error-text" id="nom_categorieError"></small>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-image"></i> Image actuelle</label>
                            <?php if ($categorie->getImageCategorie()): ?>
                                <img src="../assets/images/<?= htmlspecialchars($categorie->getImageCategorie()) ?>" style="width: 100px; border-radius: 10px; display: block; margin-bottom: 10px;">
                            <?php endif; ?>
                            <input type="file" name="image_categorie" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-filter"></i> Type <span class="required">*</span></label>
                            <select name="type_categorie" id="type_categorie">
                                <option value="aliment" <?= $categorie->getTypeCategorie() === 'aliment' ? 'selected' : '' ?>>Aliment</option>
                                <option value="boisson" <?= $categorie->getTypeCategorie() === 'boisson' ? 'selected' : '' ?>>Boisson</option>
                                <option value="autre" <?= $categorie->getTypeCategorie() === 'autre' ? 'selected' : '' ?>>Autre</option>
                            </select>
                            <small class="error-text" id="type_categorieError"></small>
                        </div>

                        <div class="form-group full-width">
                            <label><i class="fas fa-align-left"></i> Description <span class="required">*</span></label>
                            <textarea name="description" id="description" rows="4"><?= htmlspecialchars($categorie->getDescription()) ?></textarea>
                            <small class="error-text" id="descriptionError"></small>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px; display: flex; gap: 15px;">
                        <a href="categorieList.php" class="btn btn-secondary" style="background: #eee; color: #666; padding: 12px 25px; border-radius: 10px; text-decoration: none;">Annuler</a>
                        <button type="submit" class="btn btn-primary" style="background: #4CAF50; color: white; padding: 12px 25px; border-radius: 10px; border: none; cursor: pointer;">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/categorie.js"></script>
</body>
</html>
