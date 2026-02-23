<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Validator;
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
        
        // Очищаем корзину
        $this->clearCart();
        
        // Создаем уведомление для пользователя
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
        
        // Создаем уведомление для админов
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
                
                if ($isWeighted && !empty($item['weight'])) {
                    // Для весового товара используем рассчитанную цену
                    $item['price'] = $item['calculated_price'] ?? $this->calculateWeightedPrice($product, intval($item['weight']));
                    $item['price_per_kg'] = $product['price']; // Цена за кг для отображения
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
