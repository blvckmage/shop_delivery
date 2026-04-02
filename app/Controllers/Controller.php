<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Security;

/**
 * Базовый контроллер
 */
class Controller
{
    protected Database $db;
    protected Session $session;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->session = new Session();
    }
    
    /**
     * Рендер шаблона
     */
    protected function render(string $template, array $data = []): Response
    {
        extract($data);
        $isLoggedIn = $this->session->isLoggedIn();
        $user = $this->session->getUser();
        $csrfField = Security::csrfField();
        $csrfMeta = Security::csrfMeta();
        
        ob_start();
        include dirname(__DIR__, 2) . "/templates/$template.php";
        $content = ob_get_clean();
        
        return (new Response())->html($content);
    }
    
    /**
     * JSON ответ
     */
    protected function json($data, int $statusCode = 200): Response
    {
        return (new Response())->json($data, $statusCode);
    }
    
    /**
     * Успешный JSON ответ
     */
    protected function success($data = null, string $message = 'Success'): Response
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
    protected function error(string $message, int $statusCode = 400, $errors = null): Response
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
    protected function redirect(string $url): Response
    {
        return (new Response())->redirect($url);
    }
    
    /**
     * Проверка авторизации
     */
    protected function requireAuth(): ?Response
    {
        if (!$this->session->isLoggedIn()) {
            if ($this->isApiRequest()) {
                return $this->error('Требуется авторизация', 401);
            }
            return $this->redirect('/login');
        }
        
        return null;
    }
    
    /**
     * Проверка роли админа
     */
    protected function requireAdmin(): ?Response
    {
        $authError = $this->requireAuth();
        if ($authError !== null) {
            return $authError;
        }
        
        if (!$this->session->isAdmin()) {
            if ($this->isApiRequest()) {
                return $this->error('Доступ запрещен', 403);
            }
            return $this->redirect('/');
        }
        
        return null;
    }
    
    /**
     * Проверка роли курьера
     */
    protected function requireCourier(): ?Response
    {
        $authError = $this->requireAuth();
        if ($authError !== null) {
            return $authError;
        }
        
        if (!$this->session->isCourier()) {
            if ($this->isApiRequest()) {
                return $this->error('Доступ запрещен', 403);
            }
            return $this->redirect('/');
        }
        
        return null;
    }
    
    /**
     * Проверка CSRF токена
     */
    protected function verifyCsrf(Request $request): bool
    {
        $token = $request->input('_csrf') 
            ?? $request->header('X-CSRF-TOKEN')
            ?? $request->header('CSRF-TOKEN');
        
        return Security::verifyCsrfToken($token);
    }
    
    /**
     * Проверка CSRF для POST запросов
     */
    protected function requireCsrf(Request $request): ?Response
    {
        if ($request->isPost() && !$this->verifyCsrf($request)) {
            return $this->error('Неверный CSRF токен', 403);
        }
        
        return null;
    }
    
    /**
     * Проверка API запроса
     */
    protected function isApiRequest(): bool
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return strpos($path, '/api/') === 0;
    }
    
    /**
     * Получить текущего пользователя
     */
    protected function getUser(): ?array
    {
        return $this->session->getUser();
    }
    
    /**
     * Получить ID текущего пользователя
     */
    protected function getUserId(): ?int
    {
        return $this->session->getUserId();
    }
    
    /**
     * Получить корзину из сессии
     */
    protected function getCart(): array
    {
        return $this->session->getCart();
    }
    
    /**
     * Установить корзину в сессию
     */
    protected function setCart(array $cart): void
    {
        $this->session->setCart($cart);
    }
    
    /**
     * Очистить корзину
     */
    protected function clearCart(): void
    {
        $this->session->clearCart();
    }
}