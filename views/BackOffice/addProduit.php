<?php
include '../../controleurs/ProduitController.php';
<<<<<<< HEAD
include '../../controleurs/CategorieController.php';
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
require_once __DIR__ . '/../../models/produit.php';

$error = "";
$success = "";
$produitController = new ProduitController();
<<<<<<< HEAD
$categorieController = new CategorieController();
$categories = $categorieController->listCategories();
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["nom"]) && isset($_POST["id_categorie"]) && isset($_POST["origine"])) {
        if (!empty($_POST["nom"]) && !empty($_POST["id_categorie"]) && !empty($_POST["origine"])) {
            
            $image = "";
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                $target_dir = "../assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image = basename($_FILES["image"]["name"]);
                }
            }
            
            $produit = new produit(
                htmlspecialchars($_POST['nom']),
                $image,
                intval($_POST['id_categorie']),
                htmlspecialchars($_POST['origine']),
                intval($_POST['distance_transport'] ?? 0),
                htmlspecialchars($_POST['type_transport'] ?? 'camion'),
                htmlspecialchars($_POST['emballage'] ?? 'carton'),
                htmlspecialchars($_POST['transformation'] ?? 'brut'),
                htmlspecialchars($_POST['saison'] ?? '')
            );
            
            $produitController->addProduit($produit);
<<<<<<< HEAD
            session_start();
            $_SESSION['success_message'] = 'Produit ajouté avec succès';
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
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
    <title>Ajouter un Produit - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-form">
        <div class="form-card">
<<<<<<< HEAD
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px;">
                <div>
                    <h1>
                        <i class="fas fa-plus-circle"></i>
                        Ajouter un produit
                    </h1>
                    <p>Remplissez les informations ci-dessous pour ajouter un nouveau produit</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="produitList.php" class="btn btn-secondary" style="background: #003366; color: white;"><i class="fas fa-list"></i> Liste</a>
                </div>
=======
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un produit
                </h1>
                <p>Remplissez les informations ci-dessous pour ajouter un nouveau produit</p>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addProduitForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- Nom du produit -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-apple-alt"></i>
                                Nom du produit <span class="required">*</span>
                            </label>
                            <input type="text" name="nom" id="nom" placeholder="Ex: Tomate Bio">
                            <small class="error-text" id="nomError"></small>
                        </div>

                        <!-- Image -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-image"></i>
                                Image du produit
                            </label>
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
<<<<<<< HEAD
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->getIdCategorie() ?>"><?= htmlspecialchars($cat->getNomCategorie()) ?></option>
                                <?php endforeach; ?>
=======
                                <option value="1">Légumes</option>
                                <option value="2">Fruits</option>
                                <option value="3">Produits Laitiers</option>
                                <option value="4">Viandes</option>
                                <option value="5">Poissons</option>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
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
                                <option value="local">Local</option>
                                <option value="importe">Importé</option>
                            </select>
                            <small class="error-text" id="origineError"></small>
                        </div>

                        <!-- Distance Transport -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-truck"></i>
                                Distance Transport (km) <span class="required">*</span>
                            </label>
                            <input type="number" name="distance_transport" id="distance_transport" placeholder="0">
                            <small class="error-text" id="distanceError"></small>
                        </div>

                        <!-- Type Transport -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-shipping-fast"></i>
                                Type de Transport
                            </label>
                            <select name="type_transport">
                                <option value="camion">Camion</option>
                                <option value="avion">Avion</option>
                                <option value="bateau">Bateau</option>
                            </select>
                        </div>

                        <!-- Emballage -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-box-open"></i>
                                Emballage
                            </label>
                            <select name="emballage">
                                <option value="carton">Carton</option>
                                <option value="plastique">Plastique</option>
                                <option value="aucun">Aucun</option>
                            </select>
                        </div>

                        <!-- Transformation -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-industry"></i>
                                Transformation
                            </label>
                            <select name="transformation">
                                <option value="brut">Brut</option>
                                <option value="transforme">Transformé</option>
                                <option value="ultra_transforme">Ultra Transformé</option>
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
                                <option value="printemps">Printemps</option>
                                <option value="ete">Été</option>
                                <option value="automne">Automne</option>
                                <option value="hiver">Hiver</option>
                            </select>
                            <small class="error-text" id="saisonError"></small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-check"></i> Ajouter le produit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/produit.js"></script>
    <script>
        document.getElementById('addProduitForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    </script>
