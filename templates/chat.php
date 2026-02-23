<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат с поддержкой - Delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-in; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-pink-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <a href="/" class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Delivery
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛍️ Каталог</a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium relative">
                        🛒 Корзина
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </a>
                    <a href="/orders" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">📦 Заказы</a>
                    <a href="/profile" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">👤 Профиль</a>
                    <a href="/chat" class="text-purple-600 font-semibold border-b-2 border-purple-600 pb-1">💬 Чат</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                        <a href="/admin" class="text-purple-700 hover:text-purple-600 transition-colors duration-200 font-medium">⚙️ Админ</a>
                    <?php endif; ?>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</span>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Выход</button>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl p-2">☰</button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200 mt-4 rounded-xl">
                <div class="px-4 py-4 space-y-2">
                    <a href="/catalog" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">🛍️ Каталог</a>
                    <a href="/cart" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">🛒 Корзина</a>
                    <a href="/orders" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">📦 Заказы</a>
                    <a href="/profile" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">👤 Профиль</a>
                    <a href="/chat" class="block px-4 py-3 text-purple-600 font-semibold bg-purple-50 rounded-lg">💬 Чат</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="block px-4 py-3 text-orange-700 hover:bg-orange-50 rounded-lg transition-colors">🚚 Курьер</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                        <a href="/admin" class="block px-4 py-3 text-purple-700 hover:bg-purple-50 rounded-lg transition-colors">⚙️ Админ</a>
                    <?php endif; ?>
                    <hr class="my-2">
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-600 mb-3">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</p>
                        <button onclick="logout()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Выход</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 md:py-8 animate-fade-in">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-4xl font-bold text-gray-800 mb-2 md:mb-4 flex items-center">
                    <span class="mr-2 md:mr-4">💬</span> Чат с поддержкой
                </h1>
                <p class="text-sm md:text-base text-gray-600">Задайте свои вопросы и получите помощь от администратора</p>
            </div>

            <!-- Chat Interface -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-lg shadow-lg h-[500px] md:h-[600px] flex flex-col">
                <!-- Chat Header -->
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-purple-500 to-pink-500 rounded-t-2xl md:rounded-t-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-xl">A</span>
                        </div>
                        <div>
                            <h2 class="text-white font-semibold text-sm md:text-base">Служба поддержки</h2>
                            <p class="text-purple-200 text-xs md:text-sm">Отвечаем в рабочее время</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="chat-messages" class="flex-1 p-3 md:p-4 overflow-y-auto space-y-3 md:space-y-4 bg-gray-50"></div>

                <!-- Message Input -->
                <div class="border-t border-gray-200 p-3 md:p-4 bg-white rounded-b-2xl md:rounded-b-lg">
                    <form id="messageForm" class="flex space-x-2 md:space-x-3">
                        <input type="text" id="messageInput"
                               class="flex-1 px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm md:text-base"
                               placeholder="Введите сообщение..."
                               maxlength="500">
                        <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg md:rounded-xl transition-colors text-sm md:text-base whitespace-nowrap">
                            Отпр.
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 mt-2 hidden md:block">Максимальная длина сообщения: 500 символов</p>
                </div>
            </div>

            <!-- Chat Info -->
            <div class="mt-4 md:mt-6 bg-purple-50 rounded-xl md:rounded-lg p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-purple-800 mb-2">📞 Контакты для связи</h3>
                <ul class="text-xs md:text-sm text-purple-700 space-y-1">
                    <li>• Телефон: +7 (727) 123-45-67</li>
                    <li>• Email: support@kazyna.kz</li>
                    <li>• Режим работы: Пн-Пт с 9:00 до 18:00</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6 md:py-8 mt-8 md:mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm md:text-base">&copy; <?php echo date('Y'); ?> Delivery. Все права защищены.</p>
        </div>
    </footer>

    <script>
        let messagesContainer;
        let messageInput;
        let lastMessageId = 0;
        const currentUserId = <?php echo $_SESSION['user']['id'] ?? 'null'; ?>;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            messagesContainer = document.getElementById('chat-messages');
            messageInput = document.getElementById('messageInput');

            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                    }
                });
            }

            // Update cart count
            updateCartCount();

            // Load initial messages
            loadMessages();

            // Set up periodic message checking
            setInterval(loadNewMessages, 3000);

            // Focus on input
            messageInput.focus();
        });

        // Update cart count
        async function updateCartCount() {
            let count = 0;
            try {
                const response = await fetch('/api/cart');
                if (response.ok) {
                    const cart = await response.json();
                    count = cart.reduce((sum, item) => sum + item.quantity, 0);
                }
            } catch (error) {
                console.error('Error fetching cart:', error);
            }

            const cartCount = document.getElementById('cart-count');
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        }

        // Logout function
        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.href = '/');
        }

        // Load messages
        async function loadMessages() {
            messagesContainer.innerHTML = '';
            try {
                const response = await fetch('/api/chat/messages');
                if (response.ok) {
                    const messages = await response.json();
                    displayMessages(messages);
                    if (messages.length > 0) {
                        lastMessageId = messages[messages.length - 1].id;
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        // Load new messages
        async function loadNewMessages() {
            try {
                const response = await fetch('/api/chat/messages');
                if (response.ok) {
                    const messages = await response.json();
                    const newMessages = messages.filter(msg => msg.id > lastMessageId);
                    if (newMessages.length > 0) {
                        displayMessages(newMessages);
                        lastMessageId = newMessages[newMessages.length - 1].id;
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error loading new messages:', error);
            }
        }

        // Display messages
        function displayMessages(messages) {
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                const isAdmin = message.sender_role === 'admin';
                const isCurrentUser = message.sender_id === currentUserId;
                
                messageDiv.className = `flex ${(isCurrentUser || isAdmin) ? 'justify-end' : 'justify-start'}`;

                const messageContent = `
                    <div class="max-w-[80%] md:max-w-md px-3 md:px-4 py-2 rounded-xl ${
                        isCurrentUser
                            ? 'bg-blue-500 text-white'
                            : isAdmin 
                                ? 'bg-purple-500 text-white'
                                : 'bg-gray-200 text-gray-800'
                    }">
                        <div class="text-xs opacity-75 mb-1">
                            ${isCurrentUser ? 'Вы' : (isAdmin ? 'Администратор' : escapeHtml(message.sender_name))}
                        </div>
                        <div class="break-words text-sm md:text-base">${escapeHtml(message.message)}</div>
                        <div class="text-xs opacity-75 mt-1">${formatDate(message.created_at)}</div>
                    </div>
                `;

                messageDiv.innerHTML = messageContent;
                messagesContainer.appendChild(messageDiv);
            });

            scrollToBottom();
        }

        // Send message
        document.getElementById('messageForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const message = messageInput.value.trim();
            if (!message) return;

            try {
                const response = await fetch('/api/chat/messages', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });

                if (response.ok) {
                    messageInput.value = '';
                    loadMessages();
                } else {
                    alert('❌ Ошибка отправки сообщения');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('❌ Ошибка сети');
            }
        });

        // Utility functions
        function escapeHtml(text) {
            const map = {
                '&': '&',
                '<': '<',
                '>': '>',
                '"': '"',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('ru-RU', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
</body>
</html>