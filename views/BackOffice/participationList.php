<?php
require_once '../../controleurs/ParticipationController.php';

$participationController = new ParticipationController();
$participations          = $participationController->listParticipations();

usort($participations, function($a,$b){
  return strtotime($b->getDateInscription()) - strtotime($a->getDateInscription());
});

$totalPlaces = array_sum(array_map(fn($p) => $p->getNbPlacesReservees() ?? 1, $participations));
$maxPlaces   = count($participations) > 0 ? max(array_map(fn($p) => $p->getNbPlacesReservees() ?? 1, $participations)) : 10;

// Stats par événement
$statsParEvent = [];
foreach ($participations as $p) {
    $titre = trim($p->getEvenementTitre() ?: 'N/A');
    if (!isset($statsParEvent[$titre])) $statsParEvent[$titre] = 0;
    $statsParEvent[$titre]++;
}
arsort($statsParEvent);
$topEvents  = array_slice($statsParEvent, 0, 4, true);
$maxEvUsers = count($topEvents) > 0 ? max(array_values($topEvents)) : 1;

$barColors = [
    ['f0'=>'#42A5F5','f1'=>'#1565C0','s0'=>'#1565C0','s1'=>'#0D47A1','t0'=>'#90CAF9','t1'=>'#42A5F5','txt'=>'#1565C0','leg'=>'#42A5F5','pill'=>'#1565c0'],
    ['f0'=>'#66BB6A','f1'=>'#2E7D32','s0'=>'#2E7D32','s1'=>'#1B5E20','t0'=>'#A5D6A7','t1'=>'#66BB6A','txt'=>'#2E7D32','leg'=>'#66BB6A','pill'=>'#2e7d32'],
    ['f0'=>'#FFA726','f1'=>'#E65100','s0'=>'#E65100','s1'=>'#BF360C','t0'=>'#FFCC80','t1'=>'#FFA726','txt'=>'#E65100','leg'=>'#FFA726','pill'=>'#e65100'],
    ['f0'=>'#AB47BC','f1'=>'#6A1B9A','s0'=>'#6A1B9A','s1'=>'#4A148C','t0'=>'#CE93D8','t1'=>'#AB47BC','txt'=>'#6A1B9A','leg'=>'#AB47BC','pill'=>'#6a1b9a'],
];
$xPos = [80, 190, 300, 410];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion des Participations - NutriLoop</title>
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
.btn-stats{background:linear-gradient(135deg,#4CAF50,#2196F3);color:white;}
.btn-stats:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(76,175,80,.35);}
.stats-bar{display:flex;background:white;padding:15px 25px;border-radius:12px;margin-bottom:20px;flex-wrap:wrap;gap:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.stat{display:flex;align-items:center;gap:8px;font-size:14px;}
.stat i{color:#4CAF50;}
.filter-panel{background:white;border-radius:16px;padding:20px 24px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);border-left:4px solid #4CAF50;}
.fp-title{font-size:13px;font-weight:700;color:#1a1a2e;margin-bottom:16px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #e0e0e0;padding-bottom:10px;}
.fp-title i{color:#4CAF50;}
.fp-row{display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;}
.fp-col{display:flex;flex-direction:column;}
.fp-col-wide{flex:2;min-width:220px;}
.fp-col-auto{flex:1;min-width:180px;}
.fp-group-label{font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.8px;margin-bottom:7px;}
.fp-search{display:flex;align-items:center;gap:10px;background:#f5f5f5;border:1.5px solid #e0e0e0;border-radius:50px;padding:10px 18px;transition:all .3s;}
.fp-search:focus-within{border-color:#4CAF50;background:#e8f5e9;}
.fp-search i{color:#757575;font-size:13px;}
.fp-search input{background:none;border:none;outline:none;color:#333;font-size:13px;width:100%;}
.fp-search input::placeholder{color:#aaa;}
.fp-pills{display:flex;flex-wrap:wrap;gap:8px;}
.pill{padding:8px 15px;border-radius:50px;font-size:12px;font-weight:600;cursor:pointer;user-select:none;border:2px solid #e0e0e0;color:#555;background:white;display:flex;align-items:center;gap:6px;transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.pill:hover{transform:translateY(-2px);}
.pill.active{transform:translateY(-2px);color:white;box-shadow:0 4px 12px rgba(0,0,0,.2);}
.pdot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.fp-slider-wrap{flex:2;min-width:240px;}
.fp-slider-block{background:#f5f5f5;border:1.5px solid #e0e0e0;border-radius:14px;padding:14px 18px;}
.slider-inner{display:flex;align-items:center;gap:12px;}
.slider-lbl{font-size:12px;font-weight:600;color:#555;white-space:nowrap;min-width:80px;}
.fp-range{flex:1;-webkit-appearance:none;appearance:none;height:5px;border-radius:3px;outline:none;cursor:pointer;background:#e0e0e0;}
.fp-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#4CAF50,#2196F3);border:2.5px solid white;cursor:pointer;box-shadow:0 2px 8px rgba(76,175,80,.4);transition:transform .2s;}
.fp-range::-webkit-slider-thumb:hover{transform:scale(1.25);}
.fp-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#4CAF50,#2196F3);border:2.5px solid white;cursor:pointer;}
.slider-val{font-size:14px;font-weight:700;color:#4CAF50;min-width:55px;text-align:right;}
.fp-footer{display:flex;justify-content:space-between;align-items:center;margin-top:14px;padding-top:12px;border-top:1px solid #e0e0e0;}
.fp-count{font-size:13px;color:#555;}
.fp-count span{color:#4CAF50;font-weight:700;font-size:18px;}
.fp-reset{background:white;border:2px solid #e0e0e0;color:#555;padding:7px 16px;border-radius:50px;cursor:pointer;font-size:12px;font-family:'Segoe UI',sans-serif;transition:all .25s;display:flex;align-items:center;gap:6px;}
.fp-reset:hover{border-color:#f44336;color:#f44336;transform:translateY(-1px);}
.table-container{background:white;border-radius:16px;overflow:auto;box-shadow:0 2px 10px rgba(0,0,0,.05);}
table{width:100%;border-collapse:collapse;min-width:900px;}
th{background:#1a1a2e;color:white;padding:15px 12px;text-align:left;font-weight:600;cursor:pointer;user-select:none;transition:background .2s;}
th:hover{background:#2d2d4e;}th i{margin-right:8px;}
th .sort-icon{float:right;opacity:.5;font-size:.75em;}th.sorted .sort-icon{opacity:1;color:#4CAF50;}
td{padding:12px;border-bottom:1px solid #eee;vertical-align:middle;}tr:hover{background:#f8f9fa;}
.id-cell{font-weight:700;color:#2196F3;}
.event-link{color:#2196F3;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
.event-link:hover{text-decoration:underline;}
.actions{display:flex;gap:8px;}
.action-btn{padding:6px 12px;border-radius:6px;text-decoration:none;font-size:.75rem;font-weight:600;transition:.2s;border:none;cursor:pointer;}
.edit-btn{background:#2196F3;color:white;}.edit-btn:hover{background:#1976D2;transform:translateY(-2px);}
.delete-btn{background:#dc3545;color:white;}.delete-btn:hover{background:#c82333;transform:translateY(-2px);}
.empty-message{text-align:center;padding:60px;color:#999;}
.empty-message i{font-size:3rem;display:block;margin-bottom:15px;color:#4CAF50;}
.page-footer{margin-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
.pagination{display:flex;gap:8px;flex-wrap:wrap;}
.page-btn{min-width:40px;height:40px;padding:0 10px;border:1px solid #ddd;background:white;border-radius:8px;cursor:pointer;transition:.2s;font-size:13px;font-weight:600;}
.page-btn.active{background:#4CAF50;color:white;border-color:#4CAF50;}
.page-btn:hover:not(.active):not(:disabled){background:#f0f0f0;}
.page-btn:disabled{opacity:.35;cursor:default;}
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
.smb-body{background:white;padding:24px 20px 20px;border-top:4px solid #4CAF50;}
.smb-counter-row{display:flex;justify-content:center;gap:14px;margin-top:14px;flex-wrap:wrap;}
.smb-counter{text-align:center;background:#f5f5f5;border-radius:14px;padding:14px 20px;border-left:3px solid #4CAF50;}
.smb-counter-val{font-size:28px;font-weight:700;color:#4CAF50;}
.smb-counter-lbl{font-size:10px;color:#757575;text-transform:uppercase;letter-spacing:.8px;margin-top:3px;max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.smb-legend-row{display:flex;justify-content:center;gap:18px;margin-top:14px;flex-wrap:wrap;}
.smb-leg{display:flex;align-items:center;gap:6px;font-size:12px;color:#333;}
.smb-leg-sq{width:11px;height:11px;border-radius:2px;flex-shrink:0;}
</style>
</head>
<body>
<div class="container">

<div class="header">
  <h1><i class="fas fa-users"></i> Gestion des Participations</h1>
  <div class="btn-group">
    <a href="addParticipation.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Ajouter une participation</a>
    <a href="evenementList.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour aux événements</a>
    <button class="btn btn-stats" onclick="openStats()"><i class="fas fa-chart-bar"></i> Statistiques 3D</button>
  </div>
</div>

<div class="stats-bar">
  <div class="stat"><i class="fas fa-users"></i><span><strong><?= count($participations) ?></strong> participations</span></div>
  <div class="stat"><i class="fas fa-ticket-alt"></i><span>Places réservées : <strong><?= $totalPlaces ?></strong></span></div>
</div>

<div class="filter-panel">
  <div class="fp-title"><i class="fas fa-filter"></i> Filtres &amp; Recherche</div>
  <div class="fp-row">

    <!-- Recherche -->
    <div class="fp-col fp-col-wide">
      <div class="fp-group-label">Recherche</div>
      <div class="fp-search">
        <i class="fas fa-search"></i>
        <input type="text" id="partSearch" placeholder="Nom, email, événement..." oninput="applyFilters()">
      </div>
    </div>

    <!-- Pills par événement -->
    <div class="fp-col fp-col-auto">
      <div class="fp-group-label">Tri par événement</div>
      <div class="fp-pills" id="eventPills">
        <?php
        $pillColors = ['#1565c0','#2e7d32','#e65100','#6a1b9a'];
        $pi = 0;
        foreach ($statsParEvent as $titre => $cnt):
            $c = $pillColors[$pi % count($pillColors)];
            $lbl = mb_substr($titre, 0, 20) . (mb_strlen($titre) > 20 ? '…' : '');
        ?>
        <div class="pill"
             data-event="<?= htmlspecialchars($titre) ?>"
             data-color="<?= $c ?>"
             style="--pc:<?= $c ?>;"
             onclick="toggleEventPill(this)">
            <span class="pdot" style="background:<?= $c ?>"></span>
            <?= htmlspecialchars($lbl) ?>
        </div>
        <?php $pi++; endforeach; ?>
      </div>
    </div>

    <!-- Slider places -->
    <div class="fp-slider-wrap">
      <div class="fp-group-label">Filtrer par nb de places réservées</div>
      <div class="fp-slider-block">
        <div class="slider-inner">
          <span class="slider-lbl">Min places</span>
          <input type="range" class="fp-range" id="sliderPlaces" min="0" max="<?= $maxPlaces ?>" value="0" step="1" oninput="onPlacesSlider(this)">
          <span class="slider-val" id="sliderPlacesVal">Tous</span>
        </div>
      </div>
    </div>

  </div>
  <div class="fp-footer">
    <div class="fp-count">Résultats : <span id="partCount"><?= count($participations) ?></span> participation(s)</div>
    <button class="fp-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Réinitialiser</button>
  </div>
</div>

<!-- TABLE -->
<div class="table-container">
  <table id="participationsTable">
    <thead>
      <tr>
        <th onclick="sortTable(0)"><i class="fas fa-hashtag"></i> ID <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(1)"><i class="fas fa-calendar-alt"></i> Événement <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(2)"><i class="fas fa-user"></i> Nom <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(3)"><i class="fas fa-envelope"></i> Email <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th><i class="fas fa-phone"></i> Téléphone</th>
        <th onclick="sortTable(5)"><i class="fas fa-ticket-alt"></i> Places <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th onclick="sortTable(6)"><i class="fas fa-calendar-check"></i> Date inscription <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
        <th><i class="fas fa-cog"></i> Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (count($participations) > 0): ?>
      <?php foreach ($participations as $p): ?>
      <tr data-places="<?= $p->getNbPlacesReservees() ?? 1 ?>"
          data-nom="<?= strtolower(htmlspecialchars($p->getNom()??'')) ?>"
          data-email="<?= strtolower(htmlspecialchars($p->getEmail()??'')) ?>"
          data-event="<?= strtolower(htmlspecialchars($p->getEvenementTitre()??'')) ?>">
        <td class="id-cell">#<?= $p->getIdParticipation() ?></td>
        <td><a href="viewEvenement.php?id=<?= $p->getIdEvenement() ?>" class="event-link"><i class="fas fa-calendar-alt"></i><?= htmlspecialchars($p->getEvenementTitre() ?: 'N/A') ?></a></td>
        <td><strong><?= htmlspecialchars($p->getNom() ?: '—') ?></strong></td>
        <td><?= htmlspecialchars($p->getEmail() ?: '—') ?></td>
        <td><?= htmlspecialchars($p->getTelephone() ?: '—') ?></td>
        <td><strong><?= $p->getNbPlacesReservees() ?? 1 ?></strong></td>
        <td><?= $p->getDateInscription() ? date('d/m/Y H:i',strtotime($p->getDateInscription())) : '—' ?></td>
        <td>
          <div class="actions">
            <a href="editParticipation.php?id=<?= $p->getIdParticipation() ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i></a>
            <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $p->getIdParticipation() ?>);return false;"><i class="fas fa-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="8" class="empty-message"><i class="fas fa-users"></i><p>Aucune participation trouvée</p></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="page-footer">
  <div class="pagination" id="paginationPart"></div>
  <button class="pdf-btn" onclick="exportPDF()"><i class="fas fa-file-pdf"></i> Exporter PDF</button>
</div>
</div>

<!-- MODAL STATS 3D -->
<div class="stats-modal-overlay" id="statsModal" onclick="if(event.target===this)closeStats()">
  <div class="stats-modal-box">
    <div class="smb-header">
      <h3><i class="fas fa-chart-bar"></i> Statistiques — Participants par événement</h3>
      <button class="smb-close" onclick="closeStats()">×</button>
    </div>
    <div class="smb-body">
      <svg id="chart3dPart" viewBox="0 0 560 240" width="100%" role="img">
        <defs>
          <?php for($i=0;$i<count($topEvents);$i++): $c=$barColors[$i]; ?>
          <linearGradient id="fEv<?=$i?>" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="<?=$c['f0']?>"/><stop offset="100%" stop-color="<?=$c['f1']?>"/></linearGradient>
          <linearGradient id="sEv<?=$i?>" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="<?=$c['s0']?>"/><stop offset="100%" stop-color="<?=$c['s1']?>"/></linearGradient>
          <linearGradient id="tEv<?=$i?>" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="<?=$c['t0']?>"/><stop offset="100%" stop-color="<?=$c['t1']?>"/></linearGradient>
          <?php endfor; ?>
          <linearGradient id="shP" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="rgba(255,255,255,.5)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></linearGradient>
        </defs>
        <g stroke="#eee" stroke-width=".8">
          <line x1="45" y1="215" x2="510" y2="215"/>
          <line x1="45" y1="185" x2="510" y2="185" stroke-dasharray="4,3"/>
          <line x1="45" y1="155" x2="510" y2="155" stroke-dasharray="4,3"/>
          <line x1="45" y1="125" x2="510" y2="125" stroke-dasharray="4,3"/>
          <line x1="45" y1="95"  x2="510" y2="95"  stroke-dasharray="4,3"/>
        </g>
        <?php
        $mH=120;$bY=215;$dp=16;
        $step=round($maxEvUsers/4,1);
        for($li=0;$li<=4;$li++){$yp=$bY-($li*$mH/4);echo "<text x='40' y='".($yp+3)."' font-size='9' fill='#bbb' text-anchor='end'>".round($li*$step)."</text>";}
        $i=0;
        foreach($topEvents as $titre=>$val):
            $c=$barColors[$i];$x=$xPos[$i];
            $h=max(8,(int)round(($val/$maxEvUsers)*$mH));
            $y=$bY-$h;$cx=$x+27;$x2=$x+55;
            $lbl=mb_substr($titre,0,10).(mb_strlen($titre)>10?'…':'');
        ?>
        <g id="pbar_<?=$i?>" style="cursor:pointer;transition:transform .3s,filter .3s;" transform="translate(0,<?=$h?>)">
          <rect x="<?=$x?>" y="<?=$y?>" width="55" height="<?=$h?>" fill="url(#fEv<?=$i?>)" rx="3"/>
          <polygon points="<?=$x2?>,<?=$y?> <?=$x2+$dp?>,<?=$y-$dp?> <?=$x2+$dp?>,<?=$bY-$dp?> <?=$x2?>,<?=$bY?>" fill="url(#sEv<?=$i?>)" opacity=".9"/>
          <polygon points="<?=$x?>,<?=$y?> <?=$x+$dp?>,<?=$y-$dp?> <?=$x2+$dp?>,<?=$y-$dp?> <?=$x2?>,<?=$y?>" fill="url(#tEv<?=$i?>)"/>
          <rect x="<?=$x?>" y="<?=$y?>" width="55" height="<?=min(28,$h)?>" fill="url(#shP)" opacity=".45" rx="3"/>
          <text x="<?=$cx?>" y="<?=$y-$dp-6?>" font-size="14" fill="<?=$c['txt']?>" text-anchor="middle" font-weight="700"><?=$val?></text>
          <text x="<?=$cx?>" y="<?=$y-$dp+6?>" font-size="9" fill="#999" text-anchor="middle">participants</text>
        </g>
        <text x="<?=$cx?>" y="230" font-size="10" fill="#555" text-anchor="middle" font-weight="600"><?=htmlspecialchars($lbl)?></text>
        <?php $i++; endforeach; ?>
      </svg>
      <div class="smb-counter-row">
        <?php for($i=0;$i<count($topEvents);$i++): $c=$barColors[$i];$titre=array_keys($topEvents)[$i]; ?>
        <div class="smb-counter" style="border-color:<?=$c['txt']?>">
          <div class="smb-counter-val" id="pcnt_<?=$i?>" style="color:<?=$c['txt']?>">0</div>
          <div class="smb-counter-lbl" title="<?=htmlspecialchars($titre)?>"><?=htmlspecialchars(mb_substr($titre,0,12)).(mb_strlen($titre)>12?'…':'')?></div>
        </div>
        <?php endfor; ?>
      </div>
      <div class="smb-legend-row">
        <?php for($i=0;$i<count($topEvents);$i++): $c=$barColors[$i];$titre=array_keys($topEvents)[$i]; ?>
        <div class="smb-leg"><div class="smb-leg-sq" style="background:<?=$c['leg']?>"></div><?=htmlspecialchars(mb_substr($titre,0,16)).(mb_strlen($titre)>16?'…':'')?></div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</div>

<script>
let eventFilter = null;
let placesMin   = 0;

function toggleEventPill(el) {
    const allPills = document.querySelectorAll('#eventPills .pill');
    const was = el.classList.contains('active');
    // Désactiver tous
    allPills.forEach(p => {
        p.classList.remove('active');
        p.style.background = 'white';
        p.style.color = '#555';
        p.style.borderColor = '#e0e0e0';
    });
    if (!was) {
        // Activer celui cliqué
        el.classList.add('active');
        const c = el.dataset.color;
        el.style.background = c;
        el.style.color = 'white';
        el.style.borderColor = c;
        eventFilter = el.dataset.event.toLowerCase();
    } else {
        eventFilter = null;
    }
    applyFilters();
}

function onPlacesSlider(sl) {
    placesMin = parseInt(sl.value);
    const lbl = placesMin === 0 ? 'Tous' : '> ' + placesMin + ' place' + (placesMin > 1 ? 's' : '');
    document.getElementById('sliderPlacesVal').textContent = lbl;
    const pct = (placesMin / <?= $maxPlaces ?>) * 100;
    sl.style.background = `linear-gradient(to right,#4CAF50 0%,#2196F3 ${pct}%,#e0e0e0 ${pct}%)`;
    applyFilters();
}

function applyFilters() {
    const search = document.getElementById('partSearch').value.toLowerCase().trim();
    let count = 0;
    document.querySelectorAll('#participationsTable tbody tr').forEach(row => {
        const nom   = row.dataset.nom   || '';
        const email = row.dataset.email || '';
        const event = row.dataset.event || '';
        const places = parseInt(row.dataset.places) || 1;
        let ok = true;
        if (search && !nom.includes(search) && !email.includes(search) && !event.includes(search)) ok = false;
        if (eventFilter && !event.includes(eventFilter)) ok = false;
        if (placesMin > 0 && places <= placesMin) ok = false;
        row.style.display = ok ? '' : 'none';
        if (ok) count++;
    });
    document.getElementById('partCount').textContent = count;
    showPage(1);
}

function resetFilters() {
    document.getElementById('partSearch').value = '';
    eventFilter = null;
    document.querySelectorAll('#eventPills .pill').forEach(p => {
        p.classList.remove('active');
        p.style.background = 'white';
        p.style.color = '#555';
        p.style.borderColor = '#e0e0e0';
    });
    placesMin = 0;
    const sl = document.getElementById('sliderPlaces');
    sl.value = 0;
    document.getElementById('sliderPlacesVal').textContent = 'Tous';
    sl.style.background = '#e0e0e0';
    applyFilters();
}

let sortDir = {};
function sortTable(col) {
    const tbl  = document.getElementById('participationsTable');
    const rows = Array.from(tbl.querySelectorAll('tbody tr'));
    const ths  = tbl.querySelectorAll('th');
    sortDir[col] = !sortDir[col];
    rows.sort((a,b) => {
        const av = a.cells[col]?.textContent.trim() || '';
        const bv = b.cells[col]?.textContent.trim() || '';
        return sortDir[col] ? av.localeCompare(bv,'fr',{numeric:true}) : bv.localeCompare(av,'fr',{numeric:true});
    });
    tbl.querySelector('tbody').append(...rows);
    ths.forEach(t => { t.classList.remove('sorted'); const ic = t.querySelector('.sort-icon i'); if (ic) ic.className = 'fas fa-sort'; });
    ths[col].classList.add('sorted');
    const ic = ths[col].querySelector('.sort-icon i');
    if (ic) ic.className = sortDir[col] ? 'fas fa-sort-up' : 'fas fa-sort-down';
    showPage(1);
}

let currentPage = 1; const rowsPerPage = 10;
function showPage(page) {
    const vis = Array.from(document.querySelectorAll('#participationsTable tbody tr')).filter(r => r.style.display !== 'none');
    const total = Math.ceil(vis.length / rowsPerPage) || 1;
    if (page < 1) page = 1; if (page > total) page = total;
    vis.forEach((r,i) => r.style.display = (i >= (page-1)*rowsPerPage && i < page*rowsPerPage) ? '' : 'none');
    currentPage = page;
    const pg = document.getElementById('paginationPart'); pg.innerHTML = '';
    const mkBtn = (lbl,p,isActive,disabled) => {
        const b = document.createElement('button');
        b.className = 'page-btn' + (isActive ? ' active' : '');
        b.innerHTML = lbl; b.disabled = disabled;
        if (!disabled) b.onclick = () => showPage(p);
        pg.appendChild(b);
    };
    mkBtn('&laquo;',1,false,page===1); mkBtn('&lsaquo;',page-1,false,page===1);
    const start = Math.max(1,page-2), end = Math.min(total,page+2);
    for (let i = start; i <= end; i++) mkBtn(i,i,i===page,false);
    mkBtn('&rsaquo;',page+1,false,page===total); mkBtn('&raquo;',total,false,page===total);
}

function confirmDelete(id) { if (confirm('Supprimer cette participation ?')) window.location.href = 'deleteParticipation.php?id=' + id + '&confirm=yes'; }

const EV_VALS = <?= json_encode(array_values($topEvents)) ?>;
let statsOpenedP = false;
function animCount(el,t){let v=0;const x=setInterval(()=>{v+=t/60;if(v>=t){v=t;clearInterval(x);}el.textContent=Math.round(v);},1000/60);}
function openStats() {
    document.getElementById('statsModal').classList.add('open');
    if (statsOpenedP) return; statsOpenedP = true;
    EV_VALS.forEach((val,i) => {
        const g = document.getElementById('pbar_'+i); if (!g) return;
        g.style.opacity = '0';
        setTimeout(() => {
            g.style.transition = 'opacity .5s ease,transform 1s cubic-bezier(.34,1.56,.64,1)';
            g.style.opacity = '1'; g.style.transform = 'translate(0,0)';
            animCount(document.getElementById('pcnt_'+i), val);
            g.onmouseenter = () => { g.style.filter = 'brightness(1.1)'; g.style.transform = 'translate(0,-8px)'; };
            g.onmouseleave = () => { g.style.filter = ''; g.style.transform = 'translate(0,0)'; };
        }, i * 230);
    });
}
function closeStats() { document.getElementById('statsModal').classList.remove('open'); }

function exportPDF() {
    const {jsPDF} = window.jspdf;
    const doc = new jsPDF({orientation:'landscape',unit:'mm',format:'a4'});
    const pW = doc.internal.pageSize.getWidth(), pH = doc.internal.pageSize.getHeight();
    const now = new Date().toLocaleDateString('fr-FR',{day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});
    function drawPageHeader(pn){doc.setFillColor(26,26,46);doc.rect(0,0,pW,22,'F');doc.setTextColor(255,255,255);doc.setFontSize(16);doc.setFont('helvetica','bold');doc.text('NutriLoop',12,13);doc.setFontSize(12);doc.setFont('helvetica','normal');doc.text('Rapport des Participations',pW/2,13,{align:'center'});doc.setFontSize(9);doc.text('Généré le : '+now+'   |   Page '+pn,pW-12,13,{align:'right'});}
    function drawKPIs(){const kpis=[{l:'Total',v:'<?=count($participations)?>',r:76,g:175,b:80},{l:'Places',v:'<?=$totalPlaces?>',r:255,g:152,b:0}];let sx=12;kpis.forEach(k=>{doc.setFillColor(k.r,k.g,k.b);doc.roundedRect(sx,27,52,16,3,3,'F');doc.setTextColor(255,255,255);doc.setFontSize(13);doc.setFont('helvetica','bold');doc.text(k.v,sx+26,35,{align:'center'});doc.setFontSize(7);doc.setFont('helvetica','normal');doc.text(k.l,sx+26,40,{align:'center'});sx+=56;});}
    function drawTableHeader(y){const hd=['ID','Événement','Nom','Email','Tél','Places','Date inscription'],cw=[12,50,32,50,24,14,45];doc.setFillColor(26,26,46);doc.rect(12,y,pW-24,9,'F');doc.setTextColor(255,255,255);doc.setFontSize(7.5);doc.setFont('helvetica','bold');let x=14;hd.forEach((h,i)=>{doc.text(h,x,y+6);x+=cw[i];});return{y:y+9,cw};}
    let pageNum=1;drawPageHeader(pageNum);drawKPIs();let{y,cw}=drawTableHeader(50);let rc=0;
    document.querySelectorAll('#participationsTable tbody tr').forEach(row=>{
        if(row.style.display==='none')return;const cells=row.querySelectorAll('td');if(!cells.length)return;
        if(y>pH-18){doc.setFillColor(240,242,245);doc.rect(0,pH-10,pW,10,'F');doc.setTextColor(120,120,120);doc.setFontSize(7);doc.setFont('helvetica','italic');doc.text('NutriLoop',12,pH-4);doc.text('Page '+pageNum,pW-12,pH-4,{align:'right'});doc.addPage();pageNum++;drawPageHeader(pageNum);const next=drawTableHeader(15);y=next.y;}
        if(rc%2===0){doc.setFillColor(248,249,250);doc.rect(12,y,pW-24,9,'F');}
        doc.setDrawColor(220,220,220);doc.line(12,y+9,pW-12,y+9);
        const vals=[cells[0]?.innerText.replace('#','').trim()||'',cells[1]?.innerText.trim()||'',cells[2]?.innerText.trim()||'',cells[3]?.innerText.trim()||'',cells[4]?.innerText.trim()||'',cells[5]?.innerText.trim()||'',cells[6]?.innerText.trim()||''];
        let x=14;vals.forEach((v,i)=>{
            if(i===0){doc.setTextColor(33,150,243);doc.setFont('helvetica','bold');}
            else{doc.setTextColor(50,50,50);doc.setFont('helvetica','normal');}
            let t=v;while(doc.getTextWidth(t)>cw[i]-2&&t.length>3)t=t.slice(0,-4)+'...';doc.text(t,x,y+6);x+=cw[i];
        });
        y+=9;rc++;
    });
    doc.setFillColor(240,242,245);doc.rect(0,pH-10,pW,10,'F');doc.setTextColor(120,120,120);doc.setFontSize(7);doc.setFont('helvetica','italic');
    doc.text('NutriLoop — Plateforme intelligente pour une alimentation durable',12,pH-4);
    doc.text('Page '+pageNum+' — '+rc+' ligne(s)',pW-12,pH-4,{align:'right'});
    doc.save('participations_nutriloop.pdf');
}

document.addEventListener('DOMContentLoaded', () => showPage(1));
</script>
</body>
</html>