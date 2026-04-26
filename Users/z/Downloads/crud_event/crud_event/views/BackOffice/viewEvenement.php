<?php
// views/BackOffice/evenement/viewEvenement.php
include_once '../../controleurs/EvenementController.php';
include_once '../../controleurs/ParticipationController.php';
require_once __DIR__ . '/../../models/Evenement.php';
require_once __DIR__ . '/../../models/Participation.php';

$evenementController    = new EvenementController();
$participationController = new ParticipationController();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: evenementList.php');
    exit;
}

$evenement = $evenementController->getEvenementById($id);
if (!$evenement) {
    header('Location: evenementList.php');
    exit;
}

// Récupérer toutes les participations de cet événement
$participations = $participationController->getParticipationsByEvenement($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($evenement->getTitre()) ?> - Détail</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/evenement.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#f0f2f5; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; }

        .detail-container { max-width:1300px; margin:0 auto; padding:30px 20px; }

        /* Bouton retour */
        .btn-back {
            display:inline-flex; align-items:center; gap:10px;
            background:#6c757d; color:white;
            padding:12px 24px; border-radius:12px;
            text-decoration:none; margin-bottom:25px;
            transition:all .3s; font-weight:500;
        }
        .btn-back:hover { background:#5a6268; transform:translateX(-5px); }

        /* Header événement */
        .event-header {
            background:linear-gradient(135deg,#1565C0,#0d47a1);
            color:white; padding:35px; border-radius:24px;
            margin-bottom:30px; box-shadow:0 10px 30px rgba(0,0,0,.2);
        }
        .event-header h1 {
            font-size:2.2rem; margin-bottom:15px;
            display:flex; align-items:center; gap:15px; color:white;
        }
        .event-meta {
            display:flex; gap:15px; flex-wrap:wrap; margin-top:20px;
        }
        .meta-chip {
            background:rgba(255,255,255,.22); padding:8px 18px;
            border-radius:30px; font-size:14px;
            display:inline-flex; align-items:center; gap:8px;
            color:white; font-weight:500; backdrop-filter:blur(4px);
        }
        .meta-chip i { color:#ffeb3b; }

        /* Section titre */
        .section-title {
            font-size:1.5rem; color:#1565C0;
            margin:30px 0 18px; padding-bottom:12px;
            border-bottom:3px solid #2196F3;
            display:flex; justify-content:space-between;
            align-items:center; flex-wrap:wrap; gap:15px;
        }
        .section-title i { color:#2196F3; }

        .add-part-btn {
            background:#4CAF50; color:white;
            padding:10px 20px; border-radius:12px;
            text-decoration:none; font-size:14px;
            display:inline-flex; align-items:center; gap:8px;
            transition:all .3s; font-weight:500;
        }
        .add-part-btn:hover { background:#388e3c; transform:scale(1.02); }

        .btn-view-all {
            background:#2196f3; color:white;
            padding:10px 20px; border-radius:12px;
            text-decoration:none; font-size:14px;
            display:inline-flex; align-items:center; gap:8px;
            transition:all .3s; font-weight:500;
        }
        .btn-view-all:hover { background:#1976d2; transform:scale(1.02); }

        /* Description */
        .description-box {
            background:white; padding:25px; border-radius:20px;
            line-height:1.7; box-shadow:0 5px 20px rgba(0,0,0,.08);
            font-size:1rem; color:#333;
            border-left:5px solid #2196F3;
        }

        /* Cartes participation */
        .participations-list {
            display:flex; flex-direction:column; gap:20px;
        }

        .part-card {
            background:white; border-radius:20px;
            overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,.08);
            transition:transform .2s, box-shadow .2s;
        }
        .part-card:hover {
            transform:translateY(-3px);
            box-shadow:0 15px 35px rgba(0,0,0,.12);
        }

        .part-header {
            background:linear-gradient(135deg,#f8f9fa,#e9ecef);
            padding:16px 24px; border-bottom:2px solid #2196F3;
            display:flex; justify-content:space-between;
            align-items:center; flex-wrap:wrap; gap:12px;
        }

        .part-id-badge {
            background:#2196F3; color:white;
            padding:6px 18px; border-radius:30px;
            font-size:14px; font-weight:bold;
            display:inline-flex; align-items:center; gap:8px;
        }

        .part-infos {
            display:flex; gap:12px; flex-wrap:wrap;
        }

        .part-info {
            font-size:12px; color:#555; background:white;
            padding:5px 12px; border-radius:20px;
            box-shadow:0 1px 3px rgba(0,0,0,.1);
            display:inline-flex; align-items:center; gap:6px;
        }
        .part-info i { color:#2196F3; }

        .part-body { padding:22px; }

        .part-feedback {
            background:#f8f9fa; padding:16px; border-radius:12px;
            line-height:1.6; margin-bottom:18px;
            border-left:4px solid #2196F3;
            font-style:italic; color:#555;
        }
        .part-feedback i { color:#2196F3; margin-right:8px; }

        .note-stars { color:#FFC107; font-size:1.1em; letter-spacing:2px; }

        .part-actions {
            display:flex; gap:12px; padding-top:14px;
            border-top:1px solid #e0e0e0;
        }

        .btn-edit-part {
            background:#ff9800; color:white; padding:8px 18px;
            border-radius:10px; text-decoration:none; font-size:13px;
            display:inline-flex; align-items:center; gap:6px;
            transition:all .3s; font-weight:500;
        }
        .btn-edit-part:hover { background:#e68900; transform:scale(1.02); }

        .btn-delete-part {
            background:#f44336; color:white; padding:8px 18px;
            border-radius:10px; border:none; cursor:pointer;
            font-size:13px; display:inline-flex; align-items:center;
            gap:6px; transition:all .3s; font-weight:500;
        }
        .btn-delete-part:hover { background:#d32f2f; transform:scale(1.02); }

        /* Vide */
        .empty-participations {
            text-align:center; padding:60px; background:white;
            border-radius:20px; color:#666;
        }
        .empty-participations i {
            font-size:64px; color:#ccc; margin-bottom:20px; display:block;
        }

        /* Responsive */
        @media(max-width:768px){
            .detail-container { padding:15px; }
            .event-header { padding:20px; }
            .event-header h1 { font-size:1.5rem; }
            .part-header { flex-direction:column; align-items:flex-start; }
            .section-title { flex-direction:column; align-items:flex-start; }
        }
    </style>
</head>
<body>
<div class="detail-container">

    <a href="evenementList.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Retour à la liste des événements
    </a>

    <!-- ========== HEADER ÉVÉNEMENT ========== -->
    <div class="event-header">
        <h1>
            <?php
            $typeEmojis = [
                'SPORT'     => '🏃',
                'NUTRITION' => '🥗',
                'WORKSHOP'  => '📚',
                'AUTRE'     => '📅'
            ];
            echo ($typeEmojis[$evenement->getTypeEvenement()] ?? '📅') . ' ';
            echo htmlspecialchars($evenement->getTitre());
            ?>
        </h1>
        <div class="event-meta">
            <div class="meta-chip"><i class="fas fa-tag"></i> <?= $evenement->getTypeEvenement() ?></div>
            <div class="meta-chip"><i class="fas fa-calendar-day"></i> <?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></div>
            <div class="meta-chip"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($evenement->getLieu()) ?></div>
            <div class="meta-chip"><i class="fas fa-users"></i> <?= $evenement->getNbPlacesMax() ?> places max</div>
            <div class="meta-chip">
                <i class="fas fa-circle"></i>
                <span class="statut-event-badge statut-<?= $evenement->getStatut() ?>">
                    <?= $evenement->getStatut() ?>
                </span>
            </div>
        </div>
    </div>

    <!-- ========== DESCRIPTION ========== -->
    <h2 class="section-title">
        <span><i class="fas fa-align-left"></i> Description</span>
        <a href="editEvenement.php?id=<?= $id ?>" class="btn-edit-part" style="font-size:14px">
            <i class="fas fa-edit"></i> Modifier l'événement
        </a>
    </h2>
    <div class="description-box">
        <?= nl2br(htmlspecialchars($evenement->getDescription())) ?>
    </div>

    <!-- ========== PARTICIPATIONS ========== -->
    <h2 class="section-title" style="margin-top:35px">
        <div>
            <i class="fas fa-users"></i> Participations
            <span style="font-size:14px;color:#666;margin-left:10px;">
                (<?= count($participations) ?> participant(s))
            </span>
        </div>
        <div style="display:flex;gap:10px">
            <a href="participationList.php" class="btn-view-all">
                <i class="fas fa-list-ul"></i> Voir toutes
            </a>
            <a href="addParticipation.php?evenement_id=<?= $id ?>" class="add-part-btn">
                <i class="fas fa-user-plus"></i> Ajouter un participant
            </a>
        </div>
    </h2>

    <?php if (count($participations) > 0): ?>
        <div class="participations-list">
            <?php foreach ($participations as $p): ?>
            <div class="part-card">
                <div class="part-header">
                    <span class="part-id-badge">
                        <i class="fas fa-user"></i> Participation #<?= $p->getIdParticipation() ?>
                    </span>
                    <div class="part-infos">
                        <span class="part-info">
                            <i class="fas fa-id-card"></i> User #<?= $p->getIdUser() ?>
                        </span>
                        <span class="part-info">
                            <i class="fas fa-circle"></i>
                            <span class="statut-part-badge statut-<?= $p->getStatut() ?>">
                                <?= $p->getStatut() ?>
                            </span>
                        </span>
                        <?php if ($p->getDateInscription()): ?>
                        <span class="part-info">
                            <i class="fas fa-calendar-check"></i>
                            <?= date('d/m/Y H:i', strtotime($p->getDateInscription())) ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($p->getNote()): ?>
                        <span class="part-info">
                            <i class="fas fa-star" style="color:#FFC107"></i>
                            <span class="note-stars"><?= str_repeat('★', $p->getNote()) ?></span>
                            (<?= $p->getNote() ?>/5)
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="part-body">
                    <?php if ($p->getFeedback()): ?>
                    <div class="part-feedback">
                        <i class="fas fa-quote-left"></i>
                        <?= nl2br(htmlspecialchars($p->getFeedback())) ?>
                    </div>
                    
                    <?php endif; ?>

                    <div class="part-actions">
                        <a href="editParticipation.php?id=<?= $p->getIdParticipation() ?>" class="btn-edit-part">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button onclick="confirmDeletePart(<?= $p->getIdParticipation() ?>)" class="btn-delete-part">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="empty-participations">
            <i class="fas fa-users"></i>
            <h3>Aucun participant inscrit</h3>
            <p>Cet événement n'a pas encore de participants.</p>
            <a href="addParticipation.php?evenement_id=<?= $id ?>" class="add-part-btn"
               style="display:inline-flex;margin-top:15px">
                <i class="fas fa-user-plus"></i> Ajouter le premier participant
            </a>
        </div>
    <?php endif; ?>

</div>

<script>
function confirmDeletePart(id) {
    if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette participation ?\n\nCette action est irréversible.')) {
        window.location.href = 'deleteParticipation.php?id=' + id + '&confirm=yes';
    }
}
</script>
</body>
</html>