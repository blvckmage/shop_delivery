<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Админ-панель - Delivery</title>
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
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link.active {
            background: linear-gradient(135deg, #FF7A3D 0%, #F05A1A 100%);
            color: white;
        }
        
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="gradient-hero min-h-screen">
    <div class="flex">
        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex flex-col w-64 min-h-screen bg-white border-r border-gray-100 fixed left-0 top-0">
            <div class="p-4 border-b border-gray-100">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-800">Delivery</span>
                </a>
            </div>
            
            <nav class="flex-1 p-4 space-y-1">
                <button onclick="showSection('dashboard')" id="nav-dashboard" class="sidebar-link active w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="font-medium">Дашборд</span>
                </button>
                
                <button onclick="showSection('orders')" id="nav-orders" class="sidebar-link w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left text-gray-600 hover:bg-warm-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium">Заказы</span>
                </button>
                
                <button onclick="showSection('products')" id="nav-products" class="sidebar-link w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left text-gray-600 hover:bg-warm-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <span class="font-medium">Товары</span>
                </button>
                
                <button onclick="showSection('users')" id="nav-users" class="sidebar-link w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left text-gray-600 hover:bg-warm-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="font-medium">Пользователи</span>
                </button>
                
                <button onclick="showSection('couriers')" id="nav-couriers" class="sidebar-link w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left text-gray-600 hover:bg-warm-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                    <span class="font-medium">Курьеры</span>
                </button>
                
                <button onclick="showSection('chat')" id="nav-chat" class="sidebar-link w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left text-gray-600 hover:bg-warm-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span class="font-medium">Чат</span>
                </button>
            </nav>
            
            <div class="p-4 border-t border-gray-100">
                <button onclick="logout()" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-medium">Выйти</span>
                </button>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 md:ml-64 pb-20 md:pb-0">
            <!-- Mobile Header -->
            <header class="md:hidden glass sticky top-0 z-50 border-b border-warm-100">
                <div class="container mx-auto px-4">
                    <nav class="flex justify-between items-center h-16">
                        <a href="/" class="flex items-center space-x-2">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                            <span class="text-lg font-bold text-gray-800">Админ</span>
                        </a>
                        
                        <div class="flex items-center space-x-3">
                            <button onclick="toggleNotifications()" class="relative p-2 rounded-full hover:bg-warm-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span id="notification-badge-mobile" class="hidden absolute top-1 right-1 w-2 h-2 bg-warm-500 rounded-full"></span>
                            </button>
                        </div>
                    </nav>
                </div>
            </header>
            
            <!-- Content -->
            <div class="p-4 md:p-6">
                <!-- Dashboard Section -->
                <section id="section-dashboard" class="space-y-6">
                    <h1 class="text-2xl font-bold text-gray-900">Дашборд</h1>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white rounded-2xl p-4 card-shadow">
                            <p class="text-sm text-gray-500">Заказы</p>
                            <p id="stat-orders" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                        </div>
                        <div class="bg-white rounded-2xl p-4 card-shadow">
                            <p class="text-sm text-gray-500">Выручка</p>
                            <p id="stat-revenue" class="text-2xl font-bold text-warm-500 mt-1">0 ₸</p>
                        </div>
                        <div class="bg-white rounded-2xl p-4 card-shadow">
                            <p class="text-sm text-gray-500">Пользователи</p>
                            <p id="stat-users" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                        </div>
                        <div class="bg-white rounded-2xl p-4 card-shadow">
                            <p class="text-sm text-gray-500">Товары</p>
                            <p id="stat-products" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                        </div>
                    </div>
                    
                    <!-- Recent Orders -->
                    <div class="bg-white rounded-2xl p-4 card-shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Последние заказы</h2>
                        <div id="recent-orders" class="space-y-3">
                            <p class="text-gray-500 text-center py-4">Загрузка...</p>
                        </div>
                    </div>
                </section>
                
                <!-- Orders Section -->
                <section id="section-orders" class="space-y-4 hidden">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Заказы</h1>
                        <button onclick="showSection('archive')" class="px-4 py-2 bg-warm-50 hover:bg-warm-100 text-warm-600 rounded-xl font-medium text-sm transition-colors">
                            Архив
                        </button>
                    </div>
                    <div id="orders-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <p class="text-gray-500 text-center py-8 col-span-full">Загрузка...</p>
                    </div>
                </section>
                
                <!-- Archive Section -->
                <section id="section-archive" class="space-y-4 hidden">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Архив заказов</h1>
                        <button onclick="showSection('orders')" class="px-4 py-2 bg-warm-50 hover:bg-warm-100 text-warm-600 rounded-xl font-medium text-sm transition-colors">
                            Назад к заказам
                        </button>
                    </div>
                    <div id="archive-list" class="space-y-3">
                        <p class="text-gray-500 text-center py-8">Загрузка...</p>
                    </div>
                </section>
                
                <!-- Products Section -->
                <section id="section-products" class="space-y-4 hidden">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Товары</h1>
                        <button onclick="openProductModal()" class="btn-primary text-white px-4 py-2 rounded-xl font-medium text-sm">
                            Добавить товар
                        </button>
                    </div>
                    <div id="products-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <p class="text-gray-500 text-center py-8 col-span-full">Загрузка...</p>
                    </div>
                </section>
                
                <!-- Users Section -->
                <section id="section-users" class="space-y-4 hidden">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Пользователи</h1>
                    </div>
                    <div id="users-list" class="space-y-3">
                        <p class="text-gray-500 text-center py-8">Загрузка...</p>
                    </div>
                </section>
                
                <!-- Couriers Section -->
                <section id="section-couriers" class="space-y-4 hidden">
                    <h1 class="text-2xl font-bold text-gray-900">Курьеры и запросы</h1>
                    
                    <!-- Map -->
                    <div class="bg-white rounded-2xl p-4 card-shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">📍 Местоположение курьеров</h2>
                        <div id="couriers-map" class="w-full h-64 md:h-80 rounded-xl bg-gray-100"></div>
                    </div>
                    
                    <div id="couriers-list" class="space-y-3">
                        <p class="text-gray-500 text-center py-8">Загрузка...</p>
                    </div>
                </section>
                
                <!-- Chat Section -->
                <section id="section-chat" class="space-y-4 hidden">
                    <h1 class="text-2xl font-bold text-gray-900">Чат с клиентами</h1>
                    <div id="chat-users" class="space-y-3">
                        <p class="text-gray-500 text-center py-8">Загрузка...</p>
                    </div>
                </section>
            </div>
        </main>
    </div>
    
    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-gray-100 bottom-nav z-40">
        <div class="flex justify-around items-center h-16">
            <button onclick="showSection('dashboard')" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Главная</span>
            </button>
            <button onclick="showSection('orders')" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs mt-1">Заказы</span>
            </button>
            <button onclick="showSection('couriers')" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="text-xs mt-1">Курьеры</span>
            </button>
            <button onclick="showSection('products')" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <span class="text-xs mt-1">Товары</span>
            </button>
            <button onclick="showSection('users')" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-xs mt-1">Люди</span>
            </button>
            <button onclick="showSection('chat')" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs mt-1">Чат</span>
            </button>
        </div>
    </nav>
    
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

    <script>
        const sections = ['dashboard', 'orders', 'archive', 'products', 'users', 'couriers', 'chat'];
        
        function showSection(name) {
            sections.forEach(s => {
                const section = document.getElementById(`section-${s}`);
                const nav = document.getElementById(`nav-${s}`);
                if (section) section.classList.toggle('hidden', s !== name);
                if (nav) nav.classList.toggle('active', s === name);
            });
            
            if (name === 'dashboard') loadDashboard();
            else if (name === 'orders') loadOrders();
            else if (name === 'archive') loadArchive();
            else if (name === 'products') loadProducts();
            else if (name === 'users') loadUsers();
            else if (name === 'couriers') loadCouriers();
            else if (name === 'chat') loadChatUsers();
        }

        async function loadDashboard() {
            try {
                const [statsRes, ordersRes] = await Promise.all([
                    fetch('/api/admin/stats'),
                    fetch('/api/admin/orders')
                ]);
                
                const stats = await statsRes.json();
                const orders = await ordersRes.json();
                
                document.getElementById('stat-orders').textContent = stats.total_orders || 0;
                document.getElementById('stat-revenue').textContent = (stats.total_revenue || 0).toLocaleString() + ' ₸';
                document.getElementById('stat-users').textContent = stats.total_users || 0;
                document.getElementById('stat-products').textContent = stats.total_products || 0;
                
                renderRecentOrders(orders.slice(0, 5));
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }

        function renderRecentOrders(orders) {
            const container = document.getElementById('recent-orders');
            if (!orders || orders.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Нет заказов</p>';
                return;
            }
            
            container.innerHTML = orders.map(o => `
                <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="font-medium text-gray-900">Заказ #${o.id}</p>
                        <p class="text-sm text-gray-500">${o.address || 'Без адреса'}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-warm-600">${(o.total_price || o.total || 0).toLocaleString()} ₸</p>
                        <p class="text-xs text-gray-400">${formatDate(o.created_at)}</p>
                    </div>
                </div>
            `).join('');
        }

        async function loadOrders() {
            try {
                const response = await fetch('/api/admin/orders');
                const orders = await response.json();
                renderOrders('orders-list', orders);
            } catch (error) {
                document.getElementById('orders-list').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }

        async function loadArchive() {
            try {
                const response = await fetch('/api/admin/archive');
                const orders = await response.json();
                renderOrders('archive-list', orders, true);
            } catch (error) {
                document.getElementById('archive-list').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }

        function renderOrders(containerId, orders, isArchive = false) {
            const container = document.getElementById(containerId);
            if (!orders || orders.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">' + (isArchive ? 'Архив пуст' : 'Нет заказов') + '</p>';
                return;
            }
            
            container.innerHTML = orders.map(o => `
                <div class="bg-white rounded-2xl p-4 card-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Заказ #${o.id}</h3>
                            <p class="text-sm text-gray-500">${formatDate(o.created_at)}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${isArchive ? 'bg-gray-100 text-gray-600' : 'bg-warm-100 text-warm-600'}">
                            ${o.total_price || o.total} ₸
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">${o.address || 'Адрес не указан'}</p>
                    
                    ${!isArchive ? `
                        <!-- Селектор статуса -->
                        <div class="mb-3">
                            <select onchange="changeOrderStatus(${o.id}, this.value)" 
                                    class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-sm font-medium text-gray-700 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none"
                                    id="status-${o.id}">
                                <option value="СОЗДАН" ${o.status === 'СОЗДАН' || !o.status ? 'selected' : ''}>Создан</option>
                                <option value="В_ПУТИ" ${o.status === 'В_ПУТИ' ? 'selected' : ''}>В пути</option>
                                <option value="ДОСТАВЛЕН" ${o.status === 'ДОСТАВЛЕН' ? 'selected' : ''}>Доставлен</option>
                                <option value="ОТМЕНЕН" ${o.status === 'ОТМЕНЕН' ? 'selected' : ''}>Отменен</option>
                            </select>
                        </div>
                    ` : `
                        <p class="text-xs text-gray-400 mb-3">Статус: ${formatStatus(o.status)}</p>
                    `}
                    
                    <!-- Кнопки действий -->
                    <div class="flex flex-wrap gap-2">
                        <button onclick="showOrderDetails(${o.id}, ${isArchive})" class="flex-1 py-2 bg-warm-50 hover:bg-warm-100 text-warm-600 rounded-xl text-sm font-medium transition-colors">
                            Детали
                        </button>
                        ${!isArchive ? `
                            <button onclick="archiveOrder(${o.id})" class="py-2 px-3 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl text-sm font-medium transition-colors">
                                В архив
                            </button>
                        ` : `
                            <button onclick="restoreOrder(${o.id})" class="py-2 px-3 bg-green-50 hover:bg-green-100 text-green-600 rounded-xl text-sm font-medium transition-colors">
                                Восстановить
                            </button>
                        `}
                    </div>
                </div>
            `).join('');
        }
        
        function formatStatus(status) {
            const statusMap = {
                'СОЗДАН': 'Создан',
                'СБОРКА': 'Сборка',
                'ОЖИДАНИЕ_КУРЬЕРА': 'Ожидание курьера',
                'В_ПУТИ': 'В пути',
                'ДОСТАВЛЕН': 'Доставлен',
                'ОТМЕНЕН': 'Отменен'
            };
            return statusMap[status] || status || 'Создан';
        }
        
        async function showOrderDetails(orderId, isArchive) {
            try {
                const url = isArchive ? '/api/admin/archive' : '/api/admin/orders';
                const response = await fetch(url);
                const orders = await response.json();
                const order = orders.find(o => o.id === orderId);
                
                if (!order) {
                    showToast('Заказ не найден');
                    return;
                }
                
                const items = order.items || [];
                let itemsHtml = '';
                if (items.length > 0) {
                    itemsHtml = items.map(item => `
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-700">${item.name || 'Товар'} x${item.quantity || 1}</span>
                            <span class="text-gray-900 font-medium">${(item.price || 0) * (item.quantity || 1)} ₸</span>
                        </div>
                    `).join('');
                } else {
                    itemsHtml = '<p class="text-gray-500 text-center py-2">Нет товаров</p>';
                }
                
                const modal = document.createElement('div');
                modal.id = 'orderModal';
                modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
                modal.innerHTML = `
                    <div class="absolute inset-0 bg-black/30" onclick="closeOrderModal()"></div>
                    <div class="relative bg-white rounded-2xl max-w-md w-full max-h-[80vh] overflow-y-auto">
                        <div class="sticky top-0 bg-white p-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">Заказ #${order.id}</h3>
                            <button onclick="closeOrderModal()" class="p-2 hover:bg-gray-100 rounded-full">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Статус</p>
                                <p class="font-medium text-gray-900">${formatStatus(order.status)}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Адрес доставки</p>
                                <p class="font-medium text-gray-900">${order.address || 'Не указан'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Дата создания</p>
                                <p class="font-medium text-gray-900">${formatDate(order.created_at)}</p>
                            </div>
                            ${order.archived_at ? `
                                <div>
                                    <p class="text-sm text-gray-500">Дата архивации</p>
                                    <p class="font-medium text-gray-900">${formatDate(order.archived_at)}</p>
                                </div>
                            ` : ''}
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Товары</p>
                                <div class="bg-gray-50 rounded-xl p-3">
                                    ${itemsHtml}
                                </div>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-100">
                                <span class="font-medium text-gray-900">Итого</span>
                                <span class="font-bold text-warm-600">${order.total_price || order.total || 0} ₸</span>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            } catch (error) {
                showToast('Ошибка загрузки деталей');
            }
        }
        
        function closeOrderModal() {
            const modal = document.getElementById('orderModal');
            if (modal) modal.remove();
        }
        
        async function restoreOrder(orderId) {
            if (!confirm('Восстановить заказ #' + orderId + ' из архива?')) return;
            try {
                const response = await fetch(`/api/admin/archive/${orderId}/restore`, {
                    method: 'POST'
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Заказ восстановлен');
                    loadArchive();
                } else {
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка восстановления');
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('/api/admin/products');
                const products = await response.json();
                renderProducts(products);
            } catch (error) {
                document.getElementById('products-list').innerHTML = '<p class="text-gray-500 text-center py-8 col-span-full">Ошибка загрузки</p>';
            }
        }

        let allProducts = [];
        
        function renderProducts(products) {
            allProducts = products;
            const container = document.getElementById('products-list');
            if (!products || products.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8 col-span-full">Нет товаров</p>';
                return;
            }
            
            container.innerHTML = products.map(p => `
                <div onclick="openEditProductModal(${p.id})" 
                     class="bg-white rounded-2xl p-4 card-shadow cursor-pointer hover:shadow-lg transition-shadow">
                    <div class="aspect-square bg-gradient-to-br from-warm-50 to-warm-100 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                        ${p.image_url ? `<img src="${p.image_url}" class="w-full h-full object-cover rounded-xl">` : '<svg class="w-12 h-12 text-warm-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>'}
                    </div>
                    <h3 class="font-medium text-gray-900 truncate">${p.name}</h3>
                    <p class="text-warm-600 font-bold">${p.price} ₸</p>
                    <p class="text-xs text-gray-400 mt-1">${p.is_weighted ? 'Весовой товар' : 'Штучный товар'}</p>
                </div>
            `).join('');
        }
        
        async function openEditProductModal(productId) {
            const product = allProducts.find(p => p.id === productId);
            if (!product) return;
            
            // Сбрасываем выбранный файл
            selectedEditProductImageFile = null;
            
            // Загружаем категории
            let categoriesOptions = '';
            try {
                const response = await fetch('/api/categories');
                const categories = await response.json();
                categoriesOptions = categories.map(c => 
                    `<option value="${c.id}" ${c.id == product.category_id ? 'selected' : ''}>${c.name}</option>`
                ).join('');
            } catch (error) {
                console.error('Error loading categories:', error);
            }
            
            const modal = document.createElement('div');
            modal.id = 'editProductModal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/30" onclick="closeEditProductModal()"></div>
                <div class="relative bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white p-4 border-b border-gray-100 flex justify-between items-center z-10">
                        <h3 class="text-lg font-bold text-gray-900">Редактирование товара</h3>
                        <button onclick="closeEditProductModal()" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="editProductForm" class="p-4 space-y-4">
                        <input type="hidden" id="editProductId" value="${product.id}">
                        
                        <!-- Превью изображения с возможностью загрузки -->
                        <div class="relative">
                            <div id="editProductImagePreviewContainer" class="aspect-square bg-gradient-to-br from-warm-50 to-warm-100 rounded-xl flex items-center justify-center overflow-hidden cursor-pointer max-h-48" onclick="document.getElementById('editProductImageFile').click()">
                                ${product.image_url ? 
                                    `<img src="${product.image_url}" id="editProductImagePreview" class="w-full h-full object-cover rounded-xl">` : 
                                    `<div class="text-center p-4">
                                        <svg class="w-12 h-12 text-warm-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm text-warm-400">Нажмите для загрузки фото</p>
                                    </div>`
                                }
                            </div>
                            <input type="file" id="editProductImageFile" accept="image/*" class="hidden" onchange="handleEditProductImageSelect(this)">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Название</label>
                            <input type="text" id="editProductName" value="${product.name || ''}" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Цена (₸)</label>
                            <input type="number" id="editProductPrice" value="${product.price || ''}" required min="0"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                            <select id="editProductCategory" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                                ${categoriesOptions}
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editProductWeighted" ${product.is_weighted ? 'checked' : ''} class="w-4 h-4 text-warm-500 rounded">
                            <label for="editProductWeighted" class="ml-2 text-sm text-gray-700">Весовой товар (цена за 1 кг)</label>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="flex-1 btn-primary text-white py-3 rounded-xl font-medium">
                                Сохранить
                            </button>
                            <button type="button" onclick="deleteProductFromModal(${product.id})" 
                                    class="py-3 px-4 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl font-medium transition-colors">
                                Удалить
                            </button>
                        </div>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
            
            document.getElementById('editProductForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await updateProduct();
            });
        }
        
        function updateImagePreview(url) {
            const preview = document.getElementById('editProductImagePreview');
            if (url) {
                preview.outerHTML = `<img src="${url}" id="editProductImagePreview" class="w-full h-full object-cover rounded-xl">`;
            }
        }
        
        function closeEditProductModal() {
            const modal = document.getElementById('editProductModal');
            if (modal) modal.remove();
        }
        
        async function updateProduct() {
            const productId = document.getElementById('editProductId').value;
            const name = document.getElementById('editProductName').value;
            const price = parseFloat(document.getElementById('editProductPrice').value);
            const category_id = parseInt(document.getElementById('editProductCategory').value);
            const is_weighted = document.getElementById('editProductWeighted').checked ? 1 : 0;
            
            if (!name || !price || !category_id) {
                showToast('Заполните все обязательные поля');
                return;
            }
            
            try {
                // Получаем текущий URL изображения из превью или оставляем пустым
                let image_url = '';
                const currentImg = document.getElementById('editProductImagePreview');
                if (currentImg && currentImg.src) {
                    image_url = currentImg.src;
                }
                
                // Если выбран новый файл - загружаем его
                if (selectedEditProductImageFile) {
                    showToast('Загрузка изображения...');
                    const formData = new FormData();
                    formData.append('image', selectedEditProductImageFile);
                    
                    const uploadResponse = await fetch('/api/admin/products/upload-image', {
                        method: 'POST',
                        body: formData
                    });
                    const uploadData = await uploadResponse.json();
                    
                    if (uploadData.success && uploadData.url) {
                        image_url = uploadData.url;
                    } else {
                        showToast(uploadData.error || 'Ошибка загрузки изображения');
                        return;
                    }
                }
                
                const productData = {
                    name,
                    price,
                    category_id,
                    image_url,
                    is_weighted
                };
                
                const response = await fetch(`/api/admin/products/${productId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Товар обновлён');
                    selectedEditProductImageFile = null;
                    closeEditProductModal();
                    loadProducts();
                } else {
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }
        
        async function deleteProductFromModal(productId) {
            if (!confirm('Удалить товар?')) return;
            
            try {
                await fetch(`/api/admin/products/${productId}`, { method: 'DELETE' });
                showToast('Товар удалён');
                closeEditProductModal();
                loadProducts();
            } catch (error) {
                showToast('Ошибка');
            }
        }

        async function loadUsers() {
            try {
                const response = await fetch('/api/admin/users');
                const users = await response.json();
                renderUsers(users);
            } catch (error) {
                document.getElementById('users-list').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }

        let allUsers = [];
        
        function renderUsers(users) {
            allUsers = users;
            const container = document.getElementById('users-list');
            if (!users || users.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">Нет пользователей</p>';
                return;
            }
            
            container.innerHTML = users.map(u => `
                <div onclick="openUserModal(${u.id})" 
                     class="bg-white rounded-2xl p-4 card-shadow flex items-center justify-between cursor-pointer hover:shadow-lg transition-shadow">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center text-white font-medium">
                            ${(u.name || 'U')[0]}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${u.name || 'Без имени'}</p>
                            <p class="text-sm text-gray-500">${u.phone || ''}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${u.role === 'admin' ? 'bg-purple-100 text-purple-600' : u.role === 'courier' ? 'bg-blue-100 text-blue-600' : u.role === 'picker' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'}">
                        ${u.role === 'admin' ? 'Админ' : u.role === 'courier' ? 'Курьер' : u.role === 'picker' ? 'Сборщик' : 'Клиент'}
                    </span>
                </div>
            `).join('');
        }
        
        let currentUserData = null;
        
        function openUserModal(userId) {
            const user = allUsers.find(u => u.id === userId);
            if (!user) return;
            
            currentUserData = user;
            const modal = document.createElement('div');
            modal.id = 'userModal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/30" onclick="closeUserModal()"></div>
                <div class="relative bg-white rounded-2xl max-w-md w-full">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Редактирование пользователя</h3>
                        <button onclick="closeUserModal()" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="userForm" class="p-4 space-y-4">
                        <input type="hidden" id="userId" value="${user.id}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                            <input type="text" id="userName" value="${user.name || ''}" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                            <input type="text" id="userPhone" value="${user.phone || ''}" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="userEmail" value="${user.email || ''}" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                            <select id="userRole" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                                <option value="user" ${user.role === 'user' ? 'selected' : ''}>Клиент</option>
                                <option value="picker" ${user.role === 'picker' ? 'selected' : ''}>Сборщик</option>
                                <option value="courier" ${user.role === 'courier' ? 'selected' : ''}>Курьер</option>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Админ</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="flex-1 btn-primary text-white py-3 rounded-xl font-medium">
                                Сохранить
                            </button>
                            <button type="button" onclick="deleteUser(${user.id})" 
                                    class="py-3 px-4 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl font-medium transition-colors">
                                Удалить
                            </button>
                        </div>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
            
            document.getElementById('userForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await saveUser();
            });
        }
        
        function closeUserModal() {
            const modal = document.getElementById('userModal');
            if (modal) modal.remove();
        }
        
        async function saveUser() {
            const userId = document.getElementById('userId').value;
            const userData = {
                name: document.getElementById('userName').value,
                phone: document.getElementById('userPhone').value,
                email: document.getElementById('userEmail').value,
                role: document.getElementById('userRole').value
            };
            
            try {
                const response = await fetch(`/api/admin/users/${userId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(userData)
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Пользователь обновлён');
                    closeUserModal();
                    loadUsers();
                } else {
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }
        
        async function deleteUser(userId) {
            if (!confirm('Удалить пользователя?')) return;
            
            try {
                const response = await fetch(`/api/admin/users/${userId}`, { method: 'DELETE' });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Пользователь удалён');
                    closeUserModal();
                    loadUsers();
                } else {
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }
        
        // Modal for adding product
        function openProductModal() {
            const modal = document.createElement('div');
            modal.id = 'productModal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/30" onclick="closeProductModal()"></div>
                <div class="relative bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white p-4 border-b border-gray-100 flex justify-between items-center z-10">
                        <h3 class="text-lg font-bold text-gray-900">Добавить товар</h3>
                        <button onclick="closeProductModal()" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="productForm" class="p-4 space-y-4">
                        <!-- Превью изображения -->
                        <div class="relative">
                            <div id="productImagePreview" class="aspect-square bg-gradient-to-br from-warm-50 to-warm-100 rounded-xl flex items-center justify-center overflow-hidden cursor-pointer" onclick="document.getElementById('productImageFile').click()">
                                <div class="text-center p-4">
                                    <svg class="w-12 h-12 text-warm-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-warm-400">Нажмите для загрузки фото</p>
                                </div>
                            </div>
                            <input type="file" id="productImageFile" accept="image/*" class="hidden" onchange="handleProductImageSelect(this)">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Название</label>
                            <input type="text" id="productName" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Цена (₸)</label>
                            <input type="number" id="productPrice" required min="0"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                            <select id="productCategory" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                                <option value="">Выберите категорию</option>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="productWeighted" class="w-4 h-4 text-warm-500 rounded">
                            <label for="productWeighted" class="ml-2 text-sm text-gray-700">Весовой товар (цена за 1 кг)</label>
                        </div>
                        <button type="submit" class="w-full btn-primary text-white py-3 rounded-xl font-medium">
                            Добавить товар
                        </button>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Загружаем категории
            loadCategoriesForProduct();
            
            document.getElementById('productForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await saveProduct();
            });
        }
        
        let selectedProductImageFile = null;
        let selectedEditProductImageFile = null;
        
        function handleProductImageSelect(input) {
            if (input.files && input.files[0]) {
                selectedProductImageFile = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('productImagePreview');
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-xl">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function handleProductImageUrl(url) {
            if (url) {
                selectedProductImageFile = null;
                const preview = document.getElementById('productImagePreview');
                preview.innerHTML = `<img src="${url}" class="w-full h-full object-cover rounded-xl">`;
            }
        }
        
        function handleEditProductImageSelect(input) {
            if (input.files && input.files[0]) {
                selectedEditProductImageFile = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('editProductImagePreviewContainer');
                    preview.innerHTML = `<img src="${e.target.result}" id="editProductImagePreview" class="w-full h-full object-cover rounded-xl">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function handleEditProductImageUrl(url) {
            if (url) {
                selectedEditProductImageFile = null;
                const preview = document.getElementById('editProductImagePreviewContainer');
                preview.innerHTML = `<img src="${url}" id="editProductImagePreview" class="w-full h-full object-cover rounded-xl">`;
            }
        }
        
        async function loadCategoriesForProduct() {
            try {
                const response = await fetch('/api/categories');
                const categories = await response.json();
                const select = document.getElementById('productCategory');
                select.innerHTML = '<option value="">Выберите категорию</option>' + 
                    categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }
        
        function closeProductModal() {
            const modal = document.getElementById('productModal');
            if (modal) modal.remove();
        }
        
        async function saveProduct() {
            const name = document.getElementById('productName').value;
            const price = parseFloat(document.getElementById('productPrice').value);
            const category_id = parseInt(document.getElementById('productCategory').value);
            const is_weighted = document.getElementById('productWeighted').checked ? 1 : 0;
            
            if (!name || !price || !category_id) {
                showToast('Заполните все обязательные поля');
                return;
            }
            
            if (!selectedProductImageFile) {
                showToast('Загрузите фото товара');
                return;
            }
            
            try {
                let image_url = '';
                
                // Если выбран файл - загружаем его
                if (selectedProductImageFile) {
                    showToast('Загрузка изображения...');
                    const formData = new FormData();
                    formData.append('image', selectedProductImageFile);
                    
                    const uploadResponse = await fetch('/api/admin/products/upload-image', {
                        method: 'POST',
                        body: formData
                    });
                    const uploadData = await uploadResponse.json();
                    
                    if (uploadData.success && uploadData.url) {
                        image_url = uploadData.url;
                    } else {
                        showToast(uploadData.error || 'Ошибка загрузки изображения');
                        return;
                    }
                }
                
                const productData = {
                    name,
                    price,
                    category_id,
                    image_url,
                    is_weighted
                };
                
                const response = await fetch('/api/admin/products', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Товар добавлен');
                    selectedProductImageFile = null;
                    closeProductModal();
                    loadProducts();
                } else {
                    showToast(data.error || 'Ошибка');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }

        async function loadCouriers() {
            try {
                const [couriersRes, requestsRes] = await Promise.all([
                    fetch('/api/admin/couriers'),
                    fetch('/api/admin/courier-requests')
                ]);
                
                const couriers = await couriersRes.json();
                const requests = await requestsRes.json();
                
                let html = '<h2 class="text-lg font-semibold text-gray-900 mb-3">Запросы на заказы</h2>';
                
                if (requests && requests.length > 0) {
                    html += requests.map(r => `
                        <div class="bg-white rounded-2xl p-4 card-shadow mb-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">Заказ #${r.order_id}</p>
                                    <p class="text-sm text-gray-500">Курьер: ${r.courier_name}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="confirmCourier(${r.order_id}, ${r.courier_id})" class="px-4 py-2 btn-primary text-white rounded-xl text-sm font-medium">
                                        Подтвердить
                                    </button>
                                    <button onclick="rejectCourier(${r.order_id})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors">
                                        Отклонить
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    html += '<p class="text-gray-500 text-center py-4 mb-4">Нет запросов</p>';
                }
                
                html += '<h2 class="text-lg font-semibold text-gray-900 mb-3 mt-6">Курьеры</h2>';
                
                if (couriers && couriers.length > 0) {
                    html += couriers.map(c => `
                        <div class="bg-white rounded-2xl p-4 card-shadow mb-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center text-white font-medium">
                                    ${(c.name || 'K')[0]}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">${c.name || 'Без имени'}</p>
                                    <p class="text-sm text-gray-500">${c.phone || ''}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${c.current_order ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'}">
                                ${c.current_order ? 'На заказе' : 'Свободен'}
                            </span>
                        </div>
                    `).join('');
                } else {
                    html += '<p class="text-gray-500 text-center py-4">Нет курьеров</p>';
                }
                
                document.getElementById('couriers-list').innerHTML = html;
            } catch (error) {
                document.getElementById('couriers-list').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }

        let chatUsers = [];
        
        async function loadChatUsers() {
            try {
                const response = await fetch('/api/admin/chat/users');
                const users = await response.json();
                chatUsers = users;
                renderChatUsers(users);
            } catch (error) {
                document.getElementById('chat-users').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }

        function renderChatUsers(users) {
            const container = document.getElementById('chat-users');
            if (!users || users.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">Нет чатов</p>';
                return;
            }
            
            container.innerHTML = users.map(u => `
                <div onclick="openChatModal(${u.id})" 
                     class="bg-white rounded-2xl p-4 card-shadow flex items-center justify-between cursor-pointer hover:shadow-lg transition-shadow">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center text-white font-medium">
                            ${(u.name || 'U')[0]}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${u.name || 'Гость'}</p>
                            <p class="text-sm text-gray-500 truncate max-w-xs">${u.last_message || 'Нет сообщений'}</p>
                        </div>
                    </div>
                    ${u.unread_count ? `<span class="px-2 py-1 bg-warm-500 text-white text-xs rounded-full">${u.unread_count}</span>` : ''}
                </div>
            `).join('');
        }
        
        let currentChatUserId = null;
        
        async function openChatModal(userId) {
            currentChatUserId = userId;
            const user = chatUsers.find(u => u.id === userId);
            
            const modal = document.createElement('div');
            modal.id = 'chatModal';
            modal.className = 'fixed inset-0 z-50 flex items-end md:items-center justify-center';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/30" onclick="closeChatModal()"></div>
                <div class="relative bg-white w-full md:max-w-md md:rounded-2xl h-[80vh] md:h-[70vh] flex flex-col">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center text-white font-medium">
                                ${(user?.name || 'U')[0]}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">${user?.name || 'Гость'}</h3>
                                <p class="text-xs text-gray-500">${user?.phone || ''}</p>
                            </div>
                        </div>
                        <button onclick="closeChatModal()" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3">
                        <p class="text-gray-500 text-center py-4">Загрузка...</p>
                    </div>
                    <form id="chatForm" class="p-4 border-t border-gray-100 flex gap-2">
                        <input type="text" id="chatInput" placeholder="Введите сообщение..." 
                               class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                        <button type="submit" class="btn-primary text-white px-4 py-3 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Загружаем сообщения
            await loadChatMessages(userId);
            
            // Обработчик отправки
            document.getElementById('chatForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await sendChatMessage(userId);
            });
        }
        
        async function loadChatMessages(userId) {
            try {
                const response = await fetch(`/api/admin/chat/messages/${userId}`);
                const messages = await response.json();
                renderChatMessages(messages);
            } catch (error) {
                document.getElementById('chatMessages').innerHTML = '<p class="text-gray-500 text-center py-4">Ошибка загрузки</p>';
            }
        }
        
        function renderChatMessages(messages) {
            const container = document.getElementById('chatMessages');
            if (!messages || messages.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Нет сообщений</p>';
                return;
            }
            
            container.innerHTML = messages.map(m => `
                <div class="flex ${m.sender_role === 'admin' ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-[80%] ${m.sender_role === 'admin' ? 'bg-warm-500 text-white' : 'bg-gray-100 text-gray-900'} rounded-2xl px-4 py-2">
                        <p class="text-sm">${m.message}</p>
                        <p class="text-xs ${m.sender_role === 'admin' ? 'text-warm-100' : 'text-gray-400'} mt-1">${formatDate(m.created_at)}</p>
                    </div>
                </div>
            `).join('');
            
            container.scrollTop = container.scrollHeight;
        }
        
        async function sendChatMessage(userId) {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            try {
                const response = await fetch('/api/admin/chat/send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId, message })
                });
                const data = await response.json();
                
                if (data.success) {
                    input.value = '';
                    await loadChatMessages(userId);
                } else {
                    showToast('Ошибка отправки');
                }
            } catch (error) {
                showToast('Ошибка соединения');
            }
        }
        
        function closeChatModal() {
            const modal = document.getElementById('chatModal');
            if (modal) modal.remove();
        }
        
        // Карта курьеров
        let couriersMap = null;
        let couriersMarkers = [];
        let couriersData = [];
        
        async function loadCouriers() {
            try {
                const [couriersRes, requestsRes] = await Promise.all([
                    fetch('/api/admin/couriers'),
                    fetch('/api/admin/courier-requests')
                ]);
                
                const couriers = await couriersRes.json();
                const requests = await requestsRes.json();
                couriersData = couriers;
                
                // Обновляем карту
                updateCouriersMap(couriers);
                
                let html = '<h2 class="text-lg font-semibold text-gray-900 mb-3">Запросы на заказы</h2>';
                
                if (requests && requests.length > 0) {
                    html += requests.map(r => `
                        <div class="bg-white rounded-2xl p-4 card-shadow mb-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">Заказ #${r.order_id}</p>
                                    <p class="text-sm text-gray-500">Курьер: ${r.courier_name}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="confirmCourier(${r.order_id}, ${r.courier_id})" class="px-4 py-2 btn-primary text-white rounded-xl text-sm font-medium">
                                        Подтвердить
                                    </button>
                                    <button onclick="rejectCourier(${r.order_id})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors">
                                        Отклонить
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    html += '<p class="text-gray-500 text-center py-4 mb-4">Нет запросов</p>';
                }
                
                html += '<h2 class="text-lg font-semibold text-gray-900 mb-3 mt-6">Курьеры</h2>';
                
                if (couriers && couriers.length > 0) {
                    html += couriers.map(c => `
                        <div onclick="focusCourierOnMap(${c.id})" 
                             class="bg-white rounded-2xl p-4 card-shadow mb-3 flex items-center justify-between cursor-pointer hover:shadow-lg transition-all ${c.location ? 'hover:border-warm-300 border-2 border-transparent' : ''}">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center text-white font-medium">
                                    ${(c.name || 'K')[0]}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">${c.name || 'Без имени'}</p>
                                    <p class="text-sm text-gray-500">${c.phone || ''}</p>
                                    ${c.location ? `<p class="text-xs text-warm-500">📍 Показать на карте</p>` : ''}
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${c.current_order ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'}">
                                ${c.current_order ? 'На заказе' : 'Свободен'}
                            </span>
                        </div>
                    `).join('');
                } else {
                    html += '<p class="text-gray-500 text-center py-4">Нет курьеров</p>';
                }
                
                document.getElementById('couriers-list').innerHTML = html;
            } catch (error) {
                document.getElementById('couriers-list').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }
        
        function focusCourierOnMap(courierId) {
            const courier = couriersData.find(c => c.id === courierId);
            if (!courier || !courier.location || !couriersMap) {
                if (!courier?.location) {
                    showToast('Курьер не передал местоположение');
                }
                return;
            }
            
            // Центрируем карту на курьере
            couriersMap.setView([courier.location.lat, courier.location.lng], 16);
            
            // Находим маркер курьера и открываем popup
            couriersMarkers.forEach(m => {
                const latLng = m.getLatLng();
                if (Math.abs(latLng.lat - courier.location.lat) < 0.0001 && 
                    Math.abs(latLng.lng - courier.location.lng) < 0.0001) {
                    m.openPopup();
                }
            });
            
            // Скроллим к карте
            document.getElementById('couriers-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        function updateCouriersMap(couriers) {
            const mapContainer = document.getElementById('couriers-map');
            if (!mapContainer) return;
            
            // Инициализируем карту если ещё не создана
            if (!couriersMap && typeof L !== 'undefined') {
                couriersMap = L.map('couriers-map').setView([43.518703, 68.505423], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(couriersMap);
            }
            
            // Если Leaflet не загружен - загружаем динамически
            if (typeof L === 'undefined') {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
                
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = () => {
                    couriersMap = L.map('couriers-map').setView([43.518703, 68.505423], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(couriersMap);
                    addCouriersMarkers(couriers);
                };
                document.head.appendChild(script);
            } else {
                addCouriersMarkers(couriers);
            }
        }
        
        function addCouriersMarkers(couriers) {
            if (!couriersMap) return;
            
            // Очищаем старые маркеры
            couriersMarkers.forEach(m => couriersMap.removeLayer(m));
            couriersMarkers = [];
            
            // Добавляем маркеры курьеров
            couriers.forEach(c => {
                if (c.location && c.location.lat && c.location.lng) {
                    const marker = L.marker([c.location.lat, c.location.lng])
                        .addTo(couriersMap)
                        .bindPopup(`<b>${c.name || 'Курьер'}</b><br>${c.phone || ''}<br>${c.current_order ? '🚗 На заказе' : '✅ Свободен'}`);
                    couriersMarkers.push(marker);
                }
            });
            
            // Центрируем карту по маркерам если есть
            if (couriersMarkers.length > 0) {
                const group = L.featureGroup(couriersMarkers);
                couriersMap.fitBounds(group.getBounds().pad(0.1));
            }
        }

        async function changeOrderStatus(orderId, newStatus) {
            try {
                const response = await fetch(`/api/admin/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Статус обновлён');
                    // Если статус "ДОСТАВЛЕН" - заказ автоматически уйдёт в архив
                    if (newStatus === 'ДОСТАВЛЕН') {
                        setTimeout(() => loadOrders(), 500);
                    }
                } else {
                    showToast(data.error || 'Ошибка');
                    loadOrders(); // Перезагружаем для сброса селекта
                }
            } catch (error) {
                showToast('Ошибка соединения');
                loadOrders();
            }
        }
        
        async function updateOrderStatus(orderId, status) {
            await changeOrderStatus(orderId, status);
        }

        async function archiveOrder(orderId) {
            try {
                await fetch(`/api/admin/orders/${orderId}/archive`, { method: 'POST' });
                loadOrders();
            } catch (error) {
                showToast('Ошибка');
            }
        }

        async function deleteProduct(productId) {
            if (!confirm('Удалить товар?')) return;
            try {
                await fetch(`/api/admin/products/${productId}`, { method: 'DELETE' });
                loadProducts();
            } catch (error) {
                showToast('Ошибка');
            }
        }

        async function confirmCourier(orderId, courierId) {
            try {
                await fetch(`/api/orders/${orderId}/confirm`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ courier_id: courierId })
                });
                loadCouriers();
            } catch (error) {
                showToast('Ошибка');
            }
        }

        async function rejectCourier(orderId) {
            try {
                await fetch(`/api/orders/${orderId}/reject`, { method: 'POST' });
                loadCouriers();
            } catch (error) {
                showToast('Ошибка');
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
            loadDashboard();
        });
    </script>
</body>
</html>