<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Validator;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;

/**
 * Контроллер админ-панели
 */
class AdminController extends Controller
{
    private UserModel $userModel;
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private OrderModel $orderModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel($this->db);
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        $this->orderModel = new OrderModel($this->db);
    }
    
    /**
     * Страница админ-панели
     */
    public function dashboard(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $orders = $this->orderModel->getAll();
        $products = $this->productModel->getAllWithCategories();
        $categories = $this->categoryModel->getAll();
        $users = $this->userModel->getAll();
        
        // Очищаем пароли
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        return $this->render('admin', [
            'orders' => $orders,
            'products' => $products,
            'categories' => $categories,
            'users' => $users
        ]);
    }
    
    // ==================== ТОВАРЫ ====================
    
    /**
     * API: Получить все товары
     */
    public function getProducts(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $products = $this->productModel->getAll();
        return $this->json($products);
    }
    
    /**
     * API: Создать товар
     */
    public function createProduct(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'name' => 'required,min:2',
            'price' => 'required,numeric,min_value:0',
            'category_id' => 'required,integer'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        // Проверка категории
        if (!$this->categoryModel->exists(intval($data['category_id']))) {
            return $this->error('Категория не найдена', 404);
        }
        
        $productId = $this->productModel->create($data);
        
        return $this->json([
            'success' => true,
            'product_id' => $productId
        ]);
    }
    
    /**
     * API: Обновить товар
     */
    public function updateProduct(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Проверка существования товара
        if ($this->productModel->findById($id) === null) {
            return $this->error('Товар не найден', 404);
        }
        
        // Проверка категории если указана
        if (isset($data['category_id']) && !$this->categoryModel->exists(intval($data['category_id']))) {
            return $this->error('Категория не найдена', 404);
        }
        
        $this->productModel->update($id, $data);
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Удалить товар
     */
    public function deleteProduct(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        if (!$this->productModel->delete($id)) {
            return $this->error('Товар не найден', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    // ==================== КАТЕГОРИИ ====================
    
    /**
     * API: Получить все категории
     */
    public function getCategories(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $categories = $this->categoryModel->getAll();
        return $this->json($categories);
    }
    
    /**
     * API: Создать категорию
     */
    public function createCategory(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'name' => 'required,min:2'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        $categoryId = $this->categoryModel->create($data);
        
        return $this->json([
            'success' => true,
            'category_id' => $categoryId
        ]);
    }
    
    /**
     * API: Обновить категорию
     */
    public function updateCategory(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        if (!$this->categoryModel->update($id, $data)) {
            return $this->error('Категория не найдена', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Удалить категорию
     */
    public function deleteCategory(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        if (!$this->categoryModel->delete($id)) {
            return $this->error('Категория не найдена', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    // ==================== ЗАКАЗЫ ====================
    
    /**
     * API: Получить все заказы
     */
    public function getOrders(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $orders = $this->orderModel->getAll();
        return $this->json($orders);
    }
    
    /**
     * API: Обновить статус заказа
     */
    public function updateOrderStatus(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация статуса
        if (!isset($data['status']) || !in_array($data['status'], OrderModel::VALID_STATUSES)) {
            return $this->error('Неверный статус', 400);
        }
        
        if (!$this->orderModel->updateStatus($id, $data['status'])) {
            return $this->error('Заказ не найден', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Архивировать заказ
     */
    public function archiveOrder(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        if (!$this->orderModel->archive($id)) {
            return $this->error('Заказ не найден', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Восстановить заказ из архива
     */
    public function restoreOrder(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        if (!$this->orderModel->restoreFromArchive($id)) {
            return $this->error('Заказ не найден в архиве', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Получить архив заказов
     */
    public function getArchive(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $archive = $this->orderModel->getArchive();
        return $this->json($archive);
    }
    
    // ==================== ПОЛЬЗОВАТЕЛИ ====================
    
    /**
     * API: Получить всех пользователей
     */
    public function getUsers(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $users = $this->userModel->getAll();
        
        // Очищаем пароли
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        return $this->json($users);
    }
    
    /**
     * API: Обновить пользователя
     */
    public function updateUser(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Проверка существования пользователя
        if ($this->userModel->findById($id) === null) {
            return $this->error('Пользователь не найден', 404);
        }
        
        // Валидация роли если указана
        if (isset($data['role'])) {
            $validRoles = ['user', 'admin', 'courier'];
            if (!in_array($data['role'], $validRoles)) {
                return $this->error('Неверная роль', 400);
            }
        }
        
        $this->userModel->update($id, $data);
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Удалить пользователя
     */
    public function deleteUser(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        // Нельзя удалить себя
        if ($id === $this->getUserId()) {
            return $this->error('Нельзя удалить свой аккаунт', 400);
        }
        
        if (!$this->userModel->delete($id)) {
            return $this->error('Пользователь не найден', 404);
        }
        
        return $this->json(['success' => true]);
    }
    
    // ==================== СТАТИСТИКА ====================
    
    /**
     * API: Получить статистику
     */
    public function getStats(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $orderStats = $this->orderModel->getStats();
        $users = $this->userModel->getAll();
        $products = $this->productModel->getAll();
        
        return $this->json([
            'total_users' => count($users),
            'total_products' => count($products),
            'total_orders' => $orderStats['total'],
            'total_revenue' => $orderStats['total_revenue'],
            'orders_by_status' => $orderStats['by_status']
        ]);
    }
}