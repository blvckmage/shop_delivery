<?php

namespace App\Core;

/**
 * Класс для работы с JSON-хранилищем данных
 * В будущем можно заменить на MySQL/PostgreSQL
 */
class Database
{
    private string $dataDir;
    
    public function __construct(?string $dataDir = null)
    {
        $this->dataDir = $dataDir ?? dirname(__DIR__, 2) . '/data';
    }
    
    /**
     * Чтение данных из JSON-файла
     */
    public function read(string $table): array
    {
        $file = $this->getFilePath($table);
        
        if (!file_exists($file)) {
            return [];
        }
        
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        
        return $data ?: [];
    }
    
    /**
     * Запись данных в JSON-файл
     */
    public function write(string $table, array $data): bool
    {
        $file = $this->getFilePath($table);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return file_put_contents($file, $json) !== false;
    }
    
    /**
     * Получение следующего ID
     */
    public function getNextId(string $table): int
    {
        $data = $this->read($table);
        
        if (empty($data)) {
            return 1;
        }
        
        return max(array_column($data, 'id')) + 1;
    }
    
    /**
     * Найти запись по ID
     */
    public function findById(string $table, int $id): ?array
    {
        $data = $this->read($table);
        
        foreach ($data as $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                return $item;
            }
        }
        
        return null;
    }
    
    /**
     * Найти записи по условию
     */
    public function findBy(string $table, string $field, $value): array
    {
        $data = $this->read($table);
        $results = [];
        
        foreach ($data as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                $results[] = $item;
            }
        }
        
        return $results;
    }
    
    /**
     * Найти одну запись по условию
     */
    public function findOneBy(string $table, string $field, $value): ?array
    {
        $results = $this->findBy($table, $field, $value);
        
        return $results[0] ?? null;
    }
    
    /**
     * Добавить запись
     */
    public function insert(string $table, array $item): int
    {
        $data = $this->read($table);
        $id = $this->getNextId($table);
        $item['id'] = $id;
        $data[] = $item;
        $this->write($table, $data);
        
        return $id;
    }
    
    /**
     * Обновить запись
     */
    public function update(string $table, int $id, array $updates): bool
    {
        $data = $this->read($table);
        $found = false;
        
        foreach ($data as &$item) {
            if (isset($item['id']) && $item['id'] == $id) {
                $item = array_merge($item, $updates);
                $found = true;
                break;
            }
        }
        
        if ($found) {
            $this->write($table, $data);
        }
        
        return $found;
    }
    
    /**
     * Удалить запись
     */
    public function delete(string $table, int $id): bool
    {
        $data = $this->read($table);
        $initialCount = count($data);
        $data = array_filter($data, fn($item) => !isset($item['id']) || $item['id'] != $id);
        $data = array_values($data);
        
        if (count($data) < $initialCount) {
            $this->write($table, $data);
            return true;
        }
        
        return false;
    }
    
    private function getFilePath(string $table): string
    {
        return $this->dataDir . '/' . $table . '.json';
    }
}