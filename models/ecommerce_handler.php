<?php
session_start();
require_once '../config.php';
require_once 'ecommerce_functions.php';

header('Content-Type: application/json');

$pdo = Config::getConnexion();
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for demo

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $id_produit = intval($_POST['id_produit'] ?? 0);
        $quantite = intval($_POST['quantite'] ?? 1);
        $result = ajouterAuPanier($pdo, $user_id, $id_produit, $quantite);
        echo json_encode($result);
        break;

    case 'get_cart':
        $items = getPanier($pdo, $user_id);
        $total = getTotalPanier($pdo, $user_id);
        echo json_encode(['success' => true, 'items' => $items, 'total' => $total]);
        break;

    case 'remove':
        $id_produit = intval($_POST['id_produit'] ?? 0);
        $result = retirerDuPanier($pdo, $user_id, $id_produit);
        echo json_encode(['success' => $result]);
        break;

    case 'validate':
        $is_cash = ($_POST['method'] ?? '') === 'cash';
        $result = validerCommande($pdo, $user_id, $is_cash);
        echo json_encode($result);
        break;

    case 'pay':
        $id_commande = intval($_POST['id_commande'] ?? 0);
        $method = $_POST['method'] ?? 'tun';
        $location = $_POST['location'] ?? '';
        $result = payerCommande($pdo, $id_commande, $method, $location);
        echo json_encode($result);
        break;

    case 'ai_recommendations':
        require_once '../controleurs/ProduitController.php';
        require_once '../controleurs/AIPredictionController.php';
        
        $pc = new ProduitController();
        $ai = new AIPredictionController();
        
        $products = $pc->listProduits();
        $recommendations = $ai->getProductRecommendations($products);
        
        if ($recommendations) {
            echo json_encode(['success' => true, 'data' => $recommendations]);
        } else {
            echo json_encode(['success' => false, 'message' => 'AI unavailable']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action inconnue']);
        break;
}
