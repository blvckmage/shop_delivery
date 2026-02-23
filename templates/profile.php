<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Delivery</title>
    <?php echo $csrfMeta ?? ''; ?>
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in { animation: fadeIn 0.5s ease-in; }
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
                        Delivery
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛍️ Каталог</a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛒 Корзина</a>
                    <a href="/orders" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">📦 Заказы</a>
                    <a href="/profile" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">👤 Профиль</a>
                    <a href="/chat" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">💬 Чат</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                        <a href="/admin" class="text-purple-700 hover:text-purple-600 transition-colors duration-200 font-medium">⚙️ Админ</a>
                    <?php endif; ?>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</span>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">Выход</button>
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
                    <a href="/profile" class="block px-4 py-3 text-blue-600 font-semibold bg-blue-50 rounded-lg">👤 Профиль</a>
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
                    <span class="mr-2 md:mr-4">👤</span> Мой профиль
                </h1>
                <p class="text-sm md:text-base text-gray-600">Управляйте своими настройками и просмотрите историю заказов</p>
            </div>

            <!-- Profile Tabs -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-lg shadow-lg p-4 md:p-6">
                <!-- Mobile Tabs Dropdown -->
                <div class="md:hidden mb-4">
                    <select id="mobile-tab-select" onchange="showProfileTab(this.value)" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="info">👤 Личная информация</option>
                        <option value="orders">📦 История заказов</option>
                        <option value="favorites">❤️ Избранное</option>
                        <option value="reviews">⭐ Мои отзывы</option>
                    </select>
                </div>

                <!-- Desktop Tabs -->
                <div class="hidden md:block border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-4 md:space-x-8 overflow-x-auto">
                        <button onclick="showProfileTab('info')" id="tab-info" class="border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600 whitespace-nowrap">
                            Личная информация
                        </button>
                        <button onclick="showProfileTab('orders')" id="tab-orders" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                            История заказов
                        </button>
                        <button onclick="showProfileTab('favorites')" id="tab-favorites" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                            Избранное
                        </button>
                        <button onclick="showProfileTab('reviews')" id="tab-reviews" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                            Мои отзывы
                        </button>
                    </nav>
                </div>

                <!-- Personal Info Tab -->
                <div id="info-tab" class="tab-content">
                    <h3 class="text-base md:text-lg font-semibold mb-4">Личная информация</h3>
                    <form id="profileForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Имя</label>
                            <input type="text" id="profileName" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required
                                   class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-3 text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Телефон</label>
                            <input type="tel" id="profilePhone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required
                                   class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-3 text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email (опционально)</label>
                            <input type="email" id="profileEmail" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-3 text-base">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-colors font-medium">
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Orders History Tab -->
                <div id="orders-tab" class="tab-content hidden">
                    <h3 class="text-base md:text-lg font-semibold mb-4">История заказов</h3>
                    <div id="ordersList" class="space-y-4"></div>
                </div>

                <!-- Favorites Tab -->
                <div id="favorites-tab" class="tab-content hidden">
                    <h3 class="text-base md:text-lg font-semibold mb-4">Избранные товары</h3>
                    <div id="favoritesList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                </div>

                <!-- Reviews Tab -->
                <div id="reviews-tab" class="tab-content hidden">
                    <h3 class="text-base md:text-lg font-semibold mb-4">Мои отзывы</h3>
                    <div id="reviewsList" class="space-y-4"></div>
                </div>
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
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
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

        let currentProfileTab = 'info';

        function showProfileTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^=tab-]').forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            document.getElementById(tab + '-tab').classList.remove('hidden');
            const tabBtn = document.getElementById('tab-' + tab);
            if (tabBtn) {
                tabBtn.classList.remove('border-transparent', 'text-gray-500');
                tabBtn.classList.add('border-blue-500', 'text-blue-600');
            }

            // Update mobile select
            const mobileSelect = document.getElementById('mobile-tab-select');
            if (mobileSelect) mobileSelect.value = tab;

            currentProfileTab = tab;

            if (tab === 'orders') loadOrders();
            if (tab === 'favorites') loadFavorites();
            if (tab === 'reviews') loadReviews();
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.href = '/');
        }

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

        // Profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            try {
                const headers = { 'Content-Type': 'application/json' };
                const csrfToken = getCsrfToken();
                if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

                const response = await fetch('/api/profile/update', {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    showNotification('✅ Профиль обновлен успешно', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('❌ Ошибка при обновлении профиля', 'error');
                }
            } catch (error) {
                showNotification('❌ Ошибка сети', 'error');
            }
        });

        // Load orders
        async function loadOrders() {
            try {
                const response = await fetch('/api/orders/my');
                if (response.ok) {
                    const orders = await response.json();
                    const ordersList = document.getElementById('ordersList');

                    if (orders.length === 0) {
                        ordersList.innerHTML = '<p class="text-gray-500 text-center py-8">У вас пока нет заказов</p>';
                        return;
                    }

                    ordersList.innerHTML = orders.map(order => `
                        <div class="border border-gray-200 rounded-xl p-4 bg-white/50">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2 gap-2">
                                <h4 class="font-semibold">Заказ #${order.id}</h4>
                                <span class="text-sm text-gray-500">${new Date(order.created_at).toLocaleDateString()}</span>
                            </div>
                            <p class="text-sm text-gray-600">Статус: <span class="font-medium">${order.status}</span></p>
                            <p class="text-sm text-gray-600">Итого: <span class="font-bold text-green-600">${order.total_price} ₸</span></p>
                            <button onclick="reorder(${order.id})" class="mt-3 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Повторить заказ
                            </button>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        }

        // Load favorites
        async function loadFavorites() {
            try {
                const response = await fetch('/api/profile/favorites');
                if (response.ok) {
                    const favorites = await response.json();
                    const favoritesList = document.getElementById('favoritesList');

                    if (favorites.length === 0) {
                        favoritesList.innerHTML = '<p class="text-gray-500 col-span-full text-center py-8">У вас нет избранных товаров</p>';
                        return;
                    }

                    favoritesList.innerHTML = favorites.map(product => `
                        <div class="border border-gray-200 rounded-xl p-4 bg-white/50">
                            <div class="w-full h-32 bg-gradient-to-br from-blue-100 to-yellow-100 rounded-lg mb-3 flex items-center justify-center">
                                ${product.image_url ? 
                                    `<img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover rounded-lg">` : 
                                    `<span class="text-3xl">${product.name.charAt(0)}</span>`
                                }
                            </div>
                            <h4 class="font-semibold text-sm md:text-base truncate">${product.name}</h4>
                            <p class="text-sm text-green-600 font-bold">${product.price} ₸</p>
                            <button onclick="removeFromFavorites(${product.id})" class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                Удалить из избранного
                            </button>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error loading favorites:', error);
            }
        }

        // Load reviews
        async function loadReviews() {
            try {
                const response = await fetch('/api/profile/reviews');
                if (response.ok) {
                    const reviews = await response.json();
                    const reviewsList = document.getElementById('reviewsList');

                    if (reviews.length === 0) {
                        reviewsList.innerHTML = '<p class="text-gray-500 text-center py-8">У вас пока нет отзывов</p>';
                        return;
                    }

                    reviewsList.innerHTML = reviews.map(review => `
                        <div class="border border-gray-200 rounded-xl p-4 bg-white/50">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2 gap-2">
                                <h4 class="font-semibold">${review.product_name}</h4>
                                <div class="flex items-center text-yellow-500">
                                    ${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm md:text-base">${review.comment}</p>
                            <p class="text-sm text-gray-500 mt-2">${new Date(review.created_at).toLocaleDateString()}</p>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error loading reviews:', error);
            }
        }

        // Reorder function
        async function reorder(orderId) {
            try {
                const headers = { 'Content-Type': 'application/json' };
                const csrfToken = getCsrfToken();
                if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

                const response = await fetch(`/api/orders/${orderId}/reorder`, { 
                    method: 'POST',
                    headers 
                });
                if (response.ok) {
                    showNotification('✅ Заказ добавлен в корзину', 'success');
                    setTimeout(() => window.location.href = '/cart', 1500);
                } else {
                    showNotification('❌ Ошибка при повторном заказе', 'error');
                }
            } catch (error) {
                showNotification('❌ Ошибка сети', 'error');
            }
        }

        // Remove from favorites
        async function removeFromFavorites(productId) {
            try {
                const headers = {};
                const csrfToken = getCsrfToken();
                if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

                const response = await fetch(`/api/profile/favorites/${productId}`, { 
                    method: 'DELETE',
                    headers 
                });
                if (response.ok) {
                    showNotification('✅ Удалено из избранного', 'success');
                    loadFavorites();
                } else {
                    showNotification('❌ Ошибка при удалении', 'error');
                }
            } catch (error) {
                showNotification('❌ Ошибка сети', 'error');
            }
        }
    </script>
</body>
</html>