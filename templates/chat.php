<?php
/**
 * Шаблон чата
 * Для пользователей и курьеров - чат со "Службой поддержки" (админом с телефоном 7771234567)
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Чат - Delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        warm: {
                            50: '#FFF9F5',
                            100: '#FFF3EB',
                            200: '#FFE4D1',
                            300: '#FFC9A8',
                            400: '#FFA573',
                            500: '#FF7A3D',
                            600: '#F05A1A',
                            700: '#CC4412',
                            800: '#A33510',
                            900: '#7A2A0E',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; }
        
        .glass { 
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .gradient-hero {
            background: linear-gradient(180deg, #FFF9F5 0%, #FFFFFF 100%);
        }
        
        .card-shadow {
            box-shadow: 0 4px 20px rgba(240, 90, 26, 0.08);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF7A3D 0%, #F05A1A 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 90, 26, 0.3);
        }
        
        .bottom-nav {
            padding-bottom: env(safe-area-inset-bottom, 16px);
        }
    </style>
</head>
<body class="gradient-hero min-h-screen pb-20 md:pb-0 <?php echo ($user['role'] ?? 'user') === 'courier' ? 'md:pl-64' : ''; ?>">
    <!-- Header -->
    <header class="glass sticky top-0 z-50 border-b border-warm-100 <?php echo ($user['role'] ?? 'user') === 'courier' ? 'md:hidden' : ''; ?>">
        <div class="container mx-auto px-4">
            <nav class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-800">Delivery</span>
                </a>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="/catalog" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Каталог</a>
                    <a href="/orders" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Заказы</a>
                    <a href="/chat" class="text-warm-600 font-medium">Чат</a>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="hidden md:block">
                        <?php if ($isLoggedIn): ?>
                            <div class="flex items-center space-x-3">
                                <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                                    <a href="/admin" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Панель администратора</a>
                                <?php endif; ?>
                                <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                                    <a href="/courier" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Курьер</a>
                                <?php endif; ?>
                                <a href="/profile" class="text-gray-600 hover:text-warm-600 font-medium transition-colors"><?php echo htmlspecialchars($user['name'] ?? 'Профиль'); ?></a>
                                <button onclick="logout()" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </div>
                        <?php else: ?>
                            <a href="/login" class="btn-primary text-white px-5 py-2.5 rounded-full font-medium">Войти</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Chat Content -->
    <main class="px-4 py-6">
        <div class="container mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Чат с поддержкой</h1>
            
            <!-- Chat Interface -->
            <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                <!-- Chat Header -->
                <div class="p-4 bg-gradient-to-r from-warm-500 to-warm-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-white font-semibold">Служба поддержки</h2>
                            <p class="text-warm-100 text-sm">Отвечаем в рабочее время</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="chat-messages" class="h-[400px] md:h-[500px] p-4 overflow-y-auto space-y-3 bg-warm-50/50"></div>

                <!-- Message Input -->
                <div class="border-t border-gray-100 p-4">
                    <form id="messageForm" class="flex space-x-3">
                        <input type="text" id="messageInput"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-warm-500 focus:border-transparent"
                               placeholder="Введите сообщение..."
                               maxlength="500">
                        <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mt-4 bg-warm-50 rounded-xl p-4">
                <h3 class="font-semibold text-warm-800 mb-2">📞 Контакты</h3>
                <ul class="text-sm text-warm-700 space-y-1">
                    <li>• Телефон: +7 (777) 123-45-67</li>
                    <li>• Email: support@delivery.kz</li>
                    <li>• Режим работы: Пн-Пт с 9:00 до 18:00</li>
                </ul>
            </div>
        </div>
    </main>

    <?php $isCourier = ($user['role'] ?? 'user') === 'courier'; ?>
    
    <!-- Desktop Sidebar for Courier -->
    <?php if ($isCourier): ?>
    <aside class="hidden md:flex flex-col w-64 min-h-screen bg-white border-r border-gray-100 fixed left-0 top-0">
        <div class="p-4 border-b border-gray-100">
            <a href="/" class="flex items-center space-x-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-800">Delivery</span>
            </a>
        </div>
        
        <nav class="flex-1 p-4 space-y-1">
            <a href="/courier" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-warm-50 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="font-medium">Заказы</span>
            </a>
            
            <a href="/chat" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-warm-500 to-warm-600 text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="font-medium">Чат</span>
            </a>
            
            <a href="/profile" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-warm-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">Профиль</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-100">
            <button onclick="logout()" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="font-medium">Выйти</span>
            </button>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-gray-100 bottom-nav z-40">
        <?php if ($isCourier): ?>
        <!-- Courier Navigation -->
        <div class="flex justify-around items-center h-16">
            <a href="/courier" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="text-xs mt-1">Заказы</span>
            </a>
            <a href="/chat" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Чат</span>
            </a>
            <a href="/profile" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Профиль</span>
            </a>
        </div>
        <?php else: ?>
        <!-- Regular User Navigation -->
        <div class="flex justify-around items-center h-16">
            <a href="/" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs mt-1">Главная</span>
            </a>
            <a href="/catalog" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs mt-1">Каталог</span>
            </a>
            <a href="/orders" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs mt-1">Заказы</span>
            </a>
            <a href="/cart" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-xs mt-1">Корзина</span>
                <span id="cart-badge-mobile" class="hidden absolute top-0 right-4 w-4 h-4 bg-warm-500 text-white text-[10px] rounded-full flex items-center justify-center font-medium">0</span>
            </a>
            <a href="/chat" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Чат</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <script>
        let messagesContainer;
        let messageInput;
        let lastMessageId = 0;
        const currentUserId = <?php echo $_SESSION['user']['id'] ?? 'null'; ?>;
        const userRole = '<?php echo $user['role'] ?? 'user'; ?>';

        document.addEventListener('DOMContentLoaded', function() {
            messagesContainer = document.getElementById('chat-messages');
            messageInput = document.getElementById('messageInput');
            
            updateCartCount();
            
            // Загружаем сообщения и помечаем их прочитанными
            loadMessages();
            
            // Поллинг новых сообщений
            setInterval(loadNewMessages, 3000);
            
            messageInput.focus();
        });

        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const count = cart.reduce((sum, item) => {
                if (item.is_weighted) return sum + 1;
                return sum + Math.round(item.quantity || 1);
            }, 0);
            
            const badge = document.getElementById('cart-badge-mobile');
            if (count > 0 && badge) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.classList.remove('hidden');
            } else if (badge) {
                badge.classList.add('hidden');
            }
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.reload());
        }

        async function loadMessages() {
            messagesContainer.innerHTML = '';
            try {
                const response = await fetch('/api/chat/messages');
                if (response.ok) {
                    const messages = await response.json();
                    displayMessages(messages);
                    if (messages.length > 0) {
                        lastMessageId = messages[messages.length - 1].id;
                        // Помечаем сообщения прочитанными
                        markMessagesAsRead();
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

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
                        // Помечаем новые сообщения прочитанными
                        markMessagesAsRead();
                    }
                }
            } catch (error) {
                console.error('Error loading new messages:', error);
            }
        }

        async function markMessagesAsRead() {
            try {
                await fetch('/api/chat/mark-read', { method: 'POST' });
            } catch (error) {
                console.error('Error marking messages as read:', error);
            }
        }

        function displayMessages(messages) {
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                const isAdmin = message.sender_role === 'admin';
                const isCurrentUser = message.sender_id === currentUserId;
                
                messageDiv.className = `flex ${isCurrentUser ? 'justify-end' : 'justify-start'}`;

                const messageContent = `
                    <div class="max-w-[80%] px-4 py-2 rounded-2xl ${
                        isCurrentUser
                            ? 'bg-warm-500 text-white rounded-br-md'
                            : 'bg-gray-700 text-white rounded-bl-md'
                    }">
                        <div class="text-xs opacity-75 mb-1">
                            ${isCurrentUser ? 'Вы' : 'Служба поддержки'}
                        </div>
                        <div class="break-words">${escapeHtml(message.message)}</div>
                        <div class="text-xs opacity-75 mt-1">${formatDate(message.created_at)}</div>
                    </div>
                `;

                messageDiv.innerHTML = messageContent;
                messagesContainer.appendChild(messageDiv);
            });

            scrollToBottom();
        }

        // Обработчик отправки сообщения
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
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });

        function escapeHtml(text) {
            const map = { '&': '&', '<': '<', '>': '>', '"': '"', "'": '&#039;' };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        }

        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
</body>
</html>