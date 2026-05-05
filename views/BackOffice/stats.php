<?php
session_start();
require_once __DIR__ . '/../../controleurs/UserController.php';
require_once __DIR__ . '/../../models/ConnexionStats.php';

// Vérification session ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$statsModel = new ConnexionStats();
$globalStats = $statsModel->getGlobalStats();
$avgConnexions = $statsModel->getAverageConnexions();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de connexion - NutriLoop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
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

        .btn-back {
            background: #003366;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #4CAF50;
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stat-card i {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .stat-card h3 {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #003366;
        }

        .stat-card .sub {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }

        /* Charts */
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .chart-card h3 {
            margin-bottom: 20px;
            color: #003366;
            font-size: 1.1rem;
        }

        .chart-card h3 i {
            margin-right: 8px;
            color: #4CAF50;
        }

        canvas {
            max-height: 300px;
            width: 100%;
        }

        /* Top Users Table */
        .top-users {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .top-users h3 {
            margin-bottom: 20px;
            color: #003366;
            font-size: 1.1rem;
        }

        .top-users h3 i {
            margin-right: 8px;
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px;
            background: #f8f9fa;
            color: #555;
            font-weight: 600;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
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

        .rank {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #4CAF50, #003366);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
        .rank-2 { background: linear-gradient(135deg, #C0C0C0, #808080); }
        .rank-3 { background: linear-gradient(135deg, #CD7F32, #8B4513); }

        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-chart-line"></i>
                Statistiques de connexion
            </h1>
            <a href="list.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour aux utilisateurs
            </a>
        </div>

        <!-- Cartes statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-sign-in-alt"></i>
                <h3>Total connexions (30j)</h3>
                <div class="value"><?= number_format($globalStats['global']['total_logins'] ?? 0) ?></div>
                <div class="sub">connexions au total</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Utilisateurs actifs</h3>
                <div class="value"><?= $globalStats['global']['total_users_connected'] ?? 0 ?></div>
                <div class="sub">ont au moins 1 connexion</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-simple"></i>
                <h3>Moyenne par utilisateur</h3>
                <div class="value"><?= $avgConnexions ?></div>
                <div class="sub">connexions/jour en moyenne</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-week"></i>
                <h3>Période analysée</h3>
                <div class="value">30</div>
                <div class="sub">derniers jours</div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-container">
            <div class="chart-card">
                <h3><i class="fas fa-chart-line"></i> Évolution des connexions (30 jours)</h3>
                <canvas id="dailyChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fas fa-chart-pie"></i> Répartition par rôle</h3>
                <canvas id="roleChart"></canvas>
            </div>
        </div>

        <!-- Top utilisateurs -->
        <div class="top-users">
            <h3><i class="fas fa-trophy"></i> Top 10 des utilisateurs les plus actifs (30 jours)</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Total connexions</th>
                        <th>Jours connectés</th>
                        <th>Moyenne/jour</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($globalStats['top_users'])): ?>
                        <?php foreach ($globalStats['top_users'] as $index => $user): ?>
                            <tr>
                                <td>
                                    <span class="rank <?= $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : '')) ?>">
                                        <?= $index + 1 ?>
                                    </span>
                                 </td>
                                 <td>
                                    <div>
                                        <strong><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></strong><br>
                                        <small style="color: #999;"><?= htmlspecialchars($user['email']) ?></small>
                                    </div>
                                 </td>
                                 <td>
                                    <span class="badge <?= $user['role'] === 'ADMIN' ? 'badge-admin' : 'badge-user' ?>">
                                        <?= $user['role'] === 'ADMIN' ? 'Administrateur' : 'Utilisateur' ?>
                                    </span>
                                 </td>
                                 <td><strong><?= $user['total_connexions'] ?></strong></td>
                                 <td><?= $user['jours_connectes'] ?> jours</td>
                                 <td><?= round($user['total_connexions'] / $user['jours_connectes'], 1) ?> /jour</td>
                             </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center;">Aucune donnée disponible</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Graphique des connexions quotidiennes
        const dailyStats = <?= json_encode($globalStats['daily_stats']) ?>;
        const dailyLabels = dailyStats.map(s => s.connexion_date);
        const dailyData = dailyStats.map(s => s.total_connexions);

        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Nombre de connexions',
                    data: dailyData,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#003366',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Connexions: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Graphique des rôles
        const roleStats = <?= json_encode($globalStats['role_stats']) ?>;
        const roleLabels = roleStats.map(r => r.role === 'ADMIN' ? 'Administrateurs' : 'Utilisateurs');
        const roleData = roleStats.map(r => r.total_connexions);

        new Chart(document.getElementById('roleChart'), {
            type: 'pie',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleData,
                    backgroundColor: ['#003366', '#4CAF50'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = roleData.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw} connexions (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>