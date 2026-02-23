<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Delivery</title>
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
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Toast Notifications */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .toast { 
            min-width: 300px; padding: 16px 20px; margin-bottom: 10px; 
            border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out; display: flex; align-items: center; gap: 12px;
        }
        .toast-success { background: #10B981; color: white; }
        .toast-error { background: #EF4444; color: white; }
        .toast-info { background: #3B82F6; color: white; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        
        /* Skeleton Loading */
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        .skeleton-text { height: 16px; border-radius: 4px; margin-bottom: 8px; }
        .skeleton-title { height: 24px; width: 60%; border-radius: 4px; margin-bottom: 12px; }
        .skeleton-avatar { width: 40px; height: 40px; border-radius: 50%; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        
        /* Tab Transitions */
        .tab-content { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Button hover effects */
        .btn-hover { transition: all 0.2s ease; }
        .btn-hover:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        
        /* Image preview */
        .image-preview { width: 100%; height: 200px; border: 2px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; background: #f9fafb; }
        .image-preview:hover { border-color: #8b5cf6; }
        .image-preview img { max-width: 100%; max-height: 100%; object-fit: cover; }
        
        /* Modal animations */
        .modal-enter { animation: modalIn 0.2s ease-out; }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-pink-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">A</span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Админ панель</h1>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/" class="text-blue-600 hover:text-blue-800">← На сайт</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="text-orange-700 hover:text-orange-600 transition-colors duration-200 font-medium">🚚 Курьер</a>
                    <?php endif; ?>
                    <!-- Admin Notifications Bell -->
                    <div class="relative">
                        <button onclick="toggleNotifications()" class="text-gray-700 hover:text-purple-600 transition-colors duration-200 relative text-xl">
                            🔔
                            <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                    <span class="text-gray-600">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Админ'); ?>!</span>
                    <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Выход</button>
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl p-2">☰</button>
            </nav>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200 mt-4 rounded-xl">
                <div class="px-4 py-4 space-y-2">
                    <a href="/" class="block px-4 py-3 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">← На сайт</a>
                    <?php if (($user['role'] ?? 'user') === 'courier'): ?>
                        <a href="/courier" class="block px-4 py-3 text-orange-700 hover:bg-orange-50 rounded-lg transition-colors">🚚 Курьер</a>
                    <?php endif; ?>
                    <hr class="my-2">
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-600 mb-3">Привет, <?php echo htmlspecialchars($user['name'] ?? 'Админ'); ?>!</p>
                        <button onclick="logout()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Выход</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 md:py-8">
        <!-- Mobile Tabs Dropdown -->
        <div class="md:hidden mb-6">
            <select id="mobile-tab-select" onchange="showTab(this.value)" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="categories">📁 Категории</option>
                <option value="products">📦 Продукты</option>
                <option value="orders">🛒 Заказы</option>
                <option value="statistics">📊 Статистика</option>
                <option value="users">👤 Пользователи</option>
                <option value="courier-requests">🚚 Запросы курьеров</option>
                <option value="chat">💬 Чат</option>
            </select>
        </div>

        <!-- Desktop Tabs -->
        <div class="hidden md:block mb-8">
            <div class="border-b border-gray-200 overflow-x-auto">
                <nav class="-mb-px flex space-x-4 md:space-x-8 min-w-max">
                    <button onclick="showTab('categories')" id="tab-categories" class="border-b-2 border-purple-500 py-2 px-1 text-sm font-medium text-purple-600 whitespace-nowrap">
                        Категории
                    </button>
                    <button onclick="showTab('products')" id="tab-products" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Продукты
                    </button>
                    <button onclick="showTab('orders')" id="tab-orders" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Заказы
                    </button>
                    <button onclick="showTab('statistics')" id="tab-statistics" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Статистика
                    </button>
                    <button onclick="showTab('users')" id="tab-users" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Пользователи
                    </button>
                    <button onclick="showTab('courier-requests')" id="tab-courier-requests" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        🚚 Курьеры
                    </button>
                    <button onclick="showTab('chat')" id="tab-chat" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        💬 Чат
                    </button>
                </nav>
            </div>
        </div>

        <!-- Categories Tab -->
        <div id="categories-tab" class="tab-content">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Управление категориями</h2>
                    <button onclick="showCategoryModal()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                        + Добавить категорию
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Название</th>
                                <th class="px-4 py-2 text-left">Дата создания</th>
                                <th class="px-4 py-2 text-left">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="categories-table">
                            <?php foreach ($categories as $category): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?php echo $category['id']; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($category['name']); ?></td>
                                <td class="px-4 py-2"><?php echo substr($category['created_at'], 0, 10); ?></td>
                                <td class="px-4 py-2 space-x-2">
                                    <button onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')" class="text-blue-600 hover:text-blue-800">Изменить</button>
                                    <button onclick="deleteCategory(<?php echo $category['id']; ?>)" class="text-red-600 hover:text-red-800">Удалить</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products Tab -->
        <div id="products-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Управление продуктами</h2>
                    <button onclick="showProductModal()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                        + Добавить продукт
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Название</th>
                                <th class="px-4 py-2 text-left">Цена</th>
                                <th class="px-4 py-2 text-left">Категория</th>
                                <th class="px-4 py-2 text-left">Тип</th>
                                <th class="px-4 py-2 text-left">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="products-table">
                            <?php
                            $categoryMap = array_column($categories, 'name', 'id');
                            foreach ($products as $product):
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?php echo $product['id']; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td class="px-4 py-2"><?php echo $product['price']; ?> ₸</td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($categoryMap[$product['category_id']] ?? ''); ?></td>
                                <td class="px-4 py-2"><?php echo $product['is_weighted'] ? 'Весовой' : 'Штучный'; ?></td>
                                <td class="px-4 py-2 space-x-2">
                                    <button onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, <?php echo $product['category_id']; ?>, '<?php echo htmlspecialchars($product['description']); ?>', <?php echo $product['is_weighted']; ?>, '<?php echo $product['weight_unit'] ?? ''; ?>', '<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>')" class="text-blue-600 hover:text-blue-800">Изменить</button>
                                    <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="text-red-600 hover:text-red-800">Удалить</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Orders Tab -->
        <div id="orders-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Заказы</h2>
                    <button onclick="showArchiveModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        📦 Архив заказов
                    </button>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-6">Текущие заказы</h2>
                <?php $userMap = array_column($users, 'name', 'id'); ?>
                <div class="space-y-4">
                    <?php foreach ($orders as $order): ?>
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold">Заказ #<?php echo $order['id']; ?> - <?php echo htmlspecialchars($userMap[$order['user_id']] ?? 'Неизвестный пользователь'); ?></h3>
                                <p class="text-sm text-gray-600">Пользователь: <?php echo htmlspecialchars($userMap[$order['user_id']] ?? 'Неизвестный пользователь'); ?> (ID: <?php echo $order['user_id']; ?>)</p>
                                <p class="text-sm text-gray-600">Адрес: <?php echo htmlspecialchars($order['address']); ?></p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm font-medium text-gray-700">Статус:</label>
<select id="status-<?php echo $order['id']; ?>" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
<option value="" <?php echo $order['status'] === '' ? 'selected' : ''; ?>>Заказ создан</option>
                                            <option value="В_ПУТИ" <?php echo $order['status'] === 'В_ПУТИ' ? 'selected' : ''; ?>>Заказ в пути</option>
                                            <option value="ДОСТАВЛЕН" <?php echo $order['status'] === 'ДОСТАВЛЕН' ? 'selected' : ''; ?>>Заказ доставлен</option>
                                            <option value="ОТМЕНЕН" <?php echo $order['status'] === 'ОТМЕНЕН' ? 'selected' : ''; ?>>Заказ отменен</option>
                                        </select>
                                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm">
                                            Обновить
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Итого: <?php echo $order['total_price']; ?> ₸</p>
                                <div class="mt-4">
                                    <button onclick="showOrderDetails(<?php echo $order['id']; ?>, <?php echo htmlspecialchars(json_encode($order['items'])); ?>, '<?php echo htmlspecialchars($order['address']); ?>', '<?php echo htmlspecialchars($userMap[$order['user_id']] ?? 'Неизвестный пользователь'); ?>', <?php echo intval($order['delivery_included']); ?>, <?php echo intval($order['delivery_price']); ?>)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm">
                                        Показать состав
                                    </button>
                                    <button onclick="archiveOrder(<?php echo $order['id']; ?>)" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-md text-sm">
                                        📦 В архив
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500"><?php echo $order['created_at']; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Category Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 id="categoryModalTitle" class="text-lg font-bold mb-4">Добавить категорию</h3>
            <form id="categoryForm">
                <input type="hidden" id="categoryId" name="id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Название</label>
                    <input type="text" id="categoryName" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Отмена
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-500 rounded-md hover:bg-purple-600">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden overflow-y-auto">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto my-8">
            <h3 id="productModalTitle" class="text-lg font-bold mb-4">Добавить продукт</h3>
            <form id="productForm">
                <input type="hidden" id="productId" name="id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Название</label>
                    <input type="text" id="productName" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Описание</label>
                    <textarea id="productDescription" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Цена <span id="priceLabelHint" class="text-orange-600 font-normal">(за 1 кг для весовых товаров)</span>
                    </label>
                    <input type="number" step="0.01" id="productPrice" name="price" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    <p id="priceHint" class="text-xs text-gray-500 mt-1">Для весовых товаров укажите цену за 1 кг</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Категория</label>
                    <select id="productCategoryId" name="category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="productIsWeighted" name="is_weighted" value="1" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700">Весовой товар (цена за 1 кг)</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Изображение товара</label>
                    <input type="file" id="productImage" name="image" accept="image/*" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Выберите изображение для загрузки (PNG, JPG, GIF)</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeProductModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Отмена
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-500 rounded-md hover:bg-purple-600">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>

        <!-- Statistics Tab -->
        <div id="statistics-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Статистика продаж</h2>

                <!-- Period Selector -->
                <div class="mb-6">
                    <select id="statsPeriod" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="7">Последние 7 дней</option>
                        <option value="30">Последние 30 дней</option>
                        <option value="90">Последние 3 месяца</option>
                        <option value="365">Последний год</option>
                    </select>
                    <button onclick="loadStatistics()" class="ml-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        Обновить
                    </button>
                    <button onclick="exportReport('excel')" class="ml-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                        Экспорт Excel
                    </button>
                    <button onclick="exportReport('pdf')" class="ml-2 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md">
                        Экспорт PDF
                    </button>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Общая выручка</h3>
                        <p id="totalRevenue" class="text-3xl font-bold text-blue-600">0 ₸</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Количество заказов</h3>
                        <p id="totalOrders" class="text-3xl font-bold text-green-600">0</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-800">Средний чек</h3>
                        <p id="avgOrderValue" class="text-3xl font-bold text-yellow-600">0 ₸</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Активных пользователей</h3>
                        <p id="activeUsers" class="text-3xl font-bold text-purple-600">0</p>
                    </div>
                </div>

                <!-- Popular Products -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Популярные товары</h3>
                    <div id="popularProducts" class="space-y-4">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Revenue Chart Placeholder -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Динамика продаж</h3>
                    <div id="revenueChart" class="bg-gray-100 h-64 rounded-lg flex items-center justify-center">
                        <p class="text-gray-500">График продаж будет здесь</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Tab -->
        <div id="users-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Управление пользователями</h2>
                    <button onclick="showCreateUserModal()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                        + Добавить пользователя
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Имя</th>
                                <th class="px-4 py-2 text-left">Телефон</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Роль</th>
                                <th class="px-4 py-2 text-left">Дата регистрации</th>
                                <th class="px-4 py-2 text-left">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="users-table">
                            <?php foreach ($users as $user): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?php echo $user['id']; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                <td class="px-4 py-2">
                                    <select onchange="updateUserRole(<?php echo $user['id']; ?>, this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="user" <?php echo ($user['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>Пользователь</option>
                                        <option value="admin" <?php echo ($user['role'] ?? 'user') === 'admin' ? 'selected' : ''; ?>>Админ</option>
                                        <option value="courier" <?php echo ($user['role'] ?? 'user') === 'courier' ? 'selected' : ''; ?>>Курьер</option>
                                        <option value="banned" <?php echo ($user['role'] ?? 'user') === 'banned' ? 'selected' : ''; ?>>Забанен</option>
                                    </select>
                                </td>
                                <td class="px-4 py-2"><?php echo $user['created_at']; ?></td>
                                <td class="px-4 py-2 space-x-2">
                                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>', '<?php echo htmlspecialchars($user['phone']); ?>', '<?php echo htmlspecialchars($user['email'] ?? ''); ?>')" class="text-blue-600 hover:text-blue-800">Изменить</button>
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="text-red-600 hover:text-red-800">Удалить</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Archive Tab -->
        <div id="archive-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">📦 Архив заказов</h2>
                    <div class="flex space-x-3">
                        <button onclick="loadArchive()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            🔄 Обновить
                        </button>
                        <button onclick="exportArchive('excel')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            📊 Экспорт Excel
                        </button>
                        <button onclick="exportArchive('pdf')" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                            📄 Экспорт PDF
                        </button>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Фильтр по статусу:</label>
                            <select id="archiveStatusFilter" class="border border-gray-300 rounded-md px-3 py-2">
                                <option value="all">Все статусы</option>
                                <option value="DELIVERED">Выполненные</option>
                                <option value="CANCELED">Отмененные</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Период:</label>
                            <select id="archivePeriodFilter" class="border border-gray-300 rounded-md px-3 py-2">
                                <option value="30">Последние 30 дней</option>
                                <option value="90">Последние 3 месяца</option>
                                <option value="365">Последний год</option>
                                <option value="all">Все время</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Клиент</th>
                                <th class="px-4 py-2 text-left">Дата</th>
                                <th class="px-4 py-2 text-left">Статус</th>
                                <th class="px-4 py-2 text-left">Сумма</th>
                                <th class="px-4 py-2 text-left">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="archive-table">
                            <!-- Archive orders will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <div id="noArchiveOrders" class="text-center py-8 text-gray-500 hidden">
                    В архиве пока нет заказов
                </div>
            </div>
        </div>

        <!-- Courier Requests Tab -->
        <div id="courier-requests-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Requests List -->
                <div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">📋 Запросы курьеров</h2>
                        <div id="requestsList" class="space-y-4">
                            <!-- Requests will be loaded here -->
                        </div>
                        <div id="noRequests" class="text-center py-8 text-gray-500 hidden">
                            Нет активных запросов
                        </div>
                    </div>
                </div>

                <!-- Couriers Map -->
                <div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">🗺️ Карта курьеров</h2>
                        <div id="couriersMap" class="w-full h-96 rounded-xl mb-4"></div>
                        <div id="couriersList" class="space-y-2">
                            <!-- Couriers list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Tab -->
        <div id="chat-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[700px]">
                <!-- Users List -->
                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">💬 Чаты с пользователями</h2>
                    <div class="mb-4">
                        <input type="text" id="chatUserSearch" placeholder="Поиск пользователя..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                               onkeyup="filterChatUsers()">
                    </div>
                    <div id="chatUsersList" class="flex-1 overflow-y-auto space-y-2">
                        <!-- Chat users will be loaded here -->
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow flex flex-col">
                    <div class="p-4 border-b border-gray-200">
                        <h2 id="chatTitle" class="text-xl font-bold text-gray-800">Выберите чат</h2>
                        <p id="chatSubtitle" class="text-sm text-gray-600">Выберите пользователя для начала общения</p>
                    </div>
                    
                    <!-- Messages Container -->
                    <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-3">
                        <div class="text-center text-gray-500 py-8">
                            Выберите пользователя из списка слева
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div id="chatInputArea" class="p-4 border-t border-gray-200 hidden">
                        <form id="chatMessageForm" class="flex space-x-3">
                            <input type="text" id="chatMessageInput"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="Введите ваше сообщение..."
                                   maxlength="500">
                            <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                                Отправить
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">Максимальная длина сообщения: 500 символов</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="orderDetailsTitle" class="text-lg font-bold">Детали заказа</h3>
                    <button onclick="closeOrderDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <div id="orderDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Archive Modal -->
        <div id="archiveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">📦 Архив заказов</h3>
                    <button onclick="closeArchiveModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Фильтр по статусу:</label>
                            <select id="archiveModalStatusFilter" class="border border-gray-300 rounded-md px-3 py-2">
                                <option value="all">Все статусы</option>
                                <option value="DELIVERED">Выполненные</option>
                                <option value="CANCELED">Отмененные</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Период:</label>
                            <select id="archiveModalPeriodFilter" class="border border-gray-300 rounded-md px-3 py-2">
                                <option value="30">Последние 30 дней</option>
                                <option value="90">Последние 3 месяца</option>
                                <option value="365">Последний год</option>
                                <option value="all">Все время</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="loadArchiveModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                🔄 Обновить
                            </button>
                            <button onclick="exportArchiveModal('excel')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                📊 Экспорт Excel
                            </button>
                            <button onclick="exportArchiveModal('pdf')" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                                📄 Экспорт PDF
                            </button>
                        </div>
                    </div>
                </div>

                <div id="archiveModalContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 id="userModalTitle" class="text-lg font-bold mb-4">Изменить пользователя</h3>
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Имя</label>
                        <input type="text" id="userName" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Телефон</label>
                        <input type="tel" id="userPhone" name="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="userEmail" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Отмена
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-500 rounded-md hover:bg-purple-600">
                            Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create User Modal -->
        <div id="createUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Добавить пользователя</h3>
                <form id="createUserForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Имя *</label>
                        <input type="text" id="newUserName" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Телефон *</label>
                        <input type="tel" id="newUserPhone" name="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="newUserEmail" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Пароль *</label>
                        <input type="password" id="newUserPassword" name="password" required minlength="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Роль</label>
                        <select id="newUserRole" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="user">Пользователь</option>
                            <option value="courier">Курьер</option>
                            <option value="admin">Админ</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Отмена
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-500 rounded-md hover:bg-purple-600">
                            Создать
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Notifications Modal -->
    <div id="notificationsModal" class="fixed top-16 right-4 w-96 max-h-[70vh] bg-white rounded-2xl shadow-2xl z-50 hidden transform transition-all duration-300 scale-95 opacity-0 overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-purple-50 to-pink-50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <span class="mr-2">🔔</span> Уведомления админа
            </h3>
            <div class="flex items-center space-x-2">
                <button onclick="markAllAsRead()" class="text-xs text-purple-600 hover:text-purple-800 transition-colors">
                    Прочитать все
                </button>
                <button onclick="closeNotifications()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
        </div>
        <div id="notificationsList" class="max-h-96 overflow-y-auto">
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        let currentTab = 'categories';

        // Toast notification functions
        function alert(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };
            
            toast.innerHTML = `
                <i class="fas ${icons[type]} text-xl"></i>
                <span>${message}</span>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Image preview with compression
        function setupImagePreview() {
            const input = document.getElementById('productImage');
            if (!input) return;
            
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                // Check file size
                if (file.size > 5 * 1024 * 1024) {
                    alert('Файл слишком большой. Максимум 5MB', 'error');
                    this.value = '';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Compress image using canvas
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        // Calculate new dimensions (max 800px)
                        const maxSize = 800;
                        let width = img.width;
                        let height = img.height;
                        
                        if (width > height) {
                            if (width > maxSize) {
                                height = height * maxSize / width;
                                width = maxSize;
                            }
                        } else {
                            if (height > maxSize) {
                                width = width * maxSize / height;
                                height = maxSize;
                            }
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Compress to JPEG with 0.8 quality
                        const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                        
                        // Store compressed image in hidden input
                        let hiddenInput = document.getElementById('compressedImage');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.id = 'compressedImage';
                            hiddenInput.name = 'image_data';
                            input.parentNode.appendChild(hiddenInput);
                        }
                        hiddenInput.value = compressedDataUrl;
                        
                        // Show preview
                        let preview = document.getElementById('imagePreviewBox');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.id = 'imagePreviewBox';
                            preview.className = 'image-preview mt-2';
                            input.parentNode.appendChild(preview);
                        }
                        preview.innerHTML = `<img src="${compressedDataUrl}" alt="Preview">`;
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        // Skeleton loading templates
        function getSkeletonRequest() {
            return `
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div class="skeleton skeleton-title"></div>
                        <div class="skeleton skeleton-text" style="width: 60px;"></div>
                    </div>
                    <div class="skeleton skeleton-text" style="width: 70%;"></div>
                    <div class="skeleton skeleton-text" style="width: 50%; margin-bottom: 16px;"></div>
                    <div class="flex space-x-2">
                        <div class="skeleton" style="width: 100px; height: 40px; border-radius: 8px;"></div>
                        <div class="skeleton" style="width: 80px; height: 40px; border-radius: 8px;"></div>
                    </div>
                </div>
            `;
        }

        function getSkeletonCourier() {
            return `
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="skeleton skeleton-text" style="width: 120px; height: 18px; margin-bottom: 4px;"></div>
                            <div class="skeleton skeleton-text" style="width: 100px; height: 14px;"></div>
                            <div class="skeleton skeleton-text" style="width: 150px; height: 12px; margin-top: 4px;"></div>
                        </div>
                        <div class="skeleton skeleton-text" style="width: 60px; height: 24px; border-radius: 12px;"></div>
                    </div>
                </div>
            `;
        }

        function getSkeletonOrder() {
            return `
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="skeleton skeleton-title"></div>
                            <div class="skeleton skeleton-text" style="width: 80%;"></div>
                            <div class="skeleton skeleton-text" style="width: 60%;"></div>
                            <div class="skeleton skeleton-text" style="width: 40%; margin-top: 8px;"></div>
                        </div>
                        <div class="skeleton skeleton-text" style="width: 80px; height: 16px;"></div>
                    </div>
                </div>
            `;
        }

        // Initialize image preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            setupImagePreview();
            
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                    }
                });
            }
        });

        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^=tab-]').forEach(el => {
                el.classList.remove('border-purple-500', 'text-purple-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            document.getElementById(tab + '-tab').classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tab).classList.add('border-purple-500', 'text-purple-600');
            currentTab = tab;
            
            // Load data for specific tabs
            if (tab === 'courier-requests') {
                loadCourierRequests();
                loadCouriersMap();
            } else if (tab === 'statistics') {
                loadStatistics();
            } else if (tab === 'archive') {
                loadArchive();
            }
        }

        function logout() {
            fetch('/api/auth/logout', { method: 'POST' }).then(() => location.href = '/');
        }

        // Category functions
        function showCategoryModal(categoryId = null, name = '') {
            document.getElementById('categoryId').value = categoryId || '';
            document.getElementById('categoryName').value = name;
            document.getElementById('categoryModalTitle').textContent = categoryId ? 'Изменить категорию' : 'Добавить категорию';
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function editCategory(id, name) {
            showCategoryModal(id, name);
        }

        function deleteCategory(id) {
            if (confirm('Вы уверены, что хотите удалить эту категорию?')) {
                fetch(`/api/admin/categories/${id}`, { method: 'DELETE' })
                    .then(() => location.reload());
            }
        }

        // Product functions
        function showProductModal(productId = null, name = '', price = '', categoryId = '', description = '', isWeighted = 0, weightUnit = '', imageUrl = '') {
            document.getElementById('productId').value = productId || '';
            document.getElementById('productName').value = name;
            document.getElementById('productPrice').value = price;
            document.getElementById('productCategoryId').value = categoryId;
            document.getElementById('productDescription').value = description;
            document.getElementById('productIsWeighted').checked = isWeighted == 1;
            document.getElementById('productModalTitle').textContent = productId ? 'Изменить продукт' : 'Добавить продукт';
            document.getElementById('productModal').classList.remove('hidden');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        function editProduct(id, name, price, categoryId, description, isWeighted) {
            showProductModal(id, name, price, categoryId, description, isWeighted);
        }

        function deleteProduct(id) {
            if (confirm('Вы уверены, что хотите удалить этот продукт?')) {
                fetch(`/api/admin/products/${id}`, { method: 'DELETE' })
                    .then(() => location.reload());
            }
        }

        // Form submissions
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            const id = data.id;
            delete data.id;

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/admin/categories/${id}` : '/api/admin/categories';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            }).then(() => location.reload());
        });

        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const id = formData.get('id');
            
            // Get compressed image data if available
            const compressedImage = document.getElementById('compressedImage');
            const imageData = compressedImage ? compressedImage.value : '';
            
            // Convert form data to JSON object
            const data = {
                name: formData.get('name'),
                price: formData.get('price'),
                category_id: formData.get('category_id'),
                description: formData.get('description'),
                is_weighted: formData.get('is_weighted') ? 1 : 0,
                weight_unit: formData.get('weight_unit'),
                image_url: imageData || formData.get('image_url')
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/admin/products/${id}` : '/api/admin/products';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Товар успешно сохранен');
                    location.reload();
                } else {
                    alert('Ошибка при сохранении товара');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка сети');
            });
        });

        // Toggle weight unit field visibility
        function toggleWeightUnitField() {
            const isWeighted = document.getElementById('productIsWeighted').checked;
            const weightUnitContainer = document.getElementById('weightUnitContainer');
            if (isWeighted) {
                weightUnitContainer.style.display = 'block';
            } else {
                weightUnitContainer.style.display = 'none';
                document.getElementById('productWeightUnit').value = '';
            }
        }

        // Add event listener to checkbox
        document.getElementById('productIsWeighted').addEventListener('change', toggleWeightUnitField);

        // Update order status
        function updateOrderStatus(orderId) {
            const statusSelect = document.getElementById(`status-${orderId}`);
            const newStatus = statusSelect.value;

            fetch(`/api/admin/orders/${orderId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Статус заказа обновлен успешно');
                } else {
                    alert('Ошибка при обновлении статуса');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка сети');
            });
        }

        // Order details modal
        function showOrderDetails(orderId, items, address, userName, deliveryIncluded, deliveryPrice) {
            document.getElementById('orderDetailsTitle').textContent = `Детали заказа #${orderId}`;

            let content = `
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800">Клиент: ${userName}</h4>
                    <p class="text-sm text-gray-600">Адрес: ${address}</p>
                </div>
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Информация о доставке:</h4>
                    <p class="text-sm text-gray-600">
                        ${deliveryIncluded === 1 ? `✅ Доставка включена (${deliveryPrice} ₸)` : '❌ Доставка не включена'}
                    </p>
                </div>
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Состав заказа:</h4>
                    <div class="space-y-2">
            `;

            items.forEach(item => {
                const total = item.price * item.quantity;
                content += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <div>
                            <span class="font-medium">${item.name}</span>
                            <span class="text-sm text-gray-600 ml-2">${item.price} ₸ × ${item.quantity}</span>
                        </div>
                        <span class="font-bold">${total} ₸</span>
                    </div>
                `;
            });

            const itemsTotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const totalPrice = itemsTotal + (deliveryIncluded === 1 ? deliveryPrice : 0);

            content += `
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span>Товары:</span>
                        <span>${itemsTotal} ₸</span>
                    </div>
                    ${deliveryIncluded === 1 ? `
                    <div class="flex justify-between items-center mb-2">
                        <span>Доставка:</span>
                        <span>${deliveryPrice} ₸</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between items-center font-bold text-lg">
                        <span>Итого:</span>
                        <span>${totalPrice} ₸</span>
                    </div>
                </div>
            `;

            document.getElementById('orderDetailsContent').innerHTML = content;
            document.getElementById('orderDetailsModal').classList.remove('hidden');
        }

        function closeOrderDetailsModal() {
            document.getElementById('orderDetailsModal').classList.add('hidden');
        }

        // Statistics functions
        async function loadStatistics() {
            try {
                const response = await fetch('/api/admin/stats');
                if (response.ok) {
                    const data = await response.json();

                    document.getElementById('totalRevenue').textContent = (data.total_revenue || 0) + ' ₸';
                    document.getElementById('totalOrders').textContent = data.total_orders || 0;
                    document.getElementById('activeUsers').textContent = data.total_users || 0;
                    
                    const avgOrderValue = data.total_orders > 0 ? Math.round(data.total_revenue / data.total_orders) : 0;
                    document.getElementById('avgOrderValue').textContent = avgOrderValue + ' ₸';
                } else {
                    alert('Ошибка при загрузке статистики');
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
                alert('Ошибка сети при загрузке статистики');
            }
        }

        // Export report
        function exportReport(format) {
            const period = document.getElementById('statsPeriod').value;
            window.open(`/api/admin/export?format=${format}&period=${period}`, '_blank');
        }

        // User management functions
        function updateUserRole(userId, role) {
            fetch(`/api/admin/users/${userId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ role: role })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Роль пользователя обновлена');
                    location.reload();
                } else {
                    alert('Ошибка при обновлении роли');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка сети');
            });
        }

        function editUser(id, name, phone, email) {
            document.getElementById('userId').value = id;
            document.getElementById('userName').value = name;
            document.getElementById('userPhone').value = phone;
            document.getElementById('userEmail').value = email;
            document.getElementById('userModalTitle').textContent = 'Изменить пользователя';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function deleteUser(id) {
            if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
                fetch(`/api/admin/users/${id}`, { method: 'DELETE' })
                    .then(() => location.reload());
            }
        }

        // User form submission
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            const id = data.id;
            delete data.id;

            fetch(`/api/admin/users/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            }).then(() => location.reload());
        });

        // Load statistics on tab switch
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-load statistics when statistics tab is shown
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const target = mutation.target;
                        if (target.id === 'statistics-tab' && !target.classList.contains('hidden')) {
                            loadStatistics();
                        }
                    }
                });
            });

            const statisticsTab = document.getElementById('statistics-tab');
            if (statisticsTab) {
                observer.observe(statisticsTab, { attributes: true });
            }

            // Auto-load courier requests when courier requests tab is shown
            const courierRequestsTab = document.getElementById('courier-requests-tab');
            if (courierRequestsTab) {
                observer.observe(courierRequestsTab, { attributes: true });
            }
        });

        // Courier requests functions
        async function loadCourierRequests() {
            try {
                const response = await fetch('/api/admin/requests');
                const requests = await response.json();
                displayRequests(requests);
            } catch (error) {
                console.error('Error loading requests:', error);
            }
        }

        function displayRequests(requests) {
            const container = document.getElementById('requestsList');
            const noRequests = document.getElementById('noRequests');

            if (!Array.isArray(requests) || requests.length === 0) {
                container.innerHTML = '';
                noRequests.classList.remove('hidden');
                return;
            }

            noRequests.classList.add('hidden');

            const requestsHtml = requests.map(request => `
                <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800">Заказ #${request.order_id}</h3>
                        <span class="text-sm text-gray-500">${request.created_at}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-2">Курьер: ${request.courier_name}</p>
                    <p class="text-gray-600 text-sm mb-4">Адрес: ${request.order_address}</p>
                    <div class="flex space-x-2">
                        <button onclick="confirmRequest(${request.order_id}, ${request.courier_id})" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                            ✅ Подтвердить
                        </button>
                        <button onclick="rejectRequest(${request.order_id})" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                            ❌ Отклонить
                        </button>
                    </div>
                </div>
            `).join('');

            container.innerHTML = requestsHtml;
        }

        async function confirmRequest(orderId, courierId) {
            try {
                const response = await fetch(`/api/orders/${orderId}/confirm`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ courier_id: courierId })
                });

                if (response.ok) {
                    alert('Заказ подтвержден и назначен курьеру!');
                    loadCourierRequests();
                    loadCouriersMap();
                } else {
                    const errorData = await response.json();
                    alert('Ошибка: ' + (errorData.error || 'Не удалось подтвердить заказ'));
                }
            } catch (error) {
                console.error('Error confirming request:', error);
                alert('Ошибка сети при подтверждении заказа');
            }
        }

        async function rejectRequest(orderId) {
            if (!confirm('Вы уверены, что хотите отклонить этот запрос?')) {
                return;
            }

            try {
                const response = await fetch(`/api/orders/${orderId}/reject`, {
                    method: 'POST'
                });

                if (response.ok) {
                    alert('Запрос отклонен');
                    loadCourierRequests();
                    loadCouriersMap();
                } else {
                    alert('Ошибка при отклонении запроса');
                }
            } catch (error) {
                console.error('Error rejecting request:', error);
                alert('Ошибка сети при отклонении запроса');
            }
        }

        // Couriers map functions
        let couriersMap;
        let courierMarkers = {};

        async function loadCouriersMap() {
            try {
                const response = await fetch('/api/admin/couriers');
                const couriers = await response.json();
                initCouriersMap(couriers);
                displayCouriersList(couriers);
            } catch (error) {
                console.error('Error loading couriers:', error);
            }
        }

        function initCouriersMap(couriers) {
            if (!couriersMap) {
                // Координаты магазина в Кентау
                couriersMap = L.map('couriersMap').setView([43.518703, 68.505423], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(couriersMap);
            }

            // Clear existing markers
            Object.values(courierMarkers).forEach(marker => {
                couriersMap.removeLayer(marker);
            });
            courierMarkers = {};

            // Add couriers markers
            if (Array.isArray(couriers)) {
                couriers.forEach(courier => {
                    if (courier.location) {
                        const popupContent = `
                            <div>
                                <strong>🚴 ${courier.name}</strong><br>
                                <span class="text-sm text-gray-600">Телефон: ${courier.phone}</span><br>
                                ${courier.current_order ? `
                                    <div class="mt-2 p-2 bg-blue-50 rounded">
                                        <strong>Текущий заказ:</strong><br>
                                        #${courier.current_order.id}<br>
                                        <span class="text-sm">${courier.current_order.address}</span><br>
                                        <span class="text-sm text-gray-600">Статус: ${courier.current_order.status}</span>
                                    </div>
                                ` : '<span class="text-sm text-gray-500">Нет активных заказов</span>'}
                                <div class="mt-2 text-xs text-gray-500">Обновлено: ${courier.location.updated_at}</div>
                            </div>
                        `;
                        const marker = L.marker([courier.location.lat, courier.location.lng], {
                            title: courier.name
                        }).addTo(couriersMap).bindPopup(popupContent);
                        courierMarkers[courier.id] = marker;
                    }
                });
            }

            // Fit map to show all couriers
            if (Object.keys(courierMarkers).length > 0) {
                const group = new L.featureGroup(Object.values(courierMarkers));
                couriersMap.fitBounds(group.getBounds().pad(0.5));
            }
        }

        function displayCouriersList(couriers) {
            const container = document.getElementById('couriersList');

            // Format date function
            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                // Convert to Asia/Almaty timezone (+5)
                const options = { timeZone: 'Asia/Almaty', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                const formatted = new Intl.DateTimeFormat('ru-RU', options).format(date);
                // Convert from DD.MM.YYYY, HH:MM:SS to YYYY-MM-DD HH:MM:SS
                const [datePart, timePart] = formatted.split(', ');
                const [day, month, year] = datePart.split('.');
                return `${year}-${month}-${day} ${timePart}`;
            };

            const couriersHtml = couriers.map(courier => `
                <div class="bg-gray-50 rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition-colors" onclick="focusCourierOnMap(${courier.id})">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold">${courier.name}</h4>
                            <p class="text-sm text-gray-600">Телефон: ${courier.phone}</p>
                            ${courier.location ? `
                                <p class="text-xs text-gray-500">Последнее обновление: ${formatDate(courier.location.updated_at)}</p>
                            ` : '<p class="text-xs text-gray-500">Нет данных о местоположении</p>'}
                        </div>
                        <div class="text-right">
                            ${courier.current_order ? `
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">${courier.current_order.status}</span>
                                <p class="text-xs text-gray-600 mt-1">Заказ #${courier.current_order.id}</p>
                            ` : '<span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Свободен</span>'}
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = couriersHtml;
        }

        function focusCourierOnMap(courierId) {
            // Find courier in the markers and focus on them
            if (courierMarkers[courierId]) {
                couriersMap.setView([courierMarkers[courierId].getLatLng().lat, courierMarkers[courierId].getLatLng().lng], 15);
                courierMarkers[courierId].openPopup();
            }
        }

        // Archive functions
        async function loadArchive() {
            try {
                const response = await fetch('/api/admin/archive');
                if (response.ok) {
                    const orders = await response.json();
                    displayArchiveOrders(orders);
                } else {
                    console.error('Error loading archive');
                }
            } catch (error) {
                console.error('Error loading archive:', error);
            }
        }

        function displayArchiveOrders(orders) {
            const container = document.getElementById('archive-table');
            const noArchiveOrders = document.getElementById('noArchiveOrders');
            const userMap = <?php echo json_encode(array_column($users, 'name', 'id')); ?>;

            if (orders.length === 0) {
                container.innerHTML = '';
                noArchiveOrders.classList.remove('hidden');
                return;
            }

            noArchiveOrders.classList.add('hidden');

            const ordersHtml = orders.map(order => `
                <tr class="border-t">
                    <td class="px-4 py-2">${order.id}</td>
                    <td class="px-4 py-2">${userMap[order.user_id] || 'Неизвестный пользователь'}</td>
                    <td class="px-4 py-2">${order.created_at}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${
                            order.status === 'ДОСТАВЛЕН' ? 'bg-green-100 text-green-800' :
                            order.status === 'CANCELED' ? 'bg-red-100 text-red-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${order.status}
                        </span>
                    </td>
                    <td class="px-4 py-2">${order.total_price} ₸</td>
                    <td class="px-4 py-2 space-x-2">
                        <button onclick="showArchiveOrderDetails(${order.id}, ${JSON.stringify(order.items)}, '${order.address}', '${userMap[order.user_id] || 'Неизвестный пользователь'}', ${order.delivery_included}, ${order.delivery_price})" class="text-blue-600 hover:text-blue-800 text-sm">
                            Детали
                        </button>
                        <button onclick="restoreOrder(${order.id})" class="text-green-600 hover:text-green-800 text-sm">
                            📦 Вернуть в заказы
                        </button>
                    </td>
                </tr>
            `).join('');

            container.innerHTML = ordersHtml;
        }

        function showArchiveOrderDetails(orderId, items, address, userName) {
            document.getElementById('orderDetailsTitle').textContent = `Детали архивного заказа #${orderId}`;

            let content = `
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800">Клиент: ${userName}</h4>
                    <p class="text-sm text-gray-600">Адрес: ${address}</p>
                    <p class="text-sm text-gray-600">Статус: Архивный</p>
                </div>
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Состав заказа:</h4>
                    <div class="space-y-2">
            `;

            items.forEach(item => {
                const total = item.price * item.quantity;
                content += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <div>
                            <span class="font-medium">${item.name}</span>
                            <span class="text-sm text-gray-600 ml-2">${item.price} ₸ × ${item.quantity}</span>
                        </div>
                        <span class="font-bold">${total} ₸</span>
                    </div>
                `;
            });

            const totalPrice = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            content += `
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Итого:</span>
                        <span class="font-bold text-lg">${totalPrice} ₸</span>
                    </div>
                </div>
            `;

            document.getElementById('orderDetailsContent').innerHTML = content;
            document.getElementById('orderDetailsModal').classList.remove('hidden');
        }

        async function restoreOrder(orderId) {
            if (!confirm('Вы уверены, что хотите вернуть этот заказ из архива?')) {
                return;
            }

            try {
                const response = await fetch(`/api/admin/archive/${orderId}/restore`, {
                    method: 'POST'
                });

                if (response.ok) {
                    alert('Заказ успешно возвращен из архива!');
                    location.reload();
                } else {
                    alert('Ошибка при восстановлении заказа');
                }
            } catch (error) {
                console.error('Error restoring order:', error);
                alert('Ошибка сети при восстановлении заказа');
            }
        }

        function exportArchive(format) {
            const statusFilter = document.getElementById('archiveStatusFilter').value;
            const periodFilter = document.getElementById('archivePeriodFilter').value;
            window.open(`/api/admin/archive/export?format=${format}&status=${statusFilter}&period=${periodFilter}`, '_blank');
        }

        // Archive modal functions
        function showArchiveModal() {
            document.getElementById('archiveModal').classList.remove('hidden');
            loadArchiveModal();
        }

        function closeArchiveModal() {
            document.getElementById('archiveModal').classList.add('hidden');
        }

        async function loadArchiveModal() {
            try {
                const statusFilter = document.getElementById('archiveModalStatusFilter').value;
                const periodFilter = document.getElementById('archiveModalPeriodFilter').value;
                
                const response = await fetch(`/api/admin/archive?status=${statusFilter}&period=${periodFilter}`);
                const orders = await response.json();
                
                displayArchiveModalOrders(orders);
            } catch (error) {
                console.error('Error loading archive modal:', error);
            }
        }

        function displayArchiveModalOrders(orders) {
            const container = document.getElementById('archiveModalContent');
            const userMap = <?php echo json_encode(array_column($users, 'name', 'id')); ?>;

            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        В архиве пока нет заказов
                    </div>
                `;
                return;
            }

            // Group orders by date
            const ordersByDate = {};
            orders.forEach(order => {
                const date = order.created_at.split(' ')[0]; // Extract date part
                if (!ordersByDate[date]) {
                    ordersByDate[date] = [];
                }
                ordersByDate[date].push(order);
            });

            let content = '';

            // Sort dates in descending order (newest first)
            const sortedDates = Object.keys(ordersByDate).sort((a, b) => new Date(b) - new Date(a));

            sortedDates.forEach(date => {
                const dateOrders = ordersByDate[date];
                const dateOrdersCount = dateOrders.length;
                const dateTotalRevenue = dateOrders.reduce((sum, order) => sum + parseFloat(order.total_price), 0);

                content += `
                    <div class="mb-6">
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg mb-4">
                            <div>
                                <h4 class="font-bold text-gray-800">${formatDate(date)}</h4>
                                <p class="text-sm text-gray-600">${dateOrdersCount} заказов</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg">${dateTotalRevenue.toFixed(0)} ₸</p>
                                <p class="text-xs text-gray-500">Общая выручка</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                `;

                dateOrders.forEach(order => {
                    content += `
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-2">
                                        <h5 class="font-semibold">Заказ #${order.id}</h5>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium ${
                                            order.status === 'ДОСТАВЛЕН' ? 'bg-green-100 text-green-800' :
                                            order.status === 'CANCELED' ? 'bg-red-100 text-red-800' :
                                            'bg-gray-100 text-gray-800'
                                        }">
                                            ${order.status}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">Клиент: ${userMap[order.user_id] || 'Неизвестный пользователь'}</p>
                                    <p class="text-sm text-gray-600 mb-2">Адрес: ${order.address}</p>
                                    <p class="text-sm text-gray-600 mb-3">Дата: ${order.created_at}</p>
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm text-gray-600">
                                            ${order.items.length} товар(ов)
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg">${order.total_price} ₸</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 space-y-2">
                                    <button onclick="showArchiveOrderDetails(${order.id}, ${JSON.stringify(order.items)}, '${order.address}', '${userMap[order.user_id] || 'Неизвестный пользователь'}')" class="block w-full bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        Детали
                                    </button>
                                    <button onclick="restoreOrder(${order.id})" class="block w-full bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                        📦 Вернуть в заказы
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                content += `
                        </div>
                    </div>
                `;
            });

            container.innerHTML = content;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('ru-RU', options);
        }

        function exportArchiveModal(format) {
            const statusFilter = document.getElementById('archiveModalStatusFilter').value;
            const periodFilter = document.getElementById('archiveModalPeriodFilter').value;
            window.open(`/api/admin/archive/export?format=${format}&status=${statusFilter}&period=${periodFilter}`, '_blank');
        }

        // Archive order function
        async function archiveOrder(orderId) {
            if (!confirm('Вы уверены, что хотите переместить этот заказ в архив?')) {
                return;
            }

            try {
                const response = await fetch(`/api/admin/orders/${orderId}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    alert('Заказ успешно перемещен в архив!');
                    // Reload the page to refresh the orders list
                    location.reload();
                } else {
                    const errorData = await response.json();
                    alert('Ошибка: ' + (errorData.error || 'Не удалось переместить заказ в архив'));
                }
            } catch (error) {
                console.error('Error archiving order:', error);
                alert('Ошибка сети при перемещении заказа в архив');
            }
        }

        // Auto-refresh functions
        setInterval(() => {
            if (currentTab === 'courier-requests') {
                loadCourierRequests();
                loadCouriersMap();
            } else if (currentTab === 'archive') {
                loadArchive();
            } else if (currentTab === 'chat') {
                if (currentChatUserId) {
                    loadChatMessages();
                }
                loadChatUsersList();
            }
        }, 10000); // Refresh every 10 seconds

        // =====================
        // Chat Functions
        // =====================
        let currentChatUserId = null;
        let chatUsers = [];
        let lastChatMessageId = 0;

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load chat users list
        async function loadChatUsersList() {
            try {
                const response = await fetch('/api/admin/chat/users');
                if (response.ok) {
                    chatUsers = await response.json();
                    displayChatUsers(chatUsers);
                }
            } catch (error) {
                console.error('Error loading chat users:', error);
            }
        }

        // Display chat users
        function displayChatUsers(users) {
            const container = document.getElementById('chatUsersList');
            const searchTerm = document.getElementById('chatUserSearch').value.toLowerCase();
            
            const filteredUsers = users.filter(user => 
                user.name.toLowerCase().includes(searchTerm) ||
                user.phone.includes(searchTerm)
            );

            if (filteredUsers.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-4">Нет пользователей с сообщениями</div>';
                return;
            }

            container.innerHTML = filteredUsers.map(user => `
                <div onclick="selectChatUser(${user.id}, '${escapeHtml(user.name)}')" 
                     class="p-3 rounded-lg cursor-pointer hover:bg-purple-50 transition-colors ${currentChatUserId === user.id ? 'bg-purple-100 border border-purple-300' : 'bg-gray-50'}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-800">${escapeHtml(user.name)}</h3>
                            <p class="text-sm text-gray-600">${escapeHtml(user.phone)}</p>
                        </div>
                        ${user.unread_count > 0 ? `
                            <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">${user.unread_count}</span>
                        ` : ''}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${user.last_message ? escapeHtml(user.last_message.substring(0, 30)) + (user.last_message.length > 30 ? '...' : '') : 'Нет сообщений'}</p>
                </div>
            `).join('');
        }

        // Filter chat users
        function filterChatUsers() {
            displayChatUsers(chatUsers);
        }

        // Select chat user
        function selectChatUser(userId, userName) {
            currentChatUserId = userId;
            document.getElementById('chatTitle').textContent = 'Чат с ' + userName;
            document.getElementById('chatSubtitle').textContent = 'Теперь вы можете отправлять сообщения';
            document.getElementById('chatInputArea').classList.remove('hidden');
            
            // Refresh user list to remove highlight
            loadChatUsersList();
            
            // Load messages
            loadChatMessages();
        }

        // Load chat messages
        async function loadChatMessages() {
            if (!currentChatUserId) return;
            
            const container = document.getElementById('chatMessages');
            
            try {
                const response = await fetch('/api/admin/chat/messages/' + currentChatUserId);
                if (response.ok) {
                    const messages = await response.json();
                    displayChatMessages(messages);
                    if (messages.length > 0) {
                        lastChatMessageId = messages[messages.length - 1].id;
                    }
                }
            } catch (error) {
                console.error('Error loading chat messages:', error);
            }
        }

        // Display chat messages
        function displayChatMessages(messages) {
            const container = document.getElementById('chatMessages');
            const currentUserId = <?php echo $_SESSION['user']['id'] ?? 'null'; ?>;

            if (messages.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-8">Нет сообщений. Начните общение!</div>';
                return;
            }

            container.innerHTML = messages.map(msg => {
                const isAdmin = msg.sender_id === currentUserId || msg.sender_role === 'admin';
                return `
                    <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isAdmin ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-800'}">
                            <div class="text-xs opacity-75 mb-1">${isAdmin ? 'Вы' : escapeHtml(msg.sender_name)}</div>
                            <div class="break-words">${escapeHtml(msg.message)}</div>
                            <div class="text-xs opacity-75 mt-1">${formatChatDate(msg.created_at)}</div>
                        </div>
                    </div>
                `;
            }).join('');

            container.scrollTop = container.scrollHeight;
        }

        // Send chat message
        document.getElementById('chatMessageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const input = document.getElementById('chatMessageInput');
            const message = input.value.trim();
            
            if (!message || !currentChatUserId) return;

            try {
                const response = await fetch('/api/admin/chat/messages', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        user_id: currentChatUserId,
                        message: message 
                    })
                });

                if (response.ok) {
                    input.value = '';
                    loadChatMessages();
                    loadChatUsersList(); // Refresh to show last message
                } else {
                    alert('Ошибка отправки сообщения');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Ошибка сети');
            }
        });

        // Format chat date
        function formatChatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Add chat tab loading
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^=tab-]').forEach(el => {
                el.classList.remove('border-purple-500', 'text-purple-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            document.getElementById(tab + '-tab').classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tab).classList.add('border-purple-500', 'text-purple-600');
            currentTab = tab;
            
            // Save current tab to localStorage
            localStorage.setItem('adminCurrentTab', tab);
            
            // Load data for specific tabs
            if (tab === 'courier-requests') {
                loadCourierRequests();
                loadCouriersMap();
            } else if (tab === 'statistics') {
                loadStatistics();
            } else if (tab === 'archive') {
                loadArchive();
            } else if (tab === 'chat') {
                loadChatUsersList();
                currentChatUserId = null;
                document.getElementById('chatTitle').textContent = 'Выберите чат';
                document.getElementById('chatSubtitle').textContent = 'Выберите пользователя для начала общения';
                document.getElementById('chatInputArea').classList.add('hidden');
                document.getElementById('chatMessages').innerHTML = '<div class="text-center text-gray-500 py-8">Выберите пользователя из списка слева</div>';
            }
        }
        
        // Create User Modal functions
        function showCreateUserModal() {
            document.getElementById('createUserForm').reset();
            document.getElementById('createUserModal').classList.remove('hidden');
        }
        
        function closeCreateUserModal() {
            document.getElementById('createUserModal').classList.add('hidden');
        }
        
        // Create user form submission
        document.getElementById('createUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                email: formData.get('email'),
                password: formData.get('password'),
                role: formData.get('role')
            };
            
            try {
                const response = await fetch('/api/admin/users', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Пользователь успешно создан!', 'success');
                    closeCreateUserModal();
                    location.reload();
                } else {
                    alert(result.error || 'Ошибка при создании пользователя', 'error');
                }
            } catch (error) {
                console.error('Error creating user:', error);
                alert('Ошибка сети', 'error');
            }
        });
        
        // Restore tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('adminCurrentTab');
            if (savedTab && savedTab !== 'categories') {
                showTab(savedTab);
            }
            // Load notifications
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 30000);
        });
        
        // =====================
        // Admin Notifications Functions
        // =====================
        let notificationsOpen = false;
        
        function toggleNotifications() {
            if (notificationsOpen) { closeNotifications(); } else { openNotifications(); }
        }
        
        function openNotifications() {
            const modal = document.getElementById('notificationsModal');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('scale-95', 'opacity-0'); modal.classList.add('scale-100', 'opacity-100'); }, 10);
            notificationsOpen = true;
            loadNotifications();
        }
        
        function closeNotifications() {
            const modal = document.getElementById('notificationsModal');
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
            notificationsOpen = false;
        }
        
        async function loadNotifications() {
            const list = document.getElementById('notificationsList');
            list.innerHTML = '<div class="p-4 text-center text-gray-500">Загрузка...</div>';
            try {
                const response = await fetch('/api/notifications');
                if (response.ok) { renderNotifications(await response.json()); }
                else { list.innerHTML = '<div class="p-4 text-center text-gray-500">Ошибка загрузки</div>'; }
            } catch (error) { list.innerHTML = '<div class="p-4 text-center text-gray-500">Ошибка сети</div>'; }
        }
        
        function renderNotifications(notifications) {
            const list = document.getElementById('notificationsList');
            if (notifications.length === 0) { list.innerHTML = '<div class="p-8 text-center text-gray-500"><div class="text-4xl mb-2">📭</div>Нет уведомлений</div>'; return; }
            list.innerHTML = notifications.map(n => {
                const icon = {'new_order':'📦','order_status':'🚚','courier_request':'🚴','system':'⚙️','promo':'🎁'}[n.type] || '🔔';
                const bgColor = n.read ? 'bg-gray-50' : 'bg-purple-50';
                return `<div class="p-4 border-b border-gray-100 ${bgColor} hover:bg-gray-100 transition-colors cursor-pointer" onclick="handleNotificationClick(${n.id}, ${n.data?.order_id ? n.data.order_id : 'null'})">
                    <div class="flex items-start space-x-3">
                        <div class="text-2xl">${icon}</div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 text-sm">${n.title}</div>
                            <div class="text-gray-600 text-sm">${n.message}</div>
                            <div class="text-gray-400 text-xs mt-1">${formatTimeAgo(n.created_at)}</div>
                        </div>
                        ${!n.read ? '<div class="w-2 h-2 bg-purple-500 rounded-full"></div>' : ''}
                    </div>
                </div>`;
            }).join('');
        }
        
        function formatTimeAgo(dateString) {
            const diff = Math.floor((new Date() - new Date(dateString)) / 1000);
            if (diff < 60) return 'только что';
            if (diff < 3600) return Math.floor(diff / 60) + ' мин назад';
            if (diff < 86400) return Math.floor(diff / 3600) + ' ч назад';
            return Math.floor(diff / 86400) + ' дн назад';
        }
        
        async function handleNotificationClick(notificationId, orderId) {
            await fetch(`/api/notifications/${notificationId}/read`, { method: 'POST' });
            updateNotificationBadge();
            if (orderId) { showTab('orders'); }
            closeNotifications();
        }
        
        async function markAllAsRead() {
            await fetch('/api/notifications/read-all', { method: 'POST' });
            loadNotifications();
            updateNotificationBadge();
        }
        
        async function updateNotificationBadge() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                if (response.ok) {
                    const data = await response.json();
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) { badge.textContent = data.count > 9 ? '9+' : data.count; badge.classList.remove('hidden'); }
                    else { badge.classList.add('hidden'); }
                }
            } catch (error) { console.error('Error updating badge:', error); }
        }
        
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('notificationsModal');
            const bell = document.querySelector('[onclick="toggleNotifications()"]');
            if (notificationsOpen && modal && !modal.contains(e.target) && bell && !bell.contains(e.target)) { closeNotifications(); }
        });
        
        // Close modals when clicking outside
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) closeCategoryModal();
        });
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) closeProductModal();
        });
        document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) closeOrderDetailsModal();
        });
        document.getElementById('archiveModal').addEventListener('click', function(e) {
            if (e.target === this) closeArchiveModal();
        });
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) closeUserModal();
        });
        document.getElementById('createUserModal').addEventListener('click', function(e) {
            if (e.target === this) closeCreateUserModal();
        });
    </script>
</body>
</html>
