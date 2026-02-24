<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Validator;
use App\Models\ProductModel;

/**
 * Контроллер корзины
 */
class CartController extends Controller
{
    private ProductModel $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel($this->db);
    }
    
    /**
     * Страница корзины
     */
    public function page(Request $request): Response
    {
        $cart = $this->enrichCart($this->getCart());
        return $this->render('cart', ['cart' => $cart]);
    }
    
    /**
     * API: Получить корзину
     */
    public function get(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $cart = $this->getCart();
        return $this->json($cart);
    }
    
    /**
     * API: Добавить товар в корзину
     */
    public function add(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'product_id' => 'required,integer',
            'quantity' => 'integer,min_value:1'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        $productId = intval($data['product_id']);
        $quantity = intval($data['quantity'] ?? 1);
        
        // Проверка существования товара
        $product = $this->productModel->findById($productId);
        if ($product === null) {
            return $this->error('Товар не найден', 404);
        }
        
        $cart = $this->getCart();
        
        // Проверка весового товара
        $isWeighted = !empty($data['is_weighted']) || !empty($product['is_weighted']);
        $weight = isset($data['weight']) ? intval($data['weight']) : null;
        $calculatedPrice = isset($data['calculated_price']) ? floatval($data['calculated_price']) : null;
        
        // Ищем товар в корзине
        $found = false;
        foreach ($cart as &$item) {
            if (intval($item['id']) === $productId) {
                if ($isWeighted && $weight) {
                    // Для весовых товаров - обновляем вес и цену
                    $item['weight'] = $weight;
                    $item['calculated_price'] = $calculatedPrice ?? $this->calculateWeightedPrice($product, $weight);
                } else {
                    $item['quantity'] = intval($item['quantity']) + $quantity;
                }
                $found = true;
                break;
            }
        }
        
        // Если не нашли - добавляем
        if (!$found) {
            $cartItem = ['id' => $productId, 'quantity' => $quantity];
            if ($isWeighted && $weight) {
                $cartItem['weight'] = $weight;
                $cartItem['calculated_price'] = $calculatedPrice ?? $this->calculateWeightedPrice($product, $weight);
                $cartItem['is_weighted'] = true;
            }
            $cart[] = $cartItem;
        }
        
        $this->setCart($cart);
        
        return $this->json([
            'success' => true,
            'cart' => $this->enrichCart($this->getCart())
        ]);
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
     * API: Обновить количество товара
     */
    public function update(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'product_id' => 'required,integer',
            'quantity' => 'required,integer,min_value:0'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        $productId = intval($data['product_id']);
        $quantity = intval($data['quantity']);
        
        // Проверка существования товара
        $product = $this->productModel->findById($productId);
        if ($product === null) {
            return $this->error('Товар не найден', 404);
        }
        
        $cart = $this->getCart();
        
        $found = false;
        foreach ($cart as $key => &$item) {
            if (intval($item['id']) === $productId) {
                if ($quantity == 0) {
                    unset($cart[$key]);
                } else {
                    $item['quantity'] = $quantity;
                }
                $found = true;
                break;
            }
        }
        
        // Если товара нет в корзине и quantity > 0 - добавляем (для обработки race condition)
        if (!$found && $quantity > 0) {
            $cart[] = ['id' => $productId, 'quantity' => $quantity];
        }
        
        $this->setCart(array_values($cart));
        
        return $this->json([
            'success' => true,
            'cart' => $this->getCart()
        ]);
    }
    
    /**
     * API: Удалить товар из корзины
     */
    public function remove(Request $request, int $productId): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $cart = $this->getCart();
        
        $found = false;
        foreach ($cart as $key => $item) {
            if (intval($item['id']) === $productId) {
                unset($cart[$key]);
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            return $this->error('Товар не найден в корзине', 404);
        }
        
        $this->setCart(array_values($cart));
        
        return $this->json([
            'success' => true,
            'cart' => $this->getCart()
        ]);
    }
    
    /**
     * API: Очистить корзину
     */
    public function clear(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $this->clearCart();
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Синхронизировать корзину с клиентом
     */
    public function sync(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Поддержка обоих форматов: массив items или прямой массив
        $items = $data['items'] ?? $data;
        if (!is_array($items)) {
            return $this->error('Неверный формат данных', 400);
        }
        
        $cart = [];
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $isWeightedItem = !empty($item['is_weighted']);
                
                // Для весовых товаров quantity = вес в кг (может быть дробным)
                // Для штучных - количество штук (целое)
                if ($isWeightedItem) {
                    $quantity = floatval($item['quantity'] ?? 0.5);
                    $quantity = max(0.1, round($quantity * 10) / 10); // Округляем до 0.1
                } else {
                    $quantity = max(1, intval($item['quantity'] ?? 1));
                }
                
                $cartItem = [
                    'id' => intval($item['id']),
                    'quantity' => $quantity,
                    'is_weighted' => $isWeightedItem ? 1 : 0
                ];
                
                // Сохраняем дополнительные данные
                if (!empty($item['image_url'])) {
                    $cartItem['image_url'] = $item['image_url'];
                }
                if (!empty($item['name'])) {
                    $cartItem['name'] = $item['name'];
                }
                if (!empty($item['price'])) {
                    $cartItem['price'] = floatval($item['price']);
                }
                
                $cart[] = $cartItem;
            }
        }
        
        $this->setCart($cart);
        
        return $this->json([
            'success' => true,
            'cart' => $this->enrichCart($cart)
        ]);
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
                    // Рассчитанная цена хранится в calculated_price или вычисляется
                    $item['calculated_price'] = round($weightKg * $pricePerKg);
                } else {
                    $item['price'] = $product['price'];
                }
            }
        }
        
        return $cart;
    }
}