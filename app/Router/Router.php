<?php

namespace App\Router;

use App\Core\Request;
use App\Core\Response;

/**
 * Класс маршрутизатора
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];
    
    /**
     * Добавить GET маршрут
     */
    public function get(string $path, $handler): self
    {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }
    
    /**
     * Добавить POST маршрут
     */
    public function post(string $path, $handler): self
    {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }
    
    /**
     * Добавить PUT маршрут
     */
    public function put(string $path, $handler): self
    {
        $this->addRoute('PUT', $path, $handler);
        return $this;
    }
    
    /**
     * Добавить DELETE маршрут
     */
    public function delete(string $path, $handler): self
    {
        $this->addRoute('DELETE', $path, $handler);
        return $this;
    }
    
    /**
     * Добавить маршрут для нескольких методов
     */
    public function match(array $methods, string $path, $handler): self
    {
        foreach ($methods as $method) {
            $this->addRoute(strtoupper($method), $path, $handler);
        }
        return $this;
    }
    
    /**
     * Группа маршрутов
     */
    public function group(array $options, callable $callback): self
    {
        $prefix = $options['prefix'] ?? '';
        $middleware = $options['middleware'] ?? [];
        
        $previousPrefix = $this->currentPrefix ?? '';
        $previousMiddleware = $this->currentMiddleware ?? [];
        
        $this->currentPrefix = $previousPrefix . $prefix;
        $this->currentMiddleware = array_merge($previousMiddleware, (array)$middleware);
        
        $callback($this);
        
        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
        
        return $this;
    }
    
    /**
     * Добавить middleware
     */
    public function middleware(callable $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }
    
    /**
     * Диспетчеризация запроса
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        // Ищем точное совпадение
        $route = $this->findRoute($method, $path);
        
        if ($route === null) {
            return (new Response())->error('Not Found', 404);
        }
        
        // Выполняем middleware
        foreach ($this->middlewares as $middleware) {
            $response = $middleware($request);
            if ($response instanceof Response) {
                return $response;
            }
        }
        
        // Выполняем обработчик
        return $this->callHandler($route['handler'], $request, $route['params']);
    }
    
    private function addRoute(string $method, string $path, $handler): void
    {
        $prefix = $this->currentPrefix ?? '';
        $fullPath = $prefix . $path;
        
        // Преобразуем путь в регулярное выражение
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[$method][] = [
            'path' => $fullPath,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $this->currentMiddleware ?? []
        ];
    }
    
    private function findRoute(string $method, string $path): ?array
    {
        $routes = $this->routes[$method] ?? [];
        
        foreach ($routes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                // Извлекаем параметры
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                
                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                    'middleware' => $route['middleware']
                ];
            }
        }
        
        return null;
    }
    
    private function callHandler($handler, Request $request, array $params): Response
    {
        if (is_callable($handler)) {
            $result = $handler($request, ...array_values($params));
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $result = $controller->$method($request, ...array_values($params));
        } else {
            return (new Response())->error('Invalid handler', 500);
        }
        
        if ($result instanceof Response) {
            return $result;
        }
        
        return (new Response())->json($result);
    }
}