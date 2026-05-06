<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../controleurs/UserController.php';
require_once '../../models/User.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../FrontOffice/login.php');
    exit();
}

if ($_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['mot_de_passe']) || empty($_POST['role'])) {
        $error = "Tous les champs obligatoires doivent être remplis";
    } else {
        $userController = new UserController();
        $existingUser = $userController->findUserByEmail($_POST['email']);
        
        if ($existingUser) {
            $error = "Cet email est déjà utilisé";
        } else {
            $user = new User(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['mot_de_passe'],
                date('Y-m-d H:i:s'),
                strtoupper($_POST['role']),
                $_POST['statut']
            );
            
            $result = $userController->addUser($user);
            
            if ($result) {
                header('Location: list.php?success=2');
                exit();
            } else {
                $error = "Erreur lors de l'ajout";
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
    <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un utilisateur</h1>
        
        <a href="list.php" class="btn btn-info">← Retour</a>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form id="addUserForm" method="POST" onsubmit="return validateAddForm(event)">
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            
            <div class="form-group">
                <label>Prénom *</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe *</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            
            <div class="form-group">
                <label>Rôle *</label>
                <select id="role" name="role" required>
                    <option value="">Sélectionnez</option>
                    <option value="USER">Utilisateur</option>
                    <option value="ADMIN">Administrateur</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Statut</label>
                <select id="statut" name="statut">
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="list.php" class="btn btn-danger">Annuler</a>
        </form>
    </div>
    
    <script src="../assets/js/user.js"></script>
</body>
</html>