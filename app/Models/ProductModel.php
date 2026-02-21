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
        $products = $this->getAll();
        $categories = (new CategoryModel($this->db))->getAll();
        
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat['name'];
        }
        
        foreach ($products as &$product) {
            if (!empty($product['category_id']) && isset($categoryMap[$product['category_id']])) {
                $product['category_name'] = $categoryMap[$product['category_id']];
            }
        }
        
        return $products;
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
            'weight_unit' => Security::sanitize($data['weight_unit'] ?? ''),
            'created_at' => date('c')
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
        $products = $this->getAll();
        $query = mb_strtolower(trim($query), 'UTF-8');
        
        return array_filter($products, function($product) use ($query) {
            $name = mb_strtolower($product['name'] ?? '', 'UTF-8');
            $description = mb_strtolower($product['description'] ?? '', 'UTF-8');
            
            return strpos($name, $query) !== false || strpos($description, $query) !== false;
        });
    }
}