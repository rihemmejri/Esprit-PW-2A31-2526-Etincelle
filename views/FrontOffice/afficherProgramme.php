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

        /* ========== HEADER - VERT ========== */
        .header {
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 10px 20px rgba(46,125,50,0.3);
        }

        .logo h1 {
            font-size: 1.5rem;
            color: white;
        }

        .logo span {
            color: #A5D6A7;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-leaf"></i></div>
                <h1>Nutri<span>Loop</span> AI</h1>
            </div>
            <div class="logo">
                <i class="fas fa-robot" style="color: #A5D6A7;"></i>
                <span style="color: white;">Nutrition Smart</span>
            </div>
        </div>
    </div>

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
    </script>
</body>
</html>