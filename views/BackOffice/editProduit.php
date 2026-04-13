<?php
include '../../controleurs/ProduitController.php';
require_once __DIR__ . '/../../models/produit.php';

$error = "";
$success = "";
$produitController = new ProduitController();
$produit = null;

if (isset($_GET['id'])) {
    $produit = $produitController->getProduitById($_GET['id']);
    if (!$produit) {
        $error = "Produit non trouvé.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["nom"]) && isset($_POST["id_categorie"]) && isset($_POST["origine"])) {
        if (!empty($_POST["nom"]) && !empty($_POST["id_categorie"]) && !empty($_POST["origine"])) {
            
            $image = $produit->getImage();
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                $target_dir = "../assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image = basename($_FILES["image"]["name"]);
                }
            }
            
            $produit->setNom(htmlspecialchars($_POST['nom']));
            $produit->setImage($image);
            $produit->setIdCategorie(intval($_POST['id_categorie']));
            $produit->setOrigine(htmlspecialchars($_POST['origine']));
            $produit->setDistanceTransport(intval($_POST['distance_transport'] ?? 0));
            $produit->setTypeTransport(htmlspecialchars($_POST['type_transport'] ?? 'camion'));
            $produit->setEmballage(htmlspecialchars($_POST['emballage'] ?? 'carton'));
            $produit->setTransformation(htmlspecialchars($_POST['transformation'] ?? 'brut'));
            $produit->setSaison(htmlspecialchars($_POST['saison'] ?? ''));
            
            $produitController->updateProduit($produit);
            header('Location: produitList.php');
            exit;
        } else {
            $error = "Tous les champs obligatoires doivent être remplis.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Produit - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier un produit
                </h1>
                <p>Modifiez les informations du produit ci-dessous</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($produit): ?>
                <div class="form-content">
                    <form action="" method="POST" id="editProduitForm" enctype="multipart/form-data">
                        <div class="form-grid">
                            <!-- Nom du produit -->
                            <div class="form-group full-width">
                                <label>
                                    <i class="fas fa-apple-alt"></i>
                                    Nom du produit <span class="required">*</span>
                                </label>
                                <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($produit->getNom()) ?>">
                                <small class="error-text" id="nomError"></small>
                            </div>

                            <!-- Image -->
                            <div class="form-group full-width">
                                <label>
                                    <i class="fas fa-image"></i>
                                    Image du produit
                                </label>
                                <?php if ($produit->getImage()): ?>
                                    <div class="current-image">
                                        <img src="../assets/images/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" style="max-width: 150px;">
                                        <p>Image actuelle</p>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" accept="image/*">
                            </div>

                            <!-- Catégorie -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-tag"></i>
                                    Catégorie <span class="required">*</span>
                                </label>
                                <select name="id_categorie" id="id_categorie">
                                    <option value="">Choisir une catégorie</option>
                                    <option value="1" <?= $produit->getIdCategorie() == 1 ? 'selected' : '' ?>>Légumes</option>
                                    <option value="2" <?= $produit->getIdCategorie() == 2 ? 'selected' : '' ?>>Fruits</option>
                                    <option value="3" <?= $produit->getIdCategorie() == 3 ? 'selected' : '' ?>>Produits Laitiers</option>
                                    <option value="4" <?= $produit->getIdCategorie() == 4 ? 'selected' : '' ?>>Viandes</option>
                                    <option value="5" <?= $produit->getIdCategorie() == 5 ? 'selected' : '' ?>>Poissons</option>
                                </select>
                                <small class="error-text" id="categorieError"></small>
                            </div>

                            <!-- Origine -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-globe"></i>
                                    Origine <span class="required">*</span>
                                </label>
                                <select name="origine" id="origine">
                                    <option value="">Choisir une origine</option>
                                    <option value="local" <?= $produit->getOrigine() === 'local' ? 'selected' : '' ?>>Local</option>
                                    <option value="importe" <?= $produit->getOrigine() === 'importe' ? 'selected' : '' ?>>Importé</option>
                                </select>
                                <small class="error-text" id="origineError"></small>
                            </div>

                            <!-- Distance Transport -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-truck"></i>
                                    Distance Transport (km) <span class="required">*</span>
                                </label>
                                <input type="number" name="distance_transport" id="distance_transport" value="<?= htmlspecialchars($produit->getDistanceTransport()) ?>">
                                <small class="error-text" id="distanceError"></small>
                            </div>

                            <!-- Type Transport -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-shipping-fast"></i>
                                    Type de Transport
                                </label>
                                <select name="type_transport">
                                    <option value="camion" <?= $produit->getTypeTransport() === 'camion' ? 'selected' : '' ?>>Camion</option>
                                    <option value="avion" <?= $produit->getTypeTransport() === 'avion' ? 'selected' : '' ?>>Avion</option>
                                    <option value="bateau" <?= $produit->getTypeTransport() === 'bateau' ? 'selected' : '' ?>>Bateau</option>
                                </select>
                            </div>

                            <!-- Emballage -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-box-open"></i>
                                    Emballage
                                </label>
                                <select name="emballage">
                                    <option value="carton" <?= $produit->getEmballage() === 'carton' ? 'selected' : '' ?>>Carton</option>
                                    <option value="plastique" <?= $produit->getEmballage() === 'plastique' ? 'selected' : '' ?>>Plastique</option>
                                    <option value="aucun" <?= $produit->getEmballage() === 'aucun' ? 'selected' : '' ?>>Aucun</option>
                                </select>
                            </div>

                            <!-- Transformation -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-industry"></i>
                                    Transformation
                                </label>
                                <select name="transformation">
                                    <option value="brut" <?= $produit->getTransformation() === 'brut' ? 'selected' : '' ?>>Brut</option>
                                    <option value="transforme" <?= $produit->getTransformation() === 'transforme' ? 'selected' : '' ?>>Transformé</option>
                                    <option value="ultra_transforme" <?= $produit->getTransformation() === 'ultra_transforme' ? 'selected' : '' ?>>Ultra Transformé</option>
                                </select>
                            </div>

                            <!-- Saison -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-leaf"></i>
                                    Saison <span class="required">*</span>
                                </label>
                                <select name="saison" id="saison">
                                    <option value="">Choisir une saison</option>
                                    <option value="printemps" <?= $produit->getSaison() === 'printemps' ? 'selected' : '' ?>>Printemps</option>
                                    <option value="ete" <?= $produit->getSaison() === 'ete' ? 'selected' : '' ?>>Été</option>
                                    <option value="automne" <?= $produit->getSaison() === 'automne' ? 'selected' : '' ?>>Automne</option>
                                    <option value="hiver" <?= $produit->getSaison() === 'hiver' ? 'selected' : '' ?>>Hiver</option>
                                </select>
                                <small class="error-text" id="saisonError"></small>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="produitList.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-check"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/produit.js"></script>
    <script>
        document.getElementById('editProduitForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
