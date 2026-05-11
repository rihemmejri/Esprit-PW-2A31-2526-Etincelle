<!DOCTYPE html>
<html>
<head>
    <title>Notification Test</title>
    <style>
        .toast-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1050;
        }
        .toast {
            background: white;
            border-left: 4px solid #4CAF50;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease forwards;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        button { margin: 10px; padding: 10px 20px; }
    </style>
</head>
<body>
    <h1>Notification System Test</h1>
    
    <button onclick="testBasicToast()">Test Basic Toast</button>
    <button onclick="testAPI()">Test API Call</button>
    <button onclick="testManualNotification()">Manual Notification</button>
    
    <div id="toastContainer" class="toast-container"></div>

    <script>
        function showToast(message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fas fa-bell"></i> <div>${message}</div>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function testBasicToast() {
            console.log('Testing basic toast...');
            showToast('Basic toast test successful!');
        }

        function testAPI() {
            console.log('Testing API call...');
            fetch('views/BackOffice/api/check_new_programs.php?last_id=0')
                .then(res => res.json())
                .then(data => {
                    console.log('API Response:', data);
                    showToast('API Test: ' + JSON.stringify(data).substring(0, 100) + '...');
                })
                .catch(err => {
                    console.error('API Error:', err);
                    showToast('API Failed: ' + err.message);
                });
        }

        function testManualNotification() {
            console.log('Manual notification test...');
            showToast('This is a manual notification test!');
        }
    </script>
</body>
</html>
