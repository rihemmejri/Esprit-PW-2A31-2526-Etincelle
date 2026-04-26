<?php
include '../../controleurs/EvenementController.php';
require_once __DIR__ . '/../../models/Evenement.php';

$evenementController = new EvenementController();
$evenements = $evenementController->listEvenements();

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
        @media(max-width:768px){
            .nav-menu { position:fixed; left:-100%; top:70px; flex-direction:column; background:white; width:100%; text-align:center; transition:.3s; padding:20px 0; gap:15px; box-shadow:0 10px 20px rgba(0,0,0,.1); }
            .nav-menu.active { left:0; }
            .hamburger { display:flex; }
        }

        .hero-section { background:linear-gradient(135deg,#e3f2fd,#e8f5e9); padding:80px 20px; text-align:center; border-radius:30px; margin:40px 20px; }
        .hero-section h1 { font-size:2.5rem; color:#1565C0; margin-bottom:15px; }
        .hero-section p  { font-size:1.1rem; color:#555; }

        .filters-section { background:white; padding:25px; border-radius:20px; margin:0 20px 30px; box-shadow:0 5px 20px rgba(0,0,0,.05); }
        .filters-title { font-size:1.1rem; font-weight:600; color:#1565C0; margin-bottom:18px; }
        .filters-grid { display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end; }
        .filter-group { flex:1; min-width:180px; }
        .filter-group label { display:block; margin-bottom:6px; font-weight:500; color:#333; font-size:.9em; }
        .filter-group input, .filter-group select { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:10px; font-size:14px; font-family:'Poppins',sans-serif; transition:.3s; }
        .filter-group input:focus, .filter-group select:focus { outline:none; border-color:#2196F3; box-shadow:0 0 0 3px rgba(33,150,243,.1); }

        /* Tri */
        .sort-bar { display:flex; align-items:center; gap:10px; margin:0 20px 20px; flex-wrap:wrap; }
        .sort-bar span { font-size:.9em; color:#666; font-weight:500; }
        .sort-btn { padding:7px 16px; border-radius:20px; border:1px solid #ddd; background:white; cursor:pointer; font-size:.82em; font-family:'Poppins',sans-serif; transition:.3s; display:inline-flex; align-items:center; gap:5px; }
        .sort-btn:hover { border-color:#2196F3; color:#2196F3; }
        .sort-btn.active { background:#2196F3; color:white; border-color:#2196F3; }
        .sort-btn i { font-size:.85em; }

        .events-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:30px; padding:0 20px 60px; }

        .event-card { background:white; border-radius:20px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,.08); transition:all .3s ease; cursor:pointer; }
        .event-card:hover { transform:translateY(-8px); box-shadow:0 20px 40px rgba(0,0,0,.15); }

        .event-image { height:200px; position:relative; overflow:hidden; }
        .event-image img { width:100%; height:100%; object-fit:cover; transition:transform .3s; }
        .event-card:hover .event-image img { transform:scale(1.05); }

        .event-type-badge { position:absolute; top:15px; left:15px; padding:5px 13px; border-radius:20px; font-size:.72em; font-weight:700; color:white; }
        .badge-SPORT     { background:#1565c0; }
        .badge-NUTRITION { background:#2e7d32; }
        .badge-WORKSHOP  { background:#e65100; }
        .badge-AUTRE     { background:#6a1b9a; }

        .event-statut-badge { position:absolute; top:15px; right:15px; padding:5px 12px; border-radius:20px; font-size:.72em; font-weight:700; }
        .stt-ACTIF     { background:#e8f5e9; color:#2e7d32; }
        .stt-CANCELLED { background:#ffebee; color:#c62828; }
        .stt-COMPLETED { background:#e8eaf6; color:#3949ab; }

        .event-content { padding:20px; }
        .event-title { font-size:1.2em; font-weight:700; color:#333; margin-bottom:10px; }
        .event-meta { display:flex; gap:14px; margin-bottom:12px; flex-wrap:wrap; }
        .event-meta span { font-size:.82em; color:#666; display:flex; align-items:center; gap:5px; }
        .event-meta i { color:#2196F3; }
        .event-description { color:#666; line-height:1.6; margin-bottom:18px; font-size:.88em; }
        .event-footer { display:flex; justify-content:space-between; align-items:center; padding-top:14px; border-top:1px solid #eee; }

        .places-badge { padding:5px 12px; border-radius:20px; font-size:.8em; font-weight:600; }
        .places-ok   { background:#e8f5e9; color:#2e7d32; }
        .places-peu  { background:#fff3e0; color:#e65100; }
        .places-full { background:#ffebee; color:#c62828; }

        .btn-inscrire { background:linear-gradient(135deg,#2196F3,#4CAF50); color:white; padding:9px 20px; border-radius:25px; border:none; cursor:pointer; font-size:.85em; font-weight:600; font-family:'Poppins',sans-serif; display:inline-flex; align-items:center; gap:7px; transition:all .3s; text-decoration:none; }
        .btn-inscrire:hover { transform:translateX(5px); box-shadow:0 5px 15px rgba(33,150,243,.3); }

        .no-results { text-align:center; padding:60px; background:white; border-radius:20px; grid-column:1/-1; }
        .no-results i { font-size:64px; color:#ccc; margin-bottom:18px; display:block; }

        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.55); z-index:1000; justify-content:center; align-items:center; }
        .modal-content { background:white; max-width:580px; width:92%; border-radius:20px; overflow:hidden; animation:slideIn .3s ease; max-height:92vh; overflow-y:auto; }
        @keyframes slideIn { from { transform:translateY(-50px); opacity:0; } to { transform:translateY(0); opacity:1; } }
        .modal-header { background:linear-gradient(135deg,#2196F3,#4CAF50); color:white; padding:20px 24px; display:flex; justify-content:space-between; align-items:center; }
        .modal-header h3 { margin:0; font-size:1.2rem; display:flex; align-items:center; gap:10px; }
        .modal-close { background:none; border:none; color:white; font-size:28px; cursor:pointer; }
        .modal-body { padding:24px; }
        .modal-detail { margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #eee; }
        .modal-detail strong { display:inline-block; width:130px; color:#1565C0; }
        .btn-s-inscrire { background:linear-gradient(135deg,#2196F3,#4CAF50); color:white; padding:11px 24px; border-radius:25px; text-decoration:none; display:inline-flex; align-items:center; gap:9px; margin-top:16px; font-weight:600; transition:.3s; }
        .btn-s-inscrire:hover { transform:translateX(5px); }

        .footer { background:#1a1a2e; color:white; padding:40px 20px 20px; margin-top:60px; }
        .footer-content { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:28px; }
        .footer-section h3,.footer-section h4 { margin-bottom:14px; }
        .footer-section ul { list-style:none; }
        .footer-section ul li { margin-bottom:8px; }
        .footer-section a { color:#ccc; text-decoration:none; }
        .footer-section a:hover { color:#4CAF50; }
        .social-links { display:flex; gap:14px; margin-top:14px; }
        .social-links a { background:rgba(255,255,255,.12); width:35px; height:35px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:.3s; }
        .social-links a:hover { background:#4CAF50; }
        .footer-bottom { text-align:center; padding-top:25px; margin-top:25px; border-top:1px solid rgba(255,255,255,.1); font-size:.84em; }

        @media(max-width:768px){ .events-grid { grid-template-columns:1fr; } .filters-grid { flex-direction:column; } }
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

<!-- FILTRES -->
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
    </div>
</div>



<!-- GRILLE -->
<div class="events-grid" id="eventsGrid">
<?php if (count($evenements) > 0): ?>
    <?php foreach ($evenements as $ev):
        $imgUrl = $images[$ev->getTypeEvenement()] ?? $images['AUTRE'];
    ?>
    <div class="event-card"
         data-titre="<?= strtolower(htmlspecialchars($ev->getTitre())) ?>"
         data-type="<?= $ev->getTypeEvenement() ?>"
         data-statut="<?= $ev->getStatut() ?>"
         data-date="<?= $ev->getDateEvenement() ?>"
         data-places="<?= $ev->getNbPlacesMax() ?>"
         data-lieu="<?= strtolower(htmlspecialchars($ev->getLieu())) ?>">

        <div class="event-image">
            <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($ev->getTitre()) ?>">
            <span class="event-type-badge badge-<?= $ev->getTypeEvenement() ?>"><?= $ev->getTypeEvenement() ?></span>
            <span class="event-statut-badge stt-<?= $ev->getStatut() ?>"><?= $ev->getStatut() ?></span>
        </div>

        <div class="event-content">
            <h3 class="event-title"><?= htmlspecialchars($ev->getTitre()) ?></h3>
            <div class="event-meta">
                <span><i class="fas fa-calendar-day"></i> <?= date('d/m/Y', strtotime($ev->getDateEvenement())) ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($ev->getLieu()) ?></span>
                <span><i class="fas fa-users"></i> <?= $ev->getNbPlacesMax() ?> places</span>
            </div>
            <p class="event-description">
                <?= htmlspecialchars(substr($ev->getDescription(), 0, 110)) ?><?= strlen($ev->getDescription()) > 110 ? '…' : '' ?>
            </p>
            <div class="event-footer">
                <span class="places-badge places-ok">
                    <i class="fas fa-ticket-alt"></i> <?= $ev->getNbPlacesMax() ?> places
                </span>
                <?php if ($ev->getStatut() === 'ACTIF'): ?>
                <button class="btn-inscrire"
                    onclick='showEventModal(<?= json_encode([
                        "id"          => $ev->getIdEvenement(),
                        "titre"       => $ev->getTitre(),
                        "type"        => $ev->getTypeEvenement(),
                        "date"        => date("d/m/Y", strtotime($ev->getDateEvenement())),
                        "lieu"        => $ev->getLieu(),
                        "places"      => $ev->getNbPlacesMax(),
                        "description" => $ev->getDescription()
                    ]) ?>)'>
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
                <?php else: ?>
                <span style="color:#aaa;font-size:.84em;font-style:italic"><i class="fas fa-lock"></i> Non disponible</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-results">
        <i class="fas fa-calendar-times"></i>
        <h3>Aucun événement disponible</h3>
    </div>
<?php endif; ?>
</div>

<div id="noResults" style="display:none;margin:0 20px 40px">
    <div class="no-results" style="grid-column:unset">
        <i class="fas fa-search"></i>
        <h3>Aucun événement trouvé</h3>
        <p>Modifiez vos critères de recherche.</p>
    </div>
</div>

<!-- MODAL -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-check"></i> Détails de l'événement</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
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
// ===== FILTRES =====
function filterEvenements() {
    const search = document.getElementById('searchEvenement').value.toLowerCase();
    const type   = document.getElementById('filterType').value;
    const statut = document.getElementById('filterStatut').value;
    const cards  = document.querySelectorAll('.event-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const titre      = card.getAttribute('data-titre') || '';
        const cardType   = card.getAttribute('data-type')  || '';
        const cardStatut = card.getAttribute('data-statut')|| '';
        const lieu       = card.getAttribute('data-lieu')  || '';

        let match = true;
        if (search  && !titre.includes(search)) match = false;
        if (type    && cardType   !== type)   match = false;
        if (statut  && cardStatut !== statut) match = false;

        if (match) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
}

// ===== TRI =====
let sortDir = { date: 'asc', titre: 'asc', places: 'desc' };
let currentSort = 'date';

function sortEvenements(criterion) {
    // Inverser direction si même critère
    if (currentSort === criterion) {
        sortDir[criterion] = sortDir[criterion] === 'asc' ? 'desc' : 'asc';
    }
    currentSort = criterion;

    // Mettre à jour boutons
    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('sort-' + criterion).classList.add('active');

    const grid  = document.getElementById('eventsGrid');
    const cards = Array.from(grid.querySelectorAll('.event-card'));

    cards.sort((a, b) => {
        let aVal, bVal;
        if (criterion === 'date') {
            aVal = new Date(a.dataset.date);
            bVal = new Date(b.dataset.date);
            return sortDir[criterion] === 'asc' ? aVal - bVal : bVal - aVal;
        } else if (criterion === 'titre') {
            aVal = a.dataset.titre;
            bVal = b.dataset.titre;
            return sortDir[criterion] === 'asc'
                ? aVal.localeCompare(bVal, 'fr')
                : bVal.localeCompare(aVal, 'fr');
        } else if (criterion === 'places') {
            aVal = parseInt(a.dataset.places);
            bVal = parseInt(b.dataset.places);
            return sortDir[criterion] === 'asc' ? aVal - bVal : bVal - aVal;
        }
    });

    cards.forEach(card => grid.appendChild(card));
}

// ===== MODAL =====
function showEventModal(ev) {
    const typeTexts = { SPORT:'🏃 Sport', NUTRITION:'🥗 Nutrition', WORKSHOP:'📚 Workshop', AUTRE:'📅 Autre' };
    document.getElementById('modalBody').innerHTML = `
        <div class="modal-detail"><strong><i class="fas fa-heading"></i> Titre :</strong> ${ev.titre}</div>
        <div class="modal-detail"><strong><i class="fas fa-tag"></i> Type :</strong> ${typeTexts[ev.type] || ev.type}</div>
        <div class="modal-detail"><strong><i class="fas fa-calendar-day"></i> Date :</strong> ${ev.date}</div>
        <div class="modal-detail"><strong><i class="fas fa-map-marker-alt"></i> Lieu :</strong> ${ev.lieu}</div>
        <div class="modal-detail"><strong><i class="fas fa-users"></i> Places :</strong> ${ev.places} places disponibles</div>
        <div class="modal-detail"><strong><i class="fas fa-align-left"></i> Description :</strong><br>${ev.description}</div>
        <div style="text-align:center">
            <a href="afficherParticipation.php?id=${ev.id}" class="btn-s-inscrire">
                <i class="fas fa-user-plus"></i> S'inscrire à cet événement
            </a>
        </div>
    `;
    document.getElementById('eventModal').style.display = 'flex';
}

function closeModal() { document.getElementById('eventModal').style.display = 'none'; }
window.onclick = e => { if (e.target === document.getElementById('eventModal')) closeModal(); };

document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu   = document.querySelector('.nav-menu');
    if (hamburger && navMenu) hamburger.addEventListener('click', () => { hamburger.classList.toggle('active'); navMenu.classList.toggle('active'); });
});
</script>
</body>
</html>