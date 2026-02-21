<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kazyna Market - Лучшие продукты для вас</title>
    <?php echo $csrfMeta; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b',
                        accent: '#f59e0b'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-gentle': 'bounceGentle 2s infinite'
                    }
                }
            }
        }

        // Custom animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes bounceGentle {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            .animate-fade-in { animation: fadeIn 0.5s ease-in; }
            .animate-slide-up { animation: slideUp 0.6s ease-out; }
            .animate-bounce-gentle { animation: bounceGentle 2s infinite; }
        `;
        document.head.appendChild(style);
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-yellow-50 min-h-screen" data-is-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Kazyna Market
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium relative">
                        🛒 Корзина
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <a href="/orders" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">📦 Заказы</a>
                        <a href="/profile" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">👤 Профиль</a>
                        <a href="/chat" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">💬 Чат</a>
                        <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                            <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
                        <?php endif; ?>
                        <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                            <a href="/admin" class="text-purple-700 hover:text-purple-600 transition-colors duration-200 font-medium">⚙️ Админ</a>
                        <?php endif; ?>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</span>
                            <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                Выход
                            </button>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Войти
                        </a>
                        <a href="/register" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Регистрация
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl">☰</button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200">
                <div class="px-4 py-4 space-y-2">
                    <a href="/catalog" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        🛒 Корзина
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <a href="/orders" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">📦 Заказы</a>
                        <a href="/profile" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">👤 Профиль</a>
                        <a href="/chat" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">💬 Чат</a>
                        <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                            <a href="/courier" class="block px-4 py-3 text-orange-700 hover:bg-orange-50 rounded-lg transition-colors">🚚 Курьер</a>
                        <?php endif; ?>
                        <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                            <a href="/admin" class="block px-4 py-3 text-purple-700 hover:bg-purple-50 rounded-lg transition-colors">⚙️ Админ</a>
                        <?php endif; ?>
                        <hr class="my-2">
                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-600 mb-3">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</p>
                            <button onclick="logout()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                                Выход
                            </button>
                        </div>
                    <?php else: ?>
                        <hr class="my-2">
                        <a href="/login" class="block w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg transition-colors text-center font-medium">
                            Войти
                        </a>
                        <a href="/register" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg transition-colors text-center font-medium">
                            Регистрация
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="w-full bg-gradient-to-r from-blue-50 via-white to-yellow-50">
        <div class="w-full px-4 py-20">
            <div class="text-center max-w-4xl mx-auto animate-fade-in">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-yellow-600 bg-clip-text text-transparent">
                    Добро пожаловать в Kazyna Market
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 mb-8 leading-relaxed">
                    Откройте для себя мир свежих продуктов и качественных товаров.
                    Мы предлагаем только лучшее для вашего дома и семьи!
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="/catalog" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 animate-bounce-gentle">
                        🛒 Начать покупки
                    </a>
                    <a href="#features" class="border-2 border-gray-300 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300">
                        Узнать больше
                    </a>
                </div>
            </div>

            <!-- Features -->
            <div id="features" class="grid md:grid-cols-3 gap-8 mt-20 animate-slide-up max-w-6xl mx-auto">
                <div class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <span class="text-2xl">🌱</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Свежие продукты</h3>
                    <p class="text-gray-600 leading-relaxed">Только свежие и качественные продукты от проверенных поставщиков</p>
                </div>

                <div class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <span class="text-2xl">🚚</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Быстрая доставка</h3>
                    <p class="text-gray-600 leading-relaxed">Доставка в течение 1-2 часов по всему городу</p>
                </div>

                <div class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-400 to-orange-600 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <span class="text-2xl">💎</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Качество гарантировано</h3>
                    <p class="text-gray-600 leading-relaxed">Мы заботимся о качестве каждого продукта в нашем ассортименте</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-12 mt-20">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">K</span>
                        </div>
                        <span class="text-xl font-bold">Kazyna Market</span>
                    </div>
                    <p class="text-gray-400">Ваш надежный партнер в мире качественных продуктов</p>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Навигация</h4>
                    <ul class="space-y-2">
                        <li><a href="/catalog" class="text-gray-400 hover:text-white transition-colors">Каталог</a></li>
                        <li><a href="/cart" class="text-gray-400 hover:text-white transition-colors">Корзина</a></li>
                        <li><a href="/orders" class="text-gray-400 hover:text-white transition-colors">Заказы</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Контакты</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>📞 +7 (727) 123-45-67</li>
                        <li>📧 info@kazyna.kz</li>
                        <li>📍 Алматы, Казахстан</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Социальные сети</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors text-2xl">📘</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors text-2xl">📷</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors text-2xl">🐦</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p>&copy; <?php echo date('Y'); ?> Kazyna Market. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script>
        // CSRF token helper
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        // Fetch wrapper with CSRF
        async function fetchWithCsrf(url, options = {}) {
            const csrfToken = getCsrfToken();
            const headers = {
                'Content-Type': 'application/json',
                ...options.headers
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            return fetch(url, {
                ...options,
                headers
            });
        }

        // Update cart count
        async function updateCartCount() {
            const isLoggedIn = document.body.dataset.isLoggedIn === 'true';
            let count = 0;

            if (isLoggedIn) {
                try {
                    const response = await fetch('/api/cart');
                    if (response.ok) {
                        const cart = await response.json();
                        count = cart.reduce((sum, item) => sum + item.quantity, 0);
                    }
                } catch (error) {
                    const localCart = JSON.parse(localStorage.getItem('cart') || '[]');
                    count = localCart.reduce((sum, item) => sum + item.quantity, 0);
                }
            } else {
                const localCart = JSON.parse(localStorage.getItem('cart') || '[]');
                count = localCart.reduce((sum, item) => sum + item.quantity, 0);
            }

            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                if (count > 0) {
                    cartCount.textContent = count;
                    cartCount.classList.remove('hidden');
                } else {
                    cartCount.classList.add('hidden');
                }
            }
        }

        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Logout function with CSRF
        async function logout() {
            try {
                const response = await fetchWithCsrf('/api/auth/logout', { method: 'POST' });
                if (response.ok) {
                    window.location.href = '/';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            setInterval(updateCartCount, 5000);
        });
    </script>
</body>
</html>