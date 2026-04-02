<!DOCTYPE html>
<html lang="ru">
<head>
    <?php include __DIR__ . '/pwa-head.php'; ?>
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
                        <div onclick="showSection('orders')" class="bg-white rounded-2xl p-4 card-shadow cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all duration-200 border-2 border-transparent hover:border-warm-200">
                            <p class="text-sm text-gray-500">Заказы</p>
                            <p id="stat-orders" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                            <p class="text-xs text-warm-400 mt-1 flex items-center gap-1">
                                Перейти
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
                        </div>
                        <div class="bg-white rounded-2xl p-4 card-shadow">
                            <p class="text-sm text-gray-500">Выручка</p>
                            <p id="stat-revenue" class="text-2xl font-bold text-warm-500 mt-1">0 ₸</p>
                        </div>
                        <div onclick="showSection('users')" class="bg-white rounded-2xl p-4 card-shadow cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all duration-200 border-2 border-transparent hover:border-warm-200">
                            <p class="text-sm text-gray-500">Пользователи</p>
                            <p id="stat-users" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                            <p class="text-xs text-warm-400 mt-1 flex items-center gap-1">
                                Перейти
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
                        </div>
                        <div onclick="showSection('products')" class="bg-white rounded-2xl p-4 card-shadow cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all duration-200 border-2 border-transparent hover:border-warm-200">
                            <p class="text-sm text-gray-500">Товары</p>
                            <p id="stat-products" class="text-2xl font-bold text-gray-900 mt-1">0</p>
                            <p class="text-xs text-warm-400 mt-1 flex items-center gap-1">
                                Перейти
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </p>
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
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">Товары</h1>
                        <div class="flex gap-2">
                            <button onclick="openImportModal()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium text-sm transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Импорт CSV
                            </button>
                            <button onclick="openProductModal()" class="btn-primary text-white px-4 py-2 rounded-xl font-medium text-sm">
                                Добавить товар
                            </button>
                        </div>
                    </div>
                    
                    <!-- Поиск и фильтры товаров -->
                    <div class="bg-white rounded-2xl p-4 card-shadow">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="productSearch" placeholder="Поиск по названию..." 
                                       oninput="filterProducts()"
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                            </div>
                            <div class="md:w-64">
                                <select id="productCategoryFilter" onchange="filterProducts()"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                                    <option value="">Все категории</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="products-list" class="space-y-3">
                        <p class="text-gray-500 text-center py-8">Загрузка...</p>
                    </div>
                </section>
                
                <!-- Users Section -->
                <section id="section-users" class="space-y-4 hidden">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Пользователи</h1>
                    </div>
                    
                    <!-- Поиск и фильтры пользователей -->
                    <div class="bg-white rounded-2xl p-4 card-shadow">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="userSearch" placeholder="Поиск по имени или телефону..." 
                                       oninput="filterUsers()"
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                            </div>
                            <div class="md:w-64">
                                <select id="userRoleFilter" onchange="filterUsers()"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                                    <option value="">Все роли</option>
                                    <option value="admin">Админы</option>
                                    <option value="courier">Курьеры</option>
                                    <option value="user">Клиенты</option>
                                </select>
                            </div>
                        </div>
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
            <button onclick="showSection('dashboard')" id="mobile-nav-dashboard" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Главная</span>
            </button>
            <button onclick="showSection('orders')" id="mobile-nav-orders" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs mt-1">Заказы</span>
            </button>
            <button onclick="showSection('couriers')" id="mobile-nav-couriers" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="text-xs mt-1">Курьеры</span>
            </button>
            <button onclick="showSection('products')" id="mobile-nav-products" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <span class="text-xs mt-1">Товары</span>
            </button>
            <button onclick="showSection('users')" id="mobile-nav-users" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-xs mt-1">Люди</span>
            </button>
            <button onclick="showSection('chat')" id="mobile-nav-chat" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
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
                const mobileNav = document.getElementById(`mobile-nav-${s}`);
                
                if (section) section.classList.toggle('hidden', s !== name);
                if (nav) nav.classList.toggle('active', s === name);
                
                // Обновляем цвета мобильной навигации
                if (mobileNav) {
                    if (s === name) {
                        mobileNav.classList.remove('text-gray-400');
                        mobileNav.classList.add('text-warm-500');
                        const span = mobileNav.querySelector('span');
                        if (span) span.classList.add('font-medium');
                    } else {
                        mobileNav.classList.add('text-gray-400');
                        mobileNav.classList.remove('text-warm-500');
                        const span = mobileNav.querySelector('span');
                        if (span) span.classList.remove('font-medium');
                    }
                }
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
                    
                    ${o.courier_name ? `
                        <div class="mb-3 p-2 bg-blue-50 rounded-xl flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            <div>
                                <p class="text-xs text-blue-600 font-medium">Курьер: ${o.courier_name}</p>
                                ${o.courier_phone ? `<p class="text-xs text-blue-500">${o.courier_phone}</p>` : ''}
                            </div>
                        </div>
                    ` : ''}
                    
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
                const [productsRes, categoriesRes] = await Promise.all([
                    fetch('/api/admin/products'),
                    fetch('/api/categories')
                ]);
                const products = await productsRes.json();
                const categories = await categoriesRes.json();
                
                // Заполняем фильтр категорий
                const categoryFilter = document.getElementById('productCategoryFilter');
                categoryFilter.innerHTML = '<option value="">Все категории</option>' + 
                    categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                
                allProducts = products;
                allProductsOriginal = products; // Сохраняем оригинальный список
                renderProducts(products);
            } catch (error) {
                document.getElementById('products-list').innerHTML = '<p class="text-gray-500 text-center py-8 col-span-full">Ошибка загрузки</p>';
            }
        }
        
        function filterProducts() {
            const searchTerm = document.getElementById('productSearch').value.toLowerCase();
            const categoryId = document.getElementById('productCategoryFilter').value;
            
            // Если оба фильтра пустые - показываем все товары из оригинального списка
            if (!searchTerm && !categoryId) {
                renderProducts(allProductsOriginal);
                return;
            }
            
            const filtered = allProductsOriginal.filter(p => {
                const matchesSearch = !searchTerm || (p.name && p.name.toLowerCase().includes(searchTerm));
                const matchesCategory = !categoryId || p.category_id == categoryId;
                return matchesSearch && matchesCategory;
            });
            
            renderProducts(filtered);
        }

        let allProducts = [];
        let allProductsOriginal = []; // Сохраняем оригинальный список
        
        function renderProducts(products) {
            const container = document.getElementById('products-list');
            if (!products || products.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">Нет товаров</p>';
                return;
            }
            
            container.innerHTML = products.map(p => `
                <div onclick="openEditProductModal(${p.id})" 
                     class="bg-white rounded-2xl p-4 card-shadow flex items-center justify-between cursor-pointer hover:shadow-lg transition-shadow">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-warm-50 to-warm-100 flex items-center justify-center overflow-hidden">
                            ${p.image_url ? `<img src="${p.image_url}" class="w-full h-full object-cover rounded-xl">` : '<svg class="w-6 h-6 text-warm-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>'}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${p.name}</p>
                            <p class="text-sm text-gray-500">${p.category_name || ''}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warm-600">${p.price} ₸</p>
                        <p class="text-xs text-gray-400">${p.is_weighted ? 'Весовой' : 'Штучный'}</p>
                    </div>
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
                allUsers = users;
                allUsersOriginal = users; // Сохраняем оригинальный список
                renderUsers(users);
            } catch (error) {
                document.getElementById('users-list').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки</p>';
            }
        }

        let allUsers = [];
        let allUsersOriginal = []; // Сохраняем оригинальный список
        
        function filterUsers() {
            const searchTerm = document.getElementById('userSearch').value.toLowerCase();
            const roleFilter = document.getElementById('userRoleFilter').value;
            
            // Если оба фильтра пустые - показываем всех пользователей из оригинального списка
            if (!searchTerm && !roleFilter) {
                renderUsers(allUsersOriginal);
                return;
            }
            
            const filtered = allUsersOriginal.filter(u => {
                const matchesSearch = !searchTerm || 
                    (u.name && u.name.toLowerCase().includes(searchTerm)) || 
                    (u.phone && u.phone.toLowerCase().includes(searchTerm));
                const matchesRole = !roleFilter || u.role === roleFilter;
                return matchesSearch && matchesRole;
            });
            
            renderUsers(filtered);
        }
        
        function renderUsers(users) {
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
                            <p class="font-medium text-gray-900 flex items-center">
                                ${u.name || 'Без имени'}
                                ${u.whatsapp_notifications ? `
                                    <svg class="w-4 h-4 ml-2 text-green-500" fill="currentColor" viewBox="0 0 24 24" title="Получает WhatsApp уведомления">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                ` : ''}
                            </p>
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
            
            // Показываем настройки WhatsApp только для админов и сборщиков
            const showWhatsApp = user.role === 'admin' || user.role === 'picker';
            const whatsAppSection = showWhatsApp ? `
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp уведомления
                    </h4>
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="userWhatsAppNotifications" ${user.whatsapp_notifications ? 'checked' : ''} 
                                   class="w-5 h-5 text-green-500 rounded border-gray-300 focus:ring-green-500">
                            <span class="ml-3 text-sm text-gray-700">Получать уведомления о заказах</span>
                        </label>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">WhatsApp номер (если отличается от телефона)</label>
                            <input type="text" id="userWhatsAppPhone" value="${user.whatsapp_phone || ''}" 
                                   placeholder="+7 700 123 45 67"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-300 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        </div>
                    </div>
                </div>
            ` : '';
            
            const modal = document.createElement('div');
            modal.id = 'userModal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/30" onclick="closeUserModal()"></div>
                <div class="relative bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white p-4 border-b border-gray-100 flex justify-between items-center z-10">
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
                            <select id="userRole" onchange="handleRoleChange()" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none">
                                <option value="user" ${user.role === 'user' ? 'selected' : ''}>Клиент</option>
                                <option value="courier" ${user.role === 'courier' ? 'selected' : ''}>Курьер</option>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Админ</option>
                            </select>
                        </div>
                        ${whatsAppSection}
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
            
            // Добавляем WhatsApp настройки, если они есть
            const whatsappNotificationsEl = document.getElementById('userWhatsAppNotifications');
            const whatsappPhoneEl = document.getElementById('userWhatsAppPhone');
            
            if (whatsappNotificationsEl) {
                userData.whatsapp_notifications = whatsappNotificationsEl.checked ? 1 : 0;
            }
            if (whatsappPhoneEl) {
                userData.whatsapp_phone = whatsappPhoneEl.value;
            }
            
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
        
        function handleRoleChange() {
            const roleSelect = document.getElementById('userRole');
            const selectedRole = roleSelect.value;
            
            // Находим или создаем секцию WhatsApp
            let whatsappSection = document.getElementById('whatsappSection');
            
            // Показываем настройки WhatsApp только для админов и сборщиков
            if (selectedRole === 'admin' || selectedRole === 'picker') {
                if (!whatsappSection) {
                    const form = document.getElementById('userForm');
                    const submitBtn = form.querySelector('button[type="submit"]').parentElement;
                    
                    const sectionHtml = `
                        <div id="whatsappSection" class="border-t border-gray-100 pt-4 mt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                WhatsApp уведомления
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="userWhatsAppNotifications" class="w-5 h-5 text-green-500 rounded border-gray-300 focus:ring-green-500">
                                    <span class="ml-3 text-sm text-gray-700">Получать уведомления о заказах</span>
                                </label>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">WhatsApp номер (если отличается от телефона)</label>
                                    <input type="text" id="userWhatsAppPhone" placeholder="+7 700 123 45 67" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-300 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                                </div>
                            </div>
                        </div>
                    `;
                    submitBtn.insertAdjacentHTML('beforebegin', sectionHtml);
                }
            } else {
                if (whatsappSection) {
                    whatsappSection.remove();
                }
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

        // Карта курьеров
        let couriersMap = null;
        let couriersMarkers = [];
        let couriersData = [];
        
        async function loadCouriers() {
            try {
                const couriersRes = await fetch('/api/admin/couriers');
                
                const couriers = await couriersRes.json();
                couriersData = couriers;
                
                // Обновляем карту
                updateCouriersMap(couriers);
                
                let html = '<h2 class="text-lg font-semibold text-gray-900 mb-3">Курьеры</h2>';
                
                if (couriers && couriers.length > 0) {
                    html += couriers.map(c => `
                        <div onclick="focusCourierOnMap(${c.id})" 
                             class="bg-white rounded-2xl p-4 card-shadow mb-3 cursor-pointer hover:shadow-lg transition-all ${c.location ? 'hover:border-warm-300 border-2 border-transparent' : ''}">
                            <div class="flex items-center justify-between mb-2">
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
                            ${c.current_order ? `
                                <div class="mt-2 p-3 bg-green-50 rounded-xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm font-medium text-green-700">Заказ #${c.current_order.id}</p>
                                    </div>
                                    <p class="text-xs text-green-600 truncate">${c.current_order.address || 'Адрес не указан'}</p>
                                    <p class="text-xs text-green-500 mt-1">${(c.current_order.total_price || c.current_order.total || 0).toLocaleString()} ₸</p>
                                </div>
                            ` : ''}
                            ${c.location ? `<p class="text-xs text-warm-500 mt-2">📍 Показать на карте</p>` : ''}
                        </div>
                    `).join('');
                } else {
                    html += '<p class="text-gray-500 text-center py-4">Нет курьеров</p>';
                }
                
                document.getElementById('couriers-list').innerHTML = html;
            } catch (error) {
                console.error('Error loading couriers:', error);
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
        
        let chatUsers = [];
        
        async function loadChatUsers() {
            try {
                const response = await fetch('/api/admin/chat/users');
                if (!response.ok) {
                    if (response.status === 401) {
                        document.getElementById('chat-users').innerHTML = '<p class="text-gray-500 text-center py-8">Требуется авторизация. <a href="/login" class="text-warm-500 hover:underline">Войти</a></p>';
                        return;
                    }
                    document.getElementById('chat-users').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка сервера (' + response.status + ')</p>';
                    return;
                }
                
                const text = await response.text();
                let users;
                try {
                    users = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', text.substring(0, 200));
                    document.getElementById('chat-users').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка формата данных</p>';
                    return;
                }
                
                chatUsers = users;
                renderChatUsers(users);
            } catch (error) {
                console.error('Chat users error:', error);
                document.getElementById('chat-users').innerHTML = '<p class="text-gray-500 text-center py-8">Ошибка загрузки: ' + error.message + '</p>';
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
                
                // Помечаем сообщения прочитанными
                await fetch(`/api/admin/chat/mark-read/${userId}`, { method: 'POST' });
                
                // Обновляем список чатов, чтобы убрать счётчик
                loadChatUsers();
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

// Import CSV Modal
        function openImportModal() {
            const modal = document.createElement('div');
            modal.id = 'importModal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/30" onclick="closeImportModal()"></div>
                <div class="relative bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white p-4 border-b border-gray-100 flex justify-between items-center z-10">
                        <h3 class="text-lg font-bold text-gray-900">Импорт товаров из CSV</h3>
                        <button onclick="closeImportModal()" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="bg-blue-50 rounded-xl p-4 text-sm text-blue-700">
                            <p class="font-medium mb-2">Формат CSV файла:</p>
                            <code class="block bg-white p-2 rounded text-xs">
                                name,price,category_id,is_weighted<br>
                                Яблоки,500,1,1<br>
                                Молоко,350,2,0
                            </code>
                            <p class="mt-2 text-xs text-blue-600">
                                * Первая строка - заголовки (обязательно)<br>
                                * is_weighted: 1 - весовой, 0 - штучный<br>
                                * category_id - ID категории из базы
                            </p>
                        </div>
                        
                        <form id="importForm" class="space-y-4">
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                                <input type="file" id="importFile" accept=".csv" class="hidden" onchange="handleImportFile(this)">
                                <label for="importFile" class="cursor-pointer">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">Нажмите для выбора CSV файла</p>
                                    <p id="importFileName" class="text-sm text-warm-500 font-medium mt-2"></p>
                                </label>
                            </div>
                            
                            <div id="importPreview" class="hidden">
                                <h4 class="font-medium text-gray-900 mb-2">Предпросмотр товаров:</h4>
                                <div id="importPreviewList" class="max-h-48 overflow-y-auto bg-gray-50 rounded-xl p-3 text-sm"></div>
                                <p id="importCount" class="text-sm text-gray-500 mt-2"></p>
                            </div>
                            
                            <div class="flex gap-3">
                                <button type="button" onclick="closeImportModal()" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                                    Отмена
                                </button>
                                <button type="submit" id="importSubmitBtn" disabled class="flex-1 btn-primary text-white py-3 rounded-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    Импортировать
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            document.getElementById('importForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await importProducts();
            });
        }
        
        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) modal.remove();
        }
        
        let importData = [];
        
        function handleImportFile(input) {
            importData = [];
            const file = input.files[0];
            if (!file) return;
            
            document.getElementById('importFileName').textContent = file.name;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const text = e.target.result;
                const lines = text.split('\n').filter(line => line.trim());
                
                if (lines.length < 2) {
                    showToast('Файл должен содержать заголовок и хотя бы одну строку данных');
                    return;
                }
                
                const header = lines[0].split(',').map(h => h.trim().toLowerCase());
                const nameIndex = header.indexOf('name');
                const priceIndex = header.indexOf('price');
                const categoryIndex = header.indexOf('category_id');
                const weightedIndex = header.indexOf('is_weighted');
                
                if (nameIndex === -1 || priceIndex === -1 || categoryIndex === -1) {
                    showToast('CSV должен содержать колонки: name, price, category_id');
                    return;
                }
                
                for (let i = 1; i < lines.length; i++) {
                    const values = lines[i].split(',').map(v => v.trim());
                    if (values.length >= 3) {
                        importData.push({
                            name: values[nameIndex],
                            price: parseFloat(values[priceIndex]) || 0,
                            category_id: parseInt(values[categoryIndex]) || 0,
                            is_weighted: weightedIndex !== -1 ? (parseInt(values[weightedIndex]) || 0) : 0
                        });
                    }
                }
                
                if (importData.length === 0) {
                    showToast('Не удалось распознать товары в файле');
                    return;
                }
                
                // Показываем предпросмотр
                const preview = document.getElementById('importPreview');
                const previewList = document.getElementById('importPreviewList');
                preview.classList.remove('hidden');
                
                previewList.innerHTML = importData.slice(0, 10).map((item, i) => `
                    <div class="flex justify-between py-1 border-b border-gray-200 last:border-0">
                        <span class="text-gray-700">${item.name}</span>
                        <span class="text-gray-500">${item.price} ₸</span>
                    </div>
                `).join('') + (importData.length > 10 ? `<p class="text-center text-gray-400 pt-2">...и ещё ${importData.length - 10} товаров</p>` : '');
                
                document.getElementById('importCount').textContent = `Найдено товаров: ${importData.length}`;
                document.getElementById('importSubmitBtn').disabled = false;
            };
            reader.readAsText(file);
        }
        
        async function importProducts() {
            if (importData.length === 0) {
                showToast('Нет данных для импорта');
                return;
            }
            
            const btn = document.getElementById('importSubmitBtn');
            btn.disabled = true;
            btn.textContent = 'Импорт...';
            
            try {
                const response = await fetch('/api/admin/products/import', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ products: importData })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast(`Импортировано ${data.imported} товаров`);
                    closeImportModal();
                    loadProducts();
                } else {
                    showToast(data.error || 'Ошибка импорта');
                    btn.disabled = false;
                    btn.textContent = 'Импортировать';
                }
            } catch (error) {
                showToast('Ошибка соединения');
                btn.disabled = false;
                btn.textContent = 'Импортировать';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
        });
    </script>
</body>
</html>
