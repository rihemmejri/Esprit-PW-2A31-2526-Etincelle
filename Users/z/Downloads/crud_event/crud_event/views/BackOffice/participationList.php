<?php
require_once '../../controleurs/ParticipationController.php';

$participationController = new ParticipationController();
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $allParticipations = $participationController->listParticipations();
    $participations = array_filter($allParticipations, function($p) use ($search) {
        return stripos($p->getEvenementTitre(), $search) !== false
            || stripos($p->getStatut(), $search) !== false
            || stripos($p->getNom(), $search) !== false
            || stripos($p->getEmail(), $search) !== false;
    });
} else {
    $participations = $participationController->listParticipations();
}

usort($participations, function($a, $b) {
    return strtotime($b->getDateInscription()) - strtotime($a->getDateInscription());
});

$totalAttente   = count(array_filter($participations, fn($p) => $p->getStatut() === 'EN_ATTENTE'));
$totalConfirmee = count(array_filter($participations, fn($p) => $p->getStatut() === 'CONFIRMEE'));
$totalAnnulee   = count(array_filter($participations, fn($p) => $p->getStatut() === 'ANNULEE'));
$totalPresente  = count(array_filter($participations, fn($p) => $p->getStatut() === 'PRESENTE'));
$totalPlaces    = array_sum(array_map(fn($p) => $p->getNbPlacesReservees() ?? 1, $participations));
$notes          = array_filter(array_map(fn($p) => $p->getNote(), $participations));
$moyenneNote    = count($notes) > 0 ? round(array_sum($notes) / count($notes), 1) : 0;
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
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:15px; }
        .header h1 { font-size:1.8rem; color:#1a1a2e; }
        .header h1 i { color:#4CAF50; margin-right:10px; }
        .btn-group { display:flex; gap:12px; flex-wrap:wrap; }
        .btn { padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600; transition:0.3s; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer; font-family:'Segoe UI',sans-serif; }
        .btn-primary   { background:#4CAF50; color:white; }
        .btn-primary:hover { background:#45a049; transform:translateY(-2px); }
        .btn-secondary { background:#003366; color:white; }
        .btn-secondary:hover { background:#002244; transform:translateY(-2px); }
        .btn-stats { background: linear-gradient(135deg, #9b59b6, #3498db); color:white; }
        .btn-stats:hover { opacity:0.9; transform:translateY(-2px); box-shadow:0 8px 20px rgba(155,89,182,0.4); }

        .stats-bar { display:flex; justify-content:space-between; align-items:center; background:white; padding:15px 25px; border-radius:12px; margin-bottom:25px; flex-wrap:wrap; gap:15px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
        .stats { display:flex; gap:25px; flex-wrap:wrap; }
        .stat { display:flex; align-items:center; gap:8px; }
        .stat i { font-size:1.2rem; color:#4CAF50; }

        .search-box { display:flex; gap:5px; flex-wrap:wrap; }
        .search-box input { padding:8px 15px; border:1px solid #ddd; border-radius:8px; font-family:'Segoe UI',sans-serif; }
        .search-box input[type="text"]   { width:220px; }
        .search-box input[type="number"] { width:140px; }
        .search-box button { background:#4CAF50; border:none; padding:8px 15px; border-radius:8px; color:white; cursor:pointer; }

        .filter-bar { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
        .filter-bar select { padding:8px 15px; border:1px solid #ddd; border-radius:8px; font-family:'Segoe UI',sans-serif; cursor:pointer; background:white; }

        .table-container { background:white; border-radius:16px; overflow:auto; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        table { width:100%; border-collapse:collapse; min-width:1200px; }
        th { background:#1a1a2e; color:white; padding:15px 12px; text-align:left; font-weight:600; cursor:pointer; user-select:none; transition:background 0.2s; }
        th:hover { background:#2d2d4e; }
        th i { margin-right:8px; }
        th .sort-icon { float:right; opacity:0.5; font-size:0.75em; }
        th.sorted .sort-icon { opacity:1; color:#4CAF50; }
        td { padding:12px; border-bottom:1px solid #eee; vertical-align:middle; }
        tr:hover { background:#f8f9fa; }
        .id-cell { font-weight:700; color:#2196F3; }
        .event-link { color:#2196F3; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:5px; }
        .event-link:hover { text-decoration:underline; }
        .statut-badge { display:inline-block; padding:5px 12px; border-radius:20px; font-size:0.75rem; font-weight:600; }
        .statut-EN_ATTENTE { background:#fff3e0; color:#e65100; }
        .statut-CONFIRMEE  { background:#e8f5e9; color:#2e7d32; }
        .statut-ANNULEE    { background:#ffebee; color:#c62828; }
        .statut-PRESENTE   { background:#e3f2fd; color:#1565c0; }
        .note-stars { color:#FFC107; }
        .actions { display:flex; gap:8px; }
        .action-btn { padding:6px 12px; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600; transition:0.2s; border:none; cursor:pointer; }
        .edit-btn   { background:#2196F3; color:white; }
        .delete-btn { background:#dc3545; color:white; }
        .edit-btn:hover   { background:#1976D2; }
        .delete-btn:hover { background:#c82333; }
        .empty-message { text-align:center; padding:60px; color:#999; }
        .empty-message i { font-size:3rem; display:block; margin-bottom:15px; }
        .footer { margin-top:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
        .pagination { display:flex; gap:8px; }
        .page-btn { width:40px; height:40px; border:1px solid #ddd; background:white; border-radius:8px; cursor:pointer; transition:0.2s; }
        .page-btn.active { background:#4CAF50; color:white; border-color:#4CAF50; }
        .page-btn:hover:not(.active) { background:#f0f0f0; }
        .export-group { display:flex; gap:10px; }
        .pdf-btn { background:#c0392b; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; font-family:'Segoe UI',sans-serif; transition:0.3s; }
        .pdf-btn:hover { background:#a93226; }

        /* Modal statistiques */
        .stats-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center; }
        .stats-modal.open { display:flex; animation: fadeInModal 0.3s ease; }
        @keyframes fadeInModal { from { opacity:0; } to { opacity:1; } }
        .stats-modal-content { background:white; border-radius:24px; padding:36px; width:90%; max-width:620px; box-shadow:0 30px 80px rgba(0,0,0,0.3); animation: slideUpModal 0.4s cubic-bezier(0.34,1.56,0.64,1); position:relative; }
        @keyframes slideUpModal { from { transform:translateY(60px); opacity:0; } to { transform:translateY(0); opacity:1; } }
        .stats-modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
        .stats-modal-header h2 { color:#1a1a2e; font-size:1.4rem; display:flex; align-items:center; gap:10px; }
        .stats-modal-header h2 i { color:#9b59b6; }
        .stats-modal-close { background:none; border:none; font-size:1.6rem; color:#999; cursor:pointer; transition:0.2s; }
        .stats-modal-close:hover { color:#f44336; transform:scale(1.2); }
        .stats-donut-wrapper { display:flex; align-items:center; gap:36px; flex-wrap:wrap; }
        .donut-container { position:relative; width:220px; height:220px; flex-shrink:0; margin:0 auto; }
        .donut-svg { width:220px; height:220px; transform:rotate(-90deg); }
        .donut-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; }
        .donut-center .total-num { font-size:2.2rem; font-weight:800; color:#1a1a2e; line-height:1; }
        .donut-center .total-label { font-size:0.78rem; color:#999; margin-top:4px; text-transform:uppercase; letter-spacing:0.5px; }
        .stats-legend { flex:1; min-width:180px; }
        .legend-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:10px; margin-bottom:8px; transition:background 0.2s; }
        .legend-item:hover { background:#f5f5f5; }
        .legend-dot { width:14px; height:14px; border-radius:50%; flex-shrink:0; }
        .legend-info { flex:1; }
        .legend-name { font-size:0.85rem; font-weight:600; color:#333; }
        .legend-val  { font-size:0.78rem; color:#888; }
        .legend-pct  { font-size:0.85rem; font-weight:700; margin-left:auto; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1><i class="fas fa-users"></i> Gestion des Participations</h1>
        <div class="btn-group">
            <a href="addParticipation.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Ajouter une participation
            </a>
            <a href="evenementList.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux événements
            </a>
            <button class="btn btn-stats" onclick="openStats()">
                <i class="fas fa-chart-pie"></i> Statistiques
            </button>
        </div>
    </div>

    <div class="stats-bar">
        <div class="stats">
            <div class="stat"><i class="fas fa-users"></i><span><strong><?= count($participations) ?></strong> participations</span></div>
        </div>
        <div class="search-box">
            <input type="number" id="searchId" placeholder="🔢 Rechercher par ID..." onkeyup="filterTable()" min="1">
            <input type="text" id="searchInput" placeholder="🔍 Nom, email..." onkeyup="filterTable()" value="<?= htmlspecialchars($search) ?>">
            <button onclick="filterTable()"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <div class="filter-bar">
        <select id="filterStatut" onchange="filterTable()">
            <option value="">🔘 Tous les statuts</option>
            <option value="EN_ATTENTE">⏳ En attente</option>
            <option value="CONFIRMEE">✅ Confirmée</option>
            <option value="ANNULEE">❌ Annulée</option>
            <option value="PRESENTE">🎯 Présente</option>
        </select>
    </div>

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
                    <th onclick="sortTable(6)"><i class="fas fa-circle"></i> Statut <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
                    <th onclick="sortTable(7)"><i class="fas fa-calendar-check"></i> Date inscription <span class="sort-icon"><i class="fas fa-sort"></i></span></th>
                    <th><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($participations) > 0): ?>
                <?php foreach ($participations as $p): ?>
                <tr data-statut="<?= $p->getStatut() ?>" data-id="<?= $p->getIdParticipation() ?>">
                    <td class="id-cell">#<?= $p->getIdParticipation() ?></td>
                    <td>
                        <a href="viewEvenement.php?id=<?= $p->getIdEvenement() ?>" class="event-link">
                            <i class="fas fa-calendar-alt"></i>
                            <?= htmlspecialchars($p->getEvenementTitre() ?: 'N/A') ?>
                        </a>
                    </td>
                    <td><strong><?= htmlspecialchars($p->getNom() ?: '—') ?></strong></td>
                    <td><?= htmlspecialchars($p->getEmail() ?: '—') ?></td>
                    <td><?= htmlspecialchars($p->getTelephone() ?: '—') ?></td>
                    <td><strong><?= $p->getNbPlacesReservees() ?? 1 ?></strong></td>
                    <td><span class="statut-badge statut-<?= $p->getStatut() ?>"><?= $p->getStatut() ?></span></td>
                    <td><?= $p->getDateInscription() ? date('d/m/Y H:i', strtotime($p->getDateInscription())) : '—' ?></td>
                    <td>
                        <div class="actions">
                            <a href="editParticipation.php?id=<?= $p->getIdParticipation() ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i></a>
                            <a href="#" class="action-btn delete-btn" onclick="confirmDelete(<?= $p->getIdParticipation() ?>); return false;"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="empty-message"><i class="fas fa-users"></i><p>Aucune participation trouvée</p></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="pagination">
            <button class="page-btn" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
            <button class="page-btn active" id="page1">1</button>
            <button class="page-btn" id="page2" style="display:none">2</button>
            <button class="page-btn" id="page3" style="display:none">3</button>
            <button class="page-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="export-group">
            <button class="pdf-btn" onclick="exportPDF()">
                <i class="fas fa-file-pdf"></i> Exporter PDF
            </button>
        </div>
    </div>

</div>

<!-- MODAL STATISTIQUES -->
<div class="stats-modal" id="statsModal" onclick="closeStats(event)">
    <div class="stats-modal-content">
        <div class="stats-modal-header">
            <h2><i class="fas fa-chart-pie"></i> Statistiques des Participations</h2>
            <button class="stats-modal-close" onclick="closeStats()">&times;</button>
        </div>
        <div class="stats-donut-wrapper">
            <div class="donut-container">
                <svg class="donut-svg" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#f0f0f0" stroke-width="18"/>
                    <g id="donutSegments"></g>
                </svg>
                <div class="donut-center">
                    <span class="total-num"><?= count($participations) ?></span>
                    <span class="total-label">Total</span>
                </div>
            </div>
            <div class="stats-legend" id="statsLegend"></div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('⚠️ Supprimer cette participation ?')) {
            window.location.href = 'deleteParticipation.php?id=' + id + '&confirm=yes';
        }
    }

    // ===== RECHERCHE + FILTRES =====
    function filterTable() {
        const searchId    = document.getElementById('searchId').value.trim();
        const searchTexte = document.getElementById('searchInput').value.toLowerCase().trim();
        const statut      = document.getElementById('filterStatut').value;
        const rows        = document.querySelectorAll('#participationsTable tbody tr');

        rows.forEach(row => {
            const rowId     = row.getAttribute('data-id') || '';
            const rowStatut = row.getAttribute('data-statut') || '';
            const text      = row.textContent.toLowerCase();

            const matchId     = !searchId    || rowId === searchId;
            const matchTexte  = !searchTexte || text.includes(searchTexte);
            const matchStatut = !statut      || rowStatut === statut;

            row.style.display = (matchId && matchTexte && matchStatut) ? '' : 'none';
        });
    }

    // ===== TRI =====
    let sortDir = {};
    function sortTable(colIndex) {
        const table = document.getElementById('participationsTable');
        const rows  = Array.from(table.querySelectorAll('tbody tr'));
        const ths   = table.querySelectorAll('th');

        sortDir[colIndex] = !sortDir[colIndex];

        rows.sort((a, b) => {
            const aVal = a.cells[colIndex]?.textContent.trim() || '';
            const bVal = b.cells[colIndex]?.textContent.trim() || '';
            return sortDir[colIndex]
                ? aVal.localeCompare(bVal, 'fr', {numeric: true})
                : bVal.localeCompare(aVal, 'fr', {numeric: true});
        });

        const tbody = table.querySelector('tbody');
        rows.forEach(row => tbody.appendChild(row));

        ths.forEach(th => { th.classList.remove('sorted'); const icon = th.querySelector('.sort-icon i'); if (icon) icon.className = 'fas fa-sort'; });
        ths[colIndex].classList.add('sorted');
        const icon = ths[colIndex].querySelector('.sort-icon i');
        if (icon) icon.className = sortDir[colIndex] ? 'fas fa-sort-up' : 'fas fa-sort-down';
        showPage(1);
    }

    // ===== PAGINATION =====
    let currentPage = 1;
    const rowsPerPage = 10;

    function showPage(page) {
        const rows = document.querySelectorAll('#participationsTable tbody tr');
        const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
        const totalPages  = Math.ceil(visibleRows.length / rowsPerPage);

        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages || 1;

        visibleRows.forEach((row, index) => {
            row.style.display = (index >= (page-1)*rowsPerPage && index < page*rowsPerPage) ? '' : 'none';
        });

        currentPage = page;
        for (let i = 1; i <= 3; i++) {
            const btn = document.getElementById('page' + i);
            if (btn) {
                btn.classList.toggle('active', i === currentPage);
                btn.style.display = i <= totalPages ? 'inline-block' : 'none';
                btn.textContent = i;
                btn.onclick = () => showPage(i);
            }
        }
    }

    function previousPage() { showPage(currentPage - 1); }
    function nextPage()     { showPage(currentPage + 1); }

    // ===== STATISTIQUES DONUT =====
    const STATS_PART = [
        { label: 'En attente',  value: <?= $totalAttente ?>,   color: '#ff9800' },
        { label: 'Confirmées',  value: <?= $totalConfirmee ?>,  color: '#4CAF50' },
        { label: 'Annulées',    value: <?= $totalAnnulee ?>,    color: '#f44336' },
        { label: 'Présentes',   value: <?= $totalPresente ?>,   color: '#2196F3' },
    ];
    const TOTAL_PART = <?= count($participations) ?>;

    function openStats() {
        document.getElementById('statsModal').classList.add('open');
        drawDonut();
    }

    function closeStats(e) {
        if (!e || e.target === document.getElementById('statsModal')) {
            document.getElementById('statsModal').classList.remove('open');
        }
    }

    function drawDonut() {
        const seg  = document.getElementById('donutSegments');
        const leg  = document.getElementById('statsLegend');
        seg.innerHTML = '';
        leg.innerHTML = '';

        const r = 40, cx = 50, cy = 50;
        const circ = 2 * Math.PI * r;
        let offset = 0;

        STATS_PART.forEach((item, i) => {
            if (item.value <= 0) return;
            const pct  = item.value / (TOTAL_PART || 1);
            const dash = pct * circ;
            const gap  = circ - dash;

            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', cx);
            circle.setAttribute('cy', cy);
            circle.setAttribute('r', r);
            circle.setAttribute('fill', 'none');
            circle.setAttribute('stroke', item.color);
            circle.setAttribute('stroke-width', '18');
            circle.setAttribute('stroke-dasharray', `0 ${circ}`);
            circle.setAttribute('stroke-dashoffset', -offset);
            circle.style.transition = `stroke-dasharray 0.8s cubic-bezier(0.34,1.56,0.64,1) ${i * 0.15}s`;
            seg.appendChild(circle);

            setTimeout(() => {
                circle.setAttribute('stroke-dasharray', `${dash} ${gap}`);
            }, 80);

            offset += dash;

            const pctStr = Math.round(pct * 100) + '%';
            leg.innerHTML += `
                <div class="legend-item">
                    <div class="legend-dot" style="background:${item.color}"></div>
                    <div class="legend-info">
                        <div class="legend-name">${item.label}</div>
                        <div class="legend-val">${item.value} participation(s)</div>
                    </div>
                    <span class="legend-pct" style="color:${item.color}">${pctStr}</span>
                </div>`;
        });
    }

    // ===== EXPORT PDF =====
    function exportPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
        const pageW = doc.internal.pageSize.getWidth();
        const pageH = doc.internal.pageSize.getHeight();
        const now   = new Date().toLocaleDateString('fr-FR', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});

        doc.setFillColor(26,26,46); doc.rect(0,0,pageW,22,'F');
        doc.setTextColor(255,255,255); doc.setFontSize(16); doc.setFont('helvetica','bold');
        doc.text('NutriLoop', 12, 13);
        doc.setFontSize(12); doc.setFont('helvetica','normal');
        doc.text('Rapport des Participations', pageW/2, 13, {align:'center'});
        doc.setFontSize(9); doc.text('Genere le : ' + now, pageW-12, 13, {align:'right'});

        const statsData = [
            { label:'Total',      value:'<?= count($participations) ?>', color:[76,175,80] },
            { label:'Confirmees', value:'<?= $totalConfirmee ?>',        color:[33,150,243] },
            { label:'Annulees',   value:'<?= $totalAnnulee ?>',          color:[244,67,54] },
            { label:'Presentes',  value:'<?= $totalPresente ?>',         color:[57,73,171] },
            { label:'Places',     value:'<?= $totalPlaces ?>',           color:[255,193,7] },
        ];
        let sx = 12;
        statsData.forEach(s => {
            doc.setFillColor(...s.color); doc.roundedRect(sx,27,52,16,3,3,'F');
            doc.setTextColor(255,255,255); doc.setFontSize(13); doc.setFont('helvetica','bold');
            doc.text(s.value, sx+26, 35, {align:'center'});
            doc.setFontSize(7.5); doc.setFont('helvetica','normal');
            doc.text(s.label, sx+26, 40, {align:'center'});
            sx += 56;
        });

        const headers   = ['ID','Evenement','Nom','Email','Tel','Places','Statut','Date inscription','Note'];
        const colWidths = [12,45,35,45,25,15,25,35,18];
        let y = 50; const rowH = 9;

        doc.setFillColor(26,26,46); doc.rect(12,y,pageW-24,rowH,'F');
        doc.setTextColor(255,255,255); doc.setFontSize(7.5); doc.setFont('helvetica','bold');
        let x = 14; headers.forEach((h,i) => { doc.text(h,x,y+6); x+=colWidths[i]; }); y+=rowH;

        const rows = document.querySelectorAll('#participationsTable tbody tr');
        let rowCount = 0;
        rows.forEach(row => {
            if (row.style.display==='none') return;
            const cells = row.querySelectorAll('td'); if (!cells.length) return;
            if (rowCount%2===0) { doc.setFillColor(248,249,250); doc.rect(12,y,pageW-24,rowH,'F'); }
            doc.setDrawColor(220,220,220); doc.line(12,y+rowH,pageW-12,y+rowH);

            const values = [
                cells[0]?.innerText.replace('#','').trim()||'',
                cells[1]?.innerText.trim()||'',
                cells[2]?.innerText.trim()||'',
                cells[3]?.innerText.trim()||'',
                cells[4]?.innerText.trim()||'',
                cells[5]?.innerText.trim()||'',
                cells[6]?.innerText.trim()||'',
                cells[7]?.innerText.trim()||'',
                cells[8]?.innerText.replace(/★/g,'*').trim()||'',
            ];

            x = 14;
            values.forEach((val,i) => {
                if (i===6) {
                    if (val.includes('CONFIRMEE')) doc.setTextColor(46,125,50);
                    else if (val.includes('ANNULEE')) doc.setTextColor(198,40,40);
                    else if (val.includes('PRESENTE')) doc.setTextColor(21,101,192);
                    else doc.setTextColor(230,81,0);
                    doc.setFont('helvetica','bold');
                } else if (i===0) { doc.setTextColor(33,150,243); doc.setFont('helvetica','bold'); }
                else if (i===8) { doc.setTextColor(255,160,0); doc.setFont('helvetica','bold'); }
                else { doc.setTextColor(30,30,30); doc.setFont('helvetica','normal'); }
                const maxW=colWidths[i]-2; let text=val;
                while(doc.getTextWidth(text)>maxW && text.length>3) text=text.slice(0,-4)+'...';
                doc.text(text,x,y+6); x+=colWidths[i];
            });
            y+=rowH; rowCount++;
            if (y>pageH-20) {
                doc.addPage(); y=15;
                doc.setFillColor(26,26,46); doc.rect(12,y,pageW-24,rowH,'F');
                doc.setTextColor(255,255,255); doc.setFontSize(7.5); doc.setFont('helvetica','bold');
                let hx=14; headers.forEach((h,i)=>{ doc.text(h,hx,y+6); hx+=colWidths[i]; }); y+=rowH;
            }
        });

        doc.setFillColor(240,242,245); doc.rect(0,pageH-10,pageW,10,'F');
        doc.setTextColor(120,120,120); doc.setFontSize(7); doc.setFont('helvetica','italic');
        doc.text('NutriLoop - Plateforme intelligente pour une alimentation durable', 12, pageH-4);
        doc.text(rowCount+' participation(s)', pageW-12, pageH-4, {align:'right'});
        doc.save('participations_nutriloop.pdf');
    }

    document.addEventListener('DOMContentLoaded', () => showPage(1));
</script>
</body>
</html>