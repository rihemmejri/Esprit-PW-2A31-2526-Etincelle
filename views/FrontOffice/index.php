<?php
session_start();
require_once '../../controleurs/UserController.php';

$userController = new UserController();
$users = $userController->listUsers();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriLoop | Utilisateurs</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }

        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 5%;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 700;
            color: #003366;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .nav-menu a:hover {
            color: #4CAF50;
        }

        .btn-back {
            background: #4CAF50;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        h1 {
            color: #003366;
            margin-bottom: 2rem;
            text-align: center;
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        .user-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .user-card-header {
            background: linear-gradient(135deg, #003366, #4CAF50);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .user-card-header h3 {
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .role-admin {
            background: #ff9800;
            color: white;
        }

        .role-user {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        .user-card-body {
            padding: 1.5rem;
        }

        .user-info {
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .user-info strong {
            color: #003366;
            display: inline-block;
            width: 100px;
        }

        .statut-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .statut-actif {
            background: #4CAF50;
            color: white;
        }

        .user-card-footer {
            background: #f9f9f9;
            padding: 1rem;
            text-align: center;
        }

        .btn-retour {
            display: inline-block;
            margin-top: 2rem;
            padding: 12px 30px;
            background: #003366;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            text-align: center;
        }

        .btn-retour:hover {
            background: #4CAF50;
        }

        .no-users {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .user-grid {
                grid-template-columns: 1fr;
            }
            .nav-menu {
                display: none;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <nav class="navbar">
        <div class="logo">
            <img src="image/logo.PNG" alt="Logo" class="logo-img">
            <span class="logo-text">NutriLoop</span>
        </div>
        <ul class="nav-menu">
            <li><a href="index.html">Accueil</a></li>
            <li><a href="index.php" class="active">Utilisateurs</a></li>
            <li><a href="../BackOffice/index.html" class="btn-back">Dashboard</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1><i class="fas fa-users"></i> Nos utilisateurs</h1>
    
    <?php if (isset($_SESSION['user'])): ?>
        <div style="text-align: right; margin-bottom: 20px;">
            <span>👋 Bienvenue, <?= htmlspecialchars($_SESSION['user']['prenom']) ?> !</span>
            <a href="logout.php" style="margin-left: 15px; color: #f44336;">Déconnexion</a>
            <?php if ($_SESSION['user']['role'] === 'ADMIN'): ?>
                <a href="../BackOffice/list.php" style="margin-left: 15px; color: #4CAF50;">Backoffice</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="text-align: right; margin-bottom: 20px;">
            <a href="login.php" style="margin-right: 15px;">Connexion</a>
            <a href="register.php">Inscription</a>
        </div>
    <?php endif; ?>
    
    <div class="user-grid">
        <?php 
        $hasUsers = false;
        while($user = $users->fetch(PDO::FETCH_ASSOC)): 
            if($user['statut'] === 'actif' || $user['statut'] === 'ACTIF'):
                $hasUsers = true;
        ?>
            <div class="user-card">
                <div class="user-card-header">
                    <h3><?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></h3>
                    <span class="role-badge <?= $user['role'] === 'ADMIN' ? 'role-admin' : 'role-user' ?>">
                        <?= $user['role'] === 'ADMIN' ? 'Administrateur' : 'Membre' ?>
                    </span>
                </div>
                <div class="user-card-body">
                    <div class="user-info">
                        <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?>
                    </div>
                    <div class="user-info">
                        <strong>Inscrit depuis:</strong> <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                    </div>
                </div>
                <div class="user-card-footer">
                    <span class="statut-badge statut-actif">
                        <i class="fas fa-check-circle"></i> Compte actif
                    </span>
                </div>
            </div>
        <?php 
            endif;
        endwhile; 
        ?>
        
        <?php if (!$hasUsers): ?>
            <div class="no-users">
                <i class="fas fa-user-slash" style="font-size: 3rem; color: #ccc;"></i>
                <p>Aucun utilisateur actif pour le moment.</p>
                <a href="register.php" class="btn-back" style="margin-top: 1rem; display: inline-block;">Créer un compte</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center;">
        <a href="index.html" class="btn-retour"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
    </div>
</div>

</body>
</html>