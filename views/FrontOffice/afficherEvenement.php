<?php
include '../../controleurs/EvenementController.php';
require_once __DIR__ . '/../../models/Evenement.php';

if (file_exists(__DIR__ . '/../../lib/phpqrcode/qrlib.php')) {
    require_once __DIR__ . '/../../lib/phpqrcode/qrlib.php';
}

define('APP_BASE_URL', 'http://192.168.1.146:8000');

$evenementController = new EvenementController();
$evenements = $evenementController->listEvenements();

function genererQREvenement($ev) {
    if (!class_exists('QRcode')) return null;
    $dir = __DIR__ . '/../../assets/qrcodes/events/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $fichier = $dir . 'event_' . $ev->getIdEvenement() . '.png';
    $data = "BEGIN:VEVENT\r\n"
          . "SUMMARY:" . $ev->getTitre() . "\r\n"
          . "DTSTART:" . date('Ymd', strtotime($ev->getDateEvenement())) . "\r\n"
          . "LOCATION:" . $ev->getLieu() . "\r\n"
          . "DESCRIPTION:Tarif: " . ($ev->isPayant() ? number_format($ev->getPrix(),2).' TND' : 'Gratuit') . " | Places: " . $ev->getNbPlacesMax() . " | " . $ev->getDescription() . "\r\n"
          . "END:VEVENT";
    QRcode::png($data, $fichier, QR_ECLEVEL_M, 8, 2);
    return 'http://localhost:8000/assets/qrcodes/events/event_' . $ev->getIdEvenement() . '.png';
}

$images = [
    'SPORT'     => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=250&fit=crop',
    'NUTRITION' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=400&h=250&fit=crop',
    'WORKSHOP'  => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400&h=250&fit=crop',
    'AUTRE'     => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=400&h=250&fit=crop',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Événements - NutriLoop</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:#f0f2f5;font-family:'Poppins',sans-serif;}
        .header{background:white;box-shadow:0 2px 10px rgba(0,0,0,.05);position:sticky;top:0;z-index:1000;}
        .navbar{display:flex;justify-content:space-between;align-items:center;padding:15px 40px;max-width:1200px;margin:0 auto;}
        .logo{display:flex;align-items:center;gap:10px;}
        .logo-img{width:40px;height:40px;border-radius:10px;}
        .logo-text{font-size:1.5rem;font-weight:700;color:#2e7d32;}
        .nav-menu{display:flex;list-style:none;gap:30px;}
        .nav-menu li a{text-decoration:none;color:#333;font-weight:500;transition:.3s;}
        .nav-menu li a:hover,.nav-menu li a.active{color:#4CAF50;}
        .btn-dashboard{background:#003366;color:white!important;padding:8px 20px;border-radius:25px;}
        .hamburger{display:none;flex-direction:column;cursor:pointer;}
        .hamburger span{width:25px;height:3px;background:#333;margin:3px 0;}
        @media(max-width:768px){
            .nav-menu{position:fixed;left:-100%;top:70px;flex-direction:column;background:white;width:100%;text-align:center;transition:.3s;padding:20px 0;gap:15px;box-shadow:0 10px 20px rgba(0,0,0,.1);}
            .nav-menu.active{left:0;}.hamburger{display:flex;}
        }
        .hero-section{background:linear-gradient(135deg,#e3f2fd,#e8f5e9);padding:80px 20px;text-align:center;border-radius:30px;margin:40px 20px;}
        .hero-section h1{font-size:2.5rem;color:#1565C0;margin-bottom:15px;}
        .hero-section p{font-size:1.1rem;color:#555;}
        .filters-section{background:white;padding:25px;border-radius:20px;margin:0 20px 30px;box-shadow:0 5px 20px rgba(0,0,0,.05);}
        .filters-title{font-size:1.1rem;font-weight:600;color:#1565C0;margin-bottom:18px;}
        .filters-grid{display:flex;gap:15px;flex-wrap:wrap;align-items:flex-end;}
        .filter-group{flex:1;min-width:180px;}
        .filter-group label{display:block;margin-bottom:6px;font-weight:500;color:#333;font-size:.9em;}
        .filter-group input,.filter-group select{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:10px;font-size:14px;font-family:'Poppins',sans-serif;transition:.3s;}
        .filter-group input:focus,.filter-group select:focus{outline:none;border-color:#2196F3;box-shadow:0 0 0 3px rgba(33,150,243,.1);}
        .events-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:30px;padding:0 20px 60px;}
        .event-card{background:white;border-radius:20px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,.08);transition:all .3s ease;display:flex;flex-direction:column;}
        .event-card:hover{transform:translateY(-8px);box-shadow:0 20px 40px rgba(0,0,0,.15);}
        .event-image{height:200px;position:relative;overflow:hidden;}
        .event-image img{width:100%;height:100%;object-fit:cover;transition:transform .3s;}
        .event-card:hover .event-image img{transform:scale(1.05);}
        .event-type-badge{position:absolute;top:15px;left:15px;padding:5px 13px;border-radius:20px;font-size:.72em;font-weight:700;color:white;}
        .badge-SPORT{background:#1565c0;}.badge-NUTRITION{background:#2e7d32;}.badge-WORKSHOP{background:#e65100;}.badge-AUTRE{background:#6a1b9a;}
        .event-statut-badge{position:absolute;top:15px;right:15px;padding:5px 12px;border-radius:20px;font-size:.72em;font-weight:700;}
        .stt-ACTIF{background:#e8f5e9;color:#2e7d32;}.stt-CANCELLED{background:#ffebee;color:#c62828;}.stt-COMPLETED{background:#e8eaf6;color:#3949ab;}
        .event-prix-badge{position:absolute;bottom:15px;right:15px;padding:6px 14px;border-radius:20px;font-size:.78em;font-weight:700;}
        .prix-gratuit-badge{background:#4CAF50;color:white;}.prix-payant-badge{background:#2196F3;color:white;}
        .event-content{padding:20px;flex:1;display:flex;flex-direction:column;}
        .event-title{font-size:1.15em;font-weight:700;color:#333;margin-bottom:10px;}
        .event-description{color:#666;line-height:1.6;margin-bottom:20px;font-size:.88em;flex:1;}

        /* ── FOOTER CARTE ── */
        .event-footer{display:flex;gap:6px;padding-top:14px;border-top:1px solid #eee;flex-wrap:wrap;}
        .btn-qr{background:#1a1a2e;color:white;padding:8px 10px;border-radius:16px;border:none;cursor:pointer;font-size:.72em;font-weight:600;font-family:'Poppins',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:all .3s;flex:1;justify-content:center;}
        .btn-qr:hover{background:#2d2d4e;transform:translateY(-2px);}
        .btn-maps{background:#fff3e0;color:#e65100;padding:8px 10px;border-radius:16px;border:2px solid #ffcc80;cursor:pointer;font-size:.72em;font-weight:600;font-family:'Poppins',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:all .3s;flex:1;justify-content:center;}
        .btn-maps:hover{background:#ffe0b2;transform:translateY(-2px);}
        .btn-share{background:#e8f5e9;color:#2e7d32;padding:8px 10px;border-radius:16px;border:2px solid #a5d6a7;cursor:pointer;font-size:.72em;font-weight:600;font-family:'Poppins',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:all .3s;flex:1;justify-content:center;}
        .btn-share:hover{background:#c8e6c9;transform:translateY(-2px);}
        .btn-inscrire{background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;padding:8px 10px;border-radius:16px;border:none;cursor:pointer;font-size:.72em;font-weight:600;font-family:'Poppins',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:all .3s;text-decoration:none;flex:1;justify-content:center;}
        .btn-inscrire:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(33,150,243,.3);}

        .no-results{text-align:center;padding:60px;background:white;border-radius:20px;grid-column:1/-1;}
        .no-results i{font-size:64px;color:#ccc;margin-bottom:18px;display:block;}

        /* MODAL QR */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;justify-content:center;align-items:center;padding:20px;backdrop-filter:blur(8px);}
        .modal-overlay.open{display:flex;animation:mfade .2s ease;}
        @keyframes mfade{from{opacity:0}to{opacity:1}}
        @keyframes mup{from{transform:translateY(50px);opacity:0}to{transform:translateY(0);opacity:1}}

        /* QR box */
        .qr-box{background:#1a1a2e;border-radius:28px;padding:36px 30px;max-width:360px;width:100%;text-align:center;box-shadow:0 40px 100px rgba(0,0,0,.6);animation:mup .3s cubic-bezier(.34,1.56,.64,1);position:relative;}
        .modal-close-btn{position:absolute;top:14px;right:14px;background:rgba(255,255,255,.1);border:none;color:white;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;}
        .modal-close-btn:hover{background:rgba(255,255,255,.3);}
        .qr-title{color:white;font-size:1rem;font-weight:700;margin-bottom:4px;}
        .qr-sub{color:rgba(255,255,255,.45);font-size:.78em;margin-bottom:22px;}
        .qr-img-wrap{background:white;border-radius:18px;padding:14px;display:inline-block;margin-bottom:16px;box-shadow:0 0 0 8px rgba(255,255,255,.06);}
        .qr-img-wrap img{width:240px;height:240px;display:block;border-radius:6px;}
        .qr-ev-name{color:white;font-size:.95em;font-weight:700;margin-bottom:8px;}
        .qr-hint{color:rgba(255,255,255,.35);font-size:.72em;display:flex;align-items:center;justify-content:center;gap:5px;}

        /* Maps box */
        .maps-box{background:white;border-radius:24px;width:100%;max-width:720px;overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,.5);animation:mup .3s cubic-bezier(.34,1.56,.64,1);}
        .maps-mheader{background:linear-gradient(135deg,#1a1a2e,#2d3561);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;}
        .maps-mheader-left{display:flex;align-items:center;gap:12px;}
        .maps-micon{width:42px;height:42px;background:#f44336;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;color:white;flex-shrink:0;}
        .maps-mtitle{color:white;font-size:.92em;font-weight:700;margin:0;}
        .maps-msub{color:rgba(255,255,255,.5);font-size:.74em;margin:2px 0 0;}
        .maps-mclose{background:rgba(255,255,255,.1);border:none;color:white;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center;}
        .maps-mclose:hover{background:rgba(255,255,255,.25);}
        .maps-mframe{width:100%;height:400px;border:none;display:block;}
        .maps-mfooter{padding:12px 18px;display:flex;gap:10px;border-top:1px solid #f0f0f0;}
        .maps-mfooter a{flex:1;padding:11px;border-radius:10px;font-size:.84em;font-weight:600;display:flex;align-items:center;justify-content:center;gap:7px;text-decoration:none;}
        .maps-mbtn-p{background:#2196F3;color:white;}.maps-mbtn-s{background:#f5f5f5;color:#555;border:2px solid #e0e0e0;}

        /* Share box */
        .share-box{background:white;border-radius:24px;width:100%;max-width:420px;overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,.5);animation:mup .3s cubic-bezier(.34,1.56,.64,1);}
        .share-mheader{background:linear-gradient(135deg,#2e7d32,#1565C0);padding:18px 22px;display:flex;align-items:center;justify-content:space-between;}
        .share-mheader h3{color:white;font-size:1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:10px;}
        .share-mclose{background:rgba(255,255,255,.12);border:none;color:white;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center;}
        .share-mclose:hover{background:rgba(255,255,255,.25);}
        .share-mbody{padding:22px;}
        .share-ev-name{font-size:.9em;font-weight:700;color:#1a1a2e;margin-bottom:16px;text-align:center;}
        .share-btns-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;}
        .share-btn{padding:12px;border-radius:12px;text-decoration:none;font-size:.84em;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;}
        .share-wa{background:#25D366;color:white;}.share-wa:hover{background:#1da851;}
        .share-fb{background:#1877F2;color:white;}.share-fb:hover{background:#166fe5;}
        .share-copy-btn{width:100%;padding:11px;border-radius:10px;background:#f5f5f5;color:#333;border:1.5px solid #e0e0e0;cursor:pointer;font-size:.84em;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;font-family:'Poppins',sans-serif;margin-bottom:12px;}
        .share-copy-btn:hover{background:#e3f2fd;border-color:#2196F3;color:#1565C0;}
        .share-link-display{background:#f5f5f5;border-radius:8px;padding:10px 14px;font-size:.75em;color:#666;word-break:break-all;border:1px solid #e0e0e0;}

        /* Toast */
        .toast{position:fixed;bottom:24px;right:24px;background:#1a1a2e;color:white;padding:14px 20px;border-radius:12px;font-size:13px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.2);z-index:99999;display:flex;align-items:center;gap:10px;transform:translateY(100px);opacity:0;transition:all .3s cubic-bezier(.34,1.56,.64,1);}
        .toast.show{transform:translateY(0);opacity:1;}
        .toast i{color:#4CAF50;font-size:16px;}

        /* FOOTER */
        .footer{background:#1a1a2e;color:white;padding:40px 20px 20px;margin-top:60px;}
        .footer-content{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:28px;}
        .footer-section h3,.footer-section h4{margin-bottom:14px;}
        .footer-section ul{list-style:none;}
        .footer-section ul li{margin-bottom:8px;}
        .footer-section a{color:#ccc;text-decoration:none;}
        .footer-section a:hover{color:#4CAF50;}
        .social-links{display:flex;gap:14px;margin-top:14px;}
        .social-links a{background:rgba(255,255,255,.12);width:35px;height:35px;border-radius:50%;display:flex;align-items:center;justify-content:center;}
        .social-links a:hover{background:#4CAF50;}
        .footer-bottom{text-align:center;padding-top:25px;margin-top:25px;border-top:1px solid rgba(255,255,255,.1);font-size:.84em;}
        @media(max-width:768px){.events-grid{grid-template-columns:1fr;}.filters-grid{flex-direction:column;}}
    </style>
</head>
<body>

<header class="header">
    <nav class="navbar">
        <div class="logo">
            <img src="image/logo.PNG" alt="NutriLoop" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
            <span class="logo-text">NutriLoop</span>
        </div>
        <ul class="nav-menu">
            <li><a href="index.html">Accueil</a></li>
            <li><a href="afficherRecette.php">Recettes</a></li>
            <li><a href="afficherEvenement.php" class="active">Événements</a></li>
            <li><a href="about.html">À propos</a></li>
            <li><a href="../backoffice/index.html" class="btn-dashboard">Dashboard</a></li>
        </ul>
        <div class="hamburger"><span></span><span></span><span></span></div>
    </nav>
</header>

<div class="hero-section">
    <h1><i class="fas fa-calendar-star"></i> Nos Événements Nutritionnels</h1>
    <p>Rejoignez nos ateliers, workshops et événements sportifs pour améliorer votre alimentation</p>
</div>

<div class="filters-section">
    <div class="filters-title"><i class="fas fa-filter"></i> Filtrer les événements</div>
    <div class="filters-grid">
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Rechercher</label>
            <input type="text" id="searchEvenement" placeholder="Titre, lieu..." onkeyup="filterEvenements()">
        </div>
        <div class="filter-group">
            <label><i class="fas fa-tag"></i> Type</label>
            <select id="filterType" onchange="filterEvenements()">
                <option value="">Tous les types</option>
                <option value="SPORT">🏃 Sport</option>
                <option value="NUTRITION">🥗 Nutrition</option>
                <option value="WORKSHOP">📚 Workshop</option>
                <option value="AUTRE">📅 Autre</option>
            </select>
        </div>
        <div class="filter-group">
            <label><i class="fas fa-circle"></i> Statut</label>
            <select id="filterStatut" onchange="filterEvenements()">
                <option value="">Tous les statuts</option>
                <option value="ACTIF">✅ Actif</option>
                <option value="CANCELLED">❌ Annulé</option>
                <option value="COMPLETED">🏁 Terminé</option>
            </select>
        </div>
        <div class="filter-group">
            <label><i class="fas fa-coins"></i> Tarif</label>
            <select id="filterTarif" onchange="filterEvenements()">
                <option value="">Tous les tarifs</option>
                <option value="gratuit">🎁 Gratuit</option>
                <option value="payant">💳 Payant</option>
            </select>
        </div>
    </div>
</div>

<div class="events-grid" id="eventsGrid">
<?php if (count($evenements) > 0): ?>
    <?php foreach ($evenements as $ev):
        $imgUrl    = $images[$ev->getTypeEvenement()] ?? $images['AUTRE'];
        $isPayant  = $ev->isPayant();
        $prixLabel = $isPayant ? number_format($ev->getPrix(), 2) . ' TND' : 'Gratuit';
        $qrUrl     = genererQREvenement($ev);
        $inscriptionUrl = 'http://localhost:8000/views/FrontOffice/afficherParticipation.php?id=' . $ev->getIdEvenement();
        $waText = urlencode("🌿 *".$ev->getTitre()."*\n📅 ".date('d/m/Y',strtotime($ev->getDateEvenement()))."\n📍 ".$ev->getLieu()."\n".($isPayant?"💰 ".number_format($ev->getPrix(),2)." TND":"✅ Gratuit")."\n🔗 Inscrivez-vous : ".$inscriptionUrl);
    ?>
    <div class="event-card"
         data-titre="<?= strtolower(htmlspecialchars($ev->getTitre())) ?>"
         data-type="<?= $ev->getTypeEvenement() ?>"
         data-statut="<?= $ev->getStatut() ?>"
         data-tarif="<?= $isPayant ? 'payant' : 'gratuit' ?>"
         data-lieu="<?= strtolower(htmlspecialchars($ev->getLieu())) ?>">

        <div class="event-image">
            <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($ev->getTitre()) ?>">
            <span class="event-type-badge badge-<?= $ev->getTypeEvenement() ?>"><?= $ev->getTypeEvenement() ?></span>
            <span class="event-statut-badge stt-<?= $ev->getStatut() ?>"><?= $ev->getStatut() ?></span>
            <span class="event-prix-badge <?= $isPayant ? 'prix-payant-badge' : 'prix-gratuit-badge' ?>">
                <i class="fas <?= $isPayant ? 'fa-credit-card' : 'fa-gift' ?>"></i> <?= $prixLabel ?>
            </span>
        </div>

        <div class="event-content">
            <h3 class="event-title"><?= htmlspecialchars($ev->getTitre()) ?></h3>
            <p class="event-description">
                <?= htmlspecialchars(substr($ev->getDescription(), 0, 130)) ?><?= strlen($ev->getDescription()) > 130 ? '…' : '' ?>
            </p>

            <div class="event-footer">
                <?php if ($qrUrl): ?>
                <button class="btn-qr" onclick='openQR(<?= json_encode($qrUrl) ?>, <?= json_encode($ev->getTitre()) ?>)'>
                    <i class="fas fa-qrcode"></i> QR
                </button>
                <?php endif; ?>

                <button class="btn-maps" onclick='openMaps(<?= json_encode($ev->getLieu()) ?>, <?= json_encode($ev->getTitre()) ?>)'>
                    <i class="fas fa-map-marker-alt"></i> Maps
                </button>

                <button class="btn-share" onclick='openShare(<?= json_encode($ev->getTitre()) ?>, <?= json_encode($inscriptionUrl) ?>, <?= json_encode("https://wa.me/?text=".$waText) ?>, <?= json_encode("https://www.facebook.com/sharer/sharer.php?u=".urlencode($inscriptionUrl)) ?>)'>
                    <i class="fas fa-share-alt"></i> Partager
                </button>

                <?php if ($ev->getStatut() === 'ACTIF'): ?>
                <a href="afficherParticipation.php?id=<?= $ev->getIdEvenement() ?>" class="btn-inscrire">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </a>
                <?php else: ?>
                <span style="color:#aaa;font-size:.72em;font-style:italic;flex:1;text-align:center;">
                    <i class="fas fa-lock"></i> Indisponible
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-results"><i class="fas fa-calendar-times"></i><h3>Aucun événement disponible</h3></div>
<?php endif; ?>
</div>

<div id="noResults" style="display:none;margin:0 20px 40px">
    <div class="no-results" style="grid-column:unset">
        <i class="fas fa-search"></i><h3>Aucun événement trouvé</h3>
        <p>Modifiez vos critères de recherche.</p>
    </div>
</div>

<!-- MODAL QR -->
<div class="modal-overlay" id="qrModal" onclick="if(event.target===this)closeQR()">
    <div class="qr-box">
        <button class="modal-close-btn" onclick="closeQR()"><i class="fas fa-times"></i></button>
        <div class="qr-title">📱 Scanner le QR Code</div>
        <div class="qr-sub">Scannez pour voir les infos de l'événement</div>
        <div class="qr-img-wrap"><img id="qrBigImg" src="" alt="QR Code"></div>
        <div class="qr-ev-name" id="qrEvName"></div>
        <div class="qr-hint"><i class="fas fa-mobile-alt"></i> Pointez la caméra vers le QR Code</div>
    </div>
</div>

<!-- MODAL MAPS -->
<div class="modal-overlay" id="mapsModal" onclick="if(event.target===this)closeMaps()">
    <div class="maps-box">
        <div class="maps-mheader">
            <div class="maps-mheader-left">
                <div class="maps-micon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <p class="maps-mtitle" id="mapsEvName"></p>
                    <p class="maps-msub" id="mapsLieu"></p>
                </div>
            </div>
            <button class="maps-mclose" onclick="closeMaps()"><i class="fas fa-times"></i></button>
        </div>
        <iframe id="mapsIframe" class="maps-mframe" src="" allowfullscreen="" loading="lazy"></iframe>
        <div class="maps-mfooter">
            <a id="mapsItineraire" href="#" target="_blank" class="maps-mbtn-p"><i class="fas fa-directions"></i> Itinéraire</a>
            <a id="mapsOuvrir" href="#" target="_blank" class="maps-mbtn-s"><i class="fas fa-external-link-alt"></i> Ouvrir Maps</a>
        </div>
    </div>
</div>

<!-- MODAL PARTAGER -->
<div class="modal-overlay" id="shareModal" onclick="if(event.target===this)closeShare()">
    <div class="share-box">
        <div class="share-mheader">
            <h3><i class="fas fa-share-alt"></i> Partager l'événement</h3>
            <button class="share-mclose" onclick="closeShare()"><i class="fas fa-times"></i></button>
        </div>
        <div class="share-mbody">
            <div class="share-ev-name" id="shareEvName"></div>
            <div class="share-btns-grid">
                <a id="shareWa" href="#" target="_blank" class="share-btn share-wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                <a id="shareFb" href="#" target="_blank" class="share-btn share-fb"><i class="fab fa-facebook"></i> Facebook</a>
            </div>
            <button class="share-copy-btn" onclick="copyShareLink()">
                <i class="fas fa-link"></i> Copier le lien d'inscription
            </button>
            <div class="share-link-display" id="shareLinkDisplay"></div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>NutriLoop</h3>
            <p>L'intelligence artificielle au service de votre assiette.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-section">
            <h4>Liens rapides</h4>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="afficherRecette.php">Recettes</a></li>
                <li><a href="afficherEvenement.php">Événements</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Contact</h4>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> Tunis, Tunisie</li>
                <li><i class="fas fa-envelope"></i> contact@nutriloop.ai</li>
                <li><i class="fas fa-phone"></i> +216 70 000 000</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 NutriLoop - Tous droits réservés</p></div>
</footer>

<script>
let currentShareLink = '';

function openQR(url, name) {
    document.getElementById('qrBigImg').src = url;
    document.getElementById('qrEvName').textContent = name;
    document.getElementById('qrModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeQR() { document.getElementById('qrModal').classList.remove('open'); document.body.style.overflow = ''; }

function openMaps(lieu, titre) {
    const enc = encodeURIComponent(lieu);
    document.getElementById('mapsEvName').textContent = titre;
    document.getElementById('mapsLieu').textContent   = lieu;
    document.getElementById('mapsIframe').src = 'https://maps.google.com/maps?q=' + enc + '&output=embed&z=15';
    document.getElementById('mapsItineraire').href = 'https://www.google.com/maps/search/?api=1&query=' + enc;
    document.getElementById('mapsOuvrir').href     = 'https://maps.google.com/maps?q=' + enc;
    document.getElementById('mapsModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeMaps() { document.getElementById('mapsModal').classList.remove('open'); document.getElementById('mapsIframe').src = ''; document.body.style.overflow = ''; }

function openShare(titre, lien, wa, fb) {
    currentShareLink = lien;
    document.getElementById('shareEvName').textContent = titre;
    document.getElementById('shareWa').href = wa;
    document.getElementById('shareFb').href = fb;
    document.getElementById('shareLinkDisplay').textContent = lien;
    document.getElementById('shareModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeShare() { document.getElementById('shareModal').classList.remove('open'); document.body.style.overflow = ''; }

function copyShareLink() {
    navigator.clipboard.writeText(currentShareLink).then(() => {
        showToast('🔗 Lien d\'inscription copié !');
    }).catch(() => {
        const el = document.createElement('textarea'); el.value = currentShareLink;
        document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
        showToast('🔗 Lien d\'inscription copié !');
    });
}

function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

function filterEvenements() {
    const search = document.getElementById('searchEvenement').value.toLowerCase();
    const type   = document.getElementById('filterType').value;
    const statut = document.getElementById('filterStatut').value;
    const tarif  = document.getElementById('filterTarif').value;
    const cards  = document.querySelectorAll('.event-card');
    let n = 0;
    cards.forEach(c => {
        let ok = true;
        if (search && !c.dataset.titre.includes(search) && !c.dataset.lieu.includes(search)) ok=false;
        if (type   && c.dataset.type   !== type)   ok=false;
        if (statut && c.dataset.statut !== statut) ok=false;
        if (tarif  && c.dataset.tarif  !== tarif)  ok=false;
        c.style.display = ok ? '' : 'none';
        if (ok) n++;
    });
    document.getElementById('noResults').style.display = n===0 ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.hamburger')?.addEventListener('click', () => {
        document.querySelector('.hamburger').classList.toggle('active');
        document.querySelector('.nav-menu').classList.toggle('active');
    });
    document.addEventListener('keydown', e => {
        if (e.key==='Escape') { closeQR(); closeMaps(); closeShare(); }
    });
});
</script>
</body>
</html>