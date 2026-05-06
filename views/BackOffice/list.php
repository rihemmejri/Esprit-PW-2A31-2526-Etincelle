<?php
session_start();
require_once '../../controleurs/UserController.php';
require_once '../../models/User.php';
require_once '../../models/ConnexionStats.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$users = $userController->listUsers();

$success = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1': $success = "Utilisateur ajouté avec succès !"; break;
        case '2': $success = "Utilisateur modifié avec succès !"; break;
        case '3': $success = "Utilisateur supprimé avec succès !"; break;
        case '5': $success = "Utilisateur banni avec succès !"; break;
        case '6': $success = "Utilisateur débanni avec succès !"; break;
    }
}

$statsModel = new ConnexionStats();
$globalStats = $statsModel->getGlobalStats();
$avgConnexions = $statsModel->getAverageConnexions();

$totalUsers = 0;
$totalAdmins = 0;
$totalActifs = 0;
$totalBannis = 0;

$usersArray = [];
while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
    $usersArray[] = $user;
    $totalUsers++;
    if ($user['role'] === 'ADMIN') $totalAdmins++;
    if ($user['statut'] === 'actif') $totalActifs++;
    if (isset($user['is_banned']) && $user['is_banned'] == 1) $totalBannis++;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 { font-size: 1.8rem; color: #1a1a2e; }
        .header h1 i { color: #4CAF50; margin-right: 10px; }
        
        .btn-group { display: flex; gap: 12px; }
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
        .btn-primary { background: #4CAF50; color: white; }
        .btn-primary:hover { background: #45a049; transform: translateY(-2px); }
        .btn-stats { background: #003366; color: white; }
        .btn-stats:hover { background: #002244; transform: translateY(-2px); }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
            cursor: pointer;
            text-align: center;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .stat-card i { font-size: 2rem; color: #4CAF50; margin-bottom: 10px; }
        .stat-card h3 { font-size: 0.8rem; color: #666; font-weight: 500; margin-bottom: 5px; }
        .stat-card .value { font-size: 1.8rem; font-weight: 700; color: #003366; }
        
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
        }
        
        .stats-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .stats { display: flex; gap: 25px; flex-wrap: wrap; }
        .stat { display: flex; align-items: center; gap: 8px; }
        .stat i { font-size: 1.2rem; color: #4CAF50; }
        
        /* Boutons de tri */
        .sort-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 15px;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .sort-btn {
            background: #f0f2f5;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 500;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .sort-btn:hover { background: #4CAF50; color: white; }
        .sort-btn.active { background: #4CAF50; color: white; }
        .sort-btn i { font-size: 0.7rem; }
        
        .search-filters { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            background: white;
            cursor: pointer;
        }
        .search-box { display: flex; gap: 5px; }
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
        
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        table { width: 100%; border-collapse: collapse; min-width: 1100px; }
        th {
            background: #1a1a2e;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
        }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f9fa; }
        
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
        .user-name { font-weight: 600; color: #333; }
        .user-email { font-size: 0.75rem; color: #888; }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-admin { background: #e3f2fd; color: #1565c0; }
        .badge-user { background: #e8f5e9; color: #2e7d32; }
        .badge-actif { background: #e8f5e9; color: #2e7d32; }
        .badge-inactif { background: #fee2e2; color: #dc2626; }
        .badge-banned { background: #fee2e2; color: #dc2626; }
        
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
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
        .edit-btn { background: #2196F3; color: white; }
        .edit-btn:hover { background: #1976D2; }
        .delete-btn { background: #dc3545; color: white; }
        .delete-btn:hover { background: #c82333; }
        .btn-ban { background: #dc2626; color: white; }
        .btn-ban:hover { background: #b91c1c; }
        .btn-unban { background: #10b981; color: white; }
        .btn-unban:hover { background: #059669; }
        
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pagination { display: flex; gap: 8px; }
        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
        }
        .page-btn.active { background: #4CAF50; color: white; border-color: #4CAF50; }
        
        .export-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .export-btn:hover { background: #b91c1c; transform: translateY(-2px); }
        
        .empty-message { text-align: center; padding: 60px; color: #999; }
        .empty-message i { font-size: 3rem; margin-bottom: 10px; }
        
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
        }
        .modal-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 25px; }
        .modal-confirm { background: #dc3545; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .modal-cancel { background: #e0e0e0; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        
        @media (max-width: 768px) {
            .stats { flex-direction: column; gap: 10px; }
            .search-filters { flex-direction: column; width: 100%; }
            .search-box { width: 100%; }
            .search-box input { flex: 1; }
            .sort-buttons { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
            <div class="btn-group">
                <button onclick="window.location.href='stats.php'" class="btn btn-stats">
                    <i class="fas fa-chart-line"></i> Statistiques
                </button>
                <button onclick="window.location.href='add.php'" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter un utilisateur
                </button>
            </div>
        </div>

        <div class="stats-cards">
            <div class="stat-card" onclick="window.location.href='stats.php'">
                <i class="fas fa-sign-in-alt"></i>
                <h3>Connexions (30j)</h3>
                <div class="value"><?= number_format($globalStats['global']['total_logins'] ?? 0) ?></div>
            </div>
            <div class="stat-card" onclick="window.location.href='stats.php'">
                <i class="fas fa-users"></i>
                <h3>Utilisateurs actifs</h3>
                <div class="value"><?= $globalStats['global']['total_users_connected'] ?? 0 ?></div>
            </div>
            <div class="stat-card" onclick="window.location.href='stats.php'">
                <i class="fas fa-chart-simple"></i>
                <h3>Moyenne/utilisateur</h3>
                <div class="value"><?= $avgConnexions ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-tie"></i>
                <h3>Administrateurs</h3>
                <div class="value"><?= $totalAdmins ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-ban"></i>
                <h3>Utilisateurs bannis</h3>
                <div class="value"><?= $totalBannis ?></div>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="stats">
                <div class="stat"><i class="fas fa-users"></i> <span><strong><?= $totalUsers ?></strong> utilisateurs</span></div>
                <div class="stat"><i class="fas fa-user-tie"></i> <span><strong><?= $totalAdmins ?></strong> administrateurs</span></div>
                <div class="stat"><i class="fas fa-check-circle"></i> <span><strong><?= $totalActifs ?></strong> actifs</span></div>
                <div class="stat"><i class="fas fa-ban"></i> <span><strong><?= $totalBannis ?></strong> bannis</span></div>
            </div>
            <div class="search-filters">
                <select id="filterRole" class="filter-select" onchange="filterAndSort()">
                    <option value="">Tous les rôles</option>
                    <option value="ADMIN">Administrateurs</option>
                    <option value="USER">Utilisateurs</option>
                </select>
                <select id="filterStatus" class="filter-select" onchange="filterAndSort()">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actifs</option>
                    <option value="inactif">Inactifs</option>
                </select>
                <select id="filterBan" class="filter-select" onchange="filterAndSort()">
                    <option value="">Tous</option>
                    <option value="banned">Bannis</option>
                    <option value="not_banned">Non bannis</option>
                </select>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="filterAndSort()">
                    <button onclick="filterAndSort()"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        <!-- Boutons de tri -->
        <div class="sort-buttons">
            <button class="sort-btn" data-sort="id" onclick="setSort('id')">
                <i class="fas fa-hashtag"></i> ID 
                <i class="fas fa-sort" id="sort-icon-id"></i>
            </button>
            <button class="sort-btn" data-sort="name" onclick="setSort('name')">
                <i class="fas fa-user"></i> Nom 
                <i class="fas fa-sort" id="sort-icon-name"></i>
            </button>
            <button class="sort-btn" data-sort="email" onclick="setSort('email')">
                <i class="fas fa-envelope"></i> Email 
                <i class="fas fa-sort" id="sort-icon-email"></i>
            </button>
            <button class="sort-btn" data-sort="role" onclick="setSort('role')">
                <i class="fas fa-tag"></i> Rôle 
                <i class="fas fa-sort" id="sort-icon-role"></i>
            </button>
            <button class="sort-btn" data-sort="status" onclick="setSort('status')">
                <i class="fas fa-circle"></i> Statut 
                <i class="fas fa-sort" id="sort-icon-status"></i>
            </button>
            <button class="sort-btn" data-sort="date" onclick="setSort('date')">
                <i class="fas fa-calendar"></i> Date 
                <i class="fas fa-sort" id="sort-icon-date"></i>
            </button>
        </div>

        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user"></i> Utilisateur</th>
                        <th><i class="fas fa-tag"></i> Rôle</th>
                        <th><i class="fas fa-circle"></i> Statut</th>
                        <th><i class="fas fa-ban"></i> Ban</th>
                        <th><i class="fas fa-calendar"></i> Date d'inscription</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (count($usersArray) > 0): ?>
                        <?php foreach ($usersArray as $user): ?>
                            <tr data-id="<?= $user['id_user'] ?>" data-role="<?= $user['role'] ?>" data-status="<?= $user['statut'] ?>" data-banned="<?= $user['is_banned'] ?? 0 ?>" data-name="<?= strtolower($user['prenom'] . ' ' . $user['nom']) ?>" data-email="<?= strtolower($user['email']) ?>" data-date="<?= $user['date_inscription'] ?>">
                                <td><?= $user['id_user'] ?></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar"><?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?></div>
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
                                    <?php if ($user['statut'] === 'actif'): ?>
                                        <span class="badge badge-actif"><i class="fas fa-check-circle"></i> Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactif"><i class="fas fa-times-circle"></i> Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($user['is_banned']) && $user['is_banned'] == 1): ?>
                                        <span class="badge badge-banned"><i class="fas fa-ban"></i> Banni</span>
                                    <?php else: ?>
                                        <span class="badge badge-actif"><i class="fas fa-check-circle"></i> Non banni</span>
                                    <?php endif; ?>
                                </td>
                                <td class="date-cell"><i class="fas fa-calendar-alt" style="margin-right: 5px; color: #999;"></i> <?= date('d/m/Y', strtotime($user['date_inscription'])) ?> </td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $user['id_user'] ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i> Modifier</a>
                                    <?php if (isset($user['is_banned']) && $user['is_banned'] == 1): ?>
                                        <button onclick="unbanUser('<?= $user['email'] ?>', '<?= addslashes($user['prenom'] . ' ' . $user['nom']) ?>')" class="action-btn btn-unban"><i class="fas fa-check-circle"></i> Débannir</button>
                                    <?php else: ?>
                                        <?php if ($user['email'] !== $_SESSION['user']['email']): ?>
                                            <button onclick="banUser('<?= $user['email'] ?>', '<?= addslashes($user['prenom'] . ' ' . $user['nom']) ?>')" class="action-btn btn-ban"><i class="fas fa-ban"></i> Bannir</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <button onclick="openDeleteModal(<?= $user['id_user'] ?>)" class="action-btn delete-btn"><i class="fas fa-trash"></i> Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="empty-message"><i class="fas fa-users-slash"></i><p>Aucun utilisateur trouvé</p></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div class="pagination" id="pagination"></div>
            <button class="export-btn" onclick="exportToPDF()"><i class="fas fa-file-pdf"></i> Exporter PDF</button>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
            <div class="modal-buttons">
                <button class="modal-confirm" id="confirmDeleteBtn">Supprimer</button>
                <button class="modal-cancel" onclick="closeDeleteModal()">Annuler</button>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;
        let currentSort = { field: 'id', order: 'asc' };
        let allUsersData = [];
        let currentPage = 1;
        let rowsPerPage = 10;
        let totalFilteredRows = 0;

        <?php if (count($usersArray) > 0): ?>
        allUsersData = <?= json_encode($usersArray) ?>;
        <?php endif; ?>

        // ========== FONCTIONS BANNISSEMENT ==========
        
        function banUser(email, userName) {
            let reason = prompt("Raison du bannissement pour " + userName + " :", "Violation des conditions d'utilisation");
            if (reason === null) return;
            if (reason.trim() === "") reason = "Violation des conditions d'utilisation";
            
            if (confirm(`⚠️ Êtes-vous sûr de vouloir BANNIR ${userName} ?\n\nRaison : ${reason}\n\nL'utilisateur ne pourra plus se connecter.`)) {
                window.location.href = `ban_user.php?email=${encodeURIComponent(email)}&reason=${encodeURIComponent(reason)}`;
            }
        }

        function unbanUser(email, userName) {
            if (confirm(`✅ Êtes-vous sûr de vouloir DÉBANNIR ${userName} ?\n\nL'utilisateur pourra à nouveau se connecter.`)) {
                window.location.href = `unban_user.php?email=${encodeURIComponent(email)}`;
            }
        }

        // ========== FONCTIONS SUPPRESSION ==========
        
        function openDeleteModal(id) { 
            deleteId = id; 
            document.getElementById('deleteModal').style.display = 'flex'; 
        }
        
        function closeDeleteModal() { 
            deleteId = null; 
            document.getElementById('deleteModal').style.display = 'none'; 
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() { 
            if (deleteId) window.location.href = 'delete.php?id=' + deleteId; 
        });
        
        window.onclick = function(event) { 
            const modal = document.getElementById('deleteModal'); 
            if (event.target === modal) closeDeleteModal(); 
        }

        // ========== FONCTIONS TRI ==========
        
        function setSort(field) {
            if (currentSort.field === field) {
                currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.field = field;
                currentSort.order = 'asc';
            }
            updateSortIcons();
            filterAndSort();
        }

        function updateSortIcons() {
            const fields = ['id', 'name', 'email', 'role', 'status', 'date'];
            fields.forEach(f => {
                const icon = document.getElementById(`sort-icon-${f}`);
                if (icon) {
                    if (currentSort.field === f) {
                        icon.className = `fas fa-sort-${currentSort.order === 'asc' ? 'up' : 'down'}`;
                    } else {
                        icon.className = 'fas fa-sort';
                    }
                }
            });
        }

        // ========== FILTRAGE ET TRI ==========
        
        function filterAndSort() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('filterRole').value;
            const statusFilter = document.getElementById('filterStatus').value;
            const banFilter = document.getElementById('filterBan').value;
            
            let filteredData = [...allUsersData];
            
            filteredData = filteredData.filter(user => {
                const userName = (user.prenom + ' ' + user.nom).toLowerCase();
                const userEmail = user.email.toLowerCase();
                const matchesSearch = !searchTerm || userName.includes(searchTerm) || userEmail.includes(searchTerm);
                const matchesRole = !roleFilter || user.role === roleFilter;
                const matchesStatus = !statusFilter || user.statut === statusFilter;
                const matchesBan = !banFilter || (banFilter === 'banned' && user.is_banned == 1) || (banFilter === 'not_banned' && user.is_banned != 1);
                return matchesSearch && matchesRole && matchesStatus && matchesBan;
            });
            
            filteredData.sort((a, b) => {
                let valA, valB;
                switch(currentSort.field) {
                    case 'id':
                        valA = a.id_user;
                        valB = b.id_user;
                        break;
                    case 'name':
                        valA = (a.prenom + ' ' + a.nom).toLowerCase();
                        valB = (b.prenom + ' ' + b.nom).toLowerCase();
                        break;
                    case 'email':
                        valA = a.email.toLowerCase();
                        valB = b.email.toLowerCase();
                        break;
                    case 'role':
                        valA = a.role;
                        valB = b.role;
                        break;
                    case 'status':
                        valA = a.statut;
                        valB = b.statut;
                        break;
                    case 'date':
                        valA = new Date(a.date_inscription);
                        valB = new Date(b.date_inscription);
                        break;
                    default:
                        return 0;
                }
                
                if (valA < valB) return currentSort.order === 'asc' ? -1 : 1;
                if (valA > valB) return currentSort.order === 'asc' ? 1 : -1;
                return 0;
            });
            
            renderTable(filteredData);
            setupPagination(filteredData.length);
            currentPage = 1;
            applyPagination();
        }

        // ========== RENDU TABLEAU ==========
        
        function renderTable(users) {
            const tbody = document.getElementById('tableBody');
            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-message"><i class="fas fa-users-slash"></i><p>Aucun utilisateur trouvé</p></td></tr>';
                return;
            }
            
            let html = '';
            users.forEach(user => {
                const initial = (user.prenom.charAt(0) + user.nom.charAt(0)).toUpperCase();
                const roleClass = user.role === 'ADMIN' ? 'badge-admin' : 'badge-user';
                const roleIcon = user.role === 'ADMIN' ? 'fa-crown' : 'fa-user';
                const roleText = user.role === 'ADMIN' ? 'Administrateur' : 'Utilisateur';
                const statusClass = user.statut === 'actif' ? 'badge-actif' : 'badge-inactif';
                const statusIcon = user.statut === 'actif' ? 'fa-check-circle' : 'fa-times-circle';
                const statusText = user.statut === 'actif' ? 'Actif' : 'Inactif';
                const dateFormatted = new Date(user.date_inscription).toLocaleDateString('fr-FR');
                const isBanned = user.is_banned == 1;
                const currentUserEmail = '<?= $_SESSION['user']['email'] ?>';
                
                html += `
                    <tr data-id="${user.id_user}" data-role="${user.role}" data-status="${user.statut}" data-banned="${user.is_banned || 0}">
                        <td>${user.id_user}</td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">${escapeHtml(initial)}</div>
                                <div class="user-details">
                                    <span class="user-name">${escapeHtml(user.prenom)} ${escapeHtml(user.nom)}</span>
                                    <span class="user-email">${escapeHtml(user.email)}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge ${roleClass}"><i class="fas ${roleIcon}"></i> ${roleText}</span></td>
                        <td><span class="badge ${statusClass}"><i class="fas ${statusIcon}"></i> ${statusText}</span></td>
                        <td>${isBanned ? '<span class="badge badge-banned"><i class="fas fa-ban"></i> Banni</span>' : '<span class="badge badge-actif"><i class="fas fa-check-circle"></i> Non banni</span>'}</td>
                        <td class="date-cell"><i class="fas fa-calendar-alt" style="margin-right: 5px; color: #999;"></i> ${dateFormatted}</td>
                        <td class="actions">
                            <a href="edit.php?id=${user.id_user}" class="action-btn edit-btn"><i class="fas fa-edit"></i> Modifier</a>
                            ${isBanned ? 
                                `<button onclick="unbanUser('${escapeHtml(user.email)}', '${escapeHtml(user.prenom + ' ' + user.nom)}')" class="action-btn btn-unban"><i class="fas fa-check-circle"></i> Débannir</button>` : 
                                (user.email !== currentUserEmail ? `<button onclick="banUser('${escapeHtml(user.email)}', '${escapeHtml(user.prenom + ' ' + user.nom)}')" class="action-btn btn-ban"><i class="fas fa-ban"></i> Bannir</button>` : '')
                            }
                            <button onclick="openDeleteModal(${user.id_user})" class="action-btn delete-btn"><i class="fas fa-trash"></i> Supprimer</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function escapeHtml(str) { 
            if (!str) return ''; 
            return str.replace(/[&<>]/g, function(m) { 
                if (m === '&') return '&amp;'; 
                if (m === '<') return '&lt;'; 
                if (m === '>') return '&gt;'; 
                return m; 
            }); 
        }

        // ========== PAGINATION ==========
        
        function setupPagination(totalRows) {
            totalFilteredRows = totalRows;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const paginationDiv = document.getElementById('pagination');
            if (!paginationDiv) return;
            
            paginationDiv.innerHTML = '';
            if (totalPages <= 1) return;
            
            const prevBtn = document.createElement('button'); 
            prevBtn.className = 'page-btn'; 
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>'; 
            prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; applyPagination(); } }; 
            paginationDiv.appendChild(prevBtn);
            
            let startPage = Math.max(1, currentPage - 2); 
            let endPage = Math.min(totalPages, startPage + 4);
            
            for (let i = startPage; i <= endPage; i++) { 
                const pageBtn = document.createElement('button'); 
                pageBtn.className = 'page-btn' + (currentPage === i ? ' active' : ''); 
                pageBtn.textContent = i; 
                pageBtn.onclick = () => { currentPage = i; applyPagination(); }; 
                paginationDiv.appendChild(pageBtn); 
            }
            
            const nextBtn = document.createElement('button'); 
            nextBtn.className = 'page-btn'; 
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>'; 
            nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; applyPagination(); } }; 
            paginationDiv.appendChild(nextBtn);
        }
        
        function applyPagination() { 
            const rows = document.querySelectorAll('#tableBody tr'); 
            const start = (currentPage - 1) * rowsPerPage; 
            const end = start + rowsPerPage; 
            rows.forEach((row, index) => { 
                row.style.display = (index >= start && index < end) ? '' : 'none'; 
            }); 
        }

        // ========== EXPORT PDF ==========
        
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
            
            const rows = document.querySelectorAll('#tableBody tr');
            let visibleRows = [];
            rows.forEach(row => { if (row.style.display !== 'none' && row.cells && row.cells.length >= 7) visibleRows.push(row); });
            
            if (visibleRows.length === 0) { alert('Aucune donnée à exporter!'); return; }
            
            doc.setFontSize(18); 
            doc.setTextColor(0, 51, 102); 
            doc.text('Liste des Utilisateurs - NutriLoop', 14, 15);
            doc.setFontSize(10); 
            doc.setTextColor(100, 100, 100); 
            doc.text(`Exporté le: ${new Date().toLocaleString('fr-FR')}`, 14, 25);
            doc.setDrawColor(76, 175, 80); 
            doc.line(14, 30, 280, 30);
            
            const tableData = []; 
            const tableHeaders = [['ID', 'Nom Complet', 'Email', 'Rôle', 'Statut', 'Ban', "Date d'inscription"]];
            
            for (let i = 0; i < visibleRows.length; i++) { 
                const row = visibleRows[i]; 
                const id = row.cells[0].innerText.trim(); 
                const userCell = row.cells[1]; 
                const userName = userCell.querySelector('.user-name')?.innerText.trim() || ''; 
                const userEmail = userCell.querySelector('.user-email')?.innerText.trim() || ''; 
                const role = row.cells[2].innerText.trim(); 
                const status = row.cells[3].innerText.trim(); 
                const ban = row.cells[4].innerText.trim(); 
                const date = row.cells[5].innerText.trim(); 
                tableData.push([id, userName, userEmail, role, status, ban, date]); 
            }
            
            doc.autoTable({ 
                head: tableHeaders, 
                body: tableData, 
                startY: 35, 
                theme: 'striped', 
                headStyles: { fillColor: [0, 51, 102], textColor: [255, 255, 255], fontStyle: 'bold', halign: 'center' }, 
                bodyStyles: { halign: 'left', fontSize: 9 }, 
                alternateRowStyles: { fillColor: [245, 245, 245] }, 
                margin: { top: 35, left: 14, right: 14 }, 
                columnStyles: { 
                    0: { cellWidth: 15, halign: 'center' }, 
                    1: { cellWidth: 40 }, 
                    2: { cellWidth: 55 }, 
                    3: { cellWidth: 30, halign: 'center' }, 
                    4: { cellWidth: 25, halign: 'center' }, 
                    5: { cellWidth: 25, halign: 'center' }, 
                    6: { cellWidth: 30, halign: 'center' } 
                } 
            });
            
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) { 
                doc.setPage(i); 
                doc.setFontSize(8); 
                doc.setTextColor(150, 150, 150); 
                doc.text(`Page ${i} / ${pageCount}`, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' }); 
                doc.text(`NutriLoop AI - Gestion des Utilisateurs`, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 5, { align: 'center' }); 
            }
            
            doc.save(`utilisateurs_nutriloop_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.pdf`);
        }

        // ========== INITIALISATION ==========
        
        document.addEventListener('DOMContentLoaded', () => {
            updateSortIcons();
            filterAndSort();
        });
    </script>
</body>
</html>