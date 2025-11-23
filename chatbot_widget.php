<?php
/**
 * Floating Chatbot Widget - Reusable Component
 * Include this file in any page where you want the chatbot to appear
 * Usage: <?php include('chatbot_widget.php'); ?>
 */
?>

<!-- Floating Chatbot Widget -->
<div class="chatbot-widget-container" id="chatbotWidget">
    <!-- Chat Toggle Button -->
    <button class="chatbot-toggle-btn" id="chatbotToggle" title="Open AI Assistant">
        <i class="fas fa-comments"></i>
        <span class="unread-badge" id="unreadBadge" style="display: none;">1</span>
    </button>

    <!-- Chat Window -->
    <div class="chatbot-window" id="chatbotWindow" style="display: none;">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <h4 class="chatbot-title">AI Assistant</h4>
                <p class="chatbot-subtitle">UniEquip Support</p>
            </div>
            <button class="chatbot-close-btn" id="chatbotClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

    <!-- Messages Container -->
    <div class="chatbot-messages" id="chatbotMessages">
    </div>        <!-- Input Area -->
        <div class="chatbot-input-area">
            <form id="chatbotForm" onsubmit="sendChatbotMessage(event)">
                <div class="input-group">
                    <input 
                        type="text" 
                        id="chatbotInput" 
                        class="chatbot-input" 
                        placeholder="Type your question..."
                        autocomplete="off"
                    >
                    <button type="submit" class="chatbot-send-btn" title="Send message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Floating Chatbot Styles -->
<style>
    /* Chatbot Widget Container */
    .chatbot-widget-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    /* Toggle Button */
    .chatbot-toggle-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        cursor: pointer;
        font-size: 24px;
        box-shadow: 0 12px 32px rgba(102, 126, 234, 0.35);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .chatbot-toggle-btn:hover {
        transform: scale(1.12);
        box-shadow: 0 16px 48px rgba(102, 126, 234, 0.45);
    }

    .chatbot-toggle-btn:active {
        transform: scale(0.98);
    }

    /* Unread Badge */
    .unread-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        animation: badgePulse 2s ease-in-out infinite;
    }

    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6);
        }
    }

    /* Chat Window */
    .chatbot-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 360px;
        height: 500px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Header */
    .chatbot-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .chatbot-header-info {
        flex: 1;
    }

    .chatbot-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .chatbot-subtitle {
        margin: 4px 0 0 0;
        font-size: 12px;
        opacity: 0.9;
    }

    .chatbot-close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.2s ease;
    }

    .chatbot-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    /* Messages Container */
    .chatbot-messages {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f8f9fa;
    }

    .chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 3px;
    }

    .chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.2);
    }

    /* Messages */
    .chatbot-message {
        display: flex;
        flex-direction: column;
        animation: messageSlide 0.3s ease-out;
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

    @keyframes messageFade {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }

    .chatbot-message.bot-message {
        align-items: flex-start;
    }

    .chatbot-message.user-message {
        align-items: flex-end;
    }

    .message-content {
        max-width: 80%;
        padding: 12px 14px;
        border-radius: 12px;
        line-height: 1.4;
        font-size: 14px;
        word-wrap: break-word;
    }

    .bot-message .message-content {
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
        border-radius: 12px 12px 12px 4px;
    }

    .user-message .message-content {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 4px 12px;
    }

    .message-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
        padding: 0 4px;
    }

    .user-message .message-time {
        text-align: right;
    }

    /* Message Footer */
    .message-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 4px;
    }

    /* Message Delete Button */
    .message-delete-btn {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
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

    .message-delete-btn:active {
        transform: scale(0.95);
    }

    /* Input Area */
    .chatbot-input-area {
        padding: 12px;
        border-top: 1px solid #e5e7eb;
        background: white;
    }

    .input-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .chatbot-input {
        flex: 1;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s ease;
        outline: none;
    }

    .chatbot-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .chatbot-send-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .chatbot-send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }

    .chatbot-send-btn:active {
        transform: scale(0.95);
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 12px 14px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px 12px 12px 4px;
        width: fit-content;
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
            transform: translateY(-10px);
        }
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
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cancel-btn {
        background: #f3f4f6;
        color: #374151;
    }

    .cancel-btn:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }

    .cancel-btn:active {
        transform: translateY(0);
    }

    .confirm-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .confirm-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
    }

    .confirm-btn:active {
        transform: translateY(0);
    }

    /* Mobile Responsive */
    @media (max-width: 480px) {
        .chatbot-window {
            width: 100%;
            height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
            max-height: none;
        }

        .chatbot-widget-container {
            bottom: 0;
            right: 0;
        }

        .chatbot-toggle-btn {
            border-radius: 12px;
            bottom: 12px;
            right: 12px;
        }
    }
</style>

<!-- Floating Chatbot Widget JavaScript -->
<script>
    // Chatbot Widget Variables
    let chatbotOpen = false;
    let hasUnreadMessage = false;

    // Get Elements
    const chatbotWidget = document.getElementById('chatbotWidget');
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatbotForm = document.getElementById('chatbotForm');
    const chatbotInput = document.getElementById('chatbotInput');
    const chatbotMessages = document.getElementById('chatbotMessages');
    const unreadBadge = document.getElementById('unreadBadge');

    // Chat History Storage
    const CHAT_HISTORY_KEY = 'uniequip_chat_history';

    // Load chat history from localStorage on page load
    function loadChatHistory() {
        const savedHistory = localStorage.getItem(CHAT_HISTORY_KEY);
        if (savedHistory) {
            try {
                const messages = JSON.parse(savedHistory);
                // Load saved messages with their original timestamps
                messages.forEach(msg => {
                    addMessageWithoutSave(msg.text, msg.sender, msg.type, msg.time, msg.timestamp);
                });
            } catch (e) {
                console.error('Error loading chat history:', e);
                showWelcomeMessage();
            }
        } else {
            // Show welcome message only if no history exists
            showWelcomeMessage();
        }
    }

    // Show welcome message
    function showWelcomeMessage() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message bot-message';
        messageDiv.innerHTML = `
            <div class="message-content">
                <p>👋 Hello! I'm your AI Assistant. How can I help you today?</p>
                <p style="font-size: 0.85rem; margin-top: 8px; opacity: 0.8;">
                    Ask about equipment availability, bookings, or any other questions!
                </p>
            </div>
            <div class="message-time">just now</div>
        `;
        chatbotMessages.appendChild(messageDiv);
    }

    // Add message without saving (for loading history)
    function addMessageWithoutSave(text, sender, type = 'success', savedTime = null, savedTimestamp = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender === 'user' ? 'user-message' : 'bot-message'}`;

        const timeString = savedTime || new Date().toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });

        messageDiv.dataset.timestamp = savedTimestamp || new Date().toISOString();
        messageDiv.innerHTML = `
            <div class="message-content">
                <p>${escapeHtml(text)}</p>
            </div>
            <div class="message-footer">
                <div class="message-time">${timeString}</div>
                <button class="message-delete-btn" title="Delete message" onclick="deleteMessage(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Save chat history to localStorage
    function saveChatHistory() {
        const messages = [];
        chatbotMessages.querySelectorAll('.chatbot-message').forEach(el => {
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

    // Clear chat history
    function clearChatHistory() {
        localStorage.removeItem(CHAT_HISTORY_KEY);
        chatbotMessages.innerHTML = '';
    }

    // Toggle Chatbot Window
    chatbotToggle.addEventListener('click', function() {
        if (chatbotOpen) {
            closeChatbot();
        } else {
            openChatbot();
        }
    });

    // Close Button
    chatbotClose.addEventListener('click', closeChatbot);

    // Open Chatbot
    function openChatbot() {
        chatbotWindow.style.display = 'flex';
        chatbotOpen = true;
        chatbotInput.focus();
        unreadBadge.style.display = 'none';
        hasUnreadMessage = false;
    }

    // Close Chatbot
    function closeChatbot() {
        chatbotWindow.style.display = 'none';
        chatbotOpen = false;
    }

    // Send Message
    async function sendChatbotMessage(event) {
        event.preventDefault();
        
        const message = chatbotInput.value.trim();
        if (!message) return;

        // Add user message to chat
        addMessage(message, 'user');
        chatbotInput.value = '';

        // Show typing indicator
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

        // Auto-scroll to latest message
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Add Message to Chat
    function addMessage(text, sender, type = 'success') {
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
            <div class="message-content">
                <p>${escapeHtml(text)}</p>
            </div>
            <div class="message-footer">
                <div class="message-time">${timeString}</div>
                <button class="message-delete-btn" title="Delete message" onclick="deleteMessage(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        // Save to localStorage
        saveChatHistory();

        // Show unread badge if chatbot is closed
        if (!chatbotOpen && sender === 'bot') {
            hasUnreadMessage = true;
            unreadBadge.style.display = 'flex';
        }
    }

    // Show Typing Indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot-message';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        chatbotMessages.appendChild(typingDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Remove Typing Indicator
    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Delete a message
    function deleteMessage(deleteBtn) {
        const messageDiv = deleteBtn.closest('.chatbot-message');
        if (messageDiv) {
            showDeleteConfirmation(messageDiv);
        }
    }

    // Show delete confirmation modal
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

        // Handle confirm button
        modal.querySelector('.confirm-btn').addEventListener('click', () => {
            messageDiv.style.animation = 'messageFade 0.3s ease-out';
            setTimeout(() => {
                messageDiv.remove();
                saveChatHistory();
                modal.remove();
            }, 300);
        });

        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && chatbotOpen) {
            closeChatbot();
        }
    });

    // Close if clicking outside
    document.addEventListener('click', function(event) {
        if (!chatbotWidget.contains(event.target) && chatbotOpen) {
            // Optional: uncomment to close on outside click
            // closeChatbot();
        }
    });

    // Enter key to send message
    chatbotInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            chatbotForm.dispatchEvent(new Event('submit'));
        }
    });

    // Load chat history on page load
    window.addEventListener('load', function() {
        loadChatHistory();
    });
</script>
