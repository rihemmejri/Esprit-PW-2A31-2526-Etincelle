<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Nutritionnel AI - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #003366;
            --success-green: #4CAF50;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); height: 100vh; display: flex; flex-direction: column; }

        .chat-container {
            max-width: 900px;
            margin: 20px auto;
            width: 95%;
            flex-grow: 1;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .chat-header {
            padding: 20px 30px;
            background: var(--primary-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header h1 {
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-header i.fa-robot {
            background: var(--success-green);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .messages-area {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
            background: #f9fbfd;
        }

        .message {
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 18px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.user {
            align-self: flex-end;
            background: var(--success-green);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.ai {
            align-self: flex-start;
            background: white;
            color: var(--text-dark);
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #eee;
        }

        .chat-input-area {
            padding: 20px 30px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .input-wrapper {
            flex-grow: 1;
            position: relative;
        }

        #user-input {
            width: 100%;
            padding: 15px 20px;
            border-radius: 12px;
            border: 2px solid #eee;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        #user-input:focus {
            border-color: var(--success-green);
        }

        #send-btn {
            background: var(--success-green);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        #send-btn:hover {
            transform: scale(1.05);
            background: #43a047;
        }

        #send-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .typing-indicator {
            display: none;
            align-self: flex-start;
            background: white;
            padding: 10px 20px;
            border-radius: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #eee;
            gap: 5px;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: #ccc;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
        }

        .header-back {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .header-back:hover {
            color: white;
        }

    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h1><i class="fas fa-robot"></i> NutriBot AI</h1>
            <a href="index.html" class="header-back"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>
        </div>

        <div class="messages-area" id="messages-area">
            <div class="message ai">
                Bonjour ! Je suis votre assistant nutritionnel NutriLoop. 👋<br>
                Décrivez-moi votre repas (ex: "2 œufs + pain + café") et je vais estimer les calories pour vous.
            </div>
        </div>

        <div class="typing-indicator" id="typing-indicator">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <div class="chat-input-area">
            <div class="input-wrapper">
                <input type="text" id="user-input" placeholder="Décrivez votre repas..." autocomplete="off">
            </div>
            <button id="send-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        const messagesArea = document.getElementById('messages-area');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');
        const typingIndicator = document.getElementById('typing-indicator');

        function addMessage(content, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.innerHTML = content;
            messagesArea.appendChild(messageDiv);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            userInput.value = '';
            addMessage(message, 'user');
            
            // Show typing indicator
            typingIndicator.style.display = 'flex';
            messagesArea.scrollTop = messagesArea.scrollHeight;

            try {
                const response = await fetch('../../controleurs/ChatbotController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                // Hide typing indicator
                typingIndicator.style.display = 'none';

                if (data.response) {
                    addMessage(data.response.replace(/\n/g, '<br>'), 'ai');
                } else {
                    addMessage('Une erreur est survenue lors de la communication avec l\'IA.', 'ai');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                addMessage('Désolé, le service IA est indisponible pour le moment.', 'ai');
                console.error('Error:', error);
            }
        }

        sendBtn.addEventListener('click', sendMessage);
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>
