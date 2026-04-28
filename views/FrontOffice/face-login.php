<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Face ID - NutriLoop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-core@3.18.0/dist/tf-core.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-converter@3.18.0/dist/tf-converter.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/face-landmarks-detection@0.0.1/dist/face-landmarks-detection.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl@3.18.0/dist/tf-backend-webgl.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    
    <style>
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#003366 0%,#4CAF50 100%); min-height:100vh; display:flex; justify-content:center; align-items:center; padding:20px; margin:0; }
        .container { max-width:500px; width:100%; background:white; border-radius:30px; padding:30px; text-align:center; box-shadow:0 25px 50px rgba(0,0,0,0.3); }
        h1 { color:#003366; margin-bottom:10px; }
        h1 i { color:#4CAF50; }
        video { width:100%; max-width:350px; border-radius:20px; border:3px solid #4CAF50; margin:20px 0; transform:scaleX(-1); background:#333; }
        .btn { background:linear-gradient(135deg,#4CAF50,#003366); color:white; border:none; padding:12px 30px; border-radius:30px; font-size:16px; font-weight:600; cursor:pointer; margin:10px; display:inline-block; text-decoration:none; transition:0.3s; }
        .btn:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(76,175,80,0.3); }
        .btn-secondary { background:#666; }
        .btn-secondary:hover { background:#555; }
        .status { margin-top:20px; padding:12px; border-radius:10px; font-size:14px; transition:0.3s; }
        .success { background:#d4edda; color:#155724; border-left:4px solid #28a745; }
        .error { background:#fee2e2; color:#dc2626; border-left:4px solid #dc2626; }
        .info { background:#e3f2fd; color:#1565c0; border-left:4px solid #1565c0; }
        .warning { background:#fff3e0; color:#e65100; border-left:4px solid #ff9800; }
        .footer { margin-top:20px; font-size:12px; color:#999; }
        .btn:disabled { opacity:0.6; cursor:not-allowed; transform:none; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-face-smile-beam"></i> Connexion Face ID</h1>
        <p>Regardez la caméra pour vous connecter</p>
        
        <video id="video" autoplay muted playsinline></video>
        
        <div>
            <button class="btn" id="verifyBtn">
                <i class="fas fa-camera"></i> Scanner mon visage
            </button>
            <a href="login.php" class="btn btn-secondary">
                <i class="fas fa-envelope"></i> Email / Mot de passe
            </a>
        </div>
        
        <div id="status" class="status info">
            <i class="fas fa-spinner fa-pulse"></i> Chargement des modèles...
        </div>
        <div class="footer">
            <i class="fas fa-info-circle"></i> Cliquez sur "Scanner" pour analyser votre visage
        </div>
    </div>

    <script>
        let video = document.getElementById('video');
        let statusDiv = document.getElementById('status');
        let verifyBtn = document.getElementById('verifyBtn');
        let stream = null;
        let modelsLoaded = false;
        let isProcessing = false;
        
        // Charger les modèles
        async function loadModels() {
            if (typeof faceapi === 'undefined') {
                statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ❌ FaceAPI non chargé. Vérifiez votre connexion.";
                statusDiv.className = "status error";
                verifyBtn.disabled = true;
                return;
            }
            
            statusDiv.innerHTML = "<i class='fas fa-spinner fa-pulse'></i> Chargement des modèles IA...";
            statusDiv.className = "status info";
            
            try {
                const modelsPath = './models/';
                
                await faceapi.nets.tinyFaceDetector.loadFromUri(modelsPath);
                await faceapi.nets.faceLandmark68Net.loadFromUri(modelsPath);
                await faceapi.nets.faceRecognitionNet.loadFromUri(modelsPath);
                
                statusDiv.innerHTML = "<i class='fas fa-check-circle'></i> ✅ Modèles chargés ! Démarrage de la caméra...";
                statusDiv.className = "status success";
                modelsLoaded = true;
                startCamera();
            } catch(err) {
                console.error(err);
                statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ❌ Erreur modèles: " + err.message;
                statusDiv.className = "status error";
                verifyBtn.disabled = true;
            }
        }
        
        // Démarrer la caméra
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "user" } 
                });
                video.srcObject = stream;
                statusDiv.innerHTML = "<i class='fas fa-check-circle'></i> ✅ Caméra active ! Cliquez sur 'Scanner'";
                statusDiv.className = "status success";
                verifyBtn.disabled = false;
            } catch(err) {
                statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ❌ Caméra inaccessible: " + err.message;
                statusDiv.className = "status error";
                verifyBtn.disabled = true;
            }
        }
        
        // Scanner (une seule fois)
        async function scanFace() {
            if (!modelsLoaded) {
                statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ⚠️ Modèles en chargement, veuillez patienter...";
                statusDiv.className = "status warning";
                return;
            }
            
            if (!video.srcObject) {
                statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ⚠️ Caméra non active...";
                statusDiv.className = "status warning";
                startCamera();
                return;
            }
            
            if (isProcessing) {
                statusDiv.innerHTML = "<i class='fas fa-spinner fa-pulse'></i> Scan en cours, veuillez patienter...";
                return;
            }
            
            isProcessing = true;
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = "<i class='fas fa-spinner fa-pulse'></i> Scan en cours...";
            
            statusDiv.innerHTML = "<i class='fas fa-spinner fa-pulse'></i> Analyse du visage...";
            statusDiv.className = "status info";
            
            try {
                // Détecter le visage
                const detections = await faceapi.detectSingleFace(
                    video, 
                    new faceapi.TinyFaceDetectorOptions()
                ).withFaceLandmarks().withFaceDescriptor();
                
                if (!detections) {
                    statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ❌ Aucun visage détecté ! Regardez bien la caméra et réessayez.";
                    statusDiv.className = "status error";
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = "<i class='fas fa-camera'></i> Scanner mon visage";
                    isProcessing = false;
                    return;
                }
                
                statusDiv.innerHTML = "<i class='fas fa-spinner fa-pulse'></i> Visage détecté ! Vérification...";
                statusDiv.className = "status info";
                
                const descriptor = Array.from(detections.descriptor);
                
                // Envoyer au serveur
                const response = await fetch('../../controleurs/FaceController.php?action=verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ face_descriptor: descriptor })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    statusDiv.innerHTML = "<i class='fas fa-check-circle'></i> ✅ Connexion réussie ! Redirection...";
                    statusDiv.className = "status success";
                    
                    setTimeout(() => {
                        if (result.user && result.user.role === 'ADMIN') {
                            window.location.href = '../../BackOffice/index.html';
                        } else {
                            window.location.href = 'index.html';
                        }
                    }, 1000);
                } else {
                    statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ❌ Visage non reconnu. " + (result.message || 'Réessayez.');
                    statusDiv.className = "status error";
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = "<i class='fas fa-camera'></i> Scanner mon visage";
                }
                
            } catch(err) {
                console.error(err);
                statusDiv.innerHTML = "<i class='fas fa-exclamation-triangle'></i> ❌ Erreur: " + err.message;
                statusDiv.className = "status error";
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = "<i class='fas fa-camera'></i> Scanner mon visage";
            }
            
            isProcessing = false;
        }
        
        // Bouton scan
        verifyBtn.onclick = scanFace;
        
        // Démarrer
        window.onload = loadModels;
        
        // Nettoyer
        window.onbeforeunload = () => {
            if (stream) stream.getTracks().forEach(track => track.stop());
        };
    </script>
</body>
</html>