<!-- Floating Chatbot Widget -->
<div id="chatbot-widget">
    <!-- Chat Button (Floating) -->
    <button 
        id="chatbot-toggle" 
        class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-2xl flex items-center justify-center text-white text-2xl hover:scale-110 transition-transform z-50 animate-bounce"
        onclick="toggleChatbot()"
    >
        <span id="chatbot-icon">💬</span>
    </button>

    <!-- Chat Window (Hidden by default) -->
    <div 
        id="chatbot-window" 
        class="fixed bottom-24 right-6 w-96 h-[600px] bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 flex flex-col z-50 hidden"
        style="max-height: calc(100vh - 120px);"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4 rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-2xl">
                    🤖
                </div>
                <div>
                    <div class="font-bold text-white">BitMews AI</div>
                    <div class="text-xs text-orange-100 flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                        Online
                    </div>
                </div>
            </div>
            <button onclick="toggleChatbot()" class="text-white hover:text-gray-200 text-2xl">
                ×
            </button>
        </div>

        <!-- Welcome Message (shown when empty) -->
        <div id="chatbot-welcome" class="flex-1 flex flex-col items-center justify-center p-6 text-center">
            <div class="text-5xl mb-4">👋</div>
            <h3 class="text-xl font-bold text-white mb-2">Hi! I'm BitMews AI</h3>
            <p class="text-gray-400 text-sm mb-6">Ask me anything about crypto, prices, or market trends!</p>
            
            <!-- Quick Suggestions -->
            <div class="space-y-2 w-full">
                <button onclick="sendQuickMessage('What are the top cryptocurrencies?')" class="w-full bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-3 rounded-lg text-left transition">
                    💰 Top cryptocurrencies
                </button>
                <button onclick="sendQuickMessage('What is the price of Bitcoin?')" class="w-full bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-3 rounded-lg text-left transition">
                    📊 Bitcoin price
                </button>
                <button onclick="sendQuickMessage('Explain blockchain to me')" class="w-full bg-gray-800 hover:bg-gray-700 text-white text-sm px-4 py-3 rounded-lg text-left transition">
                    🎓 Learn about blockchain
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3 hidden">
            <!-- Messages will be added here -->
        </div>

        <!-- Typing Indicator -->
        <div id="chatbot-typing" class="px-4 py-2 hidden">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                    🤖
                </div>
                <div class="bg-gray-700 rounded-2xl px-4 py-2">
                    <div class="flex space-x-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-gray-700">
            <form onsubmit="sendChatMessage(event)" class="flex items-center space-x-2">
                <input 
                    type="text" 
                    id="chatbot-input" 
                    placeholder="Ask me anything..." 
                    class="flex-1 bg-gray-800 text-white text-sm px-4 py-3 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500"
                    autocomplete="off"
                >
                <button 
                    type="submit" 
                    class="bg-orange-500 hover:bg-orange-600 text-white w-10 h-10 rounded-full flex items-center justify-center transition"
                    id="chatbot-send"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
            <div class="text-xs text-gray-500 text-center mt-2">
                Powered by AI • Not financial advice
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #chatbot-window {
        animation: slideUp 0.3s ease-out;
    }
    
    .chat-message-enter {
        animation: slideUp 0.3s ease-out;
    }
    
    /* Custom scrollbar */
    #chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }
    
    #chatbot-messages::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 3px;
    }
    
    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>

<script>
    let chatbotSessionId = null;
    let chatbotOpen = false;
    const CHATBOT_API = '/api/v1/chatbot';

    // Initialize chatbot
    async function initChatbot() {
        try {
            const response = await fetch(`${CHATBOT_API}/conversation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            
            const data = await response.json();
            if (data.success) {
                chatbotSessionId = data.data.session_id;
            }
        } catch (error) {
            console.error('Error initializing chatbot:', error);
        }
    }

    // Toggle chatbot window
    function toggleChatbot() {
        const window = document.getElementById('chatbot-window');
        const icon = document.getElementById('chatbot-icon');
        const toggle = document.getElementById('chatbot-toggle');
        
        chatbotOpen = !chatbotOpen;
        
        if (chatbotOpen) {
            window.classList.remove('hidden');
            icon.textContent = '✕';
            toggle.classList.remove('animate-bounce');
            
            // Initialize if not already
            if (!chatbotSessionId) {
                initChatbot();
            }
            
            // Focus input
            setTimeout(() => {
                document.getElementById('chatbot-input').focus();
            }, 300);
        } else {
            window.classList.add('hidden');
            icon.textContent = '💬';
            toggle.classList.add('animate-bounce');
        }
    }

    // Send message
    async function sendChatMessage(event) {
        event.preventDefault();
        
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message || !chatbotSessionId) return;
        
        // Clear input
        input.value = '';
        
        // Hide welcome, show messages
        document.getElementById('chatbot-welcome').classList.add('hidden');
        document.getElementById('chatbot-messages').classList.remove('hidden');
        
        // Add user message
        addChatMessage('user', message);
        
        // Show typing
        showChatbotTyping();
        
        // Disable input
        input.disabled = true;
        document.getElementById('chatbot-send').disabled = true;
        
        try {
            const response = await fetch(`${CHATBOT_API}/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: chatbotSessionId,
                    message: message,
                }),
            });
            
            const data = await response.json();
            
            if (data.success) {
                addChatMessage('assistant', data.data.message.content);
            } else {
                addChatMessage('assistant', 'Sorry, I encountered an error. Please try again.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            addChatMessage('assistant', 'Sorry, I encountered an error. Please try again.');
        } finally {
            hideChatbotTyping();
            input.disabled = false;
            document.getElementById('chatbot-send').disabled = false;
            input.focus();
        }
    }

    // Send quick message
    function sendQuickMessage(message) {
        const input = document.getElementById('chatbot-input');
        input.value = message;
        document.querySelector('#chatbot-window form').dispatchEvent(new Event('submit'));
    }

    // Add message to chat
    function addChatMessage(role, content) {
        const container = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message-enter flex ' + (role === 'user' ? 'justify-end' : 'justify-start');
        
        const isUser = role === 'user';
        
        messageDiv.innerHTML = `
            <div class="flex items-start space-x-2 max-w-[85%] ${isUser ? 'flex-row-reverse space-x-reverse' : ''}">
                <div class="w-8 h-8 ${isUser ? 'bg-orange-500' : 'bg-gray-700'} rounded-full flex items-center justify-center flex-shrink-0 text-sm">
                    ${isUser ? '👤' : '🤖'}
                </div>
                <div class="${isUser ? 'bg-orange-500' : 'bg-gray-700'} text-white rounded-2xl px-4 py-2 text-sm">
                    ${escapeHtml(content)}
                </div>
            </div>
        `;
        
        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;
    }

    // Show/hide typing
    function showChatbotTyping() {
        document.getElementById('chatbot-typing').classList.remove('hidden');
        const container = document.getElementById('chatbot-messages');
        container.scrollTop = container.scrollHeight;
    }

    function hideChatbotTyping() {
        document.getElementById('chatbot-typing').classList.add('hidden');
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Auto-open chatbot after 5 seconds (optional)
    setTimeout(() => {
        if (!chatbotOpen && !localStorage.getItem('chatbot_seen')) {
            // Show a subtle notification
            const toggle = document.getElementById('chatbot-toggle');
            toggle.classList.add('animate-bounce');
            localStorage.setItem('chatbot_seen', 'true');
        }
    }, 5000);
</script>
