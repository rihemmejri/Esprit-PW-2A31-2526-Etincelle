<?php
session_start();

// Vérification Admin uniquement
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

require_once __DIR__ . '/../../controleurs/UserController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User(
        null,
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        $_POST['mot_de_passe'],
        date('Y-m-d'),
        $_POST['role'],
        $_POST['statut']
    );
    
    $userController = new UserController();
    $userController->addUser($user);
    header('Location: list.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter utilisateur - NutriLoop</title>
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon">🍽️</div>
            NutriLoop
        </div>
        <div class="admin-badge">Admin</div>
        
        <div class="menu-title">DASHBOARD</div>
        <a href="dashboard.php">📊 Dashboard</a>
        
        <div class="menu-title">UTILISATEURS (MODULE 1)</div>
        <a href="list.php">👥 Gestion Utilisateurs</a>
        
        <div class="menu-title">NUTRITION SMART</div>
        <a href="#">🥗 Gestion Produits</a>
        <a href="#">🍳 Recettes Anti-Gaspi</a>
        <a href="#">📈 Suivi & Objectifs</a>
        <a href="#">🎉 Gestion Événements</a>
        
        <div style="margin-top: 30px;">
            <a href="../../logout.php">🚪 Déconnexion</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="backoffice-header">
            <h2>Backoffice</h2>
            <div class="backoffice-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="list.php">Utilisateurs</a>
                <a href="addUser.php">Ajouter</a>
                <a href="../../logout.php">Déconnexion</a>
            </div>
        </div>
        
        <div class="form-container">
            <h2>Ajouter un utilisateur</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nom:</label>
                    <input type="text" name="nom" required>
                </div>
                
                <div class="form-group">
                    <label>Prénom:</label>
                    <input type="text" name="prenom" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe:</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <label>Rôle:</label>
                    <select name="role">
                        <option value="USER">Client</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Statut:</label>
                    <select name="statut">
                        <option value="ACTIF">Actif</option>
                        <option value="INACTIF">Bloqué</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-success">✅ Ajouter</button>
                    <a href="list.php" class="btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>