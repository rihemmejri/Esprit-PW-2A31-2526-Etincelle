<?php
session_start();
require_once __DIR__ . '/../../models/ConnexionStats.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$statsModel = new ConnexionStats();
$userStats = $statsModel->getUserStats($_SESSION['user']['id_user']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes statistiques - NutriLoop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #003366;
            margin-bottom: 10px;
        }
        h1 i { color: #4CAF50; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 16px;
        }
        .stat-item i {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .stat-item .value {
            font-size: 2rem;
            font-weight: 700;
            color: #003366;
        }
        .stat-item .label {
            color: #666;
            font-size: 0.85rem;
        }
        .btn-back {
            background: #003366;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            color: #003366;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
        
        <div class="card">
            <h1><i class="fas fa-chart-line"></i> Mes statistiques de connexion</h1>
            <p>Bonjour <?= htmlspecialchars($_SESSION['user']['prenom']) ?>, voici vos statistiques d'activité</p>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <i class="fas fa-calendar-check"></i>
                    <div class="value"><?= $userStats['days_connected'] ?></div>
                    <div class="label">Jours connecté(s)</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-sign-in-alt"></i>
                    <div class="value"><?= $userStats['total_connexions'] ?></div>
                    <div class="label">Connexions totales</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-chart-simple"></i>
                    <div class="value"><?= $userStats['days_connected'] > 0 ? round($userStats['total_connexions'] / $userStats['days_connected'], 1) : 0 ?></div>
                    <div class="label">Connexions/jour</div>
                </div>
            </div>
            
            <h3 style="margin: 25px 0 15px 0;"><i class="fas fa-history"></i> Historique détaillé</h3>
            <?php if (!empty($userStats['stats'])): ?>
                <table>
                    <thead>
                        <tr><th>Date</th><th>Nombre de connexions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userStats['stats'] as $stat): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($stat['connexion_date'])) ?></td>
                                <td><?= $stat['connexion_count'] ?> fois</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>