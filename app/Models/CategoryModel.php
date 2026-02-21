<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Security;

/**
 * Модель категории
 */
class CategoryModel
{
    private Database $db;
    private string $table = 'categories';
    
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }
    
    /**
     * Найти категорию по ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->findById($this->table, $id);
    }
    
    /**
     * Получить все категории
     */
    public function getAll(): array
    {
        return $this->db->read($this->table);
    }
    
    /**
     * Создать категорию
     */
    public function create(array $data): int
    {
        $category = [
            'name' => Security::sanitize($data['name']),
            'created_at' => date('c')
        ];
        
        return $this->db->insert($this->table, $category);
    }
    
    /**
     * Обновить категорию
     */
    public function update(int $id, array $data): bool
    {
        $updates = [];
        
        if (isset($data['name'])) {
            $updates['name'] = Security::sanitize($data['name']);
        }
        
        return $this->db->update($this->table, $id, $updates);
    }
    
    /**
     * Удалить категорию
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, $id);
    }
    
    /**
     * Получить карту категорий (id => name)
     */
    public function getMap(): array
    {
        $categories = $this->getAll();
        $map = [];
        
        foreach ($categories as $cat) {
            $map[$cat['id']] = $cat['name'];
        }
        
        return $map;
    }
    
    /**
     * Проверка существования категории
     */
    public function exists(int $id): bool
    {
        return $this->findById($id) !== null;
    }
}