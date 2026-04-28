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
    <style>
        .password-strength-container {
            margin-top: 8px;
        }

        .strength-bar {
            height: 6px;
            background-color: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .strength-progress {
            width: 0%;
            height: 100%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }

        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }

        .strength-faible { background-color: #f44336; }
        .strength-moyen { background-color: #ff9800; }
        .strength-fort { background-color: #4caf50; }

        .text-faible { color: #f44336; }
        .text-moyen { color: #ff9800; }
        .text-fort { color: #4caf50; }

        @keyframes loading-pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .strength-progress.loading {
            animation: loading-pulse 1s ease-in-out infinite;
        }
        
        .error-message {
            color: #f44336;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .login-header p {
            color: #666;
            margin: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-header">
            <h1>Inscription</h1>
            <p>Créez votre compte gratuitement</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
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
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" id="acceptTerms" required style="width: auto; margin: 0;">
                    <span>J'accepte les conditions d'utilisation</span>
                </label>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn-primary">S'inscrire</button>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Déjà inscrit ? <a href="login.php" style="color: #667eea; text-decoration: none; font-weight: 500;">Se connecter</a></p>
            </div>
        </form>
    </div>
    
    <script>
        function validateRegisterForm(event) {
            const nom = document.getElementById('nom');
            const prenom = document.getElementById('prenom');
            const email = document.getElementById('email');
            const motDePasse = document.getElementById('mot_de_passe');
            const confirmPassword = document.getElementById('confirm_password');
            const acceptTerms = document.getElementById('acceptTerms');
            
            clearErrors();
            let isValid = true;
            
            const nomRegex = /^[A-Z][a-zA-ZÀ-ÿ\s-]*$/;
            if (!nom.value.trim()) {
                showError(nom, 'Le nom est requis');
                isValid = false;
            } else if (!nomRegex.test(nom.value.trim())) {
                showError(nom, 'Le nom doit commencer par une majuscule (ex: Dupont)');
                isValid = false;
            }
            
            if (!prenom.value.trim()) {
                showError(prenom, 'Le prénom est requis');
                isValid = false;
            } else if (!nomRegex.test(prenom.value.trim())) {
                showError(prenom, 'Le prénom doit commencer par une majuscule (ex: Jean)');
                isValid = false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                showError(email, 'L\'email est requis');
                isValid = false;
            } else if (!emailRegex.test(email.value.trim())) {
                showError(email, 'Veuillez entrer un email valide (exemple@domaine.com)');
                isValid = false;
            }
            
            if (!motDePasse.value) {
                showError(motDePasse, 'Le mot de passe est requis');
                isValid = false;
            }
            
            if (motDePasse.value !== confirmPassword.value) {
                showError(confirmPassword, 'Les mots de passe ne correspondent pas');
                isValid = false;
            }
            
            if (!acceptTerms.checked) {
                alert('Vous devez accepter les conditions d\'utilisation');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
            
            return isValid;
        }

        function showError(input, message) {
            const formGroup = input.closest('.form-group');
            if (formGroup) {
                const existingError = formGroup.querySelector('.error-message');
                if (existingError) existingError.remove();
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerText = message;
                formGroup.appendChild(errorDiv);
                input.style.borderColor = '#f44336';
                input.style.borderWidth = '2px';
            }
        }

        function clearErrors() {
            const errors = document.querySelectorAll('.error-message');
            errors.forEach(error => error.remove());
            
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.style.borderColor = '#e0e0e0';
                input.style.borderWidth = '1px';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const motDePasseInput = document.getElementById('mot_de_passe');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            if (motDePasseInput) {
                const strengthContainer = document.createElement('div');
                strengthContainer.className = 'password-strength-container';
                strengthContainer.innerHTML = `
                    <div class="strength-bar">
                        <div class="strength-progress"></div>
                    </div>
                    <span class="strength-text"></span>
                `;
                
                motDePasseInput.parentNode.insertBefore(strengthContainer, motDePasseInput.nextSibling);
                
                const strengthProgress = strengthContainer.querySelector('.strength-progress');
                const strengthText = strengthContainer.querySelector('.strength-text');
                
                function checkPasswordStrength(password) {
                    let message = '';
                    let className = '';
                    let progressWidth = 0;
                    
                    if (password.length === 0) {
                        return { message: '', className: '', progressWidth: 0 };
                    }
                    
                    const hasLetters = /[a-zA-Z]/.test(password);
                    const hasNumbers = /[0-9]/.test(password);
                    const hasSymbols = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                    
                    // Faible : 1 seul type (lettres uniquement OU chiffres uniquement OU symboles uniquement)
                    if ((hasLetters && !hasNumbers && !hasSymbols) || 
                        (!hasLetters && hasNumbers && !hasSymbols) || 
                        (!hasLetters && !hasNumbers && hasSymbols)) {
                        message = '🔒 Mot de passe faible';
                        className = 'strength-faible text-faible';
                        progressWidth = 33;
                    }
                    // Moyen : 2 types (lettres+chiffres OU lettres+symboles OU chiffres+symboles)
                    else if ((hasLetters && hasNumbers && !hasSymbols) || 
                             (hasLetters && !hasNumbers && hasSymbols) || 
                             (!hasLetters && hasNumbers && hasSymbols)) {
                        message = '⚠️ Mot de passe moyen';
                        className = 'strength-moyen text-moyen';
                        progressWidth = 66;
                    }
                    // Fort : les 3 types ensemble (lettres + chiffres + symboles)
                    else if (hasLetters && hasNumbers && hasSymbols) {
                        message = '✅ Mot de passe fort';
                        className = 'strength-fort text-fort';
                        progressWidth = 100;
                    }
                    else {
                        message = '🔒 Mot de passe faible';
                        className = 'strength-faible text-faible';
                        progressWidth = 33;
                    }
                    
                    return { message, className, progressWidth };
                }
                
                let typingTimer;
                
                motDePasseInput.addEventListener('input', function() {
                    const password = this.value;
                    strengthProgress.classList.add('loading');
                    clearTimeout(typingTimer);
                    
                    typingTimer = setTimeout(function() {
                        const result = checkPasswordStrength(password);
                        strengthProgress.style.width = result.progressWidth + '%';
                        strengthProgress.classList.remove('loading');
                        strengthProgress.className = 'strength-progress';
                        if (result.className) {
                            const colorClass = result.className.split(' ')[0];
                            strengthProgress.classList.add(colorClass);
                        }
                        
                        strengthText.textContent = result.message;
                        strengthText.className = 'strength-text';
                        if (result.className) {
                            const textClass = result.className.split(' ')[1];
                            if (textClass) strengthText.classList.add(textClass);
                        }
                        
                        if (confirmPasswordInput && confirmPasswordInput.value) {
                            checkPasswordMatch();
                        }
                    }, 300);
                });
            }
            
            function checkPasswordMatch() {
                const motDePasse = document.getElementById('mot_de_passe');
                const confirmPassword = document.getElementById('confirm_password');
                
                if (motDePasse && confirmPassword) {
                    const matchMessage = document.getElementById('matchMessage');
                    const successMatch = document.getElementById('successMatch');
                    
                    if (confirmPassword.value && motDePasse.value !== confirmPassword.value) {
                        if (!matchMessage && !successMatch) {
                            const msg = document.createElement('div');
                            msg.id = 'matchMessage';
                            msg.style.color = '#f44336';
                            msg.style.fontSize = '12px';
                            msg.style.marginTop = '5px';
                            msg.style.fontWeight = '500';
                            msg.innerHTML = '❌ Les mots de passe ne correspondent pas';
                            confirmPassword.parentNode.appendChild(msg);
                        } else if (successMatch) {
                            successMatch.remove();
                        }
                        confirmPassword.style.borderColor = '#f44336';
                        confirmPassword.style.borderWidth = '2px';
                    } else if (confirmPassword.value && motDePasse.value === confirmPassword.value) {
                        if (matchMessage) matchMessage.remove();
                        if (!successMatch) {
                            const msg = document.createElement('div');
                            msg.id = 'successMatch';
                            msg.style.color = '#4caf50';
                            msg.style.fontSize = '12px';
                            msg.style.marginTop = '5px';
                            msg.style.fontWeight = '500';
                            msg.innerHTML = '✓ Les mots de passe correspondent';
                            confirmPassword.parentNode.appendChild(msg);
                        }
                        confirmPassword.style.borderColor = '#4caf50';
                        confirmPassword.style.borderWidth = '2px';
                    } else if (!confirmPassword.value) {
                        if (matchMessage) matchMessage.remove();
                        if (successMatch) successMatch.remove();
                        confirmPassword.style.borderColor = '#e0e0e0';
                        confirmPassword.style.borderWidth = '1px';
                    }
                }
            }
            
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            }
        });
    </script>
</body>
</html>