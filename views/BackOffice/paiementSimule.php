<?php
require_once '../../controleurs/EvenementController.php';
require_once '../../controleurs/ParticipationController.php';

$evenementController     = new EvenementController();
$participationController = new ParticipationController();

$id_evenement        = intval($_GET['id_evenement']        ?? 0);
$nb_places           = intval($_GET['nb_places']           ?? 1);
$id_user             = intval($_GET['id_user']             ?? 0);
$nom                 = htmlspecialchars($_GET['nom']        ?? '');
$email               = htmlspecialchars($_GET['email']      ?? '');
$telephone           = htmlspecialchars($_GET['telephone']  ?? '');
$statut_participation = htmlspecialchars($_GET['statut']   ?? 'EN_ATTENTE');

if (!$id_evenement) { header('Location: evenementList.php'); exit; }
$evenement = $evenementController->getEvenementById($id_evenement);
if (!$evenement) { header('Location: evenementList.php'); exit; }

$montant_total = $evenement->getPrix() * $nb_places;
$success = $error = '';

// ── Traitement du paiement simulé ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carte_nom    = trim($_POST['carte_nom']    ?? '');
    $carte_numero = preg_replace('/\s/', '', $_POST['carte_numero'] ?? '');
    $carte_exp    = trim($_POST['carte_exp']    ?? '');
    $carte_cvv    = trim($_POST['carte_cvv']    ?? '');

    $erreurs = [];
    if (strlen($carte_nom) < 3)                              $erreurs[] = "Nom du titulaire invalide.";
    if (!preg_match('/^\d{16}$/', $carte_numero))            $erreurs[] = "Numéro de carte invalide (16 chiffres).";
    if (!preg_match('/^\d{2}\/\d{2}$/', $carte_exp))        $erreurs[] = "Date d'expiration invalide (MM/AA).";
    if (!preg_match('/^\d{3,4}$/', $carte_cvv))              $erreurs[] = "CVV invalide.";

    if (empty($erreurs)) {
        // Simuler paiement réussi — générer référence unique
        $reference = 'NL-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Créer participation avec statut PAYE
        $participation = new Participation(
            $id_evenement,
            $id_user,
            $nom,
            $email,
            $telephone ?: null,
            $statut_participation,
            null, null, null,
            $nb_places,
            'PAYE',
            $reference,
            $montant_total
        );

        $newId = $participationController->addParticipation($participation, $evenement);
        if ($newId) {
            // Régénérer QR Code avec les infos paiement
            $participation->setIdParticipation($newId);
            $participationController->genererQRCode($participation);
            header('Location: paiementSuccess.php?id=' . $newId . '&ref=' . $reference);
            exit;
        } else {
            $error = "Erreur lors de l'enregistrement. Veuillez réessayer.";
        }
    } else {
        $error = implode('<br>', $erreurs);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paiement — <?= htmlspecialchars($evenement->getTitre()) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
body{background:#f0f2f5;padding:30px 20px;min-height:100vh;}
.container{max-width:900px;margin:0 auto;display:grid;grid-template-columns:1fr 380px;gap:24px;}
@media(max-width:768px){.container{grid-template-columns:1fr;}}

/* ── Récapitulatif ── */
.recap{background:white;border-radius:16px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,.06);}
.recap h2{font-size:16px;color:#1a1a2e;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.recap h2 i{color:#4CAF50;}
.ev-header{background:linear-gradient(135deg,#2196F3,#4CAF50);border-radius:12px;padding:20px;color:white;margin-bottom:18px;}
.ev-header h3{font-size:17px;margin-bottom:6px;}
.ev-header p{font-size:12px;opacity:.85;display:flex;align-items:center;gap:6px;margin:4px 0;}
.detail-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;}
.detail-row:last-child{border:none;}
.detail-row .label{color:#757575;}
.detail-row .val{font-weight:600;color:#1a1a2e;}
.total-row{background:#f8fff8;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;margin-top:12px;border:2px solid #4CAF50;}
.total-row .label{font-size:14px;color:#2e7d32;font-weight:600;}
.total-row .val{font-size:22px;font-weight:700;color:#2e7d32;}
.secure-badge{display:flex;align-items:center;gap:8px;background:#f5f5f5;border-radius:8px;padding:10px 14px;margin-top:14px;font-size:12px;color:#555;}
.secure-badge i{color:#4CAF50;font-size:14px;}

/* ── Formulaire paiement ── */
.pay-form{background:white;border-radius:16px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,.06);}
.pay-form h2{font-size:16px;color:#1a1a2e;margin-bottom:20px;display:flex;align-items:center;gap:8px;}
.pay-form h2 i{color:#2196F3;}

/* Simulé badge */
.sim-badge{background:#fff3e0;border:1.5px solid #FFA726;border-radius:10px;padding:10px 14px;margin-bottom:18px;font-size:12px;color:#e65100;display:flex;align-items:center;gap:8px;}
.sim-badge i{font-size:14px;}

.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:12px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.6px;margin-bottom:7px;}
.form-group input{width:100%;padding:12px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;transition:all .3s;outline:none;font-family:'Segoe UI',sans-serif;}
.form-group input:focus{border-color:#2196F3;background:#f0f7ff;}
.form-group input.error{border-color:#f44336;background:#fff5f5;}
.form-group input.ok{border-color:#4CAF50;background:#f5fff5;}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

/* Carte visuelle */
.card-preview{background:linear-gradient(135deg,#1a1a2e,#2d2d4e);border-radius:14px;padding:20px;margin-bottom:20px;color:white;position:relative;overflow:hidden;}
.card-preview::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05);}
.card-preview::after{content:'';position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.04);}
.card-chip{width:36px;height:28px;background:linear-gradient(135deg,#f0c060,#d4a030);border-radius:5px;margin-bottom:14px;}
.card-number{font-size:17px;letter-spacing:3px;margin-bottom:14px;font-family:monospace;}
.card-info{display:flex;justify-content:space-between;font-size:11px;opacity:.7;}
.card-brand{position:absolute;top:16px;right:18px;font-size:22px;opacity:.6;}

/* Bouton paiement */
.pay-btn{width:100%;background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;border:none;padding:16px;border-radius:12px;font-size:16px;font-weight:700;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:10px;font-family:'Segoe UI',sans-serif;margin-top:8px;}
.pay-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(33,150,243,.35);}
.pay-btn:active{transform:translateY(0);}
.pay-btn i{font-size:18px;}
.back-link{display:flex;align-items:center;gap:8px;color:#757575;text-decoration:none;font-size:13px;margin-top:14px;justify-content:center;}
.back-link:hover{color:#2196F3;}

.error-msg{background:#ffebee;border:1.5px solid #f44336;color:#c62828;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;}
.error-msg i{margin-right:6px;}
</style>
</head>
<body>

<div style="max-width:900px;margin:0 auto 20px;">
  <h1 style="font-size:1.6rem;color:#1a1a2e;display:flex;align-items:center;gap:10px;">
    <i class="fas fa-credit-card" style="color:#2196F3;"></i> Paiement de la participation
  </h1>
</div>

<div class="container">

  <!-- Récapitulatif -->
  <div class="recap">
    <h2><i class="fas fa-receipt"></i> Récapitulatif</h2>

    <div class="ev-header">
      <h3><?= htmlspecialchars($evenement->getTitre()) ?></h3>
      <p><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></p>
      <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($evenement->getLieu()) ?></p>
    </div>

    <div class="detail-row">
      <span class="label"><i class="fas fa-user"></i> Participant</span>
      <span class="val"><?= htmlspecialchars($nom) ?></span>
    </div>
    <div class="detail-row">
      <span class="label"><i class="fas fa-envelope"></i> Email</span>
      <span class="val"><?= htmlspecialchars($email) ?></span>
    </div>
    <div class="detail-row">
      <span class="label"><i class="fas fa-ticket-alt"></i> Places réservées</span>
      <span class="val"><?= $nb_places ?> place<?= $nb_places > 1 ? 's' : '' ?></span>
    </div>
    <div class="detail-row">
      <span class="label"><i class="fas fa-tag"></i> Prix unitaire</span>
      <span class="val"><?= number_format($evenement->getPrix(), 2) ?> TND</span>
    </div>

    <div class="total-row">
      <span class="label"><i class="fas fa-coins"></i> Total à payer</span>
      <span class="val"><?= number_format($montant_total, 2) ?> TND</span>
    </div>

    <div class="secure-badge">
      <i class="fas fa-lock"></i>
      <span>Paiement <strong>100% sécurisé</strong> — Vos données sont protégées</span>
    </div>
  </div>

  <!-- Formulaire paiement simulé -->
  <div class="pay-form">
    <h2><i class="fas fa-credit-card"></i> Informations de paiement</h2>

    <div class="sim-badge">
      <i class="fas fa-flask"></i>
      <span><strong>Mode simulation</strong> — Utilisez n'importe quelle valeur. Aucune transaction réelle.</span>
    </div>

    <?php if ($error): ?>
    <div class="error-msg"><i class="fas fa-exclamation-circle"></i><?= $error ?></div>
    <?php endif; ?>

    <!-- Aperçu carte -->
    <div class="card-preview" id="cardPreview">
      <div class="card-chip"></div>
      <div class="card-number" id="previewNumber">•••• •••• •••• ••••</div>
      <div class="card-info">
        <div>
          <div style="font-size:9px;opacity:.5;margin-bottom:2px;">TITULAIRE</div>
          <div id="previewNom" style="font-size:12px;letter-spacing:1px;">VOTRE NOM</div>
        </div>
        <div>
          <div style="font-size:9px;opacity:.5;margin-bottom:2px;">EXPIRE</div>
          <div id="previewExp" style="font-size:12px;">MM/AA</div>
        </div>
      </div>
      <div class="card-brand"><i class="fab fa-cc-visa"></i></div>
    </div>

    <form method="POST" id="payForm">
      <div class="form-group">
        <label><i class="fas fa-user"></i> Nom du titulaire</label>
        <input type="text" name="carte_nom" id="carte_nom" placeholder="EX: KARIM BEN ALI"
               value="<?= htmlspecialchars($_POST['carte_nom'] ?? '') ?>"
               oninput="updatePreview()">
      </div>

      <div class="form-group">
        <label><i class="fas fa-hashtag"></i> Numéro de carte</label>
        <input type="text" name="carte_numero" id="carte_numero" placeholder="1234 5678 9012 3456"
               maxlength="19" value="<?= htmlspecialchars($_POST['carte_numero'] ?? '') ?>"
               oninput="formatCard(this);updatePreview()">
      </div>

      <div class="row2">
        <div class="form-group">
          <label><i class="fas fa-calendar"></i> Date d'expiration</label>
          <input type="text" name="carte_exp" id="carte_exp" placeholder="MM/AA"
                 maxlength="5" value="<?= htmlspecialchars($_POST['carte_exp'] ?? '') ?>"
                 oninput="formatExp(this);updatePreview()">
        </div>
        <div class="form-group">
          <label><i class="fas fa-lock"></i> CVV</label>
          <input type="text" name="carte_cvv" id="carte_cvv" placeholder="123"
                 maxlength="4" value="<?= htmlspecialchars($_POST['carte_cvv'] ?? '') ?>">
        </div>
      </div>

      <button type="submit" class="pay-btn" id="payBtn">
        <i class="fas fa-lock"></i>
        Payer <?= number_format($montant_total, 2) ?> TND
      </button>
    </form>

    <a href="addParticipation.php?evenement_id=<?= $id_evenement ?>" class="back-link">
      <i class="fas fa-arrow-left"></i> Retour sans payer
    </a>
  </div>

</div>

<script>
function formatCard(input) {
  let val = input.value.replace(/\D/g, '').substring(0, 16);
  input.value = val.replace(/(\d{4})(?=\d)/g, '$1 ');
}

function formatExp(input) {
  let val = input.value.replace(/\D/g, '').substring(0, 4);
  if (val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
  input.value = val;
}

function updatePreview() {
  const nom    = document.getElementById('carte_nom').value.toUpperCase() || 'VOTRE NOM';
  const num    = document.getElementById('carte_numero').value || '';
  const exp    = document.getElementById('carte_exp').value || 'MM/AA';
  const numFmt = num.padEnd(19,'•').substring(0,19).replace(/(\d)(?=\d)/g,'$1');
  let display  = '';
  const clean  = num.replace(/\s/g,'');
  for (let i=0; i<16; i++) {
    if (i > 0 && i % 4 === 0) display += ' ';
    display += i < clean.length ? clean[i] : '•';
  }
  document.getElementById('previewNom').textContent    = nom;
  document.getElementById('previewNumber').textContent = display;
  document.getElementById('previewExp').textContent    = exp || 'MM/AA';
}

// Animation bouton paiement
document.getElementById('payForm')?.addEventListener('submit', function() {
  const btn = document.getElementById('payBtn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
  btn.disabled = true;
});
</script>
</body>
</html>