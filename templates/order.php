<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📦 Оформление заказа - Kazyna Market</title>
    <?php echo $csrfMeta ?? ''; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
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
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in { animation: fadeIn 0.5s ease-in; }
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
                    <a href="/" class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Kazyna Market
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛍️ Каталог</a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">🛒 Корзина</a>
                    <a href="/orders" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">📦 Заказы</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
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
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="block px-4 py-3 text-orange-700 hover:bg-orange-50 rounded-lg transition-colors">🚚 Курьер</a>
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
                    <span class="mr-2 md:mr-4">📦</span> Оформление заказа
                </h1>
                <p class="text-sm md:text-base text-gray-600">Пожалуйста, проверьте ваш заказ и укажите адрес доставки</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-4 md:gap-8">
                <!-- Order Items -->
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-lg">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">🛒 Товары в заказе</h2>

                    <div class="space-y-3 md:space-y-4">
                        <?php
                        $total = 0;
                        foreach ($cart as $item):
                            $itemTotal = $item['price'] * $item['quantity'];
                            $total += $itemTotal;
                        ?>
                            <div class="flex items-center space-x-3 md:space-x-4 p-3 md:p-4 bg-white/50 rounded-xl md:rounded-2xl">
                                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-100 to-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-lg md:text-xl"><?php echo mb_substr($item['name'], 0, 1, 'UTF-8'); ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-xs md:text-sm text-gray-600"><?php echo htmlspecialchars($item['price']); ?> ₸ × <?php echo htmlspecialchars($item['quantity']); ?></p>
                                </div>
                                <div class="text-lg md:text-xl font-bold text-green-600">
                                    <?php echo htmlspecialchars($itemTotal); ?> ₸
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-4 md:mt-6 pt-4 md:pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center text-base md:text-lg">
                            <span class="font-semibold">Итого товаров:</span>
                            <span class="font-bold text-blue-600"><?php echo htmlspecialchars($total); ?> ₸</span>
                        </div>
                    </div>
                </div>

                <!-- Order Form -->
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-lg">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">🚚 Детали доставки</h2>

                    <form id="orderForm" class="space-y-4 md:space-y-6">
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">📍 Адрес доставки</label>
                            <textarea id="address" name="address" required
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none text-base"
                                      rows="2"
                                      placeholder="Улица и номер дома..."></textarea>
                            <div class="mt-3">
                                <label for="apartment" class="block text-sm font-semibold text-gray-700 mb-2">🏠 Номер квартиры/офиса (необязательно)</label>
                                <input type="text" id="apartment" name="apartment"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-base"
                                       placeholder="Например: 42">
                            </div>
                            <div class="mt-4">
                                <p class="text-xs md:text-sm text-gray-600 mb-2">Или выберите адрес на карте:</p>
                                <div id="map" class="w-full h-48 md:h-64 rounded-xl border border-gray-200"></div>
                            </div>
                        </div>

                        <!-- Order Total -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl md:rounded-2xl p-4 md:p-6">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-base md:text-lg font-semibold">Итого товаров:</span>
                                <span class="text-base md:text-lg font-bold"><?php echo htmlspecialchars($total); ?> ₸</span>
                            </div>
                            <hr class="border-gray-300 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl md:text-2xl font-bold text-gray-800">К оплате:</span>
                                <span class="text-2xl md:text-3xl font-bold text-green-600" id="total-price"><?php echo htmlspecialchars($total); ?> ₸</span>
                            </div>
                        </div>

                        <button type="submit" id="orderBtn"
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 md:py-4 px-4 rounded-xl md:rounded-2xl font-bold text-base md:text-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                            <span class="flex items-center justify-center">
                                <span id="btnText">✅ Подтвердить заказ</span>
                                <span id="btnSpinner" class="hidden ml-2 w-5 md:w-6 h-5 md:h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            </span>
                        </button>
                    </form>
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

        // Logout function
        function logout() {
            fetch('/api/auth/logout', { method: 'POST' })
                .then(() => location.reload());
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

        // Order form handler
        document.getElementById('orderForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('orderBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btnText.textContent = 'Оформление...';
            btnSpinner.classList.remove('hidden');

            const formData = new FormData(e.target);
            let fullAddress = formData.get('address');
            const apartment = formData.get('apartment');
            if (apartment) {
                fullAddress += ', кв. ' + apartment;
            }
            const data = {
                items: <?php echo json_encode($cart); ?>,
                address: fullAddress
            };

            try {
                const headers = { 'Content-Type': 'application/json' };
                const csrfToken = getCsrfToken();
                if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(`🎉 Заказ #${result.order_id} успешно оформлен!`, 'success');
                    localStorage.removeItem('cart');
                    setTimeout(() => window.location.href = '/orders', 2000);
                } else {
                    showNotification('❌ ' + (result.error || 'Ошибка оформления заказа'), 'error');
                }
            } catch (error) {
                showNotification('❌ Ошибка сети', 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = '✅ Подтвердить заказ';
                btnSpinner.classList.add('hidden');
            }
        });

        // Map functionality - Kentau store coordinates
        let map, marker;

        function initMap() {
            // Координаты магазина в Кентау
            const storeLat = 43.518703;
            const storeLng = 68.505423;
            
            map = L.map('map').setView([storeLat, storeLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            map.on('click', function(e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
        }

        async function reverseGeocode(lat, lng) {
            try {
                const response = await fetch(`/api/geocode/reverse?lat=${lat}&lon=${lng}`);
                const data = await response.json();
                if (data && data.address) {
                    const addr = data.address;
                    let street = addr.road || addr.pedestrian || addr.path || addr.residential || '';
                    let house = addr.house_number || '';
                    const address = [street, house].filter(Boolean).join(' ');
                    if (address) {
                        document.getElementById('address').value = address;
                        showNotification('📍 Адрес выбран на карте', 'success');
                    }
                }
            } catch (error) {
                showNotification('❌ Ошибка при определении адреса', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>