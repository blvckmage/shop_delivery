<!DOCTYPE html>
<html lang="ru">
<head>
    <?php include __DIR__ . '/pwa-head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Оформление заказа - Delivery</title>
    <?php echo $csrfMeta ?? ''; ?>
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
        
        .btn-primary:disabled {
            opacity: 0.5;
            transform: none;
            box-shadow: none;
        }
        
        .bottom-nav {
            padding-bottom: env(safe-area-inset-bottom, 16px);
        }
        
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] { -moz-appearance: textfield; }
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
                    <a href="/orders" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Заказы</a>
                    <a href="/chat" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Чат</a>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="hidden md:block">
                        <?php if ($isLoggedIn): ?>
                            <div class="flex items-center space-x-3">
                                <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                                    <a href="/admin" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Панель администратора</a>
                                <?php endif; ?>
                                <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                                    <a href="/courier" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Курьер</a>
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

    <!-- Main Content -->
    <main class="px-4 py-6">
        <div class="container mx-auto max-w-4xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Оформление заказа</h1>
            
            <div class="grid lg:grid-cols-2 gap-4 md:gap-6">
                <!-- Order Items -->
                <div class="bg-white rounded-2xl p-4 md:p-6 card-shadow">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Товары в заказе</h2>

                    <div class="space-y-3">
                        <?php
                        $total = 0;
                        foreach ($cart as $item):
                            $isWeighted = !empty($item['is_weighted']);
                            $quantity = floatval($item['quantity'] ?? 1);
                            $pricePerKg = floatval($item['price']);
                            
                            if ($isWeighted) {
                                $itemTotal = round($pricePerKg * $quantity);
                            } else {
                                $itemTotal = $pricePerKg * intval($quantity);
                            }
                            $total += $itemTotal;
                        ?>
                            <div class="flex items-center gap-3 p-3 bg-warm-50 rounded-xl">
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-warm-100 to-warm-200 flex-shrink-0 overflow-hidden">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-warm-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 text-sm truncate"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <?php if ($isWeighted): ?>
                                        <p class="text-xs text-warm-600 font-medium"><?php echo $quantity; ?> кг × <?php echo number_format($pricePerKg, 0, '', ' '); ?> ₸/кг</p>
                                    <?php else: ?>
                                        <p class="text-xs text-gray-500"><?php echo intval($quantity); ?> шт × <?php echo number_format($pricePerKg, 0, '', ' '); ?> ₸</p>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900"><?php echo number_format($itemTotal, 0, '', ' '); ?> ₸</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Итого:</span>
                            <span class="text-xl font-bold text-warm-600"><?php echo number_format($total, 0, '', ' '); ?> ₸</span>
                        </div>
                    </div>
                </div>

                <!-- Order Form -->
                <div class="bg-white rounded-2xl p-4 md:p-6 card-shadow">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Детали доставки</h2>

                    <form id="orderForm" class="space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Адрес доставки</label>
                            <div class="relative">
                                <textarea id="address" name="address" required
                                          class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-warm-500 focus:border-transparent transition-all resize-none"
                                          rows="2"
                                          placeholder="Улица и номер дома..."></textarea>
                                <button type="button" onclick="detectLocation()" 
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-warm-500 transition-colors"
                                        title="Определить моё местоположение">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Нажмите на иконку справа для автоматического определения адреса</p>
                        </div>
                        
                        <div>
                            <label for="apartment" class="block text-sm font-medium text-gray-700 mb-2">Номер квартиры/офиса <span class="text-gray-400">(необязательно)</span></label>
                            <input type="text" id="apartment" name="apartment"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-warm-500 focus:border-transparent transition-all"
                                   placeholder="Например: 42">
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Или выберите на карте:</p>
                            <div id="map" class="w-full h-40 rounded-xl border border-gray-200"></div>
                        </div>

                        <!-- Order Total -->
                        <div class="bg-warm-50 rounded-xl p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Товары:</span>
                                <span class="font-medium"><?php echo number_format($total, 0, '', ' '); ?> ₸</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-900">К оплате:</span>
                                <span class="text-2xl font-bold text-warm-600" id="total-price"><?php echo number_format($total, 0, '', ' '); ?> ₸</span>
                            </div>
                        </div>

                        <button type="submit" id="orderBtn"
                                class="w-full btn-primary text-white py-4 rounded-xl font-semibold text-lg">
                            <span class="flex items-center justify-center">
                                <span id="btnText">Подтвердить заказ</span>
                                <span id="btnSpinner" class="hidden ml-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

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
            <a href="/orders" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs mt-1">Заказы</span>
            </a>
            <a href="/cart" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Корзина</span>
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

        // Toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-20 left-1/2 -translate-x-1/2 px-4 py-3 rounded-xl shadow-lg z-50 text-sm font-medium ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' : 'bg-gray-900 text-white'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
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

                // Проверяем, что ответ в формате JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Server returned non-JSON response:', text.substring(0, 500));
                    showToast('Ошибка сервера. Попробуйте позже.', 'error');
                    return;
                }

                const result = await response.json();

                if (!response.ok) {
                    // Если есть активный заказ - показываем кнопку перехода
                    if (result.active_order) {
                        const orderId = result.active_order.id;
                        showToast('У вас уже есть активный заказ #' + orderId, 'error');
                        setTimeout(() => {
                            if (confirm('Перейти к вашим заказам?')) {
                                window.location.href = '/orders';
                            }
                        }, 1500);
                    } else if (result.redirect) {
                        showToast(result.error || 'Ошибка оформления заказа', 'error');
                        setTimeout(() => window.location.href = result.redirect, 2000);
                    } else {
                        showToast(result.error || 'Ошибка оформления заказа', 'error');
                    }
                    return;
                }

                if (result.success) {
                    showToast('Заказ #' + result.order_id + ' успешно оформлен!', 'success');
                    localStorage.removeItem('cart');
                    setTimeout(() => window.location.href = '/orders', 2000);
                } else {
                    showToast(result.error || 'Ошибка оформления заказа', 'error');
                }
            } catch (error) {
                console.error('Order error:', error);
                showToast('Ошибка: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Подтвердить заказ';
                btnSpinner.classList.add('hidden');
            }
        });

        // Map functionality
        let map, marker;

        function initMap() {
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
                
                if (!response.ok) {
                    console.error('Geocode response not ok:', response.status);
                    return;
                }
                
                const data = await response.json();
                console.log('Geocode response:', data);
                
                if (data && data.address) {
                    const addr = data.address;
                    // Пробуем разные поля для улицы
                    let street = addr.road || addr.pedestrian || addr.path || addr.residential || 
                                 addr.street || addr.neighbourhood || addr.suburb || '';
                    let house = addr.house_number || '';
                    
                    // Формируем адрес
                    let address = '';
                    if (street && house) {
                        address = street + ' ' + house;
                    } else if (street) {
                        address = street;
                    } else if (data.display_name) {
                        // Если нет улицы, берем начало display_name
                        const parts = data.display_name.split(',');
                        address = parts[0] || '';
                    }
                    
                    if (address) {
                        document.getElementById('address').value = address.trim();
                        showToast('Адрес выбран', 'success');
                    } else {
                        showToast('Не удалось определить адрес', 'error');
                    }
                } else if (data && data.display_name) {
                    // Fallback на display_name
                    const parts = data.display_name.split(',');
                    document.getElementById('address').value = parts[0] || data.display_name;
                    showToast('Адрес выбран', 'success');
                }
            } catch (error) {
                console.error('Geocode error:', error);
                showToast('Ошибка при определении адреса', 'error');
            }
        }

        // Определение местоположения пользователя
        async function detectLocation() {
            const addressInput = document.getElementById('address');
            const originalValue = addressInput.value;
            
            // Показываем загрузку
            addressInput.value = 'Определяем местоположение...';
            addressInput.disabled = true;
            
            if (!navigator.geolocation) {
                addressInput.value = originalValue;
                addressInput.disabled = false;
                showToast('Геолокация не поддерживается браузером', 'error');
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Обновляем маркер на карте
                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 16);
                    
                    // Получаем адрес
                    await reverseGeocode(lat, lng);
                    addressInput.disabled = false;
                },
                (error) => {
                    addressInput.value = originalValue;
                    addressInput.disabled = false;
                    
                    let message = 'Ошибка определения местоположения';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Доступ к геолокации запрещён. Разрешите в настройках браузера.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Местоположение недоступно';
                            break;
                        case error.TIMEOUT:
                            message = 'Превышено время ожидания';
                            break;
                    }
                    showToast(message, 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        }

        // Sync cart
        const SYNC_KEY = 'cart_synced_timestamp';
        
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            syncCartFromLocalStorage();
        });
        
        async function syncCartFromLocalStorage() {
            const serverCart = <?php echo json_encode($cart); ?>;
            const localCartRaw = localStorage.getItem('cart');
            
            if (!localCartRaw) return;
            
            try {
                const localCart = JSON.parse(localCartRaw);
                
                if (!Array.isArray(localCart) || localCart.length === 0) return;
                
                const lastSync = localStorage.getItem(SYNC_KEY);
                if (lastSync && (Date.now() - parseInt(lastSync)) < 3000) return;
                
                const needsSync = !serverCart || serverCart.length !== localCart.length || 
                    localCart.some((localItem) => {
                        const serverItem = serverCart.find(s => s.id == localItem.id);
                        if (!serverItem) return true;
                        return localItem.quantity != serverItem.quantity;
                    });
                
                if (needsSync) {
                    localStorage.setItem(SYNC_KEY, Date.now().toString());
                    
                    const response = await fetch('/api/cart/sync', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: localCartRaw
                    });
                    
                    if (response.ok) {
                        location.reload();
                    }
                }
            } catch (e) {
                console.error('Error syncing cart:', e);
            }
        }
    </script>
</body>
</html>