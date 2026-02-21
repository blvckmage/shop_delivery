<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\ProductModel;
use App\Models\CategoryModel;

/**
 * Контроллер сайта
 */
class SiteController extends Controller
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }
    
    /**
     * Главная страница
     */
    public function home(Request $request): Response
    {
        return $this->render('home');
    }
    
    /**
     * Каталог товаров
     */
    public function catalog(Request $request): Response
    {
        // Курьеров редиректим на их страницу
        if ($this->session->isCourier()) {
            return $this->redirect('/courier');
        }
        
        $categoryId = $request->get('category');
        
        if ($categoryId) {
            $products = $this->productModel->getByCategory(intval($categoryId));
        } else {
            $products = $this->productModel->getAllWithCategories();
        }
        
        $categories = $this->categoryModel->getAll();
        
        return $this->render('catalog', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
    
    /**
     * Страница профиля
     */
    public function profile(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        // Курьеров редиректим на их страницу
        if ($this->session->isCourier()) {
            return $this->redirect('/courier');
        }
        
        return $this->render('profile');
    }
    
    /**
     * Страница заказов пользователя
     */
    public function orders(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        // Курьеров редиректим на их страницу
        if ($this->session->isCourier()) {
            return $this->redirect('/courier');
        }
        
        return $this->render('orders');
    }
    
    /**
     * Страница чата
     */
    public function chat(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        // Курьеров редиректим на их страницу
        if ($this->session->isCourier()) {
            return $this->redirect('/courier');
        }
        
        return $this->render('chat');
    }
    
    /**
     * API: Получить товары
     */
    public function getProducts(Request $request): Response
    {
        $categoryId = $request->get('category');
        
        if ($categoryId) {
            $products = $this->productModel->getByCategory(intval($categoryId));
        } else {
            $products = $this->productModel->getAllWithCategories();
        }
        
        return $this->json(array_values($products));
    }
    
    /**
     * API: Получить категории
     */
    public function getCategories(Request $request): Response
    {
        $categories = $this->categoryModel->getAll();
        return $this->json($categories);
    }
    
    // ==================== ГЕОКОДИРОВАНИЕ ====================
    
    /**
     * API: Обратное геокодирование (координаты -> адрес)
     */
    public function reverseGeocode(Request $request): Response
    {
        $lat = $request->get('lat');
        $lon = $request->get('lon');
        
        if (!$lat || !$lon) {
            return $this->error('Требуются координаты lat и lon', 400);
        }
        
        $url = sprintf(
            'https://nominatim.openstreetmap.org/reverse?format=json&lat=%s&lon=%s&addressdetails=1',
            urlencode($lat),
            urlencode($lon)
        );
        
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: KazynaMarket/1.0\r\n"
            ]
        ]);
        
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) {
            return $this->error('Не удалось получить адрес', 500);
        }
        
        $data = json_decode($result, true);
        
        return $this->json($data);
    }
    
    /**
     * API: Поиск адреса (адрес -> координаты)
     */
    public function searchGeocode(Request $request): Response
    {
        $q = $request->get('q');

        if (!$q) {
            return $this->error('Требуется параметр q', 400);
        }

        // Try multiple search queries for better results - focused on Kentau, Turkestan region
        $searchQueries = [
            $q . ', Кентау, Туркестанская область, Казахстан',
            $q . ', Кентау, Казахстан',
            $q . ', Кентау',
            $q . ', Туркестанская область, Казахстан',
            $q . ', Казахстан'
        ];

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: KazynaMarket/1.0\r\n",
                'timeout' => 10
            ]
        ]);

        foreach ($searchQueries as $searchQuery) {
            $url = sprintf(
                'https://nominatim.openstreetmap.org/search?format=json&q=%s&addressdetails=1&limit=5',
                urlencode($searchQuery)
            );

            $result = @file_get_contents($url, false, $context);

            if ($result !== false) {
                $data = json_decode($result, true);
                if (is_array($data) && count($data) > 0) {
                    return $this->json($data);
                }
            }
        }

        // If no results found, return empty array
        return $this->json([]);
    }
    
    // ==================== ПРОФИЛЬ ====================
    
    /**
     * API: Получить избранные товары
     */
    public function getFavorites(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $favorites = $this->db->read('favorites');
        $userFavorites = [];
        
        foreach ($favorites as $fav) {
            if ($fav['user_id'] == $this->getUserId()) {
                $product = $this->productModel->findById($fav['product_id']);
                if ($product) {
                    $userFavorites[] = array_merge($product, ['favorite_id' => $fav['id']]);
                }
            }
        }
        
        return $this->json($userFavorites);
    }
    
    /**
     * API: Добавить/убрать из избранного
     */
    public function toggleFavorite(Request $request, int $productId): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $favorites = $this->db->read('favorites');
        $userId = $this->getUserId();
        
        // Ищем существующее
        foreach ($favorites as $key => $fav) {
            if ($fav['user_id'] == $userId && $fav['product_id'] == $productId) {
                // Удаляем
                unset($favorites[$key]);
                $this->db->write('favorites', array_values($favorites));
                return $this->json(['success' => true, 'is_favorite' => false]);
            }
        }
        
        // Добавляем
        $favorites[] = [
            'id' => $this->db->getNextId('favorites'),
            'user_id' => $userId,
            'product_id' => $productId,
            'created_at' => date('c')
        ];
        
        $this->db->write('favorites', $favorites);
        
        return $this->json(['success' => true, 'is_favorite' => true]);
    }
    
    /**
     * API: Удалить из избранного
     */
    public function removeFavorite(Request $request, int $productId): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $favorites = $this->db->read('favorites');
        $userId = $this->getUserId();
        
        foreach ($favorites as $key => $fav) {
            if ($fav['user_id'] == $userId && $fav['product_id'] == $productId) {
                unset($favorites[$key]);
                $this->db->write('favorites', array_values($favorites));
                break;
            }
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Получить отзывы пользователя
     */
    public function getReviews(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $reviews = $this->db->read('reviews');
        $userReviews = [];
        
        foreach ($reviews as $review) {
            if ($review['user_id'] == $this->getUserId()) {
                $product = $this->productModel->findById($review['product_id']);
                $userReviews[] = array_merge($review, [
                    'product_name' => $product['name'] ?? 'Неизвестный товар'
                ]);
            }
        }
        
        return $this->json($userReviews);
    }
    
    /**
     * API: Обновить профиль
     */
    public function updateProfile(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        $users = $this->db->read('users');
        $userId = $this->getUserId();
        
        foreach ($users as &$user) {
            if ($user['id'] == $userId) {
                if (isset($data['name'])) {
                    $user['name'] = \App\Core\Security::sanitize($data['name']);
                }
                if (isset($data['phone'])) {
                    $user['phone'] = \App\Core\Security::sanitize($data['phone']);
                }
                if (isset($data['email'])) {
                    $user['email'] = \App\Core\Security::sanitize($data['email']);
                }
                if (isset($data['address'])) {
                    $user['address'] = \App\Core\Security::sanitize($data['address']);
                }
                break;
            }
        }
        
        $this->db->write('users', $users);
        
        // Обновляем сессию
        $_SESSION['user']['name'] = $data['name'] ?? $_SESSION['user']['name'];
        
        return $this->json(['success' => true]);
    }
}
