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

$lieuEncode   = urlencode($evenement->getLieu());
$mapsEmbedUrl = "https://maps.google.com/maps?q={$lieuEncode}&output=embed&z=15";
$mapsUrl      = "https://www.google.com/maps/search/?api=1&query={$lieuEncode}";

$inscriptionUrl = 'http://localhost:8000/views/FrontOffice/afficherParticipation.php?id=' . $id;

$wa_text = urlencode(
    "🌿 *" . $evenement->getTitre() . "*\n"
    . "📅 " . date('d/m/Y', strtotime($evenement->getDateEvenement())) . "\n"
    . "📍 " . $evenement->getLieu() . "\n"
    . ($evenement->isPayant() ? "💰 " . number_format($evenement->getPrix(), 2) . " TND\n" : "✅ Gratuit\n")
    . "\n🔗 Inscrivez-vous : " . $inscriptionUrl
);
$whatsappUrl = "https://wa.me/?text={$wa_text}";
$facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($inscriptionUrl);

$evYear  = (int)date('Y', strtotime($evenement->getDateEvenement()));
$evMonth = (int)date('n', strtotime($evenement->getDateEvenement()));
$evDay   = (int)date('j', strtotime($evenement->getDateEvenement()));
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
.ev-header{background:white;border-radius:16px;padding:28px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:18px;}
.ev-title h1{font-size:1.7rem;color:#1a1a2e;margin-bottom:8px;}
.meta{display:flex;flex-wrap:wrap;gap:14px;margin-top:10px;}
.meta-item{display:flex;align-items:center;gap:7px;font-size:14px;color:#555;}
.meta-item i{width:18px;text-align:center;}
.type-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:10px;}
.type-SPORT{background:#e3f2fd;color:#1565c0;}.type-NUTRITION{background:#e8f5e9;color:#2e7d32;}
.type-WORKSHOP{background:#fff3e0;color:#e65100;}.type-AUTRE{background:#f3e5f5;color:#6a1b9a;}
.statut-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.statut-ACTIF{background:#e8f5e9;color:#2e7d32;}.statut-CANCELLED{background:#ffebee;color:#c62828;}.statut-COMPLETED{background:#e8eaf6;color:#3949ab;}
.prix-badge{background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;border-radius:12px;padding:14px 20px;text-align:center;min-width:120px;}
.prix-badge .prix-val{font-size:22px;font-weight:700;}
.prix-badge .prix-lbl{font-size:11px;opacity:.8;margin-top:2px;}
.grid{display:grid;grid-template-columns:1fr 340px;gap:20px;}
@media(max-width:768px){.grid{grid-template-columns:1fr;}}
.main{display:flex;flex-direction:column;gap:20px;}
.side{display:flex;flex-direction:column;gap:16px;}
.card{background:white;border-radius:16px;padding:22px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.card h3{font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.card h3 i{color:#2196F3;}
.kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
.kpi{background:#f5f5f5;border-radius:12px;padding:14px;text-align:center;border-left:3px solid #2196F3;}
.kpi strong{display:block;font-size:20px;font-weight:700;color:#1a1a2e;}
.kpi span{font-size:11px;color:#757575;text-transform:uppercase;letter-spacing:.5px;}
.kpi.green{border-color:#4CAF50;}.kpi.orange{border-color:#FFA726;}
.description{font-size:14px;color:#555;line-height:1.7;}
.map-container{border-radius:12px;overflow:hidden;border:1.5px solid #e0e0e0;}
.map-container iframe{width:100%;height:250px;border:none;display:block;}
.map-actions{display:flex;gap:10px;margin-top:12px;}
.map-btn{flex:1;padding:10px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;text-align:center;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;border:none;cursor:pointer;font-family:'Segoe UI',sans-serif;}
.map-btn-primary{background:#2196F3;color:white;}.map-btn-primary:hover{background:#1976D2;}
.map-btn-secondary{background:#f5f5f5;color:#555;border:1.5px solid #e0e0e0;}.map-btn-secondary:hover{border-color:#2196F3;color:#2196F3;}

/* CALENDRIER */
.cal-wrap{user-select:none;}
.cal-nav{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;}
.cal-nav h4{font-size:15px;font-weight:700;color:#1a1a2e;}
.cal-nav-btn{background:#f5f5f5;border:none;width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:14px;color:#555;transition:.2s;display:flex;align-items:center;justify-content:center;}
.cal-nav-btn:hover{background:#e3f2fd;color:#2196F3;}
.cal-days-header{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:6px;}
.cal-day-name{text-align:center;font-size:11px;font-weight:700;color:#aaa;padding:4px 0;text-transform:uppercase;}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;}
.cal-cell{aspect-ratio:1;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all .2s;position:relative;color:#333;}
.cal-cell:hover:not(.cal-empty):not(.cal-other){background:#e3f2fd;color:#1565C0;}
.cal-cell.cal-empty,.cal-cell.cal-other{cursor:default;color:#ddd;}
.cal-cell.cal-today{background:#e8f5e9;color:#2e7d32;font-weight:700;}
.cal-cell.cal-event{background:linear-gradient(135deg,#2196F3,#4CAF50)!important;color:white!important;font-weight:700;box-shadow:0 4px 12px rgba(33,150,243,.4);}
.cal-cell.cal-event::after{content:'';position:absolute;bottom:3px;left:50%;transform:translateX(-50%);width:4px;height:4px;border-radius:50%;background:white;}
.cal-cell.cal-saved{background:#fff3e0;color:#e65100;font-weight:700;border:2px solid #FFA726;}
.cal-cell.cal-saved.cal-event{background:linear-gradient(135deg,#FFA726,#f44336)!important;color:white!important;}
.cal-legend{display:flex;gap:12px;margin-top:12px;flex-wrap:wrap;}
.cal-leg-item{display:flex;align-items:center;gap:6px;font-size:11px;color:#666;}
.cal-leg-dot{width:10px;height:10px;border-radius:3px;}

/* Toast */
.toast{position:fixed;bottom:24px;right:24px;background:#1a1a2e;color:white;padding:14px 20px;border-radius:12px;font-size:13px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.2);z-index:9999;display:flex;align-items:center;gap:10px;transform:translateY(100px);opacity:0;transition:all .3s cubic-bezier(.34,1.56,.64,1);}
.toast.show{transform:translateY(0);opacity:1;}
.toast i{color:#4CAF50;font-size:16px;}

/* Partage */
.share-btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;}
.share-btn{padding:10px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;text-align:center;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;}
.share-wa{background:#25D366;color:white;}.share-wa:hover{background:#1da851;}
.share-fb{background:#1877F2;color:white;}.share-fb:hover{background:#166fe5;}
.share-copy{width:100%;padding:10px;border-radius:8px;background:#f5f5f5;color:#333;border:1.5px solid #e0e0e0;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;font-family:'Segoe UI',sans-serif;}
.share-copy:hover{background:#e3f2fd;border-color:#2196F3;color:#1565C0;}
.share-link-box{background:#f5f5f5;border-radius:8px;padding:10px 14px;font-size:12px;color:#555;word-break:break-all;border:1.5px solid #e0e0e0;margin-top:10px;}

.inscr-btn{width:100%;padding:14px;border-radius:12px;border:none;cursor:pointer;font-size:15px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;transition:.3s;font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#4CAF50,#2196F3);color:white;}
.inscr-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(76,175,80,.3);}
.inscr-btn.payant{background:linear-gradient(135deg,#2196F3,#673AB7);}
.inscr-btn.disabled{background:#ccc;cursor:not-allowed;transform:none;pointer-events:none;}

table{width:100%;border-collapse:collapse;}
th{background:#1a1a2e;color:white;padding:10px 12px;text-align:left;font-size:12px;font-weight:600;}
td{padding:10px 12px;border-bottom:1px solid #eee;font-size:13px;}
tr:hover td{background:#f8f9fa;}
.pay-badge{display:inline-block;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:600;}
.pay-PAYE{background:#e8f5e9;color:#2e7d32;}.pay-GRATUIT{background:#f5f5f5;color:#757575;}.pay-EN_ATTENTE{background:#fff3e0;color:#e65100;}
.actions-header{display:flex;gap:10px;}
.btn-edit{padding:10px 18px;background:#2196F3;color:white;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;}
.btn-back{padding:10px 18px;background:#f5f5f5;color:#555;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;border:1.5px solid #e0e0e0;}
.btn-back:hover{border-color:#2196F3;color:#2196F3;}
</style>
</head>
<body>
<div class="container">

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
    <div style="margin-top:16px;">
      <div style="display:flex;justify-content:space-between;font-size:12px;color:#757575;margin-bottom:6px;">
        <span>Taux de remplissage</span>
        <span><?= $evenement->getNbPlacesMax() > 0 ? round(($totalPlacesReservees/$evenement->getNbPlacesMax())*100) : 0 ?>%</span>
      </div>
      <div style="background:#e0e0e0;border-radius:4px;height:8px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#4CAF50,#2196F3);height:8px;width:<?= $evenement->getNbPlacesMax()>0?min(100,round(($totalPlacesReservees/$evenement->getNbPlacesMax())*100)):0 ?>%;border-radius:4px;"></div>
      </div>
    </div>
  </div>

  <!-- Description -->
  <div class="card">
    <h3><i class="fas fa-align-left"></i> Description</h3>
    <p class="description"><?= nl2br(htmlspecialchars($evenement->getDescription())) ?></p>
  </div>

  <!-- Maps -->
  <div class="card">
    <h3><i class="fas fa-map-marked-alt" style="color:#f44336;"></i> Lieu de l'événement</h3>
    <div class="map-container">
      <iframe src="<?= $mapsEmbedUrl ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <p style="font-size:13px;color:#555;margin:10px 0 12px;"><i class="fas fa-map-marker-alt" style="color:#f44336;margin-right:6px;"></i><?= htmlspecialchars($evenement->getLieu()) ?></p>
    <div class="map-actions">
      <a href="<?= $mapsUrl ?>" target="_blank" class="map-btn map-btn-primary"><i class="fas fa-directions"></i> Itinéraire</a>
      <a href="<?= $mapsUrl ?>" target="_blank" class="map-btn map-btn-secondary"><i class="fas fa-external-link-alt"></i> Ouvrir Maps</a>
    </div>
  </div>

  <!-- Participations — sans Statut ni QR Code -->
  <div class="card">
    <h3><i class="fas fa-users"></i> Participations (<?= count($participations) ?>)</h3>
    <?php if (count($participations) > 0): ?>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Places</th>
            <?php if ($evenement->isPayant()): ?><th>Paiement</th><?php endif; ?>
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
            <?php if ($evenement->isPayant()): ?>
            <td>
              <span class="pay-badge pay-<?= $p->getStatutPaiement() ?>"><?= $p->getStatutPaiement() ?></span>
              <?php if ($p->getReferencePaiement()): ?>
              <br><span style="font-size:10px;color:#888;"><?= htmlspecialchars($p->getReferencePaiement()) ?></span>
              <?php endif; ?>
            </td>
            <?php endif; ?>
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

  <!-- Inscription -->
  <div class="card">
    <h3><i class="fas fa-user-plus" style="color:#4CAF50;"></i> S'inscrire</h3>
    <?php if ($evenement->getStatut() === 'ACTIF' && $placesRestantes > 0): ?>
      <?php if ($evenement->isPayant()): ?>
        <p style="font-size:13px;color:#555;margin-bottom:14px;">Tarif : <strong style="color:#2196F3;"><?= number_format($evenement->getPrix(), 2) ?> TND</strong> / place</p>
        <a href="addParticipation.php?evenement_id=<?= $id ?>" class="inscr-btn payant"><i class="fas fa-credit-card"></i> S'inscrire et payer</a>
      <?php else: ?>
        <p style="font-size:13px;color:#555;margin-bottom:14px;">Cet événement est <strong style="color:#2e7d32;">gratuit</strong>.</p>
        <a href="addParticipation.php?evenement_id=<?= $id ?>" class="inscr-btn"><i class="fas fa-user-plus"></i> S'inscrire gratuitement</a>
      <?php endif; ?>
    <?php else: ?>
      <div class="inscr-btn disabled"><i class="fas fa-ban"></i> <?= $placesRestantes <= 0 ? 'Complet' : 'Inscriptions fermées' ?></div>
    <?php endif; ?>
  </div>

  <!-- CALENDRIER INTÉGRÉ -->
  <div class="card">
    <h3><i class="fas fa-calendar-alt"></i> Mon agenda NutriLoop</h3>
    <div class="cal-wrap">
      <div class="cal-nav">
        <button class="cal-nav-btn" onclick="calPrev()"><i class="fas fa-chevron-left"></i></button>
        <h4 id="calTitle"></h4>
        <button class="cal-nav-btn" onclick="calNext()"><i class="fas fa-chevron-right"></i></button>
      </div>
      <div class="cal-days-header">
        <div class="cal-day-name">Lun</div><div class="cal-day-name">Mar</div><div class="cal-day-name">Mer</div>
        <div class="cal-day-name">Jeu</div><div class="cal-day-name">Ven</div><div class="cal-day-name">Sam</div>
        <div class="cal-day-name">Dim</div>
      </div>
      <div class="cal-grid" id="calGrid"></div>
      <div class="cal-legend">
        <div class="cal-leg-item"><div class="cal-leg-dot" style="background:linear-gradient(135deg,#2196F3,#4CAF50);"></div> Événement</div>
        <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#fff3e0;border:2px solid #FFA726;"></div> Enregistré</div>
        <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#e8f5e9;"></div> Aujourd'hui</div>
      </div>
    </div>
    <p style="font-size:11px;color:#aaa;margin-top:10px;text-align:center;">
      Cliquez sur la date surlignée pour enregistrer dans votre agenda
    </p>
  </div>

  <!-- PARTAGER -->
  <div class="card">
    <h3><i class="fas fa-share-alt"></i> Partager l'événement</h3>
    <div class="share-btns">
      <a href="<?= $whatsappUrl ?>" target="_blank" class="share-btn share-wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
      <a href="<?= $facebookUrl ?>" target="_blank" class="share-btn share-fb"><i class="fab fa-facebook"></i> Facebook</a>
    </div>
    <button class="share-copy" onclick="copyLink()"><i class="fas fa-link"></i> Copier le lien d'inscription</button>
    <div class="share-link-box"><?= htmlspecialchars($inscriptionUrl) ?></div>
  </div>

  <!-- Infos -->
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

<div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<script>
const EV_YEAR  = <?= $evYear ?>;
const EV_MONTH = <?= $evMonth ?>;
const EV_DAY   = <?= $evDay ?>;
const EV_TITLE = <?= json_encode($evenement->getTitre()) ?>;
const STORAGE_KEY = 'nutriloop_agenda';
const MOIS = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

let calYear = EV_YEAR, calMonth = EV_MONTH;

function getSaved() { try { return JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); } catch(e){ return []; } }
function saveDate(dateStr) {
    const saved = getSaved();
    if (!saved.includes(dateStr)) { saved.push(dateStr); localStorage.setItem(STORAGE_KEY, JSON.stringify(saved)); return true; }
    else { saved.splice(saved.indexOf(dateStr),1); localStorage.setItem(STORAGE_KEY, JSON.stringify(saved)); return false; }
}

function renderCal() {
    const grid = document.getElementById('calGrid');
    document.getElementById('calTitle').textContent = MOIS[calMonth-1] + ' ' + calYear;
    grid.innerHTML = '';
    const saved = getSaved();
    const today = new Date(); today.setHours(0,0,0,0);
    let startDow = new Date(calYear, calMonth-1, 1).getDay();
    startDow = startDow === 0 ? 6 : startDow - 1;
    const daysInMonth = new Date(calYear, calMonth, 0).getDate();
    const daysInPrev  = new Date(calYear, calMonth-1, 0).getDate();

    for (let i = startDow-1; i >= 0; i--) {
        const c = document.createElement('div'); c.className='cal-cell cal-other'; c.textContent=daysInPrev-i; grid.appendChild(c);
    }
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = calYear+'-'+String(calMonth).padStart(2,'0')+'-'+String(d).padStart(2,'0');
        const thisDate = new Date(calYear, calMonth-1, d); thisDate.setHours(0,0,0,0);
        let cls = 'cal-cell';
        if (thisDate.getTime()===today.getTime()) cls+=' cal-today';
        const isEv = calYear===EV_YEAR && calMonth===EV_MONTH && d===EV_DAY;
        if (isEv) cls+=' cal-event';
        if (saved.includes(dateStr)) cls+=' cal-saved';
        const c = document.createElement('div'); c.className=cls; c.textContent=d;
        if (isEv) {
            c.title='Cliquer pour enregistrer';
            c.onclick=()=>{ const added=saveDate(dateStr); renderCal(); showToast(added?'✅ "'+EV_TITLE+'" ajouté à votre agenda !':'🗑️ Événement retiré de votre agenda'); };
        }
        grid.appendChild(c);
    }
    const remaining = (startDow+daysInMonth)%7===0?0:7-(startDow+daysInMonth)%7;
    for (let i=1;i<=remaining;i++) { const c=document.createElement('div'); c.className='cal-cell cal-other'; c.textContent=i; grid.appendChild(c); }
}

function calPrev(){ calMonth--; if(calMonth<1){calMonth=12;calYear--;} renderCal(); }
function calNext(){ calMonth++; if(calMonth>12){calMonth=1;calYear++;} renderCal(); }

function showToast(msg) {
    const t=document.getElementById('toast'); document.getElementById('toastMsg').textContent=msg;
    t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),3000);
}

function copyLink() {
    const link = <?= json_encode($inscriptionUrl) ?>;
    navigator.clipboard.writeText(link).then(()=>showToast('🔗 Lien d\'inscription copié !')).catch(()=>{
        const el=document.createElement('textarea'); el.value=link; document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
        showToast('🔗 Lien d\'inscription copié !');
    });
}

document.addEventListener('DOMContentLoaded', renderCal);
</script>
</body>
</html>