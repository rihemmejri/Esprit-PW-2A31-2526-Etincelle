<?php
session_start();
require_once '../../controleurs/UserController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$users = $userController->listUsers();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice - Gestion des utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 30px;
        }

        h1 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .welcome-text {
            color: #4CAF50;
            margin-right: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-success {
            background: #4CAF50;
            color: white;
        }

        .btn-success:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .btn-info {
            background: #2196F3;
            color: white;
        }

        .btn-info:hover {
            background: #0b7dda;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ff9800;
            color: white;
        }

        .btn-warning:hover {
            background: #e68900;
            transform: translateY(-2px);
        }

        .btn-dashboard {
            background: #003366;
            color: white;
        }

        .btn-dashboard:hover {
            background: #4CAF50;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .user-table thead {
            background: linear-gradient(135deg, #003366, #4CAF50);
            color: white;
        }

        .user-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .user-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .user-table tbody tr:hover {
            background: #f9f9f9;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .role-admin {
            background: #f44336;
            color: white;
        }

        .role-user {
            background: #4CAF50;
            color: white;
        }

        .statut-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .statut-actif {
            background: #4CAF50;
            color: white;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .actions .btn {
            padding: 5px 12px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            .user-table {
                font-size: 12px;
                display: block;
                overflow-x: auto;
            }
            .header-flex {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-flex">
            <h1><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
            <div class="header-buttons">
                <span class="welcome-text">
                    <i class="fas fa-user-check"></i> Bienvenue, <strong><?= htmlspecialchars($_SESSION['user']['prenom']) ?> <?= htmlspecialchars($_SESSION['user']['nom']) ?></strong>
                </span>
                <a href="add.php" class="btn btn-success"><i class="fas fa-plus"></i> Ajouter</a>
                <a href="../FrontOffice/index.php" class="btn btn-info"><i class="fas fa-eye"></i> Frontoffice</a>
                <a href="index.html" class="btn btn-dashboard"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="../FrontOffice/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php 
                    if ($_GET['success'] == 1) echo "Opération effectuée avec succès !";
                    if ($_GET['success'] == 2) echo "Utilisateur ajouté avec succès !";
                    if ($_GET['success'] == 3) echo "Utilisateur modifié avec succès !";
                    if ($_GET['success'] == 4) echo "Utilisateur supprimé avec succès !";
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                    if ($_GET['error'] == 1) echo "Une erreur est survenue lors de l'opération !";
                    if ($_GET['error'] == 2) echo "Utilisateur non trouvé !";
                ?>
            </div>
        <?php endif; ?>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 0;
                while($user = $users->fetch(PDO::FETCH_ASSOC)): 
                    $count++;
                ?>
                <tr>
                    <td><?= htmlspecialchars($user['id_user']) ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="role-badge <?= $user['role'] === 'ADMIN' ? 'role-admin' : 'role-user' ?>">
                            <?= $user['role'] === 'ADMIN' ? 'Administrateur' : 'Utilisateur' ?>
                        </span>
                    </td>
                    <td>
                        <span class="statut-badge statut-actif">
                            <?= ucfirst($user['statut']) ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($user['date_inscription'])) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $user['id_user'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Modifier</a>
                        <button onclick="confirmDelete(<?= $user['id_user'] ?>, '<?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?>')" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
                    </td
                </tr>
                <?php endwhile; ?>
                
                <?php if ($count == 0): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px;">
                        <i class="fas fa-user-slash" style="font-size: 3rem; color: #ccc;"></i>
                        <p>Aucun utilisateur trouvé</p>
                    </td
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(userId, userName) {
            if(confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
                window.location.href = `delete.php?id=${userId}`;
            }
        }
    </script>
</body>
</html>