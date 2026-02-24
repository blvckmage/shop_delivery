<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Каталог - Delivery</title>
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
        
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] { -moz-appearance: textfield; }
        
        .qty-btn {
            transition: all 0.2s ease;
        }
        .qty-btn:active {
            transform: scale(0.9);
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
                    <a href="/catalog" class="text-warm-600 font-medium">Каталог</a>
                    <a href="/orders" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Заказы</a>
                    <a href="/chat" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Чат</a>
                </div>

                <div class="flex items-center space-x-3">
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
                                    <a href="/admin" class="text-gray-600 hover:text-warm-600 font-medium transition-colors">Админ</a>
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

    <!-- Search and Filters -->
    <section class="px-4 py-4">
        <div class="container mx-auto">
            <form method="GET" class="relative mb-4">
                <input type="text" 
                       name="search" 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                       placeholder="Поиск продуктов..." 
                       class="w-full px-5 py-3.5 pr-12 rounded-2xl bg-white border border-warm-100 focus:border-warm-300 focus:ring-2 focus:ring-warm-100 outline-none transition-all text-gray-700 placeholder-gray-400 card-shadow">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-warm-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
            
            <!-- Categories Filter -->
            <div class="flex overflow-x-auto hide-scrollbar space-x-2 -mx-4 px-4 pb-2">
                <a href="/catalog" 
                   class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo !isset($_GET['category']) ? 'bg-warm-500 text-white' : 'bg-white text-gray-600 hover:bg-warm-50 card-shadow'; ?>">
                    Все
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="/catalog?category=<?php echo $cat['id']; ?><?php echo isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?>" 
                   class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'bg-warm-500 text-white' : 'bg-white text-gray-600 hover:bg-warm-50 card-shadow'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="px-4 pb-6">
        <div class="container mx-auto">
            <?php if (!empty($_GET['search'])): ?>
                <p class="text-gray-500 text-sm mb-4">Результаты поиска: "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
            <?php endif; ?>
            
            <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-2xl overflow-hidden card-shadow card-shadow-hover transition-all flex flex-col" id="product-<?php echo $product['id']; ?>">
                    <div class="aspect-square bg-gradient-to-br from-warm-50 to-warm-100 relative overflow-hidden flex-shrink-0">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-warm-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge for quantity in cart -->
                        <div id="cart-badge-<?php echo $product['id']; ?>" class="hidden absolute top-2 right-2 w-7 h-7 bg-warm-500 text-white text-xs rounded-full flex items-center justify-center font-bold shadow-lg">
                            0
                        </div>
                    </div>
                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="font-medium text-gray-900 text-sm truncate mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-warm-600 font-bold"><?php echo $product['price']; ?> <span class="text-sm font-normal text-gray-500">₸</span></p>
                        <?php if ($product['is_weighted']): ?>
                            <p class="text-xs text-gray-400 mb-2">за 1 кг</p>
                        <?php else: ?>
                            <p class="text-xs text-transparent mb-2">.</p>
                        <?php endif; ?>
                        
                        <!-- Cart controls - pushed to bottom -->
                        <div id="cart-controls-<?php echo $product['id']; ?>" class="mt-auto pt-2">
                            <!-- Will be rendered by JS -->
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($products)): ?>
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-warm-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <p class="text-gray-500">Товары не найдены</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Weight Modal -->
    <div id="weightModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="closeWeightModal()"></div>
        <div class="absolute bottom-0 left-0 right-0 md:bottom-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:max-w-sm md:w-full bg-white md:rounded-2xl rounded-t-3xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Укажите вес</h3>
                <button onclick="closeWeightModal()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <p id="weightModalProductName" class="text-gray-600 mb-2"></p>
            <p class="text-sm text-gray-500 mb-4">Цена за 1 кг: <span id="weightModalPrice" class="text-warm-600 font-medium"></span> ₸</p>
            
            <div class="flex items-center justify-center gap-4 mb-6">
                <button onclick="adjustWeight(-0.1)" class="w-12 h-12 rounded-xl bg-warm-50 hover:bg-warm-100 text-warm-600 font-bold text-xl transition-colors">−</button>
                <div class="text-center">
                    <input type="number" id="weightInput" value="0.5" min="0.1" max="10" step="0.1" 
                           class="w-24 text-center text-3xl font-bold text-gray-900 border-b-2 border-warm-300 focus:border-warm-500 outline-none bg-transparent">
                    <p class="text-gray-500 mt-1">кг</p>
                </div>
                <button onclick="adjustWeight(0.1)" class="w-12 h-12 rounded-xl bg-warm-50 hover:bg-warm-100 text-warm-600 font-bold text-xl transition-colors">+</button>
            </div>
            
            <!-- Quick weight buttons -->
            <div class="flex justify-center gap-2 mb-6">
                <button onclick="setWeight(0.5)" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-warm-100 text-gray-700 font-medium transition-colors">0.5 кг</button>
                <button onclick="setWeight(1)" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-warm-100 text-gray-700 font-medium transition-colors">1 кг</button>
                <button onclick="setWeight(2)" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-warm-100 text-gray-700 font-medium transition-colors">2 кг</button>
            </div>
            
            <div class="bg-warm-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Итого:</span>
                    <span id="weightTotalPrice" class="text-2xl font-bold text-warm-600">0 ₸</span>
                </div>
            </div>
            
            <button onclick="confirmWeight()" class="w-full btn-primary text-white py-3 rounded-xl font-medium text-lg">
                Добавить в корзину
            </button>
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
            <a href="/catalog" class="flex flex-col items-center justify-center text-warm-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Каталог</span>
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
        // Product data from PHP
        const products = <?php 
            $productsData = [];
            foreach ($products as $p) {
                $productsData[] = [
                    'id' => $p['id'],
                    'name' => $p['name'],
                    'price' => $p['price'],
                    'is_weighted' => $p['is_weighted'] ? 1 : 0,
                    'image_url' => $p['image_url'] ?? ''
                ];
            }
            echo json_encode($productsData);
        ?>;
        
        const productMap = {};
        products.forEach(p => productMap[p.id] = p);
        
        // Current modal state
        let currentWeightProduct = null;
        
        function getCart() {
            return JSON.parse(localStorage.getItem('cart') || '[]');
        }
        
        function saveCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateAllUI();
        }
        
        function getCartItem(productId) {
            const cart = getCart();
            return cart.find(item => item.id === productId);
        }
        
        function updateAllUI() {
            updateCartCount();
            updateAllProductCards();
        }
        
        function updateCartCount() {
            const cart = getCart();
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
        
        function updateAllProductCards() {
            products.forEach(product => {
                updateProductCard(product.id);
            });
        }
        
        function updateProductCard(productId) {
            const product = productMap[productId];
            if (!product) return;
            
            const cartItem = getCartItem(productId);
            const quantity = cartItem ? cartItem.quantity : 0;
            
            const badge = document.getElementById(`cart-badge-${productId}`);
            const controls = document.getElementById(`cart-controls-${productId}`);
            
            if (badge) {
                if (quantity > 0) {
                    badge.textContent = quantity > 9 ? '9+' : quantity;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            
            if (controls) {
                if (quantity > 0) {
                    // Show quantity controls
                    controls.innerHTML = `
                        <div class="flex items-center justify-between">
                            <button onclick="decreaseQuantity(${productId})" 
                                    class="qty-btn w-9 h-9 rounded-xl bg-warm-50 hover:bg-warm-100 text-warm-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="font-bold text-gray-900">${product.is_weighted ? quantity + ' кг' : quantity}</span>
                            <button onclick="increaseQuantity(${productId})" 
                                    class="qty-btn w-9 h-9 rounded-xl bg-warm-500 hover:bg-warm-600 text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    `;
                } else {
                    // Show "Add to cart" button
                    controls.innerHTML = `
                        <button onclick="addToCart(${productId})"
                                class="w-full py-2.5 bg-warm-50 hover:bg-warm-100 text-warm-600 font-medium rounded-xl transition-colors text-sm">
                            В корзину
                        </button>
                    `;
                }
            }
        }
        
        function addToCart(productId) {
            const product = productMap[productId];
            if (!product) return;
            
            if (product.is_weighted) {
                // Show weight modal
                openWeightModal(product);
            } else {
                // Add regular product
                const cart = getCart();
                const existingIndex = cart.findIndex(item => item.id === productId);
                
                if (existingIndex >= 0) {
                    cart[existingIndex].quantity += 1;
                } else {
                    cart.push({
                        id: productId,
                        name: product.name,
                        price: product.price,
                        quantity: 1,
                        is_weighted: 0,
                        image_url: product.image_url
                    });
                }
                
                saveCart(cart);
                showToast('Добавлено в корзину');
            }
        }
        
        function increaseQuantity(productId) {
            const product = productMap[productId];
            if (!product) return;
            
            if (product.is_weighted) {
                openWeightModal(product);
            } else {
                const cart = getCart();
                const existingIndex = cart.findIndex(item => item.id === productId);
                
                if (existingIndex >= 0) {
                    cart[existingIndex].quantity += 1;
                    saveCart(cart);
                    showToast('Добавлено');
                }
            }
        }
        
        function decreaseQuantity(productId) {
            const cart = getCart();
            const existingIndex = cart.findIndex(item => item.id === productId);
            
            if (existingIndex >= 0) {
                if (cart[existingIndex].quantity > 1) {
                    cart[existingIndex].quantity -= 1;
                    saveCart(cart);
                } else {
                    // Remove from cart
                    cart.splice(existingIndex, 1);
                    saveCart(cart);
                    showToast('Удалено из корзины');
                }
            }
        }
        
        // Weight Modal Functions
        function openWeightModal(product) {
            currentWeightProduct = product;
            
            document.getElementById('weightModalProductName').textContent = product.name;
            document.getElementById('weightModalPrice').textContent = product.price;
            document.getElementById('weightInput').value = '0.5';
            updateWeightTotal();
            
            document.getElementById('weightModal').classList.remove('hidden');
        }
        
        function closeWeightModal() {
            document.getElementById('weightModal').classList.add('hidden');
            currentWeightProduct = null;
        }
        
        function adjustWeight(delta) {
            const input = document.getElementById('weightInput');
            let value = parseFloat(input.value) + delta;
            value = Math.max(0.1, Math.min(10, value));
            input.value = value.toFixed(1);
            updateWeightTotal();
        }
        
        function setWeight(weight) {
            document.getElementById('weightInput').value = weight.toFixed(1);
            updateWeightTotal();
        }
        
        function updateWeightTotal() {
            if (!currentWeightProduct) return;
            
            const weight = parseFloat(document.getElementById('weightInput').value) || 0;
            const total = Math.round(weight * currentWeightProduct.price);
            document.getElementById('weightTotalPrice').textContent = total.toLocaleString() + ' ₸';
        }
        
        function confirmWeight() {
            if (!currentWeightProduct) return;
            
            const weight = parseFloat(document.getElementById('weightInput').value) || 0.5;
            const cart = getCart();
            
            const existingIndex = cart.findIndex(item => item.id === currentWeightProduct.id);
            
            if (existingIndex >= 0) {
                cart[existingIndex].quantity += weight;
            } else {
                cart.push({
                    id: currentWeightProduct.id,
                    name: currentWeightProduct.name,
                    price: currentWeightProduct.price,
                    quantity: weight,
                    is_weighted: 1,
                    image_url: currentWeightProduct.image_url
                });
            }
            
            saveCart(cart);
            closeWeightModal();
            showToast(`Добавлено ${weight} кг`);
        }
        
        // Event listener for weight input
        document.getElementById('weightInput')?.addEventListener('input', updateWeightTotal);
        
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

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.reload());
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateAllUI();
        });
    </script>
</body>
</html>