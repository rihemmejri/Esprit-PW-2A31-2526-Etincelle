<?php
require_once '../../controleurs/EvenementController.php';
require_once '../../controleurs/ParticipationController.php';

$error                   = "";
$participationController = new ParticipationController();
$evenementController     = new EvenementController();
$evenements              = $participationController->getAllEvenements();

$evenement_id = isset($_GET['evenement_id']) ? intval($_GET['evenement_id']) : null;
$evenement    = $evenement_id ? $evenementController->getEvenementById($evenement_id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_evenement        = $_POST['id_evenement']        ?? null;
    $id_user             = $_POST['id_user']             ?? null;
    $nom                 = $_POST['nom']                 ?? '';
    $email               = $_POST['email']               ?? '';
    $telephone           = $_POST['telephone']           ?? null;
    $statut              = $_POST['statut']              ?? 'EN_ATTENTE';
    $nb_places_reservees = !empty($_POST['nb_places_reservees']) ? intval($_POST['nb_places_reservees']) : 1;

    if (!empty($id_evenement) && !empty($id_user) && !empty($nom) && !empty($email)) {
        $ev = $evenementController->getEvenementById(intval($id_evenement));

        if ($ev && $ev->isPayant()) {
            $params = http_build_query([
                'id_evenement' => $id_evenement,
                'id_user'      => $id_user,
                'nom'          => $nom,
                'email'        => $email,
                'telephone'    => $telephone ?? '',
                'statut'       => $statut,
                'nb_places'    => $nb_places_reservees,
            ]);
            header('Location: paiementSimule.php?' . $params);
            exit;
        } else {
            $participation = new Participation(
                intval($id_evenement),
                intval($id_user),
                htmlspecialchars($nom),
                htmlspecialchars($email),
                !empty($telephone) ? htmlspecialchars($telephone) : null,
                $statut,
                null, null, null,
                $nb_places_reservees,
                'GRATUIT', null, 0.00
            );
            $participationController->addParticipation($participation, $ev);
            header('Location: participationList.php');
            exit;
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Participation - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/participation.css">
    <style>
        .input-error{border-color:#f44336!important;background:#fff5f5!important;}
        .input-ok{border-color:#4CAF50!important;background:#f5fff5!important;}
        .field-error{color:#f44336;font-size:.78rem;margin-top:5px;display:none;}
        .field-error.show{display:block;}

        /* ── BADGE PRIX ── */
        .prix-info-card{
            border-radius:14px;padding:18px 20px;margin-bottom:20px;
            display:flex;align-items:center;gap:16px;
        }
        .prix-info-card.gratuit{
            background:#e8f5e9;border:2px solid #4CAF50;
        }
        .prix-info-card.payant{
            background:#e3f2fd;border:2px solid #2196F3;
        }
        .prix-icon{
            width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px;
        }
        .prix-icon.gratuit{background:#4CAF50;color:white;}
        .prix-icon.payant{background:#2196F3;color:white;}
        .prix-details h3{font-size:16px;font-weight:700;margin-bottom:4px;}
        .prix-details h3.gratuit{color:#2e7d32;}
        .prix-details h3.payant{color:#1565c0;}
        .prix-details p{font-size:13px;color:#555;margin:0;}
        .prix-montant{margin-left:auto;text-align:right;}
        .prix-montant strong{font-size:24px;font-weight:700;display:block;}
        .prix-montant strong.gratuit{color:#2e7d32;}
        .prix-montant strong.payant{color:#1565c0;}
        .prix-montant span{font-size:11px;color:#888;}
        .prix-total-box{
            background:#fff3e0;border:2px solid #FFA726;border-radius:10px;
            padding:12px 16px;margin-top:8px;display:none;
            justify-content:space-between;align-items:center;
        }
        .prix-total-box.show{display:flex;}
        .prix-total-box .lbl{font-size:13px;color:#e65100;font-weight:600;}
        .prix-total-box .val{font-size:20px;font-weight:700;color:#e65100;}
    </style>
</head>
<body>
<div class="container-list">
    <div class="form-card">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Ajouter une participation</h1>
            <p>Inscrivez un utilisateur à un événement NutriLoop</p>
        </div>

        <?php if ($error): ?>
        <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-content">
            <form action="" method="POST" id="addParticipationForm" novalidate>
                <div class="form-grid">

                    <!-- Événement -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-calendar-alt"></i> Événement <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-calendar-check"></i>
                            <select name="id_evenement" id="evenementSelect" onchange="onEvenementChange(this)">
                                <option value="">-- Sélectionnez un événement --</option>
                                <?php foreach ($evenements as $ev): ?>
                                    <option value="<?= $ev['id_evenement'] ?>"
                                            data-prix="<?= $ev['prix'] ?? 0 ?>"
                                        <?= ($evenement_id == $ev['id_evenement']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ev['titre']) ?>
                                        <?= ($ev['prix'] > 0) ? ' — ' . number_format($ev['prix'], 2) . ' TND' : ' — Gratuit' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <span class="field-error" id="err-evenement">Veuillez sélectionner un événement.</span>
                    </div>

                    <!-- BADGE PRIX — affiché dynamiquement -->
                    <div class="form-group full-width" id="prixCardWrap" style="display:<?= $evenement ? 'block' : 'none' ?>;">
                        <?php if ($evenement && $evenement->isPayant()): ?>
                        <div class="prix-info-card payant" id="prixCard">
                            <div class="prix-icon payant"><i class="fas fa-credit-card"></i></div>
                            <div class="prix-details">
                                <h3 class="payant">Événement payant</h3>
                                <p>Vous serez redirigé vers la page de paiement après validation</p>
                            </div>
                            <div class="prix-montant">
                                <strong class="payant" id="prixVal"><?= number_format($evenement->getPrix(), 2) ?> TND</strong>
                                <span>par place</span>
                            </div>
                        </div>
                        <?php elseif ($evenement): ?>
                        <div class="prix-info-card gratuit" id="prixCard">
                            <div class="prix-icon gratuit"><i class="fas fa-gift"></i></div>
                            <div class="prix-details">
                                <h3 class="gratuit">Événement gratuit</h3>
                                <p>Inscription libre, aucun paiement requis</p>
                            </div>
                            <div class="prix-montant">
                                <strong class="gratuit" id="prixVal">Gratuit</strong>
                                <span>inscription libre</span>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="prix-info-card gratuit" id="prixCard" style="display:none;"></div>
                        <?php endif; ?>

                        <!-- Total dynamique -->
                        <div class="prix-total-box" id="prixTotalBox">
                            <span class="lbl"><i class="fas fa-coins"></i> Total à payer :</span>
                            <span class="val" id="prixTotalVal">0.00 TND</span>
                        </div>
                    </div>

                    <!-- ID User -->
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> ID Utilisateur <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="number" name="id_user" id="id_user" min="1" placeholder="Ex: 12">
                        </div>
                        <span class="field-error" id="err-iduser">L'ID utilisateur est obligatoire (min 1).</span>
                    </div>

                    <!-- Nom -->
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom complet <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-signature"></i>
                            <input type="text" name="nom" id="nom" placeholder="Nom du participant">
                        </div>
                        <span class="field-error" id="err-nom">Le nom est obligatoire (minimum 3 caractères).</span>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-at"></i>
                            <input type="email" name="email" id="email" placeholder="email@exemple.com">
                        </div>
                        <span class="field-error" id="err-email">Veuillez entrer un email valide.</span>
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone</label>
                        <div class="input-icon">
                            <i class="fas fa-mobile-alt"></i>
                            <input type="text" name="telephone" id="telephone" placeholder="+216 XX XXX XXX">
                        </div>
                        <span class="field-error" id="err-telephone">Numéro invalide.</span>
                    </div>

                    <!-- Places réservées -->
                    <div class="form-group">
                        <label><i class="fas fa-ticket-alt"></i> Places réservées <span class="required">*</span></label>
                        <div class="note-input">
                            <button type="button" onclick="updatePlaces(-1)">−</button>
                            <input type="number" name="nb_places_reservees" id="placesField" min="1" max="10" value="1" oninput="updateTotal()">
                            <button type="button" onclick="updatePlaces(1)">+</button>
                        </div>
                        <span class="field-error" id="err-places">Entre 1 et 10 places.</span>
                    </div>

                    
                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span id="submitTxt">Ajouter la participation</span>
                        </button>
                        <a href="<?= $evenement_id ? 'participationList.php' : 'evenementList.php' ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
let prixUnitaire = <?= $evenement ? $evenement->getPrix() : 0 ?>;

function onEvenementChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    prixUnitaire = parseFloat(opt.dataset.prix || 0);

    const wrap = document.getElementById('prixCardWrap');
    const card = document.getElementById('prixCard');
    const btn  = document.getElementById('submitTxt');

    wrap.style.display = sel.value ? 'block' : 'none';
    card.style.display = 'flex';

    if (prixUnitaire > 0) {
        card.className = 'prix-info-card payant';
        card.innerHTML = `
            <div class="prix-icon payant"><i class="fas fa-credit-card"></i></div>
            <div class="prix-details">
                <h3 class="payant">Événement payant</h3>
                <p>Vous serez redirigé vers la page de paiement après validation</p>
            </div>
            <div class="prix-montant">
                <strong class="payant">${prixUnitaire.toFixed(2)} TND</strong>
                <span>par place</span>
            </div>`;
        btn.textContent = 'Continuer vers le paiement';
    } else {
        card.className = 'prix-info-card gratuit';
        card.innerHTML = `
            <div class="prix-icon gratuit"><i class="fas fa-gift"></i></div>
            <div class="prix-details">
                <h3 class="gratuit">Événement gratuit</h3>
                <p>Inscription libre, aucun paiement requis</p>
            </div>
            <div class="prix-montant">
                <strong class="gratuit">Gratuit</strong>
                <span>inscription libre</span>
            </div>`;
        btn.textContent = 'Ajouter la participation';
    }
    updateTotal();
    validateEvenement();
}

function updatePlaces(delta) {
    const input = document.getElementById('placesField');
    const newVal = (parseInt(input.value) || 1) + delta;
    if (newVal >= 1 && newVal <= 10) input.value = newVal;
    updateTotal();
}

function updateTotal() {
    const places = parseInt(document.getElementById('placesField').value) || 1;
    const total  = prixUnitaire * places;
    const box    = document.getElementById('prixTotalBox');
    if (prixUnitaire > 0 && places > 1) {
        box.classList.add('show');
        document.getElementById('prixTotalVal').textContent = total.toFixed(2) + ' TND';
    } else {
        box.classList.remove('show');
    }
}

function showError(inputEl, errId, show) {
    const err = document.getElementById(errId);
    if (!inputEl || !err) return;
    if (show) { inputEl.classList.add('input-error'); inputEl.classList.remove('input-ok'); err.classList.add('show'); }
    else       { inputEl.classList.remove('input-error'); inputEl.classList.add('input-ok'); err.classList.remove('show'); }
}
function validateEvenement() { const s=document.getElementById('evenementSelect'); const ok=s&&s.value!==''; showError(s,'err-evenement',!ok); return ok; }
function validateIdUser()    { const i=document.getElementById('id_user'); const v=parseInt(i?.value); const ok=!isNaN(v)&&v>=1; showError(i,'err-iduser',!ok); return ok; }
function validateNom()       { const i=document.getElementById('nom'); const ok=i&&i.value.trim().length>=3; showError(i,'err-nom',!ok); return ok; }
function validateEmail()     { const i=document.getElementById('email'); const ok=i&&/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(i.value.trim()); showError(i,'err-email',!ok); return ok; }
function validatePlaces()    { const i=document.getElementById('placesField'); const v=parseInt(i?.value); const ok=!isNaN(v)&&v>=1&&v<=10; showError(i,'err-places',!ok); return ok; }
function validateTelephone() {
    const i=document.getElementById('telephone');
    if(!i||i.value.trim()===''){i?.classList.remove('input-error','input-ok');document.getElementById('err-telephone')?.classList.remove('show');return true;}
    const ok=/^[0-9+\s\-()]{6,20}$/.test(i.value.trim()); showError(i,'err-telephone',!ok); return ok;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('evenementSelect')?.addEventListener('change', validateEvenement);
    document.getElementById('id_user')?.addEventListener('input', validateIdUser);
    document.getElementById('nom')?.addEventListener('input', validateNom);
    document.getElementById('email')?.addEventListener('input', validateEmail);
    document.getElementById('telephone')?.addEventListener('input', validateTelephone);
    document.getElementById('placesField')?.addEventListener('input', validatePlaces);

    const sel = document.getElementById('evenementSelect');
    if (sel && sel.value) onEvenementChange(sel);

    document.getElementById('addParticipationForm')?.addEventListener('submit', function(e) {
        const ok = validateEvenement() & validateIdUser() & validateNom()
                 & validateEmail() & validateTelephone() & validatePlaces();
        if (!ok) { e.preventDefault(); document.querySelector('.input-error')?.scrollIntoView({behavior:'smooth',block:'center'}); }
    });
});
</script>
</body>
</html>