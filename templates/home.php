<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Delivery - Доставка продуктов</title>
    <?php include __DIR__ . '/pwa-head.php'; ?>
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
        
        .card-shadow-hover:hover {
            box-shadow: 0 8px 30px rgba(240, 90, 26, 0.15);
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
        
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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
                    <div class="relative">
                        <button onclick="toggleNotifications()" class="relative p-2 rounded-full hover:bg-warm-50 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notification-badge" class="hidden absolute top-1 right-1 w-2 h-2 bg-warm-500 rounded-full"></span>
                        </button>
                    </div>

                    <a href="/cart" class="relative p-2 rounded-full hover:bg-warm-50 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span id="cart-count" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-warm-500 text-white text-xs rounded-full flex items-center justify-center font-medium">0</span>
                    </a>

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

    <!-- Hero Section -->
    <section class="gradient-hero pt-6 pb-8 px-4">
        <div class="container mx-auto">
            <div class="max-w-2xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                    Доставка продуктов
                    <span class="text-warm-500">домой</span>
                </h1>
                <p class="text-gray-500 text-base md:text-lg mb-6">
                    Свежие продукты с доставкой за 30-60 минут
                </p>
                
                <form action="/catalog" method="GET" class="relative max-w-xl mx-auto">
                    <input type="text" 
                           name="search" 
                           id="searchInput"
                           placeholder="Найти продукты..." 
                           class="w-full px-5 py-4 pr-12 rounded-2xl bg-white border border-warm-100 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none transition-all text-gray-700 placeholder-gray-400 card-shadow"
                           autocomplete="off">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-warm-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <!-- Search suggestions -->
                    <div id="searchSuggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl card-shadow overflow-hidden z-50"></div>
                </form>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="px-4 py-6">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Категории</h2>
                <a href="/catalog" class="text-warm-500 hover:text-warm-600 font-medium text-sm">Все</a>
            </div>
            
            <div class="flex overflow-x-auto hide-scrollbar space-x-3 -mx-4 px-4 pb-2">
                <?php foreach ($categories as $category): ?>
                <a href="/catalog?category=<?php echo $category['id']; ?>" 
                   class="flex-shrink-0 flex flex-col items-center p-4 bg-white rounded-2xl card-shadow card-shadow-hover transition-all min-w-[90px]">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-warm-100 to-warm-200 flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 text-center truncate w-full"><?php echo htmlspecialchars($category['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Products Section -->
    <section class="px-4 py-6">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Популярные товары</h2>
                <a href="/catalog" class="text-warm-500 hover:text-warm-600 font-medium text-sm">Все</a>
            </div>
            
            <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="px-4 py-8">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 card-shadow">
                    <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Быстрая доставка</h3>
                    <p class="text-sm text-gray-500">30-60 минут до двери</p>
                </div>
                
                <div class="bg-white rounded-2xl p-5 card-shadow">
                    <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Свежие продукты</h3>
                    <p class="text-sm text-gray-500">Только качественные товары</p>
                </div>
                
                <div class="bg-white rounded-2xl p-5 card-shadow">
                    <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Удобная оплата</h3>
                    <p class="text-sm text-gray-500">Наличные или картой</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Auth Required Modal -->
    <div id="authRequiredModal" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-black/30" onclick="closeAuthModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white rounded-2xl p-6 shadow-xl">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-warm-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Требуется авторизация</h3>
                <p class="text-gray-500 text-sm mb-6">Войдите в свой аккаунт или зарегистрируйтесь, чтобы добавлять товары в корзину</p>
                
                <div class="space-y-3">
                    <a href="/login" class="block w-full btn-primary text-white py-3 rounded-xl font-medium text-center">
                        Войти
                    </a>
                    <a href="/register" class="block w-full py-3 border border-warm-200 text-warm-600 rounded-xl font-medium text-center hover:bg-warm-50 transition-colors">
                        Зарегистрироваться
                    </a>
                </div>
                
                <button onclick="closeAuthModal()" class="mt-4 text-gray-400 hover:text-gray-600 text-sm">
                    Продолжить без авторизации
                </button>
            </div>
        </div>
    </div>

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
            <a href="/" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Главная</span>
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
        // ==================== PUSH УВЕДОМЛЕНИЯ ====================
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        
        // Запрос разрешения на push-уведомления
        async function requestNotificationPermission() {
            if (!('Notification' in window)) {
                console.log('Браузер не поддерживает уведомления');
                return false;
            }
            
            if (Notification.permission === 'granted') {
                return true;
            }
            
            if (Notification.permission !== 'denied') {
                const permission = await Notification.requestPermission();
                return permission === 'granted';
            }
            
            return false;
        }
        
        // Показать push-уведомление
        function showPushNotification(title, body, icon = '/favicon.ico', data = null) {
            if (Notification.permission === 'granted') {
                const notification = new Notification(title, {
                    body: body,
                    icon: icon,
                    badge: icon,
                    tag: data?.tag || 'default',
                    data: data,
                    requireInteraction: data?.requireInteraction || false
                });
                
                notification.onclick = function(event) {
                    event.preventDefault();
                    window.focus();
                    if (data?.url) {
                        window.location.href = data.url;
                    }
                    notification.close();
                };
                
                // Автоматически закрыть через 10 секунд
                setTimeout(() => notification.close(), 10000);
                
                return notification;
            }
            return null;
        }
        
        // Проверка новых уведомлений и показ push
        let lastNotificationCheck = null;
        async function checkAndShowPushNotifications() {
            if (!isLoggedIn || Notification.permission !== 'granted') return;
            
            try {
                const response = await fetch('/api/notifications');
                if (response.ok) {
                    const notifications = await response.json();
                    const newNotifications = notifications.filter(n => {
                        if (n.read) return false;
                        const createdAt = new Date(n.created_at);
                        return !lastNotificationCheck || createdAt > lastNotificationCheck;
                    });
                    
                    // Показываем только последние 3 уведомления
                    newNotifications.slice(0, 3).forEach(n => {
                        showPushNotification(n.title, n.message, '/favicon.ico', {
                            tag: 'notification-' + n.id,
                            url: n.data?.order_id ? '/orders' : null
                        });
                    });
                    
                    lastNotificationCheck = new Date();
                }
            } catch (error) {
                console.error('Ошибка проверки уведомлений:', error);
            }
        }
        
        // ==================== ОСНОВНОЙ КОД ====================
        
        let searchTimeout;
        
        // Live search
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            const suggestions = document.getElementById('searchSuggestions');
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestions.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch('/api/products?search=' + encodeURIComponent(query));
                    const products = await response.json();
                    
                    if (products.length > 0) {
                        suggestions.innerHTML = products.slice(0, 5).map(p => `
                            <a href="/catalog?product=${p.id}" class="flex items-center p-3 hover:bg-warm-50 transition-colors border-b border-gray-50 last:border-0">
                                <div class="w-10 h-10 rounded-lg bg-warm-50 flex-shrink-0 overflow-hidden">
                                    ${p.image_url ? `<img src="${p.image_url}" class="w-full h-full object-cover">` : ''}
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">${p.name}</p>
                                    <p class="text-xs text-warm-500">${p.price} ₸</p>
                                </div>
                            </a>
                        `).join('');
                        suggestions.classList.remove('hidden');
                    } else {
                        suggestions.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">Ничего не найдено</div>';
                        suggestions.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Search error:', error);
                }
            }, 300);
        });
        
        // Hide suggestions on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#searchInput') && !e.target.closest('#searchSuggestions')) {
                document.getElementById('searchSuggestions').classList.add('hidden');
            }
        });

        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            // Штучные товары считаем по количеству, весовые - как 1 позицию
            const count = cart.reduce((sum, item) => {
                if (item.is_weighted) {
                    return sum + 1; // Весовой товар = 1 позиция
                } else {
                    return sum + Math.round(item.quantity); // Штучный = количество штук
                }
            }, 0);
            
            const cartCount = document.getElementById('cart-count');
            const cartBadgeMobile = document.getElementById('cart-badge-mobile');
            
            if (count > 0) {
                if (cartCount) {
                    cartCount.textContent = count > 9 ? '9+' : count;
                    cartCount.classList.remove('hidden');
                }
                if (cartBadgeMobile) {
                    cartBadgeMobile.textContent = count > 9 ? '9+' : count;
                    cartBadgeMobile.classList.remove('hidden');
                }
            } else {
                if (cartCount) cartCount.classList.add('hidden');
                if (cartBadgeMobile) cartBadgeMobile.classList.add('hidden');
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('/api/products');
                const products = await response.json();
                displayProducts(products.slice(0, 8));
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        function displayProducts(products) {
            const container = document.getElementById('products-grid');
            if (!products || products.length === 0) {
                container.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">Нет товаров</div>';
                return;
            }

            container.innerHTML = products.map(product => `
                <div class="bg-white rounded-2xl overflow-hidden card-shadow card-shadow-hover transition-all">
                    <a href="/catalog?product=${product.id}" class="block">
                        <div class="aspect-square bg-gradient-to-br from-warm-50 to-warm-100 relative overflow-hidden">
                            ${product.image_url 
                                ? `<img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover">`
                                : `<div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-warm-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                   </div>`
                            }
                        </div>
                    </a>
                    <div class="p-3">
                        <a href="/catalog?product=${product.id}" class="block">
                            <h3 class="font-medium text-gray-900 text-sm truncate mb-1">${product.name}</h3>
                            <p class="text-warm-600 font-bold">${product.price} <span class="text-sm font-normal text-gray-500">₸</span></p>
                            ${product.is_weighted ? '<p class="text-xs text-gray-400">за 1 кг</p>' : ''}
                        </a>
                        <a href="/catalog?product=${product.id}"
                                class="block w-full mt-3 py-2.5 bg-warm-50 hover:bg-warm-100 text-warm-600 font-medium rounded-xl transition-colors text-sm text-center">
                            Подробнее
                        </a>
                    </div>
                </div>
            `).join('');
        }

        // Auth Modal functions
        function showAuthModal() {
            document.getElementById('authRequiredModal').classList.remove('hidden');
        }
        
        function closeAuthModal() {
            document.getElementById('authRequiredModal').classList.add('hidden');
        }
        
        function addToCart(id) {
            // Redirect to catalog page
            window.location.href = '/catalog?product=' + id;
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg z-50 text-sm font-medium';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
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
                    renderNotifications(notifications);
                } else if (response.status === 401) {
                    list.innerHTML = '<div class="p-8 text-center text-gray-500"><a href="/login" class="text-warm-500 hover:text-warm-600 font-medium">Войдите</a> чтобы видеть уведомления</div>';
                }
            } catch (error) {
                list.innerHTML = '<div class="p-4 text-center text-gray-500">Ошибка загрузки</div>';
            }
        }

        function renderNotifications(notifications) {
            const list = document.getElementById('notificationsList');
            if (!notifications || notifications.length === 0) {
                list.innerHTML = '<div class="p-8 text-center text-gray-500">Нет уведомлений</div>';
                return;
            }

            list.innerHTML = notifications.map(n => `
                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer ${n.read ? 'opacity-60' : ''}" onclick="handleNotification(${n.id})">
                    <div class="font-medium text-gray-900 text-sm">${n.title}</div>
                    <div class="text-gray-500 text-sm mt-1">${n.message}</div>
                    <div class="text-gray-400 text-xs mt-2">${formatTimeAgo(n.created_at)}</div>
                </div>
            `).join('');
        }

        function formatTimeAgo(dateString) {
            const diff = Math.floor((new Date() - new Date(dateString)) / 1000);
            if (diff < 60) return 'только что';
            if (diff < 3600) return Math.floor(diff / 60) + ' мин назад';
            if (diff < 86400) return Math.floor(diff / 3600) + ' ч назад';
            return Math.floor(diff / 86400) + ' дн назад';
        }

        async function handleNotification(id) {
            await fetch(`/api/notifications/${id}/read`, { method: 'POST' });
            updateNotificationBadge();
            closeNotifications();
        }

        async function updateNotificationBadge() {
            // Skip if user is not logged in
            if (!isLoggedIn) {
                const badge = document.getElementById('notification-badge');
                if (badge) badge.classList.add('hidden');
                return;
            }
            
            try {
                const response = await fetch('/api/notifications/unread-count');
                if (response.ok) {
                    const data = await response.json();
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                } else if (response.status === 401) {
                    const badge = document.getElementById('notification-badge');
                    if (badge) badge.classList.add('hidden');
                }
            } catch (error) {
                // Игнорируем ошибки
            }
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.reload());
        }

        document.addEventListener('DOMContentLoaded', async function() {
            // Clear cart if user is not logged in
            if (!isLoggedIn) {
                localStorage.removeItem('cart');
            }
            
            // Normalize cart data format
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            if (cart && cart.length > 0) {
                cart = cart.map(item => ({
                    id: parseInt(item.id),
                    name: item.name,
                    price: parseInt(item.price),
                    quantity: parseFloat(item.quantity) || 1,
                    is_weighted: parseInt(item.is_weighted) || 0,
                    image_url: item.image_url || ''
                }));
                localStorage.setItem('cart', JSON.stringify(cart));
            }
            
            // Update UI after small delay to ensure DOM is ready
            setTimeout(() => {
                updateCartCount();
                loadProducts();
                updateNotificationBadge();
            }, 100);
            
            // Запрос разрешения на push-уведомления для авторизованных пользователей
            if (isLoggedIn) {
                const granted = await requestNotificationPermission();
                if (granted) {
                    console.log('Push-уведомления включены');
                    // Проверяем уведомления каждые 30 секунд
                    setInterval(checkAndShowPushNotifications, 30000);
                    // Первая проверка через 5 секунд
                    setTimeout(checkAndShowPushNotifications, 5000);
                }
            }
            
            setInterval(updateNotificationBadge, 30000);
        });
    </script>
    <?php include __DIR__ . '/pwa-scripts.php'; ?>
</body>
</html>
