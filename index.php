<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour - Calidad y Sabor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/CSS/style.css">
    <style>
        /* Estilos del Chatbot */
        .chatbot-trigger {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #C5A059;
            color: #1A1A1A;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            z-index: 1000;
            transition: transform 0.3s;
        }
        .chatbot-trigger:hover {
            transform: scale(1.1);
        }
        .chatbot-container {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 320px;
            height: 450px;
            background-color: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid #C5A059;
            border-radius: 12px;
            display: none;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
            z-index: 1000;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }
        .chatbot-header {
            background-color: #C5A059;
            color: #1A1A1A;
            padding: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .close-chat {
            background: none;
            border: none;
            font-size: 24px;
            color: #1A1A1A;
            cursor: pointer;
            line-height: 1;
        }
        .chatbot-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .message {
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 85%;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }
        .bot-message {
            background-color: #2a2a2a;
            color: #F5F5DC;
            align-self: flex-start;
            border-bottom-left-radius: 2px;
            border: 1px solid #333;
        }
        .user-message {
            background-color: #C5A059;
            color: #1A1A1A;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
            font-weight: 500;
        }
        .chatbot-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #3d3428;
            background: rgba(17, 17, 17, 0.9);
        }
        .chatbot-input input {
            flex: 1;
            background: transparent;
            border: none;
            color: #F5F5DC;
            outline: none;
            padding: 8px;
            font-family: 'Inter', sans-serif;
        }
        .chatbot-input button {
            background: none;
            border: none;
            color: #C5A059;
            font-size: 18px;
            cursor: pointer;
            padding: 0 10px;
            transition: color 0.3s;
        }
        .chatbot-input button:hover {
            color: #F5F5DC;
        }
    </style>
</head>
<body class="welcome-bg">

<div class="welcome-overlay">
    <div class="welcome-content animate__animated animate__zoomIn" style="animation-duration: 0.8s;">
        <div class="welcome-logo">
        </div>
        
        <h1 class="welcome-title animate__animated animate__fadeInDown" style="animation-delay: 0.3s;">LIQUOUR</h1>
        <p class="welcome-slogan animate__animated animate__fadeIn" style="animation-delay: 0.6s;">Calidad • Sabor • Exclusividad</p>
        
        <div class="welcome-message animate__animated animate__fadeInUp" style="animation-delay: 0.9s;">
            <p>Descubre la selección más fina de licores y vinos reserva en un solo lugar. Una experiencia diseñada para los paladares más exigentes.</p>
        </div>

        <div class="welcome-actions animate__animated animate__bounceIn" style="animation-delay: 1.2s;">
            <a href="Views/login/login.php" class="btn-welcome-login" style="text-decoration: none; display: inline-block;">INICIAR SESIÓN</a>
        </div>

        <div class="welcome-footer animate__animated animate__fadeIn" style="animation-delay: 1.5s;">
            <p>&copy; 2026 Liquour Licorería Profesional. Todos los derechos reservados.</p>
        </div>
    </div>
</div>

<!-- Chatbot Widget -->
<div id="chatbot-container" class="chatbot-container animate__animated animate__fadeInUp">
    <div class="chatbot-header">
        <span><i class="fas fa-robot"></i> Asistente Liquour</span>
        <button onclick="toggleChatbot()" class="close-chat">&times;</button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="message bot-message">¡Hola! Soy tu asistente virtual de Liquour. ¿En qué te puedo ayudar hoy?</div>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chat-input" placeholder="Escribe un mensaje..." onkeypress="handleKeyPress(event)">
        <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>
<button id="chatbot-trigger" class="chatbot-trigger animate__animated animate__bounceIn" style="animation-delay: 1.8s;" onclick="toggleChatbot()">
    <i class="fas fa-comments"></i>
</button>

<script src="Assets/JS/validacion.js"></script>
<script>
    function toggleChatbot() {
        const chat = document.getElementById('chatbot-container');
        chat.style.display = chat.style.display === 'flex' ? 'none' : 'flex';
        if (chat.style.display === 'flex') {
            document.getElementById('chat-input').focus();
        }
    }

    function handleKeyPress(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    }

    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;
        
        appendMessage('user-message', text);
        input.value = '';
        
        // Mostrar "Escribiendo..."
        const typingId = 'typing-' + Date.now();
        appendMessage('bot-message', '...', typingId);
        
        try {
            const response = await fetch('Controller/ChatbotController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });
            
            const data = await response.json();
            
            // Remover "Escribiendo..."
            const typingMsg = document.getElementById(typingId);
            if (typingMsg) typingMsg.remove();
            
            if (data.error) {
                appendMessage('bot-message', data.error);
            } else {
                appendMessage('bot-message', data.response);
            }
        } catch (error) {
            console.error("Error:", error);
            const typingMsg = document.getElementById(typingId);
            if (typingMsg) typingMsg.remove();
            appendMessage('bot-message', 'Lo siento, hubo un error de conexión.');
        }
    }

    function appendMessage(className, text, id = null) {
        const container = document.getElementById('chatbot-messages');
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message ' + className;
        if (id) msgDiv.id = id;
        
        // Convertir saltos de línea en <br>
        msgDiv.innerHTML = text.replace(/\n/g, '<br>');
        
        container.appendChild(msgDiv);
        container.scrollTop = container.scrollHeight;
    }
</script>
</body>
</html>