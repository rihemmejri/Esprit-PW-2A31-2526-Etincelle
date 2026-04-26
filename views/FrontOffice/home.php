<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriLoop AI | Accueil</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style additionnel pour les cartes cliquables */
        .module-card {
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .module-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .module-card:active {
            transform: translateY(-2px);
        }
        
        .module-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(76, 175, 80, 0);
            transition: 0.3s;
            pointer-events: none;
            border-radius: 20px;
        }
        
        .module-card:hover::after {
            background: rgba(76, 175, 80, 0.05);
        }
        

        
        /* Badge module */
        /* Badge module */
        .module-badge {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Force 3-column grid to bypass CSS cache */
        .modules-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 2rem !important;
            max-width: 1100px !important;
            margin: 0 auto !important;
        }
        
        @media (max-width: 992px) {
            .modules-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (max-width: 576px) {
            .modules-grid {
                grid-template-columns: 1fr !important;
            }
        }
        
        body {
            overflow: auto !important;
            background: var(--gray-light);
        }
    </style>
</head>
<body style="display: block !important;">

    <!-- ========== HERO SECTION ========== -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue sur <span class="highlight">NutriLoop AI</span></h1>
            <p>L'intelligence artificielle au service de votre alimentation pour une santé optimale et un avenir durable.</p>
            <div class="hero-buttons">
                <a href="/NutriLoop_PWW/views/BackOffice/index.html" class="btn-primary">Accéder au Dashboard</a>
                <a href="#modules" class="btn-outline">Découvrir les modules</a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <h3>5000+</h3>
                <p>Utilisateurs actifs</p>
            </div>
            <div class="stat">
                <h3>1000+</h3>
                <p>Recettes IA</p>
            </div>
            <div class="stat">
                <h3>500+</h3>
                <p>Articles nutrition</p>
            </div>
        </div>
    </section>

    <!-- ========== FEATURES SECTION ========== -->
    <section id="features" class="features">
        <div class="section-header">
            <h2 class="section-title">Pourquoi choisir NutriLoop AI ?</h2>
            <p>Une solution complète pour une nutrition intelligente et personnalisée</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h3>IA Avancée</h3>
                <p>Recommandations personnalisées basées sur vos habitudes alimentaires.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Eco-Score</h3>
                <p>Évaluez l'impact environnemental de vos repas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Suivi en temps réel</h3>
                <p>Suivez vos progrès et atteignez vos objectifs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Anti-Gaspi</h3>
                <p>Recettes intelligentes pour réduire le gaspillage.</p>
            </div>
        </div>
    </section>

    <!-- ========== MODULES SECTION ========== -->
    <section id="modules" class="modules">
        <div class="section-header">
            <h2 class="section-title">Nos 6 Modules</h2>
            <p>Cliquez sur un module pour y accéder directement</p>
        </div>
        <div class="modules-grid">
            <div class="module-card" data-url="#">
                <div class="module-icon"><i class="fas fa-users"></i></div>
                <h3>Gestion Utilisateurs</h3>
                <p>Gérez les profils, rôles et statistiques des utilisateurs.</p>
                <span class="module-badge">Module 1</span>
            </div>
            <div class="module-card" data-url="#">
                <div class="module-icon"><i class="fas fa-apple-alt"></i></div>
                <h3>Nutrition Smart</h3>
                <p>Analyse nutritionnelle et recommandations IA.</p>
                <span class="module-badge">Module 2</span>
            </div>
            <div class="module-card" data-url="#">
                <div class="module-icon"><i class="fas fa-boxes"></i></div>
                <h3>Gestion Produits</h3>
                <p>Base de données des aliments et catégories.</p>
                <span class="module-badge">Module 3</span>
            </div>
            <div class="module-card" data-url="afficherRecette.php">
                <div class="module-icon"><i class="fas fa-book-open"></i></div>
                <h3>Recettes Anti-Gaspi</h3>
                <p>Créez et gérez des recettes intelligentes.</p>
                <span class="module-badge">Module 4</span>
            </div>
            <div class="module-card" data-url="afficherObjectif.php">
                <div class="module-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Suivi & Objectifs</h3>
                <p>Tableau de bord personnalisé des progrès.</p>
                <span class="module-badge">Module 5</span>
            </div>
            <div class="module-card" data-url="#">
                <div class="module-icon"><i class="fas fa-calendar-alt"></i></div>
                <h3>Gestion Événements</h3>
                <p>Organisez des ateliers et challenges nutritionnels.</p>
                <span class="module-badge">Module 6</span>
            </div>
        </div>
    </section>

    <!-- ========== TESTIMONIALS ========== -->
    <section class="testimonials">
        <div class="section-header">
            <h2 class="section-title">Ce que disent nos utilisateurs</h2>
        </div>
        <div class="testimonials-slider">
            <div class="testimonial">
                <div class="testimonial-content">
                    <i class="fas fa-quote-left"></i>
                    <p>"NutriLoop AI a transformé ma façon de manger. Les recommandations sont précises et adaptées à mon mode de vie."</p>
                    <div class="testimonial-author">
                        <img src="https://ui-avatars.com/api/?name=Sarah+M&background=random" alt="Sarah M." onerror="this.src='https://via.placeholder.com/60x60?text=S'">
                        <div>
                            <h4>Sarah M.</h4>
                            <p>Utilisatrice premium</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== NEWSLETTER ========== -->
    <section class="newsletter">
        <div class="newsletter-content">
            <h3>Restez informé</h3>
            <p>Recevez nos dernières actualités et conseils nutritionnels</p>
            <form class="newsletter-form" onsubmit="event.preventDefault();">
                <input type="email" placeholder="Votre email" required>
                <button type="submit">S'abonner</button>
            </form>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>NutriLoop AI</h4>
                <p>L'intelligence artificielle au service de votre assiette pour une meilleure santé et un monde plus durable.</p>
                <div class="social-links" style="margin-top: 1rem;">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="home.php">Accueil</a></li>
                    <li><a href="#features">Fonctionnalités</a></li>
                    <li><a href="#modules">Modules</a></li>
                    <li><a href="about.html">À propos</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Modules</h4>
                <ul>
                    <li><a href="#">Utilisateurs</a></li>
                    <li><a href="#">Nutrition</a></li>
                    <li><a href="#">Produits</a></li>
                    <li><a href="afficherRecette.php">Recettes Anti-Gaspi</a></li>
                    <li><a href="afficherObjectif.php">Suivi</a></li>
                    <li><a href="#">Événements</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> Tunis, Tunisie</li>
                    <li><i class="fas fa-envelope"></i> contact@nutriloop.ai</li>
                    <li><i class="fas fa-phone"></i> +216 70 000 000</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 NutriLoop AI - Tous droits réservés | Projet étudiant - Ryhem Mejri</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        // ========== CARTES CLIQUABLES ==========
        document.addEventListener('DOMContentLoaded', function() {
            const moduleCards = document.querySelectorAll('.module-card');
            
            moduleCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Empêcher le clic de remonter si on clique sur un lien à l'intérieur
                    if (e.target.closest('a')) return;
                    
                    const url = this.getAttribute('data-url');
                    const moduleName = this.querySelector('h3')?.innerText || 'Module';
                    
                    if (url && url !== '#') {
                        // Si on est dans un iframe, recharger dans le même iframe
                        if (window.self !== window.top) {
                            window.location.href = url;
                        } else {
                            // Sinon, ouvrir un nouvel onglet
                            window.open(url, '_self');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>