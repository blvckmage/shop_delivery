<?php

/**
 * Delivery - Главный файл приложения
 * 
 * Точка входа в приложение с MVC архитектурой
 */

// Отображение ошибок (выключить в production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Временная функция автозагрузки (пока не запущен composer install)
spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\Core\\' => __DIR__ . '/app/Core/',
        'App\\Router\\' => __DIR__ . '/app/Router/',
        'App\\Models\\' => __DIR__ . '/app/Models/',
        'App\\Controllers\\' => __DIR__ . '/app/Controllers/'
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Если есть autoload от composer - используем его
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Security;
use App\Router\Router;

// Инициализация сессии
$session = new Session();

// Создаем запрос
$request = new Request();

// Создаем маршрутизатор
$router = new Router();

// ==================== МАРШРУТЫ ====================

// Главная страница
$router->get('/', [App\Controllers\SiteController::class, 'home']);

// Каталог
$router->get('/catalog', [App\Controllers\SiteController::class, 'catalog']);

// Профиль
$router->get('/profile', [App\Controllers\SiteController::class, 'profile']);

// Заказы пользователя
$router->get('/orders', [App\Controllers\SiteController::class, 'orders']);

// Чат
$router->get('/chat', [App\Controllers\SiteController::class, 'chat']);

// Авторизация
$router->get('/login', [App\Controllers\AuthController::class, 'loginPage']);
$router->get('/register', [App\Controllers\AuthController::class, 'registerPage']);

// Корзина
$router->get('/cart', [App\Controllers\CartController::class, 'page']);

// Оформление заказа
$router->get('/order', [App\Controllers\OrderController::class, 'checkoutPage']);

// Админ-панель
$router->get('/admin', [App\Controllers\AdminController::class, 'dashboard']);

// Курьер
$router->get('/courier', [App\Controllers\ApiController::class, 'courierPage']);

// ==================== API: АВТОРИЗАЦИЯ ====================

$router->post('/api/auth/login', [App\Controllers\AuthController::class, 'login']);
$router->post('/api/auth/register', [App\Controllers\AuthController::class, 'register']);
$router->post('/api/auth/logout', [App\Controllers\AuthController::class, 'logout']);
$router->get('/api/auth/me', [App\Controllers\AuthController::class, 'me']);

// ==================== API: ТОВАРЫ ====================

$router->get('/api/products', [App\Controllers\SiteController::class, 'getProducts']);
$router->get('/api/categories', [App\Controllers\SiteController::class, 'getCategories']);

// ==================== API: КОРЗИНА ====================

$router->get('/api/cart', [App\Controllers\CartController::class, 'get']);
$router->post('/api/cart/add', [App\Controllers\CartController::class, 'add']);
$router->post('/api/cart/update', [App\Controllers\CartController::class, 'update']);
$router->put('/api/cart/update', [App\Controllers\CartController::class, 'update']);
$router->post('/api/cart/sync', [App\Controllers\CartController::class, 'sync']);
$router->delete('/api/cart/{id}', [App\Controllers\CartController::class, 'remove']);
$router->post('/api/cart/clear', [App\Controllers\CartController::class, 'clear']);

// ==================== API: ЗАКАЗЫ ====================

$router->post('/api/orders', [App\Controllers\OrderController::class, 'create']);
$router->get('/api/orders', [App\Controllers\OrderController::class, 'getMy']);
$router->get('/api/orders/my', [App\Controllers\OrderController::class, 'getMy']);
$router->get('/api/orders/history', [App\Controllers\OrderController::class, 'getMyHistory']);
$router->get('/api/orders/{id}', [App\Controllers\OrderController::class, 'getById']);
$router->get('/api/orders/{id}/tracking', [App\Controllers\ApiController::class, 'orderTracking']);

// ==================== API: ГЕОКОДИРОВАНИЕ ====================

$router->get('/api/geocode/reverse', [App\Controllers\SiteController::class, 'reverseGeocode']);
$router->get('/api/geocode/search', [App\Controllers\SiteController::class, 'searchGeocode']);

// ==================== API: ПРОФИЛЬ ====================

$router->get('/api/profile/favorites', [App\Controllers\SiteController::class, 'getFavorites']);
$router->post('/api/profile/favorites/{id}', [App\Controllers\SiteController::class, 'toggleFavorite']);
$router->delete('/api/profile/favorites/{id}', [App\Controllers\SiteController::class, 'removeFavorite']);
$router->get('/api/profile/reviews', [App\Controllers\SiteController::class, 'getReviews']);
$router->post('/api/profile/update', [App\Controllers\SiteController::class, 'updateProfile']);
$router->put('/api/profile/update', [App\Controllers\SiteController::class, 'updateProfile']);

// ==================== API: КУРЬЕР ====================

$router->get('/api/courier/orders', [App\Controllers\ApiController::class, 'courierOrders']);
$router->get('/api/courier/history', [App\Controllers\ApiController::class, 'courierHistory']);
$router->post('/api/courier/take/{id}', [App\Controllers\ApiController::class, 'courierTakeOrder']);
$router->post('/api/orders/{id}/request', [App\Controllers\ApiController::class, 'courierTakeOrder']);
$router->post('/api/courier/status/{id}', [App\Controllers\ApiController::class, 'courierUpdateStatus']);
$router->post('/api/orders/{id}/status', [App\Controllers\ApiController::class, 'courierUpdateStatus']);
$router->post('/api/courier/cancel/{id}', [App\Controllers\ApiController::class, 'courierCancelOrder']);
$router->post('/api/orders/{id}/cancel', [App\Controllers\ApiController::class, 'courierCancelOrder']);
$router->post('/api/courier/location', [App\Controllers\ApiController::class, 'courierLocation']);
$router->get('/api/orders/{id}/courier-location', [App\Controllers\ApiController::class, 'orderCourierLocation']);

// ==================== API: АДМИН ====================

// Товары
$router->get('/api/admin/products', [App\Controllers\AdminController::class, 'getProducts']);
$router->post('/api/admin/products', [App\Controllers\AdminController::class, 'createProduct']);
$router->put('/api/admin/products/{id}', [App\Controllers\AdminController::class, 'updateProduct']);
$router->delete('/api/admin/products/{id}', [App\Controllers\AdminController::class, 'deleteProduct']);

// Категории
$router->get('/api/admin/categories', [App\Controllers\AdminController::class, 'getCategories']);
$router->post('/api/admin/categories', [App\Controllers\AdminController::class, 'createCategory']);
$router->put('/api/admin/categories/{id}', [App\Controllers\AdminController::class, 'updateCategory']);
$router->delete('/api/admin/categories/{id}', [App\Controllers\AdminController::class, 'deleteCategory']);

// Заказы
$router->get('/api/admin/orders', [App\Controllers\AdminController::class, 'getOrders']);
$router->post('/api/admin/orders/{id}/status', [App\Controllers\AdminController::class, 'updateOrderStatus']);
$router->put('/api/admin/orders/{id}', [App\Controllers\AdminController::class, 'updateOrderStatus']);
$router->post('/api/admin/orders/{id}/archive', [App\Controllers\AdminController::class, 'archiveOrder']);
$router->delete('/api/admin/orders/{id}', [App\Controllers\AdminController::class, 'archiveOrder']);
$router->post('/api/admin/orders/{id}/restore', [App\Controllers\AdminController::class, 'restoreOrder']);
$router->get('/api/admin/archive', [App\Controllers\AdminController::class, 'getArchive']);

// Пользователи
$router->get('/api/admin/users', [App\Controllers\AdminController::class, 'getUsers']);
$router->post('/api/admin/users', [App\Controllers\AdminController::class, 'createUser']);
$router->put('/api/admin/users/{id}', [App\Controllers\AdminController::class, 'updateUser']);
$router->delete('/api/admin/users/{id}', [App\Controllers\AdminController::class, 'deleteUser']);

// Статистика
$router->get('/api/admin/stats', [App\Controllers\AdminController::class, 'getStats']);

// Экспорт
$router->get('/api/admin/export', [App\Controllers\AdminController::class, 'exportReport']);
$router->get('/api/admin/archive/export', [App\Controllers\AdminController::class, 'exportArchive']);

// Архив (восстановление)
$router->post('/api/admin/archive/{id}/restore', [App\Controllers\AdminController::class, 'restoreOrder']);

// Курьеры (админ)
$router->get('/api/admin/couriers', [App\Controllers\ApiController::class, 'adminCouriers']);
$router->get('/api/admin/courier-requests', [App\Controllers\ApiController::class, 'adminCourierRequests']);
$router->get('/api/admin/requests', [App\Controllers\ApiController::class, 'adminCourierRequests']);
$router->post('/api/orders/{id}/confirm', [App\Controllers\ApiController::class, 'confirmCourierRequest']);
$router->post('/api/orders/{id}/reject', [App\Controllers\ApiController::class, 'rejectCourierRequest']);

// ==================== API: ЧАТ ====================

$router->get('/api/chat/contacts', [App\Controllers\ApiController::class, 'chatContacts']);
$router->get('/api/chat/messages', [App\Controllers\ApiController::class, 'chatMessages']);
$router->get('/api/chat/messages/{contactId}', [App\Controllers\ApiController::class, 'chatMessages']);
$router->post('/api/chat/messages', [App\Controllers\ApiController::class, 'chatSend']);
$router->post('/api/chat/send', [App\Controllers\ApiController::class, 'chatSend']);
$router->post('/api/chat/send/{contactId}', [App\Controllers\ApiController::class, 'chatSend']);

// Админ: чат
$router->get('/api/admin/chat/users', [App\Controllers\ApiController::class, 'adminChatUsers']);
$router->get('/api/admin/chat/messages/{userId}', [App\Controllers\ApiController::class, 'adminChatMessages']);
$router->post('/api/admin/chat/messages', [App\Controllers\ApiController::class, 'adminChatSend']);
$router->post('/api/admin/chat/send', [App\Controllers\ApiController::class, 'adminChatSend']);

// ==================== API: УВЕДОМЛЕНИЯ ====================

$router->get('/api/notifications', [App\Controllers\ApiController::class, 'getNotifications']);
$router->get('/api/notifications/unread-count', [App\Controllers\ApiController::class, 'getUnreadCount']);
$router->post('/api/notifications/{id}/read', [App\Controllers\ApiController::class, 'markNotificationRead']);
$router->post('/api/notifications/read-all', [App\Controllers\ApiController::class, 'markAllNotificationsRead']);

// ==================== ДИСПЕТЧЕРИЗАЦИЯ ====================

$response = $router->dispatch($request);
$response->send();