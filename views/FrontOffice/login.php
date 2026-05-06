<?php
session_start();
require_once __DIR__ . '/../../controleurs/UserController.php';
require_once __DIR__ . '/../../models/User.php';

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'ADMIN') {
        header('Location: ../BackOffice/choose_interface.php');
    } else {
        header('Location: index.html');
    }
    exit();
}

$error = '';
$locked_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController = new UserController();
    $user = $userController->login($_POST['email'], $_POST['mot_de_passe']);
    
    if ($user) {
        $_SESSION['user'] = $user;
        session_regenerate_id(true);
        if ($user['role'] === 'ADMIN') {
            header('Location: ../BackOffice/choose_interface.php');
        } else {
            header('Location: index.html');
        }
        exit();
    } else {
        if (isset($_SESSION['login_error'])) {
            $error = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - NutriLoop AI</title>
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
        }
        
        .login-container {
            max-width: 450px;
            width: 90%;
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease;
        }
        
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
        
        .login-header {
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header .logo {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .login-form {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4CAF50 0%, #003366 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.3);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            white-space: pre-line;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        .alert-warning {
            background: #fff3e0;
            color: #ff9800;
            border-left: 4px solid #ff9800;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #b91c1c;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
        }
        
        .forgot-password a {
            color: #666;
            font-size: 0.8rem;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .forgot-password a:hover {
            color: #4CAF50;
            text-decoration: underline;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .register-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .security-info {
            background: #f0f2f5;
            padding: 10px 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
        
        .lock-icon {
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">🍽️</div>
            <h1>NutriLoop AI</h1>
            <p>Connectez-vous à votre espace</p>
        </div>
       
        <div class="login-form">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas <?= strpos($error, 'verrouillé') !== false ? 'fa-lock' : 'fa-exclamation-circle' ?>"></i>
                    <?= nl2br(htmlspecialchars($error)) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required placeholder="exemple@email.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="mot_de_passe" required placeholder="••••••••">
                    </div>
                </div>
                
                <div class="forgot-password">
                    <a href="forgot_password.php">
                        <i class="fas fa-key"></i> Mot de passe oublié ?
                    </a>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i> Se connecter
                </button>
            </form>
            
            <div class="register-link">
                <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
            </div>
            
            <div class="security-info">
                <i class="fas fa-shield-alt"></i> Sécurité : après 3 tentatives échouées, votre compte sera verrouillé 15 minutes
            </div>
        </div>
    </div>
</body>
</html>