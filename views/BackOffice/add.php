<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../controleurs/UserController.php';
require_once '../../models/User.php';

// Vérification session ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
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
    <title>Ajouter un utilisateur - NutriLoop Admin</title>
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
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header avec retour */
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

        /* Carte formulaire */
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

        /* Champs avec icônes */
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

        /* Alertes */
        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
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

        /* Boutons */
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

        /* Ligne pour deux champs côte à côte */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        /* Animation */
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
        <!-- Header avec retour -->
        <div class="page-header">
            <a href="list.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <h1>
                <i class="fas fa-user-plus"></i>
                Ajouter un utilisateur
            </h1>
        </div>

        <!-- Message d'erreur -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-address-card"></i> Nouvel utilisateur</h2>
                <p>Remplissez les informations ci-dessous pour créer un nouveau compte</p>
            </div>
            <div class="form-body">
                <form id="addUserForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Nom</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="nom" name="nom" placeholder="Dupont" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required">Prénom</label>
                            <div class="input-icon">
                                <i class="fas fa-user-circle"></i>
                                <input type="text" id="prenom" name="prenom" placeholder="Jean" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="exemple@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required">Mot de passe</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="required">Rôle</label>
                            <select id="role" name="role" required>
                                <option value="USER">👤 Utilisateur</option>
                                <option value="ADMIN">👑 Administrateur</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Statut</label>
                            <select id="statut" name="statut">
                                <option value="actif">✅ Actif</option>
                                <option value="inactif">⭕ Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Ajouter l'utilisateur
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
        function validateAddForm(event) {
            let nom = document.getElementById('nom').value.trim();
            let prenom = document.getElementById('prenom').value.trim();
            let email = document.getElementById('email').value.trim();
            let mdp = document.getElementById('mot_de_passe').value.trim();
            
            if (nom === '' || prenom === '' || email === '' || mdp === '') {
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
            
            if (mdp.length < 4) {
                alert('Le mot de passe doit contenir au moins 4 caractères');
                event.preventDefault();
                return false;
            }
            
            return true;
        }
        
        document.getElementById('addUserForm').addEventListener('submit', validateAddForm);
    </script>
</body>
</html>