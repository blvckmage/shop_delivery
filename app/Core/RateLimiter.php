<?php

namespace App\Core;

/**
 * Rate Limiter - защита от брутфорса и DDoS
 * Хранит данные в базе данных для надёжности
 */
class RateLimiter
{
    private const LIMITS = [
        'login' => ['attempts' => 5, 'window' => 300],      // 5 попыток за 5 минут
        'register' => ['attempts' => 3, 'window' => 3600],  // 3 попытки за час
        'api' => ['attempts' => 100, 'window' => 60],       // 100 запросов в минуту
        'upload' => ['attempts' => 10, 'window' => 60],     // 10 загрузок в минуту
    ];
    
    private static ?Database $db = null;
    
    /**
     * Инициализация подключения к БД
     */
    private static function getDb(): Database
    {
        if (self::$db === null) {
            self::$db = new Database();
        }
        return self::$db;
    }
    
    /**
     * Проверить и записать попытку
     */
    public static function attempt(string $type, ?string $identifier = null): bool
    {
        if (!isset(self::LIMITS[$type])) {
            return true;
        }
        
        $identifier = $identifier ?? self::getClientIdentifier();
        $limit = self::LIMITS[$type];
        
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Сначала очищаем старые записи
            self::cleanup($type, $identifier, $limit['window']);
            
            // Проверяем текущее количество попыток
            $sql = "SELECT attempts FROM rate_limits WHERE identifier = ? AND action = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$identifier, $type]);
            $result = $stmt->fetch();
            
            if ($result && $result['attempts'] >= $limit['attempts']) {
                return false;
            }
            
            // Записываем попытку
            if ($result) {
                $sql = "UPDATE rate_limits SET attempts = attempts + 1, last_attempt_at = NOW() 
                        WHERE identifier = ? AND action = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$identifier, $type]);
            } else {
                $sql = "INSERT INTO rate_limits (identifier, action, attempts, first_attempt_at, last_attempt_at) 
                        VALUES (?, ?, 1, NOW(), NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$identifier, $type]);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Rate limiter error: " . $e->getMessage());
            return true; // В случае ошибки БД разрешаем действие
        }
    }
    
    /**
     * Проверить, превышен ли лимит
     */
    public static function tooManyAttempts(string $type, ?string $identifier = null): bool
    {
        if (!isset(self::LIMITS[$type])) {
            return false;
        }
        
        $identifier = $identifier ?? self::getClientIdentifier();
        $limit = self::LIMITS[$type];
        
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Очищаем старые записи
            self::cleanup($type, $identifier, $limit['window']);
            
            $sql = "SELECT attempts FROM rate_limits WHERE identifier = ? AND action = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$identifier, $type]);
            $result = $stmt->fetch();
            
            return $result && $result['attempts'] >= $limit['attempts'];
        } catch (\Exception $e) {
            error_log("Rate limiter error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Получить оставшиеся попытки
     */
    public static function remaining(string $type, ?string $identifier = null): int
    {
        if (!isset(self::LIMITS[$type])) {
            return PHP_INT_MAX;
        }
        
        $identifier = $identifier ?? self::getClientIdentifier();
        $limit = self::LIMITS[$type];
        
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            self::cleanup($type, $identifier, $limit['window']);
            
            $sql = "SELECT attempts FROM rate_limits WHERE identifier = ? AND action = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$identifier, $type]);
            $result = $stmt->fetch();
            
            $attempts = $result ? (int) $result['attempts'] : 0;
            return max(0, $limit['attempts'] - $attempts);
        } catch (\Exception $e) {
            error_log("Rate limiter error: " . $e->getMessage());
            return $limit['attempts'];
        }
    }
    
    /**
     * Сбросить попытки
     */
    public static function clear(string $type, ?string $identifier = null): void
    {
        $identifier = $identifier ?? self::getClientIdentifier();
        
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            $sql = "DELETE FROM rate_limits WHERE identifier = ? AND action = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$identifier, $type]);
        } catch (\Exception $e) {
            error_log("Rate limiter clear error: " . $e->getMessage());
        }
    }
    
    /**
     * Получить время до разблокировки (в секундах)
     */
    public static function availableIn(string $type, ?string $identifier = null): int
    {
        if (!isset(self::LIMITS[$type])) {
            return 0;
        }
        
        $identifier = $identifier ?? self::getClientIdentifier();
        $limit = self::LIMITS[$type];
        
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            $sql = "SELECT first_attempt_at FROM rate_limits WHERE identifier = ? AND action = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$identifier, $type]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return 0;
            }
            
            $firstAttempt = strtotime($result['first_attempt_at']);
            $availableAt = $firstAttempt + $limit['window'];
            
            return max(0, $availableAt - time());
        } catch (\Exception $e) {
            error_log("Rate limiter error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Очистка старых записей
     */
    private static function cleanup(string $type, string $identifier, int $window): void
    {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            $sql = "DELETE FROM rate_limits WHERE identifier = ? AND action = ? AND first_attempt_at < DATE_SUB(NOW(), INTERVAL ? SECOND)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$identifier, $type, $window]);
        } catch (\Exception $e) {
            error_log("Rate limiter cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Глобальная очистка старых сессий и rate limits
     */
    public static function globalCleanup(): void
    {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Очищаем все rate limits старше 1 часа
            $conn->exec("DELETE FROM rate_limits WHERE first_attempt_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
            
            // Очищаем старые сессии (неактивные более 7 дней)
            $conn->exec("DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))");
        } catch (\Exception $e) {
            error_log("Global cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Получить идентификатор клиента
     */
    private static function getClientIdentifier(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        $realIp = $_SERVER['HTTP_X_REAL_IP'] ?? '';
        
        if ($realIp) {
            $ip = $realIp;
        } elseif ($forwarded) {
            $ip = explode(',', $forwarded)[0];
        }
        
        return trim($ip);
    }
    
    /**
     * Добавить заголовки Rate Limit в ответ
     */
    public static function addHeaders(string $type, ?string $identifier = null): array
    {
        if (!isset(self::LIMITS[$type])) {
            return [];
        }
        
        $limit = self::LIMITS[$type];
        
        return [
            'X-RateLimit-Limit' => $limit['attempts'],
            'X-RateLimit-Remaining' => self::remaining($type, $identifier),
            'X-RateLimit-Reset' => time() + $limit['window'],
        ];
    }
}