<?php

namespace App\Core;

/**
 * Класс для работы с HTTP запросом
 */
class Request
{
    private array $get;
    private array $post;
    private array $server;
    private ?array $json = null;
    
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }
    
    /**
     * Получить путь запроса
     */
    public function getPath(): string
    {
        $path = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return $path ?: '/';
    }
    
    /**
     * Получить метод запроса
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }
    
    /**
     * Проверка на POST запрос
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }
    
    /**
     * Проверка на AJAX запрос
     */
    public function isAjax(): bool
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) 
            && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Получить GET параметр
     */
    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }
    
    /**
     * Получить POST параметр
     */
    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }
    
    /**
     * Получить все GET параметры
     */
    public function allGet(): array
    {
        return $this->get;
    }
    
    /**
     * Получить все POST параметры
     */
    public function allPost(): array
    {
        return $this->post;
    }
    
    /**
     * Получить JSON данные из тела запроса
     */
    public function json(): array
    {
        if ($this->json === null) {
            $content = file_get_contents('php://input');
            $this->json = json_decode($content, true) ?? [];
        }
        
        return $this->json;
    }
    
    /**
     * Получить параметр из JSON тела
     */
    public function input(string $key, $default = null)
    {
        $json = $this->json();
        return $json[$key] ?? $default;
    }
    
    /**
     * Получить данные в зависимости от метода
     */
    public function getData(): array
    {
        if ($this->isPost()) {
            $contentType = $this->server['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                return $this->json();
            }
            return $this->allPost();
        }
        
        return $this->allGet();
    }
    
    /**
     * Получить заголовок
     */
    public function header(string $key, $default = null): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $default;
    }
    
    /**
     * Получить IP адрес клиента
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Получить User-Agent
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
}