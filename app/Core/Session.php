<?php

namespace App\Core;

/**
 * Класс для работы с сессиями
 */
class Session
{
    private const FLASH_KEY = '_flash';
    private const CSRF_KEY = '_csrf_token';
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Установить значение в сессию
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Получить значение из сессии
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Проверить наличие ключа
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Удалить значение из сессии
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Получить и удалить значение
     */
    public function pull(string $key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }
    
    /**
     * Установить flash-сообщение
     */
    public function flash(string $key, $value): void
    {
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }
    
    /**
     * Получить flash-сообщение
     */
    public function getFlash(string $key, $default = null)
    {
        return $_SESSION[self::FLASH_KEY][$key] ?? $default;
    }
    
    /**
     * Получить и удалить flash-сообщение
     */
    public function pullFlash(string $key, $default = null)
    {
        $value = $this->getFlash($key, $default);
        unset($_SESSION[self::FLASH_KEY][$key]);
        return $value;
    }
    
    /**
     * Получить текущего пользователя
     */
    public function getUser(): ?array
    {
        return $this->get('user');
    }
    
    /**
     * Установить пользователя
     */
    public function setUser(array $user): void
    {
        $this->set('user', $user);
    }
    
    /**
     * Проверка авторизации
     */
    public function isLoggedIn(): bool
    {
        return $this->has('user');
    }
    
    /**
     * Проверка роли
     */
    public function hasRole(string $role): bool
    {
        $user = $this->getUser();
        return $user && ($user['role'] ?? '') === $role;
    }
    
    /**
     * Проверка админа
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Проверка курьера
     */
    public function isCourier(): bool
    {
        return $this->hasRole('courier');
    }
    
    /**
     * Проверка сборщика
     */
    public function isPicker(): bool
    {
        return $this->hasRole('picker');
    }
    
    /**
     * Выход из системы
     */
    public function logout(): void
    {
        session_destroy();
    }
    
    /**
     * Получить ID пользователя
     */
    public function getUserId(): ?int
    {
        $user = $this->getUser();
        return $user['id'] ?? null;
    }
    
    /**
     * Получить корзину
     */
    public function getCart(): array
    {
        return $this->get('cart', []);
    }
    
    /**
     * Установить корзину
     */
    public function setCart(array $cart): void
    {
        $this->set('cart', $cart);
    }
    
    /**
     * Очистить корзину
     */
    public function clearCart(): void
    {
        $this->remove('cart');
    }
    
    /**
     * Регенерация ID сессии
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }
    
    /**
     * Получить ID сессии
     */
    public function getId(): string
    {
        return session_id();
    }
}