<?php
session_start();
require_once __DIR__ . '/../../controleurs/UserController.php';
require_once __DIR__ . '/../../models/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController = new UserController();
    $existingUser = $userController->findUserByEmail($_POST['email']);
    
    if ($existingUser) {
        $error = "Cet email est déjà utilisé";
    } else {
        if ($_POST['mot_de_passe'] !== $_POST['confirm_password']) {
            $error = "Les mots de passe ne correspondent pas";
        } else {
            $user = new User(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['mot_de_passe'],
                date('Y-m-d H:i:s'),
                'USER',
                'actif'
            );
            
            $result = $userController->addUser($user);
            
            if ($result) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header("refresh:2;url=login.php");
            } else {
                $error = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Créer un compte</title>
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>
    <div class="register-container">
        <div class="container">
            <div class="login-header">
                <h1>Inscription</h1>
                <p>Créez votre compte gratuitement</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <br>Redirection vers la page de connexion...
                </div>
            <?php endif; ?>
            
            <form id="registerForm" method="POST" onsubmit="return validateRegisterForm(event)">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>" required>
                    <small style="color: #666; font-size: 12px;">Doit commencer par une majuscule (ex: Dupont)</small>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>" required>
                    <small style="color: #666; font-size: 12px;">Doit commencer par une majuscule (ex: Jean)</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    <small style="color: #666; font-size: 12px;">exemple@domaine.com</small>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe *</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="acceptTerms" required>
                        J'accepte les conditions d'utilisation
                    </label>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p>Déjà inscrit ? <a href="login.php" style="color: #667eea;">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/user.js"></script>
</body>
</html>