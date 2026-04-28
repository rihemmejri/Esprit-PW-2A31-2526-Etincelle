<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement Facial - NutriLoop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-core"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-converter"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@tensorflow-models/face-landmarks-detection"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #003366 0%, #4CAF50 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 30px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        video {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            border: 3px solid #4CAF50;
            margin: 20px 0;
            transform: scaleX(-1);
        }
        canvas {
            position: absolute;
            top: 0;
            left: 0;
        }
        .video-container {
            position: relative;
            display: inline-block;
        }
        .btn {
            background: linear-gradient(135deg, #4CAF50, #003366);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 10px;
            transition: 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(76,175,80,0.3);
        }
        .status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 10px;
            font-size: 14px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #fee2e2; color: #dc2626; }
        .info { background: #e3f2fd; color: #1565c0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 Enregistrement Facial</h1>
        <p>Regardez la caméra et cliquez sur "Capturer mon visage"</p>
        
        <div class="video-container">
            <video id="video" autoplay muted playsinline></video>
        </div>
        
        <div>
            <button class="btn" id="captureBtn">📷 Capturer mon visage</button>
            <button class="btn" id="retryBtn" style="display:none;">🔄 Recommencer</button>
            <a href="login.php" class="btn" style="background: #666;">← Retour</a>
        </div>
        
        <div id="status" class="status info">⏳ Chargement de la caméra...</div>
    </div>

    <script>
        let video = document.getElementById('video');
        let statusDiv = document.getElementById('status');
        let captureBtn = document.getElementById('captureBtn');
        let retryBtn = document.getElementById('retryBtn');
        let stream = null;
        
        // Charger les modèles FaceAPI
        async function loadModels() {
            statusDiv.innerHTML = "⏳ Chargement des modèles IA...";
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                statusDiv.innerHTML = "✅ Modèles chargés ! Activez la caméra...";
                startCamera();
            } catch(err) {
                statusDiv.innerHTML = "❌ Erreur de chargement: " + err.message;
                statusDiv.className = "status error";
            }
        }
        
        // Démarrer la caméra
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                statusDiv.innerHTML = "✅ Caméra active ! Regardez l'écran et capturez.";
                statusDiv.className = "status success";
            } catch(err) {
                statusDiv.innerHTML = "❌ Impossible d'accéder à la caméra: " + err.message;
                statusDiv.className = "status error";
            }
        }
        
        // Capturer le visage
        async function captureFace() {
            statusDiv.innerHTML = "⏳ Analyse du visage...";
            statusDiv.className = "status info";
            
            const detections = await faceapi.detectSingleFace(
                video, 
                new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks().withFaceDescriptor();
            
            if (!detections) {
                statusDiv.innerHTML = "❌ Aucun visage détecté ! Regardez bien la caméra.";
                statusDiv.className = "status error";
                return;
            }
            
            // Récupérer le descripteur facial (128 chiffres)
            const descriptor = Array.from(detections.descriptor);
            
            // Envoyer au serveur
            const response = await fetch('../controleurs/FaceController.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: <?= json_encode($_SESSION['user']['id_user'] ?? null) ?>,
                    face_descriptor: descriptor
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                statusDiv.innerHTML = "✅ Visage enregistré avec succès ! Vous pouvez maintenant vous connecter avec Face ID.";
                statusDiv.className = "status success";
                captureBtn.style.display = "none";
                retryBtn.style.display = "inline-block";
            } else {
                statusDiv.innerHTML = "❌ Erreur: " + result.message;
                statusDiv.className = "status error";
            }
        }
        
        captureBtn.onclick = captureFace;
        retryBtn.onclick = () => { location.reload(); };
        
        // Démarrer
        loadModels();
        
        // Nettoyer caméra à la fermeture
        window.onbeforeunload = () => {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        };
    </script>
</body>
</html>