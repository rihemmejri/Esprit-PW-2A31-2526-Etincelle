<?php
require_once '../../controleurs/ParticipationController.php';
require_once '../../controleurs/EvenementController.php';

$participationController = new ParticipationController();
$evenementController     = new EvenementController();

$id = intval($_GET['id'] ?? 0);
if (!$id) { die('Participation introuvable.'); }

$p = $participationController->getParticipationById($id);
if (!$p) { die('Participation introuvable.'); }

$evenement = $evenementController->getEvenementById($p->getIdEvenement());
$qrUrl = $participationController->getQRCodeUrl($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Participation #<?= $id ?> — NutriLoop</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { background:linear-gradient(135deg,#e3f2fd,#e8f5e9); min-height:100vh; font-family:'Poppins',sans-serif; display:flex; align-items:center; justify-content:center; padding:20px; }
.card { background:white; border-radius:24px; max-width:420px; width:100%; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.15); }
.header { background:linear-gradient(135deg,#2196F3,#4CAF50); padding:28px; text-align:center; }
.header .logo { font-size:1.4rem; font-weight:700; color:white; margin-bottom:6px; }
.header h1 { color:white; font-size:1rem; font-weight:400; opacity:.9; }
.badge { display:inline-block; background:rgba(255,255,255,.2); color:white; padding:4px 14px; border-radius:20px; font-size:.8rem; margin-top:8px; }
.body { padding:24px; }

/* Statut paiement */
.statut-box { border-radius:14px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:12px; }
.statut-box.paye    { background:#e8f5e9; border:2px solid #4CAF50; }
.statut-box.gratuit { background:#e8f5e9; border:2px solid #4CAF50; }
.statut-box.attente { background:#fff3e0; border:2px solid #FFA726; }
.statut-icon { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.statut-icon.paye    { background:#4CAF50; color:white; }
.statut-icon.gratuit { background:#4CAF50; color:white; }
.statut-icon.attente { background:#FFA726; color:white; }
.statut-text h3 { font-size:14px; font-weight:700; }
.statut-text h3.paye    { color:#2e7d32; }
.statut-text h3.gratuit { color:#2e7d32; }
.statut-text h3.attente { color:#e65100; }
.statut-text p { font-size:11px; color:#666; margin:2px 0 0; }
.statut-ref { margin-left:auto; font-size:12px; font-weight:700; color:#2e7d32; }

/* Infos */
.info-row { display:flex; align-items:center; gap:12px; padding:11px 0; border-bottom:1px solid #f0f0f0; }
.info-row:last-child { border-bottom:none; }
.info-icon { width:36px; height:36px; border-radius:10px; background:#f0f7ff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.info-icon i { color:#2196F3; font-size:14px; }
.info-label { font-size:11px; color:#999; display:block; }
.info-value { font-size:14px; font-weight:600; color:#1a1a2e; }

/* Places */
.places-row { display:flex; gap:10px; margin:16px 0; }
.place-kpi { flex:1; background:#f5f5f5; border-radius:12px; padding:12px; text-align:center; border-left:3px solid #2196F3; }
.place-kpi strong { display:block; font-size:20px; font-weight:700; color:#1a1a2e; }
.place-kpi span { font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.4px; }

/* QR Code */
.qr-section { text-align:center; padding:20px; background:#f8f9fa; border-radius:14px; margin-top:16px; }
.qr-section img { border-radius:8px; border:3px solid #e0e0e0; }
.qr-section p { font-size:11px; color:#999; margin-top:8px; }

.footer { background:#1a1a2e; padding:14px; text-align:center; }
.footer p { color:rgba(255,255,255,.4); font-size:11px; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="logo">🌿 NutriLoop</div>
        <h1>Confirmation de participation</h1>
        <span class="badge">#<?= $p->getIdParticipation() ?></span>
    </div>

    <div class="body">

        <!-- Statut paiement -->
        <?php
        $statutPay = $p->getStatutPaiement();
        $cls = match($statutPay) {
            'PAYE'    => 'paye',
            'GRATUIT' => 'gratuit',
            default   => 'attente',
        };
        $icon = match($statutPay) {
            'PAYE'    => 'fa-check',
            'GRATUIT' => 'fa-gift',
            default   => 'fa-clock',
        };
        $txt = match($statutPay) {
            'PAYE'    => 'Paiement confirmé',
            'GRATUIT' => 'Inscription gratuite',
            default   => 'En attente de paiement',
        };
        ?>
        <div class="statut-box <?= $cls ?>">
            <div class="statut-icon <?= $cls ?>"><i class="fas <?= $icon ?>"></i></div>
            <div class="statut-text">
                <h3 class="<?= $cls ?>"><?= $txt ?></h3>
                <?php if ($p->getReferencePaiement()): ?>
                <p>Réf : <?= htmlspecialchars($p->getReferencePaiement()) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($p->getMontantPaye() > 0): ?>
            <div class="statut-ref"><?= number_format($p->getMontantPaye(), 2) ?> TND</div>
            <?php endif; ?>
        </div>

        <!-- Infos participant -->
        <div class="info-row">
            <div class="info-icon"><i class="fas fa-user"></i></div>
            <div><span class="info-label">Participant</span><span class="info-value"><?= htmlspecialchars($p->getNom()) ?></span></div>
        </div>
        <div class="info-row">
            <div class="info-icon"><i class="fas fa-envelope"></i></div>
            <div><span class="info-label">Email</span><span class="info-value"><?= htmlspecialchars($p->getEmail()) ?></span></div>
        </div>
        <?php if ($p->getTelephone()): ?>
        <div class="info-row">
            <div class="info-icon"><i class="fas fa-phone"></i></div>
            <div><span class="info-label">Téléphone</span><span class="info-value"><?= htmlspecialchars($p->getTelephone()) ?></span></div>
        </div>
        <?php endif; ?>

        <?php if ($evenement): ?>
        <div class="info-row">
            <div class="info-icon"><i class="fas fa-calendar-day"></i></div>
            <div><span class="info-label">Événement</span><span class="info-value"><?= htmlspecialchars($evenement->getTitre()) ?></span></div>
        </div>
        <div class="info-row">
            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div><span class="info-label">Lieu</span><span class="info-value"><?= htmlspecialchars($evenement->getLieu()) ?></span></div>
        </div>
        <div class="info-row">
            <div class="info-icon"><i class="fas fa-clock"></i></div>
            <div><span class="info-label">Date</span><span class="info-value"><?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></span></div>
        </div>
        <?php endif; ?>

        <!-- Places -->
        <div class="places-row">
            <div class="place-kpi">
                <strong><?= $p->getNbPlacesReservees() ?></strong>
                <span>Place(s) réservée(s)</span>
            </div>
            <div class="place-kpi" style="border-color:#4CAF50;">
                <strong><?= date('d/m/Y', strtotime($p->getDateInscription())) ?></strong>
                <span>Date d'inscription</span>
            </div>
        </div>

        <!-- QR Code -->
        <?php if ($qrUrl): ?>
        <div class="qr-section">
            <img src="../../<?= htmlspecialchars($qrUrl) ?>" width="150" height="150" alt="QR Code">
            <p><i class="fas fa-qrcode"></i> QR Code de participation</p>
        </div>
        <?php endif; ?>

    </div>

    <div class="footer"><p>NutriLoop — Plateforme intelligente pour une alimentation durable</p></div>
</div>
</body>
</html>