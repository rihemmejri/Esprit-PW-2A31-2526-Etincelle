<?php
include '../../controleurs/ProduitController.php';
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
include '../../controleurs/CategorieController.php';
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();
$categorieController = new CategorieController();
$allCategories = $categorieController->listCategories();
$categoryMapping = [];
foreach ($allCategories as $cat) {
    $categoryMapping[$cat->getIdCategorie()] = $cat->getNomCategorie();
}
<<<<<<< HEAD

$produits = $produitController->listProduits();

// Stats calculation
$totalProduits = count($produits);
$locauxCount = count(array_filter($produits, fn($p) => $p->getOrigine() === 'local'));
$categoriesCount = count(array_unique(array_map(fn($p) => $p->getIdCategorie(), $produits)));
=======
=======
require_once __DIR__ . '/../../models/produit.php';

$produitController = new ProduitController();
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c

// Si c'est une requête AJAX pour les détails
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $produit = $produitController->getProduitById($id);
        
        if ($produit) {
            echo json_encode([
                'success' => true,
                'id' => $produit->getIdProduit(),
                'nom' => $produit->getNom(),
                'origine' => $produit->getOrigine(),
                'distance_transport' => $produit->getDistanceTransport(),
                'type_transport' => $produit->getTypeTransport(),
                'emballage' => $produit->getEmballage(),
                'transformation' => $produit->getTransformation(),
                'saison' => $produit->getSaison()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Sinon, afficher la page normale
$produits = $produitController->listProduits();
<<<<<<< HEAD

// Calcul des statistiques
$totalProduits = count($produits);
$locauxCount = count(array_filter($produits, fn($p) => $p->getOrigine() === 'local'));
$categoriesCount = count(array_unique(array_map(fn($p) => $p->getIdCategorie(), $produits)));
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Nos Produits - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
=======
<<<<<<< HEAD
    <title>Nos Produits - NutriLoop AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2E7D32, #1565C0);
            --success-green: #2E7D32;
            --info-blue: #1565C0;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
<<<<<<< HEAD
            --accent-orange: #FF9800;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); line-height: 1.6; }

        .container-client { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px; }

        /* Floating Cart Icon */
        .cart-trigger {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            color: var(--success-green);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 1000;
            transition: 0.3s;
            border: 2px solid var(--success-green);
        }
        .cart-trigger:hover { transform: scale(1.1); box-shadow: 0 8px 25px rgba(0,0,0,0.3); }
        .cart-trigger.pulse { animation: cartPulse 0.5s ease; }
        
        @keyframes cartPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); background: var(--info-blue); color: white; }
            100% { transform: scale(1); }
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-orange);
            color: white;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 10px;
        }

        /* Hero Section (Matching Screenshot) */
        .hero-section {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 70px 30px;
            text-align: center;
            color: white;
            margin: 30px 0;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .hero-section h1 { 
            font-size: 3.2em; 
            font-weight: 800;
            margin-bottom: 15px; 
=======
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container-client {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        /* Hero Section Styling */
        .hero-section {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 50px 30px;
            text-align: center;
            color: white;
            margin: 20px 0 60px;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .hero-nav {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 12px;
        }

        .hero-nav a {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 22px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(5px);
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.9em;
        }

        .hero-nav a:hover {
            background: white;
            color: var(--success-green);
            transform: translateY(-3px);
        }

        .hero-section h1 {
            font-size: 2.8em;
            margin-bottom: 10px;
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
<<<<<<< HEAD
        .hero-section p { font-size: 1.1em; opacity: 0.9; font-weight: 500; }

        /* Stat Cards (Matching Screenshot) */
=======

        .hero-section p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        /* Stat Cards */
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
<<<<<<< HEAD
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 40px 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .stat-card h3 {
            font-size: 2.8em;
            color: #2E7D32;
            margin-bottom: 10px;
            font-weight: 800;
        }
        .stat-card p {
            color: #666;
            font-weight: 600;
            font-size: 0.95em;
        }

        /* Filter Bar (Matching Screenshot) */
        .filter-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
            margin-bottom: 40px;
            display: flex;
            gap: 25px;
            align-items: center;
        }
        .filter-group { flex: 1; }
        .filter-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 800;
            color: #2E7D32;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-group input, .filter-group select {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #eee;
            border-radius: 12px;
            font-size: 1rem;
            background: #fdfdfd;
            outline: none;
            transition: 0.3s;
            color: #444;
        }
        .filter-group input:focus, .filter-group select:focus {
            border-color: #2E7D32;
            background: white;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.05);
        }

        /* AI Recommendations Styling */
        .ai-section {
            background: var(--white);
            border: 2px solid #e8f5e9;
            padding: 40px;
            border-radius: 25px;
            margin-bottom: 40px;
            position: relative;
            box-shadow: var(--shadow);
            border-left: 10px solid var(--success-green);
        }
        .ai-header { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
        .ai-header i { font-size: 2.2rem; color: var(--success-green); }
        .ai-header h2 { color: var(--success-green); font-weight: 800; font-size: 1.8rem; }
        
        .ai-conseil-box {
            background: #e8f5e9;
            padding: 20px 25px;
            border-radius: 18px;
            margin-bottom: 30px;
            border-left: 5px solid #2E7D32;
            position: relative;
        }
        .ai-conseil-box::before {
            content: '\f0eb';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: -15px;
            right: 20px;
            font-size: 2rem;
            color: rgba(46, 125, 50, 0.1);
        }
        .conseil-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            color: #2E7D32;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .conseil-text { font-size: 1.05rem; font-weight: 500; color: #1b5e20; line-height: 1.5; }

        .ai-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .ai-card {
            background: #fdfdfd;
            padding: 20px;
            border-radius: 20px;
            border: 1px solid #eee;
            transition: 0.3s;
            position: relative;
        }
        .ai-card:hover { transform: translateY(-5px); border-color: var(--success-green); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .ai-card-title { font-weight: 800; font-size: 1rem; margin-bottom: 10px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        /* Smart NutriBot UI */
        #nutriBotBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 70px;
            height: 70px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 4px solid white;
        }
        #nutriBotBtn:hover { transform: scale(1.1) rotate(10deg); }
        
        #nutriBotChat {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 380px;
            height: 500px;
            background: white;
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
            z-index: 1001;
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #eee;
            animation: slideUpChat 0.4s ease;
        }
        @keyframes slideUpChat { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .chat-header {
            background: var(--primary-gradient);
            padding: 20px;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: #fdfdfd;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .msg {
            padding: 12px 18px;
            border-radius: 18px;
            max-width: 80%;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .msg-bot { background: #f0f0f0; color: #333; align-self: flex-start; border-bottom-left-radius: 5px; }
        .msg-user { background: var(--success-green); color: white; align-self: flex-end; border-bottom-right-radius: 5px; }
        
        .chat-input-area {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        .chat-input-area input {
            flex-grow: 1;
            padding: 12px 18px;
            border: 2px solid #f0f0f0;
            border-radius: 30px;
            outline: none;
            transition: 0.3s;
        }
        .chat-input-area input:focus { border-color: var(--success-green); }
        .btn-send-chat {
            background: var(--success-green);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-send-chat:hover { transform: scale(1.1); }

=======
            margin-top: -40px;
            margin-bottom: 40px;
            padding: 0 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 18px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-card h3 {
            font-size: 2.5em;
            color: var(--success-green);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text-gray);
            font-weight: 500;
            font-size: 0.95em;
        }

        /* Search & Filter Bar */
        .filter-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group label {
            display: block;
            font-size: 0.75em;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--success-green);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-group input, .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 10px;
            font-size: 1em;
            outline: none;
            transition: 0.3s;
        }

        .filter-group input:focus, .filter-group select:focus {
            border-color: var(--success-green);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
<<<<<<< HEAD
            min-height: 200px;
        }
        #noResults {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px;
            color: #999;
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        #noResults i { font-size: 3rem; opacity: 0.5; }
=======
        }

>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
<<<<<<< HEAD
            transition: 0.4s;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .product-card:hover { transform: translateY(-10px); }
        
=======
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        .product-image {
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
<<<<<<< HEAD
            font-size: 4em;
            position: relative;
        }
        .eco-score-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255,255,255,0.9);
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .product-content { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .product-title { font-size: 1.4em; font-weight: 700; margin-bottom: 5px; }
        .product-price { font-size: 1.6em; font-weight: 800; color: var(--info-blue); margin-bottom: 10px; }
        .product-stock { font-size: 0.85rem; color: #888; margin-bottom: 15px; }

        .btn-add-cart {
            background: var(--success-green);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: auto;
        }
        .btn-add-cart:hover { background: #1b5e20; transform: scale(1.02); }

        .btn-view-details {
            background: transparent;
            color: var(--info-blue);
            border: 2px solid var(--info-blue);
            padding: 10px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .btn-view-details:hover { background: var(--info-blue); color: white; }

        /* Sidebar Cart */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
            z-index: 2000;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 30px;
            display: flex;
            flex-direction: column;
        }
        .cart-sidebar.open { right: 0; }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .close-cart { font-size: 1.5rem; cursor: pointer; color: #ccc; }
        .cart-items { flex-grow: 1; overflow-y: auto; }
        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item-img { width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .cart-item-info { flex-grow: 1; }
        .cart-item-name { font-weight: 700; font-size: 0.95rem; }
        .cart-item-price { font-size: 0.85rem; color: var(--text-gray); }
        
        .cart-footer { padding-top: 20px; border-top: 2px solid #eee; }
        .cart-delivery-fee { display: flex; justify-content: space-between; font-size: 0.9rem; color: #666; margin-bottom: 5px; }
        .cart-total { display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 800; margin-bottom: 20px; }
        .btn-checkout {
            background: var(--primary-gradient);
            color: white;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-checkout:hover { opacity: 0.9; transform: scale(1.02); }

        /* Payment Modal Specifics */
        .bank-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .bank-item {
            border: 2px solid #eee;
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 700;
            transition: 0.3s;
        }
        .bank-item.active { border-color: var(--success-green); background: #e8f5e9; }
        
        .card-input-group {
            margin-bottom: 15px;
        }
        .card-input-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .card-input-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 10px;
            outline: none;
        }
        .card-input-group input:focus { border-color: var(--info-blue); }
        .error-msg { color: #ff4757; font-size: 0.75rem; margin-top: 4px; display: none; }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1500;
            display: none;
            backdrop-filter: blur(4px);
        }
        .overlay.active { display: block; }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(51, 51, 51, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            z-index: 3000;
            display: none; /* Keep it hidden by default */
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: toastIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-weight: 600;
            align-items: center;
            gap: 12px;
        }
        @keyframes toastIn { 
            from { bottom: -100px; opacity: 0; } 
            to { bottom: 30px; opacity: 1; } 
        }

        /* Success Modal */
        #successModal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(8px);
        }
        .success-card {
            background: white;
            padding: 50px;
            border-radius: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: var(--success-green);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 30px;
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
        }
        .success-title { font-size: 1.8rem; font-weight: 800; color: #333; margin-bottom: 15px; }
        .success-text { color: #666; line-height: 1.6; margin-bottom: 30px; }
        .btn-success-close {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-success-close:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        /* Existing Styles */
        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(5px);
            z-index: 4000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #eee;
            border-top: 5px solid var(--success-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader-container" id="globalLoader" style="display: none;">
        <div class="spinner"></div>
        <p style="font-weight: 700; color: var(--success-green);">Traitement sécurisé en cours...</p>
    </div>

    <div id="nutriBotBtn">
        <i class="fas fa-robot"></i>
    </div>

    <div id="nutriBotChat">
        <div class="chat-header">
            <i class="fas fa-robot fa-2x"></i>
            <div>
                <div style="font-weight: 800; font-size: 1.1rem;">NutriBot Assistant</div>
                <div style="font-size: 0.75rem; opacity: 0.8;">Smart AI • En ligne</div>
            </div>
            <i class="fas fa-times" id="closeChat" style="margin-left: auto; cursor: pointer; opacity: 0.7;"></i>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="msg msg-bot">Bonjour ! Je suis NutriBot. Comment puis-je vous aider aujourd'hui ? 🍎</div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chatInput" placeholder="Posez votre question...">
            <button class="btn-send-chat" id="sendChatBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>


    <div class="overlay" id="overlay"></div>

    <div class="cart-trigger" id="cartBtn">
        <i class="fas fa-shopping-basket"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>

    <!-- Sidebar Cart -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h2>Votre Panier</h2>
            <i class="fas fa-times close-cart" id="closeCart"></i>
        </div>
        <div class="cart-items" id="cartItemsList">
            <!-- Items via AJAX -->
        </div>
        <div class="cart-footer">
            <div id="deliveryFeeRow" class="cart-delivery-fee" style="display: none;">
                <span>Livraison (Ariana)</span>
                <span>7.00 DT</span>
            </div>
            <div class="cart-total">
                <span>Total</span>
                <span id="cartTotalValue">0.00 DT</span>
            </div>
            <button class="btn-checkout" id="btnCheckout">Commander & Payer</button>
        </div>
    </div>

    <div class="container-client">
        <!-- Hero Section (Strictly Matching Screenshot) -->
        <div class="hero-section">
            <h1><i class="fas fa-apple-alt"></i> Nos Produits</h1>
            <p>Découvrez notre sélection d'aliments sains, durables et locaux pour votre bien-être.</p>
        </div>

        <!-- Stats Section (Strictly Matching Screenshot) -->
=======
            position: relative;
            font-size: 5.5em;
            overflow: hidden;
        }

        .product-badge {
position: absolute;
top: 6px;
right: 6px;

background: var(--success-green);
color: white;

padding: 1px 4px;        
font-size: 0.45em;      
font-weight: 600;        
border-radius: 10px;    
line-height: 1;         
box-shadow: 0 1px 3px rgba(0,0,0,0.08);
z-index: 10;

letter-spacing: 0.2px;
text-transform: uppercase;
        }

        .product-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.4em;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .product-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: var(--text-gray);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .product-description {
            font-size: 0.95em;
            color: var(--text-gray);
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        .btn-details {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-details:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(21, 101, 192, 0.3);
        }

        .no-results {
            grid-column: 1 / -1;
            background: white;
            padding: 80px 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .no-results i {
            font-size: 4em;
            color: #ddd;
            margin-bottom: 20px;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            width: 90%;
            max-width: 600px;
            border-radius: 25px;
            padding: 35px;
            position: relative;
            animation: modalIn 0.4s ease-out;
        }

        @keyframes modalIn {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close {
            position: absolute;
            right: 25px;
            top: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #ccc;
            transition: 0.3s;
        }

        .close:hover { color: var(--text-dark); }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 25px;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 15px;
        }

        .detail-label {
            display: block;
            font-size: 0.8em;
            color: var(--success-green);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Global Footer */
        .simple-footer {
            background: var(--success-green);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
        }

        .simple-footer p {
            font-size: 0.95em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Categories icons mapping */
        .origine-local { color: #2e7d32; font-weight: 700; }
        .origine-importe { color: #1565c0; font-weight: 700; }
    </style>
=======
    <title>Nos Produits - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
</head>
<body>
    <div class="container-client">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-apple-alt"></i>
<<<<<<< HEAD
                Nos Produits
            </h1>
            <p>Découvrez notre sélection d'aliments sains, durables et locaux pour votre bien-être.</p>
        </div>

        <!-- Stats Section -->
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $totalProduits ?></h3>
                <p>Produits enregistrés</p>
            </div>
            <div class="stat-card">
                <h3><?= $locauxCount ?></h3>
                <p>Produits locaux 🌱</p>
            </div>
            <div class="stat-card">
                <h3><?= $categoriesCount ?></h3>
                <p>Catégories d'aliments</p>
            </div>
        </div>

<<<<<<< HEAD
        <!-- Filter Bar (Strictly Matching Screenshot) -->
        <div class="filter-container">
            <div class="filter-group">
                <label for="searchInput"><i class="fas fa-search"></i> RECHERCHER</label>
                <input type="text" id="searchInput" placeholder="Nom du produit..." onkeyup="filterProducts()">
            </div>
            <div class="filter-group">
                <label for="categoryFilter"><i class="fas fa-filter"></i> CATÉGORIE</label>
=======
        <!-- Filter Bar -->
        <div class="filter-container">
            <div class="filter-group">
                <label for="searchInput"><i class="fas fa-search"></i> Rechercher</label>
                <input type="text" id="searchInput" placeholder="Nom du produit..." onkeyup="filterProducts()">
            </div>
            <div class="filter-group">
                <label for="categoryFilter"><i class="fas fa-filter"></i> Catégorie</label>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                <select id="categoryFilter" onchange="filterProducts()">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= $cat->getIdCategorie() ?>"><?= htmlspecialchars($cat->getNomCategorie()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

<<<<<<< HEAD
        <!-- AI Recommendations Section -->
        <div class="ai-section" id="aiSection" style="display: none;">
            <div class="ai-header">
                <i class="fas fa-robot"></i>
                <h2>Le Choix de l'IA NutriLoop</h2>
            </div>
            
            <div class="ai-conseil-box">
                <span class="conseil-label">💡 AI Conseil</span>
                <p id="aiAdvice" class="conseil-text"></p>
            </div>

            <div class="ai-grid" id="aiRecommendationsGrid">
                <!-- Recommendations will be loaded via AJAX -->
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid" id="produitsGrid">
            <div id="noResults">
                <i class="fas fa-search-minus"></i>
                <p>Aucun produit ne correspond à votre recherche.</p>
                <button class="btn-view-details" onclick="resetSearch()" style="width: auto; padding: 10px 20px;">Voir tout</button>
            </div>
            <?php foreach ($produits as $produit): ?>
                <div class="product-card" 
                     data-cat="<?= $produit->getIdCategorie() ?>" 
                     data-nom="<?= strtolower(htmlspecialchars($produit->getNom())) ?>"
                     data-prix="<?= $produit->getPrix() ?>"
                     data-eco="<?= $produit->getEcoScore() ?>">
                    <?php 
                        $score = $produit->getEcoScore();
                        $color = ($score >= 80) ? '#4CAF50' : (($score >= 50) ? '#FF9800' : '#F44336');
                    ?>
                    <div class="eco-score-badge" style="color: <?= $color ?>">
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: <?= $color ?>"></div>
                        Eco: <?= $score ?>
                    </div>
                    
                    <div class="product-image">
                        <?php if ($produit->getImage()): ?>
                            <img src="../assets/images/<?= htmlspecialchars($produit->getImage()) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span>🥘</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-content">
                        <h3 class="product-title"><?= htmlspecialchars($produit->getNom()) ?></h3>
                        <p class="product-price"><?= number_format($produit->getPrix(), 2) ?> DT</p>
                        <p class="product-stock">
                            <i class="fas fa-cubes"></i> <?= $produit->getStock() ?> en stock
                        </p>
                        
                        <button class="btn-view-details" onclick='openDetails(<?= json_encode([
                            "id" => $produit->getIdProduit(),
                            "nom" => $produit->getNom(),
                            "prix" => $produit->getPrix(),
                            "stock" => $produit->getStock(),
                            "score" => $produit->getEcoScore(),
                            "origine" => $produit->getOrigine(),
                            "distance" => $produit->getDistanceTransport(),
                            "transport" => $produit->getTypeTransport(),
                            "emballage" => $produit->getEmballage(),
                            "transformation" => $produit->getTransformation(),
                            "saison" => $produit->getSaison()
                        ]) ?>)'>
                            <i class="fas fa-info-circle"></i> Détails
                        </button>

                        <button class="btn-add-cart" onclick="addToCart(<?= $produit->getIdProduit() ?>)">
                            <i class="fas fa-cart-plus"></i> Rapide
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="overlay" style="display: none; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; width: 95%; max-width: 650px; border-radius: 30px; padding: 40px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
            <i class="fas fa-times" id="closeModal" style="position: absolute; right: 25px; top: 25px; font-size: 1.2rem; cursor: pointer; color: #ccc;"></i>
            
            <h2 id="modalTitle" style="color: #2E7D32; font-size: 2.2rem; font-weight: 800; margin-bottom: 30px;"></h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <!-- Origine -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 18px;">
                    <span style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #2E7D32; font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-globe"></i> Origine
                    </span>
                    <span id="modalOrigine" style="font-size: 1.2rem; font-weight: 600; color: #333;"></span>
                </div>
                <!-- Distance -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 18px;">
                    <span style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #2E7D32; font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-truck"></i> Distance
                    </span>
                    <span id="modalDistance" style="font-size: 1.2rem; font-weight: 600; color: #333;"></span>
                </div>
                <!-- Transport -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 18px;">
                    <span style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #2E7D32; font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-shipping-fast"></i> Transport
                    </span>
                    <span id="modalTransport" style="font-size: 1.2rem; font-weight: 600; color: #333;"></span>
                </div>
                <!-- Emballage -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 18px;">
                    <span style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #2E7D32; font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-box-open"></i> Emballage
                    </span>
                    <span id="modalEmballage" style="font-size: 1.2rem; font-weight: 600; color: #333;"></span>
                </div>
                <!-- Transformation -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 18px;">
                    <span style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #2E7D32; font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-industry"></i> Transformation
                    </span>
                    <span id="modalTransformation" style="font-size: 1.2rem; font-weight: 600; color: #333;"></span>
                </div>
                <!-- Saison -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 18px;">
                    <span style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #2E7D32; font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-calendar-alt"></i> Saison
                    </span>
                    <span id="modalSaison" style="font-size: 1.2rem; font-weight: 600; color: #333;"></span>
                </div>
            </div>

            <!-- Eco-Score, Prix, Quantité and CTA -->
            <div style="border-top: 2px solid #f0f0f0; padding-top: 30px; display: flex; flex-direction: column; gap: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-weight: 800; color: #666; font-size: 0.9rem;">ECO-SCORE:</span>
                        <span id="modalScore" style="font-size: 1.8rem; font-weight: 900;"></span>
                    </div>
                    <span id="modalPrix" style="font-size: 2rem; font-weight: 900; color: var(--info-blue);"></span>
                </div>

                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="flex: 1; display: flex; align-items: center; gap: 15px; background: #f8f9fa; padding: 10px 20px; border-radius: 15px;">
                        <label style="font-weight: 800; color: #666; font-size: 0.9rem;">QUANTITÉ:</label>
                        <input type="number" id="modalQty" value="1" min="1" style="width: 80px; padding: 10px; border: none; background: white; border-radius: 10px; font-weight: 800; text-align: center; font-size: 1.1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    </div>
                    <button class="btn-checkout" id="modalAddBtn" style="flex: 2; height: 60px; font-size: 1.2rem;">
                        <i class="fas fa-cart-plus"></i> Ajouter au panier
                    </button>
=======
        <!-- Products Grid -->
        <div class="products-grid" id="produitsGrid">
            <?php if (count($produits) > 0): ?>
                <?php foreach ($produits as $produit): ?>
                    <div class="product-card" 
                         data-id="<?= $produit->getIdProduit() ?>"
                         data-nom="<?= strtolower(htmlspecialchars($produit->getNom())) ?>"
                         data-cat="<?= $produit->getIdCategorie() ?>">
                        
                        <div class="product-image">
                            <?php if ($produit->getImage()): ?>
                                <img src="../assets/images/<?= htmlspecialchars($produit->getImage()) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?php
                                $icone = '';
                                $nom = strtolower($produit->getNom());
                                if (strpos($nom, 'tomate') !== false) $icone = '🍅';
                                elseif (strpos($nom, 'salade') !== false) $icone = '🥗';
                                elseif (strpos($nom, 'carotte') !== false) $icone = '🥕';
                                elseif (strpos($nom, 'pomme') !== false) $icone = '🍎';
                                elseif (strpos($nom, 'orange') !== false) $icone = '🍊';
                                elseif (strpos($nom, 'banane') !== false) $icone = '🍌';
                                elseif (strpos($nom, 'fraise') !== false) $icone = '🍓';
                                elseif (strpos($nom, 'raisin') !== false) $icone = '🍇';
                                elseif (strpos($nom, 'oignon') !== false) $icone = '🧅';
                                elseif (strpos($nom, 'ail') !== false) $icone = '🧄';
                                elseif (strpos($nom, 'poivron') !== false) $icone = '🫑';
                                elseif (strpos($nom, 'concombre') !== false) $icone = '🥒';
                                elseif (strpos($nom, 'broccoli') !== false) $icone = '🥦';
                                elseif (strpos($nom, 'chou') !== false) $icone = '🥬';
                                elseif (strpos($nom, 'avocat') !== false) $icone = '🥑';
                                elseif (strpos($nom, 'citron') !== false) $icone = '🍋';
                                elseif (strpos($nom, 'lait') !== false) $icone = '🥛';
                                elseif (strpos($nom, 'viande') !== false) $icone = '🥩';
                                elseif (strpos($nom, 'poulet') !== false) $icone = '🍗';
                                elseif (strpos($nom, 'poisson') !== false) $icone = '🐟';
                                else $icone = '🥘';
                                ?>
                                <span><?= $icone ?></span>
                            <?php endif; ?>
                            
                            <?php if ($produit->getOrigine() === 'local'): ?>
                                <div class="product-badge">🌱 Local</div>
                            <?php else: ?>
                                <div class="product-badge" style="background: #1565C0;">🌍 Importé</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title"><?= htmlspecialchars($produit->getNom()) ?></h3>
                            
                            <div class="product-meta">
=======
                Découvrez nos Produits
            </h1>
            <p>Une sélection de produits locaux et durables pour une alimentation respectueuse de l'environnement</p>
        </div>

        <!-- Grille des produits -->
        <div class="recipes-grid" id="produitsGrid">
            <?php if (count($produits) > 0): ?>
                <?php foreach ($produits as $produit): ?>
                    <div class="recipe-card" 
                         data-id="<?= $produit->getIdProduit() ?>"
                         data-titre="<?= strtolower(htmlspecialchars($produit->getNom())) ?>">
                        
                        <!-- Image avec emoji personnalisé -->
                        <div class="recipe-image">
                            <?php
                            $icone = '';
                            $nom = strtolower($produit->getNom());
                            
                            if (strpos($nom, 'tomate') !== false) $icone = '🍅';
                            elseif (strpos($nom, 'salade') !== false) $icone = '🥗';
                            elseif (strpos($nom, 'carotte') !== false) $icone = '🥕';
                            elseif (strpos($nom, 'pomme') !== false) $icone = '🍎';
                            elseif (strpos($nom, 'orange') !== false) $icone = '🍊';
                            elseif (strpos($nom, 'banane') !== false) $icone = '🍌';
                            elseif (strpos($nom, 'fraise') !== false) $icone = '🍓';
                            elseif (strpos($nom, 'raisin') !== false) $icone = '🍇';
                            elseif (strpos($nom, 'oignon') !== false) $icone = '🧅';
                            elseif (strpos($nom, 'ail') !== false) $icone = '🧄';
                            elseif (strpos($nom, 'poivron') !== false) $icone = '🫑';
                            elseif (strpos($nom, 'concombre') !== false) $icone = '🥒';
                            elseif (strpos($nom, 'broccoli') !== false) $icone = '🥦';
                            elseif (strpos($nom, 'chou') !== false) $icone = '🥬';
                            elseif (strpos($nom, 'avocat') !== false) $icone = '🥑';
                            elseif (strpos($nom, 'citron') !== false) $icone = '🍋';
                            else $icone = '🥘';
                            ?>
                            <span style="font-size: 5em;"><?= $icone ?></span>
                            <div class="recipe-badge">
                                🌱 Local
                            </div>
                        </div>
                        
                        <div class="recipe-content">
                            <h3 class="recipe-title"><?= htmlspecialchars($produit->getNom()) ?></h3>
                            
                            <div class="recipe-meta">
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                <span class="meta-item">
                                    <i class="fas fa-<?= $produit->getOrigine() === 'local' ? 'leaf' : 'globe' ?>"></i> 
                                    <?= $produit->getOrigine() === 'local' ? 'Local' : 'Importé' ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-truck"></i> <?= $produit->getDistanceTransport() ?> km
                                </span>
                            </div>
                            
<<<<<<< HEAD
                            <p class="product-description">
=======
                            <p class="recipe-description">
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                <?php
                                $details = array_filter([
                                    $produit->getTransformation(),
                                    $produit->getEmballage(),
                                    $produit->getSaison()
                                ]);
<<<<<<< HEAD
                                echo htmlspecialchars(implode(' • ', $details)) ?: 'Produit de qualité supérieure sélectionné pour vous.';
                                ?>
                            </p>
                            
                            <div class="product-footer">
                                <span style="font-size: 0.85em; color: #999; font-style: italic;">
                                    <?= isset($categoryMapping[$produit->getIdCategorie()]) ? htmlspecialchars($categoryMapping[$produit->getIdCategorie()]) : 'Sans catégorie' ?>
                                </span>
                                <button class="btn-details" onclick='showProduitDetails(<?= json_encode([
                                    'nom' => $produit->getNom(),
                                    'origine' => $produit->getOrigine(),
                                    'distance' => $produit->getDistanceTransport(),
                                    'transport' => $produit->getTypeTransport(),
=======
                                echo htmlspecialchars(implode(' • ', $details)) ?: 'Produit de qualité';
                                ?>
                            </p>
                            
                            <div class="recipe-footer">
                                <div class="recipe-difficulte origine-<?= $produit->getOrigine() ?>">
                                    <i class="fas fa-<?= $produit->getOrigine() === 'local' ? 'leaf' : 'plane' ?>"></i>
                                    <span>
                                        <?= $produit->getOrigine() === 'local' ? 'Local' : 'Importé' ?>
                                    </span>
                                </div>
                                <button class="btn-details" onclick='showProduitDetails(<?= json_encode([
                                    'id' => $produit->getIdProduit(),
                                    'nom' => $produit->getNom(),
                                    'origine' => $produit->getOrigine(),
                                    'distance_transport' => $produit->getDistanceTransport(),
                                    'type_transport' => $produit->getTypeTransport(),
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                    'emballage' => $produit->getEmballage(),
                                    'transformation' => $produit->getTransformation(),
                                    'saison' => $produit->getSaison()
                                ]) ?>)'>
<<<<<<< HEAD
                                    Détails <i class="fas fa-arrow-right"></i>
=======
                                    Voir détails <i class="fas fa-arrow-right"></i>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
<<<<<<< HEAD
                <div class="no-results" id="emptyState">
                    <i class="fas fa-search"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Revenez plus tard pour découvrir nos nouveaux produits !</p>
                </div>
            <?php endif; ?>
            
            <div class="no-results" id="noMatch" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>Aucun résultat trouvé</h3>
                <p>Essayez de modifier vos critères de recherche.</p>
            </div>
=======
                <div class="no-results">
                    <i class="fas fa-box"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Revenez plus tard pour découvrir nos produits !</p>
                </div>
            <?php endif; ?>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
        </div>
    </div>

    <!-- Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
<<<<<<< HEAD
                <h2 id="modalTitle" style="color: var(--success-green); font-size: 1.8em;"></h2>
=======
                <h2 id="modalTitle"></h2>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
            </div>
            <div class="modal-body">
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-globe"></i> Origine</span>
                        <span class="detail-value" id="detailOrigine"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-truck"></i> Distance</span>
                        <span class="detail-value" id="detailDistance"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-shipping-fast"></i> Transport</span>
                        <span class="detail-value" id="detailTransport"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-box-open"></i> Emballage</span>
                        <span class="detail-value" id="detailEmballage"></span>
                    </div>
                    <div class="detail-item">
<<<<<<< HEAD
                        <span class="detail-label"><i class="fas fa-industry"></i> Transformation</span>
=======
                        <span class="detail-label"><i class="fas fa-leaf"></i> Transformation</span>
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
                        <span class="detail-value" id="detailTransformation"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar"></i> Saison</span>
                        <span class="detail-value" id="detailSaison"></span>
                    </div>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Payment Modal -->
    <div id="paymentModal" class="overlay" style="display: none; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; width: 95%; max-width: 500px; border-radius: 30px; padding: 40px; position: relative;">
            <i class="fas fa-times" id="closePayment" style="position: absolute; right: 25px; top: 25px; font-size: 1.2rem; cursor: pointer; color: #ccc;"></i>
            
            <h2 style="color: var(--success-green); margin-bottom: 10px;">Paiement Sécurisé</h2>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 25px;">Finalisez votre commande de <strong id="payAmount">0.00 DT</strong></p>

            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button id="btnIntl" class="btn-view-details" style="flex: 1; margin: 0; border-width: 1px;">International</button>
                <button id="btnTun" class="btn-view-details" style="flex: 1; margin: 0; border-width: 1px; background: var(--info-blue); color: white;">Tunisie (EDinar)</button>
                <button id="btnCash" class="btn-view-details" style="flex: 1; margin: 0; border-width: 1px; background: var(--success-green); color: white;">Cash (Livraison)</button>
            </div>

            <div id="locationContainer" style="display: none;" class="card-input-group">
                <label>Lieu de livraison (Ariana uniquement)</label>
                <select id="arianaLocation" style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; outline: none;">
                    <option value="Sokra">La Soukra</option>
                    <option value="Borj Louzir">Borj Louzir</option>
                    <option value="Cite Ghazela">Cité El Ghazala</option>
                    <option value="Ennasr">Ennasr</option>
                    <option value="Ariana Ville">Ariana Ville</option>
                    <option value="Raoued">Raoued</option>
                    <option value="Mnihla">Mnihla</option>
                </select>
            </div>
            <div id="exactLocationContainer" style="display: none;" class="card-input-group">
                <label>Adresse exacte (Rue, Immeuble, Appt...)</label>
                <input type="text" id="exactLocation" placeholder="Ex: Rue Mongi Slim, Résidence Ennasr..." style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; outline: none;">
                <div id="locationError" class="error-msg">L'adresse exacte est obligatoire pour la livraison.</div>
            </div>

            <div id="tunBanks" class="bank-grid">
                <div class="bank-item active">BIAT</div>
                <div class="bank-item">ZITOUNA</div>
                <div class="bank-item">ATB</div>
                <div class="bank-item">TIJARI</div>
                <div class="bank-item">BNA</div>
                <div class="bank-item">POSTE</div>
                <div class="bank-item">D17</div>
                <div class="bank-item">STB</div>
            </div>

            <form id="paymentForm">
                <div class="card-input-group">
                    <label>Numéro de Carte</label>
                    <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19">
                    <div id="cardError" class="error-msg">Numéro de carte invalide</div>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div class="card-input-group" style="flex: 1;">
                        <label>Date d'expiration</label>
                        <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5">
                        <div id="expiryError" class="error-msg">Format MM/YY requis</div>
                    </div>
                    <div class="card-input-group" style="flex: 1;">
                        <label>CVV</label>
                        <input type="password" id="cardCvv" placeholder="123" maxlength="3">
                        <div id="cvvError" class="error-msg">3 chiffres requis</div>
                    </div>
                </div>

                <button type="submit" class="btn-checkout" style="margin-top: 10px;">Confirmer le Paiement</button>
            </form>
        </div>
    </div>

    <div id="successModal">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="success-title">Succès !</div>
            <div id="successText" class="success-text">Votre commande a été enregistrée avec succès.</div>
            <button class="btn-success-close" id="closeSuccessBtn">Génial !</button>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle" style="color: #4CAF50; font-size: 1.2rem;"></i>
        <span id="toastMsg">Produit ajouté !</span>
    </div>

    <script>
        const cartBtn = document.getElementById('cartBtn');
        const globalLoader = document.getElementById('globalLoader');
        const cartSidebar = document.getElementById('cartSidebar');
        const closeCart = document.getElementById('closeCart');
        const overlay = document.getElementById('overlay');
        const cartItemsList = document.getElementById('cartItemsList');
        const cartTotalValue = document.getElementById('cartTotalValue');
        const cartCount = document.getElementById('cartCount');
        const btnCheckout = document.getElementById('btnCheckout');
        const toast = document.getElementById('toast');

        // Payment Modal
        const paymentModal = document.getElementById('paymentModal');
        const closePayment = document.getElementById('closePayment');
        const paymentForm = document.getElementById('paymentForm');
        const tunBanks = document.getElementById('tunBanks');
        const btnIntl = document.getElementById('btnIntl');
        const btnTun = document.getElementById('btnTun');
        const payAmount = document.getElementById('payAmount');

        // Payment Inputs
        const cardNumInput = document.getElementById('cardNumber');
        const cardExpInput = document.getElementById('cardExpiry');
        const cardCvvInput = document.getElementById('cardCvv');

        // Modal Elements
        const detailsModal = document.getElementById('detailsModal');
        const closeModal = document.getElementById('closeModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalScore = document.getElementById('modalScore');
        const modalOrigine = document.getElementById('modalOrigine');
        const modalTransport = document.getElementById('modalTransport');
        const modalEmballage = document.getElementById('modalEmballage');
        const modalTransformation = document.getElementById('modalTransformation');
        const modalSaison = document.getElementById('modalSaison');
        const modalPrix = document.getElementById('modalPrix');
        const modalQty = document.getElementById('modalQty');
        const modalAddBtn = document.getElementById('modalAddBtn');

        const btnCash = document.getElementById('btnCash');
        const arianaLocation = document.getElementById('arianaLocation');

        let currentModalProductId = null;
        let currentMethod = 'tun';

        // Toggle Cart
        cartBtn.onclick = () => {
            cartSidebar.classList.add('open');
            overlay.classList.add('active');
            loadCart();
        };

        closeCart.onclick = () => {
            cartSidebar.classList.remove('open');
            overlay.classList.remove('active');
        };

        closeModal.onclick = () => {
            detailsModal.style.display = 'none';
            overlay.classList.remove('active');
        };

        closePayment.onclick = () => {
            paymentModal.style.display = 'none';
            overlay.classList.remove('active');
        };

        overlay.onclick = () => {
            cartSidebar.classList.remove('open');
            detailsModal.style.display = 'none';
            paymentModal.style.display = 'none';
            overlay.classList.remove('active');
        };

        function openDetails(p) {
            currentModalProductId = p.id;
            modalTitle.textContent = p.nom;
            modalScore.textContent = p.score + '/100';
            modalScore.style.color = (p.score >= 80) ? '#4CAF50' : ((p.score >= 50) ? '#FF9800' : '#F44336');
            modalOrigine.textContent = p.origine.charAt(0).toUpperCase() + p.origine.slice(1);
            if (p.origine === 'local') modalOrigine.innerHTML += ' 🌱';
            
            modalDistance.textContent = p.distance + ' km';
            modalTransport.textContent = p.transport.charAt(0).toUpperCase() + p.transport.slice(1);
            modalEmballage.textContent = p.emballage.charAt(0).toUpperCase() + p.emballage.slice(1);
            modalTransformation.textContent = p.transformation.charAt(0).toUpperCase() + p.transformation.slice(1);
            modalSaison.textContent = (p.saison || 'Toute l\'année').charAt(0).toUpperCase() + (p.saison || 'Toute l\'année').slice(1);
            
            modalPrix.textContent = parseFloat(p.prix).toFixed(2) + ' DT';
            modalQty.value = 1;
            modalQty.max = p.stock;
            
            detailsModal.style.display = 'flex';
            overlay.classList.add('active');
        }

        modalAddBtn.onclick = () => {
            addToCart(currentModalProductId, parseInt(modalQty.value));
            detailsModal.style.display = 'none';
            overlay.classList.remove('active');
        };

        function showToast(msg) {
            document.getElementById('toastMsg').textContent = msg;
            toast.style.display = 'flex';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        async function addToCart(id, qty = 1) {
            cartBtn.classList.add('pulse');
            setTimeout(() => cartBtn.classList.remove('pulse'), 500);

            const formData = new FormData();
            formData.append('id_produit', id);
            formData.append('quantite', qty);

            try {
                const res = await fetch('../../models/ecommerce_handler.php?action=add', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (data.success) {
                    showToast(qty > 1 ? qty + ' produits ajoutés au panier !' : 'Produit ajouté avec succès !');
                    updateCartCount();
                } else {
                    alert(data.message || 'Erreur lors de l\'ajout');
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function loadCart() {
            const res = await fetch('../../models/ecommerce_handler.php?action=get_cart');
            const data = await res.json();
            
            if (data.success) {
                cartItemsList.innerHTML = '';
                if (data.items.length === 0) {
                    cartItemsList.innerHTML = '<p style="text-align:center; padding: 20px; color: #999;">Votre panier est vide</p>';
                }
                
                data.items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'cart-item';
                    div.innerHTML = `
                        <div class="cart-item-img">🥘</div>
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.nom}</div>
                            <div class="cart-item-price">${item.quantite} x ${item.prix_unitaire} DT</div>
                        </div>
                        <i class="fas fa-trash" style="color: #ff4757; cursor:pointer;" onclick="removeFromCart(${item.id_produit})"></i>
                    `;
                    cartItemsList.appendChild(div);
                });
                
                cartTotalValue.textContent = parseFloat(data.total).toFixed(2) + ' DT';
                cartCount.textContent = data.items.length;
            }
        }

        async function removeFromCart(id) {
            const formData = new FormData();
            formData.append('id_produit', id);
            
            await fetch('../../models/ecommerce_handler.php?action=remove', {
                method: 'POST',
                body: formData
            });
            loadCart();
        }

        async function updateCartCount() {
            const res = await fetch('../../models/ecommerce_handler.php?action=get_cart');
            const data = await res.json();
            if (data.success) {
                cartCount.textContent = data.items.length;
                let total = parseFloat(data.total);
                if (data.items.length > 0 && currentMethod === 'cash') {
                    total += 7;
                    document.getElementById('deliveryFeeRow').style.display = 'flex';
                } else {
                    document.getElementById('deliveryFeeRow').style.display = 'none';
                }
                cartTotalValue.textContent = total.toFixed(2) + " DT";
                payAmount.textContent = total.toFixed(2) + " DT";
            }
        }

=======
<<<<<<< HEAD
    <footer class="simple-footer">
        <p><i class="fas fa-heart" style="color: #ff4757;"></i> NutriLoop AI - Manger sainement pour une vie meilleure</p>
    </footer>

    <script>
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        function filterProducts() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            const cards = document.querySelectorAll('.product-card');
<<<<<<< HEAD
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;
=======
            let matchCount = 0;
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647

            cards.forEach(card => {
                const nom = card.getAttribute('data-nom');
                const cat = card.getAttribute('data-cat');
                
                const matchesSearch = nom.includes(query);
                const matchesCat = category === '' || cat === category;

                if (matchesSearch && matchesCat) {
                    card.style.display = 'flex';
<<<<<<< HEAD
                    visibleCount++;
=======
                    matchCount++;
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
                } else {
                    card.style.display = 'none';
                }
            });

<<<<<<< HEAD
            noResults.style.display = (visibleCount === 0) ? 'flex' : 'none';
        }

        function resetSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            filterProducts();
        }

        const successModal = document.getElementById('successModal');
        const successText = document.getElementById('successText');
        const closeSuccessBtn = document.getElementById('closeSuccessBtn');

        function showSuccess(msg, reload = false) {
            successText.textContent = msg;
            successModal.style.display = 'flex';
            closeSuccessBtn.onclick = () => {
                successModal.style.display = 'none';
                if (reload) location.reload();
            };
        }

        btnCheckout.onclick = async () => {
            const res = await fetch('../../models/ecommerce_handler.php?action=get_cart');
            const data = await res.json();
            if (data.items.length === 0) return alert('Panier vide');
            
            updateCartCount(); // Ensure total is fresh with current method
            cartSidebar.classList.remove('open');
            paymentModal.style.display = 'flex';
            overlay.classList.add('active');
        };

        // Payment Navigation

        btnIntl.onclick = () => {
            currentMethod = 'intl';
            btnIntl.style.background = 'var(--info-blue)'; btnIntl.style.color = 'white';
            btnTun.style.background = 'transparent'; btnTun.style.color = 'var(--info-blue)';
            btnCash.style.background = 'transparent'; btnCash.style.color = 'var(--success-green)';
            tunBanks.style.display = 'none';
            document.getElementById('locationContainer').style.display = 'none';
            document.getElementById('exactLocationContainer').style.display = 'none';
            paymentForm.style.display = 'block';
            updateCartCount();
        };
        btnTun.onclick = () => {
            currentMethod = 'tun';
            btnTun.style.background = 'var(--info-blue)'; btnTun.style.color = 'white';
            btnIntl.style.background = 'transparent'; btnIntl.style.color = 'var(--info-blue)';
            btnCash.style.background = 'transparent'; btnCash.style.color = 'var(--success-green)';
            tunBanks.style.display = 'grid';
            document.getElementById('locationContainer').style.display = 'none';
            document.getElementById('exactLocationContainer').style.display = 'none';
            paymentForm.style.display = 'block';
            updateCartCount();
        };
        btnCash.onclick = () => {
            currentMethod = 'cash';
            btnCash.style.background = 'var(--success-green)'; btnCash.style.color = 'white';
            btnIntl.style.background = 'transparent'; btnIntl.style.color = 'var(--info-blue)';
            btnTun.style.background = 'transparent'; btnTun.style.color = 'var(--info-blue)';
            tunBanks.style.display = 'none';
            paymentForm.style.display = 'none';
            document.getElementById('locationContainer').style.display = 'block';
            document.getElementById('exactLocationContainer').style.display = 'block';
            updateCartCount();
            
            // Re-create the button to ensure clean click handler
            const oldBtn = document.getElementById('confirmCashBtn');
            if (oldBtn) oldBtn.remove();

            const confirmCashBtn = document.createElement('button');
            confirmCashBtn.id = 'confirmCashBtn';
            confirmCashBtn.className = 'btn-checkout';
            confirmCashBtn.style.marginTop = '20px';
            confirmCashBtn.textContent = 'Confirmer Commande Cash';
            confirmCashBtn.type = 'button'; // Prevent any form auto-submission
            confirmCashBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const exact = document.getElementById('exactLocation').value.trim();
                if (!exact) {
                    document.getElementById('locationError').style.display = 'block';
                    return;
                }
                document.getElementById('locationError').style.display = 'none';
                submitOrder();
            });
            
            document.getElementById('exactLocationContainer').appendChild(confirmCashBtn);
        };

        async function submitOrder() {
            globalLoader.style.display = 'flex';
            try {
                // 1. Validate and Create Order (pass method to handle fee)
                const validateFormData = new FormData();
                validateFormData.append('method', currentMethod);

                const resVal = await fetch('../../models/ecommerce_handler.php?action=validate', {
                    method: 'POST',
                    body: validateFormData
                });
                const dataVal = await resVal.json();
                
                if (dataVal.success) {
                    // 2. Process Payment
                    const payFormData = new FormData();
                    payFormData.append('id_commande', dataVal.id_commande);
                    payFormData.append('method', currentMethod);
                    const fullLocation = arianaLocation.value + " - " + document.getElementById('exactLocation').value.trim();
                    payFormData.append('location', fullLocation);
                    
                    const resPay = await fetch('../../models/ecommerce_handler.php?action=pay', {
                        method: 'POST',
                        body: payFormData
                    });
                    const dataPay = await resPay.json();
                    
                    if (dataPay.success) {
                        setTimeout(() => {
                            globalLoader.style.display = 'none';
                            const msg = currentMethod === 'cash' ? 'Commande enregistrée ! Paiement Cash à la livraison.' : 'Paiement accepté ! Votre commande a été enregistrée.';
                            showSuccess(msg + ' Un email de confirmation vous a été envoyé.', true);
                            paymentModal.style.display = 'none';
                            overlay.classList.remove('active');
                        }, 1500);
                    } else {
                        globalLoader.style.display = 'none';
                        showSuccess(dataPay.message || "Erreur lors du paiement");
                    }
                } else {
                    globalLoader.style.display = 'none';
                    showSuccess(dataVal.message || "Erreur de validation");
                }
            } catch (e) {
                console.error(e);
                globalLoader.style.display = 'none';
                alert('Erreur technique. Veuillez vérifier la console.');
            }
        }

        paymentForm.onsubmit = (e) => {
            e.preventDefault();
            let isValid = true;
            document.querySelectorAll('.error-msg').forEach(m => m.style.display = 'none');

            const cardVal = cardNumInput.value.replace(/\s/g, '');
            if (cardVal.length < 16) { document.getElementById('cardError').style.display = 'block'; isValid = false; }
            if (!/^\d{2}\/\d{2}$/.test(cardExpInput.value)) { document.getElementById('expiryError').style.display = 'block'; isValid = false; }
            if (cardCvvInput.value.length < 3) { document.getElementById('cvvError').style.display = 'block'; isValid = false; }

            if (isValid) submitOrder();
        };

        // Input Masking
        cardNumInput.oninput = (e) => {
            e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        };
        cardExpInput.oninput = (e) => {
            e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{2})/, '$1/').trim();
        };

        updateCartCount();

        // Load AI Recommendations
        async function loadAIRecommendations() {
            try {
                const res = await fetch('../../models/ecommerce_handler.php?action=ai_recommendations');
                const data = await res.json();
                
                if (data.success && data.data.recommendations) {
                    const grid = document.getElementById('aiRecommendationsGrid');
                    const section = document.getElementById('aiSection');
                    const advice = document.getElementById('aiAdvice');
                    
                    grid.innerHTML = '';
                    advice.textContent = data.data.global_advice || 'Découvrez nos suggestions pour une consommation plus durable.';
                    
                    data.data.recommendations.forEach(rec => {
                        const card = document.createElement('div');
                        card.className = 'ai-card';
                        card.innerHTML = `
                            <div class="ai-card-title"><i class="fas fa-star"></i> ${rec.nom}</div>
                            <div class="ai-card-reason">${rec.reason}</div>
                        `;
                        grid.appendChild(card);
                    });
                    
                    section.style.display = 'block';
                }
            } catch (e) {
                console.error("AI Recommendation failed:", e);
            }
        }

        loadAIRecommendations();

        // NutriBot Logic
        const nutriBotBtn = document.getElementById('nutriBotBtn');
        const nutriBotChat = document.getElementById('nutriBotChat');
        const closeChat = document.getElementById('closeChat');
        const chatInput = document.getElementById('chatInput');
        const sendChatBtn = document.getElementById('sendChatBtn');
        const chatMessages = document.getElementById('chatMessages');

        nutriBotBtn.onclick = () => {
            nutriBotChat.style.display = (nutriBotChat.style.display === 'flex') ? 'none' : 'flex';
        };

        closeChat.onclick = () => nutriBotChat.style.display = 'none';

        async function sendChatMessage() {
            const text = chatInput.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            chatInput.value = '';

            const formData = new FormData();
            formData.append('chat_message', text);

            try {
                const res = await fetch('../../models/chatbot.php', {
                    method: 'POST',
                    body: formData
                });
                const responseText = await res.text();
                addMessage(responseText, 'bot');
            } catch (e) {
                addMessage("Désolé, j'ai eu un petit problème technique. 🤖", 'bot');
            }
        }

        function addMessage(text, side) {
            const div = document.createElement('div');
            div.className = `msg msg-${side}`;
            div.innerHTML = text; // Allow HTML from chatbot
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        sendChatBtn.onclick = sendChatMessage;
        chatInput.onkeypress = (e) => { if (e.key === 'Enter') sendChatMessage(); };
=======
            document.getElementById('noMatch').style.display = matchCount === 0 && cards.length > 0 ? 'block' : 'none';
        }

        function showProduitDetails(p) {
            const modal = document.getElementById('detailsModal');
            document.getElementById('modalTitle').textContent = p.nom;
            document.getElementById('detailOrigine').textContent = p.origine === 'local' ? 'Local 🌱' : 'Importé 🌍';
            document.getElementById('detailDistance').textContent = p.distance + ' km';
            document.getElementById('detailTransport').textContent = p.transport;
            document.getElementById('detailEmballage').textContent = p.emballage;
            document.getElementById('detailTransformation').textContent = p.transformation;
            document.getElementById('detailSaison').textContent = p.saison || 'Toute l\'année';
            modal.style.display = 'block';
        }

        // Close Modal
        document.querySelector('.close').onclick = () => document.getElementById('detailsModal').style.display = 'none';
        window.onclick = (e) => { if (e.target == document.getElementById('detailsModal')) document.getElementById('detailsModal').style.display = 'none'; }
=======
    <script src="../assets/js/recette.js"></script>
    <script>
        function showProduitDetails(produit) {
            const modal = document.getElementById('detailsModal');
            document.getElementById('modalTitle').textContent = produit.nom;
            document.getElementById('detailOrigine').textContent = produit.origine === 'local' ? 'Local' : 'Importé';
            document.getElementById('detailDistance').textContent = produit.distance_transport + ' km';
            document.getElementById('detailTransport').textContent = produit.type_transport;
            document.getElementById('detailEmballage').textContent = produit.emballage;
            document.getElementById('detailTransformation').textContent = produit.transformation;
            document.getElementById('detailSaison').textContent = produit.saison || 'N/A';
            modal.style.display = 'block';
        }

        // Fermeture du modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
    </script>
</body>
</html>
