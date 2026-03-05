<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Сборщик - Delivery</title>
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
        
        .btn-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        .bottom-nav {
            padding-bottom: env(safe-area-inset-bottom, 16px);
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

                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-600">Сборщик</span>
                    
                    <button onclick="logout()" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <section class="px-4 py-6">
        <div class="container mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Панель сборщика</h1>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <p class="text-sm text-gray-500">К сборке</p>
                    <p id="pending-count" class="text-2xl font-bold text-warm-500 mt-1">0</p>
                </div>
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <p class="text-sm text-gray-500">Собрано сегодня</p>
                    <p id="completed-count" class="text-2xl font-bold text-green-500 mt-1">0</p>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="flex space-x-2 mb-4">
                <button onclick="showTab('pending')" id="tab-pending" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-warm-500 text-white">
                    К сборке
                </button>
                <button onclick="showTab('completed')" id="tab-completed" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow">
                    Собрано
                </button>
            </div>
            
            <!-- Pending Orders -->
            <div id="orders-pending" class="space-y-3"></div>
            
            <!-- Completed Orders -->
            <div id="orders-completed" class="space-y-3 hidden"></div>
            
            <div id="loading" class="text-center py-8">
                <div class="inline-block w-8 h-8 border-2 border-warm-200 border-t-warm-500 rounded-full animate-spin"></div>
            </div>
        </div>
    </section>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="closeOrderModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Заказ #<span id="modal-order-id"></span></h3>
                <button onclick="closeOrderModal()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div id="modal-order-items" class="space-y-2 mb-4"></div>
            
            <div class="border-t border-gray-100 pt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600">Итого:</span>
                    <span id="modal-order-total" class="text-xl font-bold text-warm-600">0 ₸</span>
                </div>
                
                <button onclick="markAsAssembled()" class="w-full btn-success text-white py-3 rounded-xl font-medium">
                    ✓ Отметить как собранный
                </button>
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
            <a href="/picker" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Сборка</span>
            </a>
            <a href="/chat" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs mt-1">Чат</span>
            </a>
            <a href="/profile" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Профиль</span>
            </a>
        </div>
    </nav>

    <script>
        let currentOrderId = null;
        let allOrders = [];
        
        function showTab(tab) {
            ['pending', 'completed'].forEach(t => {
                const tabBtn = document.getElementById(`tab-${t}`);
                const content = document.getElementById(`orders-${t}`);
                if (t === tab) {
                    tabBtn.className = 'flex-1 py-3 rounded-xl font-medium transition-colors bg-warm-500 text-white';
                    content.classList.remove('hidden');
                } else {
                    tabBtn.className = 'flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow';
                    content.classList.add('hidden');
                }
            });
        }

        async function loadOrders() {
            try {
                const response = await fetch('/api/picker/orders');
                const data = await response.json();
                
                allOrders = data.pending || [];
                
                document.getElementById('pending-count').textContent = data.pending?.length || 0;
                document.getElementById('completed-count').textContent = data.completed_today || 0;
                
                renderOrders('orders-pending', data.pending || [], 'pending');
                renderOrders('orders-completed', data.completed || [], 'completed');
                
                document.getElementById('loading').classList.add('hidden');
            } catch (error) {
                console.error('Error loading orders:', error);
                document.getElementById('loading').innerHTML = '<p class="text-gray-500">Ошибка загрузки</p>';
            }
        }
        
        function calculateOrderTotal(order) {
            let items = [];
            try {
                items = typeof order.items === 'string' ? JSON.parse(order.items) : (order.items || []);
            } catch (e) {
                items = [];
            }
            
            return items.reduce((sum, i) => {
                if (i.is_weighted) {
                    return sum + Math.round((i.price || 0) * (i.quantity || 1));
                } else {
                    return sum + ((i.price || 0) * (i.quantity || 1));
                }
            }, 0);
        }

        function renderOrders(containerId, orders, type) {
            const container = document.getElementById(containerId);
            
            if (!orders || orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-warm-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <p class="text-gray-500">${type === 'pending' ? 'Нет заказов для сборки' : 'Нет собранных заказов'}</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = orders.map(order => {
                const total = order.total || calculateOrderTotal(order);
                const items = typeof order.items === 'string' ? JSON.parse(order.items) : (order.items || []);
                const itemsCount = items.reduce((sum, i) => sum + (i.is_weighted ? 1 : (i.quantity || 1)), 0);
                
                return `
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Заказ #${order.id}</h3>
                            <p class="text-sm text-gray-500">${formatDate(order.created_at)}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${type === 'completed' ? 'bg-green-100 text-green-600' : 'bg-warm-100 text-warm-600'}">
                                ${total.toLocaleString('ru-RU')} ₸
                            </span>
                            <p class="text-xs text-gray-400 mt-1">${itemsCount} позиций</p>
                        </div>
                    </div>
                    
                    <!-- Preview items -->
                    <div class="text-sm text-gray-600 mb-3 bg-gray-50 rounded-xl p-3">
                        ${items.slice(0, 3).map(item => `
                            <div class="flex justify-between py-1">
                                <span class="truncate flex-1">${item.name || 'Товар'}</span>
                                <span class="text-gray-500 ml-2">${item.is_weighted ? (item.quantity + ' кг') : ('x' + item.quantity)}</span>
                            </div>
                        `).join('')}
                        ${items.length > 3 ? `<p class="text-xs text-gray-400 pt-1">и ещё ${items.length - 3} товаров...</p>` : ''}
                    </div>
                    
                    ${type === 'pending' ? `
                        <button onclick="showOrderDetails(${order.id})" class="w-full py-2.5 btn-primary text-white rounded-xl font-medium text-sm">
                            Собрать заказ
                        </button>
                    ` : `
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Собран ${formatDate(order.assembled_at)}
                        </div>
                    `}
                </div>
            `}).join('');
        }
        
        function showOrderDetails(orderId) {
            const order = allOrders.find(o => o.id === orderId);
            if (!order) return;
            
            currentOrderId = orderId;
            const items = typeof order.items === 'string' ? JSON.parse(order.items) : (order.items || []);
            const total = order.total || calculateOrderTotal(order);
            
            document.getElementById('modal-order-id').textContent = orderId;
            document.getElementById('modal-order-total').textContent = total.toLocaleString('ru-RU') + ' ₸';
            
            document.getElementById('modal-order-items').innerHTML = items.map(item => `
                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-warm-50 to-warm-100 flex items-center justify-center overflow-hidden mr-3">
                            ${item.image_url 
                                ? `<img src="${item.image_url}" class="w-full h-full object-cover">` 
                                : `<svg class="w-6 h-6 text-warm-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                   </svg>`
                            }
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${item.name || 'Товар'}</p>
                            <p class="text-sm text-gray-500">${item.price} ₸ ${item.is_weighted ? '/ кг' : ''}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-semibold text-gray-900">${item.is_weighted ? item.quantity + ' кг' : 'x' + item.quantity}</span>
                        <p class="text-sm text-warm-600">${Math.round((item.price || 0) * (item.quantity || 1))} ₸</p>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('orderModal').classList.remove('hidden');
        }
        
        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            currentOrderId = null;
        }
        
        async function markAsAssembled() {
            if (!currentOrderId) return;
            
            try {
                const response = await fetch(`/api/picker/assemble/${currentOrderId}`, { 
                    method: 'POST' 
                });
                
                if (response.ok) {
                    showToast('Заказ собран!');
                    closeOrderModal();
                    loadOrders();
                } else {
                    const data = await response.json();
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg z-50 text-sm font-medium';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 2000);
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.reload());
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
        });
    </script>
</body>
</html>