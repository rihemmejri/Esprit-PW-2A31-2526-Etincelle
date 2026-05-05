<?php
// views/BackOffice/locked_accounts.php
session_start();
require_once '../../controleurs/UserController.php';
require_once '../../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$adminName = $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'];

// Récupérer les comptes verrouillés
$lockedUsers = $userController->getLockedAccounts();

// Déverrouiller un compte par EMAIL
if (isset($_GET['unlock_email'])) {
    $result = $userController->adminUnlockAccountByEmail($_GET['unlock_email'], $adminName);
    if ($result) {
        header('Location: locked_accounts.php?success=1');
    } else {
        header('Location: locked_accounts.php?error=1');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptes verrouillés - NutriLoop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 { color: #003366; }
        .header h1 i { color: #dc2626; }
        .header-buttons {
            display: flex;
            gap: 15px;
        }
        .btn-back {
            background: #003366;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .btn-back:hover { background: #4CAF50; transform: translateY(-2px); }
        .btn-export {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: 0.3s;
        }
        .btn-export:hover { background: #b91c1c; transform: translateY(-2px); }
        .stats-info {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .stat-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fee2e2;
            padding: 10px 20px;
            border-radius: 40px;
        }
        .stat-badge i { color: #dc2626; font-size: 1.2rem; }
        table {
            width: 100%;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #003366; color: white; font-weight: 600; }
        .badge-locked {
            background: #fee2e2;
            color: #dc2626;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .btn-unlock {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .btn-unlock:hover { background: #45a049; transform: translateY(-1px); }
        .empty {
            text-align: center;
            padding: 60px;
            color: #999;
            background: white;
            border-radius: 16px;
        }
        .empty i { font-size: 3rem; color: #4CAF50; margin-bottom: 15px; }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #dc2626;
        }
        @media (max-width: 768px) {
            th, td { padding: 10px; font-size: 0.8rem; }
            .btn-unlock { padding: 5px 10px; font-size: 0.7rem; }
            .header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-lock"></i> Comptes verrouillés</h1>
            <div class="header-buttons">
                <button class="btn-export" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </button>
                <a href="list.php" class="btn-back">
                    <i class="fas fa-chart-line"></i> Retour au Dashboard
                </a>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Compte déverrouillé avec succès ! L'utilisateur a été notifié.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> Erreur lors du déverrouillage.
            </div>
        <?php endif; ?>
        
        <div class="stats-info">
            <div class="stat-badge">
                <i class="fas fa-lock"></i>
                <span><strong><?= count($lockedUsers) ?></strong> compte(s) verrouillé(s)</span>
            </div>
            <div class="stat-badge">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Verrouillage définitif après <strong>3</strong> tentatives échouées</span>
            </div>
            <div class="stat-badge">
                <i class="fas fa-user-shield"></i>
                <span>Seul un <strong>administrateur</strong> peut déverrouiller</span>
            </div>
        </div>
        
        <?php if (count($lockedUsers) > 0): ?>
            <table id="lockedAccountsTable">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Nom complet</th>
                        <th>Tentatives</th>
                        <th>Verrouillé depuis</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lockedUsers as $user): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($user['email']) ?></strong></td>
                            <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                            <td><span class="badge-locked"><?= $user['failed_attempts'] ?? '3' ?> / 3</span></td>
                            <td><?= date('d/m/Y H:i', strtotime($user['locked_at'])) ?></td>
                            <td>
                                <a href="?unlock_email=<?= urlencode($user['email']) ?>" class="btn-unlock" onclick="return confirm('Déverrouiller ce compte ?\n\nL\'utilisateur pourra se reconnecter immédiatement.')">
                                    <i class="fas fa-unlock-alt"></i> Déverrouiller
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">
                <i class="fas fa-check-circle"></i>
                <p>Aucun compte verrouillé</p>
                <small>Tous les comptes sont accessibles normalement</small>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
            
            // Récupérer les données du tableau
            const rows = document.querySelectorAll('#lockedAccountsTable tbody tr');
            
            if (rows.length === 0) {
                alert('Aucune donnée à exporter!');
                return;
            }
            
            // En-tête
            doc.setFontSize(18);
            doc.setTextColor(0, 51, 102);
            doc.text('Comptes verrouillés - NutriLoop', 14, 15);
            
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text(`Exporté le: ${new Date().toLocaleString('fr-FR')}`, 14, 25);
            
            doc.setDrawColor(76, 175, 80);
            doc.line(14, 30, 280, 30);
            
            // Préparer les données
            const tableData = [];
            const tableHeaders = [['Email', 'Nom complet', 'Tentatives', 'Verrouillé depuis', 'Statut']];
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const email = row.cells[0].innerText.trim();
                const nom = row.cells[1].innerText.trim();
                const tentatives = row.cells[2].innerText.trim();
                const date = row.cells[3].innerText.trim();
                
                tableData.push([email, nom, tentatives, date, 'VERROUILLÉ']);
            }
            
            // Générer le tableau PDF
            doc.autoTable({
                head: tableHeaders,
                body: tableData,
                startY: 35,
                theme: 'striped',
                headStyles: {
                    fillColor: [0, 51, 102],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                bodyStyles: {
                    halign: 'left',
                    fontSize: 9
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                margin: { top: 35, left: 14, right: 14 },
                columnStyles: {
                    0: { cellWidth: 60 },
                    1: { cellWidth: 50 },
                    2: { cellWidth: 30, halign: 'center' },
                    3: { cellWidth: 40, halign: 'center' },
                    4: { cellWidth: 30, halign: 'center' }
                }
            });
            
            // Pied de page
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(150, 150, 150);
                doc.text(`Page ${i} / ${pageCount}`, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
                doc.text(`NutriLoop AI - Sécurité`, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 5, { align: 'center' });
            }
            
            // Sauvegarder
            doc.save(`comptes_verrouilles_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.pdf`);
        }
    </script>
</body>
</html>