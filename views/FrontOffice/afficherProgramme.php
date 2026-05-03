<?php
include '../../controleurs/ProgrammeController.php';
include '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/programme.php';

$programmeController = new ProgrammeController();
$repasController = new RepasController();

$programmes = $programmeController->listProgrammes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Smart - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
        }

        /* ========== HEADER AVEC MENU (COMME L'AUTRE PAGE) ========== */
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
            max-width: 1400px;
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

        .nav-menu li a:hover,
        .nav-menu li a.active {
            color: #4CAF50;
        }

        .btn-dashboard {
            background: #2e7d32;
            color: white !important;
            padding: 8px 20px;
            border-radius: 25px;
        }

        .btn-dashboard:hover {
            background: #388e3c;
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

        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background: white;
                width: 100%;
                text-align: center;
                transition: 0.3s;
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                padding: 20px 0;
                gap: 15px;
            }
            .nav-menu.active {
                left: 0;
            }
            .hamburger {
                display: flex;
            }
        }

        /* ========== HERO - VERT ========== */
        .hero {
            background: linear-gradient(135deg, #2E7D32, #1B5E20, #0D47A1);
            margin: 30px 30px 40px 30px;
            border-radius: 30px;
            padding: 50px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .hero h1 {
            font-size: 2rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .hero p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        /* ========== STATS SECTION ========== */
        .stats-section {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: -30px 30px 40px 30px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: white;
            padding: 20px 35px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #666;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        /* ========== FILTRES ========== */
        .filters {
            max-width: 1400px;
            margin: 0 auto 40px auto;
            padding: 0 30px;
        }

        .filters-container {
            background: white;
            border-radius: 20px;
            padding: 20px 25px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #2E7D32;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }

        /* ========== GRILLE DES PROGRAMMES ========== */
        .programmes-grid {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px 50px 30px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
        }

        /* ========== CARTE PROGRAMME ========== */
        .programme-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .programme-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        /* En-tête avec image selon objectif */
        .programme-header {
            position: relative;
            height: 140px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
            padding: 20px;
            color: white;
        }

        .programme-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.3));
            border-radius: 25px 25px 0 0;
        }

        .header-perte-poids { background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=400&fit=crop'); }
        .header-prise-muscle { background-image: url('https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=800&h=400&fit=crop'); }
        .header-maintien { background-image: url('https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=800&h=400&fit=crop'); }
        .header-equilibre { background-image: url('https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=400&fit=crop'); }
        .header-default { background-image: url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=400&fit=crop'); }

        .programme-header-content {
            position: relative;
            z-index: 1;
        }

        .programme-header h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .programme-dates {
            font-size: 0.7rem;
            opacity: 0.9;
        }

        /* Corps de la carte */
        .programme-body {
            padding: 20px;
        }

        /* Stats mini */
        .programme-stats {
            display: flex;
            justify-content: space-around;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .stat-mini {
            text-align: center;
        }

        .stat-mini-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2E7D32;
        }

        .stat-mini-label {
            font-size: 0.6rem;
            color: #666;
        }

        /* Repas par jour */
        .jour-title {
            font-weight: 600;
            color: #2E7D32;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #A5D6A7;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .repas-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #eee;
        }

        .repas-item:hover {
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
            transform: translateX(5px);
            border-color: #4CAF50;
        }

        .repas-nom {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .repas-type-badge {
            font-size: 0.65rem;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .type-matin { background: #E8F5E9; color: #2E7D32; }
        .type-midi { background: #E8F5E9; color: #2E7D32; }
        .type-soir { background: #E8F5E9; color: #2E7D32; }

        .repas-calories {
            background: #FF9800;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .total-programme {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #eee;
            text-align: right;
            font-weight: 700;
            color: #e65100;
            font-size: 1rem;
        }

        .click-hint {
            font-size: 0.6rem;
            color: #999;
            margin-left: 8px;
        }

        /* ========== MODAL ========== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 25px;
            max-width: 550px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            color: white;
            padding: 20px;
            border-radius: 25px 25px 0 0;
            position: relative;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .modal-header-image {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.3rem;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.8rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-detail-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-detail-label {
            font-weight: 600;
            color: #2E7D32;
            width: 120px;
            display: inline-block;
        }

        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            margin: 10px 0;
        }

        .btn-fermer {
            background: #2E7D32;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
        }

        .btn-fermer:hover {
            background: #1B5E20;
        }

        .no-data {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 25px;
        }

        /* ========== FOOTER - VERT ========== */
        .footer {
            background: #1B5E20;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 40px;
        }

        /* Style pour le lien actif */
        .nav-menu li a.active {
            color: #4CAF50;
            font-weight: 600;
        }
        
        /* ========== CHATBOT GEMINI ========== */
        .chatbot-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            animation: bounce 2s infinite;
        }

        .chatbot-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }

        .chat-window {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            height: 500px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .chat-window.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .chat-header {
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h3 {
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header h3 i {
            font-size: 1.3rem;
            color: #A5D6A7;
        }

        .close-chat {
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.2s;
        }

        .close-chat:hover {
            transform: rotate(90deg);
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #f8f9fa;
        }

        .message {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            line-height: 1.4;
            animation: fadeIn 0.3s ease;
        }

        .message.bot {
            background: white;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .message.user {
            background: #2E7D32;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
            box-shadow: 0 2px 5px rgba(46,125,50,0.2);
        }

        .chat-input {
            padding: 15px;
            background: white;
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
        }

        .chat-input input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            outline: none;
            transition: border-color 0.3s;
        }

        .chat-input input:focus {
            border-color: #2E7D32;
        }

        .chat-input button {
            background: #2E7D32;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background 0.3s;
        }

        .chat-input button:hover {
            background: #1B5E20;
        }
        
        .typing-indicator {
            display: none;
            padding: 10px 15px;
            background: white;
            border-radius: 15px;
            border-bottom-left-radius: 5px;
            align-self: flex-start;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: #ccc;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: typing 1.4s infinite ease-in-out both;
        }
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            background: white;
            border-left: 4px solid #4CAF50;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 250px;
            animation: slideIn 0.3s ease forwards;
            transition: opacity 0.3s;
        }
        .toast i {
            color: #4CAF50;
            font-size: 1.2rem;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- ========== HEADER AVEC MENU (COMME L'AUTRE PAGE) ========== -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
                <span class="logo-text">NutriLoop</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">Accueil</a></li>
                <li><a href="index.html#features">Fonctionnalités</a></li>
                <li><a href="index.html#modules">Modules</a></li>
                <li><a href="about.html">À propos</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="../backoffice/index.html" class="btn-dashboard">Dashboard</a></li>
                <li style="margin-top: 5px;"><div id="google_translate_element"></div></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <div class="hero">
        <h1><i class="fas fa-calendar-alt"></i> Programmes Nutritionnels</h1>
        <p>Découvrez nos programmes personnalisés • Cliquez sur un repas pour voir ses détails</p>
    </div>

    <?php
    $totalProgrammes = count($programmes);
    $totalRepas = 0;
    $totalCalories = 0;
    foreach ($programmes as $p) {
        $repasListe = $p->getRepas();
        $totalRepas += count($repasListe);
        foreach ($repasListe as $r) {
            $totalCalories += $r['calories'] ?? 0;
        }
    }
    ?>

    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-number"><?= $totalProgrammes ?></div>
            <div class="stat-label">Programmes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $totalRepas ?></div>
            <div class="stat-label">Repas inclus</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $totalCalories ?></div>
            <div class="stat-label">Kcal total</div>
        </div>
    </div>

    <div class="filters">
        <div class="filters-container">
            <div class="filter-group">
                <label><i class="fas fa-search"></i> Rechercher</label>
                <input type="text" id="searchInput" placeholder="Nom du programme..." onkeyup="filterProgrammes()">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-bullseye"></i> Objectif</label>
                <select id="filterObjectif" onchange="filterProgrammes()">
                    <option value="">Tous</option>
                    <option value="PERDRE_POIDS">🔥 Perte de poids</option>
                    <option value="PRENDRE_MUSCLE">💪 Prise de muscle</option>
                    <option value="MAINTENIR">⚖️ Maintien</option>
                    <option value="EQUILIBRE">🥗 Équilibre</option>
                </select>
            </div>
        </div>
    </div>

    <div class="programmes-grid" id="programmesGrid">
        <?php if ($totalProgrammes > 0): ?>
            <?php foreach ($programmes as $programme): 
                $repasListe = $programme->getRepas();
                $totalCaloriesProg = 0;
                foreach ($repasListe as $r) {
                    $totalCaloriesProg += $r['calories'] ?? 0;
                }
                
                $repasParJour = [];
                $ordreJours = ['LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI', 'DIMANCHE'];
                foreach ($repasListe as $item) {
                    $jour = $item['jour_semaine'];
                    if (!isset($repasParJour[$jour])) $repasParJour[$jour] = [];
                    $repasParJour[$jour][] = $item;
                }
                
                $objectifKey = '';
                $objectifText = '';
                $objectifIcon = '';
                $headerClass = 'header-default';
                
                switch($programme->getObjectif()) {
                    case 'PERDRE_POIDS': 
                        $objectifKey = 'PERDRE_POIDS';
                        $objectifText = 'Perte de poids'; 
                        $objectifIcon = '🔥'; 
                        $headerClass = 'header-perte-poids';
                        break;
                    case 'PRENDRE_MUSCLE': 
                        $objectifKey = 'PRENDRE_MUSCLE';
                        $objectifText = 'Prise de muscle'; 
                        $objectifIcon = '💪'; 
                        $headerClass = 'header-prise-muscle';
                        break;
                    case 'MAINTENIR': 
                        $objectifKey = 'MAINTENIR';
                        $objectifText = 'Maintien'; 
                        $objectifIcon = '⚖️'; 
                        $headerClass = 'header-maintien';
                        break;
                    case 'EQUILIBRE': 
                        $objectifKey = 'EQUILIBRE';
                        $objectifText = 'Équilibre'; 
                        $objectifIcon = '🥗'; 
                        $headerClass = 'header-equilibre';
                        break;
                    default: 
                        $objectifKey = '';
                        $objectifText = $programme->getObjectif(); 
                        $objectifIcon = '🎯';
                }
            ?>
                <div class="programme-card" data-objectif="<?= $objectifKey ?>">
                    <div class="programme-header <?= $headerClass ?>">
                        <div class="programme-header-content">
                            <h3><?= $objectifIcon ?> <?= $objectifText ?></h3>
                            <div class="programme-dates">
                                <i class="fas fa-calendar-alt"></i> <?= $programme->getDateDebut() ?> → <?= $programme->getDateFin() ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="programme-body">
                        <div class="programme-stats">
                            <div class="stat-mini">
                                <div class="stat-mini-value"><?= count($repasListe) ?></div>
                                <div class="stat-mini-label">Repas</div>
                            </div>
                            <div class="stat-mini">
                                <div class="stat-mini-value"><?= $totalCaloriesProg ?></div>
                                <div class="stat-mini-label">Kcal</div>
                            </div>
                            <div class="stat-mini">
                                <div class="stat-mini-value"><?= round($totalCaloriesProg / max(1, count($repasListe))) ?></div>
                                <div class="stat-mini-label">Moy/repas</div>
                            </div>
                        </div>
                        
                        <?php foreach ($repasParJour as $jour => $repasDuJour): ?>
                            <div class="jour-title">
                                <i class="fas fa-calendar-day"></i> <?= ucfirst(strtolower($jour)) ?>
                            </div>
                            <?php foreach ($repasDuJour as $item): 
                                $typeLabel = '';
                                $typeClass = '';
                                switch($item['type_repas']) {
                                    case 'PETIT_DEJEUNER': $typeLabel = '☀️ Matin'; $typeClass = 'type-matin'; break;
                                    case 'DEJEUNER': $typeLabel = '🍽️ Midi'; $typeClass = 'type-midi'; break;
                                    case 'DINER': $typeLabel = '🌙 Soir'; $typeClass = 'type-soir'; break;
                                    default: $typeLabel = '🍎 Collation'; $typeClass = '';
                                }
                            ?>
                                <div class="repas-item" onclick='showRepasDetails(<?= json_encode($item) ?>)'>
                                    <div class="repas-nom">
                                        <i class="fas fa-utensils"></i>
                                        <?= htmlspecialchars($item['nom'] ?? 'Repas') ?>
                                        <span class="repas-type-badge <?= $typeClass ?>"><?= $typeLabel ?></span>
                                        <span class="click-hint"><i class="fas fa-mouse-pointer"></i> cliquer</span>
                                    </div>
                                    <div class="repas-calories">
                                        <?= $item['calories'] ?? 0 ?> kcal
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        
                        <div class="total-programme">
                            <i class="fas fa-chart-line"></i> Total: <?= $totalCaloriesProg ?> kcal
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-empty-folder" style="font-size: 4rem; color: #ccc;"></i>
                <h3>Aucun programme disponible</h3>
                <p>Revenez plus tard pour découvrir nos programmes personnalisés !</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p><i class="fas fa-heart" style="color: #A5D6A7;"></i> NutriLoop AI - Manger sainement pour une vie meilleure</p>
    </div>

    <!-- Modal Détails du Repas -->
    <div id="repasModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <!-- Image sera ajoutée ici dynamiquement -->
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div style="padding: 20px; border-top: 1px solid #eee;">
                <button class="btn-fermer" onclick="closeModal()">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Chatbot Widget -->
    <div class="chatbot-btn" id="chatbotBtn" onclick="toggleChat()">
        <i class="fas fa-robot"></i>
    </div>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <h3><i class="fas fa-leaf"></i> NutriLoop Assistant</h3>
            <i class="fas fa-times close-chat" onclick="toggleChat()"></i>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                Bonjour ! Je suis l'assistant expert de NutriLoop. Comment puis-je vous aider aujourd'hui avec votre nutrition ou nos programmes ?
            </div>
            <div class="typing-indicator" id="typingIndicator">
                <span></span><span></span><span></span>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Posez votre question..." onkeypress="handleEnter(event)">
            <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        // Mapping des images pour les repas
        const repasImages = {
            'salade': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=150&h=150&fit=crop',
            'poulet': 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=150&h=150&fit=crop',
            'toast': 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=150&h=150&fit=crop',
            'poisson': 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=150&h=150&fit=crop',
            'pizza': 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=150&h=150&fit=crop',
            'pasta': 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=150&h=150&fit=crop',
            'riz': 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=150&h=150&fit=crop',
            'soupe': 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=150&h=150&fit=crop',
            'porridge': 'https://images.unsplash.com/photo-1517673132405-a56a62b18caf?w=150&h=150&fit=crop',
            'bowl': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=150&h=150&fit=crop',
            'oeuf': 'https://images.unsplash.com/photo-1506976785307-8732e854ad03?w=150&h=150&fit=crop',
            'default': 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=150&h=150&fit=crop'
        };

        function getRepasImage(nom) {
            const nomLower = nom.toLowerCase();
            for (const [key, url] of Object.entries(repasImages)) {
                if (nomLower.includes(key)) {
                    return url;
                }
            }
            return repasImages['default'];
        }

        function showRepasDetails(repas) {
            const modal = document.getElementById('repasModal');
            const modalHeader = document.getElementById('modalHeader');
            const modalBody = document.getElementById('modalBody');
            
            const imageUrl = getRepasImage(repas.nom);
            
            let typeIcon = '', typeText = '';
            switch(repas.type_repas) {
                case 'PETIT_DEJEUNER': typeIcon = '☕'; typeText = 'Petit déjeuner'; break;
                case 'DEJEUNER': typeIcon = '🍽️'; typeText = 'Déjeuner'; break;
                case 'DINER': typeIcon = '🌙'; typeText = 'Dîner'; break;
                case 'COLLATION': typeIcon = '🍎'; typeText = 'Collation'; break;
                default: typeIcon = '🍴'; typeText = repas.type_repas;
            }
            
            const calories = repas.calories || 0;
            const proteines = repas.proteines || 0;
            const glucides = repas.glucides || 0;
            const lipides = repas.lipides || 0;
            const pctP = calories > 0 ? Math.round(proteines * 4 / calories * 100) : 0;
            const pctG = calories > 0 ? Math.round(glucides * 4 / calories * 100) : 0;
            const pctL = calories > 0 ? Math.round(lipides * 9 / calories * 100) : 0;
            
            modalHeader.innerHTML = `
                <img src="${imageUrl}" alt="${repas.nom}" class="modal-header-image" onerror="this.src='${repasImages['default']}'">
                <div>
                    <h2><i class="fas fa-info-circle"></i> ${repas.nom}</h2>
                    <p style="opacity: 0.9; font-size: 0.85rem;">${typeIcon} ${typeText}</p>
                </div>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            `;
            
            modalBody.innerHTML = `
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-fire"></i> Calories :</span>
                    <span class="modal-detail-value"><strong style="color:#e65100;">${calories} kcal</strong></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-dumbbell"></i> Protéines :</span>
                    <span class="modal-detail-value">${proteines} g (${pctP}%)</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-bread-slice"></i> Glucides :</span>
                    <span class="modal-detail-value">${glucides} g (${pctG}%)</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-oil-can"></i> Lipides :</span>
                    <span class="modal-detail-value">${lipides} g (${pctL}%)</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label"><i class="fas fa-chart-pie"></i> Répartition :</span>
                    <div class="progress-bar">
                        <div style="background: #4CAF50; width: ${pctP}%; height: 12px;"></div>
                        <div style="background: #FF9800; width: ${pctG}%; height: 12px;"></div>
                        <div style="background: #2196F3; width: ${pctL}%; height: 12px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 0.7rem;">
                        <span><span style="color:#4CAF50;">●</span> Protéines ${pctP}%</span>
                        <span><span style="color:#FF9800;">●</span> Glucides ${pctG}%</span>
                        <span><span style="color:#2196F3;">●</span> Lipides ${pctL}%</span>
                    </div>
                </div>
            `;
            
            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('repasModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('repasModal');
            if (event.target === modal) closeModal();
        }

        function filterProgrammes() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filterObjectif = document.getElementById('filterObjectif').value;
            const cards = document.querySelectorAll('.programme-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const objectif = card.getAttribute('data-objectif');
                let show = true;
                
                if (searchTerm && !text.includes(searchTerm)) show = false;
                if (filterObjectif && objectif !== filterObjectif) show = false;
                
                card.style.display = show ? '' : 'none';
            });
        }

        // Menu hamburger pour mobile
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

        // ================= GEMINI CHATBOT LOGIC =================
        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            chatWindow.classList.toggle('active');
            if (chatWindow.classList.contains('active')) {
                document.getElementById('chatInput').focus();
            }
        }

        function handleEnter(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        async function sendMessage() {
            const inputField = document.getElementById('chatInput');
            const message = inputField.value.trim();
            if (!message) return;

            // Clear input
            inputField.value = '';

            // Add user message to UI
            addMessageToUI(message, 'user');

            // Show typing indicator
            const typingIndicator = document.getElementById('typingIndicator');
            typingIndicator.style.display = 'block';
            
            // Scroll to bottom
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.appendChild(typingIndicator); // move to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                // Call backend API
                const response = await fetch('api/gemini_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                // Hide typing indicator
                typingIndicator.style.display = 'none';

                if (response.ok) {
                    addMessageToUI(data.reply, 'bot');
                    
                    if (data.proposedProgram) {
                        window.pendingProgram = data.proposedProgram;
                        const btnHtml = `<br><button id="confirmBtn" onclick="confirmAddProgram()" style="margin-top: 10px; padding: 8px 12px; background: #A5D6A7; color: #333; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%;"><i class="fas fa-save"></i> Ajouter ce programme à ma liste</button>`;
                        addMessageToUI(btnHtml, 'bot', false); // Do not save the button HTML to history
                    }
                } else {
                    addMessageToUI("Désolé, une erreur est survenue lors de la communication avec l'assistant. (" + (data.error || 'Erreur inconnue') + ")", 'bot');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                addMessageToUI("Erreur de connexion. Veuillez réessayer plus tard.", 'bot');
            }
        }

        function addMessageToUI(text, sender, save = true) {
            const chatMessages = document.getElementById('chatMessages');
            const typingIndicator = document.getElementById('typingIndicator');
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message ' + sender;
            // Basic markdown-like bold parsing
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // Line breaks
            formattedText = formattedText.replace(/\n/g, '<br>');
            messageDiv.innerHTML = formattedText;
            
            chatMessages.insertBefore(messageDiv, typingIndicator);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            if (save) {
                saveChatHistory(text, sender);
            }
        }

        function saveChatHistory(text, sender) {
            let history = JSON.parse(localStorage.getItem('nutriloop_chat_history') || '[]');
            history.push({ text, sender });
            // Keep only last 50 messages to avoid huge storage
            if (history.length > 50) history = history.slice(-50);
            localStorage.setItem('nutriloop_chat_history', JSON.stringify(history));
        }

        function loadChatHistory() {
            const history = JSON.parse(localStorage.getItem('nutriloop_chat_history') || '[]');
            if (history.length > 0) {
                // Clear default greeting if we have history
                const defaultMsg = document.querySelector('.chat-messages .message.bot:first-child');
                if (defaultMsg && history.length > 0) {
                    defaultMsg.remove();
                }
                
                history.forEach(msg => {
                    addMessageToUI(msg.text, msg.sender, false); // false to not save again
                });
            }
        }

        // Load history on page load
        document.addEventListener('DOMContentLoaded', loadChatHistory);

        async function confirmAddProgram() {
            if (!window.pendingProgram) return;
            const btn = document.getElementById('confirmBtn');
            if(btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde en cours...';
            }

            try {
                const response = await fetch('api/save_program.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(window.pendingProgram)
                });
                
                const data = await response.json();
                if (response.ok && data.success) {
                    if(btn) {
                        btn.innerHTML = '<i class="fas fa-check"></i> Programme ajouté !';
                        btn.style.background = '#81C784';
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert("Erreur lors de l'ajout du programme : " + (data.error || 'Erreur inconnue'));
                    if(btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-save"></i> Réessayer';
                    }
                }
            } catch (error) {
                alert("Erreur de connexion au serveur.");
                if(btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save"></i> Réessayer';
                }
            }
        }

        // Notification Polling System
        let lastProgramId = 0;

        function showToast(message) {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fas fa-bell"></i> <div>${message}</div>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function checkNewPrograms() {
            fetch('../BackOffice/api/check_new_programs.php?last_id=' + lastProgramId)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (lastProgramId === 0) {
                            lastProgramId = data.last_id;
                        } else if (data.new_programs && data.new_programs.length > 0) {
                            lastProgramId = data.last_id;
                            data.new_programs.forEach(prog => {
                                showToast(`Un nouveau programme a été généré avec succès !`);
                            });
                        }
                    }
                })
                .catch(err => console.error("Erreur de notification:", err));
        }

        setInterval(checkNewPrograms, 5000); // Check every 5 seconds
        checkNewPrograms();
    </script>
</body>
</html>