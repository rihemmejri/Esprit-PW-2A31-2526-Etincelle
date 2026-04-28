<?php
session_start();
require_once '../../controleurs/UserController.php';
require_once '../../models/User.php';

// Vérification session ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$users = $userController->listUsers();

// Message de succès
$success = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1': $success = "Utilisateur ajouté avec succès !"; break;
        case '2': $success = "Utilisateur modifié avec succès !"; break;
        case '3': $success = "Utilisateur supprimé avec succès !"; break;
    }
}

// Calculer les statistiques
$totalUsers = 0;
$totalAdmins = 0;
$totalActifs = 0;

$usersArray = [];
while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
    $usersArray[] = $user;
    $totalUsers++;
    if ($user['role'] === 'ADMIN') $totalAdmins++;
    if ($user['statut'] === 'actif') $totalActifs++;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
        }

        .header h1 i {
            color: #4CAF50;
            margin-right: 10px;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #003366;
            color: white;
        }

        .btn-secondary:hover {
            background: #002244;
            transform: translateY(-2px);
        }

        /* Message d'alerte */
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #28a745;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stats Bar */
        .stats-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stats {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat i {
            font-size: 1.2rem;
            color: #4CAF50;
        }

        .search-box {
            display: flex;
            gap: 5px;
        }

        .search-box input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
            font-family: inherit;
        }

        .search-box button {
            background: #4CAF50;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #1a1a2e;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
        }

        th i {
            margin-right: 8px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Avatar utilisateur */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4CAF50, #003366);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #333;
        }

        .user-email {
            font-size: 0.75rem;
            color: #888;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-admin {
            background: #e3f2fd;
            color: #1565c0;
        }

        .badge-user {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-actif {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-inactif {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Date */
        .date-cell {
            font-size: 0.8rem;
            color: #666;
            white-space: nowrap;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .edit-btn {
            background: #2196F3;
            color: white;
        }

        .edit-btn:hover {
            background: #1976D2;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pagination {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
        }

        .page-btn.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .export-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .empty-message {
            text-align: center;
            padding: 60px;
            color: #999;
        }

        .empty-message i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        /* Filter selects */
        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            background: white;
            cursor: pointer;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 30px;
            max-width: 400px;
            text-align: center;
            animation: modalFade 0.3s ease;
        }

        @keyframes modalFade {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-content i {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 15px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .modal-confirm {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .modal-cancel {
            background: #e0e0e0;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-users"></i>
                Gestion des Utilisateurs
            </h1>
            <div class="btn-group">
                <button onclick="window.location.href='add.php'" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter un utilisateur
                </button>
            </div>
        </div>

        <!-- Message de succès -->
        <?php if ($success): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <span><strong><?= $totalUsers ?></strong> utilisateurs</span>
                </div>
                <div class="stat">
                    <i class="fas fa-user-tie"></i>
                    <span><strong><?= $totalAdmins ?></strong> administrateurs</span>
                </div>
                <div class="stat">
                    <i class="fas fa-check-circle"></i>
                    <span><strong><?= $totalActifs ?></strong> actifs</span>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <select id="filterRole" class="filter-select" onchange="filterTable()">
                    <option value="">Tous les rôles</option>
                    <option value="ADMIN">Administrateurs</option>
                    <option value="USER">Utilisateurs</option>
                </select>
                <select id="filterStatus" class="filter-select" onchange="filterTable()">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actifs</option>
                    <option value="inactif">Inactifs</option>
                </select>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                    <button onclick="searchTable()"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Utilisateur</th>
                        <th><i class="fas fa-tag"></i> Rôle</th>
                        <th><i class="fas fa-circle"></i> Statut</th>
                        <th><i class="fas fa-calendar"></i> Date d'inscription</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usersArray) > 0): ?>
                        <?php foreach ($usersArray as $user): ?>
                            <tr data-role="<?= $user['role'] ?>" data-status="<?= $user['statut'] ?>">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                                            <span class="user-email"><?= htmlspecialchars($user['email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $user['role'] === 'ADMIN' ? 'badge-admin' : 'badge-user' ?>">
                                        <i class="fas <?= $user['role'] === 'ADMIN' ? 'fa-crown' : 'fa-user' ?>"></i>
                                        <?= $user['role'] === 'ADMIN' ? 'Administrateur' : 'Utilisateur' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $user['statut'] === 'actif' ? 'badge-actif' : 'badge-inactif' ?>">
                                        <i class="fas <?= $user['statut'] === 'actif' ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                        <?= $user['statut'] === 'actif' ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td class="date-cell">
                                    <i class="fas fa-calendar-alt" style="margin-right: 5px; color: #999;"></i>
                                    <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                                </td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $user['id_user'] ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <button onclick="openDeleteModal(<?= $user['id_user'] ?>)" class="action-btn delete-btn">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-message">
                                <i class="fas fa-users-slash"></i>
                                <p>Aucun utilisateur trouvé</p>
                                <button onclick="window.location.href='add.php'" class="btn btn-primary" style="margin-top: 10px;">
                                    <i class="fas fa-plus-circle"></i> Ajouter un utilisateur
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="pagination" id="pagination"></div>
            <button class="export-btn" onclick="exportTable()">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
    </div>

    <!-- Modal de confirmation suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
            <p style="font-size: 12px; color: #999; margin-top: 10px;">Cette action est irréversible.</p>
            <div class="modal-buttons">
                <button class="modal-confirm" id="confirmDeleteBtn">Supprimer</button>
                <button class="modal-cancel" onclick="closeDeleteModal()">Annuler</button>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;

        function openDeleteModal(id) {
            deleteId = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            deleteId = null;
            document.getElementById('deleteModal').style.display = 'none';
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteId) {
                window.location.href = 'delete.php?id=' + deleteId;
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        }

        // Filtres et recherche
        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('filterRole').value;
            const statusFilter = document.getElementById('filterStatus').value;
            
            const rows = document.querySelectorAll('#usersTable tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const userName = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const userEmail = row.querySelector('.user-email')?.textContent.toLowerCase() || '';
                const userRole = row.getAttribute('data-role');
                const userStatus = row.getAttribute('data-status');
                
                let show = true;
                
                if (searchTerm && !userName.includes(searchTerm) && !userEmail.includes(searchTerm)) {
                    show = false;
                }
                
                if (roleFilter && userRole !== roleFilter) {
                    show = false;
                }
                
                if (statusFilter && userStatus !== statusFilter) {
                    show = false;
                }
                
                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            
            // Mettre à jour la pagination
            setupPagination();
        }
        
        function searchTable() {
            filterTable();
        }

        // Pagination
        let currentPage = 1;
        const rowsPerPage = 10;

        function setupPagination() {
            const rows = document.querySelectorAll('#usersTable tbody tr');
            let visibleRows = [];
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    visibleRows.push(row);
                }
            });
            
            const totalRows = visibleRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            // Cacher toutes les lignes d'abord
            visibleRows.forEach((row, index) => {
                row.style.display = 'none';
            });
            
            // Afficher la page courante
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            for (let i = start; i < end && i < visibleRows.length; i++) {
                visibleRows[i].style.display = '';
            }
            
            // Mettre à jour les boutons de pagination
            const paginationDiv = document.getElementById('pagination');
            paginationDiv.innerHTML = '';
            
            // Bouton précédent
            const prevBtn = document.createElement('button');
            prevBtn.className = 'page-btn';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; setupPagination(); } };
            paginationDiv.appendChild(prevBtn);
            
            // Numéros de pages
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = 'page-btn' + (currentPage === i ? ' active' : '');
                pageBtn.textContent = i;
                pageBtn.onclick = () => { currentPage = i; setupPagination(); };
                paginationDiv.appendChild(pageBtn);
            }
            
            // Bouton suivant
            const nextBtn = document.createElement('button');
            nextBtn.className = 'page-btn';
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; setupPagination(); } };
            paginationDiv.appendChild(nextBtn);
        }

        // Export CSV
        function exportTable() {
            let csv = "Nom,Prénom,Email,Rôle,Statut,Date d'inscription\n";
            
            <?php foreach ($usersArray as $user): ?>
                csv += `<?= addslashes($user['nom']) ?>,<?= addslashes($user['prenom']) ?>,<?= addslashes($user['email']) ?>,<?= $user['role'] ?>,<?= $user['statut'] ?>,<?= $user['date_inscription'] ?>\n`;
            <?php endforeach; ?>
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'utilisateurs_export.csv';
            a.click();
            URL.revokeObjectURL(url);
        }

        // Initialiser la pagination au chargement
        document.addEventListener('DOMContentLoaded', () => {
            setupPagination();
        });
    </script>
</body>
</html>