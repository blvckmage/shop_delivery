<?php

namespace App\Core;

/**
 * Конфигурация базы данных MySQL
 */
class DatabaseConfig
{
    private static ?array $config = null;
    
    /**
     * Получить конфигурацию базы данных
     */
    public static function getConfig(): array
    {
        if (self::$config === null) {
            // Загружаем переменные окружения
            Env::load();
            
            self::$config = [
                'host' => Env::get('DB_HOST', 'localhost'),
                'port' => Env::get('DB_PORT', '3306'),
                'database' => Env::get('DB_NAME', 'delivery_shop'),
                'username' => Env::get('DB_USER', 'root'),
                'password' => Env::get('DB_PASS', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            ];
        }
        
        return self::$config;
    }
    
    /**
     * Получить DSN строку подключения
     */
    public static function getDsn(): string
    {
        $config = self::getConfig();
        
        return sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=%s",
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );
    }
}
