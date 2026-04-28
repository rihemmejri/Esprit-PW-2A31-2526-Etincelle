<?php session_start(); 

// Vérifier qu'il y a une session reset_email
if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot_password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification - NutriLoop</title>
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
        .body { padding:30px; }
        .code-input { text-align:center; font-size:2rem; letter-spacing:15px; font-weight:bold; border:2px solid #e0e0e0; border-radius:12px; padding:15px; width:100%; text-align:center; }
        .code-input:focus { border-color:#4CAF50; }
        .btn { width:100%; padding:14px; background:linear-gradient(135deg,#4CAF50 0%,#003366 100%); color:white; border:none; border-radius:12px; font-size:16px; font-weight:600; cursor:pointer; margin-top:10px; }
        .alert { padding:12px 15px; border-radius:10px; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-danger { background:#fee2e2; color:#dc2626; border-left:4px solid #dc2626; }
        .alert-success { background:#d4edda; color:#155724; border-left:4px solid #28a745; }
        .info { text-align:center; font-size:13px; color:#666; margin-top:15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-shield-alt"></i>
            <h1>Vérification</h1>
            <p>Entrez le code reçu par email</p>
        </div>
        <div class="body">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            
            <form action="../../controleurs/AuthController.php?action=verify" method="POST">
                <input type="text" name="code" class="code-input" placeholder="0000" maxlength="4" autocomplete="off" required>
                <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Vérifier le code</button>
            </form>
            <div class="info">
                <i class="fas fa-clock"></i> Le code expire dans 10 minutes
            </div>
        </div>
    </div>
</body>
</html>