<?php

namespace App\Core;

/**
 * Класс для загрузки переменных окружения из .env файла
 */
class Env
{
    private static array $vars = [];
    private static bool $loaded = false;
    
    /**
     * Загрузить .env файл
     */
    public static function load(?string $path = null): void
    {
        if (self::$loaded) {
            return;
        }
        
        $envPath = $path ?? dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envPath)) {
            return;
        }
        
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Пропускаем комментарии
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Парсим строку
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Удаляем кавычки
                $value = trim($value, '"\'');
                
                self::$vars[$name] = $value;
                
                // Устанавливаем в $_ENV и $_SERVER
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
                
                // Также через putenv для getenv()
                putenv("$name=$value");
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Получить значение переменной окружения
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }
        
        // Сначала проверяем $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Затем getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Затем наш кэш
        if (isset(self::$vars[$key])) {
            return self::$vars[$key];
        }
        
        return $default;
    }
    
    /**
     * Проверить существование переменной
     */
    public static function has(string $key): bool
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return isset($_ENV[$key]) || getenv($key) !== false || isset(self::$vars[$key]);
    }
}