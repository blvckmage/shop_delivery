<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Корзина - Delivery</title>
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
<body class="gradient-hero min-h-screen pb-32 md:pb-0">
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

    <!-- Cart Content -->
    <section class="px-4 py-6 pb-40 md:pb-6">
        <div class="container mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Корзина</h1>
            
            <div id="cart-items" class="space-y-3">
                <!-- Cart items will be rendered here -->
            </div>
            
            <div id="empty-cart" class="hidden text-center py-12">
                <svg class="w-20 h-20 text-warm-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-500 mb-4">Корзина пуста</p>
                <a href="/catalog" class="inline-block btn-primary text-white px-6 py-3 rounded-full font-medium">
                    Перейти в каталог
                </a>
            </div>
        </div>
    </section>

    <!-- Order Summary (Fixed at bottom on mobile) -->
    <div id="order-summary" class="fixed bottom-0 left-0 right-0 md:relative md:bottom-auto bg-white border-t border-gray-100 p-4 z-40 hidden">
        <div class="container mx-auto max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Итого:</span>
                <span id="total-price" class="text-xl font-bold text-gray-900">0 ₸</span>
            </div>
            <button onclick="checkout()" class="w-full btn-primary text-white py-4 rounded-2xl font-semibold text-lg">
                Оформить заказ
            </button>
        </div>
    </div>

    <!-- Mobile Bottom Navigation - hidden on cart page when has items -->
    <nav id="bottom-nav" class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-gray-100 bottom-nav z-30">
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
        let cart = [];

        function renderCart() {
            cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const container = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const orderSummary = document.getElementById('order-summary');
            const bottomNav = document.getElementById('bottom-nav');
            
            if (cart.length === 0) {
                container.innerHTML = '';
                emptyCart.classList.remove('hidden');
                orderSummary.classList.add('hidden');
                if (bottomNav) bottomNav.classList.remove('hidden');
                return;
            }
            
            emptyCart.classList.add('hidden');
            orderSummary.classList.remove('hidden');
            if (bottomNav) bottomNav.classList.add('hidden');
            
            container.innerHTML = cart.map((item, index) => {
                const isWeighted = item.is_weighted;
                const displayQty = isWeighted ? `${item.quantity} кг` : item.quantity;
                const itemTotal = Math.round(item.price * item.quantity);
                const qtyStep = isWeighted ? 0.1 : 1;
                
                return `
                <div class="bg-white rounded-2xl p-4 card-shadow flex gap-4">
                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-warm-50 to-warm-100 flex-shrink-0 overflow-hidden">
                        ${item.image_url 
                            ? `<img src="${item.image_url}" alt="${item.name}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-warm-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                               </div>`
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-gray-900 truncate">${item.name}</h3>
                        <p class="text-warm-600 font-bold mt-1">${item.price} ₸ ${isWeighted ? '/ кг' : ''}</p>
                        <p class="text-sm text-gray-500 mt-1">Сумма: <span class="font-semibold text-gray-700">${itemTotal.toLocaleString('ru-RU')} ₸</span></p>
                        
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center bg-warm-50 rounded-full">
                                <button onclick="updateQuantity(${index}, -${qtyStep})" class="w-8 h-8 flex items-center justify-center text-warm-600 hover:bg-warm-100 rounded-full transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input type="number" value="${item.quantity}" min="${isWeighted ? 0.1 : 1}" step="${qtyStep}"
                                       onchange="setQuantity(${index}, this.value)"
                                       class="w-12 text-center bg-transparent text-gray-900 font-medium text-sm outline-none">
                                <button onclick="updateQuantity(${index}, ${qtyStep})" class="w-8 h-8 flex items-center justify-center text-warm-600 hover:bg-warm-100 rounded-full transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <button onclick="removeItem(${index})" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `}).join('');
            
            updateTotal();
        }

        function updateQuantity(index, delta) {
            const isWeighted = cart[index].is_weighted;
            const minQty = isWeighted ? 0.1 : 1;
            let newQty = cart[index].quantity + delta;
            
            // Удаляем товар если количество меньше минимального
            if (newQty < minQty) {
                removeItem(index);
                return;
            }
            
            // Округляем для весовых товаров
            if (isWeighted) {
                newQty = Math.round(newQty * 10) / 10;
            }
            
            cart[index].quantity = newQty;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }

        function setQuantity(index, value) {
            const isWeighted = cart[index].is_weighted;
            const minQty = isWeighted ? 0.1 : 1;
            
            let qty;
            if (isWeighted) {
                qty = parseFloat(value) || minQty;
                qty = Math.round(qty * 10) / 10;
            } else {
                qty = parseInt(value) || 1;
            }
            
            qty = Math.max(minQty, qty);
            
            cart[index].quantity = qty;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
            showToast('Товар удален');
        }

        function updateTotal() {
            const total = cart.reduce((sum, item) => sum + Math.round(item.price * item.quantity), 0);
            document.getElementById('total-price').textContent = total.toLocaleString('ru-RU') + ' ₸';
        }
        
        function getCartItemCount() {
            // Штучные товары считаем по количеству, весовые - как 1 позицию
            return cart.reduce((sum, item) => {
                if (item.is_weighted) {
                    return sum + 1; // Весовой товар = 1 позиция
                } else {
                    return sum + Math.round(item.quantity); // Штучный = количество штук
                }
            }, 0);
        }

        function checkout() {
            if (cart.length === 0) {
                showToast('Корзина пуста');
                return;
            }
            
            // Sync cart to server and redirect to order page
            fetch('/api/cart/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: cart })
            }).then(() => {
                window.location.href = '/order';
            }).catch(err => {
                console.error('Error syncing cart:', err);
                window.location.href = '/order';
            });
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

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.reload());
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderCart();
        });
    </script>
</body>
</html>