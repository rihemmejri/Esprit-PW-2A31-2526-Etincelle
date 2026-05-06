<?php
require_once '../../controleurs/ParticipationController.php';

$participationController = new ParticipationController();
$id = $_GET['id'] ?? null;

if (!$id) { header('Location: participationList.php'); exit; }

$participation = $participationController->getParticipationById($id);
if (!$participation) { header('Location: participationList.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail participation #<?= $id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/participation.css">
    <style>
        .detail-card { max-width:900px; margin:0 auto; }
        .detail-section { background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:20px; }
        .detail-section h3 { color:#1565C0; margin-bottom:15px; border-bottom:2px solid #2196F3; padding-bottom:8px; display:flex; align-items:center; gap:8px; }
        .detail-section h3 i { color:#2196F3; }
        .detail-row { display:flex; padding:10px 0; border-bottom:1px solid #e0e0e0; }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { width:220px; font-weight:600; color:#555; }
        .detail-value { flex:1; color:#333; }
        .feedback-text { background:white; padding:15px; border-radius:8px; line-height:1.6; font-style:italic; color:#555; }
        .note-stars { color:#FFC107; font-size:1.2em; letter-spacing:3px; }
    </style>
</head>
<body>
<div class="container-list">

    <div class="header">
        <h1><i class="fas fa-info-circle"></i> Détail participation #<?= $id ?></h1>
        <div style="display:flex;gap:10px">
            <a href="editParticipation.php?id=<?= $id ?>" class="add-btn" style="background:#ff9800">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="viewEvenement.php?id=<?= $participation->getIdEvenement() ?>" class="add-btn" style="background:#2196f3">
                <i class="fas fa-calendar-alt"></i> Voir l'événement
            </a>
            <a href="participationList.php" class="add-btn" style="background:#666">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="detail-card">

        <!-- Informations participant -->
        <div class="detail-section">
            <h3><i class="fas fa-user"></i> Informations du participant</h3>
            <div class="detail-row">
                <div class="detail-label">ID participation :</div>
                <div class="detail-value">#<?= $participation->getIdParticipation() ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ID Utilisateur :</div>
                <div class="detail-value"><strong style="color:#2196F3">#<?= $participation->getIdUser() ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nom complet :</div>
                <div class="detail-value"><strong><?= htmlspecialchars($participation->getNom() ?: '—') ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email :</div>
                <div class="detail-value">
                    <a href="mailto:<?= htmlspecialchars($participation->getEmail()) ?>" style="color:#2196F3">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($participation->getEmail() ?: '—') ?>
                    </a>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Téléphone :</div>
                <div class="detail-value">
                    <i class="fas fa-phone" style="color:#2196F3"></i>
                    <?= htmlspecialchars($participation->getTelephone() ?: 'Non renseigné') ?>
                </div>
            </div>
        </div>

        <!-- Événement -->
        <div class="detail-section">
            <h3><i class="fas fa-calendar-alt"></i> Événement</h3>
            <div class="detail-row">
                <div class="detail-label">Événement :</div>
                <div class="detail-value">
                    <a href="viewEvenement.php?id=<?= $participation->getIdEvenement() ?>" style="color:#2196F3;text-decoration:none;font-weight:600">
                        <i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($participation->getEvenementTitre() ?: 'N/A') ?>
                    </a>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Places réservées :</div>
                <div class="detail-value">
                    <i class="fas fa-ticket-alt" style="color:#2196F3"></i>
                    <strong><?= $participation->getNbPlacesReservees() ?? 1 ?></strong> place(s)
                </div>
            </div>
        </div>

        <!-- Statut et date -->
        <div class="detail-section">
            <h3><i class="fas fa-circle"></i> Statut & Inscription</h3>
            <div class="detail-row">
                <div class="detail-label">Statut :</div>
                <div class="detail-value">
                    <span class="statut-part-badge statut-<?= $participation->getStatut() ?>">
                        <?= $participation->getStatut() ?>
                    </span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date d'inscription :</div>
                <div class="detail-value">
                    <?php if ($participation->getDateInscription()): ?>
                        <i class="fas fa-calendar-check" style="color:#2196F3"></i>
                        <?= date('d/m/Y à H:i', strtotime($participation->getDateInscription())) ?>
                    <?php else: ?> Non renseignée <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Évaluation -->
        <div class="detail-section">
            <h3><i class="fas fa-star"></i> Évaluation</h3>
            <div class="detail-row">
                <div class="detail-label">Note :</div>
                <div class="detail-value">
                    <?php if ($participation->getNote()): ?>
                        <span class="note-stars"><?= str_repeat('★', $participation->getNote()) ?></span>
                        <span style="color:#666">(<?= $participation->getNote() ?> / 5)</span>
                    <?php else: ?>
                        <span style="color:#999">Pas encore notée</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Feedback -->
        <div class="detail-section">
            <h3><i class="fas fa-comment-alt"></i> Feedback</h3>
            <?php if ($participation->getFeedback()): ?>
                <div class="feedback-text">
                    <i class="fas fa-quote-left" style="color:#ccc;margin-right:8px"></i>
                    <?= nl2br(htmlspecialchars($participation->getFeedback())) ?>
                </div>
            <?php else: ?>
                <p style="color:#aaa;font-style:italic"><i class="fas fa-comment-slash"></i> Aucun feedback.</p>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>