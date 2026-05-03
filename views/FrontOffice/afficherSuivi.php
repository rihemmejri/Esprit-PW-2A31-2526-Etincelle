<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../../controleurs/SuiviController.php';
include_once '../../controleurs/ObjectifController.php';
include_once '../../controleurs/AlertController.php';
include_once '../../controleurs/AIPredictionController.php';
require_once __DIR__ . '/../../models/suivi.php';

$suiviController = new SuiviController();
$objectifController = new ObjectifController();
$alertController = new AlertController();
$predictionController = new AIPredictionController();

$current_user_id = 1; // Assuming user 1 for demo
$alerts = $alertController->getAlertsByUser($current_user_id);
$latestPrediction = $predictionController->getLatestPrediction($current_user_id);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    if ($action === 'create' || $action === 'update') {
        $user_id = $_POST['user_id'] ?? 1;
        $id_objectif = $_POST['id_objectif'] ?? null;
        $date = $_POST['date'] ?? null;
        $poids = $_POST['poids'] ?? null;
        $calories_consommees = $_POST['calories_consommees'] ?? null;
        $calories_objectif = $_POST['calories_objectif'] ?? null;
        $eau_bue = $_POST['eau_bue'] ?? null;
        $eau_objectif = $_POST['eau_objectif'] ?? null;
        
        // Calculate remaining calories
        $calories_restant = null;
        if ($calories_objectif !== null && $calories_consommees !== null) {
            $calories_restant = $calories_objectif - $calories_consommees;
        }
        
        $suivi = new suivi($user_id, $id_objectif, $date, $poids, $calories_consommees, $calories_objectif, $calories_restant, $eau_bue, $eau_objectif);
        
        if ($action === 'update') {
            $suivi->setId($_POST['id_suivi'] ?? null);
            $success = $suiviController->updateSuivi($suivi);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Suivi modifié avec succès' : 'Erreur lors de la modification'
            ]);
        } else {
            $success = $suiviController->addSuivi($suivi);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Suivi ajouté avec succès' : 'Erreur lors de l\'ajout'
            ]);
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        $success = $suiviController->deleteSuivi($id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Suivi supprimé avec succès' : 'Erreur lors de la suppression'
        ]);
    }
    exit;
}

// Handle AJAX GET for details
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $suivi = $suiviController->getSuiviById($id);
        
        if ($suivi) {
            echo json_encode([
                'success' => true,
                'id' => $suivi->getId(),
                'id_objectif' => $suivi->getIdObjectif(),
                'date' => $suivi->getDate(),
                'poids' => $suivi->getPoids(),
                'calories_consommees' => $suivi->getCaloriesConsommees(),
                'calories_objectif' => $suivi->getCaloriesObjectif(),
                'eau_bue' => $suivi->getEauBue(),
                'eau_objectif' => $suivi->getEauObjectif()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Suivi non trouvé']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Display page
$suiviController = new SuiviController();
$objectifController = new ObjectifController();
$alertController = new AlertController();

$current_user_id = 1; // Assuming user 1 for now
$suivis = $suiviController->listSuivis();
$objectifs = $objectifController->listObjectifs();
$alerts = $alertController->getAlertsByUser($current_user_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Suivi Quotidien - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/fr.js'></script>
    <style>
        /* Styles additionnels pour les suivis */
        .suivi-card {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 5px solid var(--success-green);
            transition: all 0.3s ease;
        }

        .suivi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .suivi-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .suivi-date {
            background: linear-gradient(135deg, var(--success-green), var(--primary-light));
            color: var(--white);
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .suivi-actions {
            display: flex;
            gap: 10px;
        }

        .suivi-actions button {
            padding: 8px 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-suivi-btn {
            background: var(--primary-blue);
            color: var(--white);
        }

        .edit-suivi-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .delete-suivi-btn {
            background: var(--danger);
            color: var(--white);
        }

        .delete-suivi-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .suivi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 15px 0;
        }

        .suivi-item {
            background: var(--gray-light);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--gray-medium);
        }

        .suivi-item-label {
            font-size: 0.85em;
            color: var(--gray-dark);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .suivi-item-value {
            font-size: 1.5em;
            color: var(--primary-blue);
            font-weight: 700;
        }

        .suivi-item-unit {
            font-size: 0.75em;
            color: var(--gray-dark);
            margin-left: 5px;
            font-weight: normal;
        }

        /* Form styles */
        .form-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .form-header {
            background: linear-gradient(90deg, #2E7D32 0%, #003366 100%);
            color: var(--white);
            padding: 30px;
        }

        .form-title {
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
        }

        .form-content {
            padding: 40px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group select {
            padding: 12px 15px;
            border: 2px solid var(--gray-medium);
            border-radius: 10px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }

        /* Error and Valid states */
        .form-group input.error,
        .form-group select.error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }

        .form-group input.valid,
        .form-group select.valid {
            border-color: #28a745 !important;
            background-color: #f8fff8;
        }

        .field-error {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .field-error i {
            font-size: 0.9em;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
            color: var(--white);
            flex: 1;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .cancel-btn {
            background: var(--gray-light);
            color: var(--gray-dark);
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: none;
        }

        .hero-section {
            background: linear-gradient(90deg, #2E7D32 0%, #003366 100%);
            color: var(--white);
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .nav-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn-nav {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(5px);
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-nav:hover {
            background: white;
            color: var(--primary-blue);
        }

        .success-message, .error-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .success-message { background: #e8f5e9; color: #2e7d32; border-left: 5px solid #4caf50; }
        .error-message { background: #ffeerb; color: #c62828; border-left: 5px solid #f44336; }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }

        /* Alert Styles */
        .alerts-panel {
            margin-bottom: 30px;
        }
        
        .alert-card {
            background: white;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 6px solid #ccc;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from { transform: translateX(30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .alert-card.critical { border-left-color: #ef4444; background-color: #fef2f2; }
        .alert-card.warning { border-left-color: #f59e0b; background-color: #fffbeb; }
        .alert-card.success { border-left-color: #10b981; background-color: #f0fdf4; }
        .alert-card.info { border-left-color: #3b82f6; background-color: #eff6ff; }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .alert-icon {
            font-size: 1.5rem;
        }

        .critical .alert-icon { color: #ef4444; }
        .warning .alert-icon { color: #f59e0b; }
        .success .alert-icon { color: #10b981; }
        .info .alert-icon { color: #3b82f6; }

        .alert-message {
            font-weight: 600;
            color: #1f2937;
        }

        .alert-date {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .alert-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .critical .alert-badge { background: #fee2e2; color: #991b1b; }
        .warning .alert-badge { background: #fef3c7; color: #92400e; }
        .success .alert-badge { background: #d1fae5; color: #065f46; }
        .info .alert-badge { background: #dbeafe; color: #1e40af; }
        
        body { background: #f0f4f8; margin: 0; padding-bottom: 60px; }
        .simple-footer { background: #2E7D32; color: white; text-align: center; padding: 15px; position: fixed; bottom: 0; width: 100%; z-index: 100; }

        /* AI Chatbot Widget Styles */
        .ai-chatbot-widget {
            position: fixed;
            bottom: 80px;
            right: 30px;
            z-index: 5000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .chatbot-button {
            background: linear-gradient(135deg, #003366, #2E7D32);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid rgba(255,255,255,0.1);
        }

        .chatbot-button:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .chatbot-icon {
            width: 35px;
            height: 35px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .chatbot-label {
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .chatbot-popup {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            height: 480px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            animation: popupSlideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popupSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chatbot-popup.show {
            display: flex;
        }

        .chatbot-header {
            background: #003366;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-header h3 {
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .close-chatbot {
            cursor: pointer;
            opacity: 0.7;
            transition: 0.3s;
        }

        .close-chatbot:hover { opacity: 1; }

        .chatbot-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8f9fa;
        }

        .chat-msg {
            max-width: 85%;
            padding: 10px 14px;
            border-radius: 15px;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .chat-msg.ai {
            align-self: flex-start;
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .chat-msg.user {
            align-self: flex-end;
            background: #4CAF50;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .chatbot-input-area {
            padding: 12px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 8px;
        }

        .chatbot-input-area input {
            flex-grow: 1;
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 0.85rem;
        }

        .chatbot-input-area button {
            background: #4CAF50;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-form" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <div id="messageContainer" style="display: none; margin-bottom: 20px; padding: 15px 20px; border-radius: 12px; border-left: 5px solid #4CAF50; background: #e8f5e9; color: #2e7d32; font-weight: 500; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); animation: slideDown 0.4s ease-out;">
            <i class="fas fa-check-circle"></i>
            <span id="messageText"></span>
        </div>

        <style>
            @keyframes slideDown {
                from { transform: translateY(-20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        </style>

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="nav-toggle">
            </div>
            <h1>
                <i class="fas fa-chart-line"></i>
                Gestion des Suivis
            </h1>
            <p>Enregistrez vos métriques quotidiennes pour atteindre vos objectifs</p>
        </div>

        <!-- AI Insight Section -->
        <?php if ($latestPrediction && !empty($_SESSION['new_ai_action'])): ?>
        <div class="ai-insight-panel" style="margin-bottom: 30px; background: white; border-radius: 16px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-left: 6px solid <?= $latestPrediction['risk_level'] === 'ÉLEVÉ' ? '#ef4444' : ($latestPrediction['risk_level'] === 'MOYEN' ? '#f59e0b' : '#10b981') ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #1f2937;">
                    <i class="fas fa-robot" style="color: #6366f1;"></i>
                    Analyse de l'IA
                <span style="padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: <?= $latestPrediction['risk_level'] === 'ÉLEVÉ' ? '#fef2f2' : ($latestPrediction['risk_level'] === 'MOYEN' ? '#fffbeb' : '#f0fdf4') ?>; color: <?= $latestPrediction['risk_level'] === 'ÉLEVÉ' ? '#ef4444' : ($latestPrediction['risk_level'] === 'MOYEN' ? '#f59e0b' : '#10b981') ?>;">
                    Risque: <?= $latestPrediction['risk_level'] ?>
                </span>
            </div>
            <p style="margin: 0; color: #4b5563; line-height: 1.6; font-size: 0.95rem;">
                <?= htmlspecialchars($latestPrediction['prediction']) ?>
            </p>
            <div style="margin-top: 15px; font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 5px;">
                <i class="far fa-clock"></i>
                Dernière analyse: <?= date('d/m/Y', strtotime($latestPrediction['date'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Intelligent Alerts Panel -->
        <?php if (!empty($alerts) && !empty($_SESSION['new_ai_action'])): ?>
        <div class="alerts-panel">
            <h3 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px; color: #1f2937;">
                <i class="fas fa-bell" style="color: #f59e0b;"></i>
                Alertes Intelligentes
            </h3>
            <div class="alerts-container">
                <?php foreach (array_slice($alerts, 0, 5) as $alert): 
                    $typeClass = strtolower($alert['type']);
                    $icon = 'fa-info-circle';
                    if ($alert['type'] === 'CRITICAL') $icon = 'fa-exclamation-triangle';
                    if ($alert['type'] === 'WARNING') $icon = 'fa-exclamation-circle';
                    if ($alert['type'] === 'SUCCESS') $icon = 'fa-check-circle';
                ?>
                    <div class="alert-card <?= $typeClass ?>" data-id="<?= $alert['id'] ?>">
                        <div class="alert-content">
                            <div class="alert-icon">
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <div>
                                <div class="alert-message"><?= htmlspecialchars($alert['message']) ?></div>
                                <div class="alert-date"><?= date('d M Y', strtotime($alert['date'])) ?></div>
                            </div>
                        </div>
                        <span class="alert-badge"><?= $alert['type'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
            (function() {
                const seenAlertKey = 'nutriloop_seen_alerts_v2';
                const seenAlerts = JSON.parse(localStorage.getItem(seenAlertKey) || '[]');
                
                document.addEventListener('DOMContentLoaded', function() {
                    const alerts = document.querySelectorAll('.alert-card');
                    const panel = document.querySelector('.alerts-panel');
                    let activeCount = 0;

                    alerts.forEach(alert => {
                        const alertId = alert.getAttribute('data-id');
                        const msg = alert.querySelector('.alert-message').textContent.trim();
                        const uniqueKey = alertId + '_' + msg;
                        
                        if (seenAlerts.includes(uniqueKey)) {
                            alert.style.display = 'none';
                            alert.remove();
                        } else {
                            activeCount++;
                            seenAlerts.push(uniqueKey);
                        }
                    });

                    localStorage.setItem(seenAlertKey, JSON.stringify(seenAlerts));

                    if (activeCount === 0 && panel) {
                        panel.remove();
                    } else if (activeCount > 0 && panel) {
                        setTimeout(() => {
                            const currentAlerts = document.querySelectorAll('.alert-card');
                            currentAlerts.forEach(a => {
                                a.style.transition = 'opacity 1s ease, transform 1s ease';
                                a.style.opacity = '0';
                                a.style.transform = 'translateX(20px)';
                                setTimeout(() => a.remove(), 1000);
                            });
                            setTimeout(() => {
                                panel.style.transition = 'opacity 0.5s ease';
                                panel.style.opacity = '0';
                                setTimeout(() => panel.remove(), 500);
                            }, 1000);
                        }, 20000);
                    }

                    // Auto-dismiss AI Insight Panel
                    const aiPanel = document.querySelector('.ai-insight-panel');
                    if (aiPanel) {
                        setTimeout(() => {
                            aiPanel.style.transition = 'opacity 1s ease, transform 1s ease';
                            aiPanel.style.opacity = '0';
                            aiPanel.style.transform = 'translateY(-20px)';
                            setTimeout(() => aiPanel.remove(), 1000);
                        }, 20000);
                    }
                });
            })();
        </script>
        <?php 
            // Clear the flag so it doesn't show on next simple page enter
            unset($_SESSION['new_ai_action']);
        ?>
        <?php endif; ?>

        <!-- Formulaire Ajouter/Modifier Suivi -->
        <div class="form-card">
            <div class="form-header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 30px;">
                <h2 class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    Nouvelle entrée de suivi
                </h2>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <!-- AI Health Analysis Button (NutriLoop Style) -->
                    <div id="aiHealthBtn" style="background: var(--primary-blue); color: white; padding: 10px 20px; border-radius: 12px; cursor: pointer; font-size: 0.95em; font-weight: 700; transition: 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-magic"></i> Calendrier IA
                    </div>
                    <a href="afficherObjectif.php" style="background: var(--success-green); color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-size: 0.95em; font-weight: 700; transition: 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-bullseye"></i> Mes Objectifs
                    </a>
                </div>
            </div>
            <div class="form-content">
                <form id="addSuiviForm">
                    <input type="hidden" name="id_suivi" value="">
                    <input type="hidden" name="user_id" value="1">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_objectif">
                                <i class="fas fa-bullseye"></i> Objectif lié
                            </label>
                            <select id="id_objectif" name="id_objectif" required>
                                <option value="">Choisir un objectif...</option>
                                <?php foreach ($objectifs as $obj): ?>
                                    <option value="<?= $obj->getId() ?>" 
                                            data-calories="<?= $obj->getCaloriesObjectif() ?>" 
                                            data-eau="<?= $obj->getEauObjectif() ?>">
                                        Objectif <?= $obj->getId() ?> (du <?= date('d/m/Y', strtotime($obj->getDateDebut())) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date">
                                <i class="fas fa-calendar-day"></i> Date
                            </label>
                            <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="poids">
                                <i class="fas fa-weight"></i> Poids Actuel (kg)
                            </label>
                            <input type="number" id="poids" name="poids" step="0.1" placeholder="Ex: 78.2" required>
                        </div>
                        <div class="form-group">
                            <label for="calories_consommees">
                                <i class="fas fa-utensils"></i> Calories Consommées (kcal)
                            </label>
                            <input type="number" id="calories_consommees" name="calories_consommees" placeholder="Ex: 1850" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories_objectif">
                                <i class="fas fa-fire"></i> Objectif Calories (kcal)
                            </label>
                            <input type="number" id="calories_objectif" name="calories_objectif" placeholder="Rempli automatiquement" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="eau_bue">
                                <i class="fas fa-glass-water"></i> Eau Bue (litres)
                            </label>
                            <input type="number" id="eau_bue" name="eau_bue" step="0.1" placeholder="Ex: 1.5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eau_objectif">
                                <i class="fas fa-droplet"></i> Objectif Eau (litres)
                            </label>
                            <input type="number" id="eau_objectif" name="eau_objectif" step="0.1" placeholder="Rempli automatiquement" readonly required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <button type="button" class="cancel-btn">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Suivis -->
        <div class="suivis-list">
            <?php if (count($suivis) > 0): ?>
                <h2 style="margin-bottom: 20px; color: var(--text-dark);">
                    <i class="fas fa-history"></i> Historique de Suivi
                </h2>
                
                <?php foreach ($suivis as $s): ?>
                    <div class="suivi-card">
                        <div class="suivi-header">
                            <div class="suivi-date">
                                <i class="fas fa-calendar-check"></i>
                                <?= date('d/m/Y', strtotime($s->getDate())) ?>
                            </div>
                            <div class="suivi-actions">
                                <button class="edit-suivi-btn" data-id="<?= $s->getId() ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete-suivi-btn" data-id="<?= $s->getId() ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="suivi-grid">
                            <div class="suivi-item">
                                <div class="suivi-item-label">Poids</div>
                                <div class="suivi-item-value"><?= number_format($s->getPoids(), 1) ?> <span class="suivi-item-unit">kg</span></div>
                            </div>
                            <div class="suivi-item">
                                <div class="suivi-item-label">Calories</div>
                                <div class="suivi-item-value"><?= $s->getCaloriesConsommees() ?> / <?= $s->getCaloriesObjectif() ?></div>
                                <div style="font-size: 0.8em; color: <?= ($s->getCaloriesRestant() >= 0) ? 'green' : 'red' ?>;">
                                    Reste: <?= $s->getCaloriesRestant() ?> kcal
                                </div>
                            </div>
                            <div class="suivi-item">
                                <div class="suivi-item-label">Eau</div>
                                <div class="suivi-item-value"><?= number_format($s->getEauBue(), 1) ?> / <?= number_format($s->getEauObjectif(), 1) ?> <span class="suivi-item-unit">L</span></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-objectif" style="text-align: center; padding: 40px; background: white; border-radius: 20px;">
                    <i class="fas fa-clipboard-list" style="font-size: 3em; opacity: 0.3; margin-bottom: 20px;"></i>
                    <p>Aucun suivi enregistré pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="simple-footer">
        💚 NutriLoop AI - Votre compagnon santé
    </div>

    <!-- AI Chatbot Widget -->
    <div class="ai-chatbot-widget">
        <div class="chatbot-popup" id="chatbotPopup">
            <div class="chatbot-header">
                <h3><i class="fas fa-robot"></i> NutriBot AI</h3>
                <i class="fas fa-times close-chatbot" id="closeChatbot"></i>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <div class="chat-msg ai">
                    Besoin d'aide pour vos calories ? 🥗<br>
                    Dites-moi ce que vous avez mangé !
                </div>
            </div>
            <div class="chatbot-input-area">
                <input type="text" id="chatbotInput" placeholder="Ex: Un burger et des frites..." autocomplete="off">
                <button id="chatbotSendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <div class="chatbot-button" id="openChatbot">
            <div class="chatbot-icon">
                <i class="fas fa-robot"></i>
            </div>
            <span class="chatbot-label">Calcul de calories</span>
        </div>
    </div>

    <!-- AI Health Analysis Panel (Premium Custom UI) -->
    <div id="aiHealthPanel" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 1150px; max-width: 95vw; height: 750px; max-height: 90vh; background: #fff; border-radius: 40px; box-shadow: 0 40px 100px rgba(0,0,0,0.2); z-index: 7000; overflow: hidden; animation: aiModalFade 0.6s cubic-bezier(0.23, 1, 0.32, 1);">
        <div style="display: flex; flex-direction: column; height: 100%;">
            <!-- Top: Brand Banner (Horizontal) -->
            <div style="background: linear-gradient(90deg, #2E7D32 0%, #003366 100%); padding: 25px 40px; display: flex; align-items: center; gap: 30px; border-bottom: 2px solid rgba(255,255,255,0.1);">
                <div style="width: 70px; height: 70px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255,255,255,0.3); box-shadow: 0 8px 15px rgba(0,0,0,0.1); flex-shrink: 0;">
                    <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400" alt="Health" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="flex-shrink: 0;">
                    <h2 style="font-size: 1.4rem; color: #fff; margin: 0; font-weight: 800; line-height: 1.1;">AI Health Dashboard</h2>
                    <select id="calendarObjectifFilter" style="margin-top: 8px; background: rgba(255,255,255,0.6); border: 1px solid #c8d6cb; padding: 6px 12px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; color: #4a5d4e; outline: none; cursor: pointer; min-width: 200px;">
                        <option value="all">Tous mes objectifs</option>
                        <?php foreach ($objectifs as $obj): ?>
                            <option value="<?= $obj->getId() ?>">Objectif #<?= $obj->getId() ?> (<?= date('d/m/Y', strtotime($obj->getDateDebut())) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- AI Score Quick View (Expanded Width) -->
                <div style="flex: 1; display: flex; align-items: center; gap: 20px; background: #fff; padding: 12px 25px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin: 0 20px; min-width: 0;">
                    <div id="aiScoreBadge" style="width: 55px; height: 55px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 900; color: white; flex-shrink: 0;">--</div>
                    <div style="min-width: 0; flex: 1;">
                        <h4 id="aiStatusText" style="margin: 0; font-size: 0.75rem; font-weight: 900; color: #4a5d4e; text-transform: uppercase; letter-spacing: 1px;">AI Analysis</h4>
                        <p id="aiMessageText" style="margin: 3px 0 0; font-size: 0.85rem; color: #636e72; line-height: 1.4; font-style: italic; display: block; width: 100%;">Sélectionnez un jour pour une analyse complète...</p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 20px;">
                    <i class="fas fa-times" id="closeAiPanel" style="cursor: pointer; color: rgba(255,255,255,0.7); font-size: 1.4rem; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.7)'"></i>
                </div>
            </div>

            <!-- Bottom: Content Area -->
            <div style="flex: 1; display: flex; overflow: hidden;">
                <!-- Main: Calendar -->
                <div style="flex: 1; padding: 40px; background: #fff; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <h3 id="currentMonthYear" style="font-size: 1.5rem; font-weight: 800; color: #4a5d4e; margin: 0;">Mai 2026</h3>
                        <div style="display: flex; gap: 10px;">
                            <button onclick="changeMonth(-1)" style="background: #f8faf9; border: none; width: 40px; height: 40px; border-radius: 50%; color: #4a5d4e; cursor: pointer; transition: 0.3s;"><i class="fas fa-chevron-left"></i></button>
                            <button onclick="changeMonth(1)" style="background: #f8faf9; border: none; width: 40px; height: 40px; border-radius: 50%; color: #4a5d4e; cursor: pointer; transition: 0.3s;"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Calendar Header -->
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; margin-bottom: 15px;">
                        <?php foreach(['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] as $day): ?>
                            <span style="font-size: 0.7rem; font-weight: 800; color: #ccc; text-transform: uppercase; letter-spacing: 1px;"><?= $day ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Calendar Grid -->
                    <div id="calendarGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; flex: 1; align-content: start;">
                        <!-- JS -->
                    </div>
                </div>

                <!-- Right Sidebar: Details -->
                <div style="width: 300px; background: #fff; border-left: 1px solid #f0f0f0; padding: 40px 30px; display: flex; flex-direction: column; gap: 20px;">
                    <h4 style="margin: 0; font-size: 0.8rem; font-weight: 900; color: #4a5d4e; text-transform: uppercase; letter-spacing: 1px; text-align: center;">Détails du jour</h4>
                    
                    <div id="aiDayStats" style="display: none; flex-direction: column; gap: 15px;">
                        <div style="background: #e1ead8; padding: 20px; border-radius: 20px; text-align: center;">
                            <span id="aiCalValue" style="display: block; font-weight: 900; color: #4a5d4e; font-size: 1.2rem;">--</span>
                            <span style="font-size: 0.65rem; color: #7a8d7e; text-transform: uppercase; font-weight: 800;">Calories</span>
                        </div>
                        <div style="background: #e1ead8; padding: 20px; border-radius: 20px; text-align: center;">
                            <span id="aiWaterValue" style="display: block; font-weight: 900; color: #4a5d4e; font-size: 1.2rem;">--</span>
                            <span style="font-size: 0.65rem; color: #7a8d7e; text-transform: uppercase; font-weight: 800;">Eau (L)</span>
                        </div>
                    </div>

                    <div style="margin-top: auto; background: #fbfdfc; padding: 20px; border-radius: 20px; border: 1px dashed #e1ead8;">
                        <p style="font-size: 0.8rem; color: #7a8d7e; line-height: 1.6; margin: 0; text-align: center; font-style: italic;">
                            L'IA Health analyse vos habitudes pour vous proposer des conseils personnalisés.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes aiModalFade { from { opacity: 0; transform: translate(-50%, -40%) scale(0.95); } to { opacity: 1; transform: translate(-50%, -50%) scale(1); } }
        
        #aiHealthPanel { border-radius: 30px; box-shadow: 0 50px 150px rgba(0,51,102,0.3); }
        
        .calendar-day-circle {
            aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
            border-radius: 12px; cursor: pointer; transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700; color: #64748b; font-size: 0.95rem; position: relative;
            background: #fff; border: 1px solid #f1f5f9;
        }
        .calendar-day-circle:hover:not(.empty) { background: #f0f7ef; color: #2E7D32; border-color: #2E7D32; transform: translateY(-2px); }
        .calendar-day-circle.has-data { color: #003366; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .calendar-day-circle.active { background: #003366 !important; color: #fff !important; box-shadow: 0 10px 20px rgba(0,51,102,0.2); border: none !important; }
        
        /* Status Colors - Refined for NutriLoop */
        .status-perfect { background: #e8f5e9 !important; border: 1.5px solid #2E7D32 !important; color: #2E7D32 !important; }
        .status-normal { background: #fff9c4 !important; border: 1.5px solid #fbc02d !important; color: #9a7d0a !important; }
        .status-bad { background: #ffebee !important; border: 1.5px solid #dc3545 !important; color: #c62828 !important; }
        
        #aiMessageText { display: block !important; max-width: none !important; white-space: normal !important; overflow: visible !important; text-overflow: clip !important; }
        
        #calendarObjectifFilter {
            border: 2px solid #003366;
            color: #003366;
            background: white;
            font-weight: 800;
        }
    </style>

    <?php
    $suivisData = [];
    foreach ($suivis as $s) {
        $suivisData[] = [
            'date' => $s->getDate(),
            'calories' => (int)$s->getCaloriesConsommees(),
            'targetCal' => (int)$s->getCaloriesObjectif(),
            'water' => (float)$s->getEauBue(),
            'targetWater' => (float)$s->getEauObjectif(),
            'objectif_id' => $s->getIdObjectif()
        ];
    }
    ?>

    <script src="../assets/js/suivi.js"></script>
    <script>
        // Auto-fill objectives
        document.getElementById('id_objectif').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('calories_objectif').value = selected.dataset.calories;
                document.getElementById('eau_objectif').value = selected.dataset.eau;
            }
        });

        // --- AI Chatbot Widget Logic ---
        const chatbotPopup = document.getElementById('chatbotPopup');
        const openChatbotBtn = document.getElementById('openChatbot');
        const closeChatbotBtn = document.getElementById('closeChatbot');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSendBtn = document.getElementById('chatbotSendBtn');
        const chatbotMessages = document.getElementById('chatbotMessages');

        if (openChatbotBtn && chatbotPopup) {
            openChatbotBtn.onclick = () => {
                chatbotPopup.classList.add('show');
                openChatbotBtn.style.opacity = '0';
                openChatbotBtn.style.pointerEvents = 'none';
            };
        }

        if (closeChatbotBtn && chatbotPopup) {
            closeChatbotBtn.onclick = () => {
                chatbotPopup.classList.remove('show');
                openChatbotBtn.style.opacity = '1';
                openChatbotBtn.style.pointerEvents = 'auto';
            };
        }

        async function handleChatbot() {
            const text = chatbotInput.value.trim();
            if (!text) return;

            // Add user message
            const userDiv = document.createElement('div');
            userDiv.className = 'chat-msg user';
            userDiv.textContent = text;
            chatbotMessages.appendChild(userDiv);
            chatbotInput.value = '';
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            // Add loading
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chat-msg ai';
            loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Réflexion...';
            chatbotMessages.appendChild(loadingDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            try {
                const response = await fetch('../../controleurs/ChatbotController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `message=${encodeURIComponent(text)}`
                });
                const result = await response.json();
                loadingDiv.remove();
                
                const aiDiv = document.createElement('div');
                aiDiv.className = 'chat-msg ai';
                aiDiv.innerHTML = (result.reply || result.response || "Je n'ai pas pu traiter votre demande.").replace(/\n/g, '<br>');
                chatbotMessages.appendChild(aiDiv);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            } catch (e) {
                loadingDiv.remove();
                const errDiv = document.createElement('div');
                errDiv.className = 'chat-msg ai';
                errDiv.textContent = "Désolé, une erreur est survenue... 😴";
                chatbotMessages.appendChild(errDiv);
            }
        }

        if (chatbotSendBtn) chatbotSendBtn.onclick = handleChatbot;
        if (chatbotInput) chatbotInput.onkeypress = (e) => { if (e.key === 'Enter') handleChatbot(); };

        // --- Premium AI Health Calendar Logic ---
        const suivisData = <?= json_encode($suivisData) ?>;
        const aiHealthPanel = document.getElementById('aiHealthPanel');
        const aiHealthBtn = document.getElementById('aiHealthBtn');
        const closeAiPanel = document.getElementById('closeAiPanel');
        const calendarGrid = document.getElementById('calendarGrid');
        const currentMonthYear = document.getElementById('currentMonthYear');
        const objectifFilter = document.getElementById('calendarObjectifFilter');

        let calendarDate = new Date(); // Start with today's month

        aiHealthBtn.onclick = () => {
            aiHealthPanel.style.display = 'block';
            renderCustomCalendar();
        };

        closeAiPanel.onclick = () => aiHealthPanel.style.display = 'none';

        objectifFilter.onchange = () => renderCustomCalendar();

        function changeMonth(delta) {
            calendarDate.setMonth(calendarDate.getMonth() + delta);
            renderCustomCalendar();
        }

        function renderCustomCalendar() {
            calendarGrid.innerHTML = '';
            const year = calendarDate.getFullYear();
            const month = calendarDate.getMonth();
            const selectedObj = objectifFilter.value;
            
            const months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            currentMonthYear.textContent = `${months[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Padding
            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement('div');
                empty.className = 'calendar-day-circle empty';
                calendarGrid.appendChild(empty);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day-circle';
                dayEl.textContent = day;

                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                // Filter by objective if selected
                const data = suivisData.find(s => s.date === dateStr && (selectedObj === 'all' || s.objectif_id == selectedObj));

                if (data) {
                    dayEl.classList.add('has-data');
                    const score = calculateQuickScore(data);
                    if (score === 100) dayEl.classList.add('status-perfect');
                    else if (score >= 50) dayEl.classList.add('status-normal');
                    else dayEl.classList.add('status-bad');

                    dayEl.onclick = () => {
                        document.querySelectorAll('.calendar-day-circle').forEach(d => d.classList.remove('active'));
                        dayEl.classList.add('active');
                        analyzeDayData(data);
                    };
                }

                calendarGrid.appendChild(dayEl);
            }
        }

        function calculateQuickScore(data) {
            let s = 0;
            if (data.calories <= data.targetCal) s += 50;
            if (data.water >= data.targetWater) s += 50;
            return s;
        }

        async function analyzeDayData(dayData) {
            const scoreBadge = document.getElementById('aiScoreBadge');
            const statusText = document.getElementById('aiStatusText');
            const messageText = document.getElementById('aiMessageText');
            const stats = document.getElementById('aiDayStats');

            stats.style.display = 'flex';
            statusText.textContent = "ANALYSE EN COURS...";
            messageText.textContent = "L'intelligence artificielle analyse vos métriques nutritionnelles...";
            document.getElementById('aiCalValue').textContent = dayData.calories + ' / ' + dayData.targetCal + ' kcal';
            document.getElementById('aiWaterValue').textContent = dayData.water + ' / ' + dayData.targetWater + ' L';

            try {
                const response = await fetch('../../controleurs/AIHealthController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        calories: dayData.calories, 
                        water: dayData.water,
                        targetCalories: dayData.targetCal,
                        targetWater: dayData.targetWater,
                        caloriesOK: dayData.calories <= dayData.targetCal,
                        waterOK: dayData.water >= dayData.targetWater
                    })
                });
                const result = await response.json();
                
                scoreBadge.textContent = result.score;
                scoreBadge.style.backgroundColor = result.color;
                statusText.textContent = result.status.toUpperCase();
                statusText.style.color = result.color;
                messageText.textContent = result.message;
            } catch (e) {
                messageText.textContent = "Erreur de connexion à l'IA.";
            }
        }
    </script>
    <?php 
        if (isset($_SESSION['new_ai_action'])) unset($_SESSION['new_ai_action']); 
    ?>
</body>
</html>
