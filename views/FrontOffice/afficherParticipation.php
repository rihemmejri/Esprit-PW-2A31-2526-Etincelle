<?php
require_once '../../controleurs/EvenementController.php';
require_once '../../controleurs/ParticipationController.php';

$evenementController     = new EvenementController();
$participationController = new ParticipationController();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: afficherEvenement.php'); exit; }

$evenement = $evenementController->getEvenementById($id);
if (!$evenement || $evenement->getStatut() !== 'ACTIF') { header('Location: afficherEvenement.php'); exit; }

$participations  = $participationController->getParticipationsByEvenement($id);
$placesReservees = 0;
foreach ($participations as $p) $placesReservees += ($p->getNbPlacesReservees() ?? 1);
$placesRestantes = $evenement->getNbPlacesMax() - $placesReservees;
$pourcentage     = $evenement->getNbPlacesMax() > 0 ? round(($placesReservees / $evenement->getNbPlacesMax()) * 100) : 0;

$error = ''; $success = false;
$successData = [];
$etape = 'inscription';
if ($evenement->isPayant() && isset($_POST['etape']) && $_POST['etape'] === 'paiement') {
    $etape = 'paiement';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['etape'] === 'inscription') {
        $id_user             = intval($_POST['id_user']             ?? 0);
        $nom                 = trim($_POST['nom']                   ?? '');
        $email               = trim($_POST['email']                 ?? '');
        $telephone           = trim($_POST['telephone']             ?? '');
        $nb_places_reservees = intval($_POST['nb_places_reservees'] ?? 1);

        $errors = [];
        if ($id_user < 1)                                                              $errors[] = "L'ID utilisateur est obligatoire.";
        if (empty($nom))                                                               $errors[] = "Le nom complet est obligatoire.";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))               $errors[] = "L'email n'est pas valide.";
        if ($nb_places_reservees < 1)                                                  $errors[] = "Le nombre de places doit être au moins 1.";
        if ($nb_places_reservees > $placesRestantes)                                   $errors[] = "Seulement $placesRestantes place(s) disponible(s).";
        if (!empty($telephone) && !preg_match('/^[0-9+\s\-()]{6,20}$/', $telephone))  $errors[] = "Numéro de téléphone invalide.";

        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } elseif ($placesRestantes <= 0) {
            $error = "Plus de places disponibles.";
        } elseif ($evenement->isPayant()) {
            $etape = 'paiement';
        } else {
            $participation = new Participation(
                $id, $id_user,
                htmlspecialchars($nom), htmlspecialchars($email),
                !empty($telephone) ? htmlspecialchars($telephone) : null,
                'EN_ATTENTE', null, null, null,
                $nb_places_reservees, 'GRATUIT', null, 0.00
            );
            $newId = $participationController->addParticipation($participation, $evenement);
            if ($newId) {
                $placesReservees += $nb_places_reservees;
                $placesRestantes -= $nb_places_reservees;
                $pourcentage = round(($placesReservees / $evenement->getNbPlacesMax()) * 100);
                $success = true;
                $successData = ['nom'=>$nom,'email'=>$email,'places'=>$nb_places_reservees,'ref'=>null,'montant'=>null,'type'=>'gratuit'];
            } else {
                $error = "Erreur lors de l'inscription. Veuillez réessayer avec un autre ID utilisateur.";
            }
        }
    }

    elseif ($_POST['etape'] === 'paiement') {
        $id_user             = intval($_POST['id_user']             ?? 0);
        $nom                 = trim($_POST['nom']                   ?? '');
        $email               = trim($_POST['email']                 ?? '');
        $telephone           = trim($_POST['telephone']             ?? '');
        $nb_places_reservees = intval($_POST['nb_places_reservees'] ?? 1);
        $carte_nom           = trim($_POST['carte_nom']             ?? '');
        $carte_numero        = preg_replace('/\s/', '', $_POST['carte_numero'] ?? '');
        $carte_exp           = trim($_POST['carte_exp']             ?? '');
        $carte_cvv           = trim($_POST['carte_cvv']             ?? '');
        $montant_total       = $evenement->getPrix() * $nb_places_reservees;

        $errors = [];
        if (strlen($carte_nom) < 3)                       $errors[] = "Nom du titulaire invalide.";
        if (!preg_match('/^\d{16}$/', $carte_numero))     $errors[] = "Numéro de carte invalide (16 chiffres).";
        if (!preg_match('/^\d{2}\/\d{2}$/', $carte_exp)) $errors[] = "Date d'expiration invalide (MM/AA).";
        if (!preg_match('/^\d{3,4}$/', $carte_cvv))       $errors[] = "CVV invalide.";

        if (!empty($errors)) {
            $error = implode('<br>', $errors);
            $etape = 'paiement';
        } else {
            $reference = 'NL-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $participation = new Participation(
                $id, $id_user,
                htmlspecialchars($nom), htmlspecialchars($email),
                !empty($telephone) ? htmlspecialchars($telephone) : null,
                'EN_ATTENTE', null, null, null,
                $nb_places_reservees, 'PAYE', $reference, $montant_total
            );
            $newId = $participationController->addParticipation($participation, $evenement);
            if ($newId) {
                $placesReservees += $nb_places_reservees;
                $placesRestantes -= $nb_places_reservees;
                $pourcentage = round(($placesReservees / $evenement->getNbPlacesMax()) * 100);
                $success = true;
                $successData = ['nom'=>$nom,'email'=>$email,'places'=>$nb_places_reservees,'ref'=>$reference,'montant'=>$montant_total,'type'=>'paye'];
            } else {
                $error = "Erreur lors de l'enregistrement. Veuillez réessayer.";
                $etape = 'paiement';
            }
        }
    }
}

$images = [
    'SPORT'     => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&h=400&fit=crop',
    'NUTRITION' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=1200&h=400&fit=crop',
    'WORKSHOP'  => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1200&h=400&fit=crop',
    'AUTRE'     => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200&h=400&fit=crop',
];
$typeEmojis = ['SPORT'=>'🏃','NUTRITION'=>'🥗','WORKSHOP'=>'📚','AUTRE'=>'📅'];
$lieuEncode = urlencode($evenement->getLieu());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — <?= htmlspecialchars($evenement->getTitre()) ?> — NutriLoop</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:#f0f2f5;font-family:'Poppins',sans-serif;}

        /* NAV */
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
        @media(max-width:768px){.nav-menu{position:fixed;left:-100%;top:70px;flex-direction:column;background:white;width:100%;text-align:center;transition:.3s;padding:20px 0;gap:15px;}.nav-menu.active{left:0;}.hamburger{display:flex;}}

        /* HERO IMAGE */
        .ev-hero{height:300px;position:relative;overflow:hidden;}
        .ev-hero img{width:100%;height:100%;object-fit:cover;}
        .ev-hero-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,.1),rgba(0,0,0,.75));display:flex;flex-direction:column;justify-content:flex-end;padding:32px 48px;color:white;}
        .ev-hero-overlay h1{font-size:2rem;margin-bottom:10px;}
        .ev-hero-meta{display:flex;gap:20px;flex-wrap:wrap;font-size:.88em;opacity:.95;}
        .ev-hero-meta span{display:flex;align-items:center;gap:6px;}
        .ev-hero-meta i{color:#ffeb3b;}

        /* LAYOUT PRINCIPAL */
        .page-layout{max-width:1150px;margin:0 auto;padding:40px 20px 60px;}

        /* BOUTON RETOUR */
        .btn-back{display:inline-flex;align-items:center;gap:8px;background:#6c757d;color:white;padding:10px 22px;border-radius:30px;text-decoration:none;margin-bottom:30px;transition:.3s;font-weight:500;font-size:.9em;}
        .btn-back:hover{background:#5a6268;transform:translateX(-5px);}

        /* ══ PAGE SUCCÈS ══ */
        .success-page{text-align:center;padding:20px 0;}
        .success-icon-wrap{width:100px;height:100px;border-radius:50%;margin:0 auto 24px;display:flex;align-items:center;justify-content:center;animation:popIn .6s cubic-bezier(.34,1.56,.64,1);}
        .success-icon-wrap.gratuit{background:linear-gradient(135deg,#4CAF50,#2e7d32);}
        .success-icon-wrap.paye   {background:linear-gradient(135deg,#2196F3,#673AB7);}
        .success-icon-wrap i{font-size:44px;color:white;}
        @keyframes popIn{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
        .success-title{font-size:1.8rem;font-weight:700;color:#1a1a2e;margin-bottom:8px;}
        .success-sub{font-size:1rem;color:#666;margin-bottom:28px;}

        .success-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:28px;}
        .scard{background:white;border-radius:16px;padding:20px;box-shadow:0 4px 16px rgba(0,0,0,.07);text-align:center;}
        .scard-icon{width:44px;height:44px;border-radius:50%;margin:0 auto 10px;display:flex;align-items:center;justify-content:center;font-size:18px;}
        .scard-icon.gr{background:#e8f5e9;color:#2e7d32;}
        .scard-icon.bl{background:#e3f2fd;color:#1565C0;}
        .scard-icon.pu{background:#f3e5f5;color:#6a1b9a;}
        .scard-icon.or{background:#fff3e0;color:#e65100;}
        .scard-val{font-size:1.1rem;font-weight:700;color:#1a1a2e;}
        .scard-lbl{font-size:.72em;color:#aaa;margin-top:3px;}

        .success-ref{display:inline-block;background:#f5f5f5;border:2px dashed #4CAF50;border-radius:12px;padding:12px 28px;margin-bottom:20px;font-size:1.1em;font-weight:700;color:#2e7d32;letter-spacing:3px;}
        .success-ref.paye{border-color:#2196F3;color:#1565c0;}

        .success-email-note{background:#e3f2fd;border-radius:12px;padding:14px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;font-size:.88em;color:#1565C0;}
        .success-email-note i{font-size:20px;flex-shrink:0;}

        .success-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
        .btn-success-primary{background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;padding:12px 28px;border-radius:25px;text-decoration:none;font-weight:600;font-size:.9em;display:flex;align-items:center;gap:8px;transition:.3s;}
        .btn-success-primary:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(33,150,243,.35);}
        .btn-success-sec{background:white;color:#555;padding:12px 28px;border-radius:25px;text-decoration:none;font-weight:600;font-size:.9em;border:2px solid #e0e0e0;display:flex;align-items:center;gap:8px;transition:.3s;}
        .btn-success-sec:hover{border-color:#aaa;}

        /* ══ LAYOUT 2 COL ══ */
        .two-col{display:grid;grid-template-columns:1fr 380px;gap:30px;}
        @media(max-width:920px){.two-col{grid-template-columns:1fr;}}

        /* ERREUR */
        .error-box{background:#ffebee;border-left:4px solid #f44336;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;color:#c62828;line-height:1.7;font-size:.9em;}

        /* ÉTAPES */
        .steps{display:flex;margin-bottom:24px;}
        .step{flex:1;text-align:center;padding:12px;font-size:13px;font-weight:600;color:#aaa;border-bottom:3px solid #e0e0e0;}
        .step.active{color:#2196F3;border-color:#2196F3;}
        .step.done{color:#4CAF50;border-color:#4CAF50;}
        .step i{display:block;font-size:18px;margin-bottom:4px;}

        /* FORM CARD */
        .form-card{background:white;border-radius:22px;padding:30px;box-shadow:0 8px 28px rgba(0,0,0,.07);}
        .form-card h2{color:#1565C0;margin-bottom:22px;display:flex;align-items:center;gap:10px;border-bottom:3px solid #2196F3;padding-bottom:12px;font-size:1.1rem;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        @media(max-width:600px){.form-row{grid-template-columns:1fr;}}
        .form-group{margin-bottom:16px;}
        .form-group label{display:block;margin-bottom:6px;font-weight:600;color:#333;font-size:.88em;}
        .form-group label i{color:#2196F3;margin-right:5px;}
        .required{color:#f44336;}
        .form-group input{width:100%;padding:12px 15px;border:2px solid #e0e0e0;border-radius:12px;font-size:14px;font-family:'Poppins',sans-serif;transition:.3s;}
        .form-group input:focus{outline:none;border-color:#2196F3;box-shadow:0 0 0 3px rgba(33,150,243,.1);}
        .form-group input.input-error{border-color:#f44336;background:#fff5f5;}
        .form-group input.input-ok{border-color:#4CAF50;background:#f5fff5;}
        .field-error{color:#f44336;font-size:.75em;margin-top:3px;display:none;}
        .field-error.show{display:block;}
        .input-hint{font-size:.73em;color:#aaa;margin-top:3px;display:block;}

        /* PRIX INFO */
        .prix-info{border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:14px;}
        .prix-info.g{background:#e8f5e9;border:2px solid #4CAF50;}
        .prix-info.p{background:#e3f2fd;border:2px solid #2196F3;}
        .prix-info-icon{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
        .prix-info-icon.g{background:#4CAF50;color:white;}.prix-info-icon.p{background:#2196F3;color:white;}
        .prix-info-text h4{font-size:13px;font-weight:700;}
        .prix-info-text h4.g{color:#2e7d32;}.prix-info-text h4.p{color:#1565c0;}
        .prix-info-text p{font-size:11px;color:#888;margin:2px 0 0;}
        .prix-info-montant{margin-left:auto;font-size:18px;font-weight:700;}
        .prix-info-montant.g{color:#2e7d32;}.prix-info-montant.p{color:#1565c0;}

        /* TOTAL */
        .total-box{background:#fff3e0;border:2px solid #FFA726;border-radius:10px;padding:12px 16px;margin-top:-8px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;}
        .total-box .lbl{font-size:13px;color:#e65100;font-weight:600;}
        .total-box .val{font-size:20px;font-weight:700;color:#e65100;}

        /* CARTE BANCAIRE */
        .card-preview{background:linear-gradient(135deg,#1a1a2e,#2d2d4e);border-radius:16px;padding:22px;margin-bottom:20px;color:white;position:relative;overflow:hidden;}
        .card-preview::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05);}
        .card-chip{width:36px;height:28px;background:linear-gradient(135deg,#f0c060,#d4a030);border-radius:5px;margin-bottom:14px;}
        .card-number{font-size:17px;letter-spacing:3px;margin-bottom:14px;font-family:monospace;}
        .card-info{display:flex;justify-content:space-between;font-size:11px;opacity:.7;}
        .card-brand{position:absolute;top:16px;right:18px;font-size:22px;opacity:.6;}
        .sim-badge{background:#fff3e0;border:1.5px solid #FFA726;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#e65100;display:flex;align-items:center;gap:8px;}

        /* SUBMIT */
        .btn-submit{width:100%;background:linear-gradient(135deg,#2196F3,#4CAF50);color:white;padding:14px;border:none;border-radius:14px;font-size:1em;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;transition:.3s;margin-top:8px;}
        .btn-submit:hover{transform:translateY(-2px);box-shadow:0 10px 25px rgba(33,150,243,.35);}
        .btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none;}
        .btn-submit.pay{background:linear-gradient(135deg,#2196F3,#673AB7);}

        /* ══ SIDEBAR ══ */
        .sidebar{display:flex;flex-direction:column;gap:22px;}

        /* Info card sidebar */
        .info-card{background:white;border-radius:20px;padding:22px;box-shadow:0 5px 20px rgba(0,0,0,.07);}
        .info-card-title{font-size:.92em;color:#1565C0;margin-bottom:14px;display:flex;align-items:center;gap:8px;border-bottom:2px solid #e3f2fd;padding-bottom:10px;font-weight:700;}
        .info-card-title i{color:#2196F3;}
        .irow{display:flex;align-items:flex-start;gap:12px;padding:9px 0;border-bottom:1px solid #f5f5f5;}
        .irow:last-child{border-bottom:none;}
        .irow i{color:#2196F3;width:18px;margin-top:2px;font-size:13px;}
        .irow-label{font-size:.75em;color:#aaa;display:block;}
        .irow-value{font-size:.88em;font-weight:600;color:#333;}

        /* Barre places */
        .places-bar-bg{background:#e0e0e0;border-radius:10px;height:10px;overflow:hidden;margin-top:6px;}
        .places-bar-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,#4CAF50,#2196F3);}
        .places-bar-fill.danger{background:linear-gradient(90deg,#f44336,#ff9800);}
        .places-numbers{display:flex;justify-content:space-between;font-size:.74em;color:#aaa;margin-top:4px;}
        .urgence-badge{background:#fff3e0;border-radius:10px;padding:10px 14px;margin-top:10px;font-size:.8em;color:#e65100;display:flex;align-items:center;gap:8px;}

        /* ══ GOOGLE MAPS — section séparée grand format ══ */
        .maps-section{background:white;border-radius:22px;overflow:hidden;box-shadow:0 8px 28px rgba(0,0,0,.08);}
        .maps-header{padding:18px 22px;background:linear-gradient(135deg,#1a1a2e,#2d3561);display:flex;align-items:center;justify-content:space-between;}
        .maps-header-left{display:flex;align-items:center;gap:12px;}
        .maps-header-icon{width:42px;height:42px;background:#f44336;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;color:white;}
        .maps-header-text h3{color:white;font-size:.95em;font-weight:700;margin:0;}
        .maps-header-text p{color:rgba(255,255,255,.6);font-size:.75em;margin:2px 0 0;}
        .maps-iframe-wrap{position:relative;height:320px;}
        .maps-iframe-wrap iframe{width:100%;height:100%;border:none;display:block;}
        .maps-footer{padding:14px 22px;display:flex;gap:10px;border-top:1px solid #f0f0f0;}
        .maps-btn{flex:1;padding:10px;border-radius:10px;border:none;cursor:pointer;font-size:.82em;font-weight:600;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:7px;transition:.3s;text-decoration:none;}
        .maps-btn-primary{background:#2196F3;color:white;}
        .maps-btn-primary:hover{background:#1976D2;}
        .maps-btn-sec{background:#f5f5f5;color:#555;border:2px solid #e0e0e0;}
        .maps-btn-sec:hover{border-color:#2196F3;color:#1565C0;}

        /* Avantages */
        .avantages-list{list-style:none;}
        .avantages-list li{display:flex;align-items:center;gap:10px;padding:7px 0;font-size:.84em;color:#444;border-bottom:1px solid #f5f5f5;}
        .avantages-list li:last-child{border-bottom:none;}
        .avantages-list li i{color:#4CAF50;width:16px;}

        /* FOOTER */
        .footer{background:#1a1a2e;color:white;padding:40px 20px 20px;margin-top:20px;}
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

<!-- HERO IMAGE ÉVÉNEMENT -->
<div class="ev-hero">
    <img src="<?= $images[$evenement->getTypeEvenement()] ?? $images['AUTRE'] ?>" alt="<?= htmlspecialchars($evenement->getTitre()) ?>">
    <div class="ev-hero-overlay">
        <h1><?= ($typeEmojis[$evenement->getTypeEvenement()] ?? '📅') . ' ' . htmlspecialchars($evenement->getTitre()) ?></h1>
        <div class="ev-hero-meta">
            <span><i class="fas fa-tag"></i> <?= $evenement->getTypeEvenement() ?></span>
            <span><i class="fas fa-calendar-day"></i> <?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></span>
            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($evenement->getLieu()) ?></span>
            <span><i class="fas fa-users"></i> <?= $placesRestantes ?> places restantes</span>
            <?php if ($evenement->isPayant()): ?>
            <span><i class="fas fa-credit-card"></i> <?= number_format($evenement->getPrix(), 2) ?> TND / place</span>
            <?php else: ?>
            <span><i class="fas fa-gift"></i> Gratuit</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="page-layout">
    <a href="afficherEvenement.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour aux événements</a>

    <?php if ($success): ?>
    <!-- ══════════════════════════════════════════
         PAGE SUCCÈS
    ══════════════════════════════════════════ -->
    <div class="success-page">
        <div class="success-icon-wrap <?= $successData['type'] ?>">
            <i class="fas <?= $successData['type']==='paye' ? 'fa-check-double' : 'fa-check' ?>"></i>
        </div>

        <h2 class="success-title">
            <?= $successData['type']==='paye' ? '🎉 Paiement réussi !' : '✅ Inscription confirmée !' ?>
        </h2>
        <p class="success-sub">
            Bienvenue <strong><?= htmlspecialchars($successData['nom']) ?></strong> — votre participation a bien été enregistrée.
        </p>

        <?php if ($successData['ref']): ?>
        <div class="success-ref paye"><?= htmlspecialchars($successData['ref']) ?></div>
        <?php endif; ?>

        <div class="success-cards">
            <div class="scard">
                <div class="scard-icon gr"><i class="fas fa-user"></i></div>
                <div class="scard-val"><?= htmlspecialchars($successData['nom']) ?></div>
                <div class="scard-lbl">Participant</div>
            </div>
            <div class="scard">
                <div class="scard-icon bl"><i class="fas fa-ticket-alt"></i></div>
                <div class="scard-val"><?= $successData['places'] ?></div>
                <div class="scard-lbl">Place(s) réservée(s)</div>
            </div>
            <div class="scard">
                <div class="scard-icon or"><i class="fas fa-calendar-check"></i></div>
                <div class="scard-val"><?= date('d/m/Y') ?></div>
                <div class="scard-lbl">Date d'inscription</div>
            </div>
            <?php if ($successData['montant']): ?>
            <div class="scard">
                <div class="scard-icon pu"><i class="fas fa-coins"></i></div>
                <div class="scard-val"><?= number_format($successData['montant'], 2) ?> TND</div>
                <div class="scard-lbl">Montant payé</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Note email -->
        <div class="success-email-note">
            <i class="fas fa-envelope-open-text"></i>
            <div>
                <strong>Email de confirmation envoyé !</strong><br>
                Un email de confirmation a été envoyé à <strong><?= htmlspecialchars($successData['email']) ?></strong>
            </div>
        </div>

        <div class="success-actions">
            <a href="afficherEvenement.php" class="btn-success-primary">
                <i class="fas fa-calendar-alt"></i> Voir d'autres événements
            </a>
            <a href="afficherParticipation.php?id=<?= $id ?>" class="btn-success-sec">
                <i class="fas fa-user-plus"></i> Nouvelle inscription
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- ══════════════════════════════════════════
         FORMULAIRE + SIDEBAR
    ══════════════════════════════════════════ -->
    <div class="two-col">
        <!-- COLONNE GAUCHE : FORMULAIRE -->
        <div>
            <?php if ($placesRestantes <= 0): ?>
            <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <strong>Complet !</strong> Plus de places disponibles.</div>
            <?php endif; ?>

            <?php if ($evenement->isPayant()): ?>
            <div class="steps">
                <div class="step <?= $etape==='inscription' ? 'active' : 'done' ?>"><i class="fas fa-user-plus"></i> Inscription</div>
                <div class="step <?= $etape==='paiement' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Paiement</div>
            </div>
            <?php endif; ?>

            <?php if ($etape === 'inscription'): ?>
            <!-- FORMULAIRE INSCRIPTION -->
            <div class="form-card">
                <h2><i class="fas fa-user-plus"></i> Formulaire d'inscription</h2>

                <?php if ($error): ?>
                <div class="error-box"><i class="fas fa-exclamation-circle"></i> <span><?= $error ?></span></div>
                <?php endif; ?>

                <!-- Prix info -->
                <?php $c = $evenement->isPayant() ? 'p' : 'g'; ?>
                <div class="prix-info <?= $c ?>">
                    <div class="prix-info-icon <?= $c ?>"><i class="fas <?= $evenement->isPayant() ? 'fa-credit-card' : 'fa-gift' ?>"></i></div>
                    <div class="prix-info-text">
                        <h4 class="<?= $c ?>"><?= $evenement->isPayant() ? 'Événement payant' : 'Événement gratuit' ?></h4>
                        <p><?= $evenement->isPayant() ? 'Paiement requis à l\'étape suivante' : 'Inscription libre, aucun frais' ?></p>
                    </div>
                    <div class="prix-info-montant <?= $c ?>">
                        <?= $evenement->isPayant() ? number_format($evenement->getPrix(),2).' TND <small style="font-size:11px;font-weight:400">/place</small>' : 'Gratuit' ?>
                    </div>
                </div>

                <form id="inscriptionForm" action="" method="POST" novalidate>
                    <input type="hidden" name="etape" value="inscription">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> ID Utilisateur <span class="required">*</span></label>
                            <input type="number" id="id_user" name="id_user" placeholder="Ex: 1" min="1" value="<?= htmlspecialchars($_POST['id_user'] ?? '') ?>">
                            <span class="input-hint">Votre ID profil NutriLoop</span>
                            <span class="field-error" id="err-id">Obligatoire (min 1).</span>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nom complet <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" placeholder="Votre nom et prénom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            <span class="field-error" id="err-nom">Minimum 3 caractères.</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <span class="field-error" id="err-email">Email invalide.</span>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" placeholder="+216 XX XXX XXX" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-ticket-alt"></i> Nombre de places <span class="required">*</span></label>
                        <input type="number" id="nb_places" name="nb_places_reservees" min="1" max="<?= max(1,$placesRestantes) ?>" value="1" oninput="updateTotal()">
                        <span class="input-hint"><?= $placesRestantes ?> place(s) disponible(s)</span>
                        <span class="field-error" id="err-places">Entre 1 et <?= $placesRestantes ?> places.</span>
                    </div>
                    <?php if ($evenement->isPayant()): ?>
                    <div class="total-box">
                        <span class="lbl"><i class="fas fa-coins"></i> Total à payer :</span>
                        <span class="val" id="totalVal"><?= number_format($evenement->getPrix(),2) ?> TND</span>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn-submit <?= $evenement->isPayant() ? 'pay' : '' ?>" <?= $placesRestantes<=0?'disabled':'' ?>>
                        <i class="fas <?= $evenement->isPayant() ? 'fa-credit-card' : 'fa-paper-plane' ?>"></i>
                        <?= $placesRestantes<=0 ? 'Complet' : ($evenement->isPayant() ? 'Continuer vers le paiement' : 'Confirmer mon inscription') ?>
                    </button>
                </form>
            </div>

            <?php elseif ($etape === 'paiement'): ?>
            <!-- FORMULAIRE PAIEMENT -->
            <div class="form-card">
                <h2><i class="fas fa-credit-card"></i> Paiement sécurisé</h2>

                <?php if ($error): ?>
                <div class="error-box"><i class="fas fa-exclamation-circle"></i> <span><?= $error ?></span></div>
                <?php endif; ?>

                <!-- Récap -->
                <div class="prix-info p" style="margin-bottom:20px;">
                    <div class="prix-info-icon p"><i class="fas fa-receipt"></i></div>
                    <div class="prix-info-text">
                        <h4 class="p"><?= htmlspecialchars($evenement->getTitre()) ?></h4>
                        <p><?= intval($_POST['nb_places_reservees']??1) ?> place(s) × <?= number_format($evenement->getPrix(),2) ?> TND</p>
                    </div>
                    <div class="prix-info-montant p"><?= number_format($evenement->getPrix()*intval($_POST['nb_places_reservees']??1),2) ?> TND</div>
                </div>

                <div class="sim-badge"><i class="fas fa-flask"></i> <span><strong>Mode simulation</strong> — Utilisez n'importe quelle valeur. Aucune transaction réelle.</span></div>

                <!-- Carte preview -->
                <div class="card-preview">
                    <div class="card-chip"></div>
                    <div class="card-number" id="previewNumber">•••• •••• •••• ••••</div>
                    <div class="card-info">
                        <div><div style="font-size:9px;opacity:.5;margin-bottom:2px;">TITULAIRE</div><div id="previewNom" style="font-size:12px;letter-spacing:1px;">VOTRE NOM</div></div>
                        <div><div style="font-size:9px;opacity:.5;margin-bottom:2px;">EXPIRE</div><div id="previewExp" style="font-size:12px;">MM/AA</div></div>
                    </div>
                    <div class="card-brand"><i class="fab fa-cc-visa"></i></div>
                </div>

                <form id="paiementForm" action="" method="POST" novalidate>
                    <input type="hidden" name="etape"               value="paiement">
                    <input type="hidden" name="id_user"             value="<?= htmlspecialchars($_POST['id_user']??'') ?>">
                    <input type="hidden" name="nom"                 value="<?= htmlspecialchars($_POST['nom']??'') ?>">
                    <input type="hidden" name="email"               value="<?= htmlspecialchars($_POST['email']??'') ?>">
                    <input type="hidden" name="telephone"           value="<?= htmlspecialchars($_POST['telephone']??'') ?>">
                    <input type="hidden" name="nb_places_reservees" value="<?= intval($_POST['nb_places_reservees']??1) ?>">

                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom du titulaire</label>
                        <input type="text" name="carte_nom" id="carte_nom" placeholder="EX: KARIM BEN ALI" oninput="updatePreview()">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Numéro de carte</label>
                        <input type="text" name="carte_numero" id="carte_numero" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCard(this);updatePreview()">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Expiration</label>
                            <input type="text" name="carte_exp" id="carte_exp" placeholder="MM/AA" maxlength="5" oninput="formatExp(this);updatePreview()">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> CVV</label>
                            <input type="text" name="carte_cvv" placeholder="123" maxlength="4">
                        </div>
                    </div>
                    <button type="submit" class="btn-submit pay" id="payBtn">
                        <i class="fas fa-lock"></i> Payer <?= number_format($evenement->getPrix()*intval($_POST['nb_places_reservees']??1),2) ?> TND
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- COLONNE DROITE : SIDEBAR -->
        <div class="sidebar">

            <!-- Infos événement -->
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-calendar-alt"></i> Détails de l'événement</div>
                <div class="irow"><i class="fas fa-calendar-day"></i><div><span class="irow-label">Date</span><span class="irow-value"><?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></span></div></div>
                <div class="irow"><i class="fas fa-map-marker-alt"></i><div><span class="irow-label">Lieu</span><span class="irow-value"><?= htmlspecialchars($evenement->getLieu()) ?></span></div></div>
                <div class="irow">
                    <i class="fas <?= $evenement->isPayant()?'fa-credit-card':'fa-gift' ?>" style="color:<?= $evenement->isPayant()?'#2196F3':'#4CAF50' ?>;"></i>
                    <div><span class="irow-label">Tarif</span><span class="irow-value" style="color:<?= $evenement->isPayant()?'#1565c0':'#2e7d32' ?>;"><?= $evenement->isPayant()?number_format($evenement->getPrix(),2).' TND / place':'Gratuit' ?></span></div>
                </div>
            </div>

            <!-- Places -->
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-users"></i> Disponibilité</div>
                <div class="irow"><i class="fas fa-ticket-alt"></i><div><span class="irow-label">Places totales</span><span class="irow-value"><?= $evenement->getNbPlacesMax() ?></span></div></div>
                <div class="irow"><i class="fas fa-user-check"></i><div><span class="irow-label">Réservées</span><span class="irow-value" style="color:#f44336"><?= $placesReservees ?></span></div></div>
                <div class="irow"><i class="fas fa-user-plus"></i><div><span class="irow-label">Disponibles</span><span class="irow-value" style="color:<?= $placesRestantes<=5?'#f44336':'#2e7d32' ?>"><?= $placesRestantes ?></span></div></div>
                <div class="places-bar-bg"><div class="places-bar-fill <?= $pourcentage>=80?'danger':'' ?>" style="width:<?= $pourcentage ?>%"></div></div>
                <div class="places-numbers"><span><?= $pourcentage ?>% rempli</span><span><?= $placesRestantes ?>/<?= $evenement->getNbPlacesMax() ?> libres</span></div>
                <?php if ($placesRestantes<=5 && $placesRestantes>0): ?>
                <div class="urgence-badge"><i class="fas fa-fire"></i> <strong>Dernières places !</strong></div>
                <?php endif; ?>
            </div>

            <!-- ══ GOOGLE MAPS GRAND FORMAT ══ -->
            <div class="maps-section">
                <div class="maps-header">
                    <div class="maps-header-left">
                        <div class="maps-header-icon"><i class="fas fa-map-marked-alt"></i></div>
                        <div class="maps-header-text">
                            <h3>Localisation du lieu</h3>
                            <p><?= htmlspecialchars($evenement->getLieu()) ?></p>
                        </div>
                    </div>
                </div>
                <div class="maps-iframe-wrap">
                    <iframe
                        src="https://maps.google.com/maps?q=<?= $lieuEncode ?>&output=embed&z=15"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="maps-footer">
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $lieuEncode ?>" target="_blank" class="maps-btn maps-btn-primary">
                        <i class="fas fa-directions"></i> Itinéraire
                    </a>
                    <a href="https://maps.google.com/maps?q=<?= $lieuEncode ?>" target="_blank" class="maps-btn maps-btn-sec">
                        <i class="fas fa-external-link-alt"></i> Ouvrir Maps
                    </a>
                </div>
            </div>

            <!-- Avantages -->
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-star"></i> Inclus dans cet événement</div>
                <ul class="avantages-list">
                    <li><i class="fas fa-check"></i> Accès complet à l'événement</li>
                    <li><i class="fas fa-check"></i> Certificat de participation</li>
                    <li><i class="fas fa-check"></i> Supports et ressources offerts</li>
                    <li><i class="fas fa-check"></i> Accès communauté NutriLoop</li>
                    <li><i class="fas fa-check"></i> Conseils personnalisés</li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

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
const prixUnitaire = <?= $evenement->getPrix() ?>;
function updateTotal(){
    const pl = parseInt(document.getElementById('nb_places')?.value)||1;
    const v  = document.getElementById('totalVal');
    if(v) v.textContent = (prixUnitaire*pl).toFixed(2)+' TND';
}
function formatCard(input){let v=input.value.replace(/\D/g,'').substring(0,16);input.value=v.replace(/(\d{4})(?=\d)/g,'$1 ');}
function formatExp(input){let v=input.value.replace(/\D/g,'').substring(0,4);if(v.length>=2)v=v.substring(0,2)+'/'+v.substring(2);input.value=v;}
function updatePreview(){
    const nom=document.getElementById('carte_nom')?.value.toUpperCase()||'VOTRE NOM';
    const num=document.getElementById('carte_numero')?.value.replace(/\s/g,'')||'';
    const exp=document.getElementById('carte_exp')?.value||'MM/AA';
    let d='';for(let i=0;i<16;i++){if(i>0&&i%4===0)d+=' ';d+=i<num.length?num[i]:'•';}
    if(document.getElementById('previewNom'))document.getElementById('previewNom').textContent=nom;
    if(document.getElementById('previewNumber'))document.getElementById('previewNumber').textContent=d;
    if(document.getElementById('previewExp'))document.getElementById('previewExp').textContent=exp;
}
document.getElementById('paiementForm')?.addEventListener('submit',function(){
    const btn=document.getElementById('payBtn');
    if(btn){btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';btn.disabled=true;}
});

// Validation
function se(iid,eid,show){const i=document.getElementById(iid),e=document.getElementById(eid);if(!i||!e)return;if(show){i.classList.add('input-error');i.classList.remove('input-ok');e.classList.add('show');}else{i.classList.remove('input-error');i.classList.add('input-ok');e.classList.remove('show');}}
function vId(){const v=parseInt(document.getElementById('id_user')?.value);const ok=!isNaN(v)&&v>=1;se('id_user','err-id',!ok);return ok;}
function vNom(){const v=document.getElementById('nom')?.value.trim();const ok=v&&v.length>=3;se('nom','err-nom',!ok);return ok;}
function vEmail(){const v=document.getElementById('email')?.value.trim();const ok=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);se('email','err-email',!ok);return ok;}
function vPl(){const v=parseInt(document.getElementById('nb_places')?.value);const ok=v>=1&&v<=<?=max(1,$placesRestantes)?>;se('nb_places','err-places',!ok);return ok;}
document.getElementById('id_user')?.addEventListener('input',vId);
document.getElementById('nom')?.addEventListener('input',vNom);
document.getElementById('email')?.addEventListener('input',vEmail);
document.getElementById('nb_places')?.addEventListener('input',vPl);
document.getElementById('inscriptionForm')?.addEventListener('submit',function(e){
    const ok=vId()&vNom()&vEmail()&vPl();
    if(!ok){e.preventDefault();document.querySelector('.input-error')?.scrollIntoView({behavior:'smooth',block:'center'});}
});

document.addEventListener('DOMContentLoaded',()=>{
    document.querySelector('.hamburger')?.addEventListener('click',()=>{
        document.querySelector('.hamburger').classList.toggle('active');
        document.querySelector('.nav-menu').classList.toggle('active');
    });
    updateTotal();
});
</script>
</body>
</html>