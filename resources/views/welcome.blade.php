<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - AI-Powered Crypto Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#F7931A',
                        dark: '#0F0F0F',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-white">
    <!-- Navigation -->
    <nav class="bg-gray-900 border-b border-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-primary">BitMews</a>
                    <div class="hidden md:flex ml-10 space-x-8">
                        <a href="/tokens" class="hover:text-primary transition">Tokens</a>
                        <a href="/community" class="hover:text-primary transition">Community</a>
                        <a href="/news" class="hover:text-primary transition">News</a>
                        <a href="/pricing" class="hover:text-primary transition">Pricing</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/login" class="hover:text-primary transition">Login</a>
                    <a href="/register" class="bg-primary text-dark px-6 py-2 rounded-lg font-semibold hover:bg-orange-600 transition">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 bg-gradient-to-b from-gray-900 to-dark">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                AI-Powered Crypto<br>
                <span class="text-primary">Intelligence Platform</span>
            </h1>
            <p class="text-xl text-gray-400 mb-8 max-w-3xl mx-auto">
                Real-time crypto news, AI analysis, multi-chain data, and community insights all in one platform. 
                Make smarter decisions with the power of AI.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="/register" class="bg-primary text-dark px-8 py-4 rounded-lg font-semibold text-lg hover:bg-orange-600 transition">
                    Start Free Trial
                </a>
                <a href="/tokens" class="bg-gray-800 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-700 transition">
                    Explore Tokens
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-primary mb-2">20+</div>
                    <div class="text-gray-400">Blockchains</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-primary mb-2">5</div>
                    <div class="text-gray-400">AI Providers</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-primary mb-2">13+</div>
                    <div class="text-gray-400">Payment Gateways</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-primary mb-2">24/7</div>
                    <div class="text-gray-400">Live Updates</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16">Powerful Features</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 hover:border-primary transition">
                    <div class="text-4xl mb-4">🤖</div>
                    <h3 class="text-2xl font-bold mb-4">AI-Powered Analysis</h3>
                    <p class="text-gray-400">
                        Multi-AI integration with OpenAI, Gemini, Claude for sentiment analysis, 
                        price predictions, and content generation.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 hover:border-primary transition">
                    <div class="text-4xl mb-4">⛓️</div>
                    <h3 class="text-2xl font-bold mb-4">Multi-Chain Support</h3>
                    <p class="text-gray-400">
                        Track tokens across 20+ blockchains including Ethereum, Solana, BSC, 
                        Polygon, and more with real-time data.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 hover:border-primary transition">
                    <div class="text-4xl mb-4">💰</div>
                    <h3 class="text-2xl font-bold mb-4">Write-to-Earn</h3>
                    <p class="text-gray-400">
                        Earn rewards for creating quality content. Get paid for your insights 
                        and analysis with our creator monetization program.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 hover:border-primary transition">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold mb-4">Real-Time Data</h3>
                    <p class="text-gray-400">
                        Live crypto prices, market data, and analytics powered by CoinGecko 
                        and blockchain explorers.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 hover:border-primary transition">
                    <div class="text-4xl mb-4">👥</div>
                    <h3 class="text-2xl font-bold mb-4">Community Driven</h3>
                    <p class="text-gray-400">
                        Join a vibrant community of crypto enthusiasts. Share insights, 
                        discuss trends, and learn together.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 hover:border-primary transition">
                    <div class="text-4xl mb-4">💬</div>
                    <h3 class="text-2xl font-bold mb-4">AI Chatbot Assistant</h3>
                    <p class="text-gray-400">
                        Get instant answers about crypto prices, market trends, and trading tips 
                        from our AI-powered chatbot. Available 24/7!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Tokens Section -->
    <section class="py-20 bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-4xl font-bold">Trending Tokens</h2>
                <a href="/tokens" class="text-primary hover:underline">View All →</a>
            </div>
            <div class="grid md:grid-cols-4 gap-6" id="trending-tokens">
                <!-- Tokens will be loaded via API -->
                <div class="bg-gray-800 p-6 rounded-xl animate-pulse">
                    <div class="h-12 w-12 bg-gray-700 rounded-full mb-4"></div>
                    <div class="h-4 bg-gray-700 rounded mb-2"></div>
                    <div class="h-6 bg-gray-700 rounded"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-4">Simple, Transparent Pricing</h2>
            <p class="text-gray-400 text-center mb-16">Choose the plan that's right for you</p>
            
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Free Plan -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800">
                    <h3 class="text-2xl font-bold mb-2">Free</h3>
                    <div class="text-4xl font-bold mb-6">$0<span class="text-lg text-gray-400">/mo</span></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> Basic news access</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> Limited token data</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> 100 API calls/day</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> AI Chatbot (10 msgs/day)</li>
                    </ul>
                    <a href="/register" class="block text-center bg-gray-800 px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                        Get Started
                    </a>
                </div>

                <!-- Basic Plan -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800">
                    <h3 class="text-2xl font-bold mb-2">Basic</h3>
                    <div class="text-4xl font-bold mb-6">$9.99<span class="text-lg text-gray-400">/mo</span></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> Full news access</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> Real-time token data</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> 1,000 API calls/day</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> AI Chatbot (100 msgs/day)</li>
                    </ul>
                    <a href="/register" class="block text-center bg-gray-800 px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                        Get Started
                    </a>
                </div>

                <!-- Pro Plan -->
                <div class="bg-primary text-dark p-8 rounded-xl border-2 border-primary transform scale-105">
                    <div class="text-sm font-bold mb-2">MOST POPULAR</div>
                    <h3 class="text-2xl font-bold mb-2">Pro</h3>
                    <div class="text-4xl font-bold mb-6">$29.99<span class="text-lg opacity-70">/mo</span></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="mr-2">✓</span> Everything in Basic</li>
                        <li class="flex items-center"><span class="mr-2">✓</span> 10,000 API calls/day</li>
                        <li class="flex items-center"><span class="mr-2">✓</span> Advanced analytics</li>
                        <li class="flex items-center"><span class="mr-2">✓</span> AI Chatbot (1000 msgs/day)</li>
                    </ul>
                    <a href="/register" class="block text-center bg-dark text-white px-6 py-3 rounded-lg hover:bg-gray-900 transition">
                        Get Started
                    </a>
                </div>

                <!-- Expert Plan -->
                <div class="bg-gray-900 p-8 rounded-xl border border-gray-800">
                    <h3 class="text-2xl font-bold mb-2">Expert</h3>
                    <div class="text-4xl font-bold mb-6">$99.99<span class="text-lg text-gray-400">/mo</span></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> Everything in Pro</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> Unlimited API calls</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> White-label API</li>
                        <li class="flex items-center"><span class="text-primary mr-2">✓</span> AI Chatbot (Unlimited)</li>
                    </ul>
                    <a href="/register" class="block text-center bg-gray-800 px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary to-orange-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-dark mb-6">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-dark opacity-90 mb-8">
                Join thousands of crypto enthusiasts using BitMews
            </p>
            <a href="/register" class="inline-block bg-dark text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-900 transition">
                Start Free Trial
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold text-primary mb-4">BitMews</div>
                    <p class="text-gray-400">AI-Powered Crypto Intelligence Platform</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/tokens" class="hover:text-primary">Tokens</a></li>
                        <li><a href="/community" class="hover:text-primary">Community</a></li>
                        <li><a href="/news" class="hover:text-primary">News</a></li>
                        <li><a href="/pricing" class="hover:text-primary">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/about" class="hover:text-primary">About</a></li>
                        <li><a href="/blog" class="hover:text-primary">Blog</a></li>
                        <li><a href="/careers" class="hover:text-primary">Careers</a></li>
                        <li><a href="/contact" class="hover:text-primary">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/privacy" class="hover:text-primary">Privacy</a></li>
                        <li><a href="/terms" class="hover:text-primary">Terms</a></li>
                        <li><a href="/api-docs" class="hover:text-primary">API Docs</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 BitMews. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Include Floating Chatbot Widget -->
    @include('components.chatbot-widget')

    <script>
        // Load trending tokens
        fetch('/api/v1/tokens/trending')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('trending-tokens');
                if (data.success && data.data.length > 0) {
                    container.innerHTML = data.data.slice(0, 4).map(token => `
                        <a href="/tokens/${token.id}" class="bg-gray-800 p-6 rounded-xl hover:border hover:border-primary transition">
                            <img src="${token.logo_url || '/images/default-token.png'}" class="h-12 w-12 rounded-full mb-4" alt="${token.name}">
                            <div class="font-bold mb-1">${token.name}</div>
                            <div class="text-gray-400 text-sm mb-2">${token.symbol}</div>
                            <div class="text-2xl font-bold">$${parseFloat(token.current_price).toFixed(2)}</div>
                            <div class="text-sm ${token.price_change_24h >= 0 ? 'text-green-500' : 'text-red-500'}">
                                ${token.price_change_24h >= 0 ? '↑' : '↓'} ${Math.abs(token.price_change_24h).toFixed(2)}%
                            </div>
                        </a>
                    `).join('');
                }
            })
            .catch(err => console.error('Error loading tokens:', err));
    </script>
</body>
</html>
