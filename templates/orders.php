<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Мои заказы - Delivery</title>
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
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body class="gradient-hero min-h-screen pb-20 md:pb-0">
    <!-- Header -->
    <header class="glass sticky top-0 z-50 border-b border-warm-100">
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
                    <a href="/orders" class="text-warm-600 font-medium">Заказы</a>
                    <a href="/chat" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Чат</a>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="hidden md:block">
                        <?php if ($isLoggedIn): ?>
                            <div class="flex items-center space-x-3">
                                <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                                    <a href="/admin" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Панель администратора</a>
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

    <!-- Orders Content -->
    <section class="px-4 py-6">
        <div class="container mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Мои заказы</h1>
            
            <?php if (!$isLoggedIn): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-warm-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-gray-500 mb-4">Войдите, чтобы увидеть ваши заказы</p>
                <a href="/login" class="inline-block btn-primary text-white px-6 py-3 rounded-full font-medium">
                    Войти
                </a>
            </div>
            <?php else: ?>
            
            <!-- Tabs -->
            <div class="flex space-x-2 mb-6">
                <button onclick="showTab('active')" id="tab-active" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-warm-500 text-white">
                    Активные
                </button>
                <button onclick="showTab('history')" id="tab-history" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow">
                    История
                </button>
            </div>
            
            <!-- Active Orders -->
            <div id="orders-active" class="space-y-4">
                <!-- Active orders will be loaded here -->
            </div>
            
            <!-- History Orders -->
            <div id="orders-history" class="space-y-4 hidden">
                <!-- History orders will be loaded here -->
            </div>
            
            <div id="loading" class="text-center py-8">
                <div class="inline-block w-8 h-8 border-2 border-warm-200 border-t-warm-500 rounded-full animate-spin"></div>
            </div>
            
            <?php endif; ?>
        </div>
    </section>

    <!-- Order Details Modal -->
    <div id="order-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="closeOrderModal()"></div>
        <div class="absolute bottom-0 left-0 right-0 md:bottom-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:max-w-md md:w-full bg-white md:rounded-2xl rounded-t-3xl max-h-[85vh] overflow-hidden flex flex-col">
            <div class="flex justify-center pt-2 pb-1 md:hidden">
                <div class="w-12 h-1 bg-gray-300 rounded-full"></div>
            </div>
            <div id="modal-content" class="overflow-y-auto flex-1">
                <!-- Modal content will be rendered here -->
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-gray-100 bottom-nav z-40">
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
            <a href="/orders" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Заказы</span>
            </a>
            <a href="/cart" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-xs mt-1">Корзина</span>
                <span id="cart-badge-mobile" class="hidden absolute top-0 right-4 w-4 h-4 bg-warm-500 text-white text-[10px] rounded-full flex items-center justify-center font-medium">0</span>
            </a>
            <?php if ($isLoggedIn): ?>
            <a href="/profile" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Профиль</span>
            </a>
            <?php else: ?>
            <a href="/login" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="text-xs mt-1">Войти</span>
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
        const statusLabels = {
            'СОЗДАН': { text: 'Создан', class: 'bg-blue-100 text-blue-700' },
            'В_ПУТИ': { text: 'В пути', class: 'bg-warm-100 text-warm-700' },
            'ДОСТАВЛЕН': { text: 'Доставлен', class: 'bg-gray-100 text-gray-700' },
            'ОТМЕНЕН': { text: 'Отменен', class: 'bg-red-100 text-red-700' }
        };

        function getStatusBadge(status) {
            const s = statusLabels[status] || { text: status, class: 'bg-gray-100 text-gray-700' };
            return `<span class="status-badge ${s.class}">${s.text}</span>`;
        }

        function showTab(tab) {
            const activeTab = document.getElementById('tab-active');
            const historyTab = document.getElementById('tab-history');
            const activeOrders = document.getElementById('orders-active');
            const historyOrders = document.getElementById('orders-history');
            
            if (tab === 'active') {
                activeTab.className = 'flex-1 py-3 rounded-xl font-medium transition-colors bg-warm-500 text-white';
                historyTab.className = 'flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow';
                activeOrders.classList.remove('hidden');
                historyOrders.classList.add('hidden');
            } else {
                historyTab.className = 'flex-1 py-3 rounded-xl font-medium transition-colors bg-warm-500 text-white';
                activeTab.className = 'flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow';
                historyOrders.classList.remove('hidden');
                activeOrders.classList.add('hidden');
            }
        }

        async function loadOrders() {
            try {
                const [activeRes, historyRes] = await Promise.all([
                    fetch('/api/orders'),
                    fetch('/api/orders/history')
                ]);
                
                const activeOrders = await activeRes.json();
                const historyOrders = await historyRes.json();
                
                // Store for modal access
                window.activeOrdersData = activeOrders;
                window.historyOrdersData = historyOrders;
                
                renderOrders('orders-active', activeOrders, true);
                renderOrders('orders-history', historyOrders, false);
                
                document.getElementById('loading').classList.add('hidden');
            } catch (error) {
                console.error('Error loading orders:', error);
                document.getElementById('loading').innerHTML = '<p class="text-gray-500">Ошибка загрузки</p>';
            }
        }

        function renderOrders(containerId, orders, isActive) {
            const container = document.getElementById(containerId);
            
            if (!orders || orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-warm-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500">${isActive ? 'Нет активных заказов' : 'История пуста'}</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = orders.map(order => {
                let items = [];
                try {
                    items = typeof order.items === 'string' ? JSON.parse(order.items) : (order.items || []);
                } catch (e) {
                    items = [];
                }
                
                // Весовые товары считаем как 1 позицию, штучные - по количеству
                const itemsCount = items.reduce((sum, i) => {
                    if (i.is_weighted) {
                        return sum + 1; // Весовой товар = 1 позиция
                    } else {
                        return sum + Math.round(i.quantity || 1);
                    }
                }, 0);
                
                // Рассчитываем общую сумму
                const total = items.reduce((sum, i) => {
                    if (i.is_weighted) {
                        // Для весовых: price = цена за кг, quantity = вес в кг
                        return sum + Math.round((i.price || 0) * (i.quantity || 1));
                    } else {
                        return sum + ((i.price || 0) * (i.quantity || 1));
                    }
                }, 0);
                
                const displayTotal = order.total || total || 0;
                
                return `
                    <div onclick="showOrderDetails(${order.id})" class="bg-white rounded-2xl p-4 card-shadow cursor-pointer hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">Заказ #${order.id}</h3>
                                <p class="text-sm text-gray-500">${formatDate(order.created_at)}</p>
                            </div>
                            ${getStatusBadge(order.status)}
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-3">
                            <p>${itemsCount} ${itemsCount === 1 ? 'товар' : itemsCount < 5 ? 'товара' : 'товаров'}</p>
                            <p class="font-semibold text-warm-600 mt-1">${displayTotal.toLocaleString('ru-RU')} ₸</p>
                        </div>
                        
                        <div class="text-sm text-gray-500 mb-3">
                            <svg class="w-4 h-4 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            ${order.address || 'Адрес не указан'}
                        </div>
                        
                        ${isActive && (order.status === 'ON_THE_WAY' || order.status === 'В_ПУТИ') ? `
                            <button onclick="event.stopPropagation(); trackOrder(${order.id})" class="w-full py-2.5 bg-warm-50 hover:bg-warm-100 text-warm-600 font-medium rounded-xl transition-colors text-sm">
                                Отследить заказ
                            </button>
                        ` : ''}
                    </div>
                `;
            }).join('');
        }

        // Order Details Modal
        let currentOrder = null;
        
        function showOrderDetails(orderId) {
            // Find order in loaded data
            const order = [...(window.activeOrdersData || []), ...(window.historyOrdersData || [])].find(o => o.id === orderId);
            if (order) {
                renderOrderModal(order);
            } else {
                // Fetch order details
                fetch(`/api/orders/${orderId}`)
                    .then(res => res.json())
                    .then(order => renderOrderModal(order))
                    .catch(err => console.error('Error loading order:', err));
            }
        }
        
        function renderOrderModal(order) {
            currentOrder = order;
            
            let items = [];
            try {
                items = typeof order.items === 'string' ? JSON.parse(order.items) : (order.items || []);
            } catch (e) {
                items = [];
            }
            
            // Calculate total
            const total = items.reduce((sum, i) => {
                if (i.is_weighted) {
                    return sum + Math.round((i.price || 0) * (i.quantity || 1));
                } else {
                    return sum + ((i.price || 0) * (i.quantity || 1));
                }
            }, 0);
            
            const displayTotal = order.total || total || 0;
            
            const statusLabels = {
                'СОЗДАН': { text: 'Создан', class: 'bg-blue-100 text-blue-700' },
                'В_ПУТИ': { text: 'В пути', class: 'bg-warm-100 text-warm-700' },
                'ДОСТАВЛЕН': { text: 'Доставлен', class: 'bg-gray-100 text-gray-700' },
                'ОТМЕНЕН': { text: 'Отменен', class: 'bg-red-100 text-red-700' }
            };
            const status = statusLabels[order.status] || { text: order.status, class: 'bg-gray-100 text-gray-700' };
            
            const itemsHtml = items.map(item => {
                const isWeighted = item.is_weighted;
                const qty = isWeighted ? `${item.quantity} кг` : `${item.quantity} шт`;
                const itemTotal = isWeighted 
                    ? Math.round((item.price || 0) * (item.quantity || 1))
                    : (item.price || 0) * (item.quantity || 1);
                
                return `
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-warm-100 to-warm-200 flex-shrink-0 overflow-hidden">
                            ${item.image_url 
                                ? `<img src="${item.image_url}" alt="${item.name}" class="w-full h-full object-cover">`
                                : `<div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-warm-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                   </div>`
                            }
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 text-sm truncate">${item.name || 'Товар'}</h4>
                            <p class="text-xs text-gray-500">${qty} × ${item.price || 0} ₸${isWeighted ? '/кг' : ''}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">${itemTotal.toLocaleString('ru-RU')} ₸</p>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('modal-content').innerHTML = `
                <div class="p-4 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Заказ #${order.id}</h3>
                            <p class="text-sm text-gray-500">${formatDate(order.created_at)}</p>
                        </div>
                        <span class="status-badge ${status.class}">${status.text}</span>
                    </div>
                </div>
                
                <div class="p-4 space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Товары</h4>
                        <div class="space-y-2">
                            ${itemsHtml || '<p class="text-gray-500">Нет товаров</p>'}
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Адрес доставки:</span>
                        </div>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-xl">${order.address || 'Не указан'}</p>
                    </div>
                    
                    <div class="bg-warm-50 rounded-xl p-4">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900">Итого:</span>
                            <span class="text-xl font-bold text-warm-600">${displayTotal.toLocaleString('ru-RU')} ₸</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-100">
                    <button onclick="closeOrderModal()" class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Закрыть
                    </button>
                </div>
            `;
            
            document.getElementById('order-modal').classList.remove('hidden');
        }
        
        function closeOrderModal() {
            document.getElementById('order-modal').classList.add('hidden');
            currentOrder = null;
        }
        
        // Close modal on backdrop click
        document.getElementById('order-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', { 
                day: 'numeric', 
                month: 'long',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function trackOrder(orderId) {
            window.location.href = `/orders?id=${orderId}`;
        }

        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            // Весовые товары считаем как 1 позицию, штучные - по количеству
            const count = cart.reduce((sum, item) => {
                if (item.is_weighted) {
                    return sum + 1;
                } else {
                    return sum + Math.round(item.quantity || 1);
                }
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

        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            <?php if ($isLoggedIn): ?>
            loadOrders();
            <?php endif; ?>
        });
    </script>
</body>
</html>