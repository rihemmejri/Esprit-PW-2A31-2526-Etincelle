<?php
session_start();
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

// ========== FONCTION POUR LE SYMBOLE CRÉATIF DE LA NOTIFICATION ==========
function getSymboleRecette($nomRecette) {
    $nom = strtolower($nomRecette);
    
    // SYMBOLES CRÉATIFS PAR TYPE DE RECETTE
    if (strpos($nom, 'pizza') !== false) return '🍕🔥';  // Pizza en feu
    if (strpos($nom, 'tajine') !== false) return '🏺🍲'; // Tajine marocain
    if (strpos($nom, 'salade') !== false) return '🥗✨'; // Salade fraîche
    if (strpos($nom, 'gateau') !== false || strpos($nom, 'cake') !== false || strpos($nom, 'gâteau') !== false) return '🎂🎉'; // Gâteau fête
    if (strpos($nom, 'pasta') !== false || strpos($nom, 'spaghetti') !== false || strpos($nom, 'pâtes') !== false) return '🍝🍅'; // Pâtes italiennes
    if (strpos($nom, 'poulet') !== false) return '🍗🔥'; // Poulet grillé
    if (strpos($nom, 'poisson') !== false) return '🐟🌊'; // Poisson de mer
    if (strpos($nom, 'burger') !== false) return '🍔💨'; // Burger chaud
    if (strpos($nom, 'crepe') !== false || strpos($nom, 'crêpe') !== false) return '🥞🍯'; // Crêpe au miel
    if (strpos($nom, 'couscous') !== false) return '🍛🌟'; // Couscous royal
    if (strpos($nom, 'tiramisu') !== false) return '☕🍰'; // Tiramisu café
    if (strpos($nom, 'omelette') !== false || strpos($nom, 'oeuf') !== false || strpos($nom, 'œuf') !== false) return '🍳❤️'; // Omelette cœur
    if (strpos($nom, 'riz') !== false) return '🍚🥢'; // Riz baguettes
    if (strpos($nom, 'glace') !== false || strpos($nom, 'ice cream') !== false) return '🍦❄️'; // Glace froide
    if (strpos($nom, 'soupe') !== false) return '🥣🔥'; // Soupe chaude
    if (strpos($nom, 'sandwich') !== false) return '🥪🧀'; // Sandwich fromage
    if (strpos($nom, 'sushi') !== false) return '🍣🥢'; // Sushi japonais
    if (strpos($nom, 'fondue') !== false) return '🍲🧀'; // Fondue fromage
    if (strpos($nom, 'raclette') !== false) return '🧀🥔'; // Raclette pommes
    if (strpos($nom, 'quiche') !== false) return '🥧🍳'; // Quiche lorraine
    if (strpos($nom, 'smoothie') !== false) return '🥤🍓'; // Smoothie fruits
    
    // SYMBOLES PAR DIFFICULTÉ
    $diff = strtolower($nom);
    if (strpos($diff, 'facile') !== false || strpos($diff, 'simple') !== false) return '😊👌';
    if (strpos($diff, 'moyen') !== false) return '🤔⚡';
    if (strpos($diff, 'difficile') !== false || strpos($diff, 'compliqué') !== false) return '😤💪';
    
    // SYMBOLES PAR TEMPS
    if (strpos($nom, 'rapide') !== false || strpos($nom, 'quick') !== false) return '⚡⏱️';
    if (strpos($nom, 'long') !== false) return '🐌⏲️';
    
    // SYMBOLE PAR DÉFAUT CRÉATIF
    return '🍽️✨';
}

// ========== LOGIQUE INTELLIGENTE SPÉCIFIQUE POUR CHAQUE TYPE DE RECETTE ==========
function getEtapesSpecifiquesRecette($nomRecette, $etapesExistantes) {
    $nom = strtolower($nomRecette);
    $etapesRequis = [];
    
    // Détecter les types d'étapes déjà présentes
    $existant = [];
    foreach ($etapesExistantes as $e) {
        $inst = strtoupper($e->getInstruction());
        $action = strtoupper($e->getTypeAction());
        if (strpos($inst, 'COUPER') !== false || strpos($inst, 'PRÉPARER') !== false) $existant[] = 'PREPARATION';
        if (strpos($inst, 'CUIRE') !== false || strpos($inst, 'FOUR') !== false) $existant[] = 'CUISSON';
        if (strpos($inst, 'MÉLANGER') !== false || strpos($inst, 'MELANGER') !== false) $existant[] = 'MELANGE';
        if (strpos($inst, 'SERVIR') !== false || strpos($inst, 'DÉCORER') !== false) $existant[] = 'FINITION';
        if (strpos($inst, 'PÉTRIR') !== false || strpos($inst, 'PETRIR') !== false) $existant[] = 'PETRISSAGE';
    }
    
    // === LOGIQUE SPÉCIFIQUE POUR PIZZA ===
    if (strpos($nom, 'pizza') !== false) {
        $etapesRequis = [
            ['nom' => '🍕 Préparation de la pâte', 'desc' => 'Mélanger farine, eau, levure, sel. Pétrir 10 min.', 'icon' => '🥣', 'keywords' => ['PÉTRIR', 'FARINE', 'PÂTE']],
            ['nom' => '⏲️ Repos de la pâte', 'desc' => 'Laisser reposer 1h jusqu\'à doublement.', 'icon' => '⏰', 'keywords' => ['REPOS', 'DOUBLER']],
            ['nom' => '🍽️ Étaler la pâte', 'desc' => 'Étaler en cercle sur plan fariné.', 'icon' => '👩‍🍳', 'keywords' => ['ÉTALER', 'ABBAISSER']],
            ['nom' => '🍅 Garniture', 'desc' => 'Ajouter sauce tomate, fromage, garnitures.', 'icon' => '🧀', 'keywords' => ['GARNIR', 'SAUCE', 'FROMAGE']],
            ['nom' => '🔥 Cuisson au four', 'desc' => 'Enfourner à 250°C pendant 10-12 min.', 'icon' => '🔥', 'keywords' => ['FOUR', 'CUIRE']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR GÂTEAU ===
    elseif (strpos($nom, 'gateau') !== false || strpos($nom, 'cake') !== false || strpos($nom, 'gâteau') !== false) {
        $etapesRequis = [
            ['nom' => '🎂 Préparation', 'desc' => 'Sortir œufs et beurre à température ambiante.', 'icon' => '🥚', 'keywords' => ['ŒUFS', 'BEURRE']],
            ['nom' => '🥣 Mélange sec', 'desc' => 'Mélanger farine, levure, sucre, sel.', 'icon' => '🥄', 'keywords' => ['MÉLANGER', 'FARINE']],
            ['nom' => '🫧 Blanchiment', 'desc' => 'Battre œufs avec sucre jusqu\'à blanchiment.', 'icon' => '🥣', 'keywords' => ['BATTRE', 'BLANCHIR']],
            ['nom' => '🥄 Incorporation', 'desc' => 'Ajouter ingrédients liquides aux secs.', 'icon' => '✨', 'keywords' => ['INCORPORER', 'AJOUTER']],
            ['nom' => '🔥 Cuisson', 'desc' => 'Enfourner à 180°C pendant 30-35 min.', 'icon' => '🔥', 'keywords' => ['FOUR', 'CUIRE']],
            ['nom' => '🎨 Décoration', 'desc' => 'Laisser refroidir puis décorer.', 'icon' => '🎨', 'keywords' => ['DÉCORER', 'REFROIDIR']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR TAJINE ===
    elseif (strpos($nom, 'tajine') !== false) {
        $etapesRequis = [
            ['nom' => '🍗 Préparation viande', 'desc' => 'Couper viande en morceaux et assaisonner.', 'icon' => '🍖', 'keywords' => ['COUPER', 'VIANDE']],
            ['nom' => '🥕 Préparation légumes', 'desc' => 'Éplucher et couper les légumes.', 'icon' => '🥕', 'keywords' => ['LÉGUMES', 'ÉPLUCHER']],
            ['nom' => '🔥 Cuisson tajine', 'desc' => 'Mettre ingrédients dans tajine avec eau.', 'icon' => '🍲', 'keywords' => ['TAJINE', 'CUIRE']],
            ['nom' => '⏲️ Mijotage', 'desc' => 'Laisser mijoter à feu doux 1h30.', 'icon' => '⏲️', 'keywords' => ['MIJOTER', 'FEU DOUX']],
            ['nom' => '✨ Finition', 'desc' => 'Parsemer de coriandre fraîche.', 'icon' => '🌿', 'keywords' => ['CORIANDRE', 'SERVIR']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR COUSCOUS ===
    elseif (strpos($nom, 'couscous') !== false) {
        $etapesRequis = [
            ['nom' => '🫘 Préparation semoule', 'desc' => 'Humidifier la semoule et l\'égrener.', 'icon' => '🥣', 'keywords' => ['SEMOULE', 'HUMIDIFIER']],
            ['nom' => '💨 Cuisson vapeur', 'desc' => 'Mettre semoule dans couscoussier.', 'icon' => '💨', 'keywords' => ['VAPEUR', 'COUSCOUSSIER']],
            ['nom' => '🍲 Bouillon', 'desc' => 'Faire cuire viande et légumes pour bouillon.', 'icon' => '🍲', 'keywords' => ['BOUILLON', 'VIANDE']],
            ['nom' => '🌶️ Assaisonnement', 'desc' => 'Ajouter ras el hanout, sel, poivre.', 'icon' => '🌶️', 'keywords' => ['ÉPICES', 'ASSISONNER']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR SALADE ===
    elseif (strpos($nom, 'salade') !== false) {
        $etapesRequis = [
            ['nom' => '🥬 Lavage', 'desc' => 'Laver soigneusement les légumes.', 'icon' => '💧', 'keywords' => ['LAVER', 'LÉGUMES']],
            ['nom' => '🔪 Découpage', 'desc' => 'Couper légumes en petits morceaux.', 'icon' => '🔪', 'keywords' => ['COUPER', 'DÉCOUPER']],
            ['nom' => '🥣 Assaisonnement', 'desc' => 'Préparer vinaigrette huile/vinaigre.', 'icon' => '🥣', 'keywords' => ['VINAIGRETTE', 'ASSISONNER']],
            ['nom' => '🥗 Mélange', 'desc' => 'Mélanger ingrédients avec vinaigrette.', 'icon' => '🥗', 'keywords' => ['MÉLANGER', 'SALADE']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR PÂTES ===
    elseif (strpos($nom, 'pasta') !== false || strpos($nom, 'spaghetti') !== false || strpos($nom, 'pâtes') !== false) {
        $etapesRequis = [
            ['nom' => '💧 Eau bouillante', 'desc' => 'Remplir casserole d\'eau salée, porter ébullition.', 'icon' => '💧', 'keywords' => ['EAU', 'BOUILLIR']],
            ['nom' => '🍝 Cuisson pâtes', 'desc' => 'Plonger pâtes, cuire al dente.', 'icon' => '🍝', 'keywords' => ['PÂTES', 'CUIRE']],
            ['nom' => '🥫 Sauce', 'desc' => 'Préparer sauce tomate/crème/pesto.', 'icon' => '🥫', 'keywords' => ['SAUCE', 'TOMATO']],
            ['nom' => '🥄 Mélange', 'desc' => 'Égoutter et mélanger pâtes avec sauce.', 'icon' => '🥄', 'keywords' => ['MÉLANGER', 'ÉGOUTTER']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR POULET ===
    elseif (strpos($nom, 'poulet') !== false) {
        $etapesRequis = [
            ['nom' => '🧼 Nettoyage', 'desc' => 'Laver et sécher le poulet.', 'icon' => '🧼', 'keywords' => ['LAVER', 'POULET']],
            ['nom' => '🥣 Marinade', 'desc' => 'Mariner avec épices, huile, citron 30min.', 'icon' => '🥣', 'keywords' => ['MARINER', 'ÉPICES']],
            ['nom' => '🔥 Cuisson', 'desc' => 'Cuire à la poêle ou four à 200°C.', 'icon' => '🔥', 'keywords' => ['CUIRE', 'POÊLE']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR BURGER ===
    elseif (strpos($nom, 'burger') !== false) {
        $etapesRequis = [
            ['nom' => '🍔 Façonnage', 'desc' => 'Former steaks avec viande hachée.', 'icon' => '🍔', 'keywords' => ['STEAK', 'FORMER']],
            ['nom' => '🔥 Cuisson steaks', 'desc' => 'Cuire steaks à la poêle/grill.', 'icon' => '🔥', 'keywords' => ['CUIRE', 'GRILL']],
            ['nom' => '🥬 Garnitures', 'desc' => 'Laver salade, trancher tomates/oignons.', 'icon' => '🥬', 'keywords' => ['SALADE', 'TOMATES']],
            ['nom' => '🍔 Assemblage', 'desc' => 'Monter burger: pain, sauce, salade, steak...', 'icon' => '🍔', 'keywords' => ['ASSEMBLER', 'PAIN']]
        ];
    }
    // === LOGIQUE SPÉCIFIQUE POUR CRÊPE ===
    elseif (strpos($nom, 'crepe') !== false || strpos($nom, 'crêpe') !== false) {
        $etapesRequis = [
            ['nom' => '🥣 Préparation pâte', 'desc' => 'Mélanger farine, œufs, lait, beurre fondu.', 'icon' => '🥣', 'keywords' => ['PÂTE', 'MÉLANGER']],
            ['nom' => '⏲️ Repos pâte', 'desc' => 'Laisser reposer la pâte 30 minutes.', 'icon' => '⏰', 'keywords' => ['REPOS', 'ATTENDRE']],
            ['nom' => '🍳 Cuisson crêpes', 'desc' => 'Cuire dans poêle chaude beurrée.', 'icon' => '🍳', 'keywords' => ['CUIRE', 'POÊLE']],
            ['nom' => '🍯 Garniture', 'desc' => 'Ajouter sucre, confiture, Nutella, fruits.', 'icon' => '🍯', 'keywords' => ['GARNIR', 'SUCRE']]
        ];
    }
    // === LOGIQUE DÉFAUT ===
    else {
        $etapesRequis = [
            ['nom' => '🔪 Préparation', 'desc' => 'Laver, éplucher et couper ingrédients.', 'icon' => '🔪', 'keywords' => ['PRÉPARER', 'COUPER']],
            ['nom' => '🔥 Cuisson', 'desc' => 'Cuire selon recette (température/durée).', 'icon' => '🔥', 'keywords' => ['CUIRE']],
            ['nom' => '🥄 Assemblage', 'desc' => 'Mélanger/assembler tous ingrédients.', 'icon' => '🥄', 'keywords' => ['MÉLANGER']],
            ['nom' => '✨ Finition', 'desc' => 'Servir et décorer le plat.', 'icon' => '✨', 'keywords' => ['SERVIR', 'DÉCORER']]
        ];
    }
    
    return $etapesRequis;
}

// Analyser les étapes manquantes spécifiques
$etapesSpecifiques = getEtapesSpecifiquesRecette($recette->getNom(), $etapes);
$etapesManquantesSpecifiques = [];
$etapesManquantesDetails = [];

foreach ($etapesSpecifiques as $etapeSpec) {
    $trouvee = false;
    foreach ($etapes as $etape) {
        $instruction = strtoupper($etape->getInstruction());
        $action = strtoupper($etape->getTypeAction());
        foreach ($etapeSpec['keywords'] as $keyword) {
            if (strpos($instruction, $keyword) !== false || strpos($action, $keyword) !== false) {
                $trouvee = true;
                break;
            }
        }
        if ($trouvee) break;
    }
    if (!$trouvee) {
        $etapesManquantesSpecifiques[] = $etapeSpec['icon'] . ' ' . $etapeSpec['nom'];
        $etapesManquantesDetails[] = '   ➤ ' . $etapeSpec['desc'];
    }
}

$hasMissingSteps = count($etapesManquantesSpecifiques) > 0;
$score = max(0, 100 - (count($etapesManquantesSpecifiques) * (100 / count($etapesSpecifiques))));
$score = round($score);

// Récupérer le symbole unique pour cette recette
$notificationSymbole = getSymboleRecette($recette->getNom());

// Fonction pour obtenir l'URL de l'image
function getImageUrl($nom) {
    $nom = strtolower($nom);
    
    if (strpos($nom, 'pizza') !== false) 
        return 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&h=400&fit=crop';
    if (strpos($nom, 'tajine') !== false) 
        return 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800&h=400&fit=crop';
    if (strpos($nom, 'salade') !== false) 
        return 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=400&fit=crop';
    if (strpos($nom, 'gateau') !== false || strpos($nom, 'cake') !== false || strpos($nom, 'gâteau') !== false) 
        return 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=400&fit=crop';
    if (strpos($nom, 'pasta') !== false || strpos($nom, 'spaghetti') !== false || strpos($nom, 'pâtes') !== false) 
        return 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=800&h=400&fit=crop';
    if (strpos($nom, 'poulet') !== false) 
        return 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=800&h=400&fit=crop';
    if (strpos($nom, 'poisson') !== false) 
        return 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=800&h=400&fit=crop';
    if (strpos($nom, 'burger') !== false) 
        return 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&h=400&fit=crop';
    if (strpos($nom, 'crepe') !== false || strpos($nom, 'crêpe') !== false) 
        return 'https://images.unsplash.com/photo-1519676867248-6e4f6f4f0a1c?w=800&h=400&fit=crop';
    if (strpos($nom, 'couscous') !== false) 
        return 'https://images.unsplash.com/photo-1617098900591-3f4c9b9f5a1a?w=800&h=400&fit=crop';
    if (strpos($nom, 'tiramisu') !== false) 
        return 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&h=400&fit=crop';
    if (strpos($nom, 'omelette') !== false || strpos($nom, 'oeuf') !== false || strpos($nom, 'œuf') !== false) 
        return 'https://images.unsplash.com/photo-1626978378685-1fd1c2f63d9b?w=800&h=400&fit=crop';
    if (strpos($nom, 'riz') !== false) 
        return 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800&h=400&fit=crop';
    if (strpos($nom, 'glace') !== false || strpos($nom, 'ice cream') !== false) 
        return 'https://images.unsplash.com/photo-1576506295286-5cda18df43e7?w=800&h=400&fit=crop';
    if (strpos($nom, 'soupe') !== false) 
        return 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&h=400&fit=crop';
    
    return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=400&fit=crop';
}

$imageUrl = getImageUrl($recette->getNom());
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Poppins', sans-serif; }
        .header { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; max-width: 1200px; margin: 0 auto; }
        .logo { display: flex; align-items: center; gap: 10px; }
        .logo-img { width: 40px; height: 40px; border-radius: 10px; }
        .logo-text { font-size: 1.5rem; font-weight: 700; color: #2e7d32; }
        .nav-menu { display: flex; list-style: none; gap: 30px; }
        .nav-menu li a { text-decoration: none; color: #333; font-weight: 500; transition: 0.3s; }
        .nav-menu li a:hover { color: #4CAF50; }
        .btn-dashboard { background: #4CAF50; color: white !important; padding: 8px 20px; border-radius: 25px; }
        .hamburger { display: none; flex-direction: column; cursor: pointer; }
        .hamburger span { width: 25px; height: 3px; background: #333; margin: 3px 0; transition: 0.3s; }
        .container-recette { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; background: #6c757d; color: white; padding: 10px 24px; border-radius: 30px; text-decoration: none; margin-bottom: 30px; transition: 0.3s; font-weight: 500; }
        .btn-back:hover { background: #5a6268; transform: translateX(-5px); }
        .recette-image { width: 100%; height: 350px; border-radius: 30px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); position: relative; }
        .recette-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .quality-badge { position: absolute; bottom: 20px; right: 20px; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); padding: 8px 15px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; color: white; }
        .recette-hero { background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white; padding: 40px; border-radius: 30px; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .recette-hero h1 { font-size: 2.2rem; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
        .recette-meta { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 20px; }
        .meta-item { background: rgba(255,255,255,0.2); padding: 8px 18px; border-radius: 30px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; }
        .description-section { background: white; padding: 30px; border-radius: 20px; margin-bottom: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .description-section h2 { color: #2e7d32; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .description-text { line-height: 1.7; color: #555; }
        .etapes-section h2 { color: #2e7d32; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }
        .etapes-grid { display: flex; flex-direction: column; gap: 25px; }
        .etape-card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; }
        .etape-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .etape-header { background: linear-gradient(135deg, #f8f9fa, #e9ecef); padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 3px solid #4CAF50; }
        .etape-numero { background: #4CAF50; color: white; padding: 8px 20px; border-radius: 40px; font-size: 1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; }
        .etape-badges { display: flex; gap: 10px; flex-wrap: wrap; }
        .etape-badge { background: white; padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 5px; color: #555; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .etape-body { padding: 25px; }
        .etape-details-table { background: #f8f9fa; border-radius: 16px; margin-bottom: 20px; overflow: hidden; }
        .detail-row { display: flex; border-bottom: 1px solid #e0e0e0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { width: 140px; padding: 12px 15px; background: #e9ecef; font-weight: 600; color: #2e7d32; display: flex; align-items: center; gap: 8px; }
        .detail-value { flex: 1; padding: 12px 15px; background: white; color: #333; }
        .etape-instruction { background: #f8f9fa; padding: 20px; border-radius: 16px; line-height: 1.7; margin-bottom: 20px; }
        .etape-astuce { background: #fff8e1; padding: 15px 20px; border-radius: 16px; font-size: 0.85rem; color: #856404; border-left: 4px solid #ffc107; display: flex; align-items: center; gap: 12px; }
        .empty-etapes { text-align: center; padding: 60px; background: white; border-radius: 20px; color: #666; }
        .empty-etapes i { font-size: 64px; color: #ccc; margin-bottom: 20px; }
        .footer { background: #1a1a2e; color: white; padding: 40px 20px 20px; margin-top: 60px; }
        .footer-content { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .footer-section h3, .footer-section h4 { margin-bottom: 15px; }
        .footer-section ul { list-style: none; }
        .footer-section ul li { margin-bottom: 8px; }
        .footer-section a { color: #ccc; text-decoration: none; }
        .footer-section a:hover { color: #4CAF50; }
        .social-links { display: flex; gap: 15px; margin-top: 15px; }
        .social-links a { background: rgba(255,255,255,0.1); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .social-links a:hover { background: #4CAF50; }
        .footer-bottom { text-align: center; padding-top: 30px; margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); }
        
        /* Bouton flottant pour activer les notifications */
        .btn-notif { position: fixed; bottom: 30px; right: 30px; background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white; border: none; border-radius: 50px; padding: 14px 24px; cursor: pointer; font-weight: 600; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 999; transition: all 0.3s ease; display: flex; align-items: center; gap: 10px; font-family: 'Poppins', sans-serif; }
        .btn-notif:hover { background: linear-gradient(135deg, #1b5e20, #0d3b0f); transform: scale(1.05); }
        
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-menu { position: fixed; left: -100%; top: 70px; flex-direction: column; background: white; width: 100%; text-align: center; transition: 0.3s; padding: 20px 0; gap: 15px; }
            .nav-menu.active { left: 0; }
            .hamburger { display: flex; }
            .recette-hero { padding: 25px; }
            .recette-hero h1 { font-size: 1.5rem; }
            .etape-header { flex-direction: column; align-items: flex-start; }
            .detail-label { width: 110px; font-size: 0.8rem; }
            .recette-image { height: 200px; }
            .btn-notif { bottom: 20px; right: 20px; padding: 10px 18px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
                <span class="logo-text">NutriLoop</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">Accueil</a></li>
                <li><a href="afficherRecette.php">Recettes</a></li>
                <li><a href="about.html">À propos</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="../backoffice/index.html" class="btn-dashboard">Dashboard</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <div class="container-recette">
        <a href="afficherRecette.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour aux recettes
        </a>

        <div class="recette-image">
            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($recette->getNom()) ?>">
            <?php if ($hasMissingSteps): ?>
            <div class="quality-badge">
                <i class="fas fa-exclamation-triangle"></i> Incomplète (<?= $score ?>%)
            </div>
            <?php else: ?>
            <div class="quality-badge">
                <i class="fas fa-check-circle"></i> Complète (100%)
            </div>
            <?php endif; ?>
        </div>

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
                elseif (strpos($nom, 'poulet') !== false) $icone = '🍗';
                elseif (strpos($nom, 'poisson') !== false) $icone = '🐟';
                elseif (strpos($nom, 'burger') !== false) $icone = '🍔';
                elseif (strpos($nom, 'crepe') !== false) $icone = '🥞';
                elseif (strpos($nom, 'couscous') !== false) $icone = '🍛';
                else $icone = '🍽️';
                ?>
                <span><?= $icone ?></span>
                <?= htmlspecialchars($recette->getNom()) ?>
            </h1>
            <div class="recette-meta">
                <div class="meta-item"><i class="fas fa-clock"></i> <?= $recette->getTempsPreparation() ?> min</div>
                <div class="meta-item"><i class="fas fa-users"></i> <?= $recette->getNbPersonne() ?> pers</div>
                <div class="meta-item"><i class="fas fa-chart-line"></i> 
                    <?php
                    $diffIcon = '';
                    if ($recette->getDifficulte() == 'FACILE') $diffIcon = '😊';
                    elseif ($recette->getDifficulte() == 'MOYEN') $diffIcon = '😐';
                    else $diffIcon = '😣';
                    echo $diffIcon . ' ' . $recette->getDifficulte();
                    ?>
                </div>
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

        <div class="description-section">
            <h2><i class="fas fa-align-left"></i> Description</h2>
            <div class="description-text"><?= nl2br(htmlspecialchars($recette->getDescription())) ?></div>
        </div>

        <div class="etapes-section">
            <h2><i class="fas fa-list-ol"></i> Étapes de préparation 
                <?php if (count($etapes) > 0): ?>
                <span style="font-size:0.8rem; background:#e8f5e9; padding:4px 12px; border-radius:20px; color:#2e7d32;">
                    <?= count($etapes) ?> étape(s)
                </span>
                <?php endif; ?>
            </h2>
            
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

                                <div class="etape-instruction">
                                    <i class="fas fa-align-left"></i> 
                                    <strong>Instruction :</strong><br>
                                    <?= nl2br(htmlspecialchars($etape->getInstruction())) ?>
                                </div>

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
                    <a href="../backoffice/preparation/addPreperation.php?recette_id=<?= $id ?>" style="display:inline-block; margin-top:15px; background:#4CAF50; color:white; padding:10px 20px; border-radius:25px; text-decoration:none;">
                        <i class="fas fa-plus"></i> Ajouter des étapes
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

    <button class="btn-notif" id="activerNotifBtn">
        <i class="fas fa-bell"></i> Activer les notifications
    </button>

    <script>
        // ========== NOTIFICATION AVEC SYMBOLE UNIQUE POUR CHAQUE RECETTE ==========
        
        const hasMissingSteps = <?= json_encode($hasMissingSteps) ?>;
        const manquesList = <?= json_encode($etapesManquantesSpecifiques) ?>;
        const manquesDetails = <?= json_encode($etapesManquantesDetails) ?>;
        const score = <?= $score ?>;
        const totalEtapes = <?= count($etapes) ?>;
        const recetteName = <?= json_encode($recette->getNom()) ?>;
        const symbole = <?= json_encode($notificationSymbole) ?>;
        
        function afficherNotification() {
            if (!hasMissingSteps || manquesList.length === 0) {
                console.log("✅ Recette complète");
                return;
            }
            
            let manquesText = "";
            for(let i = 0; i < manquesList.length; i++) {
                manquesText += "⚠️ " + manquesList[i] + "\n";
            }
            
            let manquesDetailsText = "";
            for(let i = 0; i < manquesDetails.length; i++) {
                manquesDetailsText += manquesDetails[i] + "\n";
            }
            
            // Notification avec symbole unique
            const notification = new Notification(symbole + " " + recetteName + " - Étapes manquantes " + symbole, {
                body: `📌 ${recetteName}\n📊 Score: ${score}%\n━━━━━━━━━━━━━━━━━━━━\n🔴 ÉTAPES MANQUANTES:\n${manquesText}━━━━━━━━━━━━━━━━━━━━\n💡 COMMENT FAIRE:\n${manquesDetailsText}━━━━━━━━━━━━━━━━━━━━\n📋 ${totalEtapes} étape(s) trouvée(s)`,
                icon: "https://cdn-icons-png.flaticon.com/512/1995/1995572.png",
                badge: "https://cdn-icons-png.flaticon.com/512/1995/1995572.png",
                vibrate: [200, 100, 200],
                silent: false,
                requireInteraction: true
            });
            
            notification.onclick = function() {
                window.focus();
                this.close();
            };
            
            setTimeout(() => notification.close(), 20000);
        }
        
        function demanderPermissionNotifications() {
            if (!("Notification" in window)) {
                alert("❌ Votre navigateur ne supporte pas les notifications");
                return false;
            }
            
            if (Notification.permission === "granted") {
                afficherNotification();
                return true;
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        alert("✅ Notifications activées !");
                        afficherNotification();
                    } else {
                        alert("⚠️ Pour activer les notifications:\nCliquez sur le cadenas 🔒 puis autorisez");
                    }
                });
            } else {
                alert("⚠️ Notifications bloquées.\nCliquez sur le cadenas 🔒 > Notifications > Autoriser");
            }
        }
        
        document.getElementById('activerNotifBtn').addEventListener('click', demanderPermissionNotifications);
        
        window.addEventListener('load', function() {
            if (hasMissingSteps && manquesList.length > 0 && Notification.permission === "granted") {
                setTimeout(afficherNotification, 2000);
            }
        });
        
        // Animation des étapes
        const etapeCards = document.querySelectorAll('.etape-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(30px)';
                    setTimeout(() => {
                        entry.target.style.transition = 'all 0.5s ease';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        etapeCards.forEach(card => observer.observe(card));
        
        // Hamburger menu
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