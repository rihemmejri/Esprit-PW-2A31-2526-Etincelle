<?php
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix interface - NutriLoop AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            width: 100%;
        }
        
        .welcome-box {
            background: white;
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            animation: fadeInDown 0.6s ease;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .welcome-box .avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .welcome-box .avatar i {
            font-size: 40px;
            color: white;
        }
        
        .welcome-box h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome-box p {
            color: #666;
        }
        
        .badge-admin {
            display: inline-block;
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .choices {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            width: 280px;
            padding: 40px 30px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeInUp 0.6s ease;
            animation-fill-mode: both;
        }
        
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        
        .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }
        
        .card-front .card-icon {
            background: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
        }
        
        .card-back .card-icon {
            background: rgba(0, 51, 102, 0.1);
            color: #003366;
        }
        
        .card h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .card small {
            font-size: 12px;
            display: block;
            margin-top: 10px;
        }
        
        .card-front small {
            color: #4CAF50;
        }
        
        .card-back small {
            color: #003366;
        }
        
        .logout-link {
            text-align: center;
            margin-top: 40px;
        }
        
        .logout-link a {
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 25px;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .logout-link a:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-box">
            <div class="avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <h2>Bonjour, <?= htmlspecialchars($_SESSION['user']['prenom'] ?? $_SESSION['user']['nom']) ?></h2>
            <p>Bienvenue sur votre espace administrateur</p>
            <span class="badge-admin"><i class="fas fa-crown"></i> Administrateur</span>
        </div>
        
        <div class="choices">
            <!-- Front Office : views/FrontOffice/index.html -->
            <a href="../FrontOffice/index.html" class="card card-front">
                <div class="card-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3>Front Office</h3>
                <p>Accéder à la boutique comme un client</p>
                <small><i class="fas fa-shopping-cart"></i> Voir les produits, passer commande</small>
            </a>
            
            <!-- Back Office : views/BackOffice/index.html -->
            <a href="index.html" class="card card-back">
                <div class="card-icon">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <h3>Back Office</h3>
                <p>Accéder à l'administration</p>
                <small><i class="fas fa-gear"></i> Gérer produits, utilisateurs, commandes</small>
            </a>
        </div>
        
        <div class="logout-link">
            <a href="../FrontOffice/logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
        </div>
    </div>
</body>
</html>