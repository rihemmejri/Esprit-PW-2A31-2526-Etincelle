<?php
require_once '../../controleurs/EvenementController.php';
require_once '../../controleurs/ParticipationController.php';

$evenementController     = new EvenementController();
$participationController = new ParticipationController();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: evenementList.php'); exit; }

$evenement      = $evenementController->getEvenementById($id);
if (!$evenement) { header('Location: evenementList.php'); exit; }

$participations = $participationController->getParticipationsByEvenement($id);
$totalPlacesReservees = array_sum(array_map(fn($p) => $p->getNbPlacesReservees(), $participations));
$placesRestantes = $evenement->getNbPlacesMax() - $totalPlacesReservees;

// Google Calendar lien
$cal_titre  = urlencode($evenement->getTitre());
$cal_lieu   = urlencode($evenement->getLieu());
$cal_date   = date('Ymd', strtotime($evenement->getDateEvenement()));
$cal_fin    = date('Ymd', strtotime($evenement->getDateEvenement() . ' +2 hours'));
$cal_desc   = urlencode('Événement NutriLoop — ' . $evenement->getDescription());
$googleCalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE"
    . "&text={$cal_titre}&dates={$cal_date}/{$cal_fin}"
    . "&location={$cal_lieu}&details={$cal_desc}";

// Google Maps embed (lieu encodé)
$lieuEncode = urlencode($evenement->getLieu());
$mapsEmbedUrl = "https://maps.google.com/maps?q={$lieuEncode}&output=embed&z=15";
$mapsUrl      = "https://www.google.com/maps/search/?api=1&query={$lieuEncode}";

// WhatsApp partage
$wa_text = urlencode(
    "🌿 *" . $evenement->getTitre() . "*\n"
    . "📅 " . date('d/m/Y', strtotime($evenement->getDateEvenement())) . "\n"
    . "📍 " . $evenement->getLieu() . "\n"
    . ($evenement->isPayant() ? "💰 " . number_format($evenement->getPrix(), 2) . " TND\n" : "✅ Gratuit\n")
    . "\nInscrivez-vous sur NutriLoop !"
);
$whatsappUrl = "https://wa.me/?text={$wa_text}";

// Facebook partage
$facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode("http://localhost/NutriLoop/");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($evenement->getTitre()) ?> — NutriLoop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
body{background:#f0f2f5;padding:24px 20px;}
.container{max-width:1100px;margin:0 auto;}

/* Header */
.ev-header{background:white;border-radius:16px;padding:28px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:18px;}
.ev-title h1{font-size:1.7rem;color:#1a1a2e;margin-bottom:8px;}
.meta{display:flex;flex-wrap:wrap;gap:14px;margin-top:10px;}
.meta-item{display:flex;align-items:center;gap:7px;font-size:14px;color:#555;}
.meta-item i{width:18px;text-align:center;}
.type-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:10px;}
.type-SPORT    {background:#e3f2fd;color:#1565c0;}
.type-NUTRITION{background:#e8f5e9;color:#2e7d32;}
.type-WORKSHOP {background:#fff3e0;color:#e65100;}
.type-AUTRE    {background:#f3e5f5;color:#6a1b9a;}
.statut-badge  {display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.statut-ACTIF    {background:#e8f5e9;color:#2e7d32;}
.statut-CANCELLED{background:#ffebee;color:#c62828;}
.statut-COMPLETED{background:#e8eaf6;color:#3949ab;}

/* Prix badge */
.prix-badge{background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;border-radius:12px;padding:14px 20px;text-align:center;min-width:120px;}
.prix-badge .prix-val{font-size:22px;font-weight:700;}
.prix-badge .prix-lbl{font-size:11px;opacity:.8;margin-top:2px;}

/* Grid */
.grid{display:grid;grid-template-columns:1fr 340px;gap:20px;}
@media(max-width:768px){.grid{grid-template-columns:1fr;}}
.main{display:flex;flex-direction:column;gap:20px;}
.side{display:flex;flex-direction:column;gap:16px;}

/* Cards */
.card{background:white;border-radius:16px;padding:22px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.card h3{font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.card h3 i{color:#2196F3;}

/* KPIs */
.kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
.kpi{background:#f5f5f5;border-radius:12px;padding:14px;text-align:center;border-left:3px solid #2196F3;}
.kpi strong{display:block;font-size:20px;font-weight:700;color:#1a1a2e;}
.kpi span{font-size:11px;color:#757575;text-transform:uppercase;letter-spacing:.5px;}
.kpi.green{border-color:#4CAF50;}
.kpi.orange{border-color:#FFA726;}

/* Description */
.description{font-size:14px;color:#555;line-height:1.7;}

/* Google Maps */
.map-container{border-radius:12px;overflow:hidden;border:1.5px solid #e0e0e0;}
.map-container iframe{width:100%;height:250px;border:none;display:block;}
.map-actions{display:flex;gap:10px;margin-top:12px;}
.map-btn{flex:1;padding:10px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;text-align:center;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;border:none;cursor:pointer;font-family:'Segoe UI',sans-serif;}
.map-btn-primary{background:#2196F3;color:white;}.map-btn-primary:hover{background:#1976D2;}
.map-btn-secondary{background:#f5f5f5;color:#555;border:1.5px solid #e0e0e0;}.map-btn-secondary:hover{border-color:#2196F3;color:#2196F3;}

/* Google Calendar */
.cal-btn{width:100%;padding:12px;background:white;border:2px solid #4285F4;border-radius:10px;color:#4285F4;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;text-decoration:none;transition:.3s;font-family:'Segoe UI',sans-serif;}
.cal-btn:hover{background:#4285F4;color:white;}
.cal-btn img{width:20px;height:20px;}

/* Partage */
.share-btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.share-btn{padding:10px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;text-align:center;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;}
.share-wa{background:#25D366;color:white;}.share-wa:hover{background:#1da851;}
.share-fb{background:#1877F2;color:white;}.share-fb:hover{background:#166fe5;}

/* Inscription */
.inscr-btn{width:100%;padding:14px;border-radius:12px;border:none;cursor:pointer;font-size:15px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;transition:.3s;font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#4CAF50,#2196F3);color:white;}
.inscr-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(76,175,80,.3);}
.inscr-btn.payant{background:linear-gradient(135deg,#2196F3,#673AB7);}
.inscr-btn:disabled,.inscr-btn.disabled{background:#ccc;cursor:not-allowed;transform:none;box-shadow:none;}

/* Table participations */
table{width:100%;border-collapse:collapse;}
th{background:#1a1a2e;color:white;padding:10px 12px;text-align:left;font-size:12px;font-weight:600;}
td{padding:10px 12px;border-bottom:1px solid #eee;font-size:13px;}
tr:hover td{background:#f8f9fa;}
.statut-badge-sm{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.statut-EN_ATTENTE{background:#fff3e0;color:#e65100;}
.statut-CONFIRMEE {background:#e8f5e9;color:#2e7d32;}
.statut-ANNULEE   {background:#ffebee;color:#c62828;}
.statut-PRESENTE  {background:#e3f2fd;color:#1565c0;}
.pay-badge{display:inline-block;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:600;}
.pay-PAYE   {background:#e8f5e9;color:#2e7d32;}
.pay-GRATUIT{background:#f5f5f5;color:#757575;}
.pay-EN_ATTENTE{background:#fff3e0;color:#e65100;}

.qr-thumb{width:50px;height:50px;border-radius:6px;border:1.5px solid #e0e0e0;cursor:pointer;transition:transform .2s;}
.qr-thumb:hover{transform:scale(1.5);}
.no-qr{font-size:10px;color:#bbb;}

/* Actions header */
.actions-header{display:flex;gap:10px;}
.btn-edit{padding:10px 18px;background:#2196F3;color:white;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;}
.btn-back{padding:10px 18px;background:#f5f5f5;color:#555;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;border:1.5px solid #e0e0e0;}
.btn-back:hover{border-color:#2196F3;color:#2196F3;}
</style>
</head>
<body>
<div class="container">

<!-- Header -->
<div class="ev-header">
  <div class="ev-title">
    <span class="type-badge type-<?= $evenement->getTypeEvenement() ?>">
      <?php $icons=['SPORT'=>'fa-running','NUTRITION'=>'fa-apple-alt','WORKSHOP'=>'fa-chalkboard-teacher','AUTRE'=>'fa-calendar'];?>
      <i class="fas <?= $icons[$evenement->getTypeEvenement()]??'fa-calendar' ?>"></i>
      <?= $evenement->getTypeEvenement() ?>
    </span>
    &nbsp;
    <span class="statut-badge statut-<?= $evenement->getStatut() ?>"><?= $evenement->getStatut() ?></span>
    <h1><?= htmlspecialchars($evenement->getTitre()) ?></h1>
    <div class="meta">
      <div class="meta-item"><i class="fas fa-calendar-day" style="color:#2196F3;"></i><?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></div>
      <div class="meta-item"><i class="fas fa-map-marker-alt" style="color:#f44336;"></i><?= htmlspecialchars($evenement->getLieu()) ?></div>
      <div class="meta-item"><i class="fas fa-users" style="color:#4CAF50;"></i><?= $evenement->getNbPlacesMax() ?> places max</div>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;">
    <div class="actions-header">
      <a href="editEvenement.php?id=<?= $id ?>" class="btn-edit"><i class="fas fa-edit"></i> Modifier</a>
      <a href="evenementList.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    <div class="prix-badge">
      <?php if ($evenement->isPayant()): ?>
        <div class="prix-val"><?= number_format($evenement->getPrix(), 2) ?> TND</div>
        <div class="prix-lbl">par personne</div>
      <?php else: ?>
        <div class="prix-val"><i class="fas fa-check"></i> Gratuit</div>
        <div class="prix-lbl">inscription libre</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="grid">
<div class="main">

  <!-- KPIs -->
  <div class="card">
    <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
    <div class="kpis">
      <div class="kpi"><strong><?= count($participations) ?></strong><span>Participants</span></div>
      <div class="kpi green"><strong><?= max(0,$placesRestantes) ?></strong><span>Places restantes</span></div>
      <div class="kpi orange"><strong><?= $totalPlacesReservees ?></strong><span>Places réservées</span></div>
    </div>
    <!-- Barre de progression -->
    <div style="margin-top:16px;">
      <div style="display:flex;justify-content:space-between;font-size:12px;color:#757575;margin-bottom:6px;">
        <span>Taux de remplissage</span>
        <span><?= $evenement->getNbPlacesMax() > 0 ? round(($totalPlacesReservees/$evenement->getNbPlacesMax())*100) : 0 ?>%</span>
      </div>
      <div style="background:#e0e0e0;border-radius:4px;height:8px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#4CAF50,#2196F3);height:8px;width:<?= $evenement->getNbPlacesMax()>0?min(100,round(($totalPlacesReservees/$evenement->getNbPlacesMax())*100)):0 ?>%;border-radius:4px;transition:width .5s;"></div>
      </div>
    </div>
  </div>

  <!-- Description -->
  <div class="card">
    <h3><i class="fas fa-align-left"></i> Description</h3>
    <p class="description"><?= nl2br(htmlspecialchars($evenement->getDescription())) ?></p>
  </div>

  <!-- Google Maps -->
  <div class="card">
    <h3><i class="fas fa-map-marked-alt" style="color:#f44336;"></i> Lieu de l'événement</h3>
    <div class="map-container">
      <iframe
        src="<?= $mapsEmbedUrl ?>"
        allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
    <p style="font-size:13px;color:#555;margin:10px 0 12px;"><i class="fas fa-map-marker-alt" style="color:#f44336;margin-right:6px;"></i><?= htmlspecialchars($evenement->getLieu()) ?></p>
    <div class="map-actions">
      <a href="<?= $mapsUrl ?>" target="_blank" class="map-btn map-btn-primary">
        <i class="fas fa-directions"></i> Obtenir un itinéraire
      </a>
      <a href="<?= $mapsUrl ?>" target="_blank" class="map-btn map-btn-secondary">
        <i class="fas fa-external-link-alt"></i> Ouvrir Maps
      </a>
    </div>
  </div>

  <!-- Liste des participations -->
  <div class="card">
    <h3><i class="fas fa-users"></i> Participations (<?= count($participations) ?>)</h3>
    <?php if (count($participations) > 0): ?>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Places</th>
            <th>Statut</th>
            <?php if ($evenement->isPayant()): ?><th>Paiement</th><?php endif; ?>
            <th>QR Code</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($participations as $p): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($p->getNom()) ?></strong><br>
              <span style="font-size:11px;color:#888;"><?= htmlspecialchars($p->getEmail()) ?></span>
            </td>
            <td><strong><?= $p->getNbPlacesReservees() ?></strong></td>
            <td><span class="statut-badge-sm statut-<?= $p->getStatut() ?>"><?= $p->getStatut() ?></span></td>
            <?php if ($evenement->isPayant()): ?>
            <td>
              <span class="pay-badge pay-<?= $p->getStatutPaiement() ?>"><?= $p->getStatutPaiement() ?></span>
              <?php if ($p->getReferencePaiement()): ?>
              <br><span style="font-size:10px;color:#888;"><?= htmlspecialchars($p->getReferencePaiement()) ?></span>
              <?php endif; ?>
            </td>
            <?php endif; ?>
            <td>
              <?php $qr = $participationController->getQRCodeUrl($p->getIdParticipation()); ?>
              <?php if ($qr): ?>
                <img src="../../<?= htmlspecialchars($qr) ?>" class="qr-thumb" alt="QR Code"
                     title="<?= htmlspecialchars($p->getNom()) ?>">
              <?php else: ?>
                <span class="no-qr">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <p style="text-align:center;padding:30px;color:#bbb;font-size:14px;">
      <i class="fas fa-user-slash" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
      Aucune participation pour cet événement
    </p>
    <?php endif; ?>
  </div>

</div>
<div class="side">

  <!-- Inscription / Paiement -->
  <div class="card">
    <h3><i class="fas fa-user-plus" style="color:#4CAF50;"></i> S'inscrire</h3>
    <?php if ($evenement->getStatut() === 'ACTIF' && $placesRestantes > 0): ?>
      <?php if ($evenement->isPayant()): ?>
        <p style="font-size:13px;color:#555;margin-bottom:14px;">
          Tarif : <strong style="color:#2196F3;"><?= number_format($evenement->getPrix(), 2) ?> TND</strong> / place
        </p>
        <a href="addParticipation.php?evenement_id=<?= $id ?>" class="inscr-btn payant">
          <i class="fas fa-credit-card"></i> S'inscrire et payer
        </a>
      <?php else: ?>
        <p style="font-size:13px;color:#555;margin-bottom:14px;">Cet événement est <strong style="color:#2e7d32;">gratuit</strong>. Inscription libre.</p>
        <a href="addParticipation.php?evenement_id=<?= $id ?>" class="inscr-btn">
          <i class="fas fa-user-plus"></i> S'inscrire gratuitement
        </a>
      <?php endif; ?>
    <?php else: ?>
      <div class="inscr-btn disabled" style="pointer-events:none;">
        <i class="fas fa-ban"></i>
        <?= $placesRestantes <= 0 ? 'Complet' : 'Inscriptions fermées' ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Google Calendar -->
  <div class="card">
    <h3><i class="fas fa-calendar-plus" style="color:#4285F4;"></i> Ajouter à mon agenda</h3>
    <a href="<?= $googleCalUrl ?>" target="_blank" class="cal-btn">
      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Google_Calendar_icon_%282020%29.svg/48px-Google_Calendar_icon_%282020%29.svg.png" alt="Google Calendar">
      Ajouter à Google Calendar
    </a>
    <p style="font-size:11px;color:#aaa;margin-top:10px;text-align:center;">
      <?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?> — <?= htmlspecialchars($evenement->getLieu()) ?>
    </p>
  </div>

  <!-- Partager -->
  <div class="card">
    <h3><i class="fas fa-share-alt"></i> Partager l'événement</h3>
    <div class="share-btns">
      <a href="<?= $whatsappUrl ?>" target="_blank" class="share-btn share-wa">
        <i class="fab fa-whatsapp"></i> WhatsApp
      </a>
      <a href="<?= $facebookUrl ?>" target="_blank" class="share-btn share-fb">
        <i class="fab fa-facebook"></i> Facebook
      </a>
    </div>
  </div>

  <!-- Infos rapides -->
  <div class="card">
    <h3><i class="fas fa-info-circle"></i> Informations</h3>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">
        <span style="color:#757575;">Type</span><span style="font-weight:600;"><?= $evenement->getTypeEvenement() ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">
        <span style="color:#757575;">Statut</span><span style="font-weight:600;"><?= $evenement->getStatut() ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">
        <span style="color:#757575;">Places max</span><span style="font-weight:600;"><?= $evenement->getNbPlacesMax() ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:13px;">
        <span style="color:#757575;">Tarif</span>
        <span style="font-weight:600;color:<?= $evenement->isPayant()?'#2196F3':'#2e7d32' ?>;">
          <?= $evenement->isPayant() ? number_format($evenement->getPrix(),2).' TND' : 'Gratuit' ?>
        </span>
      </div>
    </div>
  </div>

</div>
</div>
</div>
</body>
</html>