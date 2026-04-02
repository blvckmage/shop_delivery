<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Validator;
use App\Core\WhatsApp;
use App\Core\RateLimiter;
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
        
        return $this->render('admin');
    }
    
    // ==================== ТОВАРЫ ====================
    
    /**
     * API: Загрузить изображение товара
     */
    public function uploadProductImage(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        // Проверка rate limiting
        if (RateLimiter::tooManyAttempts('upload')) {
            $seconds = RateLimiter::availableIn('upload');
            return $this->error("Слишком много загрузок. Попробуйте через {$seconds} секунд", 429);
        }
        
        RateLimiter::attempt('upload');
        
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return $this->error('Файл не загружен', 400);
        }
        
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return $this->error('Разрешены только изображения (JPEG, PNG, GIF, WebP)', 400);
        }
        
        // Максимальный размер файла - 5MB
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return $this->error('Размер файла не должен превышать 5MB', 400);
        }
        
        // Дополнительная проверка MIME-типа по содержимому
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($realMime, $allowedTypes)) {
            return $this->error('Некорректный тип файла', 400);
        }
        
        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return $this->error('Ошибка сохранения файла', 500);
        }
        
        return $this->json([
            'success' => true,
            'url' => '/uploads/products/' . $filename
        ]);
    }
    
    /**
     * API: Получить все товары
     */
    public function getProducts(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        return $this->json($this->productModel->getAll());
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
        
        $validator = Validator::make($data, [
            'name' => 'required,min:2',
            'price' => 'required,numeric,min_value:0',
            'category_id' => 'required,integer'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        if (!$this->categoryModel->exists(intval($data['category_id']))) {
            return $this->error('Категория не найдена', 404);
        }
        
        $productId = $this->productModel->create([
            'name' => Security::sanitize($data['name']),
            'price' => floatval($data['price']),
            'category_id' => intval($data['category_id']),
            'image_url' => $data['image_url'] ?? '',
            'is_weighted' => isset($data['is_weighted']) ? intval($data['is_weighted']) : 0
        ]);
        
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
        
        if ($this->productModel->findById($id) === null) {
            return $this->error('Товар не найден', 404);
        }
        
        if (isset($data['category_id']) && !$this->categoryModel->exists(intval($data['category_id']))) {
            return $this->error('Категория не найдена', 404);
        }
        
        $this->productModel->update($id, [
            'name' => Security::sanitize($data['name'] ?? ''),
            'price' => floatval($data['price'] ?? 0),
            'category_id' => intval($data['category_id'] ?? 0),
            'image_url' => $data['image_url'] ?? '',
            'is_weighted' => isset($data['is_weighted']) ? intval($data['is_weighted']) : 0
        ]);
        
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
    
    /**
     * API: Импорт товаров из CSV
     */
    public function importProducts(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        $products = $data['products'] ?? [];
        
        if (empty($products) || !is_array($products)) {
            return $this->error('Нет данных для импорта', 400);
        }
        
        // Получаем все категории для проверки
        $categories = $this->categoryModel->getAll();
        $categoryIds = array_column($categories, 'id');
        
        $imported = 0;
        $errors = [];
        
        foreach ($products as $index => $product) {
            // Проверяем обязательные поля
            if (empty($product['name']) || empty($product['price']) || empty($product['category_id'])) {
                $errors[] = "Строка " . ($index + 1) . ": не все обязательные поля заполнены";
                continue;
            }
            
            // Проверяем существование категории
            if (!in_array(intval($product['category_id']), $categoryIds)) {
                $errors[] = "Строка " . ($index + 1) . ": категория ID {$product['category_id']} не найдена";
                continue;
            }
            
            try {
                $this->productModel->create([
                    'name' => Security::sanitize($product['name']),
                    'price' => floatval($product['price']),
                    'category_id' => intval($product['category_id']),
                    'image_url' => '',
                    'is_weighted' => isset($product['is_weighted']) ? intval($product['is_weighted']) : 0
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Строка " . ($index + 1) . ": ошибка создания товара";
            }
        }
        
        return $this->json([
            'success' => true,
            'imported' => $imported,
            'total' => count($products),
            'errors' => $errors
        ]);
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
        
        return $this->json($this->categoryModel->getAll());
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
        $users = $this->userModel->getAll();
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['id']] = $user;
        }
        
        // Добавляем информацию о курьере в каждый заказ
        foreach ($orders as &$order) {
            if (!empty($order['courier_id']) && isset($userMap[$order['courier_id']])) {
                $courier = $userMap[$order['courier_id']];
                $order['courier_name'] = $courier['name'] ?? 'Неизвестный';
                $order['courier_phone'] = $courier['phone'] ?? '';
            } else {
                $order['courier_name'] = null;
                $order['courier_phone'] = null;
            }
        }
        
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
        
        $status = $data['status'] ?? '';
        if ($status === '') {
            $status = OrderModel::STATUS_CREATED;
        }
        
        if (!in_array($status, OrderModel::VALID_STATUSES)) {
            return $this->error('Неверный статус: ' . $status, 400);
        }
        
        $order = $this->orderModel->findById($id);
        $previousStatus = $order['status'] ?? '';
        
        if (!$this->orderModel->updateStatus($id, $status)) {
            return $this->error('Заказ не найден', 404);
        }
        
        // Уведомление курьеров о новом доступном заказе
        if ($status === OrderModel::STATUS_ON_THE_WAY && $previousStatus !== OrderModel::STATUS_ON_THE_WAY) {
            ApiController::notifyCouriers(
                $this->db,
                'new_order',
                '📦 Новый заказ доступен!',
                "Заказ #{$id} готов к доставке. Адрес: {$order['address']}",
                ['order_id' => $id]
            );
        }
        
        // Отправляем уведомление в WhatsApp при изменении статуса
        $whatsapp = new WhatsApp();
        if ($whatsapp->isAvailable() && $previousStatus !== $status) {
            $whatsapp->notifyOrderStatus($id, $status);
            
            // Уведомление назначенному курьеру
            if (!empty($order['courier_id'])) {
                $courier = $this->userModel->findById($order['courier_id']);
                if ($courier && !empty($courier['phone'])) {
                    $courierPhone = $courier['whatsapp_phone'] ?? $courier['phone'];
                    
                    // При статусе "ОЖИДАНИЕ_КУРЬЕРА" - уведомляем что заказ готов
                    if ($status === OrderModel::STATUS_AWAITING_COURIER) {
                        $whatsapp->notifyCourierOrderStatus($courierPhone, $id, $status);
                    }
                }
            }
        }
        
        // Если статус "ДОСТАВЛЕН" - перемещаем заказ в архив
        if ($status === OrderModel::STATUS_DELIVERED) {
            $this->orderModel->archive($id);
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
        
        return $this->json($this->orderModel->getArchive());
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
        
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        return $this->json($users);
    }
    
    /**
     * API: Создать пользователя
     */
    public function createUser(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        $validator = Validator::make($data, [
            'name' => 'required,min:2',
            'phone' => 'required',
            'password' => 'required,min:4'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        if ($this->userModel->findByPhone($data['phone']) !== null) {
            return $this->error('Пользователь с таким телефоном уже существует', 409);
        }
        
        $userId = $this->userModel->create([
            'name' => Security::sanitize($data['name']),
            'phone' => Security::sanitize($data['phone']),
            'email' => Security::sanitize($data['email'] ?? ''),
            'password' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role' => $data['role'] ?? 'user'
        ]);
        
        return $this->json([
            'success' => true,
            'user_id' => $userId
        ]);
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
        
        if ($this->userModel->findById($id) === null) {
            return $this->error('Пользователь не найден', 404);
        }
        
        if (isset($data['role']) && !in_array($data['role'], ['user', 'admin', 'courier'])) {
            return $this->error('Неверная роль', 400);
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
        
        return $this->json([
            'total_users' => count($this->userModel->getAll()),
            'total_products' => count($this->productModel->getAll()),
            'total_orders' => $orderStats['total'],
            'total_revenue' => $orderStats['total_revenue'],
            'orders_by_status' => $orderStats['by_status']
        ]);
    }
    
    // ==================== КУРЬЕРЫ ====================
    
    /**
     * API: Получить курьеров
     */
    public function getCouriers(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $users = $this->userModel->getAll();
        $couriers = array_filter($users, fn($u) => ($u['role'] ?? '') === 'courier');
        
        foreach ($couriers as &$courier) {
            unset($courier['password']);
        }
        
        return $this->json(array_values($couriers));
    }
    
    /**
     * API: Получить запросы курьеров
     */
    public function getCourierRequests(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        return $this->json($this->db->read('courier_requests') ?? []);
    }
    
    // ==================== ЭКСПОРТ ====================
    
    /**
     * API: Экспорт отчёта
     */
    public function exportReport(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $format = $request->get('format', 'excel');
        $period = intval($request->get('period', 30));
        
        $orders = $this->orderModel->getRecent($period);
        
        if ($format === 'pdf') {
            return $this->exportPdf($orders, 'Отчёт по заказам');
        }
        
        return $this->exportExcel($orders, 'orders_report');
    }
    
    /**
     * API: Экспорт архива
     */
    public function exportArchive(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $format = $request->get('format', 'excel');
        $archive = $this->orderModel->getArchive();
        
        if ($format === 'pdf') {
            return $this->exportPdf($archive, 'Архив заказов');
        }
        
        return $this->exportExcel($archive, 'archive_report');
    }
    
    /**
     * Экспорт в Excel (CSV)
     */
    private function exportExcel(array $data, string $filename): Response
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
        ];
        
        $output = fopen('php://temp', 'r+');
        
        // BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");
        
        // Header row
        fputcsv($output, ['ID', 'Пользователь', 'Адрес', 'Статус', 'Сумма', 'Дата создания'], ';');
        
        $userMap = [];
        $users = $this->userModel->getAll();
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['name'];
        }
        
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $userMap[$row['user_id']] ?? 'Неизвестный',
                $row['address'],
                $row['status'],
                $row['total_price'],
                $row['created_at']
            ], ';');
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        
        return new Response($content, 200, $headers);
    }
    
    /**
     * Экспорт в PDF (простой HTML)
     */
    private function exportPdf(array $data, string $title): Response
    {
        $userMap = [];
        $users = $this->userModel->getAll();
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['name'];
        }
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $title . '</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4F46E5; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>' . $title . '</h1>
    <p>Дата: ' . date('d.m.Y H:i') . '</p>
    <table>
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Адрес</th>
            <th>Статус</th>
            <th>Сумма</th>
            <th>Дата</th>
        </tr>';
        
        foreach ($data as $row) {
            $html .= '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . htmlspecialchars($userMap[$row['user_id']] ?? 'Неизвестный') . '</td>
                <td>' . htmlspecialchars($row['address']) . '</td>
                <td>' . htmlspecialchars($row['status']) . '</td>
                <td>' . $row['total_price'] . ' ₸</td>
                <td>' . $row['created_at'] . '</td>
            </tr>';
        }
        
        $html .= '</table>
</body>
</html>';
        
        return new Response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="report.html"'
        ]);
    }
}