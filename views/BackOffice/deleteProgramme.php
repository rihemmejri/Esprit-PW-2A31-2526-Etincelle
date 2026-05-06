<?php
include '../../controleurs/ProgrammeController.php';
require_once __DIR__ . '/../../models/programme.php';

$programmeController = new ProgrammeController();
$id = $_GET['id'] ?? null;

if ($id) {
    // Vérifier si la confirmation est donnée
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $programmeController->deleteProgramme($id);
        header('Location: programmeList.php');
        exit;
    }
} else {
    header('Location: programmeList.php');
    exit;
}

// Récupérer les infos du programme pour affichage
$programme = $programmeController->getProgrammeById($id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de suppression - Programme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f7fa;
        }
        .container-delete {
            max-width: 550px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .confirmation-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
            animation: fadeIn 0.3s ease-out;
        }
        .warning-icon {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            padding: 30px;
            color: white;
            font-size: 4rem;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            margin-bottom: 15px;
            color: #333;
        }
        .content p {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .programme-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: left;
        }
        .programme-info p {
            margin: 8px 0;
            font-size: 0.95rem;
            color: #333;
        }
        .programme-info i {
            width: 25px;
            color: #4CAF50;
        }
        .repas-preview {
            background: #fff3e0;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            max-height: 150px;
            overflow-y: auto;
        }
        .repas-preview small {
            display: block;
            padding: 5px;
            border-bottom: 1px solid #ffe0b2;
            font-size: 0.8rem;
        }
        .warning-text {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #e65100;
            font-size: 0.9rem;
        }
        .warning-text i {
            font-size: 1.2rem;
        }
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 25px 0;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220,53,69,0.3);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .footer-note {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 0.85rem;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container-delete">
        <div class="confirmation-card">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <div class="content">
                <h2>⚠️ Confirmation de suppression</h2>
                <p>Êtes-vous sûr de vouloir supprimer ce programme ?</p>
                
                <?php if ($programme): ?>
                <div class="programme-info">
                    <p><i class="fas fa-calendar-alt"></i> <strong>Programme #<?= $programme->getIdProgramme() ?></strong></p>
                    <p><i class="fas fa-user"></i> Utilisateur ID: <?= $programme->getIdUser() ?></p>
                    <p><i class="fas fa-bullseye"></i> Objectif: 
                        <?php 
                        switch($programme->getObjectif()) {
                            case 'PERDRE_POIDS': echo '🔥 Perdre du poids'; break;
                            case 'PRENDRE_MUSCLE': echo '💪 Prendre du muscle'; break;
                            case 'MAINTENIR': echo '⚖️ Maintenir son poids'; break;
                            case 'EQUILIBRE': echo '🥗 Équilibre alimentaire'; break;
                            default: echo $programme->getObjectif();
                        }
                        ?>
                    </p>
                    <p><i class="fas fa-calendar-week"></i> Période: <?= $programme->getDateDebut() ?> → <?= $programme->getDateFin() ?></p>
                    <p><i class="fas fa-utensils"></i> Nombre de repas: <?= count($programme->getRepas()) ?> repas</p>
                    
                    <?php 
                    $repasListe = $programme->getRepas();
                    if (!empty($repasListe)): 
                    ?>
                    <div class="repas-preview">
                        <small><strong>📋 Repas du programme :</strong></small>
                        <?php foreach (array_slice($repasListe, 0, 5) as $item): ?>
                            <small>• <?= $item['jour_semaine'] ?> - <?= $item['type_repas'] ?> : <?= $item['nom'] ?? 'Repas' ?></small>
                        <?php endforeach; ?>
                        <?php if (count($repasListe) > 5): ?>
                            <small><em>... et <?= count($repasListe) - 5 ?> autre(s) repas</em></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="warning-text">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Cette action est irréversible. Tous les repas associés à ce programme seront également supprimés de la jointure (programme_repas).</span>
                </div>
                
                <div class="actions">
                    <a href="deleteProgramme.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        Oui, supprimer
                    </a>
                    <a href="programmeList.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Non, annuler
                    </a>
                </div>
                
                <div class="footer-note">
                    <i class="fas fa-info-circle"></i>
                    Vous serez redirigé vers la liste des programmes après l'action
                </div>
            </div>
        </div>
    </div>
</body>
</html>