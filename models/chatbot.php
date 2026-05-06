<?php
/**
 * NutriLoop AI - Smart Chatbot Assistant
 * Moved to models for architectural consolidation
 */

require_once __DIR__ . '/ecommerce_functions.php';

/**
 * Main Chatbot Logic
 */
function handleChatbot($pdo, $user_id, $message) {
    $msg = strtolower($message);
    
    // 1. ECO SCORE EXPLANATION
    if (containsAny($msg, ['eco', 'score', 'pourquoi', 'bad', 'good', 'sain'])) {
        return handleEcoExplanation($pdo, $msg);
    }
    
    // 2. PRODUCT RECOMMENDATION
    if (containsAny($msg, ['recommend', 'similar', 'suggère', 'propose', 'idée'])) {
        return handleRecommendations($pdo);
    }
    
    // 3. CART DISPLAY
    if (containsAny($msg, ['cart', 'panier', 'mes articles', 'achat'])) {
        return handleCartDisplay($pdo, $user_id);
    }
    
    // 4. ORDER STATUS
    if (containsAny($msg, ['order', 'commande', 'status', 'état'])) {
        return handleOrderStatus($pdo, $user_id);
    }
    
    // 5. SMART ECO IMPROVEMENT
    if (containsAny($msg, ['improve', 'mieux', 'optimise', 'écologique', 'mieux manger'])) {
        return handleEcoImprovement($pdo, $user_id);
    }
    
    // 6. PRODUCT COMPARISON
    if (strpos($msg, 'compar') !== false || strpos($msg, ' vs ') !== false || strpos($msg, ' entre ') !== false) {
        $stmt = $pdo->query("SELECT * FROM produit");
        $allProducts = $stmt->fetchAll();
        $found = [];

        foreach ($allProducts as $p) {
            if (strpos($msg, strtolower($p['nom'])) !== false) {
                $found[] = $p;
            }
            if (count($found) >= 2) break;
        }

        if (count($found) >= 2) {
            $p1 = $found[0];
            $p2 = $found[1];
            
            $score1 = $p1['eco_score'];
            $score2 = $p2['eco_score'];
            
            $c1 = ($score1 >= 80) ? '#4CAF50' : (($score1 >= 50) ? '#FF9800' : '#F44336');
            $c2 = ($score2 >= 80) ? '#4CAF50' : (($score2 >= 50) ? '#FF9800' : '#F44336');

            return "📊 <strong>Comparaison :</strong><br>
                    <div style='display:flex; gap:10px; margin-top:10px;'>
                        <div style='flex:1; background:#f9f9f9; padding:10px; border-radius:10px; border-left:4px solid $c1;'>
                            <strong>{$p1['nom']}</strong><br>
                            ⭐ Score: <span style='color:$c1; font-weight:bold;'>$score1</span><br>
                            📍 {$p1['origine']}<br>
                            💰 {$p1['prix']} DT
                        </div>
                        <div style='flex:1; background:#f9f9f9; padding:10px; border-radius:10px; border-left:4px solid $c2;'>
                            <strong>{$p2['nom']}</strong><br>
                            ⭐ Score: <span style='color:$c2; font-weight:bold;'>$score2</span><br>
                            📍 {$p2['origine']}<br>
                            💰 {$p2['prix']} DT
                        </div>
                    </div>
                    <br>" . ($score1 > $score2 ? "✅ <strong>{$p1['nom']}</strong> est plus écologique !" : "✅ <strong>{$p2['nom']}</strong> est un meilleur choix pour la planète !");
        }
        return "Désolé, je n'ai pas trouvé les deux produits à comparer. Essayez d'écrire leurs noms exactement comme sur le site. 🔍";
    }

    // 7. DEFAULT RESPONSE
    return "Bonjour ! Je suis votre assistant NutriLoop. Je peux vous aider à :<br>
            • <b>Recommander</b> des produits sains<br>
            • Expliquer votre <b>Eco-score</b><br>
            • Voir votre <b>panier</b> ou vos <b>commandes</b><br>
            • <b>Optimiser</b> votre panier pour l'écologie";
}

/**
 * HELPERS
 */
function containsAny($str, $keywords) {
    foreach ($keywords as $kw) {
        if (strpos($str, $kw) !== false) return true;
    }
    return false;
}

function handleEcoExplanation($pdo, $msg) {
    $stmt = $pdo->query("SELECT * FROM produit");
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $found = null;
    foreach ($all as $p) {
        if (strpos($msg, strtolower($p['nom'])) !== false) {
            $found = $p;
            break;
        }
    }
    if (!$found) return "De quel produit souhaitez-vous connaître l'Eco-score ? (ex: 'score tomate')";
    $score = $found['eco_score'];
    $color = $score >= 80 ? '#2E7D32' : ($score >= 50 ? '#F9A825' : '#C62828');
    $html = "L'Eco-score de <b>" . $found['nom'] . "</b> est de <b style='color:$color'>$score/100</b>.<br><br>";
    $html .= "<b>Détails :</b><br>";
    $html .= "• Origine : " . ($found['origine'] == 'local' ? 'Local 🌱' : 'Importé 🚢') . "<br>";
    $html .= "• Transport : " . $found['distance_transport'] . " km (" . $found['type_transport'] . ")<br>";
    $html .= "• Emballage : " . $found['emballage'] . "<br>";
    $html .= "• Transformation : " . $found['transformation'];
    return $html;
}

function handleRecommendations($pdo) {
    $stmt = $pdo->query("SELECT * FROM produit ORDER BY eco_score DESC LIMIT 4");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = "Voici nos recommandations les plus écologiques :<br><br>";
    foreach ($items as $p) {
        $color = $p['eco_score'] >= 80 ? '#2E7D32' : '#F9A825';
        $html .= "• <b>" . $p['nom'] . "</b> - " . $p['prix'] . " DT <span style='color:$color'>(Eco: " . $p['eco_score'] . ")</span><br>";
    }
    return $html;
}

function handleCartDisplay($pdo, $user_id) {
    $items = getPanier($pdo, $user_id);
    if (empty($items)) return "Votre panier est actuellement vide. 🛒";
    $total = getTotalPanier($pdo, $user_id);
    $html = "Voici votre panier :<br>";
    foreach ($items as $i) {
        $html .= "• " . $i['nom'] . " (x" . $i['quantite'] . ") - " . ($i['prix_unitaire'] * $i['quantite']) . " DT<br>";
    }
    $html .= "<br><b>Total : $total DT</b>";
    return $html;
}

function handleOrderStatus($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM commande WHERE user_id = ? ORDER BY date_commande DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) return "Vous n'avez pas encore passé de commande.";
    $statut = $order['statut'] == 'paye' ? 'Payée ✅' : ($order['statut'] == 'en_attente' ? 'En attente ⏳' : $order['statut']);
    return "Votre dernière commande <b>#" . $order['id_commande'] . "</b> est actuellement : <b>$statut</b>.";
}

function handleEcoImprovement($pdo, $user_id) {
    $cart = getPanier($pdo, $user_id);
    if (empty($cart)) return "Ajoutez des produits à votre panier pour que je puisse les optimiser !";
    $suggestions = [];
    foreach ($cart as $item) {
        if ($item['eco_score'] < 70) {
            $stmt = $pdo->prepare("SELECT * FROM produit WHERE id_categorie = ? AND eco_score > ? ORDER BY eco_score DESC LIMIT 1");
            $stmt->execute([$item['id_categorie'], $item['eco_score']]);
            $better = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($better) {
                $suggestions[] = "• Remplacer <b>" . $item['nom'] . "</b> (Eco: " . $item['eco_score'] . ") par <b>" . $better['nom'] . "</b> (Eco: <span style='color:#2E7D32'>" . $better['eco_score'] . "</span>)";
            }
        }
    }
    if (empty($suggestions)) return "Félicitations ! Votre panier est déjà très optimisé pour l'environnement. 🌱";
    return "Voici comment améliorer l'impact de votre panier :<br><br>" . implode("<br>", $suggestions);
}

// AJAX Handler
if (isset($_POST['chat_message'])) {
    require_once '../config.php';
    $pdo = Config::getConnexion();
    $user_id = 1; // Simulation
    echo handleChatbot($pdo, $user_id, $_POST['chat_message']);
}
