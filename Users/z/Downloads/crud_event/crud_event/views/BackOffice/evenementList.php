<?php
include '../../controleurs/EvenementController.php';
require_once __DIR__ . '/../../models/Evenement.php';

$evenementController = new EvenementController();
$evenements          = $evenementController->listEvenements();

$totalActifs    = count(array_filter($evenements, fn($e) => $e->getStatut() === 'ACTIF'));
$totalCancelled = count(array_filter($evenements, fn($e) => $e->getStatut() === 'CANCELLED'));
$totalCompleted = count(array_filter($evenements, fn($e) => $e->getStatut() === 'COMPLETED'));
$totalPlaces    = array_sum(array_map(fn($e) => $e->getNbPlacesMax(), $evenements));

$placesSport     = array_sum(array_map(fn($e) => $e->getTypeEvenement()==='SPORT'     ? $e->getNbPlacesMax() : 0, $evenements));
$placesNutrition = array_sum(array_map(fn($e) => $e->getTypeEvenement()==='NUTRITION' ? $e->getNbPlacesMax() : 0, $evenements));
$placesWorkshop  = array_sum(array_map(fn($e) => $e->getTypeEvenement()==='WORKSHOP'  ? $e->getNbPlacesMax() : 0, $evenements));
$placesAutre     = array_sum(array_map(fn($e) => $e->getTypeEvenement()==='AUTRE'     ? $e->getNbPlacesMax() : 0, $evenements));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion des Événements - NutriLoop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
body{background:#f0f2f5;padding:20px;}
.container{max-width:1400px;margin:0 auto;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:15px;}
.header h1{font-size:1.8rem;color:#1a1a2e;}
.header h1 i{color:#4CAF50;margin-right:10px;}
.btn-group{display:flex;gap:12px;flex-wrap:wrap;}
.btn{padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;transition:.3s;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;font-family:'Segoe UI',sans-serif;font-size:14px;}
.btn-primary{background:#4CAF50;color:white;}.btn-primary:hover{background:#45a049;transform:translateY(-2px);}
.btn-secondary{background:#003366;color:white;}.btn-secondary:hover{background:#002244;transform:translateY(-2px);}
.btn-stats{background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;}
.btn-stats:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(33,150,243,.35);}
.stats-bar{display:flex;justify-content:space-between;align-items:center;background:white;padding:15px 25px;border-radius:12px;margin-bottom:20px;flex-wrap:wrap;gap:15px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.stats{display:flex;gap:25px;flex-wrap:wrap;}
.stat{display:flex;align-items:center;gap:8px;font-size:14px;}
.stat i{color:#4CAF50;}
.filter-panel{background:white;border-radius:16px;padding:20px 24px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);border-left:4px solid #2196F3;}
.fp-title{font-size:13px;font-weight:700;color:#1a1a2e;letter-spacing:.5px;margin-bottom:16px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #e0e0e0;padding-bottom:10px;}
.fp-title i{color:#2196F3;}
.fp-row{display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;}
.fp-col{display:flex;flex-direction:column;}
.fp-col-wide{flex:2;min-width:220px;}
.fp-col-auto{flex:1;min-width:155px;}
.fp-group-label{font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.8px;margin-bottom:7px;}
.fp-search{display:flex;align-items:center;gap:10px;background:#f5f5f5;border:1.5px solid #e0e0e0;border-radius:50px;padding:10px 18px;transition:all .3s;}
.fp-search:focus-within{border-color:#2196F3;background:#e3f2fd;}
.fp-search i{color:#757575;font-size:13px;}
.fp-search input{background:none;border:none;outline:none;color:#333;font-size:13px;width:100%;}
.fp-search input::placeholder{color:#aaa;}
.fp-pills{display:flex;flex-wrap:wrap;gap:8px;}
.pill{padding:8px 16px;border-radius:50px;font-size:12px;font-weight:600;cursor:pointer;user-select:none;border:2px solid #e0e0e0;color:#555;background:white;display:flex;align-items:center;gap:6px;transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.pill:hover{transform:translateY(-2px);border-color:#2196F3;color:#2196F3;}
.pill.active{transform:translateY(-2px);color:white;box-shadow:0 4px 12px rgba(0,0,0,.15);}
.pdot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.pill.sport.active{background:#1565c0;border-color:#1565c0;}
.pill.nutrition.active{background:#2e7d32;border-color:#2e7d32;}
.pill.workshop.active{background:#e65100;border-color:#e65100;}
.pill.autre.active{background:#6a1b9a;border-color:#6a1b9a;}
.fp-slider-wrap{flex:2;min-width:240px;}
.fp-slider-block{background:#f5f5f5;border:1.5px solid #e0e0e0;border-radius:14px;padding:14px 18px;}
.slider-inner{display:flex;align-items:center;gap:12px;margin-bottom:10px;}
.slider-lbl{font-size:12px;font-weight:600;color:#555;white-space:nowrap;min-width:68px;}
.fp-range{flex:1;-webkit-appearance:none;appearance:none;height:5px;border-radius:3px;outline:none;cursor:pointer;background:#e0e0e0;}
.fp-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#2196F3,#4CAF50);border:2.5px solid white;cursor:pointer;box-shadow:0 2px 8px rgba(33,150,243,.4);transition:transform .2s;}
.fp-range::-webkit-slider-thumb:hover{transform:scale(1.25);}
.fp-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#2196F3,#4CAF50);border:2.5px solid white;cursor:pointer;}
.slider-val{font-size:14px;font-weight:700;color:#2196F3;min-width:38px;text-align:right;}
.slider-ticks{display:flex;justify-content:space-between;padding:0 2px;}
.tick{display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer;}
.tick-line{width:2px;height:6px;background:#e0e0e0;border-radius:1px;transition:all .2s;}
.tick-lbl{font-size:9px;color:#aaa;font-weight:600;transition:color .2s;}
.tick.active .tick-line{background:#2196F3;height:10px;}
.tick.active .tick-lbl{color:#2196F3;}
.slider-date-display{text-align:center;margin-top:8px;font-size:11px;color:#757575;}
.slider-date-display strong{color:#1a1a2e;font-size:12px;}
.fp-footer{display:flex;justify-content:space-between;align-items:center;margin-top:14px;padding-top:12px;border-top:1px solid #e0e0e0;}
.fp-count{font-size:13px;color:#555;}
.fp-count span{color:#2196F3;font-weight:700;font-size:18px;}
.fp-reset{background:white;border:2px solid #e0e0e0;color:#555;padding:7px 16px;border-radius:50px;cursor:pointer;font-size:12px;font-family:'Segoe UI',sans-serif;transition:all .25s;display:flex;align-items:center;gap:6px;}
.fp-reset:hover{border-color:#f44336;color:#f44336;transform:translateY(-1px);}

/* TABLE */
.table-container{background:white;border-radius:16px;overflow:auto;box-shadow:0 2px 10px rgba(0,0,0,.05);}
table{width:100%;border-collapse:collapse;min-width:1200px;}
th{background:#1a1a2e;color:white;padding:15px 12px;text-align:left;font-weight:600;cursor:pointer;user-select:none;transition:background .2s;}
th:hover{background:#2d2d4e;}th i{margin-right:8px;}
th .sort-icon{float:right;opacity:.5;font-size:.75em;}th.sorted .sort-icon{opacity:1;color:#4CAF50;}
td{padding:12px;border-bottom:1px solid #eee;vertical-align:middle;}tr:hover{background:#f8f9fa;}
.id-cell{font-weight:700;color:#2196F3;}
.type-badge{display:inline-block;padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:600;}
.type-SPORT{background:#e3f2fd;color:#1565c0;}.type-NUTRITION{background:#e8f5e9;color:#2e7d32;}
.type-WORKSHOP{background:#fff3e0;color:#e65100;}.type-AUTRE{background:#f3e5f5;color:#6a1b9a;}
.statut-badge{display:inline-block;padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:600;}
.statut-ACTIF{background:#e8f5e9;color:#2e7d32;}.statut-CANCELLED{background:#ffebee;color:#c62828;}.statut-COMPLETED{background:#e8eaf6;color:#3949ab;}
.prix-badge{display:inline-block;padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:600;}
.prix-payant{background:#e3f2fd;color:#1565c0;}
.prix-gratuit{background:#e8f5e9;color:#2e7d32;}
.actions{display:flex;gap:8px;}
.action-btn{padding:6px 12px;border-radius:6px;text-decoration:none;font-size:.75rem;font-weight:600;transition:.2s;border:none;cursor:pointer;}
.view-btn{background:#e3f2fd;color:#1976d2;}.view-btn:hover{background:#bbdefb;transform:translateY(-2px);}
.edit-btn{background:#2196F3;color:white;}.edit-btn:hover{background:#1976D2;transform:translateY(-2px);}
.delete-btn{background:#dc3545;color:white;}.delete-btn:hover{background:#c82333;transform:translateY(-2px);}
.empty-message{text-align:center;padding:60px;color:#999;}
.empty-message i{font-size:3rem;display:block;margin-bottom:15px;color:#2196F3;}
.page-footer{margin-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
.pagination{display:flex;gap:8px;}
.page-btn{width:40px;height:40px;border:1px solid #ddd;background:white;border-radius:8px;cursor:pointer;transition:.2s;font-size:13px;font-weight:600;}
.page-btn.active{background:#4CAF50;color:white;border-color:#4CAF50;}
.page-btn:hover:not(.active){background:#f0f0f0;}
.pdf-btn{background:#c0392b;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-family:'Segoe UI',sans-serif;transition:.3s;font-size:14px;}
.pdf-btn:hover{background:#a93226;transform:translateY(-2px);}
.stats-modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;justify-content:center;align-items:center;}
.stats-modal-overlay.open{display:flex;animation:smFade .3s ease;}
@keyframes smFade{from{opacity:0}to{opacity:1}}
.stats-modal-box{background:white;border-radius:20px;width:92%;max-width:720px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.3);animation:smUp .4s cubic-bezier(.34,1.56,.64,1);}
@keyframes smUp{from{transform:translateY(50px);opacity:0}to{transform:translateY(0);opacity:1}}
.smb-header{display:flex;justify-content:space-between;align-items:center;background:#1a1a2e;padding:18px 24px;}
.smb-header h3{color:white;font-size:16px;font-weight:600;margin:0;display:flex;align-items:center;gap:10px;}
.smb-close{background:rgba(255,255,255,.15);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;transition:.2s;}
.smb-close:hover{background:rgba(255,255,255,.3);transform:scale(1.1);}
.smb-body{background:white;padding:24px 20px 20px;border-top:4px solid #2196F3;}
.smb-counter-row{display:flex;justify-content:center;gap:14px;margin-top:14px;flex-wrap:wrap;}
.smb-counter{text-align:center;background:#f5f5f5;border-radius:14px;padding:14px 20px;border-left:3px solid #2196F3;}
.smb-counter-val{font-size:28px;font-weight:700;color:#2196F3;}
.smb-counter-lbl{font-size:10px;color:#757575;text-transform:uppercase;letter-spacing:.8px;margin-top:3px;}
.smb-legend-row{display:flex;justify-content:center;gap:18px;margin-top:14px;flex-wrap:wrap;}
.smb-leg{display:flex;align-items:center;gap:6px;font-size:12px;color:#333;}
.smb-leg-sq{width:11px;height:11px;border-radius:2px;flex-shrink:0;}
</style>
</head>
<body>
<div class="container">

<div class="header">
  <h1><i class="fas fa-calendar-alt"></i> Gestion des Événements</h1>
  <div class="btn-group">
    <a href="addEvenement.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Ajouter un événement</a>
    <a href="participationList.php" class="btn btn-secondary"><i class="fas fa-users"></i> Participations</a>
    <button class="btn btn-stats" onclick="openStats()"><i class="fas fa-chart-bar"></i> Statistiques 3D</button>
  </div>
</div>

<div class="stats-bar">
  <div class="stats">
    <div class="stat"><i class="fas fa-calendar-check"></i><span><strong><?= count($evenements) ?></strong> événements</span></div>
    <div class="stat"><i class="fas fa-check-circle"></i><span>Actifs : <strong><?= $totalActifs ?></strong></span></div>
    <div class="stat"><i class="fas fa-ban"></i><span>Annulés : <strong><?= $totalCancelled ?></strong></span></div>
    <div class="stat"><i class="fas fa-ticket-alt"></i><span>Places : <strong><?= $totalPlaces ?></strong></span></div>
  </div>
</div>

<div class="filter-panel">
  <div class="fp-title"><i class="fas fa-filter"></i> Filtres &amp; Recherche</div>
  <div class="fp-row">
    <div class="fp-col fp-col-wide">
      <div class="fp-group-label">Recherche</div>
      <div class="fp-search">
        <i class="fas fa-search"></i>
        <input type="text" id="evSearch" placeholder="Titre, lieu..." oninput="applyFilters()">
      </div>
    </div>
    <div class="fp-col fp-col-auto">
      <div class="fp-group-label">Tri par type</div>
      <div class="fp-pills">
        <div class="pill sport"     onclick="togglePill(this,'SPORT')"    ><span class="pdot" style="background:#1565c0"></span><i class="fas fa-running" style="font-size:11px;"></i>Sport</div>
        <div class="pill nutrition" onclick="togglePill(this,'NUTRITION')" ><span class="pdot" style="background:#2e7d32"></span><i class="fas fa-apple-alt" style="font-size:11px;"></i>Nutrition</div>
        <div class="pill workshop"  onclick="togglePill(this,'WORKSHOP')"  ><span class="pdot" style="background:#e65100"></span><i class="fas fa-chalkboard-teacher" style="font-size:11px;"></i>Workshop</div>
        <div class="pill autre"     onclick="togglePill(this,'AUTRE')"     ><span class="pdot" style="background:#6a1b9a"></span><i class="fas fa-calendar" style="font-size:11px;"></i>Autre</div>
      </div>
    </div>
    <div class="fp-slider-wrap">
      <div class="fp-group-label">Date d'événement</div>
      <div class="fp-slider-block">
        <div class="slider-inner">
          <span class="slider-lbl">Jusqu'à</span>
          <input type="range" class="fp-range" id="sliderDate" min="0" max="12" value="12" step="1" oninput="onDateSlider(this)">
          <span class="slider-val" id="sliderDateVal">12</span>
        </div>
        <div class="slider-ticks" id="sliderTicks"></div>
        <div class="slider-date-display">Événements jusqu'au <strong id="sliderDateDisplay">—</strong></div>
      </div>
    </div>
  </div>
  <div class="fp-footer">
    <div class="fp-count">Résultats : <span id="evCount"><?= count($evenements) ?></span> événement(s)</div>
    <button class="fp-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Réinitialiser</button>
  </div>
</div>

<!-- TABLE -->
<div class="table-container">
  <table id="evenementsTable">
    <thead>
      <tr>
        <th onclick="sortTable(0)"><i class="fas fa-hashtag"></i> ID <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(1)"><i class="fas fa-heading"></i> Titre <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th><i class="fas fa-align-left"></i> Description</th>
        <th onclick="sortTable(3)"><i class="fas fa-tag"></i> Type <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(4)"><i class="fas fa-calendar-day"></i> Date <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(5)"><i class="fas fa-map-marker-alt"></i> Lieu <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(6)"><i class="fas fa-users"></i> Places <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(7)"><i class="fas fa-coins"></i> Prix <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(8)"><i class="fas fa-circle"></i> Statut <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th><i class="fas fa-cog"></i> Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (count($evenements) > 0): ?>
      <?php foreach ($evenements as $ev): ?>
      <tr data-titre="<?= strtolower(htmlspecialchars($ev->getTitre())) ?>"
          data-lieu="<?= strtolower(htmlspecialchars($ev->getLieu())) ?>"
          data-type="<?= $ev->getTypeEvenement() ?>"
          data-date="<?= $ev->getDateEvenement() ?>">
        <td class="id-cell">#<?= $ev->getIdEvenement() ?></td>
        <td><strong><?= htmlspecialchars($ev->getTitre()) ?></strong></td>
        <td style="max-width:180px;white-space:normal;word-wrap:break-word;font-size:13px;color:#666;">
          <?= htmlspecialchars(substr($ev->getDescription(),0,75)) ?><?php if(strlen($ev->getDescription())>75): ?>...<?php endif; ?>
        </td>
        <td>
          <?php $icons=['SPORT'=>'fa-running','NUTRITION'=>'fa-apple-alt','WORKSHOP'=>'fa-chalkboard-teacher','AUTRE'=>'fa-calendar']; ?>
          <span class="type-badge type-<?= $ev->getTypeEvenement() ?>">
            <i class="fas <?= $icons[$ev->getTypeEvenement()]??'fa-calendar' ?>"></i> <?= $ev->getTypeEvenement() ?>
          </span>
        </td>
        <td><?= date('d/m/Y',strtotime($ev->getDateEvenement())) ?></td>
        <td><?= htmlspecialchars($ev->getLieu()) ?></td>
        <td><strong><?= $ev->getNbPlacesMax() ?></strong></td>
        <td>
          <?php if ($ev->isPayant()): ?>
            <span class="prix-badge prix-payant">
              <i class="fas fa-credit-card"></i> <?= number_format($ev->getPrix(), 2) ?> TND
            </span>
          <?php else: ?>
            <span class="prix-badge prix-gratuit">
              <i class="fas fa-gift"></i> Gratuit
            </span>
          <?php endif; ?>
        </td>
        <td><span class="statut-badge statut-<?= $ev->getStatut() ?>"><?= $ev->getStatut() ?></span></td>
        <td>
          <div class="actions">
            <a href="viewEvenement.php?id=<?= $ev->getIdEvenement() ?>" class="action-btn view-btn"><i class="fas fa-eye"></i></a>
            <a href="editEvenement.php?id=<?= $ev->getIdEvenement() ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i></a>
            <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $ev->getIdEvenement() ?>);return false;"><i class="fas fa-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="10" class="empty-message"><i class="fas fa-calendar-times"></i><p>Aucun événement</p></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="page-footer">
  <div class="pagination" id="paginationEv"></div>
  <button class="pdf-btn" onclick="exportPDF()"><i class="fas fa-file-pdf"></i> Exporter PDF</button>
</div>
</div>

<!-- MODAL STATS 3D -->
<div class="stats-modal-overlay" id="statsModal" onclick="if(event.target===this)closeStats()">
  <div class="stats-modal-box">
    <div class="smb-header">
      <h3><i class="fas fa-chart-bar"></i> Statistiques — Places par type</h3>
      <button class="smb-close" onclick="closeStats()">×</button>
    </div>
    <div class="smb-body">
      <svg id="chart3dEv" viewBox="0 0 560 240" width="100%" role="img" aria-label="Barres 3D places par type">
        <title>Places par type d'événement</title>
        <defs>
          <linearGradient id="fSp" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#42A5F5"/><stop offset="100%" stop-color="#1565C0"/></linearGradient>
          <linearGradient id="sSp" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#1565C0"/><stop offset="100%" stop-color="#0D47A1"/></linearGradient>
          <linearGradient id="tSp" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#90CAF9"/><stop offset="100%" stop-color="#42A5F5"/></linearGradient>
          <linearGradient id="fNu" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#66BB6A"/><stop offset="100%" stop-color="#2E7D32"/></linearGradient>
          <linearGradient id="sNu" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#2E7D32"/><stop offset="100%" stop-color="#1B5E20"/></linearGradient>
          <linearGradient id="tNu" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#A5D6A7"/><stop offset="100%" stop-color="#66BB6A"/></linearGradient>
          <linearGradient id="fWo" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#FFA726"/><stop offset="100%" stop-color="#E65100"/></linearGradient>
          <linearGradient id="sWo" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#E65100"/><stop offset="100%" stop-color="#BF360C"/></linearGradient>
          <linearGradient id="tWo" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#FFCC80"/><stop offset="100%" stop-color="#FFA726"/></linearGradient>
          <linearGradient id="fAu" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#AB47BC"/><stop offset="100%" stop-color="#6A1B9A"/></linearGradient>
          <linearGradient id="sAu" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#6A1B9A"/><stop offset="100%" stop-color="#4A148C"/></linearGradient>
          <linearGradient id="tAu" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#CE93D8"/><stop offset="100%" stop-color="#AB47BC"/></linearGradient>
          <linearGradient id="shine" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="rgba(255,255,255,.5)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></linearGradient>
        </defs>
        <g stroke="#eee" stroke-width=".8">
          <line x1="45" y1="215" x2="510" y2="215"/>
          <line x1="45" y1="185" x2="510" y2="185" stroke-dasharray="4,3"/>
          <line x1="45" y1="155" x2="510" y2="155" stroke-dasharray="4,3"/>
          <line x1="45" y1="125" x2="510" y2="125" stroke-dasharray="4,3"/>
          <line x1="45" y1="95"  x2="510" y2="95"  stroke-dasharray="4,3"/>
        </g>
        <text x="40" y="218" font-size="9" fill="#bbb" text-anchor="end">0</text>
        <text x="40" y="188" font-size="9" fill="#bbb" text-anchor="end">30</text>
        <text x="40" y="158" font-size="9" fill="#bbb" text-anchor="end">60</text>
        <text x="40" y="128" font-size="9" fill="#bbb" text-anchor="end">90</text>
        <text x="40" y="98"  font-size="9" fill="#bbb" text-anchor="end">120</text>
        <?php
        $maxP=max($placesSport,$placesNutrition,$placesWorkshop,$placesAutre,1);
        $maxH=120;$baseY=215;$depth=16;
        $bars=[
          ['label'=>'Sport',    'val'=>$placesSport,    'x'=>80, 'fid'=>'fSp','sid'=>'sSp','tid'=>'tSp','txt'=>'#1565C0'],
          ['label'=>'Nutrition','val'=>$placesNutrition,'x'=>190,'fid'=>'fNu','sid'=>'sNu','tid'=>'tNu','txt'=>'#2E7D32'],
          ['label'=>'Workshop', 'val'=>$placesWorkshop, 'x'=>300,'fid'=>'fWo','sid'=>'sWo','tid'=>'tWo','txt'=>'#E65100'],
          ['label'=>'Autre',    'val'=>$placesAutre,    'x'=>410,'fid'=>'fAu','sid'=>'sAu','tid'=>'tAu','txt'=>'#6A1B9A'],
        ];
        foreach($bars as $i=>$b):
          $h=max(8,(int)round(($b['val']/$maxP)*$maxH));
          $y=$baseY-$h;$cx=$b['x']+27;$x2=$b['x']+55;
        ?>
        <g id="bar3d_<?=$i?>" style="cursor:pointer;transition:transform .3s,filter .3s;" transform="translate(0,<?=$h?>)">
          <rect x="<?=$b['x']?>" y="<?=$y?>" width="55" height="<?=$h?>" fill="url(#<?=$b['fid']?>)" rx="3"/>
          <polygon points="<?=$x2?>,<?=$y?> <?=$x2+$depth?>,<?=$y-$depth?> <?=$x2+$depth?>,<?=$baseY-$depth?> <?=$x2?>,<?=$baseY?>" fill="url(#<?=$b['sid']?>)" opacity=".9"/>
          <polygon points="<?=$b['x']?>,<?=$y?> <?=$b['x']+$depth?>,<?=$y-$depth?> <?=$x2+$depth?>,<?=$y-$depth?> <?=$x2?>,<?=$y?>" fill="url(#<?=$b['tid']?>)"/>
          <rect x="<?=$b['x']?>" y="<?=$y?>" width="55" height="<?=min(28,$h)?>" fill="url(#shine)" opacity=".45" rx="3"/>
          <text x="<?=$cx?>" y="<?=$y-$depth-6?>" font-size="14" fill="<?=$b['txt']?>" text-anchor="middle" font-weight="700"><?=$b['val']?></text>
          <text x="<?=$cx?>" y="<?=$y-$depth+6?>" font-size="9" fill="#999" text-anchor="middle">places</text>
        </g>
        <text x="<?=$cx?>" y="230" font-size="11" fill="#555" text-anchor="middle" font-weight="600"><?=$b['label']?></text>
        <?php endforeach; ?>
      </svg>
      <div class="smb-counter-row">
        <div class="smb-counter"><div class="smb-counter-val" id="cnt_0" style="color:#1565C0">0</div><div class="smb-counter-lbl">Sport</div></div>
        <div class="smb-counter"><div class="smb-counter-val" id="cnt_1" style="color:#2E7D32">0</div><div class="smb-counter-lbl">Nutrition</div></div>
        <div class="smb-counter"><div class="smb-counter-val" id="cnt_2" style="color:#E65100">0</div><div class="smb-counter-lbl">Workshop</div></div>
        <div class="smb-counter"><div class="smb-counter-val" id="cnt_3" style="color:#6A1B9A">0</div><div class="smb-counter-lbl">Autre</div></div>
      </div>
      <div class="smb-legend-row">
        <div class="smb-leg"><div class="smb-leg-sq" style="background:#42A5F5"></div>Sport</div>
        <div class="smb-leg"><div class="smb-leg-sq" style="background:#66BB6A"></div>Nutrition</div>
        <div class="smb-leg"><div class="smb-leg-sq" style="background:#FFA726"></div>Workshop</div>
        <div class="smb-leg"><div class="smb-leg-sq" style="background:#AB47BC"></div>Autre</div>
      </div>
    </div>
  </div>
</div>

<script>
const MOIS_FR=['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const MAX_MOIS=12;
let moisFilter=MAX_MOIS,typeFilter=null;

function buildTicks(){
  const c=document.getElementById('sliderTicks'),now=new Date();c.innerHTML='';
  for(let m=0;m<=MAX_MOIS;m++){
    const d=new Date(now.getFullYear(),now.getMonth()+m,1);
    const lbl=m===0?'Auj.':MOIS_FR[d.getMonth()];
    const t=document.createElement('div');t.className='tick'+(m===MAX_MOIS?' active':'');t.dataset.m=m;
    t.innerHTML=`<div class="tick-line"></div><div class="tick-lbl">${lbl}</div>`;
    t.addEventListener('click',()=>{document.getElementById('sliderDate').value=m;onDateSlider(document.getElementById('sliderDate'));});
    c.appendChild(t);
  }
}
function getEndDate(m){const now=new Date();return new Date(now.getFullYear(),now.getMonth()+m+1,0);}
function fmt(d){return d.toLocaleDateString('fr-FR',{day:'2-digit',month:'long',year:'numeric'});}
function onDateSlider(sl){
  moisFilter=parseInt(sl.value);
  document.getElementById('sliderDateVal').textContent=moisFilter;
  const pct=(moisFilter/MAX_MOIS)*100;
  sl.style.background=`linear-gradient(to right,#2196F3 0%,#4CAF50 ${pct}%,#e0e0e0 ${pct}%)`;
  document.getElementById('sliderDateDisplay').textContent=moisFilter===0?fmt(new Date())+" (aujourd'hui)":fmt(getEndDate(moisFilter));
  document.querySelectorAll('.tick').forEach(t=>t.classList.toggle('active',parseInt(t.dataset.m)===moisFilter));
  applyFilters();
}
function togglePill(el,val){
  el.closest('.fp-pills').querySelectorAll('.pill').forEach(p=>p.classList.remove('active'));
  const was=el.classList.contains('active');
  if(!was){el.classList.add('active');typeFilter=val;}else typeFilter=null;
  applyFilters();
}
function applyFilters(){
  const search=document.getElementById('evSearch').value.toLowerCase().trim();
  const now=new Date();now.setHours(0,0,0,0);
  const end=getEndDate(moisFilter);end.setHours(23,59,59,999);
  let count=0;
  document.querySelectorAll('#evenementsTable tbody tr').forEach(row=>{
    const titre=row.dataset.titre||'',lieu=row.dataset.lieu||'',type=row.dataset.type||'';
    const date=row.dataset.date?new Date(row.dataset.date):null;
    let ok=true;
    if(search&&!titre.includes(search)&&!lieu.includes(search))ok=false;
    if(typeFilter&&type!==typeFilter)ok=false;
    if(date&&(date<now||date>end))ok=false;
    row.style.display=ok?'':'none';if(ok)count++;
  });
  document.getElementById('evCount').textContent=count;showPage(1);
}
function resetFilters(){
  document.getElementById('evSearch').value='';typeFilter=null;
  document.querySelectorAll('.filter-panel .pill').forEach(p=>p.classList.remove('active'));
  moisFilter=MAX_MOIS;const sl=document.getElementById('sliderDate');sl.value=MAX_MOIS;
  sl.style.background=`linear-gradient(to right,#2196F3 0%,#4CAF50 100%,#e0e0e0 100%)`;
  document.getElementById('sliderDateVal').textContent=MAX_MOIS;
  document.getElementById('sliderDateDisplay').textContent=fmt(getEndDate(MAX_MOIS));
  document.querySelectorAll('.tick').forEach(t=>t.classList.toggle('active',parseInt(t.dataset.m)===MAX_MOIS));
  applyFilters();
}
let sortDir={};
function sortTable(col){
  const tbl=document.getElementById('evenementsTable');const rows=Array.from(tbl.querySelectorAll('tbody tr'));const ths=tbl.querySelectorAll('th');
  sortDir[col]=!sortDir[col];
  rows.sort((a,b)=>{const av=a.cells[col]?.textContent.trim()||'';const bv=b.cells[col]?.textContent.trim()||'';return sortDir[col]?av.localeCompare(bv,'fr',{numeric:true}):bv.localeCompare(av,'fr',{numeric:true});});
  tbl.querySelector('tbody').append(...rows);
  ths.forEach(t=>{t.classList.remove('sorted');const ic=t.querySelector('.sort-icon i');if(ic)ic.className='fas fa-sort';});
  ths[col].classList.add('sorted');const ic=ths[col].querySelector('.sort-icon i');if(ic)ic.className=sortDir[col]?'fas fa-sort-up':'fas fa-sort-down';showPage(1);
}
let currentPage=1;const rowsPerPage=10;
function showPage(page){
  const vis=Array.from(document.querySelectorAll('#evenementsTable tbody tr')).filter(r=>r.style.display!=='none');
  const total=Math.ceil(vis.length/rowsPerPage)||1;
  if(page<1)page=1;if(page>total)page=total;
  vis.forEach((r,i)=>r.style.display=(i>=(page-1)*rowsPerPage&&i<page*rowsPerPage)?'':'none');
  currentPage=page;
  const pg=document.getElementById('paginationEv');pg.innerHTML='';
  const mkBtn=(lbl,p,isActive,disabled)=>{const b=document.createElement('button');b.className='page-btn'+(isActive?' active':'');b.textContent=lbl;b.disabled=disabled;b.onclick=()=>showPage(p);pg.appendChild(b);};
  mkBtn('«',1,false,page===1);mkBtn('‹',page-1,false,page===1);
  const start=Math.max(1,page-2),end=Math.min(total,page+2);
  for(let i=start;i<=end;i++)mkBtn(i,i,i===page,false);
  mkBtn('›',page+1,false,page===total);mkBtn('»',total,false,page===total);
}
function confirmDelete(id){if(confirm('Supprimer cet événement ?'))window.location.href='deleteEvenement.php?id='+id+'&confirm=yes';}
const EV_VALS=[<?=$placesSport?>,<?=$placesNutrition?>,<?=$placesWorkshop?>,<?=$placesAutre?>];
let statsOpened=false;
function animCount(el,t){let v=0;const x=setInterval(()=>{v+=t/60;if(v>=t){v=t;clearInterval(x);}el.textContent=Math.round(v);},1000/60);}
function openStats(){
  document.getElementById('statsModal').classList.add('open');
  if(statsOpened)return;statsOpened=true;
  ['bar3d_0','bar3d_1','bar3d_2','bar3d_3'].forEach((id,i)=>{
    const g=document.getElementById(id);g.style.opacity='0';
    setTimeout(()=>{g.style.transition='opacity .5s ease,transform 1s cubic-bezier(.34,1.56,.64,1)';g.style.opacity='1';g.style.transform='translate(0,0)';
    animCount(document.getElementById('cnt_'+i),EV_VALS[i]);
    g.onmouseenter=()=>{g.style.filter='brightness(1.12)';g.style.transform='translate(0,-8px)';};
    g.onmouseleave=()=>{g.style.filter='';g.style.transform='translate(0,0)';};},i*230);
  });
}
function closeStats(){document.getElementById('statsModal').classList.remove('open');}
function exportPDF(){
  const {jsPDF}=window.jspdf;
  const doc=new jsPDF({orientation:'landscape',unit:'mm',format:'a4'});
  const pW=doc.internal.pageSize.getWidth(),pH=doc.internal.pageSize.getHeight();
  const now=new Date().toLocaleDateString('fr-FR',{day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});
  function drawPageHeader(pageNum){
    doc.setFillColor(26,26,46);doc.rect(0,0,pW,22,'F');
    doc.setTextColor(255,255,255);doc.setFontSize(16);doc.setFont('helvetica','bold');doc.text('NutriLoop',12,13);
    doc.setFontSize(12);doc.setFont('helvetica','normal');doc.text('Rapport des Événements',pW/2,13,{align:'center'});
    doc.setFontSize(9);doc.text('Généré le : '+now+'   |   Page '+pageNum,pW-12,13,{align:'right'});
  }
  function drawKPIs(){
    const kpis=[{l:'Total',v:'<?=count($evenements)?>',r:76,g:175,b:80},{l:'Actifs',v:'<?=$totalActifs?>',r:33,g:150,b:243},{l:'Annulés',v:'<?=$totalCancelled?>',r:244,g:67,b:54},{l:'Terminés',v:'<?=$totalCompleted?>',r:156,g:39,b:176},{l:'Places',v:'<?=$totalPlaces?>',r:255,g:152,b:0}];
    let sx=12;kpis.forEach(k=>{doc.setFillColor(k.r,k.g,k.b);doc.roundedRect(sx,27,52,16,3,3,'F');doc.setTextColor(255,255,255);doc.setFontSize(13);doc.setFont('helvetica','bold');doc.text(k.v,sx+26,35,{align:'center'});doc.setFontSize(7);doc.setFont('helvetica','normal');doc.text(k.l,sx+26,40,{align:'center'});sx+=56;});
  }
  function drawTableHeader(y){
    const hd=['ID','Titre','Type','Date','Lieu','Places','Prix','Statut'],cw=[12,55,26,23,40,16,22,24];
    doc.setFillColor(26,26,46);doc.rect(12,y,pW-24,9,'F');
    doc.setTextColor(255,255,255);doc.setFontSize(8);doc.setFont('helvetica','bold');
    let x=14;hd.forEach((h,i)=>{doc.text(h,x,y+6);x+=cw[i];});
    return {y:y+9,hd,cw};
  }
  let pageNum=1;drawPageHeader(pageNum);drawKPIs();
  let {y,hd,cw}=drawTableHeader(50);let rc=0;
  document.querySelectorAll('#evenementsTable tbody tr').forEach(row=>{
    if(row.style.display==='none')return;
    const cells=row.querySelectorAll('td');if(!cells.length)return;
    if(y>pH-18){
      doc.setFillColor(240,242,245);doc.rect(0,pH-10,pW,10,'F');
      doc.setTextColor(120,120,120);doc.setFontSize(7);doc.setFont('helvetica','italic');
      doc.text('NutriLoop',12,pH-4);doc.text('Page '+pageNum,pW-12,pH-4,{align:'right'});
      doc.addPage();pageNum++;drawPageHeader(pageNum);
      const next=drawTableHeader(15);y=next.y;
    }
    if(rc%2===0){doc.setFillColor(248,249,250);doc.rect(12,y,pW-24,9,'F');}
    doc.setDrawColor(220,220,220);doc.line(12,y+9,pW-12,y+9);
    const vals=[
      cells[0]?.innerText.replace('#','').trim()||'',
      cells[1]?.innerText.trim()||'',
      cells[3]?.innerText.trim()||'',
      cells[4]?.innerText.trim()||'',
      cells[5]?.innerText.trim()||'',
      cells[6]?.innerText.trim()||'',
      cells[7]?.innerText.trim()||'',
      cells[8]?.innerText.trim()||''
    ];
    let x=14;vals.forEach((v,i)=>{
      if(i===0){doc.setTextColor(33,150,243);doc.setFont('helvetica','bold');}
      else if(i===6){doc.setTextColor(v==='Gratuit'?46:21,v==='Gratuit'?125:101,v==='Gratuit'?50:192);doc.setFont('helvetica','bold');}
      else if(i===7){if(v==='ACTIF')doc.setTextColor(46,125,50);else if(v==='CANCELLED')doc.setTextColor(198,40,40);else doc.setTextColor(57,73,171);doc.setFont('helvetica','bold');}
      else{doc.setTextColor(50,50,50);doc.setFont('helvetica','normal');}
      let t=v;while(doc.getTextWidth(t)>cw[i]-2&&t.length>3)t=t.slice(0,-4)+'...';
      doc.text(t,x,y+6);x+=cw[i];
    });
    y+=9;rc++;
  });
  doc.setFillColor(240,242,245);doc.rect(0,pH-10,pW,10,'F');
  doc.setTextColor(120,120,120);doc.setFontSize(7);doc.setFont('helvetica','italic');
  doc.text('NutriLoop — Plateforme intelligente pour une alimentation durable',12,pH-4);
  doc.text('Page '+pageNum+' — Total : '+rc+' événement(s)',pW-12,pH-4,{align:'right'});
  doc.save('evenements_nutriloop.pdf');
}
document.addEventListener('DOMContentLoaded',()=>{
  buildTicks();
  const sl=document.getElementById('sliderDate');
  sl.style.background=`linear-gradient(to right,#2196F3 0%,#4CAF50 100%,#e0e0e0 100%)`;
  document.getElementById('sliderDateDisplay').textContent=fmt(getEndDate(MAX_MOIS));
  showPage(1);
});
</script>
</body>
</html>