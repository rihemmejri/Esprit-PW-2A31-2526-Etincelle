<?php
require_once '../../config.php';
?>
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
        
        /* Indicateur cliquable */
        .click-hint {
            position: absolute;
            bottom: 15px;
            right: 20px;
            font-size: 0.7rem;
            color: #4CAF50;
            opacity: 0;
            transition: 0.3s;
        }
        
        .module-card:hover .click-hint {
            opacity: 1;
            transform: translateX(-5px);
        }
        
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
                
        /* Logo image */
        .logo-img {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
        }

        /* ========== IFRAME CONTAINER ========== */
        .main-wrapper {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 80px);
        }

        .iframe-container {
            flex: 1;
            overflow: hidden;
            background: var(--gray-light);
        }

        .content-iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
        }
    </style>
</head>
<body>

    <!-- ========== HEADER ========== -->
<header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
                <span class="logo-text">NutriLoop </span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="home.php" class="nav-link active" data-page="home">Accueil</a></li>
                <li><a href="home.php#features" class="nav-link" data-page="features">Fonctionnalités</a></li>
                <li><a href="home.php#modules" class="nav-link" data-page="modules">Modules</a></li>
                <li><a href="about.html" class="nav-link" data-page="about">À propos</a></li>
                <li><a href="contact.html" class="nav-link" data-page="contact">Contact</a></li>
                <li><a href="/NutriLoop_PWW/views/BackOffice/index.html" class="btn-dashboard">Dashboard</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- ========== MAIN IFRAME CONTAINER ========== -->
    <div class="iframe-container">
        <iframe id="contentIframe" class="content-iframe" src="home.php"></iframe>
    </div>

    <script src="js/main.js"></script>
    <script>
        (function() {
            const contentIframe = document.getElementById('contentIframe');
            const navMenuEl = document.getElementById('navMenu');
            const hamburgerEl = document.getElementById('hamburger');

            navMenuEl.addEventListener('click', function(e) {
                const link = e.target.closest('a.nav-link');
                if (!link) return;
                
                e.preventDefault();
                const href = link.getAttribute('href');
                
                if (href && href !== '#') {
                    const parts = href.split('#');
                    const page = parts[0];
                    const hash = parts[1];
                    
                    let currentSrc = contentIframe.src || '';
                    let currentPage = currentSrc.substring(currentSrc.lastIndexOf('/') + 1).split('#')[0];
                    if (!currentPage) currentPage = 'home.php';
                    
                    const targetPage = page || currentPage;

                    if (targetPage === currentPage && hash) {
                        try {
                            const targetElement = contentIframe.contentWindow.document.getElementById(hash);
                            if (targetElement) {
                                targetElement.scrollIntoView({ behavior: 'smooth' });
                            } else {
                                contentIframe.src = href;
                            }
                        } catch(err) {
                            contentIframe.src = href;
                        }
                    } else {
                        contentIframe.src = href;
                    }
                    
                    navMenuEl.querySelectorAll('a.nav-link').forEach(a => a.classList.remove('active'));
                    link.classList.add('active');
                    navMenuEl.classList.remove('active');
                    hamburgerEl.classList.remove('active');
                }
            });

            hamburgerEl.addEventListener('click', function() {
                navMenuEl.classList.toggle('active');
                hamburgerEl.classList.toggle('active');
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.navbar')) {
                    navMenuEl.classList.remove('active');
                    hamburgerEl.classList.remove('active');
                }
            });
        })();
    </script>
</body>
</html>