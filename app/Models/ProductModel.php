<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Security;

/**
 * Модель товара
 */
class ProductModel
{
    private Database $db;
    private string $table = 'products';
    
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }
    
    /**
     * Найти товар по ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->findById($this->table, $id);
    }
    
    /**
     * Получить все товары
     */
    public function getAll(): array
    {
        return $this->db->read($this->table);
    }
    
    /**
     * Получить товары с названиями категорий
     */
    public function getAllWithCategories(): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.id";
        
        return $this->db->query($sql);
    }
    
    /**
     * Получить товары по категории
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->db->findBy($this->table, 'category_id', $categoryId);
    }
    
    /**
     * Создать товар
     */
    public function create(array $data): int
    {
        $product = [
            'name' => Security::sanitize($data['name']),
            'description' => Security::sanitize($data['description'] ?? ''),
            'price' => floatval($data['price']),
            'category_id' => intval($data['category_id']),
            'image_url' => Security::sanitize($data['image_url'] ?? ''),
            'is_weighted' => intval($data['is_weighted'] ?? 0),
            'weight_unit' => Security::sanitize($data['weight_unit'] ?? '')
        ];
        
        return $this->db->insert($this->table, $product);
    }
    
    /**
     * Обновить товар
     */
    public function update(int $id, array $data): bool
    {
        $updates = [];
        
        if (isset($data['name'])) {
            $updates['name'] = Security::sanitize($data['name']);
        }
        
        if (isset($data['description'])) {
            $updates['description'] = Security::sanitize($data['description']);
        }
        
        if (isset($data['price'])) {
            $updates['price'] = floatval($data['price']);
        }
        
        if (isset($data['category_id'])) {
            $updates['category_id'] = intval($data['category_id']);
        }
        
        if (isset($data['image_url'])) {
            $updates['image_url'] = Security::sanitize($data['image_url']);
        }
        
        if (isset($data['is_weighted'])) {
            $updates['is_weighted'] = intval($data['is_weighted']);
        }
        
        if (isset($data['weight_unit'])) {
            $updates['weight_unit'] = Security::sanitize($data['weight_unit']);
        }
        
        if (empty($updates)) {
            return false;
        }
        
        return $this->db->update($this->table, $id, $updates);
    }
    
    /**
     * Удалить товар
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, $id);
    }
    
    /**
     * Поиск товаров по названию
     */
    public function search(string $query): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR p.description LIKE ?
                ORDER BY p.name";
        
        $searchTerm = "%{$query}%";
        return $this->db->query($sql, [$searchTerm, $searchTerm]);
    }
}
