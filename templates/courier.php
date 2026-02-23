<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚚 Курьер - Delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
<body class="bg-gradient-to-br from-orange-50 via-white to-yellow-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">🚚</span>
                    </div>
                    <a href="/" class="text-xl md:text-2xl font-bold bg-gradient-to-r from-orange-600 to-yellow-600 bg-clip-text text-transparent">
                        Delivery
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/courier" class="text-orange-600 font-semibold border-b-2 border-orange-600 pb-1">
                        🚚 Курьер
                    </a>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Курьер'); ?>!</span>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">Выход</button>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl p-2">☰</button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200 mt-4 rounded-xl">
                <div class="px-4 py-4 space-y-2">
                    <a href="/courier" class="block px-4 py-3 text-orange-600 font-semibold bg-orange-50 rounded-lg">🚚 Курьер</a>
                    <hr class="my-2">
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-600 mb-3">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Курьер'); ?>!</p>
                        <button onclick="logout()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Выход</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 md:py-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-4xl font-bold text-gray-800 mb-2 md:mb-4 flex items-center">
                    <span class="mr-2 md:mr-4">🚚</span> Панель курьера
                </h1>
                <p class="text-sm md:text-base text-gray-600">Управляйте заказами и отслеживайте доставку</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
                <!-- Available Orders List -->
                <div class="lg:col-span-1">
                    <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-lg">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">📋 Доступные заказы</h2>
                        <div id="ordersList" class="space-y-4"></div>
                        <div id="noOrders" class="text-center py-8 text-gray-500 hidden">Нет доступных заказов</div>
                    </div>
                </div>

                <!-- Current Orders List -->
                <div class="lg:col-span-1">
                    <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-lg">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">🚚 Мои заказы</h2>
                        <div id="currentOrdersList" class="space-y-4"></div>
                        <div id="noCurrentOrders" class="text-center py-8 text-gray-500 hidden">У вас нет текущих заказов</div>
                    </div>
                </div>

                <!-- Map -->
                <div class="lg:col-span-1">
                    <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-lg">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">🗺️ Карта</h2>
                            <div class="flex space-x-2">
                                <button id="toStoreBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg transition-colors duration-200 text-sm">📍 Магазин</button>
                                <button id="toClientBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 md:px-4 py-2 rounded-lg transition-colors duration-200 text-sm">👤 Клиент</button>
                            </div>
                        </div>
                        <div id="map" class="w-full h-64 md:h-96 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">📦 Детали заказа</h2>
                <button onclick="closeOrderModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="modalOrderInfo" class="mb-6"></div>
            <div class="flex flex-wrap gap-3">
                <button id="modalShowMapBtn" onclick="showCurrentOrderOnMap()" class="bg-green-500 hover:bg-green-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl transition-colors duration-200 text-sm md:text-base">👤 Показать на карте</button>
                <button id="modalOnTheWayBtn" onclick="updateOrderStatus('В_ПУТИ')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl transition-colors duration-200 hidden text-sm md:text-base">🚴 В пути</button>
                <button id="modalDeliveredBtn" onclick="updateOrderStatus('ДОСТАВЛЕН')" class="bg-green-500 hover:bg-green-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl transition-colors duration-200 hidden text-sm md:text-base">✅ Доставлено</button>
                <button id="modalCancelBtn" onclick="cancelOrder()" class="bg-red-500 hover:bg-red-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl transition-colors duration-200 hidden text-sm md:text-base">❌ Отменить</button>
            </div>
        </div>
    </div>

    <script>
        let map;
        let currentOrder = null;
        let currentOrderAddress = null;
        let courierMarker = null;
        let storeMarker = null;
        let clientMarker = null;
        let watchId = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            loadOrders();
            loadCurrentOrders();
            startLocationTracking();
            setInterval(loadOrders, 30000);
            setInterval(loadCurrentOrders, 30000);
            
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
        });

        // Initialize map - Kentau, Turkestan region coordinates
        function initMap() {
            // Координаты магазина в Кентау
            const storeLat = 43.518703;
            const storeLng = 68.505423;
            
            map = L.map('map').setView([storeLat, storeLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            storeMarker = L.marker([storeLat, storeLng], { title: 'Магазин' }).addTo(map).bindPopup('🏪 Магазин');
        }

        // Start location tracking
        function startLocationTracking() {
            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    updateCourierLocation(lat, lng);
                    fetch('/api/courier/location', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ lat: lat, lng: lng })
                    });
                }, function(error) {
                    console.error('Error getting location:', error);
                }, { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 });
            }
        }

        // Update courier location on map
        function updateCourierLocation(lat, lng) {
            if (courierMarker) {
                courierMarker.setLatLng([lat, lng]);
            } else {
                courierMarker = L.marker([lat, lng], { title: 'Вы' }).addTo(map).bindPopup('🚴 Вы');
            }
        }

        // Load available orders
        async function loadOrders() {
            try {
                const response = await fetch('/api/courier/orders');
                const data = await response.json();
                displayOrders(data.available || []);
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        }

        // Load current orders
        async function loadCurrentOrders() {
            try {
                const response = await fetch('/api/courier/orders');
                const data = await response.json();
                displayCurrentOrders(data.current || []);
            } catch (error) {
                console.error('Error loading current orders:', error);
            }
        }

        // Display orders
        function displayOrders(orders) {
            const container = document.getElementById('ordersList');
            const noOrders = document.getElementById('noOrders');
            if (orders.length === 0) {
                container.innerHTML = '';
                noOrders.classList.remove('hidden');
                return;
            }
            noOrders.classList.add('hidden');
            container.innerHTML = orders.map(order => `
                <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800 text-sm md:text-base">Заказ #${order.id}</h3>
                        <span class="text-green-600 font-bold text-sm">${order.total_price} ₸</span>
                    </div>
                    <p class="text-gray-600 text-xs md:text-sm mb-2">${order.address}</p>
                    <p class="text-gray-500 text-xs">${order.items.length} товаров</p>
                    <div class="flex space-x-2 mt-3">
                        <button class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors text-sm"
                                onclick="requestOrder(${order.id})">Запросить</button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg transition-colors text-sm"
                                onclick="showClientOnMap('${order.address.replace(/'/g, "\\'")}')"
                                title="Показать клиента на карте">👤</button>
                    </div>
                </div>
            `).join('');
        }

        // Display current orders
        function displayCurrentOrders(orders) {
            const container = document.getElementById('currentOrdersList');
            const noCurrentOrders = document.getElementById('noCurrentOrders');
            if (orders.length === 0) {
                container.innerHTML = '';
                noCurrentOrders.classList.remove('hidden');
                return;
            }
            noCurrentOrders.classList.add('hidden');
            container.innerHTML = orders.map(order => `
                <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer" onclick="openOrderModal(${order.id})">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800 text-sm md:text-base">Заказ #${order.id}</h3>
                        <span class="text-green-600 font-bold text-sm">${order.total_price} ₸</span>
                    </div>
                    <p class="text-gray-600 text-xs md:text-sm mb-2">${order.address}</p>
                    <p class="text-gray-500 text-xs">${order.items.length} товаров</p>
                    <div class="mt-2">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">${getStatusText(order.status)}</span>
                    </div>
                </div>
            `).join('');
        }

        // Status text mapping
        function getStatusText(status) {
            const statuses = {
                'СОЗДАН': 'Заказ создан',
                'СБОРКА': 'Заказ собирается',
                'ОЖИДАНИЕ_КУРЬЕРА': 'Ожидание курьера',
                'В_ПУТИ': 'Заказ в пути',
                'ДОСТАВЛЕН': 'Заказ доставлен',
                'ОТМЕНЕН': 'Заказ отменен'
            };
            return statuses[status] || status;
        }

        // Request order
        async function requestOrder(orderId) {
            try {
                const response = await fetch(`/api/orders/${orderId}/request`, { method: 'POST' });
                if (response.ok) {
                    alert('✅ Запрос на заказ отправлен администратору!');
                    loadOrders();
                } else {
                    const errorData = await response.json();
                    alert('❌ Ошибка: ' + (errorData.error || 'Не удалось отправить запрос'));
                }
            } catch (error) {
                console.error('Error requesting order:', error);
                alert('❌ Ошибка сети');
            }
        }

        // Open order modal
        async function openOrderModal(orderId) {
            try {
                const response = await fetch('/api/courier/orders');
                const data = await response.json();
                const orders = data.current || [];
                const order = orders.find(o => o.id == orderId);
                if (order) {
                    currentOrder = order;
                    currentOrderAddress = order.address;
                    
                    // Automatically show client on map
                    showClientOnMap(order.address);
                    const info = document.getElementById('modalOrderInfo');
                    const itemsHtml = order.items.map(item => `<li class="text-sm">${item.name} x ${item.quantity}</li>`).join('');
                    info.innerHTML = `
                        <div class="space-y-3">
                            <h3 class="font-semibold text-gray-800 text-lg">Заказ #${order.id}</h3>
                            <p class="text-gray-600 text-sm"><strong>Адрес:</strong> ${order.address}</p>
                            <p class="text-gray-600 text-sm"><strong>Сумма:</strong> ${order.total_price} ₸</p>
                            <div>
                                <h4 class="font-semibold text-gray-800 text-sm mb-2">Товары:</h4>
                                <ul class="text-gray-600 space-y-1">${itemsHtml}</ul>
                            </div>
                        </div>
                    `;
                    
                    const onTheWayBtn = document.getElementById('modalOnTheWayBtn');
                    const deliveredBtn = document.getElementById('modalDeliveredBtn');
                    const cancelBtn = document.getElementById('modalCancelBtn');
                    
                    onTheWayBtn.classList.add('hidden');
                    deliveredBtn.classList.add('hidden');
                    cancelBtn.classList.add('hidden');
                    
                    if (order.status === 'ОЖИДАНИЕ_КУРЬЕРА') {
                        onTheWayBtn.classList.remove('hidden');
                        cancelBtn.classList.remove('hidden');
                    } else if (order.status === 'В_ПУТИ') {
                        deliveredBtn.classList.remove('hidden');
                        cancelBtn.classList.remove('hidden');
                    }
                    
                    document.getElementById('orderModal').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading order:', error);
            }
        }

        // Close order modal
        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            currentOrder = null;
        }

        // Update order status
        async function updateOrderStatus(status) {
            if (!currentOrder) return;
            try {
                const response = await fetch(`/api/orders/${currentOrder.id}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: status })
                });
                if (response.ok) {
                    alert(`✅ Статус обновлен!`);
                    closeOrderModal();
                    loadCurrentOrders();
                } else {
                    alert('❌ Ошибка обновления статуса');
                }
            } catch (error) {
                console.error('Error updating status:', error);
            }
        }

        // Cancel order
        async function cancelOrder() {
            if (!currentOrder || !confirm('Вы уверены, что хотите отменить этот заказ?')) return;
            try {
                const response = await fetch(`/api/orders/${currentOrder.id}/cancel`, { method: 'POST' });
                if (response.ok) {
                    alert('✅ Заказ отменен');
                    closeOrderModal();
                    loadCurrentOrders();
                } else {
                    alert('❌ Ошибка при отмене заказа');
                }
            } catch (error) {
                console.error('Error canceling order:', error);
            }
        }

        // Show current order on map
        async function showCurrentOrderOnMap() {
            if (!currentOrderAddress) {
                alert('❌ Нет адреса для показа');
                return;
            }
            await showClientOnMap(currentOrderAddress);
        }

        // Show client on map by geocoding address
        async function showClientOnMap(address) {
            if (!address) return;
            
            try {
                // Use local proxy for geocoding
                const response = await fetch(`/api/geocode/search?q=${encodeURIComponent(address)}`);
                const results = await response.json();
                
                console.log('Geocoding result:', results);

                // Check if results is an array and has items
                if (Array.isArray(results) && results.length > 0) {
                    const lat = parseFloat(results[0].lat);
                    const lng = parseFloat(results[0].lon);

                    if (isNaN(lat) || isNaN(lng)) {
                        alert('❌ Не удалось определить координаты адреса');
                        return;
                    }

                    // Remove old client marker
                    if (clientMarker) {
                        map.removeLayer(clientMarker);
                    }

                    // Add new client marker
                    clientMarker = L.marker([lat, lng], { title: 'Клиент' }).addTo(map).bindPopup(`👤 Клиент<br>${address}`);
                    map.setView([lat, lng], 15);
                    clientMarker.openPopup();
                } else {
                    console.error('Geocoding returned no results or error:', results);
                    alert('❌ Не удалось найти адрес на карте. Попробуйте более точный адрес.');
                }
            } catch (error) {
                console.error('Error geocoding address:', error);
                alert('❌ Ошибка при поиске адреса');
            }
        }

        // Map buttons
        document.getElementById('toStoreBtn').addEventListener('click', () => {
            if (storeMarker) map.setView(storeMarker.getLatLng(), 15);
        });

        document.getElementById('toClientBtn').addEventListener('click', async () => {
            if (clientMarker) {
                map.setView(clientMarker.getLatLng(), 15);
                clientMarker.openPopup();
            } else if (currentOrderAddress) {
                await showClientOnMap(currentOrderAddress);
            } else {
                alert('❌ Сначала выберите заказ');
            }
        });

        // Logout function
        function logout() {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.href = '/');
        }
    </script>
</body>
</html>