<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📋 Мои заказы - Delivery</title>
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
                        'slide-in': 'slideIn 0.4s ease-out'
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Custom animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes slideIn {
                from { opacity: 0; transform: translateX(-20px); }
                to { opacity: 1; transform: translateX(0); }
            }
            .animate-fade-in { animation: fadeIn 0.5s ease-in; }
            .animate-slide-in { animation: slideIn 0.4s ease-out; }
        `;
        document.head.appendChild(style);
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-yellow-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Delivery
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
                    <a href="/orders" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
                        📦 Заказы
                    </a>
                    <a href="/profile" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">👤 Профиль</a>
                    <a href="/chat" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">💬 Чат</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                        <a href="/admin" class="text-purple-700 hover:text-purple-600 transition-colors duration-200 font-medium">⚙️ Админ</a>
                    <?php endif; ?>
                    <!-- Notifications Bell -->
                    <div class="relative">
                        <button onclick="toggleNotifications()" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 relative">
                            🔔
                            <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</span>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Выход
                        </button>
                    </div>
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
                    <a href="/orders" class="block px-4 py-3 text-blue-600 font-semibold rounded-lg bg-blue-50 hover:bg-blue-100">
                        📦 Заказы
                    </a>
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
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="mr-4">📋</span> Мои заказы
                </h1>
                <p class="text-gray-600 text-lg">История всех ваших заказов</p>
            </div>

            <!-- Tab Navigation -->
            <div class="flex space-x-4 mb-6">
                <button id="activeTabBtn" onclick="switchTab('active')" class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-blue-500 text-white shadow-lg">
                    📦 Активные заказы
                </button>
                <button id="historyTabBtn" onclick="switchTab('history')" class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300">
                    📜 История заказов
                </button>
            </div>

            <!-- Orders Container -->
            <div id="ordersContainer" class="space-y-6">
                <!-- Loading state -->
                <div id="loadingState" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                    <span class="ml-3 text-gray-600">Загрузка заказов...</span>
                </div>

                <!-- No orders state -->
                <div id="noOrdersState" class="bg-white/70 backdrop-blur-sm rounded-3xl p-12 text-center shadow-lg hidden">
                    <div class="text-6xl mb-4">📦</div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">У вас пока нет заказов</h2>
                    <p class="text-gray-600 mb-8 text-lg">Самое время сделать первый заказ!</p>
                    <a href="/catalog" class="inline-flex items-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <span class="mr-2">🛍️</span> Перейти к покупкам
                    </a>
                </div>

                <!-- Orders will be loaded here -->
            </div>
        </div>
    </main>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Детали заказа</h3>
                        <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>
                </div>
                <div id="orderDetails" class="p-6">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div id="trackingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Отслеживание заказа</h3>
                        <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>
                </div>
                <div id="trackingDetails" class="p-6">
                    <!-- Tracking details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div id="notificationsModal" class="fixed top-16 right-4 w-96 max-h-[70vh] bg-white rounded-2xl shadow-2xl z-50 hidden transform transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 to-yellow-50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <span class="mr-2">🔔</span> Уведомления
            </h3>
            <div class="flex items-center space-x-2">
                <button onclick="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                    Прочитать все
                </button>
                <button onclick="closeNotifications()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
        </div>
        <div id="notificationsList" class="max-h-96 overflow-y-auto">
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Delivery. Все права защищены.</p>
        </div>
    </footer>

    <script>
        let orders = [];
        let historyOrders = [];
        let currentTab = 'active';

        // Switch between tabs
        function switchTab(tab) {
            currentTab = tab;
            const activeBtn = document.getElementById('activeTabBtn');
            const historyBtn = document.getElementById('historyTabBtn');
            
            if (tab === 'active') {
                activeBtn.className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-blue-500 text-white shadow-lg';
                historyBtn.className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                displayOrders(orders);
            } else {
                activeBtn.className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                historyBtn.className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-blue-500 text-white shadow-lg';
                // Load history if not loaded yet
                if (historyOrders.length === 0) {
                    loadHistory();
                } else {
                    displayHistory(historyOrders);
                }
            }
        }

        // Update cart count
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartCount = document.getElementById('cart-count');
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        }

        // Load orders
        async function loadOrders() {
            try {
                const response = await fetch('/api/orders');
                const ordersData = await response.json();

                if (Array.isArray(ordersData) && ordersData.length > 0) {
                    orders = ordersData;
                    displayOrders(orders);
                } else {
                    showNoOrdersState();
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                showNoOrdersState();
            }
        }

        // Display orders
        function displayOrders(ordersList) {
            const container = document.getElementById('ordersContainer');

            // Clear container completely
            container.innerHTML = '';

            if (ordersList.length === 0) {
                // Show no orders state inside container
                container.innerHTML = `
                    <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-12 text-center shadow-lg">
                        <div class="text-6xl mb-4">📦</div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">У вас пока нет заказов</h2>
                        <p class="text-gray-600 mb-8 text-lg">Самое время сделать первый заказ!</p>
                        <a href="/catalog" class="inline-flex items-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <span class="mr-2">🛍️</span> Перейти к покупкам
                        </a>
                    </div>
                `;
                return;
            }

            const ordersHtml = ordersList.map((order, index) => createOrderCard(order, index)).join('');
            container.innerHTML = ordersHtml;
        }

        // Create order card
        function createOrderCard(order, index) {
            const statusColors = {
                'СОЗДАН': 'bg-blue-100 text-blue-800',
                'СБОРКА': 'bg-yellow-100 text-yellow-800',
                'ОЖИДАНИЕ_КУРЬЕРА': 'bg-orange-100 text-orange-800',
                'В_ПУТИ': 'bg-indigo-100 text-indigo-800',
                'ДОСТАВЛЕН': 'bg-green-100 text-green-800',
                'ОТМЕНЕН': 'bg-red-100 text-red-800'
            };

            const paymentStatusColors = {
                'Ожидает': 'bg-gray-100 text-gray-800',
                'Оплачен': 'bg-green-100 text-green-800',
                'Отменен': 'bg-red-100 text-red-800'
            };

            const itemsCount = Array.isArray(order.items) ? order.items.length : 0;
            const itemsText = itemsCount === 1 ? '1 товар' : (itemsCount < 5 ? `${itemsCount} товара` : `${itemsCount} товаров`);

            return `
                <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 animate-slide-in"
                     style="animation-delay: ${index * 0.1}s">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Заказ #${order.id}</h3>
                            <p class="text-gray-600">${new Date(order.created_at).toLocaleDateString('ru-RU')}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-600">${order.total_price} ₸</div>
                            <div class="text-sm text-gray-500">${itemsText}</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                            ${getStatusText(order.status)}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${paymentStatusColors[order.payment_status] || 'bg-gray-100 text-gray-800'}">
                            ${order.payment_status}
                        </span>
                        ${order.delivery_included ? '<span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">🚚 С доставкой</span>' : ''}
                        ${order.delivery_status ? `<span class="px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">📦 ${order.delivery_status}</span>` : ''}
                    </div>

                    <div class="text-sm text-gray-600 mb-4">
                        <strong>Адрес:</strong> ${order.address}
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex space-x-3">
                            <button onclick="showOrderDetails(${order.id})"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-xl transition-colors duration-200">
                                📋 Подробнее
                            </button>
                        </div>
                        <div class="text-xs text-gray-500">
                            Создан ${new Date(order.created_at).toLocaleString('ru-RU')}
                        </div>
                    </div>
                </div>
            `;
        }

// Status text mapping for user display
function getStatusText(status) {
    switch (status) {
        case 'СОЗДАН': return 'Заказ создан';
        case 'СБОРКА': return 'Заказ собирается';
        case 'ОЖИДАНИЕ_КУРЬЕРА': return 'Ожидание курьера';
        case 'В_ПУТИ': return 'Заказ в пути';
        case 'ДОСТАВЛЕН': return 'Заказ доставлен';
        case 'ОТМЕНЕН': return 'Заказ отменен';
        default: return status;
    }
}

        // Show no orders state
        function showNoOrdersState() {
            const container = document.getElementById('ordersContainer');
            container.innerHTML = `
                <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-12 text-center shadow-lg">
                    <div class="text-6xl mb-4">📦</div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">У вас пока нет заказов</h2>
                    <p class="text-gray-600 mb-8 text-lg">Самое время сделать первый заказ!</p>
                    <a href="/catalog" class="inline-flex items-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <span class="mr-2">🛍️</span> Перейти к покупкам
                    </a>
                </div>
            `;
        }
        
        // Show no history state
        function showNoHistoryState() {
            const container = document.getElementById('ordersContainer');
            container.innerHTML = `
                <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-12 text-center shadow-lg">
                    <div class="text-6xl mb-4">📜</div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">История заказов пуста</h2>
                    <p class="text-gray-600 mb-8 text-lg">У вас пока нет доставленных заказов</p>
                </div>
            `;
        }

        // Load history orders
        async function loadHistory() {
            try {
                const response = await fetch('/api/orders/history');
                const historyData = await response.json();

                if (Array.isArray(historyData) && historyData.length > 0) {
                    historyOrders = historyData;
                    displayHistory(historyOrders);
                } else {
                    showNoHistoryState();
                }
            } catch (error) {
                console.error('Error loading history:', error);
                showNoHistoryState();
            }
        }

        // Display history orders
        function displayHistory(historyList) {
            const container = document.getElementById('ordersContainer');

            // Clear container
            container.innerHTML = '';

            if (historyList.length === 0) {
                showNoHistoryState();
                return;
            }

            const historyHtml = historyList.map((order, index) => createHistoryCard(order, index)).join('');
            container.innerHTML = historyHtml;
        }

        // Create history card
        function createHistoryCard(order, index) {
            const itemsCount = Array.isArray(order.items) ? order.items.length : 0;
            const itemsText = itemsCount === 1 ? '1 товар' : (itemsCount < 5 ? `${itemsCount} товара` : `${itemsCount} товаров`);

            return `
                <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 animate-slide-in"
                     style="animation-delay: ${index * 0.1}s">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Заказ #${order.id}</h3>
                            <p class="text-gray-600">${new Date(order.created_at).toLocaleDateString('ru-RU')}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-600">${order.total_price} ₸</div>
                            <div class="text-sm text-gray-500">${itemsText}</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ Доставлен
                        </span>
                        ${order.delivery_included ? '<span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">🚚 С доставкой</span>' : ''}
                    </div>

                    <div class="text-sm text-gray-600 mb-4">
                        <strong>Адрес:</strong> ${order.address}
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex space-x-3">
                            <button onclick="showHistoryOrderDetails(${order.id})"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-xl transition-colors duration-200">
                                📋 Подробнее
                            </button>
                        </div>
                        <div class="text-xs text-gray-500">
                            Доставлен ${order.archived_at ? new Date(order.archived_at).toLocaleString('ru-RU') : new Date(order.created_at).toLocaleString('ru-RU')}
                        </div>
                    </div>
                </div>
            `;
        }

        // Show history order details
        function showHistoryOrderDetails(orderId) {
            const order = historyOrders.find(o => o.id == orderId);
            if (!order) return;

            const modal = document.getElementById('orderModal');
            const details = document.getElementById('orderDetails');

            const itemsHtml = order.items.map(item => `
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-yellow-100 rounded-lg flex items-center justify-center">
                        <span class="text-lg">${item.name ? item.name.charAt(0) : '?'}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">${item.name || 'Товар'}</h4>
                        <p class="text-gray-600">${item.price} ₸ × ${item.quantity}</p>
                    </div>
                    <div class="font-bold text-green-600">
                        ${(item.price || 0) * (item.quantity || 1)} ₸
                    </div>
                </div>
            `).join('');

            details.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Информация о заказе</h4>
                            <p><strong>ID:</strong> #${order.id}</p>
                            <p><strong>Дата:</strong> ${new Date(order.created_at).toLocaleString('ru-RU')}</p>
                            <p><strong>Статус:</strong> ✅ Доставлен</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Доставка</h4>
                            <p><strong>Адрес:</strong> ${order.address}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Товары</h4>
                        <div class="space-y-3">
                            ${itemsHtml}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Итого:</span>
                            <span class="text-2xl font-bold text-green-600">${order.total_price} ₸</span>
                        </div>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
        }

        let orderModalMap = null;
        let courierModalMarker = null;
        let courierLocationInterval = null;

        // Show order details
        async function showOrderDetails(orderId) {
            const order = orders.find(o => o.id == orderId);
            if (!order) return;

            const modal = document.getElementById('orderModal');
            const details = document.getElementById('orderDetails');

            const itemsHtml = order.items.map(item => `
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-yellow-100 rounded-lg flex items-center justify-center">
                        <span class="text-lg">${item.name.charAt(0)}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">${item.name}</h4>
                        <p class="text-gray-600">${item.price} ₸ × ${item.quantity}</p>
                    </div>
                    <div class="font-bold text-green-600">
                        ${item.price * item.quantity} ₸
                    </div>
                </div>
            `).join('');

            // Check if order has courier and is in transit
            const showCourierMap = order.status === 'В_ПУТИ' || order.status === 'ОЖИДАНИЕ_КУРЬЕРА';

            details.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Информация о заказе</h4>
                            <p><strong>ID:</strong> #${order.id}</p>
                            <p><strong>Дата:</strong> ${new Date(order.created_at).toLocaleString('ru-RU')}</p>
                            <p><strong>Статус:</strong> ${getStatusText(order.status)}</p>
                            <p><strong>Оплата:</strong> ${order.payment_status}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Доставка</h4>
                            <p><strong>Адрес:</strong> ${order.address}</p>
                        </div>
                    </div>

                    ${showCourierMap ? `
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">📍 Местоположение курьера</h4>
                            <div id="orderModalMap" class="w-full h-48 rounded-xl border border-gray-200"></div>
                            <div id="courierDetails" class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">Загрузка данных курьера...</p>
                            </div>
                        </div>
                    ` : ''}

                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Товары</h4>
                        <div class="space-y-3">
                            ${itemsHtml}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Итого:</span>
                            <span class="text-2xl font-bold text-green-600">${order.total_price} ₸</span>
                        </div>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');

            // Initialize map and load courier location
            if (showCourierMap) {
                setTimeout(() => initOrderModalMap(orderId), 100);
                // Update courier location every 10 seconds
                courierLocationInterval = setInterval(() => loadCourierLocation(orderId), 10000);
            }
        }

        // Initialize map in order modal
        function initOrderModalMap(orderId) {
            // Координаты магазина в Кентау
            const storeLat = 43.518703;
            const storeLng = 68.505423;

            orderModalMap = L.map('orderModalMap').setView([storeLat, storeLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(orderModalMap);

            // Add store marker
            L.marker([storeLat, storeLng]).addTo(orderModalMap).bindPopup('🏪 Магазин');

            // Load courier location
            loadCourierLocation(orderId);
        }

        // Load courier location for order
        async function loadCourierLocation(orderId) {
            try {
                const response = await fetch(`/api/orders/${orderId}/courier-location`);
                if (response.ok) {
                    const data = await response.json();
                    updateCourierOnModalMap(data);
                }
            } catch (error) {
                console.error('Error loading courier location:', error);
            }
        }

        // Update courier marker on modal map
        function updateCourierOnModalMap(data) {
            const courierDetails = document.getElementById('courierDetails');
            
            if (!data.courier || !data.location) {
                if (courierDetails) {
                    courierDetails.innerHTML = `
                        <p class="text-sm text-gray-600">⏳ Курьер ещё не назначен или местоположение недоступно</p>
                    `;
                }
                return;
            }

            // Update courier details
            if (courierDetails) {
                courierDetails.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-lg">🚴</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">${data.courier.name || 'Курьер'}</p>
                                <p class="text-sm text-gray-600">${data.courier.phone || ''}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                📍 В пути
                            </span>
                        </div>
                    </div>
                `;
            }

            // Update marker on map
            if (orderModalMap && data.location) {
                const lat = parseFloat(data.location.lat);
                const lng = parseFloat(data.location.lng);

                if (!isNaN(lat) && !isNaN(lng)) {
                    if (courierModalMarker) {
                        courierModalMarker.setLatLng([lat, lng]);
                    } else {
                        courierModalMarker = L.marker([lat, lng]).addTo(orderModalMap);
                        courierModalMarker.bindPopup('🚴 Курьер').openPopup();
                    }
                    orderModalMap.setView([lat, lng], 15);
                }
            }
        }

        // Show tracking modal
        function showTracking(orderId) {
            const order = orders.find(o => o.id == orderId);
            if (!order) return;

            const modal = document.getElementById('trackingModal');
            const trackingDetails = document.getElementById('trackingDetails');

            // Load tracking data
            loadTrackingData(orderId);

            trackingDetails.innerHTML = `
                <div class="space-y-6">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Отслеживание заказа #${order.id}</h3>
                        <p class="text-gray-600">Статус: <span class="font-semibold">${order.status}</span></p>
                    </div>

                    <div id="trackingMap" class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                        <p class="text-gray-500">Загрузка карты...</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-800 mb-2">Адрес доставки</h4>
                            <p class="text-blue-700">${order.address}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-800 mb-2">Курьер</h4>
                            <p class="text-green-700" id="courierInfo">Ожидается назначение</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-3">История статусов</h4>
                        <div id="statusHistory" class="space-y-2">
                            <!-- Status history will be loaded here -->
                        </div>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
        }

        // Load tracking data
        async function loadTrackingData(orderId) {
            try {
                const response = await fetch(`/api/orders/${orderId}/tracking`);
                if (response.ok) {
                    const data = await response.json();
                    updateTrackingMap(data);
                    updateCourierInfo(data);
                    updateStatusHistory(data);
                }
            } catch (error) {
                console.error('Error loading tracking data:', error);
            }
        }

        // Update tracking map
        function updateTrackingMap(data) {
            const mapContainer = document.getElementById('trackingMap');
            mapContainer.innerHTML = '';

            // Initialize map
            const map = L.map('trackingMap').setView([43.518802, 68.505393], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add store marker
            const storeMarker = L.marker([43.518802, 68.505393]).addTo(map);
            storeMarker.bindPopup('<b>Магазин</b><br>Наш склад').openPopup();

            // Add destination marker
            if (data.order && data.order.address) {
                const destMarker = L.marker([43.518802, 68.505393]).addTo(map);
                destMarker.bindPopup('<b>Адрес доставки</b><br>' + data.order.address);
            }

            // Add courier marker
            if (data.courier_location) {
                const courierMarker = L.marker([data.courier_location.lat, data.courier_location.lng], {
                    icon: L.icon({
                        iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
                        shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34]
                    })
                }).addTo(map);
                courierMarker.bindPopup('<b>Курьер</b><br>Текущее положение').openPopup();
            }

            // Fit bounds to show all markers
            const group = new L.featureGroup([storeMarker]);
            if (data.order && data.order.address) group.addLayer(L.marker([43.518802, 68.505393]));
            if (data.courier_location) group.addLayer(L.marker([data.courier_location.lat, data.courier_location.lng]));
            map.fitBounds(group.getBounds().pad(0.5));
        }

        // Update courier info
        function updateCourierInfo(data) {
            const courierInfo = document.getElementById('courierInfo');
            if (data.courier_location) {
                courierInfo.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                        <span>${data.courier.name}</span>
                        <span class="text-sm text-gray-600">(${data.courier.phone})</span>
                    </div>
                `;
            } else {
                courierInfo.innerHTML = 'Ожидается назначение';
            }
        }

        // Update status history
        function updateStatusHistory(data) {
            const statusHistory = document.getElementById('statusHistory');
            if (data.statusHistory && data.statusHistory.length > 0) {
                statusHistory.innerHTML = data.statusHistory.map(status => `
                    <div class="flex items-center justify-between p-2 bg-white rounded-lg">
                        <div>
                            <span class="font-semibold">${status.status}</span>
                            <span class="text-sm text-gray-600 ml-2">${new Date(status.updated_at).toLocaleString('ru-RU')}</span>
                        </div>
                        <div class="w-3 h-3 rounded-full ${getStatusColor(status.status)}"></div>
                    </div>
                `).join('');
            } else {
                statusHistory.innerHTML = '<p class="text-gray-600">История статусов пока недоступна</p>';
            }
        }

        // Get status color
        function getStatusColor(status) {
            const colors = {
                'NEW': 'bg-blue-500',
                'ACCEPTED': 'bg-yellow-500',
                'COOKING': 'bg-purple-500',
                'READY': 'bg-orange-500',
                'TAKEN_BY_COURIER': 'bg-cyan-500',
                'В_ПУТИ': 'bg-indigo-500',
                'ДОСТАВЛЕН': 'bg-green-500'
            };
            return colors[status] || 'bg-gray-500';
        }

        // Close order modal
        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            // Clear location tracking interval
            if (courierLocationInterval) {
                clearInterval(courierLocationInterval);
                courierLocationInterval = null;
            }
            // Reset map
            orderModalMap = null;
            courierModalMarker = null;
        }

        // Close tracking modal
        function closeTrackingModal() {
            document.getElementById('trackingModal').classList.add('hidden');
        }

        // Logout function
        function logout() {
            fetch('/api/auth/logout', { method: 'POST' })
                .then(() => location.reload());
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            loadOrders();
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 30000);
            
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
        
        // =====================
        // Notifications Functions
        // =====================
        let notificationsOpen = false;
        
        function toggleNotifications() {
            if (notificationsOpen) { closeNotifications(); } else { openNotifications(); }
        }
        
        function openNotifications() {
            const modal = document.getElementById('notificationsModal');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('scale-95', 'opacity-0'); modal.classList.add('scale-100', 'opacity-100'); }, 10);
            notificationsOpen = true;
            loadNotifications();
        }
        
        function closeNotifications() {
            const modal = document.getElementById('notificationsModal');
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
            notificationsOpen = false;
        }
        
        async function loadNotifications() {
            const list = document.getElementById('notificationsList');
            list.innerHTML = '<div class="p-4 text-center text-gray-500">Загрузка...</div>';
            try {
                const response = await fetch('/api/notifications');
                if (response.ok) { renderNotifications(await response.json()); }
                else { list.innerHTML = '<div class="p-4 text-center text-gray-500">Ошибка загрузки</div>'; }
            } catch (error) { list.innerHTML = '<div class="p-4 text-center text-gray-500">Ошибка сети</div>'; }
        }
        
        function renderNotifications(notifications) {
            const list = document.getElementById('notificationsList');
            if (notifications.length === 0) { list.innerHTML = '<div class="p-8 text-center text-gray-500"><div class="text-4xl mb-2">📭</div>Нет уведомлений</div>'; return; }
            list.innerHTML = notifications.map(n => {
                const icon = {'order_created':'📦','order_status':'🚚','new_order':'🆕','delivery':'🛵','system':'⚙️','promo':'🎁'}[n.type] || '🔔';
                const bgColor = n.read ? 'bg-gray-50' : 'bg-blue-50';
                return `<div class="p-4 border-b border-gray-100 ${bgColor} hover:bg-gray-100 transition-colors cursor-pointer" onclick="handleNotificationClick(${n.id}, ${n.data?.order_id ? n.data.order_id : 'null'})">
                    <div class="flex items-start space-x-3">
                        <div class="text-2xl">${icon}</div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 text-sm">${n.title}</div>
                            <div class="text-gray-600 text-sm">${n.message}</div>
                            <div class="text-gray-400 text-xs mt-1">${formatTimeAgo(n.created_at)}</div>
                        </div>
                        ${!n.read ? '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                    </div>
                </div>`;
            }).join('');
        }
        
        function formatTimeAgo(dateString) {
            const diff = Math.floor((new Date() - new Date(dateString)) / 1000);
            if (diff < 60) return 'только что';
            if (diff < 3600) return Math.floor(diff / 60) + ' мин назад';
            if (diff < 86400) return Math.floor(diff / 3600) + ' ч назад';
            return Math.floor(diff / 86400) + ' дн назад';
        }
        
        async function handleNotificationClick(notificationId, orderId) {
            await fetch(`/api/notifications/${notificationId}/read`, { method: 'POST' });
            updateNotificationBadge();
            if (orderId) loadOrders();
            closeNotifications();
        }
        
        async function markAllAsRead() {
            await fetch('/api/notifications/read-all', { method: 'POST' });
            loadNotifications();
            updateNotificationBadge();
        }
        
        async function updateNotificationBadge() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                if (response.ok) {
                    const data = await response.json();
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) { badge.textContent = data.count > 9 ? '9+' : data.count; badge.classList.remove('hidden'); }
                    else { badge.classList.add('hidden'); }
                }
            } catch (error) { console.error('Error updating badge:', error); }
        }
        
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('notificationsModal');
            const bell = document.querySelector('[onclick="toggleNotifications()"]');
            if (notificationsOpen && modal && !modal.contains(e.target) && bell && !bell.contains(e.target)) { closeNotifications(); }
        });

        // Close modals when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(e) {
            // Close if clicked on the backdrop or the flex container
            if (e.target === this || e.target.classList.contains('flex')) {
                closeOrderModal();
            }
        });
        
        document.getElementById('trackingModal').addEventListener('click', function(e) {
            // Close if clicked on the backdrop or the flex container
            if (e.target === this || e.target.classList.contains('flex')) {
                closeTrackingModal();
            }
        });
    </script>
</body>
</html>