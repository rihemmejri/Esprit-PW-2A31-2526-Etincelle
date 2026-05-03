<?php
include '../../controleurs/CategorieController.php';
require_once __DIR__ . '/../../models/categorie.php';

$categorieController = new CategorieController();
$categories = $categorieController->listCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Catégories - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2E7D32, #1565C0);
            --success-green: #2E7D32;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); line-height: 1.6; }
        .container-client { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px; }

        .hero-section {
            background: var(--primary-gradient); border-radius: 20px; padding: 50px 30px;
            text-align: center; color: white; margin: 20px 0 60px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .hero-section h1 { font-size: 2.8em; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 15px; }

        .categories-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        
        .category-card {
            background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; flex-direction: column;
        }
        .category-card:hover { transform: scale(1.03); box-shadow: 0 15px 40px rgba(0,0,0,0.15); }

        .category-image {
            height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;
            position: relative; font-size: 5em;
        }
        .category-badge {
            position: absolute; top: 15px; right: 15px; background: var(--success-green);
            color: white; padding: 6px 15px; border-radius: 20px; font-size: 0.8em; font-weight: 600;
        }

        .category-content { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .category-title { font-size: 1.5em; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .category-description { font-size: 0.95em; color: var(--text-gray); margin-bottom: 20px; flex-grow: 1; }

        .category-footer { border-top: 1px solid #f0f0f0; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; }
        .btn-view {
            background: var(--primary-gradient); color: white; border: none; padding: 10px 20px;
            border-radius: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px;
        }

        .simple-footer { background: var(--success-green); color: white; text-align: center; padding: 20px; margin-top: 60px; }
    </style>
</head>
<body>
    <div class="container-client">
        <div class="hero-section">
            <h1><i class="fas fa-tags"></i> Nos Catégories</h1>
            <p>Explorez notre univers nutritionnel à travers nos différentes catégories de produits.</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <div class="category-card">
                    <div class="category-image">
                        <?php if ($cat->getImageCategorie()): ?>
                            <img src="../assets/images/<?= htmlspecialchars($cat->getImageCategorie()) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span><?= $cat->getTypeCategorie() === 'boisson' ? '🥤' : '🍎' ?></span>
                        <?php endif; ?>
                        <div class="category-badge"><?= ucfirst($cat->getTypeCategorie()) ?></div>
                    </div>
                    <div class="category-content">
                        <h3 class="category-title"><?= htmlspecialchars($cat->getNomCategorie()) ?></h3>
                        <p class="category-description"><?= htmlspecialchars($cat->getDescription()) ?: 'Découvrez tous les produits de cette catégorie.' ?></p>
                        <div class="category-footer">
                            <span style="font-size: 0.85em; color: #999;"><?= date('M Y', strtotime($cat->getDateCreation())) ?></span>
                            <a href="afficherProduit.php?cat=<?= $cat->getIdCategorie() ?>" class="btn-view">
                                Voir Produits <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="simple-footer">
        <p><i class="fas fa-heart" style="color: #ff4757;"></i> NutriLoop AI - Manger sainement pour une vie meilleure</p>
    </footer>
</body>
</html>
