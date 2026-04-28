<?php
session_start();

// Anti-cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once '../../controleurs/UserController.php';
require_once '../../models/User.php';

// Vérification session ADMIN
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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = strtoupper(trim($_POST['role'] ?? 'USER'));
    $statut = strtoupper(trim($_POST['statut'] ?? 'ACTIF')); // ← CORRIGÉ: mettre en majuscule
    
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = "Tous les champs obligatoires doivent être remplis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide";
    } else {
        // 1. Mettre à jour nom, prénom, email, role
        $updatedUser = new User(
            $_GET['id'],
            $nom,
            $prenom,
            $email,
            null,
            $user['date_inscription'],
            $role,
            $user['statut']  // Garder l'ancien statut pour l'instant
        );
        
        $result = $userController->updateUser($updatedUser, $_GET['id']);
        
        // 2. Mettre à jour le statut séparément
        if ($result) {
            $statusResult = $userController->changeStatus($_GET['id'], $statut); // ← $statut est maintenant en majuscule
            
            if ($statusResult) {
                header("Location: edit.php?id=" . $_GET['id'] . "&success=1");
                exit();
            } else {
                $error = "Erreur lors de la modification du statut";
            }
        } else {
            $error = "Erreur lors de la modification des informations";
        }
    }
}

// Récupérer le message de succès après redirection
if (isset($_GET['success'])) {
    $success = "✅ Utilisateur modifié avec succès !";
    // Recharger l'utilisateur depuis la base
    $user = $userController->showUser($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur - NutriLoop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Ton CSS reste le même */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #003366;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-back:hover {
            background: #4CAF50;
            transform: translateX(-5px);
        }

        .page-header h1 {
            color: #003366;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .page-header h1 i {
            color: #4CAF50;
            margin-right: 10px;
        }

        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            padding: 25px 30px;
            color: white;
        }

        .form-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-header p {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .form-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
        }

        .required::after {
            content: " *";
            color: #dc2626;
        }

        input, select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1rem;
        }

        .input-icon input {
            padding-left: 45px;
        }

        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            animation: fadeIn 0.3s ease;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-submit {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-submit:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-cancel {
            background: #e8e8e8;
            color: #666;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-cancel:hover {
            background: #ddd;
            transform: translateY(-2px);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-badge {
            background: #f0f2f5;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #666;
        }

        .info-badge i {
            color: #4CAF50;
            font-size: 1rem;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .form-body {
                padding: 20px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-card {
            animation: fadeIn 0.4s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="list.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <h1>
                <i class="fas fa-user-edit"></i>
                Modifier l'utilisateur
            </h1>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-address-card"></i> Informations utilisateur</h2>
                <p>Modifiez les informations du compte #<?= htmlspecialchars($user['id_user']) ?></p>
            </div>
            <div class="form-body">
                <div class="info-badge">
                    <i class="fas fa-calendar-alt"></i>
                    Membre depuis le <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                </div>

                <form id="editUserForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Nom</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required">Prénom</label>
                            <div class="input-icon">
                                <i class="fas fa-user-circle"></i>
                                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Rôle</label>
                            <select id="role" name="role" required>
                                <option value="USER" <?= $user['role'] === 'USER' ? 'selected' : '' ?>>👤 Utilisateur</option>
                                <option value="ADMIN" <?= $user['role'] === 'ADMIN' ? 'selected' : '' ?>>👑 Administrateur</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Statut</label>
                            <select id="statut" name="statut">
                                <option value="ACTIF" <?= strtoupper($user['statut']) === 'ACTIF' ? 'selected' : '' ?>>✅ Actif</option>
                                <option value="INACTIF" <?= strtoupper($user['statut']) === 'INACTIF' ? 'selected' : '' ?>>⭕ Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="info-badge" style="background: #fff3e0; margin-top: 10px;">
                        <i class="fas fa-info-circle"></i>
                        Le mot de passe ne peut pas être modifié depuis cette page.
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="list.php" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function validateEditForm(event) {
            let nom = document.getElementById('nom').value.trim();
            let prenom = document.getElementById('prenom').value.trim();
            let email = document.getElementById('email').value.trim();
            
            if (nom === '' || prenom === '' || email === '') {
                alert('Veuillez remplir tous les champs obligatoires');
                event.preventDefault();
                return false;
            }
            
            const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Veuillez entrer un email valide');
                event.preventDefault();
                return false;
            }
            
            return true;
        }
        
        document.getElementById('editUserForm').addEventListener('submit', validateEditForm);
    </script>
</body>
</html>