<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Validator;
use App\Core\WhatsApp;
use App\Models\OrderModel;
use App\Models\ProductModel;

/**
 * Контроллер заказов
 */
class OrderController extends Controller
{
    private OrderModel $orderModel;
    private ProductModel $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new OrderModel($this->db);
        $this->productModel = new ProductModel($this->db);
    }
    
    /**
     * Страница оформления заказа
     */
    public function checkoutPage(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $cart = $this->enrichCart($this->getCart());
        return $this->render('order', ['cart' => $cart]);
    }
    
    /**
     * Страница истории заказов
     */
    public function historyPage(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        return $this->render('orders');
    }
    
    /**
     * API: Создать заказ
     */
    public function create(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'address' => 'required,min:5',
            'items' => 'required'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        $items = $data['items'] ?? [];
        
        if (empty($items)) {
            return $this->error('Корзина пуста', 400);
        }
        
        // Создаем заказ
        $orderId = $this->orderModel->create([
            'user_id' => $this->getUserId(),
            'items' => $items,
            'address' => Security::sanitize($data['address']),
            'delivery_included' => !empty($data['delivery_included'])
        ]);
        
        // Проверяем результат создания
        if ($orderId === -1) {
            // Получаем активный заказ пользователя
            $activeOrders = $this->orderModel->getByUserId($this->getUserId());
            $activeOrder = null;
            foreach ($activeOrders as $order) {
                if (in_array($order['status'] ?? '', ['СОЗДАН', 'В_ПУТИ'])) {
                    $activeOrder = $order;
                    break;
                }
            }
            
            return $this->json([
                'success' => false,
                'error' => 'У вас уже есть активный заказ',
                'active_order' => $activeOrder,
                'redirect' => '/orders'
            ], 400);
        }
        
        if ($orderId === 0 || $orderId === null) {
            return $this->error('Не удалось создать заказ. Попробуйте позже.', 500);
        }
        
        // Очищаем корзину
        $this->clearCart();
        
        // Создаем уведомление для пользователя (без прерывания если ошибка)
        try {
            \App\Controllers\ApiController::createNotification(
                $this->db,
                'order_created',
                'Заказ оформлен',
                "Ваш заказ #{$orderId} успешно оформлен и скоро будет готов к доставке!",
                $this->getUserId(),
                null,
                false,
                ['order_id' => $orderId]
            );
        } catch (\Exception $e) {
            error_log("Failed to create user notification: " . $e->getMessage());
        }
        
        // Создаем уведомление для админов (без прерывания если ошибка)
        try {
            \App\Controllers\ApiController::createNotification(
                $this->db,
                'new_order',
                'Новый заказ',
                "Поступил новый заказ #{$orderId} на сумму " . $this->calculateOrderTotal($items) . ' ₸',
                null,
                'admin',
                false,
                ['order_id' => $orderId]
            );
        } catch (\Exception $e) {
            error_log("Failed to create admin notification: " . $e->getMessage());
        }
        
        // Отправляем уведомление в WhatsApp группу (без прерывания если ошибка)
        try {
            $whatsapp = new WhatsApp();
            if ($whatsapp->isAvailable()) {
                $order = $this->orderModel->findById($orderId);
                if ($order) {
                    // Добавляем phone в заказ для уведомления
                    $order['phone'] = $data['phone'] ?? '';
                    $whatsapp->notifyNewOrder($order);
                }
            }
        } catch (\Exception $e) {
            error_log("Failed to send WhatsApp notification: " . $e->getMessage());
        }
        
        return $this->json([
            'success' => true,
            'order_id' => $orderId
        ]);
    }
    
    /**
     * API: Получить заказы пользователя
     */
    public function getMy(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $orders = $this->orderModel->getByUserId($this->getUserId());
        
        return $this->json(array_values($orders));
    }
    
    /**
     * API: Получить историю заказов пользователя (архив)
     */
    public function getMyHistory(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $archive = $this->orderModel->getArchive();
        $userId = $this->getUserId();
        
        // Фильтруем заказы текущего пользователя
        $history = array_filter($archive, function($order) use ($userId) {
            return isset($order['user_id']) && $order['user_id'] == $userId;
        });
        
        // Сортируем по дате (новые первые)
        usort($history, function($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });
        
        return $this->json(array_values($history));
    }
    
    /**
     * API: Получить заказ по ID
     */
    public function getById(Request $request, int $id): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $order = $this->orderModel->findById($id);
        
        if ($order === null) {
            return $this->error('Заказ не найден', 404);
        }
        
        // Проверка доступа
        if ($order['user_id'] != $this->getUserId() && !$this->session->isAdmin()) {
            return $this->error('Доступ запрещен', 403);
        }
        
        return $this->json($order);
    }
    
    /**
     * Обогатить корзину данными товаров
     */
    private function enrichCart(array $cart): array
    {
        if (empty($cart)) {
            return $cart;
        }
        
        $products = $this->productModel->getAll();
        $productMap = [];
        foreach ($products as $product) {
            $productMap[$product['id']] = $product;
        }
        
        foreach ($cart as &$item) {
            $productId = intval($item['id']);
            if (isset($productMap[$productId])) {
                $product = $productMap[$productId];
                $item['name'] = $product['name'];
                $item['image_url'] = $product['image_url'] ?? '';
                $item['weight_unit'] = $product['weight_unit'] ?? '';
                $item['product_id'] = $product['id'];
                
                // Обработка весовых товаров
                $isWeighted = !empty($item['is_weighted']) || !empty($product['is_weighted']);
                $item['is_weighted'] = $isWeighted ? 1 : 0;
                
                if ($isWeighted) {
                    // Для весового товара quantity = вес в кг
                    // Цена = цена за кг * вес
                    $weightKg = floatval($item['quantity'] ?? 0.5);
                    $pricePerKg = floatval($product['price']);
                    $item['price'] = $pricePerKg; // Цена за кг для отображения
                    $item['calculated_price'] = round($weightKg * $pricePerKg);
                } else {
                    $item['price'] = $product['price'];
                }
            }
        }
        
        return $cart;
    }
    
    /**
     * Рассчитать цену весового товара
     */
    private function calculateWeightedPrice(array $product, int $weightGrams): float
    {
        $pricePerKg = floatval($product['price']);
        return round(($weightGrams / 1000) * $pricePerKg);
    }
    
    /**
     * Рассчитать общую сумму заказа
     */
    private function calculateOrderTotal(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            if (!empty($item['is_weighted']) && !empty($item['weight'])) {
                $total += floatval($item['price'] ?? 0);
            } else {
                $total += floatval($item['price'] ?? 0) * intval($item['quantity'] ?? 1);
            }
        }
        return $total;
    }
}
