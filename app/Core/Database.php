<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Класс для работы с MySQL базой данных
 */
class Database
{
    private static ?PDO $connection = null;
    
    public function __construct()
    {
        if (self::$connection === null) {
            $this->connect();
        }
    }
    
    /**
     * Установить соединение с базой данных
     */
    private function connect(): void
    {
        try {
            $config = DatabaseConfig::getConfig();
            $dsn = DatabaseConfig::getDsn();
            
            self::$connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new \Exception("Ошибка подключения к базе данных");
        }
    }
    
    /**
     * Получить соединение
     */
    public function getConnection(): PDO
    {
        if (self::$connection === null) {
            $this->connect();
        }
        
        return self::$connection;
    }
    
    /**
     * Найти запись по ID
     */
    public function findById(string $table, int $id): ?array
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Найти записи по условию
     */
    public function findBy(string $table, string $field, $value): array
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Найти одну запись по условию
     */
    public function findOneBy(string $table, string $field, $value): ?array
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$table} WHERE {$field} = ? LIMIT 1");
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Получить все записи из таблицы
     */
    public function read(string $table): array
    {
        $stmt = self::$connection->query("SELECT * FROM {$table}");
        
        return $stmt->fetchAll();
    }
    
    /**
     * Добавить запись
     */
    public function insert(string $table, array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        // Добавляем двоеточие к ключам для PDO плейсхолдеров
        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
        
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        
        return (int) self::$connection->lastInsertId();
    }
    
    /**
     * Добавить запись с сохранением ID
     */
    public function insertWithId(string $table, array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        try {
            // Добавляем двоеточие к ключам для PDO плейсхолдеров
            $params = [];
            foreach ($data as $key => $value) {
                $params[":$key"] = $value;
            }
            
            $stmt = self::$connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Insert with ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Обновить запись
     */
    public function update(string $table, int $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }
        
        $setParts = [];
        foreach ($data as $field => $value) {
            $setParts[] = "{$field} = :{$field}";
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE id = :id",
            $table,
            implode(', ', $setParts)
        );
        
        // Добавляем двоеточие к ключам для PDO плейсхолдеров
        $params = [':id' => $id];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
        
        $stmt = self::$connection->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Удалить запись
     */
    public function delete(string $table, int $id): bool
    {
        $stmt = self::$connection->prepare("DELETE FROM {$table} WHERE id = ?");
        
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Выполнить произвольный запрос
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Выполнить запрос и вернуть одну запись
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Получить следующий ID для вставки
     */
    public function getNextId(string $table): int
    {
        $stmt = self::$connection->query("SELECT MAX(id) as max_id FROM {$table}");
        $result = $stmt->fetch();
        
        return (int) ($result['max_id'] ?? 0) + 1;
    }
    
    /**
     * Записать все данные в таблицу (полная замена)
     * ВНИМАНИЕ: Используйте только для миграции или тестов!
     */
    public function write(string $table, array $data): bool
    {
        // Для MySQL это не применимо напрямую, используем транзакцию
        // для полной перезаписи данных
        try {
            self::$connection->beginTransaction();
            
            // Очищаем таблицу
            self::$connection->exec("TRUNCATE TABLE {$table}");
            
            // Вставляем все записи
            foreach ($data as $row) {
                if (!empty($row)) {
                    $this->insert($table, $row);
                }
            }
            
            self::$connection->commit();
            return true;
        } catch (\Exception $e) {
            self::$connection->rollBack();
            error_log("Write error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Получить количество записей
     */
    public function count(string $table, string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        $result = $this->queryOne($sql, $params);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Получить записи с пагинацией
     */
    public function paginate(string $table, int $page = 1, int $perPage = 20, string $orderBy = 'id DESC', string $where = '', array $params = []): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage)); // Максимум 100 записей
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        
        $items = $this->query($sql, $params);
        $total = $this->count($table, $where, $params);
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
            'has_more' => ($page * $perPage) < $total
        ];
    }
    
    /**
     * Выполнить запрос с блокировкой (для транзакций)
     */
    public function queryForUpdate(string $sql, array $params = []): ?array
    {
        if (strpos(strtoupper($sql), 'SELECT') === 0 && strpos(strtoupper($sql), 'FOR UPDATE') === false) {
            $sql .= ' FOR UPDATE';
        }
        
        return $this->queryOne($sql, $params);
    }
    
    /**
     * Начать транзакцию
     */
    public function beginTransaction(): bool
    {
        return self::$connection->beginTransaction();
    }
    
    /**
     * Подтвердить транзакцию
     */
    public function commit(): bool
    {
        return self::$connection->commit();
    }
    
    /**
     * Откатить транзакцию
     */
    public function rollBack(): bool
    {
        return self::$connection->rollBack();
    }
}
