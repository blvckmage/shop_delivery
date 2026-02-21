<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог - Kazyna Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b',
                        accent: '#f59e0b'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'scale-in': 'scaleIn 0.3s ease-out',
                        'pulse-soft': 'pulseSoft 2s infinite'
                    }
                }
            }
        }

        // Custom animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes scaleIn {
                from { opacity: 0; transform: scale(0.9); }
                to { opacity: 1; transform: scale(1); }
            }
            @keyframes pulseSoft {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            .animate-fade-in { animation: fadeIn 0.5s ease-in; }
            .animate-scale-in { animation: scaleIn 0.3s ease-out; }
            .animate-pulse-soft { animation: pulseSoft 2s infinite; }
            
            /* Remove focus outline on buttons */
            button:focus { outline: none; }
            button:focus-visible { outline: 2px solid rgba(34, 197, 94, 0.5); outline-offset: 2px; }
        `;
        document.head.appendChild(style);
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-yellow-50 min-h-screen" data-is-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Kazyna Market
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium relative">
                        🛒 Корзина
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </a>
                    <?php if ($isLoggedIn): ?>
                    <a href="/orders" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">📦 Заказы</a>
                    <a href="/profile" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">👤 Профиль</a>
                    <a href="/chat" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">💬 Чат</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                            <a href="/admin" class="text-purple-700 hover:text-purple-600 transition-colors duration-200 font-medium">⚙️ Админ</a>
                        <?php endif; ?>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</span>
                            <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                Выход
                            </button>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Войти
                        </a>
                        <a href="/register" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Регистрация
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl">☰</button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200">
                <div class="px-4 py-4 space-y-2">
                    <a href="/catalog" class="block px-4 py-3 text-blue-600 font-semibold rounded-lg bg-blue-50 hover:bg-blue-100">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        🛒 Корзина
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <a href="/orders" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">📦 Заказы</a>
                        <a href="/profile" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">👤 Профиль</a>
                        <a href="/chat" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">💬 Чат</a>
                        <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                            <a href="/courier" class="block px-4 py-3 text-orange-700 hover:bg-orange-50 rounded-lg transition-colors">🚚 Курьер</a>
                        <?php endif; ?>
                        <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                            <a href="/admin" class="block px-4 py-3 text-purple-700 hover:bg-purple-50 rounded-lg transition-colors">⚙️ Админ</a>
                        <?php endif; ?>
                        <hr class="my-2">
                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-600 mb-3">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Пользователь'); ?>!</p>
                            <button onclick="logout()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                                Выход
                            </button>
                        </div>
                    <?php else: ?>
                        <hr class="my-2">
                        <a href="/login" class="block w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg transition-colors text-center font-medium">
                            Войти
                        </a>
                        <a href="/register" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg transition-colors text-center font-medium">
                            Регистрация
                        </a>
                    <?php endif; ?>
                </div>
            </div>

    <main class="container mx-auto px-4 py-8 animate-fade-in">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">🛍️ Наш Каталог</h1>
            <p class="text-gray-600 text-lg">Откройте для себя разнообразие свежих продуктов и качественных товаров</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar with filters -->
            <div class="lg:col-span-1">
                <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-6 shadow-lg sticky top-24">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="mr-2">🏷️</span>Категории
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/catalog"
                               class="block p-3 rounded-xl transition-all duration-200 <?php echo !isset($_GET['category']) ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-gray-100 text-gray-700'; ?>">
                                🏠 Все товары
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="/catalog?category=<?php echo $category['id']; ?>"
                                   class="block p-3 rounded-xl transition-all duration-200 <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-gray-100 text-gray-700'; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Search -->
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">🔍</span>Поиск
                        </h4>
                        <form method="GET" class="space-y-3">
                            <input type="text" name="search"
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                   placeholder="Название товара..."
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                Искать
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:col-span-3">
                <?php if (empty($products)): ?>
                    <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-12 text-center shadow-lg">
                        <div class="text-6xl mb-4">📦</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Товары не найдены</h3>
                        <p class="text-gray-600 mb-6">Попробуйте изменить параметры поиска или выберите другую категорию</p>
                        <a href="/catalog" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-colors duration-200">
                            Показать все товары
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($products as $index => $product): ?>
                            <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 animate-scale-in relative"
                                 style="animation-delay: <?php echo $index * 0.1; ?>s"
                                 data-product-id="<?php echo $product['id']; ?>">
                                <!-- Favorite Button -->
                                <button onclick="toggleFavorite(<?php echo $product['id']; ?>, this)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl transition-colors duration-200" title="Добавить в избранное">
                                    ♥
                                </button>
                                <!-- Product Image -->
                                <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-yellow-100 rounded-2xl mb-4 flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($product['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover rounded-2xl">
                                    <?php else: ?>
                                        <span class="text-4xl">
                                            <?php
                                            $firstChar = mb_substr($product['name'], 0, 1, 'UTF-8');
                                            echo $firstChar;
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Product Info -->
                                <div class="space-y-3">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800 leading-tight"><?php echo htmlspecialchars($product['name']); ?></h4>
                                        <?php if (!empty($product['category_name'])): ?>
                                            <p class="text-sm text-blue-600 font-medium"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <p class="text-gray-600 text-sm leading-relaxed"><?php echo htmlspecialchars($product['description']); ?></p>

                                    <div class="flex items-center justify-between">
                                        <div class="text-2xl font-bold text-green-600">
                                            <?php echo htmlspecialchars($product['price']); ?> ₸
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php if (!empty($product['weight_unit'])): ?>
                                                за <?php 
                                                    $unit = $product['weight_unit'];
                                                    // If it's a number (grams), convert properly
                                                    if (is_numeric($unit)) {
                                                        echo $unit >= 1000 ? ($unit / 1000) . 'кг' : $unit . 'г';
                                                    } else {
                                                        // It's already a formatted string like "1 л"
                                                        echo htmlspecialchars($unit);
                                                    }
                                                ?>
                                            <?php else: ?>
                                                за шт.
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add to Cart / Quantity Controls -->
                                <?php
                                $cartItem = null;
                                $isLoggedIn = isset($_SESSION['user']);
                                $cart = $_SESSION['cart'] ?? [];
                                foreach ($cart as $item) {
                                    if ($item['id'] == $product['id']) {
                                        $cartItem = $item;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($cartItem): ?>
                                    <div class="w-full mt-6 flex items-center justify-center space-x-4 bg-green-50 p-3 rounded-xl border-2 border-green-200">
                                        <button onclick="updateQuantityFromCatalog(<?php echo $product['id']; ?>, <?php echo $cartItem['quantity'] - 1; ?>)"
                                                class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-colors duration-200 font-bold">
                                            -
                                        </button>
                                        <span class="bg-white text-green-800 px-4 py-2 rounded-lg font-semibold min-w-[3rem] text-center border-2 border-green-200">
                                            <?php echo $cartItem['quantity']; ?>
                                        </span>
                                        <button onclick="updateQuantityFromCatalog(<?php echo $product['id']; ?>, <?php echo $cartItem['quantity'] + 1; ?>)"
                                                class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-colors duration-200 font-bold">
                                            +
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)"
                                            class="w-full mt-6 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg animate-pulse-soft">
                                        ➕ Добавить в корзину
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Auth Modal -->
    <div id="authModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl p-8 max-w-md mx-4 shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">🔐</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Требуется авторизация</h2>
                <p class="text-gray-600">Чтобы начать покупки Вам нужно зарегистрироваться или войти в свой аккаунт</p>
            </div>
            <div class="space-y-3">
                <a href="/login" class="block w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 text-center">
                    Войти
                </a>
                <a href="/register" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 text-center">
                    Регистрация
                </a>
            </div>
            <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Kazyna Market. Все права защищены.</p>
        </div>
    </footer>

    <script>
        // Update cart count
        async function updateCartCount() {
            const isLoggedIn = document.body.dataset.isLoggedIn === 'true';
            let count = 0;

            if (isLoggedIn) {
                try {
                    const response = await fetch('/api/cart');
                    if (response.ok) {
                        const cart = await response.json();
                        count = cart.reduce((sum, item) => sum + item.quantity, 0);
                    }
                } catch (error) {
                    console.error('Error fetching cart:', error);
                }
            } else {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                count = cart.reduce((sum, item) => sum + item.quantity, 0);
            }

            const cartCount = document.getElementById('cart-count');
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
                if (!isLoggedIn) {
                    localStorage.removeItem('cart');
                }
            }
        }

        // Logout function
        function logout() {
            fetch('/api/auth/logout', { method: 'POST' })
                .then(() => location.reload());
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            updateCartCount();
            syncCartIfNeeded();
        });

        // Sync cart from localStorage to session if user is logged in
        function syncCartIfNeeded() {
            const isLoggedIn = document.body.dataset.isLoggedIn === 'true';
            const cart = localStorage.getItem('cart');
            if (isLoggedIn && cart) {
                fetch('/api/cart/sync', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: cart
                }).then(() => {
                    localStorage.removeItem('cart');
                    updateCartCount();
                });
            }
        }

        // Add to cart function with animation
        async function addToCart(productId, name, price) {
            const isLoggedIn = document.body.dataset.isLoggedIn === 'true';
            if (!isLoggedIn) {
                showAuthModal();
                return;
            }

            // Validation
            if (!productId || productId <= 0) {
                showNotification('Ошибка: недопустимый товар', 'error');
                return;
            }

            // Prevent multiple clicks by disabling buttons temporarily
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (productCard) {
                const buttons = productCard.querySelectorAll('button');
                buttons.forEach(btn => btn.disabled = true);
            }

            try {
                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        product_id: parseInt(productId), 
                        quantity: 1 
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    updateCartCount();
                    showNotification(`✅ ${name} добавлен в корзину!`, 'success');
                    // Update UI dynamically - get actual quantity from response
                    const actualQuantity = result.cart ? result.cart.find(item => item.id == productId)?.quantity || 1 : 1;
                    updateProductCard(parseInt(productId), actualQuantity);
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Ошибка при добавлении в корзину', 'error');
                    // Re-enable buttons on error
                    if (productCard) {
                        const buttons = productCard.querySelectorAll('button');
                        buttons.forEach(btn => btn.disabled = false);
                    }
                }
            } catch (error) {
                showNotification('Ошибка сети: ' + error.message, 'error');
                // Re-enable buttons on error
                if (productCard) {
                    const buttons = productCard.querySelectorAll('button');
                    buttons.forEach(btn => btn.disabled = false);
                }
            }
        }

        // Update quantity from catalog
        async function updateQuantityFromCatalog(productId, newQuantity) {
            const isLoggedIn = document.body.dataset.isLoggedIn === 'true';
            if (!isLoggedIn) {
                showAuthModal();
                return;
            }

            // Validation
            if (!productId || productId <= 0) {
                showNotification('Ошибка: недопустимый товар', 'error');
                return;
            }

            if (!Number.isInteger(newQuantity) || newQuantity < 0) {
                showNotification('Количество не может быть отрицательным', 'error');
                return;
            }

            // Prevent multiple clicks by disabling buttons temporarily
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (productCard) {
                const buttons = productCard.querySelectorAll('button');
                buttons.forEach(btn => btn.disabled = true);
            }

            try {
                const response = await fetch('/api/cart/update', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: parseInt(productId), quantity: parseInt(newQuantity) })
                });

                if (response.ok) {
                    updateCartCount();
                    if (newQuantity > 0) {
                        showNotification('Количество обновлено', 'success');
                        updateProductCard(parseInt(productId), parseInt(newQuantity));
                    } else {
                        showNotification('Товар удален из корзины', 'success');
                        revertProductCard(parseInt(productId));
                    }
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Ошибка при обновлении количества', 'error');
                    // Re-enable buttons on error
                    if (productCard) {
                        const buttons = productCard.querySelectorAll('button');
                        buttons.forEach(btn => btn.disabled = false);
                    }
                }
            } catch (error) {
                showNotification('Ошибка сети: ' + error.message, 'error');
                // Re-enable buttons on error
                if (productCard) {
                    const buttons = productCard.querySelectorAll('button');
                    buttons.forEach(btn => btn.disabled = false);
                }
            }
        }

        // Update product card UI dynamically
        function updateProductCard(productId, quantity) {
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (!productCard) return;

            const buttonContainer = productCard.querySelector('.mt-6');
            if (!buttonContainer) return;

            // Ensure quantity is a valid integer
            const qty = parseInt(quantity) || 1;

            // Create new button group HTML
            const newHTML = `
                <div class="w-full flex items-center justify-center space-x-4 bg-green-50 p-3 rounded-xl border-2 border-green-200">
                    <button onclick="updateQuantityFromCatalog(${productId}, ${qty - 1})"
                            class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-colors duration-200 font-bold"
                            title="Уменьшить количество">
                        -
                    </button>
                    <span class="bg-white text-green-800 px-4 py-2 rounded-lg font-semibold min-w-[3rem] text-center border-2 border-green-200">
                        ${qty}
                    </span>
                    <button onclick="updateQuantityFromCatalog(${productId}, ${qty + 1})"
                            class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-colors duration-200 font-bold"
                            title="Увеличить количество">
                        +
                    </button>
                </div>
            `;
            
            buttonContainer.innerHTML = newHTML;
        }

        // Revert product card to "Add to Cart" button
        function revertProductCard(productId) {
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (!productCard) return;

            const buttonContainer = productCard.querySelector('.mt-6');
            if (!buttonContainer) return;

            // Get product name from the card
            const productName = productCard.querySelector('h4') ? productCard.querySelector('h4').textContent : 'Товар';
            const productPrice = productCard.querySelector('.text-2xl.font-bold.text-green-600') ? 
                productCard.querySelector('.text-2xl.font-bold.text-green-600').textContent.replace(' ₸', '') : '0';

            // Revert to add to cart button (green color)
            const newHTML = `
                <button onclick="addToCart(${productId}, '${productName.replace(/'/g, "\\'")}', ${productPrice})"
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                    ➕ Добавить в корзину
                </button>
            `;
            
            buttonContainer.innerHTML = newHTML;
        }

        // Show auth modal
        function showAuthModal() {
            const modal = document.getElementById('authModal');
            const modalContent = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Close auth modal
        function closeAuthModal() {
            const modal = document.getElementById('authModal');
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Toggle favorite
        async function toggleFavorite(productId, button) {
            const isLoggedIn = document.body.dataset.isLoggedIn === 'true';
            if (!isLoggedIn) {
                showAuthModal();
                return;
            }

            try {
                const response = await fetch(`/api/profile/favorites/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        button.classList.toggle('text-red-500');
                        button.classList.toggle('text-gray-400');
                        showNotification('Товар добавлен в избранное', 'success');
                    }
                } else if (response.status === 409) {
                    // Already in favorites, remove
                    const removeResponse = await fetch(`/api/profile/favorites/${productId}`, {
                        method: 'DELETE'
                    });
                    if (removeResponse.ok) {
                        button.classList.remove('text-red-500');
                        button.classList.add('text-gray-400');
                        showNotification('Товар удален из избранного', 'success');
                    }
                }
            } catch (error) {
                console.error('Error toggling favorite:', error);
                showNotification('Ошибка при добавлении в избранное', 'error');
            }
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `<span class="font-medium">${message}</span>`;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (menuBtn && mobileMenu) {
                menuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking on a link
                const menuLinks = mobileMenu.querySelectorAll('a, button');
                menuLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Don't close for logout button as it will redirect
                        if (this.textContent !== 'Выход') {
                            mobileMenu.classList.add('hidden');
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
