<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chat-message {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .typing-indicator span {
            animation: blink 1.4s infinite;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes blink {
            0%, 60%, 100% { opacity: 0.3; }
            30% { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="flex h-screen">
        <!-- Sidebar (Conversation History) -->
        <div class="w-80 bg-gray-800 border-r border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-bold text-orange-500">💬 AI Assistant</h2>
                <p class="text-sm text-gray-400">Powered by Multi-AI</p>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4">
                <button onclick="newConversation()" class="w-full bg-orange-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-orange-600 transition mb-4">
                    + New Chat
                </button>
                
                <div id="conversation-list" class="space-y-2">
                    <!-- Conversations will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <div class="bg-gray-800 border-b border-gray-700 p-4 flex items-center justify-between">
                <div>
                    <h3 class="font-bold">BitMews AI Assistant</h3>
                    <p class="text-sm text-gray-400">Ask me anything about crypto!</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-sm text-gray-400">Online</span>
                </div>
            </div>

            <!-- Suggested Prompts (shown when empty) -->
            <div id="suggested-prompts" class="flex-1 flex items-center justify-center p-8">
                <div class="max-w-4xl w-full">
                    <div class="text-center mb-8">
                        <div class="text-6xl mb-4">🤖</div>
                        <h2 class="text-3xl font-bold mb-2">How can I help you today?</h2>
                        <p class="text-gray-400">Ask me about crypto prices, market analysis, or trading tips</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <button onclick="sendSuggestedPrompt('What are the current prices of top cryptocurrencies?')" class="bg-gray-800 p-6 rounded-xl text-left hover:bg-gray-700 transition">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="font-bold mb-1">Token Prices</div>
                            <div class="text-sm text-gray-400">Get current crypto prices</div>
                        </button>
                        
                        <button onclick="sendSuggestedPrompt('Give me a market analysis of Bitcoin')" class="bg-gray-800 p-6 rounded-xl text-left hover:bg-gray-700 transition">
                            <div class="text-3xl mb-2">📊</div>
                            <div class="font-bold mb-1">Market Analysis</div>
                            <div class="text-sm text-gray-400">Analyze market trends</div>
                        </button>
                        
                        <button onclick="sendSuggestedPrompt('What are the latest crypto news?')" class="bg-gray-800 p-6 rounded-xl text-left hover:bg-gray-700 transition">
                            <div class="text-3xl mb-2">📰</div>
                            <div class="font-bold mb-1">Latest News</div>
                            <div class="text-sm text-gray-400">Stay updated</div>
                        </button>
                        
                        <button onclick="sendSuggestedPrompt('Explain blockchain technology to me')" class="bg-gray-800 p-6 rounded-xl text-left hover:bg-gray-700 transition">
                            <div class="text-3xl mb-2">🎓</div>
                            <div class="font-bold mb-1">Learn Crypto</div>
                            <div class="text-sm text-gray-400">Understand the basics</div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 hidden">
                <!-- Messages will be added here -->
            </div>

            <!-- Typing Indicator -->
            <div id="typing-indicator" class="px-6 py-2 hidden">
                <div class="flex items-center space-x-2 text-gray-400">
                    <div class="typing-indicator flex space-x-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                    </div>
                    <span class="text-sm">AI is thinking...</span>
                </div>
            </div>

            <!-- Input Area -->
            <div class="bg-gray-800 border-t border-gray-700 p-4">
                <form onsubmit="sendMessage(event)" class="flex items-center space-x-4">
                    <input 
                        type="text" 
                        id="message-input" 
                        placeholder="Ask me anything about crypto..." 
                        class="flex-1 bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        autocomplete="off"
                    >
                    <button 
                        type="submit" 
                        class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        id="send-button"
                    >
                        Send
                    </button>
                </form>
                <div class="text-xs text-gray-500 mt-2 text-center">
                    Powered by OpenAI, Gemini, Claude & more • Not financial advice
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSessionId = null;
        const API_BASE = '/api/v1';

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            newConversation();
        });

        // Create new conversation
        async function newConversation() {
            try {
                const response = await fetch(`${API_BASE}/chatbot/conversation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                
                const data = await response.json();
                if (data.success) {
                    currentSessionId = data.data.session_id;
                    clearMessages();
                    showSuggestedPrompts();
                }
            } catch (error) {
                console.error('Error creating conversation:', error);
            }
        }

        // Send message
        async function sendMessage(event) {
            event.preventDefault();
            
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            
            if (!message || !currentSessionId) return;
            
            // Clear input
            input.value = '';
            
            // Hide suggested prompts
            hideSuggestedPrompts();
            
            // Add user message
            addMessage('user', message);
            
            // Show typing indicator
            showTyping();
            
            // Disable send button
            const sendButton = document.getElementById('send-button');
            sendButton.disabled = true;
            
            try {
                const response = await fetch(`${API_BASE}/chatbot/message`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: currentSessionId,
                        message: message,
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Add assistant message
                    addMessage('assistant', data.data.message.content, data.data.message.id);
                } else {
                    addMessage('assistant', 'Sorry, I encountered an error. Please try again.');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                addMessage('assistant', 'Sorry, I encountered an error. Please try again.');
            } finally {
                hideTyping();
                sendButton.disabled = false;
                input.focus();
            }
        }

        // Send suggested prompt
        function sendSuggestedPrompt(prompt) {
            const input = document.getElementById('message-input');
            input.value = prompt;
            document.querySelector('form').dispatchEvent(new Event('submit'));
        }

        // Add message to chat
        function addMessage(role, content, messageId = null) {
            const container = document.getElementById('messages-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;
            
            const isUser = role === 'user';
            const bgColor = isUser ? 'bg-orange-500' : 'bg-gray-700';
            const textAlign = isUser ? 'text-right' : 'text-left';
            
            messageDiv.innerHTML = `
                <div class="max-w-3xl">
                    <div class="flex items-start space-x-2 ${isUser ? 'flex-row-reverse space-x-reverse' : ''}">
                        <div class="w-8 h-8 rounded-full ${bgColor} flex items-center justify-center flex-shrink-0">
                            ${isUser ? '👤' : '🤖'}
                        </div>
                        <div class="${bgColor} rounded-2xl px-4 py-3 ${textAlign}">
                            <div class="text-sm whitespace-pre-wrap">${escapeHtml(content)}</div>
                        </div>
                    </div>
                    ${!isUser && messageId ? `
                        <div class="flex items-center space-x-2 mt-2 ml-10">
                            <button onclick="rateMessage(${messageId}, true)" class="text-xs text-gray-400 hover:text-green-500">
                                👍 Helpful
                            </button>
                            <button onclick="rateMessage(${messageId}, false)" class="text-xs text-gray-400 hover:text-red-500">
                                👎 Not helpful
                            </button>
                        </div>
                    ` : ''}
                </div>
            `;
            
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
        }

        // Rate message
        async function rateMessage(messageId, helpful) {
            try {
                await fetch(`${API_BASE}/chatbot/message/${messageId}/rate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ helpful }),
                });
            } catch (error) {
                console.error('Error rating message:', error);
            }
        }

        // Show/hide typing indicator
        function showTyping() {
            document.getElementById('typing-indicator').classList.remove('hidden');
        }

        function hideTyping() {
            document.getElementById('typing-indicator').classList.add('hidden');
        }

        // Show/hide suggested prompts
        function showSuggestedPrompts() {
            document.getElementById('suggested-prompts').classList.remove('hidden');
            document.getElementById('messages-container').classList.add('hidden');
        }

        function hideSuggestedPrompts() {
            document.getElementById('suggested-prompts').classList.add('hidden');
            document.getElementById('messages-container').classList.remove('hidden');
        }

        // Clear messages
        function clearMessages() {
            document.getElementById('messages-container').innerHTML = '';
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
