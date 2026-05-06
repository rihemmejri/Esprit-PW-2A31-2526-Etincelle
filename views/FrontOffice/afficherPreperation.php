<?php
include '../../controleurs/RecetteController.php';
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/recette.php';
require_once __DIR__ . '/../../models/preperation.php';

$recetteController = new RecetteController();
$preperationController = new PreperationController();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: afficherRecette.php');
    exit;
}

$recette = $recetteController->getRecetteById($id);
if (!$recette) {
    header('Location: afficherRecette.php');
    exit;
}

// Récupérer les étapes de cette recette
$etapes = $preperationController->getPreperationsByRecetteId($id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recette->getNom()) ?> - NutriLoop</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2e7d32;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        .nav-menu li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: 0.3s;
        }
        
        .nav-menu li a:hover {
            color: #4CAF50;
        }
        
        .btn-dashboard {
            background: #4CAF50;
            color: white !important;
            padding: 8px 20px;
            border-radius: 25px;
        }
        
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        
        .hamburger span {
            width: 25px;
            height: 3px;
            background: #333;
            margin: 3px 0;
            transition: 0.3s;
        }
        
        /* Container */
        .container-recette {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* Bouton retour */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #6c757d;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            text-decoration: none;
            margin-bottom: 30px;
            transition: 0.3s;
            font-weight: 500;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateX(-5px);
        }
        
        /* Hero section */
        .recette-hero {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white;
            padding: 40px;
            border-radius: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .recette-hero h1 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .recette-hero h1 i {
            font-size: 2rem;
        }
        
        .recette-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .meta-item {
            background: rgba(255,255,255,0.2);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Description */
        .description-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .description-section h2 {
            color: #2e7d32;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .description-text {
            line-height: 1.7;
            color: #555;
        }
        
        /* Étapes */
        .etapes-section h2 {
            color: #2e7d32;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .etapes-grid {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .etape-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .etape-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .etape-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 3px solid #4CAF50;
        }
        
        .etape-numero {
            background: #4CAF50;
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .etape-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .etape-badge {
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #555;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .etape-badge i {
            color: #4CAF50;
        }
        
        .etape-body {
            padding: 25px;
        }
        
        /* Tableau des informations détaillées */
        .etape-details-table {
            background: #f8f9fa;
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .detail-row {
            display: flex;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            width: 140px;
            padding: 12px 15px;
            background: #e9ecef;
            font-weight: 600;
            color: #2e7d32;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-value {
            flex: 1;
            padding: 12px 15px;
            background: white;
            color: #333;
        }
        
        .etape-instruction {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        
        .etape-instruction i {
            color: #4CAF50;
            margin-right: 8px;
        }
        
        .etape-astuce {
            background: #fff8e1;
            padding: 15px 20px;
            border-radius: 16px;
            font-size: 0.85rem;
            color: #856404;
            border-left: 4px solid #ffc107;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .etape-astuce i {
            font-size: 1.2rem;
        }
        
        .etape-image-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #f0f2f5, #e9ecef);
            border-radius: 16px;
            margin-bottom: 20px;
        }
        
        .action-icon {
            font-size: 3rem;
        }
        
        .empty-etapes {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            color: #666;
        }
        
        .empty-etapes i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        /* Footer */
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 40px 20px 20px;
            margin-top: 60px;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .footer-section h3, .footer-section h4 {
            margin-bottom: 15px;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 8px;
        }
        
        .footer-section a {
            color: #ccc;
            text-decoration: none;
        }
        
        .footer-section a:hover {
            color: #4CAF50;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            background: rgba(255,255,255,0.1);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .social-links a:hover {
            background: #4CAF50;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background: white;
                width: 100%;
                text-align: center;
                transition: 0.3s;
                padding: 20px 0;
                gap: 15px;
            }
            .nav-menu.active {
                left: 0;
            }
            .hamburger {
                display: flex;
            }
            .recette-hero {
                padding: 25px;
            }
            .recette-hero h1 {
                font-size: 1.5rem;
            }
            .etape-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .detail-label {
                width: 110px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img">
                <span class="logo-text">NutriLoop</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">Accueil</a></li>
                <li><a href="index.html#features">Fonctionnalités</a></li>
                <li><a href="index.html#modules">Modules</a></li>
                <li><a href="about.html">À propos</a></li>
                <li><a href="contact.html">Contact</a></li>
                
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- Contenu -->
    <div class="container-recette">
        <a href="afficherRecette.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour aux recettes
        </a>

        <!-- Hero -->
        <div class="recette-hero">
            <h1>
                <?php
                $icone = '';
                $nom = strtolower($recette->getNom());
                if (strpos($nom, 'pizza') !== false) $icone = '🍕';
                elseif (strpos($nom, 'tajine') !== false) $icone = '🍲';
                elseif (strpos($nom, 'salade') !== false) $icone = '🥗';
                elseif (strpos($nom, 'gateau') !== false) $icone = '🍰';
                elseif (strpos($nom, 'pasta') !== false) $icone = '🍝';
                else $icone = '🍽️';
                ?>
                <span><?= $icone ?></span>
                <?= htmlspecialchars($recette->getNom()) ?>
            </h1>
            <div class="recette-meta">
                <div class="meta-item"><i class="fas fa-clock"></i> <?= $recette->getTempsPreparation() ?> min</div>
                <div class="meta-item"><i class="fas fa-users"></i> <?= $recette->getNbPersonne() ?> pers</div>
                <div class="meta-item"><i class="fas fa-chart-line"></i> <?= $recette->getDifficulte() ?></div>
                <div class="meta-item"><i class="fas fa-mug-hot"></i> 
                    <?php
                    $types = ['PETIT_DEJEUNER' => 'Petit déjeuner', 'DEJEUNER' => 'Déjeuner', 'DINER' => 'Dîner', 'DESSERT' => 'Dessert'];
                    echo $types[$recette->getTypeRepas()] ?? $recette->getTypeRepas();
                    ?>
                </div>
                <?php if ($recette->getOrigine()): ?>
                <div class="meta-item"><i class="fas fa-globe"></i> <?= htmlspecialchars($recette->getOrigine()) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Description -->
        <div class="description-section">
            <h2><i class="fas fa-align-left"></i> Description</h2>
            <div class="description-text"><?= nl2br(htmlspecialchars($recette->getDescription())) ?></div>
        </div>

        <!-- Étapes -->
        <div class="etapes-section">
            <h2><i class="fas fa-list-ol"></i> Étapes de préparation</h2>
            
            <?php if (count($etapes) > 0): ?>
                <div class="etapes-grid">
                    <?php foreach ($etapes as $index => $etape): ?>
                        <div class="etape-card">
                            <div class="etape-header">
                                <span class="etape-numero">
                                    <i class="fas fa-check-circle"></i> Étape <?= htmlspecialchars($etape->getOrdre()) ?>
                                </span>
                                <div class="etape-badges">
                                    <?php if ($etape->getDuree() > 0): ?>
                                    <span class="etape-badge"><i class="fas fa-hourglass-half"></i> <?= $etape->getDuree() ?> min</span>
                                    <?php endif; ?>
                                    <?php if ($etape->getTemperature()): ?>
                                    <span class="etape-badge"><i class="fas fa-thermometer-half"></i> <?= $etape->getTemperature() ?>°C</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="etape-body">
                                <!-- Icône d'illustration -->
                                <div class="etape-image-icon">
                                    <?php
                                    $action = $etape->getTypeAction();
                                    $imageIcon = '';
                                    $actionText = '';
                                    if ($action == 'COUPER') {
                                        $imageIcon = '🔪';
                                        $actionText = 'Couper';
                                    } elseif ($action == 'MELANGER') {
                                        $imageIcon = '🥄';
                                        $actionText = 'Mélanger';
                                    } elseif ($action == 'CUISSON') {
                                        $imageIcon = '🔥';
                                        $actionText = 'Cuisson';
                                    } else {
                                        $imageIcon = '🍳';
                                        $actionText = 'Préparation';
                                    }
                                    ?>
                                    <span class="action-icon"><?= $imageIcon ?></span>
                                    <span style="font-weight: 500; color: #2e7d32;"><?= $actionText ?></span>
                                </div>

                                <!-- Tableau des détails -->
                                <div class="etape-details-table">
                                    <?php if ($etape->getTypeAction()): ?>
                                    <div class="detail-row">
                                        <div class="detail-label"><i class="fas fa-cut"></i> Type d'action</div>
                                        <div class="detail-value"><?= htmlspecialchars($etape->getTypeAction()) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($etape->getOutilUtilise()): ?>
                                    <div class="detail-row">
                                        <div class="detail-label"><i class="fas fa-tools"></i> Outil utilisé</div>
                                        <div class="detail-value"><?= htmlspecialchars($etape->getOutilUtilise()) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($etape->getQuantiteIngredient()): ?>
                                    <div class="detail-row">
                                        <div class="detail-label"><i class="fas fa-weight-hanging"></i> Quantité ingrédient</div>
                                        <div class="detail-value"><?= htmlspecialchars($etape->getQuantiteIngredient()) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($etape->getDuree() > 0): ?>
                                    <div class="detail-row">
                                        <div class="detail-label"><i class="fas fa-clock"></i> Durée</div>
                                        <div class="detail-value"><?= $etape->getDuree() ?> minutes</div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($etape->getTemperature()): ?>
                                    <div class="detail-row">
                                        <div class="detail-label"><i class="fas fa-thermometer-half"></i> Température</div>
                                        <div class="detail-value"><?= $etape->getTemperature() ?> °C</div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Instruction -->
                                <div class="etape-instruction">
                                    <i class="fas fa-align-left"></i> 
                                    <strong>Instruction :</strong><br>
                                    <?= nl2br(htmlspecialchars($etape->getInstruction())) ?>
                                </div>

                                <!-- Astuce -->
                                <?php if ($etape->getAstuce()): ?>
                                <div class="etape-astuce">
                                    <i class="fas fa-lightbulb"></i>
                                    <span><strong>Astuce :</strong> <?= nl2br(htmlspecialchars($etape->getAstuce())) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-etapes">
                    <i class="fas fa-info-circle"></i>
                    <h3>Aucune étape de préparation</h3>
                    <p>Cette recette n'a pas encore d'étapes de préparation.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>NutriLoop</h3>
                <p>L'intelligence artificielle au service de votre assiette.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="afficherRecette.php">Recettes</a></li>
                    <li><a href="about.html">À propos</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-envelope"></i> contact@nutriloop.ai</li>
                    <li><i class="fas fa-phone"></i> +216 70 000 000</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 NutriLoop - Tous droits réservés</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            if (hamburger && navMenu) {
                hamburger.addEventListener('click', function() {
                    hamburger.classList.toggle('active');
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>