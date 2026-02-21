<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✨ Регистрация - Kazyna Market</title>
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
            @keyframes bounceGentle {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            .animate-fade-in { animation: fadeIn 0.6s ease-in; }
            .animate-bounce-gentle { animation: bounceGentle 2s infinite; }
        `;
        document.head.appendChild(style);
    </script>
</head>
<body class="bg-gradient-to-br from-yellow-50 via-white to-orange-50 min-h-screen">
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
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛍️ Каталог</a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛒 Корзина</a>
                    <a href="/login" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">Войти</a>
                    <a href="/register" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-semibold">✨ Регистрация</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl p-2">☰</button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200 mt-4 rounded-xl">
                <div class="px-4 py-4 space-y-2">
                    <a href="/catalog" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">🛍️ Каталог</a>
                    <a href="/cart" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">🛒 Корзина</a>
                    <hr class="my-2">
                    <a href="/login" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Войти</a>
                    <a href="/register" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg transition-colors text-center font-medium">✨ Регистрация</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 md:py-16 animate-fade-in">
        <div class="max-w-md mx-auto">
            <!-- Welcome Illustration -->
            <div class="text-center mb-6 md:mb-8">
                <div class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-r from-yellow-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 animate-bounce-gentle">
                    <span class="text-2xl md:text-3xl">✨</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Присоединяйтесь!</h1>
                <p class="text-sm md:text-base text-gray-600">Создайте аккаунт и откройте мир качественных товаров</p>
            </div>

            <!-- Register Form -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-xl">
                <form id="registerForm" class="space-y-4 md:space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">👤 Имя</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 text-base"
                               placeholder="Ваше имя">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">📱 Телефон</label>
                        <input type="tel" id="phone" name="phone" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 text-base"
                               placeholder="+77001234567">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">🔐 Пароль</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 text-base"
                               placeholder="Придумайте пароль">
                        <p class="text-xs text-gray-500 mt-1">Минимум 6 символов</p>
                    </div>

                    <button type="submit" id="registerBtn"
                            class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white py-3 md:py-4 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] shadow-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center">
                            <span id="btnText">🎉 Зарегистрироваться</span>
                            <span id="btnSpinner" class="hidden ml-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </span>
                    </button>
                </form>

                <div class="mt-4 md:mt-6 text-center">
                    <p class="text-sm md:text-base text-gray-600">
                        Уже есть аккаунт?
                        <a href="/login" class="text-yellow-600 hover:text-yellow-700 font-semibold transition-colors duration-200">Войти</a>
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

        // Register form handler
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('registerBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btnText.textContent = 'Регистрация...';
            btnSpinner.classList.remove('hidden');

            const formData = new FormData(e.target);
            const data = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                password: formData.get('password')
            };

            try {
                const headers = { 'Content-Type': 'application/json' };
                const csrfToken = getCsrfToken();
                if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('🎉 Регистрация успешна!', 'success');
                    setTimeout(() => location.href = '/login', 2000);
                } else {
                    showNotification('❌ ' + (result.error || 'Ошибка регистрации'), 'error');
                }
            } catch (error) {
                showNotification('❌ Ошибка сети', 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = '🎉 Зарегистрироваться';
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