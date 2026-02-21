<?php

namespace App\Core;

/**
 * Класс для работы с HTTP ответом
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private ?string $content = null;
    
    /**
     * Установить статус код
     */
    public function setStatus(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }
    
    /**
     * Получить статус код
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Установить заголовок
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Установить содержимое
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Получить содержимое
     */
    public function getContent(): ?string
    {
        return $this->content;
    }
    
    /**
     * JSON ответ
     */
    public function json($data, int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this;
    }
    
    /**
     * Успешный JSON ответ
     */
    public function success($data = null, string $message = 'Success'): self
    {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    /**
     * Ошибка JSON ответ
     */
    public function error(string $message, int $statusCode = 400, $errors = null): self
    {
        $response = [
            'success' => false,
            'error' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        return $this->json($response, $statusCode);
    }
    
    /**
     * Редирект
     */
    public function redirect(string $url, int $statusCode = 302): self
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Location', $url);
        return $this;
    }
    
    /**
     * Рендер HTML
     */
    public function html(string $content, int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->content = $content;
        return $this;
    }
    
    /**
     * Отправить ответ
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        if ($this->content !== null) {
            echo $this->content;
        }
    }
    
    /**
     * Статические коды
     */
    public static function ok(): self
    {
        return (new self())->setStatus(200);
    }
    
    public static function created(): self
    {
        return (new self())->setStatus(201);
    }
    
    public static function badRequest(string $message = 'Bad Request'): self
    {
        return (new self())->setStatus(400);
    }
    
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return (new self())->setStatus(401);
    }
    
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return (new self())->setStatus(403);
    }
    
    public static function notFound(string $message = 'Not Found'): self
    {
        return (new self())->setStatus(404);
    }
    
    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return (new self())->setStatus(500);
    }
}