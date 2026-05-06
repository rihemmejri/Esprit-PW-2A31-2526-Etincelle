<?php
require_once '../../controleurs/EvenementController.php';

$evenementController = new EvenementController();
$id = intval($_GET['id'] ?? 0);
if (!$id) { echo '<h2>Événement introuvable.</h2>'; exit; }

$ev = $evenementController->getEvenementById($id);
if (!$ev) { echo '<h2>Événement introuvable.</h2>'; exit; }

$images = [
    'SPORT'     => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=400&fit=crop',
    'NUTRITION' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=800&h=400&fit=crop',
    'WORKSHOP'  => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&h=400&fit=crop',
    'AUTRE'     => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=400&fit=crop',
];
$imgUrl     = $images[$ev->getTypeEvenement()] ?? $images['AUTRE'];
$lieuEncode = urlencode($ev->getLieu());
$isPayant   = $ev->isPayant();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?= htmlspecialchars($ev->getTitre()) ?> — NutriLoop</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
body{font-family:'Poppins',sans-serif;background:#f0f2f5;color:#333;max-width:480px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column;}

.topbar{background:linear-gradient(135deg,#1a1a2e,#2e3f6f);padding:12px 18px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
.topbar-logo{font-size:1.1rem;font-weight:700;color:white;}
.topbar-logo em{color:#4CAF50;font-style:normal;}
.topbar-badge{background:rgba(255,255,255,.15);color:white;padding:4px 12px;border-radius:20px;font-size:.72em;font-weight:600;display:flex;align-items:center;gap:5px;}

.hero-img{width:100%;height:230px;object-fit:cover;display:block;}

.content{background:white;padding:22px 18px 10px;flex:1;}

.badges-row{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.badge{padding:5px 14px;border-radius:20px;font-size:.72em;font-weight:700;color:white;}
.b-SPORT{background:#1565c0;}.b-NUTRITION{background:#2e7d32;}.b-WORKSHOP{background:#e65100;}.b-AUTRE{background:#6a1b9a;}
.badge-statut{padding:5px 14px;border-radius:20px;font-size:.72em;font-weight:700;display:flex;align-items:center;gap:5px;}
.s-ACTIF{background:#e8f5e9;color:#2e7d32;}.s-CANCELLED{background:#ffebee;color:#c62828;}.s-COMPLETED{background:#e8eaf6;color:#3949ab;}
.stt-dot{width:7px;height:7px;border-radius:50%;}
.d-ACTIF{background:#4CAF50;}.d-CANCELLED{background:#c62828;}.d-COMPLETED{background:#3949ab;}

.ev-title{font-size:1.5rem;font-weight:800;color:#1a1a2e;margin-bottom:10px;line-height:1.3;}
.ev-desc{font-size:.9em;color:#666;line-height:1.7;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid #f0f0f0;}

.info-section-title{font-size:.72em;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;}
.irow{display:flex;align-items:center;gap:14px;padding:11px 14px;border-radius:14px;background:#f5f5f5;margin-bottom:8px;}
.irow-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:15px;}
.ic-b{background:#dbeafe;color:#1d4ed8;}.ic-g{background:#dcfce7;color:#16a34a;}.ic-o{background:#ffedd5;color:#ea580c;}.ic-p{background:#f3e8ff;color:#9333ea;}
.irow-label{font-size:.7em;color:#aaa;display:block;}
.irow-val{font-size:.92em;font-weight:600;color:#1a1a2e;}

.prix-box{border-radius:16px;padding:16px;display:flex;align-items:center;gap:14px;margin:18px 0;}
.px-g{background:#dcfce7;border:2px solid #4CAF50;}.px-p{background:#dbeafe;border:2px solid #2196F3;}
.px-icon{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.px-icon-g{background:#4CAF50;color:white;}.px-icon-p{background:#2196F3;color:white;}
.px-text h3{font-size:14px;font-weight:700;}
.px-text-g h3{color:#16a34a;}.px-text-p h3{color:#1d4ed8;}
.px-text p{font-size:11px;color:#888;margin:2px 0 0;}
.px-val{margin-left:auto;font-size:22px;font-weight:800;}
.px-val-g{color:#16a34a;}.px-val-p{color:#1d4ed8;}

.maps-wrap{border-radius:16px;overflow:hidden;border:2px solid #e5e7eb;margin-bottom:18px;}
.maps-header{background:#1a1a2e;padding:12px 16px;display:flex;align-items:center;gap:10px;}
.maps-header i{color:#f44336;font-size:17px;}
.maps-header strong{color:white;font-size:.9em;display:block;}
.maps-header span{color:rgba(255,255,255,.5);font-size:.72em;}
.maps-iframe{width:100%;height:220px;border:none;display:block;}
.maps-open-btn{display:flex;align-items:center;justify-content:center;gap:8px;background:#2196F3;color:white;padding:13px;text-decoration:none;font-size:.86em;font-weight:600;}

.sticky-btn{position:sticky;bottom:0;background:white;padding:14px 18px;border-top:1px solid #f0f0f0;box-shadow:0 -4px 20px rgba(0,0,0,.08);}
.btn-inscr{display:flex;align-items:center;justify-content:center;gap:10px;padding:15px;border-radius:16px;text-decoration:none;font-size:1em;font-weight:700;width:100%;border:none;cursor:pointer;font-family:'Poppins',sans-serif;}
.btn-inscr-g{background:linear-gradient(135deg,#4CAF50,#2196F3);color:white;}
.btn-inscr-p{background:linear-gradient(135deg,#2196F3,#673AB7);color:white;}
.btn-inscr-dis{background:#e5e7eb;color:#9ca3af;pointer-events:none;}

.page-footer{background:#1a1a2e;padding:12px 18px;text-align:center;}
.page-footer p{color:rgba(255,255,255,.3);font-size:.7em;}
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">🌿 Nutri<em>Loop</em></div>
    <div class="topbar-badge"><i class="fas fa-qrcode"></i> Détail événement</div>
</div>

<img src="<?= $imgUrl ?>" class="hero-img" alt="<?= htmlspecialchars($ev->getTitre()) ?>">

<div class="content">
    <div class="badges-row">
        <span class="badge b-<?= $ev->getTypeEvenement() ?>"><?= $ev->getTypeEvenement() ?></span>
        <span class="badge-statut s-<?= $ev->getStatut() ?>">
            <span class="stt-dot d-<?= $ev->getStatut() ?>"></span>
            <?= $ev->getStatut() ?>
        </span>
    </div>

    <h1 class="ev-title"><?= htmlspecialchars($ev->getTitre()) ?></h1>
    <p class="ev-desc"><?= htmlspecialchars($ev->getDescription()) ?></p>

    <div class="info-section-title">Informations</div>

    <div class="irow">
        <div class="irow-icon ic-b"><i class="fas fa-calendar-day"></i></div>
        <div>
            <span class="irow-label">Date</span>
            <span class="irow-val"><?= date('d/m/Y', strtotime($ev->getDateEvenement())) ?></span>
        </div>
    </div>
    <div class="irow">
        <div class="irow-icon ic-o"><i class="fas fa-map-marker-alt"></i></div>
        <div>
            <span class="irow-label">Lieu</span>
            <span class="irow-val"><?= htmlspecialchars($ev->getLieu()) ?></span>
        </div>
    </div>
    <div class="irow">
        <div class="irow-icon ic-p"><i class="fas fa-users"></i></div>
        <div>
            <span class="irow-label">Places</span>
            <span class="irow-val"><?= $ev->getNbPlacesMax() ?> places disponibles</span>
        </div>
    </div>

    <?php $px = $isPayant ? 'p' : 'g'; ?>
    <div class="prix-box px-<?= $px ?>">
        <div class="px-icon px-icon-<?= $px ?>">
            <i class="fas <?= $isPayant ? 'fa-credit-card' : 'fa-gift' ?>"></i>
        </div>
        <div class="px-text px-text-<?= $px ?>">
            <h3><?= $isPayant ? 'Événement payant' : 'Événement gratuit' ?></h3>
            <p><?= $isPayant ? "Paiement requis à l'inscription" : 'Inscription libre et gratuite' ?></p>
        </div>
        <div class="px-val px-val-<?= $px ?>">
            <?= $isPayant ? number_format($ev->getPrix(), 2) . ' TND' : 'Gratuit' ?>
        </div>
    </div>

    <div class="maps-wrap">
        <div class="maps-header">
            <i class="fas fa-map-marked-alt"></i>
            <div>
                <strong>Localisation</strong>
                <span><?= htmlspecialchars($ev->getLieu()) ?></span>
            </div>
        </div>
        <iframe src="https://maps.google.com/maps?q=<?= $lieuEncode ?>&output=embed&z=15"
                class="maps-iframe" allowfullscreen="" loading="lazy"></iframe>
        <a href="https://www.google.com/maps/search/?api=1&query=<?= $lieuEncode ?>"
           target="_blank" class="maps-open-btn">
            <i class="fas fa-directions"></i> Obtenir un itinéraire
        </a>
    </div>
</div>

<div class="sticky-btn">
    <?php if ($ev->getStatut() === 'ACTIF'): ?>
    <a href="afficherParticipation.php?id=<?= $ev->getIdEvenement() ?>"
       class="btn-inscr <?= $isPayant ? 'btn-inscr-p' : 'btn-inscr-g' ?>">
        <i class="fas <?= $isPayant ? 'fa-credit-card' : 'fa-user-plus' ?>"></i>
        <?= $isPayant ? "S'inscrire et payer " . number_format($ev->getPrix(), 2) . ' TND' : "S'inscrire gratuitement" ?>
    </a>
    <?php else: ?>
    <div class="btn-inscr btn-inscr-dis">
        <i class="fas fa-lock"></i> Inscription non disponible
    </div>
    <?php endif; ?>
</div>

<div class="page-footer">
    <p>NutriLoop — Plateforme intelligente pour une alimentation durable</p>
</div>

</body>
</html>