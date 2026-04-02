<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Профиль - Delivery</title>
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
        
        .input-field {
            transition: all 0.2s ease;
        }
        
        .input-field:focus {
            border-color: #FF7A3D;
            box-shadow: 0 0 0 3px rgba(255, 122, 61, 0.1);
        }
    </style>
</head>
<body class="gradient-hero min-h-screen pb-20 md:pb-0 <?php echo ($user['role'] ?? 'user') === 'courier' ? 'md:pl-64' : ''; ?>">
    <!-- Header -->
    <header class="glass sticky top-0 z-50 border-b border-warm-100 <?php echo ($user['role'] ?? 'user') === 'courier' ? 'md:hidden' : ''; ?>">
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

    <!-- Profile Content -->
    <section class="px-4 py-6">
        <div class="container mx-auto max-w-2xl">
            <?php if (!$isLoggedIn): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-warm-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-gray-500 mb-4">Войдите для просмотра профиля</p>
                <a href="/login" class="inline-block btn-primary text-white px-6 py-3 rounded-full font-medium">
                    Войти
                </a>
            </div>
            <?php else: ?>
            
            <!-- Profile Header -->
            <div class="bg-white rounded-2xl p-6 card-shadow mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center text-white text-xl font-bold">
                        <?php echo mb_substr($user['name'] ?? 'U', 0, 1); ?>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?></h1>
                        <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['phone'] ?? ''); ?></p>
                        <?php if (($user['role'] ?? 'user') !== 'user'): ?>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-warm-100 text-warm-600 text-xs font-medium rounded-full">
                            <?php echo $user['role'] === 'admin' ? 'Администратор' : 'Курьер'; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <a href="/orders" class="bg-white rounded-2xl p-4 card-shadow flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Заказы</p>
                        <p class="text-sm text-gray-500">История заказов</p>
                    </div>
                </a>
                <a href="/chat" class="bg-white rounded-2xl p-4 card-shadow flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Чат</p>
                        <p class="text-sm text-gray-500">Поддержка</p>
                    </div>
                </a>
            </div>
            
            <?php if (($user['role'] ?? 'user') === 'admin'): ?>
            <!-- Admin Panel Button for Mobile -->
            <a href="/admin" class="md:hidden bg-white rounded-2xl p-4 card-shadow flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Админ панель</p>
                    <p class="text-sm text-gray-500">Управление магазином</p>
                </div>
            </a>
            <?php endif; ?>
            
            <?php if (($user['role'] ?? 'user') === 'courier'): ?>
            <!-- Courier Panel Button for Mobile -->
            <a href="/courier" class="md:hidden bg-white rounded-2xl p-4 card-shadow flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Курьер</p>
                    <p class="text-sm text-gray-500">Доставки</p>
                </div>
            </a>
            <?php endif; ?>
            
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Редактировать профиль</h2>
                
                <form id="profileForm" onsubmit="updateProfile(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <input type="text" 
                               id="name" 
                               value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                               class="input-field w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="tel" 
                               id="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                               class="input-field w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               id="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                               class="input-field w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700"
                               placeholder="Не указан">
                    </div>
                    
                    <div id="message" class="hidden rounded-xl p-3 text-sm"></div>
                    
                    <button type="submit" 
                            id="submitBtn"
                            class="w-full btn-primary text-white py-3 rounded-xl font-medium">
                        Сохранить изменения
                    </button>
                </form>
            </div>
            
            <!-- Logout Button -->
            <button onclick="logout()" class="w-full mt-4 py-3 text-red-500 font-medium rounded-xl hover:bg-red-50 transition-colors">
                Выйти из аккаунта
            </button>
            
            <?php endif; ?>
        </div>
    </section>

    <?php $isCourier = ($user['role'] ?? 'user') === 'courier'; ?>
    
    <!-- Desktop Sidebar for Courier -->
    <?php if ($isCourier): ?>
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
            <a href="/courier" id="desktop-nav-orders" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-warm-50 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="font-medium">Заказы</span>
            </a>
            
            <a href="/chat" id="desktop-nav-chat" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-warm-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="font-medium">Чат</span>
            </a>
            
            <a href="/profile" id="desktop-nav-profile" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-warm-500 to-warm-600 text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">Профиль</span>
            </a>
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
    <?php endif; ?>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-gray-100 bottom-nav z-40">
        <?php if ($isCourier): ?>
        <!-- Courier Navigation -->
        <div class="flex justify-around items-center h-16">
            <a href="/courier" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                <span class="text-xs mt-1">Заказы</span>
            </a>
            <a href="/chat" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs mt-1">Чат</span>
            </a>
            <a href="/profile" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Профиль</span>
            </a>
        </div>
        <?php else: ?>
        <!-- Regular User Navigation -->
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
            <a href="/cart" class="flex flex-col items-center justify-center text-gray-400 hover:text-warm-500 transition-colors relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-xs mt-1">Корзина</span>
                <span id="cart-badge-mobile" class="hidden absolute top-0 right-4 w-4 h-4 bg-warm-500 text-white text-[10px] rounded-full flex items-center justify-center font-medium">0</span>
            </a>
            <a href="/profile" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Профиль</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <script>
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
            
            const cartBadgeMobile = document.getElementById('cart-badge-mobile');
            
            if (count > 0) {
                if (cartBadgeMobile) {
                    cartBadgeMobile.textContent = count > 9 ? '9+' : count;
                    cartBadgeMobile.classList.remove('hidden');
                }
            } else {
                if (cartBadgeMobile) cartBadgeMobile.classList.add('hidden');
            }
        }

        async function updateProfile(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;
            const messageEl = document.getElementById('message');
            const submitBtn = document.getElementById('submitBtn');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Сохранение...';
            
            try {
                const response = await fetch('/api/profile/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, phone, email })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    messageEl.textContent = 'Профиль обновлен';
                    messageEl.className = 'rounded-xl p-3 text-sm bg-green-50 text-green-600';
                    messageEl.classList.remove('hidden');
                    
                    // Update header name
                    setTimeout(() => location.reload(), 1000);
                } else {
                    messageEl.textContent = data.error || 'Ошибка сохранения';
                    messageEl.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-600';
                    messageEl.classList.remove('hidden');
                }
            } catch (error) {
                messageEl.textContent = 'Ошибка соединения';
                messageEl.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-600';
                messageEl.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Сохранить изменения';
            }
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.reload());
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>