<?php


function calculateEcoScore($produit) {
    $score = 0;
    
    // 1. Origine
    if (($produit['origine'] ?? '') === 'local') {
        $score += 40;
    } else if (($produit['origine'] ?? '') === 'importe') {
        $score += 5;
    }
    
    // 2. Distance Transport
    $dist = intval($produit['distance_transport'] ?? 0);
    if ($dist < 100) {
        $score += 30;
    } else if ($dist >= 100 && $dist <= 500) {
        $score += 15;
    } else if ($dist > 500 && $dist <= 1000) {
        $score += 5;
    }
    
    // 3. Emballage
    $emb = $produit['emballage'] ?? '';
    if ($emb === 'aucun') {
        $score += 15;
    } else if ($emb === 'carton') {
        $score += 10;
    } else if ($emb === 'plastique') {
        $score += 0;
    }
    
    // 4. Transformation
    $trans = $produit['transformation'] ?? '';
    if ($trans === 'brut') {
        $score += 15;
    } else if ($trans === 'transforme') {
        $score += 5;
    } else if ($trans === 'ultra_transforme') {
        $score += 0;
    }
    
    return min(100, $score);
}

function getEcoScoreBadgeColor($score) {
    if ($score >= 80) return 'green';
    if ($score >= 50) return 'yellow';
    return 'red';
}

/**
 * 6. PRODUCT RECOMMENDATION
 */
function getRecommendations($pdo, $id_produit) {
    $stmt = $pdo->prepare("SELECT id_categorie FROM produit WHERE id_produit = ?");
    $stmt->execute([$id_produit]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produit || !$produit['id_categorie']) {
        return [];
    }
    
    $id_categorie = $produit['id_categorie'];
    $sql = "SELECT * FROM produit WHERE id_categorie = ? AND id_produit != ? ORDER BY eco_score DESC LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_categorie, $id_produit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 1. PANIER LOGIC
 */
function getPanier($pdo, $user_id) {
    $sql = "SELECT pi.*, p.nom, p.image, p.stock 
            FROM panier_item pi
            JOIN panier pan ON pi.id_panier = pan.id_panier
            JOIN produit p ON pi.id_produit = p.id_produit
            WHERE pan.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ajouterAuPanier($pdo, $user_id, $id_produit, $quantite) {
    $stmt = $pdo->prepare("SELECT stock, prix FROM produit WHERE id_produit = ?");
    $stmt->execute([$id_produit]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produit || $quantite > $produit['stock']) {
        return ["success" => false, "message" => "Stock insuffisant"];
    }

    $stmt = $pdo->prepare("INSERT IGNORE INTO panier (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    
    $stmt = $pdo->prepare("SELECT id_panier FROM panier WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $id_panier = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT id_item, quantite FROM panier_item WHERE id_panier = ? AND id_produit = ?");
    $stmt->execute([$id_panier, $id_produit]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $new_qty = $existing['quantite'] + $quantite;
        if ($new_qty > $produit['stock']) {
            return ["success" => false, "message" => "Stock total insuffisant"];
        }
        $stmt = $pdo->prepare("UPDATE panier_item SET quantite = ? WHERE id_item = ?");
        $stmt->execute([$new_qty, $existing['id_item']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO panier_item (id_panier, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_panier, $id_produit, $quantite, $produit['prix']]);
    }
    
    return ["success" => true];
}

function retirerDuPanier($pdo, $user_id, $id_produit) {
    $stmt = $pdo->prepare("DELETE pi FROM panier_item pi JOIN panier p ON pi.id_panier = p.id_panier WHERE p.user_id = ? AND pi.id_produit = ?");
    return $stmt->execute([$user_id, $id_produit]);
}

function mettreAJourQuantite($pdo, $user_id, $id_produit, $nouvelle_quantite) {
    $stmt = $pdo->prepare("SELECT stock FROM produit WHERE id_produit = ?");
    $stmt->execute([$id_produit]);
    $stock = $stmt->fetchColumn();
    
    if ($nouvelle_quantite > $stock) {
        return ["success" => false, "message" => "Stock insuffisant"];
    }

    $stmt = $pdo->prepare("UPDATE panier_item pi JOIN panier p ON pi.id_panier = p.id_panier SET pi.quantite = ? WHERE p.user_id = ? AND pi.id_produit = ?");
    $stmt->execute([$nouvelle_quantite, $user_id, $id_produit]);
    return ["success" => true];
}

function getTotalPanier($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT SUM(quantite * prix_unitaire) FROM panier_item pi JOIN panier p ON pi.id_panier = p.id_panier WHERE p.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() ?: 0;
}

/**
 * 2. COMMANDE LOGIC
 */
function validerCommande($pdo, $user_id, $is_cash = false) {
    try {
        $pdo->beginTransaction();
        $items = getPanier($pdo, $user_id);
        if (empty($items)) throw new Exception("Le panier est vide");

        $total = 0;
        foreach ($items as $item) {
            $stmt = $pdo->prepare("SELECT stock FROM produit WHERE id_produit = ? FOR UPDATE");
            $stmt->execute([$item['id_produit']]);
            $current_stock = $stmt->fetchColumn();
            
            if ($item['quantite'] > $current_stock) throw new Exception("Stock insuffisant pour le produit: " . $item['nom']);
            $total += $item['quantite'] * $item['prix_unitaire'];
        }

        if ($is_cash) $total += 7; 
        $stmt = $pdo->prepare("INSERT INTO commande (user_id, total, statut) VALUES (?, ?, 'en_attente')");
        $stmt->execute([$user_id, $total]);
        $id_commande = $pdo->lastInsertId();

        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO commande_item (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_commande, $item['id_produit'], $item['quantite'], $item['prix_unitaire']]);
            $stmt = $pdo->prepare("UPDATE produit SET stock = stock - ? WHERE id_produit = ?");
            $stmt->execute([$item['quantite'], $item['id_produit']]);
        }

        $stmt = $pdo->prepare("DELETE pi FROM panier_item pi JOIN panier p ON pi.id_panier = p.id_panier WHERE p.user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        return ["success" => true, "id_commande" => $id_commande];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ["success" => false, "message" => $e->getMessage()];
    }
}

/**
 * 3. PAIEMENT LOGIC
 */
function payerCommande($pdo, $id_commande, $methode = 'carte', $location = '') {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT total FROM commande WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        $total = $stmt->fetchColumn();
        if ($total === false) throw new Exception("Commande non trouvée");

        $stmt = $pdo->prepare("SELECT id_paiement FROM paiement WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        $id_paiement = $stmt->fetchColumn();

        if (!$id_paiement) {
            $stmt = $pdo->prepare("INSERT INTO paiement (id_commande, methode, statut, montant) VALUES (?, ?, 'en_attente', ?)");
            $stmt->execute([$id_commande, $methode, $total]);
        }

        $final_statut = ($methode === 'cash') ? 'en_attente' : 'valide';
        $stmt = $pdo->prepare("UPDATE paiement SET statut = ?, date_paiement = CURRENT_TIMESTAMP WHERE id_commande = ?");
        $stmt->execute([$final_statut, $id_commande]);

        $stmt = $pdo->prepare("UPDATE commande SET statut = 'paye' WHERE id_commande = ?");
        $stmt->execute([$id_commande]);

        $pdo->commit();
        sendOrderEmail($pdo, $id_commande, $location);
        return ["success" => true];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ["success" => false, "message" => $e->getMessage()];
    }
}

/**
 * 4. EMAIL NOTIFICATION
 */
function sendOrderEmail($pdo, $id_commande, $location = '') {
    $stmt = $pdo->prepare("SELECT c.*, u.email, u.nom as user_nom, u.prenom FROM commande c JOIN user u ON c.user_id = u.id_user WHERE c.id_commande = ?");
    $stmt->execute([$id_commande]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) return false;

    $stmt = $pdo->prepare("SELECT ci.*, p.nom FROM commande_item ci JOIN produit p ON ci.id_produit = p.id_produit WHERE ci.id_commande = ?");
    $stmt->execute([$id_commande]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($_ENV['SMTP_FROM'] ?? 'no-reply@nutriloop.com', 'NutriLoop AI');
        $mail->addAddress($order['email'], $order['prenom'] . ' ' . $order['user_nom']);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de votre commande NutriLoop AI #' . $id_commande;

        $itemsHtml = '<table border="1" cellpadding="5" style="border-collapse: collapse;"><thead><tr><th>Produit</th><th>Quantité</th><th>Prix Unitaire</th><th>Total</th></tr></thead><tbody>';
        foreach ($items as $item) {
            $subtotal = $item['quantite'] * $item['prix_unitaire'];
            $itemsHtml .= "<tr><td>{$item['nom']}</td><td>{$item['quantite']}</td><td>{$item['prix_unitaire']} DT</td><td>{$subtotal} DT</td></tr>";
        }
        $itemsHtml .= '</tbody></table>';

        $mail->Body = "
            <div style='font-family: Arial, sans-serif;'>
                <h2>Merci pour votre commande !</h2>
                <p>Bonjour <strong>{$order['prenom']}</strong>,</p>
                <p>Nous avons le plaisir de vous confirmer que votre commande <strong>#{$id_commande}</strong> a été enregistrée.</p>
                <p>📍 <strong>Lieu de livraison (Ariana) :</strong> {$location}</p>
                <p>🚚 <strong>Frais de livraison :</strong> 7.00 DT (Inclus dans le total)</p>
                <h3>Récapitulatif :</h3>
                {$itemsHtml}
                <p style='font-size: 1.2em;'><strong>Total : {$order['total']} DT</strong></p>
                <p>Statut de la commande : <strong>{$order['statut']}</strong></p>
                <br>
                <p>Cordialement,<br>L'équipe NutriLoop AI</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
