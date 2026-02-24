<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Курьер - Delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
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
        
        #map { z-index: 1; }
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
                    <button onclick="toggleNotifications()" class="relative p-2 rounded-full hover:bg-warm-50 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notification-badge" class="hidden absolute top-1 right-1 w-2 h-2 bg-warm-500 rounded-full"></span>
                    </button>
                    
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
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Панель курьера</h1>
            
            <!-- Map Section -->
            <div id="map-section" class="mb-6 hidden">
                <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                    <div class="p-3 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900">📍 Адрес доставки</h3>
                        <button onclick="hideMap()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div id="map" class="w-full h-48"></div>
                    <div class="p-3 bg-warm-50">
                        <p id="map-address" class="text-sm text-gray-700 font-medium"></p>
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <p class="text-sm text-gray-500">Доступные заказы</p>
                    <p id="available-count" class="text-2xl font-bold text-warm-500 mt-1">0</p>
                </div>
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <p class="text-sm text-gray-500">Мои заказы</p>
                    <p id="current-count" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="flex space-x-2 mb-4">
                <button onclick="showTab('available')" id="tab-available" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-warm-500 text-white">
                    Доступные
                </button>
                <button onclick="showTab('current')" id="tab-current" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow">
                    Мои
                </button>
                <button onclick="showTab('history')" id="tab-history" class="flex-1 py-3 rounded-xl font-medium transition-colors bg-white text-gray-600 card-shadow">
                    История
                </button>
            </div>
            
            <!-- Available Orders -->
            <div id="orders-available" class="space-y-3"></div>
            
            <!-- Current Orders -->
            <div id="orders-current" class="space-y-3 hidden"></div>
            
            <!-- History Orders -->
            <div id="orders-history" class="space-y-3 hidden"></div>
            
            <div id="loading" class="text-center py-8">
                <div class="inline-block w-8 h-8 border-2 border-warm-200 border-t-warm-500 rounded-full animate-spin"></div>
            </div>
        </div>
    </section>

    <!-- Notifications Modal -->
    <div id="notificationsModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="closeNotifications()"></div>
        <div class="absolute top-0 right-0 bottom-0 w-full max-w-sm bg-white shadow-xl transform transition-transform duration-300 translate-x-full" id="notificationsPanel">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Уведомления</h3>
                <button onclick="closeNotifications()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="notificationsList" class="overflow-y-auto h-full pb-20">
                <div class="p-4 text-center text-gray-500">Загрузка...</div>
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
            <a href="/courier" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Заказы</span>
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
        // Map variables
        let map = null;
        let marker = null;
        let routeLine = null;
        const KENTAU_LAT = 43.5187;
        const KENTAU_LNG = 68.5054;
        
        function initMap() {
            if (map) return;
            
            map = L.map('map').setView([KENTAU_LAT, KENTAU_LNG], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            
            // Add store marker
            L.marker([KENTAU_LAT, KENTAU_LNG])
                .addTo(map)
                .bindPopup('📦 Магазин Delivery');
        }
        
        function showOrderOnMap(address) {
            initMap();
            
            document.getElementById('map-section').classList.remove('hidden');
            document.getElementById('map-address').textContent = address;
            
            // Geocode address
            geocodeAddress(address);
            
            // Scroll to map
            document.getElementById('map-section').scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideMap() {
            document.getElementById('map-section').classList.add('hidden');
        }
        
        async function geocodeAddress(address) {
            console.log('Geocoding address:', address);
            
            try {
                // Пробуем разные варианты поиска
                const searchQueries = [
                    `${address}, Кентау, Туркестанская область, Казахстан`,
                    `${address}, Кентау, Казахстан`,
                    `${address}, Туркестанская область, Казахстан`,
                    `Кентау, ${address}, Казахстан`,
                    address
                ];
                
                for (const query of searchQueries) {
                    console.log('Trying query:', query);
                    const response = await fetch(`/api/geocode/search?q=${encodeURIComponent(query)}`);
                    const results = await response.json();
                    
                    console.log('Results:', results);
                    
                    if (results && results.length > 0) {
                        const lat = parseFloat(results[0].lat);
                        const lon = parseFloat(results[0].lon);
                        
                        console.log('Found coordinates:', lat, lon);
                        
                        // Remove old delivery marker and route line
                        if (marker) map.removeLayer(marker);
                        if (routeLine) map.removeLayer(routeLine);
                        
                        // Update map view - центрируем между магазином и адресом
                        const centerLat = (KENTAU_LAT + lat) / 2;
                        const centerLon = (KENTAU_LNG + lon) / 2;
                        map.setView([centerLat, centerLon], 13);
                        
                        // Add delivery marker
                        marker = L.marker([lat, lon])
                            .addTo(map)
                            .bindPopup(`📍 Адрес доставки<br><b>${address}</b>`)
                            .openPopup();
                        
                        // Draw line between store and delivery
                        routeLine = L.polyline([[KENTAU_LAT, KENTAU_LNG], [lat, lon]], {
                            color: '#FF7A3D',
                            weight: 3,
                            opacity: 0.7,
                            dashArray: '10, 10'
                        }).addTo(map);
                        
                        return; // Успешно нашли адрес
                    }
                }
                
                // Если не нашли - показываем адрес и уведомление
                console.log('Address not found in Kentau area');
                showToast('Адрес не найден на карте. Показываем центр города.');
                map.setView([KENTAU_LAT, KENTAU_LNG], 15);
                
            } catch (error) {
                console.error('Geocoding error:', error);
                showToast('Ошибка поиска адреса');
                map.setView([KENTAU_LAT, KENTAU_LNG], 15);
            }
        }

        function showTab(tab) {
            ['available', 'current', 'history'].forEach(t => {
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
            
            // Hide map when switching tabs
            hideMap();
        }

        async function loadOrders() {
            try {
                const [ordersRes, historyRes] = await Promise.all([
                    fetch('/api/courier/orders'),
                    fetch('/api/courier/history')
                ]);
                
                const orders = await ordersRes.json();
                const history = await historyRes.json();
                
                document.getElementById('available-count').textContent = orders.available?.length || 0;
                document.getElementById('current-count').textContent = orders.current?.length || 0;
                
                renderOrders('orders-available', orders.available || [], 'available');
                renderOrders('orders-current', orders.current || [], 'current');
                renderOrders('orders-history', history || [], 'history');
                
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
                        <p class="text-gray-500">${type === 'available' ? 'Нет доступных заказов' : type === 'current' ? 'Нет активных заказов' : 'История пуста'}</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = orders.map(order => {
                const total = order.total || calculateOrderTotal(order);
                const address = order.address || 'Адрес не указан';
                
                return `
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Заказ #${order.id}</h3>
                            <p class="text-sm text-gray-500">${formatDate(order.created_at)}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${type === 'history' ? 'bg-gray-100 text-gray-600' : 'bg-warm-100 text-warm-600'}">
                            ${total.toLocaleString('ru-RU')} ₸
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-3">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>${address}</span>
                        </div>
                    </div>
                    
                    ${type === 'available' ? `
                        <button onclick="takeOrder(${order.id})" class="w-full py-2.5 btn-primary text-white rounded-xl font-medium text-sm">
                            Взять заказ
                        </button>
                    ` : type === 'current' ? `
                        <button onclick="showOrderOnMap('${address.replace(/'/g, "\\'")}')" class="w-full py-2 mb-2 bg-warm-50 hover:bg-warm-100 text-warm-600 font-medium rounded-xl transition-colors text-sm flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Показать на карте
                        </button>
                        <div class="flex space-x-2 mb-2">
                            <button onclick="updateStatus(${order.id}, 'В_ПУТИ')" class="flex-1 py-2.5 bg-warm-50 hover:bg-warm-100 text-warm-600 font-medium rounded-xl transition-colors text-sm">
                                В пути
                            </button>
                            <button onclick="updateStatus(${order.id}, 'ДОСТАВЛЕН')" class="flex-1 py-2.5 btn-primary text-white rounded-xl font-medium text-sm">
                                Доставлен
                            </button>
                        </div>
                        <button onclick="cancelOrder(${order.id})" class="w-full py-2 text-gray-400 hover:text-red-500 text-sm transition-colors">
                            Отменить заказ
                        </button>
                    ` : ''}
                </div>
            `}).join('');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        }

        async function takeOrder(orderId) {
            try {
                const response = await fetch(`/api/courier/take/${orderId}`, { method: 'POST' });
                if (response.ok) {
                    showToast('Заказ принят!');
                    loadOrders();
                } else {
                    const data = await response.json();
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }

        async function updateStatus(orderId, status) {
            try {
                const response = await fetch(`/api/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });
                if (response.ok) {
                    showToast(status === 'ДОСТАВЛЕН' ? 'Заказ доставлен!' : 'Статус обновлен');
                    loadOrders();
                    hideMap();
                } else {
                    const data = await response.json();
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }

        async function cancelOrder(orderId) {
            if (!confirm('Вы уверены, что хотите отменить заказ?')) {
                return;
            }
            try {
                const response = await fetch(`/api/courier/cancel/${orderId}`, { method: 'POST' });
                if (response.ok) {
                    showToast('Заказ отменен');
                    loadOrders();
                    hideMap();
                } else {
                    const data = await response.json();
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg z-50 text-sm font-medium';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 2000);
        }

        let notificationsOpen = false;
        function toggleNotifications() {
            notificationsOpen ? closeNotifications() : openNotifications();
        }
        function openNotifications() {
            document.getElementById('notificationsModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('notificationsPanel').classList.remove('translate-x-full'), 10);
            notificationsOpen = true;
            loadNotifications();
        }
        function closeNotifications() {
            document.getElementById('notificationsPanel').classList.add('translate-x-full');
            setTimeout(() => document.getElementById('notificationsModal').classList.add('hidden'), 300);
            notificationsOpen = false;
        }

        async function loadNotifications() {
            const list = document.getElementById('notificationsList');
            try {
                const response = await fetch('/api/notifications');
                if (response.ok) {
                    const notifications = await response.json();
                    if (notifications.length === 0) {
                        list.innerHTML = '<div class="p-8 text-center text-gray-500">Нет уведомлений</div>';
                        return;
                    }
                    list.innerHTML = notifications.map(n => `
                        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer" onclick="handleNotification(${n.id})">
                            <div class="font-medium text-gray-900 text-sm">${n.title}</div>
                            <div class="text-gray-500 text-sm mt-1">${n.message}</div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                list.innerHTML = '<div class="p-4 text-center text-gray-500">Ошибка загрузки</div>';
            }
        }

        async function handleNotification(id) {
            await fetch(`/api/notifications/${id}/read`, { method: 'POST' });
            closeNotifications();
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