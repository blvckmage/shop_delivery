<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 Корзина - Kazyna Market</title>
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
                        'bounce-gentle': 'bounceGentle 2s infinite'
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
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-20px); }
            }
            @keyframes bounceGentle {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            .animate-fade-in { animation: fadeIn 0.5s ease-in; }
            .animate-bounce-gentle { animation: bounceGentle 2s infinite; }
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
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Kazyna Market
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
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
                    <a href="/catalog" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        🛍️ Каталог
                    </a>
                    <a href="/cart" class="block px-4 py-3 text-blue-600 font-semibold rounded-lg bg-blue-50 hover:bg-blue-100">
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
                        <a href="/login" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            Войти
                        </a>
                        <a href="/register" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            Регистрация
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="mr-4">🛒</span> Ваша корзина
                </h1>
                <p class="text-gray-600 text-lg">Проверьте свои товары перед оформлением заказа</p>
            </div>

            <?php if (empty($cart)): ?>
                <!-- Empty Cart -->
                <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-16 text-center shadow-lg">
                    <div class="text-8xl mb-6">🛒</div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Корзина пуста</h2>
                    <p class="text-gray-600 mb-8 text-lg">Добавьте товары из нашего каталога, чтобы оформить заказ</p>
                    <a href="/catalog" class="inline-flex items-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg animate-bounce-gentle">
                        <span class="mr-2">🛍️</span> Начать покупки
                    </a>
                </div>
            <?php else: ?>
                <!-- Cart Items -->
                <div class="space-y-6">
                    <?php
                    $total = 0;
                    foreach ($cart as $index => $item):
                        // Skip items without price
                        if (empty($item['price']) || empty($item['name'])) {
                            continue;
                        }
                        $itemTotal = $item['price'] * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                        <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 animate-fade-in"
                             style="animation-delay: <?php echo $index * 0.1; ?>s"
                             data-cart-item-id="<?php echo $item['id']; ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-6">
                                    <!-- Product Image Placeholder -->
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-yellow-100 rounded-2xl flex items-center justify-center">
                                        <span class="text-2xl">
                                            <?php echo mb_substr($item['name'] ?? '', 0, 1, 'UTF-8'); ?>
                                        </span>
                                    </div>

                                    <!-- Product Info -->
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($item['name'] ?? ''); ?></h3>
                                        <p class="text-gray-600">
                                            <span class="item-price"><?php echo htmlspecialchars($item['price'] ?? '0'); ?></span> ₸
                                            <?php if (!empty($item['weight_unit'])): ?>
                                                за <?php 
                                                    $unit = $item['weight_unit'];
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
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-8">
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        <button onclick="decreaseQuantity(this)" class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-colors duration-200">
                                            <span class="text-lg font-bold">-</span>
                                        </button>
                                        <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold min-w-[3rem] text-center item-quantity">
                                            <?php echo htmlspecialchars($item['quantity']); ?>
                                        </span>
                                        <button onclick="increaseQuantity(this)" class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-colors duration-200">
                                            <span class="text-lg font-bold">+</span>
                                        </button>
                                    </div>

                                    <!-- Item Total -->
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600 item-total">
                                            <?php echo htmlspecialchars($itemTotal); ?> ₸
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="text-red-500 hover:text-red-700 transition-colors duration-200">
                                        <span class="text-xl">🗑️</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Order Summary -->
                    <div class="bg-gradient-to-r from-green-400/20 to-blue-400/20 backdrop-blur-sm rounded-3xl p-8 shadow-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Итого к оплате</h3>
                                <p class="text-gray-600">Доставка рассчитывается отдельно</p>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-bold text-green-600 mb-2" id="cart-total">
                                    <?php echo htmlspecialchars($total); ?> ₸
                                </div>
                                <div class="text-sm text-gray-500" id="cart-count-text">
                                    <?php echo count($cart); ?> товар<?php echo count($cart) > 1 ? 'а' : ''; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/catalog" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-2xl font-semibold transition-colors duration-200">
                                ← Продолжить покупки
                            </a>
                            <a href="/order" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-3 rounded-2xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg animate-pulse-soft">
                                ✅ Оформить заказ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Kazyna Market. Все права защищены.</p>
        </div>
    </footer>

    <script>
        // Update cart count
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartCount = document.getElementById('cart-count');
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
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
            loadCartItems();
            
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });

        // Load cart items dynamically
        async function loadCartItems() {
            try {
                const response = await fetch('/api/cart');
                if (response.ok) {
                    const cart = await response.json();
                    // For now, the cart is rendered server-side
                    // In a more dynamic version, we could re-render the items here
                }
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        }

        // Update quantity
        async function updateQuantity(productId, newQuantity) {
            // Validation
            if (!productId || productId <= 0) {
                showNotification('Ошибка: недопустимый товар', 'error');
                return;
            }
            
            // Convert to integer if string
            newQuantity = parseInt(newQuantity, 10);
            
            if (!Number.isInteger(newQuantity) || newQuantity < 0) {
                showNotification('Ошибка: недопустимое количество', 'error');
                return;
            }

            try {
                const response = await fetch('/api/cart/update', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        product_id: parseInt(productId), 
                        quantity: parseInt(newQuantity) 
                    })
                });

                if (response.ok) {
                    if (newQuantity === 0) {
                        // Remove the item from DOM
                        const cartItem = document.querySelector(`[data-cart-item-id="${productId}"]`);
                        if (cartItem) {
                            cartItem.style.animation = 'fadeOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                cartItem.remove();
                                
                                // Check if cart is now empty
                                const remainingItems = document.querySelectorAll('[data-cart-item-id]').length;
                                if (remainingItems === 0) {
                                    location.reload(); // Reload to show empty cart message
                                } else {
                                    // Only update totals if there are still items
                                    updateCartTotal();
                                    updateCartCount();
                                }
                            }, 300);
                        }
                        showNotification('Товар удален из корзины', 'success');
                    } else {
                        // Update the item display
                        const cartItem = document.querySelector(`[data-cart-item-id="${productId}"]`);
                        if (cartItem) {
                            const quantityEl = cartItem.querySelector('.item-quantity');
                            const priceEl = cartItem.querySelector('.item-price');
                            const totalEl = cartItem.querySelector('.item-total');
                            
                            if (quantityEl && priceEl) {
                                const price = parseFloat(priceEl.textContent);
                                quantityEl.textContent = newQuantity;
                                totalEl.textContent = (price * newQuantity) + ' ₸';
                            }
                        }
                        showNotification('Количество обновлено', 'success');
                        updateCartTotal();
                        updateCartCount();
                    }
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Ошибка при обновлении количества', 'error');
                }
            } catch (error) {
                showNotification('Ошибка сети: ' + error.message, 'error');
            }
        }

        // Decrease quantity - called from button click
        function decreaseQuantity(button) {
            // Get the closest cart item container
            const cartItem = button.closest('[data-cart-item-id]');
            if (!cartItem) {
                showNotification('Ошибка: товар не найден', 'error');
                return;
            }

            const productId = cartItem.getAttribute('data-cart-item-id');
            const quantityEl = cartItem.querySelector('.item-quantity');
            const currentQuantity = parseInt(quantityEl.textContent, 10);
            const newQuantity = currentQuantity - 1;

            updateQuantity(productId, newQuantity);
        }

        // Increase quantity - called from button click
        function increaseQuantity(button) {
            // Get the closest cart item container
            const cartItem = button.closest('[data-cart-item-id]');
            if (!cartItem) {
                showNotification('Ошибка: товар не найден', 'error');
                return;
            }

            const productId = cartItem.getAttribute('data-cart-item-id');
            const quantityEl = cartItem.querySelector('.item-quantity');
            const currentQuantity = parseInt(quantityEl.textContent, 10);
            const newQuantity = currentQuantity + 1;

            updateQuantity(productId, newQuantity);
        }

        // Update cart total and item count
        function updateCartTotal() {
            let total = 0;
            let itemCount = 0;
            const cartItems = document.querySelectorAll('[data-cart-item-id]');
            
            cartItems.forEach(item => {
                const quantityEl = item.querySelector('.item-quantity');
                const priceEl = item.querySelector('.item-price');
                
                if (quantityEl && priceEl) {
                    const quantity = parseInt(quantityEl.textContent) || 0;
                    const price = parseFloat(priceEl.textContent) || 0;
                    
                    // Validate parsed values
                    if (!isNaN(quantity) && !isNaN(price) && quantity > 0 && price > 0) {
                        total += price * quantity;
                        itemCount += quantity;
                    }
                }
            });
            
            const totalEl = document.getElementById('cart-total');
            const countEl = document.getElementById('cart-count-text');
            
            if (totalEl) {
                totalEl.textContent = total.toFixed(2) + ' ₸';
            }
            if (countEl) {
                const itemWord = itemCount === 1 ? 'товар' : (itemCount % 10 === 1 && itemCount % 100 !== 11 ? 'товар' : 'товара');
                countEl.textContent = itemCount + ' ' + itemWord;
            }
        }

        // Remove from cart
        async function removeFromCart(productId) {
            // Validation
            if (!productId || productId <= 0) {
                showNotification('Ошибка: недопустимый товар', 'error');
                return;
            }

            if (!confirm('Вы уверены, что хотите удалить этот товар из корзины?')) return;

            try {
                const response = await fetch(`/api/cart/${parseInt(productId)}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    // Remove the item from DOM
                    const cartItem = document.querySelector(`[data-cart-item-id="${productId}"]`);
                    if (cartItem) {
                        cartItem.style.animation = 'fadeOut 0.3s ease-out forwards';
                        setTimeout(() => {
                            cartItem.remove();
                            
                            // Check if cart is empty and show empty message
                            const remainingItems = document.querySelectorAll('[data-cart-item-id]').length;
                            if (remainingItems === 0) {
                                location.reload(); // Reload to show empty cart message
                            } else {
                                // Only update totals if there are still items in the cart
                                updateCartTotal();
                                updateCartCount();
                            }
                        }, 300);
                    }
                    showNotification('Товар удален из корзины', 'success');
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Ошибка при удалении товара', 'error');
                }
            } catch (error) {
                showNotification('Ошибка сети: ' + error.message, 'error');
            }
        }

        // Add notification for cart updates
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
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
    </script>
</body>
</html>
