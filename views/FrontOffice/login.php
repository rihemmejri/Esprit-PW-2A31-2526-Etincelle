<?php
session_start();
require_once __DIR__ . '/../../controleurs/UserController.php';
require_once __DIR__ . '/../../models/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController = new UserController();
    $user = $userController->login($_POST['email'], $_POST['mot_de_passe']);
    
    if ($user) {
        $_SESSION['user'] = $user;
        if ($user['role'] === 'ADMIN') {
            header('Location: ../BackOffice/list.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="login-header">
                <h1>Connexion</h1>
                <p>Connectez-vous à votre compte</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" onsubmit="return validateLoginForm(event)">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="register.php">Pas encore de compte ? Inscrivez-vous</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/user.js"></script>
</body>
</html>