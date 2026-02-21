<?php

namespace App\Core;

/**
 * Класс безопасности (CSRF, валидация, санитизация)
 */
class Security
{
    private const CSRF_TOKEN_LENGTH = 32;
    private const CSRF_SESSION_KEY = '_csrf_token';
    
    /**
     * Генерация CSRF токена
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION[self::CSRF_SESSION_KEY])) {
            $_SESSION[self::CSRF_SESSION_KEY] = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
        }
        
        return $_SESSION[self::CSRF_SESSION_KEY];
    }
    
    /**
     * Проверка CSRF токена
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        if ($token === null) {
            return false;
        }
        
        $sessionToken = $_SESSION[self::CSRF_SESSION_KEY] ?? null;
        
        if ($sessionToken === null) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Получить поле CSRF для формы
     */
    public static function csrfField(): string
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Получить CSRF токен для AJAX
     */
    public static function csrfMeta(): string
    {
        $token = self::generateCsrfToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Санитизация строки
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Санитизация массива
     */
    public static function sanitizeArray(array $data): array
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::sanitizeArray($value);
            } elseif (is_string($value)) {
                $result[$key] = self::sanitize($value);
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Удаление опасных символов
     */
    public static function stripTags(string $input, array $allowedTags = []): string
    {
        if (empty($allowedTags)) {
            return strip_tags($input);
        }
        
        $allowed = '<' . implode('><', $allowedTags) . '>';
        return strip_tags($input, $allowed);
    }
    
    /**
     * Валидация email
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Валидация телефона (казахстанский формат)
     */
    public static function validatePhone(string $phone): bool
    {
        // Удаляем все нецифровые символы
        $digits = preg_replace('/[^0-9]/', '', $phone);
        
        // Проверяем длину (10-11 цифр для Казахстана)
        $length = strlen($digits);
        return $length >= 10 && $length <= 12;
    }
    
    /**
     * Нормализация телефона
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        
        // Если начинается с 8, заменяем на 7
        if (strlen($digits) === 11 && $digits[0] === '8') {
            $digits = '7' . substr($digits, 1);
        }
        
        // Если 10 цифр, добавляем 7 в начало
        if (strlen($digits) === 10) {
            $digits = '7' . $digits;
        }
        
        return $digits;
    }
    
    /**
     * Валидация пароля
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов';
        }
        
        if (strlen($password) > 72) {
            $errors[] = 'Пароль не должен превышать 72 символа';
        }
        
        return $errors;
    }
    
    /**
     * Хеширование пароля
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Проверка пароля
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Генерация случайной строки
     */
    public static function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Валидация числа
     */
    public static function validateNumber($value, ?float $min = null, ?float $max = null): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        
        $num = floatval($value);
        
        if ($min !== null && $num < $min) {
            return false;
        }
        
        if ($max !== null && $num > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Валидация целого числа
     */
    public static function validateInteger($value, ?int $min = null, ?int $max = null): bool
    {
        if (!is_numeric($value) || floor($value) != $value) {
            return false;
        }
        
        $num = intval($value);
        
        if ($min !== null && $num < $min) {
            return false;
        }
        
        if ($max !== null && $num > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Валидация URL
     */
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Проверка на XSS в входных данных
     */
    public static function detectXss(string $input): bool
    {
        $patterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Защита от SQL инъекций (для JSON-хранилища - экранирование)
     */
    public static function escapeJson(string $input): string
    {
        return addslashes($input);
    }
}
