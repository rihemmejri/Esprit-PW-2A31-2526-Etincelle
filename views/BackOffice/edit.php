<?php
session_start();
require_once '../../controleurs/UserController.php';
require_once '../../models/User.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$user = $userController->showUser($_GET['id']);

if (!$user) {
    header('Location: list.php?error=1');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedUser = new User(
        $_GET['id'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        null,
        $_POST['date_inscription'],
        strtoupper($_POST['role']),
        $_POST['statut']
    );
    
    $result = $userController->updateUser($updatedUser, $_GET['id']);
    
    if ($result) {
        header('Location: list.php?success=3');
        exit();
    } else {
        $error = "Erreur lors de la modification";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur</title>
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>
    <div class="container">
        <h1>Modifier l'utilisateur</h1>
        
        <a href="list.php" class="btn btn-info">← Retour</a>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form id="editUserForm" method="POST" onsubmit="return validateEditForm(event)">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Rôle *</label>
                <select id="role" name="role" required>
                    <option value="USER" <?= $user['role'] === 'USER' ? 'selected' : '' ?>>Utilisateur</option>
                    <option value="ADMIN" <?= $user['role'] === 'ADMIN' ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="actif" <?= $user['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="inactif" <?= $user['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                </select>
            </div>
            
            <input type="hidden" name="date_inscription" value="<?= $user['date_inscription'] ?>">
            
            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary">Modifier</button>
                <a href="list.php" class="btn btn-danger">Annuler</a>
            </div>
        </form>
    </div>
    
    <script src="../assets/js/user.js"></script>
</body>
</html>