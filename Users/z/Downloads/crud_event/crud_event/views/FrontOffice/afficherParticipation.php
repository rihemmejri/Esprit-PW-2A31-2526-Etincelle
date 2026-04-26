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
foreach ($participations as $p) {
    $placesReservees += ($p->getNbPlacesReservees() ?? 1);
}
$placesRestantes = $evenement->getNbPlacesMax() - $placesReservees;
$pourcentage     = $evenement->getNbPlacesMax() > 0 ? round(($placesReservees / $evenement->getNbPlacesMax()) * 100) : 0;

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // MODIFICATION 1 : id_user récupéré du POST
    $id_user             = intval($_POST['id_user']             ?? 0);
    $nom                 = trim($_POST['nom']                   ?? '');
    $email               = trim($_POST['email']                 ?? '');
    $telephone           = trim($_POST['telephone']             ?? '');
    $nb_places_reservees = intval($_POST['nb_places_reservees'] ?? 1);

    $errors = [];
    if ($id_user < 1)                             $errors[] = "L'ID utilisateur est obligatoire.";
    if (empty($nom))                              $errors[] = "Le nom complet est obligatoire.";
    if (empty($email))                            $errors[] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
    if ($nb_places_reservees < 1)                 $errors[] = "Le nombre de places doit être au moins 1.";
    if ($nb_places_reservees > $placesRestantes)  $errors[] = "Vous demandez plus de places que disponibles ($placesRestantes restante(s)).";
    if (!empty($telephone) && !preg_match('/^[0-9+\s\-()]{6,20}$/', $telephone)) $errors[] = "Le numéro de téléphone n'est pas valide.";

    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } elseif ($placesRestantes <= 0) {
        $error = "Désolé, il n'y a plus de places disponibles.";
    } else {
        $participation = new Participation(
            $id, $id_user,
            htmlspecialchars($nom),
            htmlspecialchars($email),
            !empty($telephone) ? htmlspecialchars($telephone) : null,
            'EN_ATTENTE', null, null, null,
            $nb_places_reservees
        );
        $participationController->addParticipation($participation);
        $placesReservees += $nb_places_reservees;
        $placesRestantes -= $nb_places_reservees;
        $pourcentage = round(($placesReservees / $evenement->getNbPlacesMax()) * 100);
        $success = "Inscription réussie ! Bienvenue $nom, vous avez réservé $nb_places_reservees place(s).";
    }
}

$images = [
    'SPORT'     => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&h=400&fit=crop',
    'NUTRITION' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=1200&h=400&fit=crop',
    'WORKSHOP'  => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1200&h=400&fit=crop',
    'AUTRE'     => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200&h=400&fit=crop',
];
$typeEmojis = ['SPORT'=>'🏃','NUTRITION'=>'🥗','WORKSHOP'=>'📚','AUTRE'=>'📅'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?= htmlspecialchars($evenement->getTitre()) ?> - NutriLoop</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#f0f2f5; font-family:'Poppins',sans-serif; }
        .header { background:white; box-shadow:0 2px 10px rgba(0,0,0,.05); position:sticky; top:0; z-index:1000; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 40px; max-width:1200px; margin:0 auto; }
        .logo { display:flex; align-items:center; gap:10px; }
        .logo-img { width:40px; height:40px; border-radius:10px; }
        .logo-text { font-size:1.5rem; font-weight:700; color:#2e7d32; }
        .nav-menu { display:flex; list-style:none; gap:30px; }
        .nav-menu li a { text-decoration:none; color:#333; font-weight:500; transition:.3s; }
        .nav-menu li a:hover, .nav-menu li a.active { color:#4CAF50; }
        .btn-dashboard { background:#003366; color:white!important; padding:8px 20px; border-radius:25px; }
        .hamburger { display:none; flex-direction:column; cursor:pointer; }
        .hamburger span { width:25px; height:3px; background:#333; margin:3px 0; }
        @media(max-width:768px){ .nav-menu { position:fixed; left:-100%; top:70px; flex-direction:column; background:white; width:100%; text-align:center; transition:.3s; padding:20px 0; gap:15px; } .nav-menu.active { left:0; } .hamburger { display:flex; } }

        .ev-hero-img { height:280px; position:relative; overflow:hidden; }
        .ev-hero-img img { width:100%; height:100%; object-fit:cover; }
        .ev-hero-overlay { position:absolute; inset:0; background:linear-gradient(to bottom,rgba(0,0,0,.2),rgba(0,0,0,.7)); display:flex; flex-direction:column; justify-content:flex-end; padding:30px 40px; color:white; }
        .ev-hero-overlay h1 { font-size:2rem; margin-bottom:12px; }
        .ev-hero-meta { display:flex; gap:18px; flex-wrap:wrap; font-size:.88em; opacity:.95; }
        .ev-hero-meta span { display:flex; align-items:center; gap:6px; }
        .ev-hero-meta i { color:#ffeb3b; }

        .page-layout { max-width:1100px; margin:0 auto; padding:40px 20px 60px; display:grid; grid-template-columns:1fr 360px; gap:30px; }
        @media(max-width:900px){ .page-layout { grid-template-columns:1fr; } }

        .btn-back { display:inline-flex; align-items:center; gap:8px; background:#6c757d; color:white; padding:10px 22px; border-radius:30px; text-decoration:none; margin-bottom:24px; transition:.3s; font-weight:500; }
        .btn-back:hover { background:#5a6268; transform:translateX(-5px); }

        .success-box { background:#e8f5e9; border:2px solid #4CAF50; border-radius:20px; padding:30px; text-align:center; margin-bottom:24px; }
        .success-box i { font-size:3.5em; color:#4CAF50; margin-bottom:14px; display:block; }
        .success-box h3 { color:#2e7d32; margin-bottom:10px; }
        .btn-retour { display:inline-flex; align-items:center; gap:8px; background:#4CAF50; color:white; padding:12px 26px; border-radius:25px; text-decoration:none; font-weight:600; transition:.3s; margin-top:14px; }
        .btn-retour:hover { background:#388e3c; }

        .error-box { background:#ffebee; border-left:4px solid #f44336; border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:flex-start; gap:10px; color:#c62828; line-height:1.7; }

        .form-card { background:white; border-radius:22px; padding:32px; box-shadow:0 8px 28px rgba(0,0,0,.08); }
        .form-card h2 { color:#1565C0; margin-bottom:24px; display:flex; align-items:center; gap:10px; border-bottom:3px solid #2196F3; padding-bottom:12px; }

        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        @media(max-width:600px){ .form-row { grid-template-columns:1fr; } }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#333; font-size:.9em; }
        .form-group label i { color:#2196F3; margin-right:6px; }
        .required { color:#f44336; }

        .form-group input { width:100%; padding:12px 15px; border:2px solid #e0e0e0; border-radius:12px; font-size:14px; font-family:'Poppins',sans-serif; transition:.3s; }
        .form-group input:focus { outline:none; border-color:#2196F3; box-shadow:0 0 0 3px rgba(33,150,243,.1); }
        .form-group input.input-error { border-color:#f44336; background:#fff5f5; }
        .form-group input.input-ok    { border-color:#4CAF50; background:#f5fff5; }

        .field-error { color:#f44336; font-size:.78em; margin-top:4px; display:none; }
        .field-error.show { display:block; }
        .input-hint { font-size:.75em; color:#999; margin-top:4px; display:block; }

        .btn-submit { width:100%; background:linear-gradient(135deg,#2196F3,#4CAF50); color:white; padding:15px; border:none; border-radius:14px; font-size:1.05em; font-weight:700; cursor:pointer; font-family:'Poppins',sans-serif; display:flex; align-items:center; justify-content:center; gap:10px; transition:.3s; margin-top:8px; }
        .btn-submit:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(33,150,243,.35); }
        .btn-submit:disabled { opacity:.6; cursor:not-allowed; transform:none; }

        .sidebar { display:flex; flex-direction:column; gap:20px; }
        .info-card { background:white; border-radius:20px; padding:22px; box-shadow:0 5px 20px rgba(0,0,0,.07); }
        .info-card h3 { font-size:.95em; color:#1565C0; margin-bottom:14px; display:flex; align-items:center; gap:8px; border-bottom:2px solid #e3f2fd; padding-bottom:10px; }
        .info-card h3 i { color:#2196F3; }
        .info-row { display:flex; align-items:flex-start; gap:12px; padding:9px 0; border-bottom:1px solid #f5f5f5; }
        .info-row:last-child { border-bottom:none; }
        .info-row i { color:#2196F3; width:18px; margin-top:2px; }
        .info-label { font-size:.78em; color:#999; display:block; }
        .info-value { font-size:.9em; font-weight:600; color:#333; }

        .places-bar-bg { background:#e0e0e0; border-radius:10px; height:10px; overflow:hidden; margin-top:6px; }
        .places-bar-fill { height:100%; border-radius:10px; background:linear-gradient(90deg,#4CAF50,#2196F3); transition:width .5s; }
        .places-bar-fill.danger { background:linear-gradient(90deg,#f44336,#ff9800); }
        .places-numbers { display:flex; justify-content:space-between; font-size:.76em; color:#888; margin-top:5px; }

        .avantages-list { list-style:none; }
        .avantages-list li { display:flex; align-items:center; gap:10px; padding:7px 0; font-size:.86em; color:#444; border-bottom:1px solid #f5f5f5; }
        .avantages-list li:last-child { border-bottom:none; }
        .avantages-list li i { color:#4CAF50; width:16px; }

        .footer { background:#1a1a2e; color:white; padding:40px 20px 20px; margin-top:20px; }
        .footer-content { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:28px; }
        .footer-section h3,.footer-section h4 { margin-bottom:14px; }
        .footer-section ul { list-style:none; }
        .footer-section ul li { margin-bottom:8px; }
        .footer-section a { color:#ccc; text-decoration:none; }
        .footer-section a:hover { color:#4CAF50; }
        .social-links { display:flex; gap:14px; margin-top:14px; }
        .social-links a { background:rgba(255,255,255,.12); width:35px; height:35px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
        .social-links a:hover { background:#4CAF50; }
        .footer-bottom { text-align:center; padding-top:25px; margin-top:25px; border-top:1px solid rgba(255,255,255,.1); font-size:.84em; }
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

<div class="ev-hero-img">
    <img src="<?= $images[$evenement->getTypeEvenement()] ?? $images['AUTRE'] ?>" alt="<?= htmlspecialchars($evenement->getTitre()) ?>">
    <div class="ev-hero-overlay">
        <h1><?= ($typeEmojis[$evenement->getTypeEvenement()] ?? '📅') . ' ' . htmlspecialchars($evenement->getTitre()) ?></h1>
        <div class="ev-hero-meta">
            <span><i class="fas fa-tag"></i> <?= $evenement->getTypeEvenement() ?></span>
            <span><i class="fas fa-calendar-day"></i> <?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></span>
            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($evenement->getLieu()) ?></span>
            <span><i class="fas fa-users"></i> <?= $placesRestantes ?> places restantes</span>
        </div>
    </div>
</div>

<div class="page-layout">
    <div>
        <a href="afficherEvenement.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour aux événements</a>

        <?php if ($success): ?>
        <div class="success-box">
            <i class="fas fa-check-circle"></i>
            <h3>✅ Inscription confirmée !</h3>
            <p><?= htmlspecialchars($success) ?></p>
            <p style="font-size:.84em;color:#888"><i class="fas fa-clock"></i> Date : <strong><?= date('d/m/Y à H:i') ?></strong></p>
            <a href="afficherEvenement.php" class="btn-retour"><i class="fas fa-calendar-alt"></i> Voir d'autres événements</a>
        </div>
        <?php else: ?>

        <?php if ($placesRestantes <= 0): ?>
        <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <strong>Complet !</strong> Plus de places disponibles.</div>
        <?php endif; ?>

        <div class="form-card">
            <h2><i class="fas fa-user-plus"></i> Formulaire d'inscription</h2>

            <?php if ($error): ?>
            <div class="error-box"><i class="fas fa-exclamation-circle"></i> <span><?= $error ?></span></div>
            <?php endif; ?>

            <form id="inscriptionForm" action="" method="POST" novalidate>

                <!-- MODIFICATION 2 : ID user visible -->
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> ID Utilisateur <span class="required">*</span></label>
                        <input type="number" id="id_user" name="id_user" placeholder="Ex: 1" min="1"
                               value="<?= htmlspecialchars($_POST['id_user'] ?? '') ?>">
                        <span class="input-hint">Votre ID dans votre profil NutriLoop</span>
                        <span class="field-error" id="err-iduser">L'ID utilisateur est obligatoire (min 1).</span>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom complet <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom et prénom"
                               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                        <span class="field-error" id="err-nom">Le nom est obligatoire (minimum 3 caractères).</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="votre@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <span class="field-error" id="err-email">Veuillez entrer un email valide.</span>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="+216 XX XXX XXX"
                               value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        <span class="field-error" id="err-telephone">Numéro invalide.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-ticket-alt"></i> Nombre de places <span class="required">*</span></label>
                    <input type="number" id="nb_places" name="nb_places_reservees"
                           min="1" max="<?= max(1, $placesRestantes) ?>" value="1">
                    <span class="input-hint"><?= $placesRestantes ?> place(s) disponible(s) max</span>
                    <span class="field-error" id="err-places">Entre 1 et <?= $placesRestantes ?> places.</span>
                </div>

                <button type="submit" class="btn-submit" <?= $placesRestantes <= 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-paper-plane"></i>
                    <?= $placesRestantes <= 0 ? 'Complet — Inscription impossible' : 'Confirmer mon inscription' ?>
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <div class="sidebar">
        <div class="info-card">
            <h3><i class="fas fa-calendar-alt"></i> Détails de l'événement</h3>
            <div class="info-row"><i class="fas fa-calendar-day"></i><div><span class="info-label">Date</span><span class="info-value"><?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></span></div></div>
            <div class="info-row"><i class="fas fa-map-marker-alt"></i><div><span class="info-label">Lieu</span><span class="info-value"><?= htmlspecialchars($evenement->getLieu()) ?></span></div></div>
            <div class="info-row"><i class="fas fa-clock"></i><div><span class="info-label">Date d'inscription</span><span class="info-value"><?= date('d/m/Y à H:i') ?></span></div></div>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-users"></i> Places disponibles</h3>
            <div class="info-row"><i class="fas fa-ticket-alt"></i><div><span class="info-label">Places totales</span><span class="info-value"><?= $evenement->getNbPlacesMax() ?></span></div></div>
            <div class="info-row"><i class="fas fa-user-check"></i><div><span class="info-label">Places réservées</span><span class="info-value" style="color:#f44336"><?= $placesReservees ?></span></div></div>
            <div class="info-row"><i class="fas fa-user-plus"></i><div><span class="info-label">Places restantes</span><span class="info-value" style="color:<?= $placesRestantes <= 5 ? '#f44336' : '#2e7d32' ?>"><?= $placesRestantes ?></span></div></div>
            <div class="places-bar-bg"><div class="places-bar-fill <?= $pourcentage >= 80 ? 'danger' : '' ?>" style="width:<?= $pourcentage ?>%"></div></div>
            <div class="places-numbers"><span><?= $pourcentage ?>% rempli</span><span><?= $placesRestantes ?>/<?= $evenement->getNbPlacesMax() ?> libres</span></div>
            <?php if ($placesRestantes <= 5 && $placesRestantes > 0): ?>
            <div style="background:#fff3e0;padding:10px;border-radius:10px;margin-top:12px;font-size:.82em;color:#e65100;display:flex;align-items:center;gap:8px">
                <i class="fas fa-fire"></i> <strong>Dernières places !</strong>
            </div>
            <?php endif; ?>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-star"></i> Ce que vous obtenez</h3>
            <ul class="avantages-list">
                <li><i class="fas fa-check"></i> Accès complet à l'événement</li>
                <li><i class="fas fa-check"></i> Certificat de participation</li>
                <li><i class="fas fa-check"></i> Supports et ressources offerts</li>
                <li><i class="fas fa-check"></i> Accès à la communauté NutriLoop</li>
                <li><i class="fas fa-check"></i> Conseils personnalisés en nutrition</li>
            </ul>
        </div>
    </div>
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
const maxPlaces = <?= max(1, $placesRestantes) ?>;

function showError(inputId, errId, show) {
    const input = document.getElementById(inputId);
    const err   = document.getElementById(errId);
    if (!input || !err) return;
    if (show) { input.classList.add('input-error'); input.classList.remove('input-ok'); err.classList.add('show'); }
    else       { input.classList.remove('input-error'); input.classList.add('input-ok'); err.classList.remove('show'); }
}

// MODIFICATION 3 : validation id_user
function validateIdUser() {
    const val = parseInt(document.getElementById('id_user').value);
    const ok  = !isNaN(val) && val >= 1;
    showError('id_user', 'err-iduser', !ok);
    return ok;
}

function validateNom() {
    const val = document.getElementById('nom').value.trim();
    showError('nom', 'err-nom', val.length < 3);
    return val.length >= 3;
}

function validateEmail() {
    const val = document.getElementById('email').value.trim();
    const ok  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
    showError('email', 'err-email', !ok);
    return ok;
}

function validateTelephone() {
    const val = document.getElementById('telephone').value.trim();
    if (val === '') { document.getElementById('telephone').classList.remove('input-error','input-ok'); document.getElementById('err-telephone').classList.remove('show'); return true; }
    const ok = /^[0-9+\s\-()\d]{6,20}$/.test(val);
    showError('telephone', 'err-telephone', !ok);
    return ok;
}

function validatePlaces() {
    const val = parseInt(document.getElementById('nb_places').value);
    const ok  = val >= 1 && val <= maxPlaces;
    showError('nb_places', 'err-places', !ok);
    return ok;
}

document.getElementById('id_user')?.addEventListener('input', validateIdUser);
document.getElementById('nom')?.addEventListener('input', validateNom);
document.getElementById('email')?.addEventListener('input', validateEmail);
document.getElementById('telephone')?.addEventListener('input', validateTelephone);
document.getElementById('nb_places')?.addEventListener('input', validatePlaces);

document.getElementById('inscriptionForm')?.addEventListener('submit', function(e) {
    const ok = validateIdUser() & validateNom() & validateEmail() & validateTelephone() & validatePlaces();
    if (!ok) {
        e.preventDefault();
        const firstError = document.querySelector('.input-error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu   = document.querySelector('.nav-menu');
    if (hamburger && navMenu) hamburger.addEventListener('click', () => { hamburger.classList.toggle('active'); navMenu.classList.toggle('active'); });
});
</script>
</body>
</html>