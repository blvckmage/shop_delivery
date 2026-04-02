<?php

namespace App\Models;

use App\Core\Database;

/**
 * Модель корзины (хранение в БД)
 */
class CartModel
{
    private Database $db;
    private string $table = 'cart_items';
    
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }
    
    /**
     * Получить корзину пользователя
     */
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT c.*, p.name, p.price, p.image_url, p.is_weighted, p.weight_unit, cat.name as category_name
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                LEFT JOIN categories cat ON p.category_id = cat.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC";
        
        return $this->db->query($sql, [$userId]);
    }
    
    /**
     * Получить товар в корзине
     */
    public function getItem(int $userId, int $productId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        return $this->db->queryOne($sql, [$userId, $productId]);
    }
    
    /**
     * Добавить товар в корзину
     */
    public function add(int $userId, int $productId, float $quantity = 1): bool
    {
        // Проверяем, есть ли уже такой товар
        $existing = $this->getItem($userId, $productId);
        
        if ($existing) {
            // Обновляем количество
            return $this->updateQuantity($userId, $productId, $existing['quantity'] + $quantity);
        }
        
        // Добавляем новый товар
        $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + ?";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId, $productId, $quantity, $quantity]);
    }
    
    /**
     * Обновить количество товара
     */
    public function updateQuantity(int $userId, int $productId, float $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->remove($userId, $productId);
        }
        
        $sql = "UPDATE {$this->table} SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$quantity, $userId, $productId]);
    }
    
    /**
     * Удалить товар из корзины
     */
    public function remove(int $userId, int $productId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId, $productId]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Очистить корзину пользователя
     */
    public function clear(int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * Получить количество товаров в корзине
     */
    public function getCount(int $userId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $result = $this->db->queryOne($sql, [$userId]);
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Получить общую сумму корзины
     */
    public function getTotal(int $userId): float
    {
        $sql = "SELECT SUM(c.quantity * p.price) as total
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        
        $result = $this->db->queryOne($sql, [$userId]);
        return (float) ($result['total'] ?? 0);
    }
    
    /**
     * Перенести корзину из сессии в БД (при авторизации)
     */
    public function migrateFromSession(int $userId, array $sessionCart): void
    {
        foreach ($sessionCart as $productId => $item) {
            $quantity = floatval($item['quantity'] ?? 1);
            $this->add($userId, intval($productId), $quantity);
        }
    }
    
    /**
     * Получить товары для создания заказа
     */
    public function getItemsForOrder(int $userId): array
    {
        $sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.is_weighted
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        
        return $this->db->query($sql, [$userId]);
    }
}