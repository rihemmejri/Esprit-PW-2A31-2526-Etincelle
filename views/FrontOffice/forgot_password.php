<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - NutriLoop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#003366 0%,#4CAF50 100%); min-height:100vh; display:flex; justify-content:center; align-items:center; padding:20px; }
        .container { max-width:450px; width:100%; background:white; border-radius:30px; overflow:hidden; box-shadow:0 25px 50px rgba(0,0,0,0.3); animation:fadeInUp 0.6s ease; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        .header { background:linear-gradient(135deg,#003366 0%,#4CAF50 100%); padding:40px 30px; text-align:center; color:white; }
        .header i { font-size:3rem; margin-bottom:10px; }
        .header h1 { font-size:1.6rem; }
        .header p { font-size:0.85rem; opacity:0.9; margin-top:5px; }
        .body { padding:30px; }
        .form-group { margin-bottom:20px; }
        label { display:block; margin-bottom:8px; font-weight:500; color:#333; }
        .input-group { position:relative; }
        .input-group i { position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#999; }
        input { width:100%; padding:14px 15px 14px 45px; border:2px solid #e0e0e0; border-radius:12px; font-size:14px; font-family:'Poppins',sans-serif; }
        input:focus { outline:none; border-color:#4CAF50; box-shadow:0 0 0 3px rgba(76,175,80,0.1); }
        .btn { width:100%; padding:14px; background:linear-gradient(135deg,#4CAF50 0%,#003366 100%); color:white; border:none; border-radius:12px; font-size:16px; font-weight:600; cursor:pointer; transition:0.3s; }
        .btn:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(76,175,80,0.3); }
        .alert { padding:12px 15px; border-radius:10px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#d4edda; color:#155724; border-left:4px solid #28a745; }
        .alert-danger { background:#fee2e2; color:#dc2626; border-left:4px solid #dc2626; }
        .alert-info { background:#e3f2fd; color:#1565c0; border-left:4px solid #1565c0; }
        .back-link { text-align:center; margin-top:20px; padding-top:20px; border-top:1px solid #eee; }
        .back-link a { color:#4CAF50; text-decoration:none; font-weight:500; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-key"></i>
            <h1>Mot de passe oublié ?</h1>
            <p>Entrez votre email, nous vous enverrons un code à 4 chiffres</p>
        </div>
        <div class="body">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                    <i class="fas <?= $_SESSION['flash']['type'] === 'success' ? 'fa-check-circle' : ($_SESSION['flash']['type'] === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle') ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            
            <form action="/controleurs/AuthController.php?action=request" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required placeholder="exemple@email.com">
                    </div>
                </div>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Envoyer le code</button>
            </form>
            <div class="back-link"><a href="login.php"><i class="fas fa-arrow-left"></i> Retour à la connexion</a></div>
        </div>
    </div>
</body>
</html>