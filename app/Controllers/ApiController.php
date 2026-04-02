<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Models\UserModel;
use App\Models\OrderModel;

/**
 * Контроллер API для курьера и чата
 */
class ApiController extends Controller
{
    private UserModel $userModel;
    private OrderModel $orderModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel($this->db);
        $this->orderModel = new OrderModel($this->db);
    }
    
    // ==================== КУРЬЕР ====================
    
    /**
     * Страница курьера
     */
    public function courierPage(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        return $this->render('courier');
    }
    
    /**
     * API: Получить статус смены курьера
     */
    public function getShiftStatus(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $courierId = $this->getUserId();
        
        // Получаем активную смену
        $activeShift = $this->db->queryOne(
            "SELECT * FROM courier_shifts WHERE courier_id = ? AND is_active = 1 LIMIT 1",
            [$courierId]
        );
        
        return $this->json([
            'is_on_shift' => $activeShift !== null,
            'shift' => $activeShift
        ]);
    }
    
    /**
     * API: Начать смену
     */
    public function startShift(Request $request): Response
    {
        try {
            $error = $this->requireCourier();
            if ($error !== null) {
                return $error;
            }
            
            $courierId = $this->getUserId();
            
            // Проверяем существование таблицы courier_shifts
            try {
                $this->db->query("SELECT 1 FROM courier_shifts LIMIT 1");
            } catch (\Exception $e) {
                // Создаем таблицу если не существует
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS courier_shifts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        courier_id INT NOT NULL,
                        started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        ended_at TIMESTAMP NULL DEFAULT NULL,
                        is_active TINYINT(1) DEFAULT 1,
                        FOREIGN KEY (courier_id) REFERENCES users(id) ON DELETE CASCADE,
                        INDEX idx_courier (courier_id),
                        INDEX idx_active (is_active),
                        INDEX idx_started (started_at)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }
            
            // Проверяем, нет ли уже активной смены
            $activeShift = $this->db->queryOne(
                "SELECT id FROM courier_shifts WHERE courier_id = ? AND is_active = 1",
                [$courierId]
            );
            
            if ($activeShift) {
                return $this->error('У вас уже есть активная смена', 400);
            }
            
            // Создаем новую смену
            $this->db->insert('courier_shifts', [
                'courier_id' => $courierId,
                'is_active' => 1
            ]);
            
            return $this->json([
                'success' => true,
                'message' => 'Смена начата'
            ]);
        } catch (\Exception $e) {
            error_log("Start shift error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json([
                'success' => false,
                'error' => 'Ошибка: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * API: Закончить смену
     */
    public function endShift(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $courierId = $this->getUserId();
        
        // Проверяем есть ли активные заказы
        $activeOrders = $this->orderModel->getByCourierId($courierId);
        $hasActiveOrders = array_filter($activeOrders, function($o) {
            return $o['status'] === OrderModel::STATUS_ON_THE_WAY;
        });
        
        if (count($hasActiveOrders) > 0) {
            return $this->error('Нельзя закончить смену с активными заказами', 400);
        }
        
        // Заканчиваем активную смену
        $this->db->query(
            "UPDATE courier_shifts SET is_active = 0, ended_at = NOW() WHERE courier_id = ? AND is_active = 1",
            [$courierId]
        );
        
        // Удаляем местоположение
        $this->db->query(
            "DELETE FROM courier_locations WHERE courier_id = ?",
            [$courierId]
        );
        
        return $this->json([
            'success' => true,
            'message' => 'Смена закончена'
        ]);
    }
    
    /**
     * API: Получить доступные заказы для курьера
     */
    public function courierOrders(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $available = $this->orderModel->getAvailableForCourier();
        $current = $this->orderModel->getByCourierId($this->getUserId());
        
        return $this->json([
            'available' => array_values($available),
            'current' => array_values($current)
        ]);
    }
    
    /**
     * API: Взять заказ курьером (автоматическое назначение)
     */
    public function courierTakeOrder(Request $request, int $id): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $courierId = $this->getUserId();
        
        // Проверяем, на смене ли курьер
        $activeShift = $this->db->queryOne(
            "SELECT id FROM courier_shifts WHERE courier_id = ? AND is_active = 1",
            [$courierId]
        );
        
        if (!$activeShift) {
            return $this->error('Вы должны быть на смене, чтобы брать заказы', 400);
        }
        
        // Проверка доступности заказа
        if (!$this->orderModel->isAvailableForCourier($id)) {
            return $this->error('Заказ недоступен или уже взят другим курьером', 400);
        }
        
        // Проверяем, есть ли у курьера уже активный заказ
        $currentOrders = $this->orderModel->getByCourierId($courierId);
        $activeOrders = array_filter($currentOrders, function($o) {
            return $o['status'] === OrderModel::STATUS_ON_THE_WAY;
        });
        
        if (count($activeOrders) > 0) {
            return $this->error('У вас уже есть активный заказ. Сначала доставьте его.', 400);
        }
        
        // Назначаем курьера и меняем статус на В_ПУТИ
        $this->orderModel->update($id, [
            'courier_id' => $this->getUserId(),
            'courier_request_id' => null,
            'status' => OrderModel::STATUS_ON_THE_WAY
        ]);
        
        // Получаем заказ для уведомлений
        $order = $this->orderModel->findById($id);
        
        // Уведомление для пользователя о назначении курьера
        if ($order) {
            self::notifyUser(
                $this->db,
                $order['user_id'],
                'courier_assigned',
                'Курьер назначен',
                "Ваш заказ #{$id} принят курьером и скоро будет доставлен!",
                ['order_id' => $id]
            );
        }
        
        // Уведомление для админов о взятии заказа
        self::notifyAdmins(
            $this->db,
            'order_taken',
            'Заказ взят курьером',
            "Курьер {$this->getUser()['name']} взял заказ #{$id}",
            ['order_id' => $id, 'courier_id' => $this->getUserId()]
        );
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Обновить статус заказа курьером
     */
    public function courierUpdateStatus(Request $request, int $id): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Проверка принадлежности заказа курьеру
        if (!$this->orderModel->belongsToCourier($id, $this->getUserId())) {
            return $this->error('Заказ не принадлежит вам', 403);
        }
        
        // Валидация статуса
        $validStatuses = [OrderModel::STATUS_ON_THE_WAY, OrderModel::STATUS_DELIVERED];
        if (!isset($data['status']) || !in_array($data['status'], $validStatuses)) {
            return $this->error('Неверный статус', 400);
        }
        
        $this->orderModel->updateStatus($id, $data['status']);
        
        // Получаем заказ для уведомлений
        $order = $this->orderModel->findById($id);
        
        // Если статус "ДОСТАВЛЕН" - перемещаем заказ в архив
        if ($data['status'] === OrderModel::STATUS_DELIVERED) {
            $this->orderModel->archive($id);
            
            // Уведомление для пользователя о доставке
            if ($order) {
                self::notifyUser(
                    $this->db,
                    $order['user_id'],
                    'order_delivered',
                    'Заказ доставлен!',
                    "Ваш заказ #{$id} успешно доставлен! Спасибо за покупку!",
                    ['order_id' => $id]
                );
            }
            
            // Уведомление для админов о доставке
            self::notifyAdmins(
                $this->db,
                'order_delivered',
                'Заказ доставлен',
                "Заказ #{$id} успешно доставлен курьером",
                ['order_id' => $id]
            );
        } elseif ($data['status'] === OrderModel::STATUS_ON_THE_WAY) {
            // Уведомление для пользователя о том, что курьер в пути
            if ($order) {
                self::notifyUser(
                    $this->db,
                    $order['user_id'],
                    'order_status',
                    'Курьер в пути',
                    "Курьер уже везет ваш заказ #{$id}!",
                    ['order_id' => $id]
                );
            }
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Отменить заказ курьером
     */
    public function courierCancelOrder(Request $request, int $id): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        // Проверка принадлежности заказа курьеру
        if (!$this->orderModel->belongsToCourier($id, $this->getUserId())) {
            return $this->error('Заказ не принадлежит вам', 403);
        }
        
        // Сбрасываем курьера - заказ снова доступен для других курьеров
        $this->orderModel->update($id, [
            'courier_id' => null,
            'courier_request_id' => null
        ]);
        
        // Получаем заказ для уведомления пользователя
        $order = $this->orderModel->findById($id);
        
        // Уведомление для пользователя об отмене курьером
        if ($order) {
            self::notifyUser(
                $this->db,
                $order['user_id'],
                'courier_cancelled',
                'Курьер отменил доставку',
                "Курьер отменил доставку заказа #{$id}. Ищем нового курьера.",
                ['order_id' => $id]
            );
        }
        
        // Уведомление для админов об отмене заказа курьером
        self::notifyAdmins(
            $this->db,
            'courier_cancelled',
            'Курьер отменил заказ',
            "Курьер {$this->getUser()['name']} отменил заказ #{$id}",
            ['order_id' => $id]
        );
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Обновить местоположение курьера
     */
    public function courierLocation(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Валидация
        if (!isset($data['lat']) || !isset($data['lng'])) {
            return $this->error('Требуются координаты', 400);
        }
        
        $courierId = $this->getUserId();
        $lat = floatval($data['lat']);
        $lng = floatval($data['lng']);
        
        // Проверяем, есть ли уже запись для этого курьера
        $existing = $this->db->queryOne(
            "SELECT id FROM courier_locations WHERE courier_id = ?",
            [$courierId]
        );
        
        if ($existing) {
            // Обновляем существующую запись
            $this->db->query(
                "UPDATE courier_locations SET latitude = ?, longitude = ?, updated_at = NOW() WHERE courier_id = ?",
                [$lat, $lng, $courierId]
            );
        } else {
            // Создаём новую запись
            $this->db->insert('courier_locations', [
                'courier_id' => $courierId,
                'latitude' => $lat,
                'longitude' => $lng
            ]);
        }
        
        return $this->json(['success' => true]);
    }
    
    // ==================== КУРЬЕР: МЕСТОПОЛОЖЕНИЕ ====================
    
    /**
     * API: Получить местоположение курьера заказа (для заказчика)
     */
    public function orderCourierLocation(Request $request, int $orderId): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        // Получаем заказ
        $order = $this->orderModel->findById($orderId);
        
        if (!$order) {
            return $this->error('Заказ не найден', 404);
        }
        
        // Проверяем принадлежность заказа пользователю
        if ($order['user_id'] != $this->getUserId()) {
            return $this->error('Заказ не принадлежит вам', 403);
        }
        
        // Проверяем есть ли курьер
        if (empty($order['courier_id'])) {
            return $this->json(['courier' => null, 'location' => null]);
        }
        
        // Получаем данные курьера
        $courier = $this->userModel->findById($order['courier_id']);
        if ($courier) {
            unset($courier['password']);
        }
        
        // Получаем местоположение курьера из MySQL
        $location = $this->db->queryOne(
            "SELECT latitude, longitude, updated_at FROM courier_locations WHERE courier_id = ?",
            [$order['courier_id']]
        );
        
        $locationResult = null;
        if ($location) {
            $locationResult = [
                'lat' => (float) $location['latitude'],
                'lng' => (float) $location['longitude'],
                'updated_at' => $location['updated_at']
            ];
        }
        
        return $this->json([
            'courier' => $courier,
            'location' => $locationResult,
            'order' => [
                'id' => $order['id'],
                'status' => $order['status'],
                'address' => $order['address']
            ]
        ]);
    }
    
    // ==================== АДМИН: КУРЬЕРЫ ====================
    
    /**
     * API: Получить курьеров (админ)
     */
    public function adminCouriers(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $couriers = $this->userModel->getCouriers();
        $orders = $this->orderModel->getAll();
        
        // Получаем местоположения из MySQL таблицы
        $courierLocations = $this->db->query("SELECT courier_id, latitude, longitude, updated_at FROM courier_locations");
        $locationMap = [];
        foreach ($courierLocations as $loc) {
            $locationMap[$loc['courier_id']] = [
                'lat' => (float) $loc['latitude'],
                'lng' => (float) $loc['longitude'],
                'updated_at' => $loc['updated_at']
            ];
        }
        
        foreach ($couriers as &$courier) {
            unset($courier['password']);
            
            // Текущий заказ
            $courier['current_order'] = null;
            foreach ($orders as $o) {
                if (isset($o['courier_id']) && $o['courier_id'] == $courier['id'] 
                    && in_array($o['status'], [OrderModel::STATUS_ON_THE_WAY])) {
                    $courier['current_order'] = $o;
                    break;
                }
            }
            
            // Местоположение
            $courier['location'] = $locationMap[$courier['id']] ?? null;
        }
        
        return $this->json(array_values($couriers));
    }
    
    /**
     * API: Запросы курьеров (админ)
     */
    public function adminCourierRequests(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $orders = $this->orderModel->getAll();
        $users = $this->userModel->getAll();
        $userMap = [];
        foreach ($users as $u) {
            $userMap[$u['id']] = $u;
        }
        
        $requests = [];
        foreach ($orders as $o) {
            // Показываем заказы с запросом от курьера (courier_request_id), но еще не назначенные
            if (!empty($o['courier_request_id']) && empty($o['courier_id']) && 
                $o['status'] === OrderModel::STATUS_ON_THE_WAY) {
                $courier = $userMap[$o['courier_request_id']] ?? null;
                $requests[] = [
                    'order_id' => $o['id'],
                    'courier_id' => $o['courier_request_id'],
                    'courier_name' => $courier['name'] ?? 'Неизвестный',
                    'order_address' => $o['address'],
                    'status' => $o['status'],
                    'created_at' => $o['created_at']
                ];
            }
        }
        
        return $this->json($requests);
    }
    
    // ==================== ЧАТ ====================
    
    /**
     * API: Получить контакты чата
     */
    public function chatContacts(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $users = $this->userModel->getAll();
        $contacts = [];
        
        foreach ($users as $user) {
            if ($user['id'] != $this->getUserId()) {
                unset($user['password']);
                $contacts[] = $user;
            }
        }
        
        return $this->json($contacts);
    }
    
    /**
     * API: Получить сообщения
     */
    public function chatMessages(Request $request, ?int $contactId = null): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $userId = $this->getUserId();
        $userRole = $this->getUser()['role'] ?? 'user';
        
        // Получаем ID всех админов
        $adminIds = array_map(fn($a) => $a['id'], $this->userModel->getAdmins());
        $adminIdsStr = implode(',', $adminIds);
        
        if ($contactId === null || $contactId === 0) {
            // Для обычного пользователя показываем сообщения с админами
            if ($userRole !== 'admin') {
                $messages = $this->db->query(
                    "SELECT c.*, u.name as sender_name, u.role as sender_role 
                     FROM chat c 
                     JOIN users u ON c.sender_id = u.id 
                     WHERE (c.sender_id = ? AND c.receiver_id IN ({$adminIdsStr}))
                        OR (c.sender_id IN ({$adminIdsStr}) AND c.receiver_id = ?)
                     ORDER BY c.created_at ASC",
                    [$userId, $userId]
                );
            } else {
                // Для админа - общий чат (устаревший) - возвращаем пустой массив
                $messages = [];
            }
        } else {
            // Личные сообщения с конкретным контактом
            $messages = $this->db->query(
                "SELECT c.*, u.name as sender_name, u.role as sender_role 
                 FROM chat c 
                 JOIN users u ON c.sender_id = u.id 
                 WHERE (c.sender_id = ? AND c.receiver_id = ?)
                    OR (c.sender_id = ? AND c.receiver_id = ?)
                 ORDER BY c.created_at ASC",
                [$userId, $contactId, $contactId, $userId]
            );
        }
        
        return $this->json(array_values($messages));
    }
    
    /**
     * API: Отправить сообщение
     * Все сообщения отправляются админу с телефоном 7771234567
     */
    public function chatSend(Request $request, ?int $contactId = null): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        if (empty($data['message'])) {
            return $this->error('Сообщение не может быть пустым', 400);
        }
        
        // Находим админа с телефоном 7771234567
        $admin = $this->db->queryOne(
            "SELECT id FROM users WHERE phone = ? AND role = 'admin' LIMIT 1",
            ['7771234567']
        );
        
        $receiverId = $admin['id'] ?? 1; // Fallback на ID 1 если админ не найден
        
        // Сохраняем сообщение в MySQL
        $this->db->insert('chat', [
            'sender_id' => $this->getUserId(),
            'receiver_id' => $receiverId,
            'message' => Security::sanitize($data['message']),
            'is_read' => 0
        ]);
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Пометить сообщения прочитанными
     */
    public function chatMarkRead(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $userId = $this->getUserId();
        
        // Помечаем все сообщения от админов к текущему пользователю как прочитанные
        $this->db->query(
            "UPDATE chat SET is_read = 1 WHERE receiver_id = ? AND is_read = 0",
            [$userId]
        );
        
        return $this->json(['success' => true]);
    }
    
    // ==================== АДМИН: ЗАКАЗЫ ====================
    
    /**
     * API: Подтвердить запрос курьера
     */
    public function confirmCourierRequest(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        $courierId = intval($data['courier_id'] ?? 0);
        
        if ($courierId <= 0) {
            return $this->error('Неверный ID курьера', 400);
        }
        
        // Переносим запрос в назначение: назначаем курьера и очищаем запрос
        $this->orderModel->update($id, [
            'courier_id' => $courierId,
            'courier_request_id' => null
        ]);
        
        // Уведомление для курьера о назначении заказа
        self::notifyCourier(
            $this->db,
            $courierId,
            'order_assigned',
            'Заказ назначен',
            "Вам назначен заказ #{$id}. Заберите его в магазине!",
            ['order_id' => $id]
        );
        
        // Уведомление для пользователя о смене статуса
        $order = $this->orderModel->findById($id);
        if ($order) {
            self::notifyUser(
                $this->db,
                $order['user_id'],
                'order_status',
                'Заказ в пути',
                "Ваш заказ #{$id} передан курьеру и уже в пути!",
                ['order_id' => $id]
            );
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Отклонить запрос курьера
     */
    public function rejectCourierRequest(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        // Получаем заказ для уведомления курьера
        $order = $this->orderModel->findById($id);
        $courierId = $order['courier_request_id'] ?? null;
        
        // Очищаем запрос курьера
        $this->orderModel->update($id, ['courier_request_id' => null]);
        
        // Уведомление для курьера об отклонении
        if ($courierId) {
            self::notifyCourier(
                $this->db,
                $courierId,
                'request_rejected',
                'Запрос отклонен',
                "Ваш запрос на заказ #{$id} был отклонен.",
                ['order_id' => $id]
            );
        }
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Обновить заказ (отклонить курьера)
     */
    public function updateOrder(Request $request, int $id): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        // Сбрасываем курьера если отклоняем запрос
        if (isset($data['courier_id']) && $data['courier_id'] === null) {
            $this->orderModel->update($id, ['courier_id' => null, 'courier_request_id' => null]);
        }
        
        return $this->json(['success' => true]);
    }
    
    // ==================== КУРЬЕР: ЧАТ ====================
    
    /**
     * API: Контакты чата для курьера (админы)
     */
    public function courierChatContacts(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $admins = $this->userModel->getAdmins();
        $courierId = $this->getUserId();
        
        $contacts = [];
        foreach ($admins as $admin) {
            unset($admin['password']);
            
            // Получаем последнее сообщение с этим админом из MySQL
            $lastMessage = $this->db->queryOne(
                "SELECT c.*, u.name as sender_name, u.role as sender_role 
                 FROM chat c 
                 JOIN users u ON c.sender_id = u.id 
                 WHERE (c.sender_id = ? AND c.receiver_id = ?) OR (c.sender_id = ? AND c.receiver_id = ?)
                 ORDER BY c.created_at DESC LIMIT 1",
                [$courierId, $admin['id'], $admin['id'], $courierId]
            );
            
            // Непрочитанные сообщения от админа
            $unreadCount = $this->db->count('chat', 'sender_id = ? AND receiver_id = ? AND is_read = 0', [$admin['id'], $courierId]);
            
            $contacts[] = [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'phone' => $admin['phone'] ?? '',
                'role' => 'admin',
                'last_message' => $lastMessage['message'] ?? null,
                'last_message_time' => $lastMessage['created_at'] ?? null,
                'unread_count' => $unreadCount
            ];
        }
        
        return $this->json($contacts);
    }
    
    /**
     * API: Сообщения чата для курьера с конкретным админом
     */
    public function courierChatMessages(Request $request, int $adminId): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $courierId = $this->getUserId();
        
        // Получаем все сообщения между курьером и админом из MySQL
        $messages = $this->db->query(
            "SELECT c.*, u.name as sender_name, u.role as sender_role 
             FROM chat c 
             JOIN users u ON c.sender_id = u.id 
             WHERE (c.sender_id = ? AND c.receiver_id = ?) OR (c.sender_id = ? AND c.receiver_id = ?)
             ORDER BY c.created_at ASC",
            [$courierId, $adminId, $adminId, $courierId]
        );
        
        return $this->json(array_values($messages));
    }
    
    /**
     * API: Отправить сообщение от курьера админу
     */
    public function courierChatSend(Request $request, int $adminId): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        if (empty($data['message'])) {
            return $this->error('Сообщение не может быть пустым', 400);
        }
        
        // Сохраняем сообщение в MySQL
        $this->db->insert('chat', [
            'sender_id' => $this->getUserId(),
            'receiver_id' => $adminId,
            'message' => Security::sanitize($data['message']),
            'is_read' => 0
        ]);
        
        // Уведомление для админа
        self::notifyUser(
            $this->db,
            $adminId,
            'new_message',
            'Новое сообщение',
            "Курьер {$this->getUser()['name']} отправил вам сообщение",
            ['courier_id' => $this->getUserId()]
        );
        
        return $this->json(['success' => true]);
    }

    // ==================== АДМИН: ЧАТ ====================
    
    /**
     * API: Получить сообщения с пользователем (админ)
     */
    public function adminChatMessages(Request $request, int $userId): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        // Получаем ID всех админов
        $adminIds = array_map(fn($a) => $a['id'], $this->userModel->getAdmins());
        $adminIdsStr = implode(',', $adminIds);
        
        // Получаем сообщения из MySQL
        $messages = $this->db->query(
            "SELECT c.*, u.name as sender_name, u.role as sender_role 
             FROM chat c 
             JOIN users u ON c.sender_id = u.id 
             WHERE (c.sender_id = ? AND c.receiver_id IN ({$adminIdsStr}))
                OR (c.sender_id IN ({$adminIdsStr}) AND c.receiver_id = ?)
             ORDER BY c.created_at ASC",
            [$userId, $userId]
        );
        
        return $this->json(array_values($messages));
    }
    
    /**
     * API: Пользователи с сообщениями (админ)
     */
    public function adminChatUsers(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        // Получаем ID всех админов
        $adminIds = array_map(fn($a) => $a['id'], $this->userModel->getAdmins());
        $adminIdsStr = implode(',', $adminIds);
        
        // Получаем пользователей с сообщениями из MySQL
        $users = $this->db->query(
            "SELECT DISTINCT 
                u.id, u.name, u.phone, u.role,
                (SELECT c2.message FROM chat c2 
                 WHERE (c2.sender_id = u.id AND c2.receiver_id IN ({$adminIdsStr}))
                    OR (c2.sender_id IN ({$adminIdsStr}) AND c2.receiver_id = u.id)
                 ORDER BY c2.created_at DESC LIMIT 1) as last_message,
                (SELECT c3.created_at FROM chat c3 
                 WHERE (c3.sender_id = u.id AND c3.receiver_id IN ({$adminIdsStr}))
                    OR (c3.sender_id IN ({$adminIdsStr}) AND c3.receiver_id = u.id)
                 ORDER BY c3.created_at DESC LIMIT 1) as last_message_time,
                (SELECT COUNT(*) FROM chat c4 
                 WHERE c4.sender_id = u.id AND c4.receiver_id IN ({$adminIdsStr}) AND c4.is_read = 0) as unread_count
             FROM users u
             WHERE u.id NOT IN ({$adminIdsStr})
                AND EXISTS (
                    SELECT 1 FROM chat c 
                    WHERE (c.sender_id = u.id AND c.receiver_id IN ({$adminIdsStr}))
                       OR (c.sender_id IN ({$adminIdsStr}) AND c.receiver_id = u.id)
                )
             ORDER BY last_message_time DESC"
        );
        
        return $this->json(array_values($users));
    }
    
    /**
     * API: Отправить сообщение от админа
     */
    public function adminChatSend(Request $request): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        $data = $request->json();
        
        if (empty($data['message']) || empty($data['user_id'])) {
            return $this->error('Требуется сообщение и ID пользователя', 400);
        }
        
        // Сохраняем сообщение в MySQL
        $this->db->insert('chat', [
            'sender_id' => $this->getUserId(),
            'receiver_id' => intval($data['user_id']),
            'message' => Security::sanitize($data['message']),
            'is_read' => 0
        ]);

        return $this->json(['success' => true]);
    }
    
    /**
     * API: Пометить сообщения прочитанными (админ)
     */
    public function adminChatMarkRead(Request $request, int $userId): Response
    {
        $error = $this->requireAdmin();
        if ($error !== null) {
            return $error;
        }
        
        // Помечаем все сообщения от пользователя к админам как прочитанные
        $this->db->query(
            "UPDATE chat SET is_read = 1 WHERE sender_id = ? AND is_read = 0",
            [$userId]
        );
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: История заказов курьера (доставленные)
     */
    public function courierHistory(Request $request): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        // Получаем архив заказов через модель (с декодированием items)
        $archive = $this->orderModel->getArchive();
        $courierId = $this->getUserId();
        
        // Фильтруем заказы текущего курьера (только доставленные)
        $history = array_filter($archive, function($order) use ($courierId) {
            return isset($order['courier_id']) && $order['courier_id'] == $courierId
                && ($order['status'] === OrderModel::STATUS_DELIVERED || $order['status'] === 'ДОСТАВЛЕН');
        });
        
        // Сортируем по дате (новые первые)
        usort($history, function($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });
        
        return $this->json(array_values($history));
    }
    
    /**
     * API: Отслеживание заказа
     */
    public function orderTracking(Request $request, int $orderId): Response
    {
        $orderModel = new \App\Models\OrderModel($this->db);
        $order = $orderModel->findById($orderId);
        
        if ($order === null) {
            return $this->error('Заказ не найден', 404);
        }
        
        $result = [
            'order' => $order,
            'courier' => null,
            'courier_location' => null,
            'statusHistory' => [
                ['status' => $order['status'], 'updated_at' => $order['created_at']]
            ]
        ];
        
        // Если есть курьер, получаем его данные
        if (!empty($order['courier_id'])) {
            $userModel = new \App\Models\UserModel($this->db);
            $courier = $userModel->findById($order['courier_id']);
            if ($courier) {
                $result['courier'] = [
                    'name' => $courier['name'],
                    'phone' => $courier['phone']
                ];
                
                // Получаем местоположение курьера из MySQL
                $location = $this->db->queryOne(
                    "SELECT latitude, longitude FROM courier_locations WHERE courier_id = ?",
                    [$order['courier_id']]
                );
                if ($location) {
                    $result['courier_location'] = [
                        'lat' => (float) $location['latitude'],
                        'lng' => (float) $location['longitude']
                    ];
                }
            }
        }
        
        return $this->json($result);
    }
    
    // ==================== УВЕДОМЛЕНИЯ ====================
    
    /**
     * API: Получить уведомления пользователя (обычные пользователи)
     */
    public function getNotifications(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $userRole = $this->getUser()['role'] ?? 'user';
        
        // Для админов возвращаем админские уведомления
        if ($userRole === 'admin') {
            return $this->getAdminNotifications($request);
        }
        
        // Для курьеров возвращаем курьерские уведомления
        if ($userRole === 'courier') {
            return $this->getCourierNotifications($request);
        }
        
        // Для обычных пользователей
        return $this->getUserNotifications($request);
    }
    
    /**
     * API: Уведомления для обычного пользователя
     */
    public function getUserNotifications(Request $request): Response
    {
        $userId = $this->getUserId();
        
        // Получаем непрочитанные уведомления пользователя из MySQL
        $notifications = $this->db->query(
            "SELECT id, title, message, is_read, created_at 
             FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC",
            [$userId]
        );
        
        return $this->json(array_values($notifications));
    }
    
    /**
     * API: Уведомления для админа
     */
    public function getAdminNotifications(Request $request): Response
    {
        $userId = $this->getUserId();
        
        // Получаем непрочитанные уведомления админа из MySQL
        $notifications = $this->db->query(
            "SELECT id, title, message, is_read, created_at 
             FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC",
            [$userId]
        );
        
        return $this->json(array_values($notifications));
    }
    
    /**
     * API: Уведомления для курьера
     */
    public function getCourierNotifications(Request $request): Response
    {
        $userId = $this->getUserId();
        
        // Получаем непрочитанные уведомления курьера из MySQL
        $notifications = $this->db->query(
            "SELECT id, title, message, is_read, created_at 
             FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC",
            [$userId]
        );
        
        return $this->json(array_values($notifications));
    }
    
    /**
     * API: Получить количество непрочитанных уведомлений
     */
    public function getUnreadCount(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $userId = $this->getUserId();
        
        // Считаем непрочитанные уведомления пользователя
        $result = $this->db->queryOne(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        
        return $this->json(['count' => (int) ($result['count'] ?? 0)]);
    }
    
    /**
     * API: Пометить уведомление как прочитанное
     */
    public function markNotificationRead(Request $request, int $id): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $userId = $this->getUserId();
        
        // Помечаем уведомление как прочитанное (только если принадлежит пользователю)
        $this->db->query(
            "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );
        
        return $this->json(['success' => true]);
    }
    
    /**
     * API: Пометить все уведомления как прочитанные
     */
    public function markAllNotificationsRead(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        $userId = $this->getUserId();
        
        // Помечаем все уведомления пользователя как прочитанные
        $this->db->query(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        
        return $this->json(['success' => true]);
    }
    
    /**
     * Создать уведомление (вспомогательный метод)
     */
    public static function createNotification(
        \App\Core\Database $db, 
        string $type, 
        string $title, 
        string $message, 
        ?int $userId = null, 
        ?string $forRole = null, 
        bool $forAll = false,
        ?array $data = null
    ): void {
        // Для MySQL используем прямой insert
        // Если userId не указан, используем 0 для системных уведомлений
        $targetUserId = $userId ?? 0;
        
        $db->insert('notifications', [
            'user_id' => $targetUserId,
            'title' => $title,
            'message' => $message,
            'is_read' => 0
        ]);
    }
    
    /**
     * Создать уведомление для пользователя
     */
    public static function notifyUser(
        \App\Core\Database $db,
        int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        self::createNotification($db, $type, $title, $message, $userId, null, false, $data);
    }
    
    /**
     * Создать уведомление для всех пользователей
     */
    public static function notifyAllUsers(
        \App\Core\Database $db,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        // Получаем всех пользователей
        $users = $db->query("SELECT id FROM users WHERE role = 'user'");
        foreach ($users as $user) {
            $db->insert('notifications', [
                'user_id' => $user['id'],
                'title' => $title,
                'message' => $message,
                'is_read' => 0
            ]);
        }
    }
    
    /**
     * Создать уведомление для всех админов
     */
    public static function notifyAdmins(
        \App\Core\Database $db,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        // Получаем всех админов
        $admins = $db->query("SELECT id FROM users WHERE role = 'admin'");
        foreach ($admins as $admin) {
            $db->insert('notifications', [
                'user_id' => $admin['id'],
                'title' => $title,
                'message' => $message,
                'is_read' => 0
            ]);
        }
    }
    
    /**
     * Создать уведомление для всех курьеров на смене
     */
    public static function notifyCouriers(
        \App\Core\Database $db,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        // Получаем всех курьеров на активной смене
        $couriers = $db->query(
            "SELECT u.id FROM users u 
             INNER JOIN courier_shifts cs ON u.id = cs.courier_id 
             WHERE u.role = 'courier' AND cs.is_active = 1"
        );
        foreach ($couriers as $courier) {
            $db->insert('notifications', [
                'user_id' => $courier['id'],
                'title' => $title,
                'message' => $message,
                'is_read' => 0
            ]);
        }
    }
    
    /**
     * Создать уведомление для конкретного курьера
     */
    public static function notifyCourier(
        \App\Core\Database $db,
        int $courierId,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        self::createNotification($db, $type, $title, $message, $courierId, null, false, $data);
    }
    
    // ==================== ГЕОКОДИНГ ====================
    
    /**
     * API: Поиск координат по адресу (OpenStreetMap Nominatim)
     */
    public function geocodeSearch(Request $request): Response
    {
        $query = $request->query('q', '');
        
        if (empty($query)) {
            return $this->json([]);
        }
        
        // Используем OpenStreetMap Nominatim API
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q' => $query,
            'format' => 'json',
            'limit' => 5,
            'countrycodes' => 'kz', // Ограничиваем поиск Казахстаном
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Delivery App/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || empty($response)) {
            return $this->json([]);
        }
        
        $data = json_decode($response, true);
        
        if (!is_array($data)) {
            return $this->json([]);
        }
        
        // Форматируем результаты
        $results = array_map(function($item) {
            return [
                'lat' => $item['lat'] ?? null,
                'lon' => $item['lon'] ?? null,
                'display_name' => $item['display_name'] ?? '',
            ];
        }, $data);
        
        return $this->json($results);
    }
}
