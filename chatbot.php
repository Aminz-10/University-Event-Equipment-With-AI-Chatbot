<?php
session_start();
include("db.php");

// Check if student is logged in
if (!isset($_SESSION['user_number']) || $_SESSION['role'] !== 'student') {
    echo "<script>alert('Access denied. Please login as student.'); window.location.href='login.php';</script>";
    exit();
}

$stud_num = $_SESSION['user_number'];

// Fetch student name
$query = "SELECT stud_name FROM user WHERE stud_num = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $stud_num);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_name = $student['stud_name'] ?? "Student";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniEquip AI - Equipment Assistant</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Navbar */
        .navbar-modern {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 1rem 2rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.3rem;
            color: #667eea;
            text-decoration: none;
        }

        .navbar-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }

        .user-role {
            color: #9ca3af;
            font-size: 12px;
        }

        .back-btn {
            background: #f3f4f6;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            color: #6b7280;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .back-btn:hover {
            background: #e5e7eb;
            color: #374151;
        }

        /* Main Container */
        .chatbot-container {
            flex: 1;
            display: flex;
            width: 100%;
            gap: 20px;
            padding: 20px;
            overflow: hidden;
        }

        /* Sidebar */
        .chatbot-sidebar {
            width: 280px;
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow-y: auto;
            flex-shrink: 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .chatbot-sidebar::-webkit-scrollbar {
            display: none;
        }
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-header-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .new-chat-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .new-chat-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .sidebar-section {
            margin-bottom: 24px;
        }

        .sidebar-title {
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .sidebar-item:hover {
            background: #f9fafb;
            color: #667eea;
        }

        .sidebar-item i {
            width: 24px;
            text-align: center;
            font-size: 16px;
        }

        /* Main Chat Area */
        .chatbot-main {
            flex: 1;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        /* Chat Messages */
        .chat-messages {
            flex: 1;
            padding: 20px 24px;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            gap: 16px;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .chat-messages::-webkit-scrollbar {
            display: none;
        }
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
        }

        .welcome-icon {
            font-size: 80px;
            margin-bottom: 16px;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .welcome-subtitle {
            color: #9ca3af;
            font-size: 16px;
            margin-bottom: 30px;
            max-width: 400px;
        }

        .welcome-features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            width: 100%;
            max-width: 600px;
        }

        .feature-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px 12px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .feature-badge:hover {
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        .feature-badge i {
            font-size: 24px;
            color: #667eea;
        }

        .feature-badge-text {
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
        }

        /* Messages */
        .chatbot-message {
            display: flex;
            gap: 12px;
            animation: messageSlide 0.3s ease-out;
            max-width: 100%;
        }

        .bot-message {
            justify-content: flex-start;
        }

        .user-message {
            justify-content: flex-end;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .bot-message .message-avatar {
            background: #f0f0f0;
            color: #667eea;
        }

        .user-message .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: none;
        }

        .message-content-wrapper {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-width: 70%;
        }

        .bot-message .message-content-wrapper {
            max-width: 75%;
        }

        .user-message .message-content-wrapper {
            max-width: 70%;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 12px;
            line-height: 1.5;
            font-size: 14px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            max-width: 100%;
        }

        .bot-message .message-content {
            background: #f9fafb;
            color: #374151;
        }

        .user-message .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .message-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0 4px;
            font-size: 12px;
        }

        .message-time {
            color: #9ca3af;
        }

        .message-delete-btn {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            transition: all 0.2s ease;
            opacity: 0;
        }

        .chatbot-message:hover .message-delete-btn {
            opacity: 1;
            background: rgba(0, 0, 0, 0.05);
            color: #ef4444;
        }

        .message-delete-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d1d5db;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                opacity: 0.3;
                transform: translateY(0);
            }
            30% {
                opacity: 1;
                transform: translateY(-8px);
            }
        }

        /* Input Area */
        .chat-input-area {
            padding: 20px 24px;
            border-top: 1px solid #f0f0f0;
            background: white;
            flex-shrink: 0;
        }

        .input-group-custom {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .chat-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            resize: none;
            max-height: 100px;
            transition: all 0.2s ease;
            outline: none;
        }

        .chat-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .send-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .send-btn:active {
            transform: translateY(0);
        }

        .send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Delete Confirmation Modal */
        .delete-confirmation-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .delete-confirmation-content {
            background: white;
            border-radius: 16px;
            padding: 32px 24px;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .delete-confirmation-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .delete-confirmation-header i {
            font-size: 24px;
            color: #ef4444;
        }

        .delete-confirmation-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .delete-confirmation-text {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 24px 0;
        }

        .delete-confirmation-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .delete-confirmation-btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cancel-btn {
            background: #f3f4f6;
            color: #374151;
        }

        .cancel-btn:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .confirm-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .confirm-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chatbot-container {
                flex-direction: column;
            }

            .chatbot-sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #e0e7ff;
                max-height: 280px;
                overflow-y: auto;
            }

            .sidebar-section h3 {
                font-size: 13px;
                margin-top: 10px;
                margin-bottom: 8px;
            }

            .sidebar-question, .sidebar-help-item {
                font-size: 13px;
                padding: 8px 10px;
            }

            .welcome-features {
                grid-template-columns: repeat(2, 1fr);
            }

            .chat-messages {
                padding: 16px 20px;
            }

            .chat-input-area {
                padding: 12px 16px;
            }

            .chat-input {
                font-size: 14px;
                padding: 10px 12px;
            }

            .chat-send-btn {
                padding: 10px 14px;
                font-size: 14px;
            }

            .message-content {
                font-size: 13px;
                padding: 10px 12px;
            }

            .navbar-content {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            .chatbot-main {
                height: calc(100vh - 120px);
            }

            .navbar-right {
                gap: 8px;
            }

            .user-name {
                font-size: 12px;
            }

            .user-role {
                font-size: 10px;
                display: none;
            }

            .chatbot-sidebar {
                padding: 12px;
                max-height: 250px;
            }

            .sidebar-section h3 {
                font-size: 12px;
                margin-top: 8px;
            }

            .sidebar-question, .sidebar-help-item {
                font-size: 12px;
                padding: 6px 8px;
            }

            .welcome-icon {
                font-size: 50px;
            }

            .welcome-title {
                font-size: 20px;
            }

            .welcome-subtitle {
                font-size: 13px;
            }

            .welcome-features {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .feature-badge {
                padding: 12px;
                font-size: 12px;
            }

            .chat-messages {
                padding: 12px 16px;
            }

            .message-content {
                font-size: 12px;
                padding: 8px 10px;
            }

            .chat-input-area {
                padding: 10px 12px;
            }

            .chat-input {
                font-size: 12px;
                padding: 8px 10px;
            }

            .chat-send-btn {
                padding: 8px 10px;
                font-size: 12px;
            }
        }


    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-modern">
        <div class="navbar-content">
            <a href="#" class="navbar-brand">
                <div class="navbar-brand-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <div style="font-size: 14px; color: #1f2937;">UniEquip AI</div>
                    <div style="font-size: 11px; color: #9ca3af; font-weight: 400;">Equipment Assistant</div>
                </div>
            </a>
            <div class="navbar-right">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($student_name); ?></div>
                    <div class="user-role">Student</div>
                </div>
                <a href="user_dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="chatbot-container">
        <!-- Sidebar -->
        <div class="chatbot-sidebar">

            <button class="new-chat-btn" onclick="location.reload()">
                <i class="fas fa-plus"></i>
                New Chat
            </button>

            <div class="sidebar-section">
                <div class="sidebar-title">Quick Questions</div>
                <button class="sidebar-item" onclick="sendQuickQuestion('Is projector EPSON EB-X41 available?')">
                    <i class="fas fa-search"></i>
                    Projector availability
                </button>
                <button class="sidebar-item" onclick="sendQuickQuestion('Show me audio equipment')">
                    <i class="fas fa-list"></i>
                    Audio Equipment
                </button>
                <button class="sidebar-item" onclick="sendQuickQuestion('What furniture do you have?')">
                    <i class="fas fa-chair"></i>
                    Furniture Equipment
                </button>
                <button class="sidebar-item" onclick="sendQuickQuestion('Show tripod availability')">
                    <i class="fas fa-video"></i>
                    Tripod availability
                </button>
                <button class="sidebar-item" onclick="sendQuickQuestion('Check projector')">
                    <i class="fas fa-check"></i>
                    Projector check
                </button>
                <button class="sidebar-item" onclick="sendQuickQuestion('Show all equipment')">
                    <i class="fas fa-layer-group"></i>
                    All equipment
                </button>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">Help</div>
                <button class="sidebar-item" onclick="sendQuickQuestion('How do I book equipment?')">
                    <i class="fas fa-book"></i>
                    Booking guide
                </button>
                <button class="sidebar-item" onclick="sendQuickQuestion('What can you help me with?')">
                    <i class="fas fa-lightbulb"></i>
                    What can you help?
                </button>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chatbot-main">
            <!-- Messages Container -->
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-state" id="welcomeState">
                    <div class="welcome-icon">🤖</div>
                    <h1 class="welcome-title">How can I help?</h1>
                    <p class="welcome-subtitle">
                        Ask me about equipment availability, bookings, or anything else you need help with.
                    </p>
                    <div class="welcome-features">
                        <div class="feature-badge">
                            <i class="fas fa-boxes"></i>
                            <span class="feature-badge-text">Equipment Info</span>
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-chart-bar"></i>
                            <span class="feature-badge-text">Availability</span>
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="feature-badge-text">Bookings</span>
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-filter"></i>
                            <span class="feature-badge-text">Categories</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="chat-input-area">
                <form id="chatForm" onsubmit="sendMessage(event)">
                    <div class="input-group-custom">
                        <input 
                            type="text" 
                            id="chatInput" 
                            class="chat-input" 
                            placeholder="Ask me about equipment..."
                            autocomplete="off"
                        >
                        <button type="submit" class="send-btn" id="sendBtn" title="Send message">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatMessages = document.getElementById('chatMessages');
        const welcomeState = document.getElementById('welcomeState');
        const CHAT_HISTORY_KEY = 'uniequip_chat_history';

        // Load chat history
        function loadChatHistory() {
            const savedHistory = localStorage.getItem(CHAT_HISTORY_KEY);
            if (savedHistory) {
                try {
                    const messages = JSON.parse(savedHistory);
                    if (welcomeState) welcomeState.remove();
                    messages.forEach(msg => {
                        addMessageWithoutSave(msg.text, msg.sender, msg.type, msg.time, msg.timestamp);
                    });
                } catch (e) {
                    console.error('Error loading chat history:', e);
                }
            }
        }

        // Save chat history
        function saveChatHistory() {
            const messages = [];
            chatMessages.querySelectorAll('.chatbot-message').forEach(el => {
                const isUserMessage = el.classList.contains('user-message');
                const content = el.querySelector('p')?.textContent || '';
                const time = el.querySelector('.message-time')?.textContent || '';
                const timestamp = el.dataset.timestamp || new Date().toISOString();
                if (content) {
                    messages.push({
                        text: content,
                        sender: isUserMessage ? 'user' : 'bot',
                        type: 'success',
                        time: time,
                        timestamp: timestamp
                    });
                }
            });
            localStorage.setItem(CHAT_HISTORY_KEY, JSON.stringify(messages));
        }

        // Add message
        function addMessage(text, sender, type = 'success') {
            if (welcomeState) welcomeState.remove();

            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${sender === 'user' ? 'user-message' : 'bot-message'}`;

            const now = new Date();
            const timestamp = now.toISOString();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });

            messageDiv.dataset.timestamp = timestamp;
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    ${sender === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>'}
                </div>
                <div class="message-content-wrapper">
                    <div class="message-content">
                        <p>${escapeHtml(text)}</p>
                    </div>
                    <div class="message-footer">
                        <div class="message-time">${timeString}</div>
                        <button class="message-delete-btn" title="Delete message" onclick="deleteMessage(this)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `;

            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            saveChatHistory();
        }

        // Add message without saving
        function addMessageWithoutSave(text, sender, type = 'success', savedTime = null, savedTimestamp = null) {
            if (welcomeState) welcomeState.remove();

            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${sender === 'user' ? 'user-message' : 'bot-message'}`;

            const timeString = savedTime || new Date().toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });

            messageDiv.dataset.timestamp = savedTimestamp || new Date().toISOString();
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    ${sender === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>'}
                </div>
                <div class="message-content-wrapper">
                    <div class="message-content">
                        <p>${escapeHtml(text)}</p>
                    </div>
                    <div class="message-footer">
                        <div class="message-time">${timeString}</div>
                        <button class="message-delete-btn" title="Delete message" onclick="deleteMessage(this)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `;

            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Send message
        async function sendMessage(event) {
            event.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            chatInput.value = '';
            chatInput.style.height = 'auto';
            sendBtn.disabled = true;

            showTypingIndicator();

            try {
                const response = await fetch('ai_openrouter_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'message=' + encodeURIComponent(message)
                });

                const data = await response.json();
                removeTypingIndicator();

                if (data.success) {
                    addMessage(data.message, 'bot', data.type);
                } else {
                    addMessage(data.message || 'An error occurred', 'bot', 'error');
                }
            } catch (error) {
                removeTypingIndicator();
                addMessage('Connection error. Please try again.', 'bot', 'error');
                console.error('Error:', error);
            }

            sendBtn.disabled = false;
            chatInput.focus();
        }

        // Typing indicator
        function showTypingIndicator() {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chatbot-message bot-message';
            messageDiv.id = 'typingIndicator';
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) indicator.remove();
        }

        // Delete message
        function deleteMessage(deleteBtn) {
            const messageDiv = deleteBtn.closest('.chatbot-message');
            if (messageDiv) {
                showDeleteConfirmation(messageDiv);
            }
        }

        // Delete confirmation
        function showDeleteConfirmation(messageDiv) {
            const modal = document.createElement('div');
            modal.className = 'delete-confirmation-modal';
            modal.innerHTML = `
                <div class="delete-confirmation-content">
                    <div class="delete-confirmation-header">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Delete Message?</h3>
                    </div>
                    <p class="delete-confirmation-text">Are you sure you want to delete this message? This action cannot be undone.</p>
                    <div class="delete-confirmation-buttons">
                        <button class="delete-confirmation-btn cancel-btn" onclick="this.closest('.delete-confirmation-modal').remove()">
                            Cancel
                        </button>
                        <button class="delete-confirmation-btn confirm-btn">
                            Delete
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            modal.querySelector('.confirm-btn').addEventListener('click', () => {
                messageDiv.style.animation = 'messageFade 0.3s ease-out';
                setTimeout(() => {
                    messageDiv.remove();
                    saveChatHistory();
                    modal.remove();
                }, 300);
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
        }

        // Send quick question
        function sendQuickQuestion(question) {
            chatInput.value = question;
            chatInput.focus();
            setTimeout(() => sendMessage(new Event('submit')), 100);
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto-resize input
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight > 100 ? 100 : this.scrollHeight) + 'px';
        });

        // Load history on load
        window.addEventListener('load', loadChatHistory);
    </script>
</body>
</html>
