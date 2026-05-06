<?php
require_once '../../controleurs/ParticipationController.php';

$participationController = new ParticipationController();
$id  = intval($_GET['id']  ?? 0);
$ref = htmlspecialchars($_GET['ref'] ?? '');

if (!$id) { header('Location: evenementList.php'); exit; }
$p = $participationController->getParticipationById($id);
if (!$p) { header('Location: evenementList.php'); exit; }

$qrUrl = $participationController->getQRCodeUrl($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paiement confirmé — NutriLoop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
body{background:#f0f2f5;padding:40px 20px;min-height:100vh;display:flex;align-items:flex-start;justify-content:center;}
.card{background:white;border-radius:20px;max-width:520px;width:100%;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.1);}
.success-header{background:linear-gradient(135deg,#4CAF50,#2196F3);padding:36px;text-align:center;}
.check-circle{width:70px;height:70px;background:rgba(255,255,255,.25);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
.check-circle i{color:white;font-size:34px;}
.success-header h1{color:white;font-size:20px;margin-bottom:6px;}
.success-header p{color:rgba(255,255,255,.8);font-size:13px;}
.body{padding:28px;}
.ref-badge{background:#e8f5e9;border:2px solid #4CAF50;border-radius:12px;padding:14px 18px;text-align:center;margin-bottom:22px;}
.ref-badge .label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;}
.ref-badge .val{font-size:20px;font-weight:700;color:#2e7d32;letter-spacing:2px;margin-top:4px;}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:13px;}
.info-row .label{color:#757575;}
.info-row .val{font-weight:600;color:#1a1a2e;}
.qr-section{text-align:center;margin:22px 0;padding:20px;background:#f8f9fa;border-radius:14px;}
.qr-section h3{font-size:14px;color:#1a1a2e;margin-bottom:14px;display:flex;align-items:center;justify-content:center;gap:8px;}
.qr-section h3 i{color:#2196F3;}
.qr-section img{border-radius:8px;border:3px solid #e0e0e0;}
.qr-section p{font-size:12px;color:#757575;margin-top:10px;}
.actions{display:flex;flex-direction:column;gap:10px;margin-top:22px;}
.btn{padding:13px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;text-align:center;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px;border:none;cursor:pointer;font-family:'Segoe UI',sans-serif;}
.btn-primary{background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(33,150,243,.3);}
.btn-secondary{background:white;color:#555;border:1.5px solid #e0e0e0;}
.btn-secondary:hover{border-color:#2196F3;color:#2196F3;}
.footer{background:#f8f9fa;padding:14px;text-align:center;font-size:11px;color:#aaa;}
</style>
</head>
<body>
<div class="card">
  <div class="success-header">
    <div class="check-circle"><i class="fas fa-check"></i></div>
    <h1>Paiement confirmé !</h1>
    <p>Votre participation a bien été enregistrée et payée</p>
  </div>

  <div class="body">
    <div class="ref-badge">
      <div class="label">Référence de paiement</div>
      <div class="val"><?= htmlspecialchars($ref) ?></div>
    </div>

    <div class="info-row"><span class="label">Participant</span><span class="val"><?= htmlspecialchars($p->getNom()) ?></span></div>
    <div class="info-row"><span class="label">Email</span><span class="val"><?= htmlspecialchars($p->getEmail()) ?></span></div>
    <div class="info-row"><span class="label">Places réservées</span><span class="val"><?= $p->getNbPlacesReservees() ?></span></div>
    <div class="info-row"><span class="label">Montant payé</span><span class="val"><?= number_format($p->getMontantPaye(), 2) ?> TND</span></div>
    <div class="info-row" style="border:none;"><span class="label">Statut paiement</span><span class="val" style="color:#2e7d32;"><i class="fas fa-check-circle"></i> Payé</span></div>

    <?php if ($qrUrl): ?>
    <div class="qr-section">
      <h3><i class="fas fa-qrcode"></i> Votre QR Code d'entrée</h3>
      <img src="../../<?= htmlspecialchars($qrUrl) ?>" width="160" height="160" alt="QR Code participation">
      <p>Présentez ce QR Code à l'entrée de l'événement.<br>Un email de confirmation vous a été envoyé.</p>
    </div>
    <?php else: ?>
    <div class="qr-section" style="background:#fff3e0;">
      <i class="fas fa-info-circle" style="color:#FFA726;font-size:24px;display:block;margin-bottom:8px;"></i>
      <p style="color:#e65100;font-size:12px;">QR Code non disponible (lib phpqrcode non installée).<br>
      <a href="https://sourceforge.net/projects/phpqrcode/" target="_blank" style="color:#2196F3;">Télécharger phpqrcode</a> et placer dans <code>lib/phpqrcode/</code></p>
    </div>
    <?php endif; ?>

    <div class="actions">
      <a href="participationList.php" class="btn btn-primary">
        <i class="fas fa-users"></i> Voir toutes les participations
      </a>
      <a href="evenementList.php" class="btn btn-secondary">
        <i class="fas fa-calendar"></i> Retour aux événements
      </a>
    </div>
  </div>

  <div class="footer">NutriLoop — Paiement simulé à des fins académiques</div>
</div>
</body>
</html>