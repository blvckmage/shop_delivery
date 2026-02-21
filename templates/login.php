<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Вход - Kazyna Market</title>
    <?php echo $csrfMeta ?? ''; ?>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        'fade-in': 'fadeIn 0.6s ease-in',
                        'float': 'float 3s ease-in-out infinite'
                    }
                }
            }
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            .animate-fade-in { animation: fadeIn 0.6s ease-in; }
            .animate-float { animation: float 3s ease-in-out infinite; }
        `;
        document.head.appendChild(style);
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <a href="/" class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Kazyna Market
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium relative">
                        🛒 Корзина
                    </a>
                    <a href="/login" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
                        🔐 Войти
                    </a>
                    <a href="/register" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Регистрация
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl p-2">☰</button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200 mt-4 rounded-xl">
                <div class="px-4 py-4 space-y-2">
                    <a href="/catalog" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        🛒 Корзина
                    </a>
                    <hr class="my-2">
                    <a href="/login" class="block px-4 py-3 text-blue-600 font-semibold bg-blue-50 rounded-lg">
                        🔐 Войти
                    </a>
                    <a href="/register" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg transition-colors text-center font-medium">
                        Регистрация
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 md:py-16 animate-fade-in">
        <div class="max-w-md mx-auto">
            <!-- Welcome Illustration -->
            <div class="text-center mb-6 md:mb-8">
                <div class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-r from-blue-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 animate-float">
                    <span class="text-2xl md:text-3xl">🔐</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Добро пожаловать!</h1>
                <p class="text-sm md:text-base text-gray-600">Войдите в свой аккаунт для продолжения покупок</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-xl">
                <form id="loginForm" class="space-y-4 md:space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            📧 Email или телефон
                        </label>
                        <input type="text" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-base"
                               placeholder="example@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            🔒 Пароль
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-base"
                               placeholder="Ваш пароль">
                    </div>

                    <button type="submit" id="loginBtn"
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-3 md:py-4 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center">
                            <span id="btnText">🚀 Войти</span>
                            <span id="btnSpinner" class="hidden ml-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </span>
                    </button>
                </form>

                <div class="mt-4 md:mt-6 text-center">
                    <p class="text-sm md:text-base text-gray-600">
                        Нет аккаунта?
                        <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold transition-colors duration-200">
                            Зарегистрироваться
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6 md:py-8 mt-8 md:mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm md:text-base">&copy; <?php echo date('Y'); ?> Kazyna Market. Все права защищены.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('mobile-menu');
            const btn = document.getElementById('mobile-menu-btn');
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // CSRF helper
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        // Login form handler
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btnText.textContent = 'Вход...';
            btnSpinner.classList.remove('hidden');

            const formData = new FormData(e.target);
            const data = {
                email: formData.get('email'),
                password: formData.get('password')
            };

            try {
                const headers = { 'Content-Type': 'application/json' };
                const csrfToken = getCsrfToken();
                if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('🎉 Успешный вход!', 'success');
                    setTimeout(() => {
                        location.href = result.redirect || '/';
                    }, 1500);
                } else {
                    showNotification('❌ ' + (result.error || 'Ошибка входа'), 'error');
                }
            } catch (error) {
                showNotification('❌ Ошибка сети', 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = '🚀 Войти';
                btnSpinner.classList.add('hidden');
            }
        });

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 left-4 right-4 md:left-auto md:right-4 px-4 md:px-6 py-3 rounded-xl shadow-lg z-50 transform transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `<span class="font-medium text-sm md:text-base">${message}</span>`;

            document.body.appendChild(notification);

            setTimeout(() => notification.classList.add('opacity-0'), 4000);
            setTimeout(() => notification.remove(), 4300);
        }
    </script>
</body>
</html>