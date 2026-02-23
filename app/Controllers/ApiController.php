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
     * API: Взять заказ курьером (отправить запрос)
     */
    public function courierTakeOrder(Request $request, int $id): Response
    {
        $error = $this->requireCourier();
        if ($error !== null) {
            return $error;
        }
        
        // Проверка доступности заказа
        if (!$this->orderModel->isAvailableForCourier($id)) {
            return $this->error('Заказ недоступен', 400);
        }
        
        // Сохраняем запрос курьера (не назначаем сразу)
        $this->orderModel->update($id, ['courier_request_id' => $this->getUserId()]);
        
        // Уведомление для админов о запросе заказа курьером
        self::notifyAdmins(
            $this->db,
            'courier_request',
            'Запрос на заказ',
            "Курьер {$this->getUser()['name']} запросил заказ #{$id}",
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
        
        // Сбрасываем курьера и статус
        $this->orderModel->updateStatus($id, OrderModel::STATUS_WAITING_COURIER);
        
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
        
        // Сохраняем местоположение
        $courierLocations = $this->db->read('courier');
        $found = false;
        
        foreach ($courierLocations as &$loc) {
            if ($loc['courier_id'] == $this->getUserId()) {
                $loc['lat'] = floatval($data['lat']);
                $loc['lng'] = floatval($data['lng']);
                $loc['updated_at'] = date('c');
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $courierLocations[] = [
                'courier_id' => $this->getUserId(),
                'lat' => floatval($data['lat']),
                'lng' => floatval($data['lng']),
                'updated_at' => date('c')
            ];
        }
        
        $this->db->write('courier', $courierLocations);
        
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
        
        // Получаем местоположение курьера
        $courierLocations = $this->db->read('courier');
        $location = null;
        
        foreach ($courierLocations as $loc) {
            if ($loc['courier_id'] == $order['courier_id']) {
                $location = $loc;
                break;
            }
        }
        
        return $this->json([
            'courier' => $courier,
            'location' => $location,
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
        $courierLocations = $this->db->read('courier');
        
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
            $courier['location'] = null;
            foreach ($courierLocations as $loc) {
                if ($loc['courier_id'] == $courier['id']) {
                    $courier['location'] = $loc;
                    break;
                }
            }
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
        
        $chat = $this->db->read('chat');
        $userId = $this->getUserId();
        $userRole = $this->getUser()['role'] ?? 'user';
        
        // Получаем ID всех админов
        $adminIds = array_map(fn($a) => $a['id'], $this->userModel->getAdmins());
        
        if ($contactId === null || $contactId === 0) {
            // Для обычного пользователя показываем сообщения с админами
            if ($userRole !== 'admin') {
                $messages = array_filter($chat, function($m) use ($userId, $adminIds) {
                    $senderId = $m['sender_id'];
                    $receiverId = $m['receiver_id'] ?? 0;
                    // Сообщения от пользователя к админам
                    if ($senderId == $userId && in_array($receiverId, $adminIds)) {
                        return true;
                    }
                    // Сообщения от админов к пользователю
                    if (in_array($senderId, $adminIds) && $receiverId == $userId) {
                        return true;
                    }
                    return false;
                });
            } else {
                // Для админа - общий чат (устаревший)
                $messages = array_filter($chat, fn($m) => !isset($m['receiver_id']) || $m['receiver_id'] == 0);
            }
        } else {
            // Личные сообщения с конкретным контактом
            $messages = array_filter($chat, function($m) use ($userId, $contactId) {
                return ($m['sender_id'] == $userId && $m['receiver_id'] == $contactId) ||
                       ($m['sender_id'] == $contactId && $m['receiver_id'] == $userId);
            });
        }
        
        return $this->json(array_values($messages));
    }
    
    /**
     * API: Отправить сообщение
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
        
        $chat = $this->db->read('chat');
        
        // Если пользователь не админ и не указан получатель, отправляем админу
        $receiverId = $contactId ?? 0;
        if ($receiverId === 0 && ($this->getUser()['role'] ?? 'user') !== 'admin') {
            // Находим первого админа
            $admins = $this->userModel->getAdmins();
            if (!empty($admins)) {
                $receiverId = $admins[0]['id'];
            }
        }
        
        $message = [
            'id' => $this->db->getNextId('chat'),
            'sender_id' => $this->getUserId(),
            'sender_name' => $this->getUser()['name'],
            'sender_role' => $this->getUser()['role'] ?? 'user',
            'receiver_id' => $receiverId,
            'message' => Security::sanitize($data['message']),
            'created_at' => date('c')
        ];
        
        $chat[] = $message;
        $this->db->write('chat', $chat);
        
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
        
        $chat = $this->db->read('chat');
        $adminId = $this->getUserId();
        
        // Получаем ID всех админов
        $adminIds = array_map(fn($a) => $a['id'], $this->userModel->getAdmins());
        
        // Показываем все сообщения между пользователем и любым админом
        $messages = array_filter($chat, function($m) use ($userId, $adminIds) {
            $senderId = $m['sender_id'];
            $receiverId = $m['receiver_id'] ?? 0;
            
            // Сообщения от пользователя к админам
            if ($senderId == $userId && in_array($receiverId, $adminIds)) {
                return true;
            }
            // Сообщения от админов к пользователю
            if (in_array($senderId, $adminIds) && $receiverId == $userId) {
                return true;
            }
            return false;
        });
        
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
        
        $chat = $this->db->read('chat');
        $users = $this->userModel->getAll();
        $adminId = $this->getUserId();
        
        // Получаем ID всех админов
        $adminIds = array_map(fn($a) => $a['id'], $this->userModel->getAdmins());
        
        $userIds = [];
        foreach ($chat as $msg) {
            // Если отправитель не админ - это пользователь
            if (!in_array($msg['sender_id'], $adminIds)) {
                $userIds[$msg['sender_id']] = true;
            }
            // Если получатель не админ - это пользователь
            if (isset($msg['receiver_id']) && $msg['receiver_id'] > 0 && !in_array($msg['receiver_id'], $adminIds)) {
                $userIds[$msg['receiver_id']] = true;
            }
        }
        
        $result = [];
        foreach ($userIds as $uid => $_) {
            $user = null;
            foreach ($users as $u) {
                if ($u['id'] == $uid) {
                    $user = $u;
                    break;
                }
            }
            
            if ($user) {
                // Все сообщения пользователя с админами
                $userMessages = array_filter($chat, function($m) use ($uid, $adminIds) {
                    $senderId = $m['sender_id'];
                    $receiverId = $m['receiver_id'] ?? 0;
                    return ($senderId == $uid && in_array($receiverId, $adminIds)) ||
                           (in_array($senderId, $adminIds) && $receiverId == $uid);
                });
                $userMessages = array_values($userMessages);
                $lastMessage = end($userMessages);
                
                // Непрочитанные - сообщения от пользователя к текущему админу
                $unread = count(array_filter($chat, fn($m) => 
                    $m['sender_id'] == $uid && 
                    isset($m['receiver_id']) && 
                    $m['receiver_id'] == $adminId
                ));
                
                $result[] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'phone' => $user['phone'],
                    'last_message' => $lastMessage['message'] ?? null,
                    'unread_count' => $unread
                ];
            }
        }
        
        return $this->json($result);
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
        
        $chat = $this->db->read('chat');
        
        $message = [
            'id' => $this->db->getNextId('chat'),
            'sender_id' => $this->getUserId(),
            'sender_name' => $this->getUser()['name'],
            'sender_role' => 'admin',
            'receiver_id' => intval($data['user_id']),
            'message' => Security::sanitize($data['message']),
            'created_at' => date('c')
        ];
        
        $chat[] = $message;
        $this->db->write('chat', $chat);

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
                
                // Получаем местоположение курьера
                $courierLocations = $this->db->findBy('courier', 'courier_id', $order['courier_id']);
                if (!empty($courierLocations)) {
                    $lastLocation = end($courierLocations);
                    $result['courier_location'] = [
                        'lat' => $lastLocation['lat'] ?? 43.518703,
                        'lng' => $lastLocation['lng'] ?? 68.505423
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
        $notifications = $this->db->read('notifications');
        $userId = $this->getUserId();
        
        $userNotifications = array_filter($notifications, function($n) use ($userId) {
            // Уведомления для всех пользователей
            if (isset($n['for_all']) && $n['for_all']) {
                // Для пользователей проверяем read_by
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            // Личные уведомления пользователя
            if (isset($n['user_id']) && $n['user_id'] == $userId && empty($n['read'])) {
                return true;
            }
            // Уведомления для всех пользователей (не для ролей)
            if (isset($n['for_users']) && $n['for_users'] === true) {
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            return false;
        });
        
        usort($userNotifications, function($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });
        
        return $this->json(array_values($userNotifications));
    }
    
    /**
     * API: Уведомления для админа
     */
    public function getAdminNotifications(Request $request): Response
    {
        $notifications = $this->db->read('notifications');
        $userId = $this->getUserId();
        
        $adminNotifications = array_filter($notifications, function($n) use ($userId) {
            // Уведомления для админов
            if (isset($n['for_role']) && $n['for_role'] === 'admin') {
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            // Уведомления для всех админов
            if (isset($n['for_admins']) && $n['for_admins'] === true) {
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            return false;
        });
        
        usort($adminNotifications, function($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });
        
        return $this->json(array_values($adminNotifications));
    }
    
    /**
     * API: Уведомления для курьера
     */
    public function getCourierNotifications(Request $request): Response
    {
        $notifications = $this->db->read('notifications');
        $userId = $this->getUserId();
        
        $courierNotifications = array_filter($notifications, function($n) use ($userId) {
            // Уведомления для курьеров
            if (isset($n['for_role']) && $n['for_role'] === 'courier') {
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            // Уведомления для всех курьеров
            if (isset($n['for_couriers']) && $n['for_couriers'] === true) {
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            // Личные уведомления курьера
            if (isset($n['user_id']) && $n['user_id'] == $userId && empty($n['read'])) {
                return true;
            }
            return false;
        });
        
        usort($courierNotifications, function($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });
        
        return $this->json(array_values($courierNotifications));
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
        
        $notifications = $this->db->read('notifications');
        $userId = $this->getUserId();
        $userRole = $this->getUser()['role'] ?? 'user';
        
        $unreadCount = count(array_filter($notifications, function($n) use ($userId, $userRole) {
            if (!empty($n['read'])) {
                return false;
            }
            
            if (isset($n['for_all']) && $n['for_all']) {
                // Проверяем, прочитал ли конкретный пользователь
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            if (isset($n['user_id']) && $n['user_id'] == $userId) {
                return true;
            }
            if (isset($n['for_role']) && $n['for_role'] === $userRole) {
                return !isset($n['read_by']) || !in_array($userId, $n['read_by']);
            }
            return false;
        }));
        
        return $this->json(['count' => $unreadCount]);
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
        
        $notifications = $this->db->read('notifications');
        $userId = $this->getUserId();
        
        foreach ($notifications as &$n) {
            if ($n['id'] == $id) {
                if (isset($n['for_all']) && $n['for_all']) {
                    $n['read_by'] = $n['read_by'] ?? [];
                    if (!in_array($userId, $n['read_by'])) {
                        $n['read_by'][] = $userId;
                    }
                } else {
                    $n['read'] = true;
                }
                break;
            }
        }
        
        $this->db->write('notifications', $notifications);
        
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
        
        $notifications = $this->db->read('notifications');
        $userId = $this->getUserId();
        $userRole = $this->getUser()['role'] ?? 'user';
        
        foreach ($notifications as &$n) {
            $isForUser = false;
            
            if (isset($n['for_all']) && $n['for_all']) {
                $isForUser = true;
            } elseif (isset($n['user_id']) && $n['user_id'] == $userId) {
                $isForUser = true;
            } elseif (isset($n['for_role']) && $n['for_role'] === $userRole) {
                $isForUser = true;
            }
            
            if ($isForUser) {
                if (isset($n['for_all']) && $n['for_all']) {
                    $n['read_by'] = $n['read_by'] ?? [];
                    if (!in_array($userId, $n['read_by'])) {
                        $n['read_by'][] = $userId;
                    }
                } else {
                    $n['read'] = true;
                }
            }
        }
        
        $this->db->write('notifications', $notifications);
        
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
        $notifications = $db->read('notifications');
        
        $notification = [
            'id' => $db->getNextId('notifications'),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'created_at' => date('c'),
            'read' => false,
            'read_by' => [],
            'data' => $data
        ];
        
        if ($forAll) {
            $notification['for_all'] = true;
            $notification['for_users'] = true;
        } elseif ($userId !== null) {
            $notification['user_id'] = $userId;
        } elseif ($forRole !== null) {
            $notification['for_role'] = $forRole;
        }
        
        $notifications[] = $notification;
        $db->write('notifications', $notifications);
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
        $notifications = $db->read('notifications');
        
        $notifications[] = [
            'id' => $db->getNextId('notifications'),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'created_at' => date('c'),
            'read' => false,
            'read_by' => [],
            'for_users' => true,
            'data' => $data
        ];
        
        $db->write('notifications', $notifications);
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
        $notifications = $db->read('notifications');
        
        $notifications[] = [
            'id' => $db->getNextId('notifications'),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'created_at' => date('c'),
            'read' => false,
            'read_by' => [],
            'for_admins' => true,
            'for_role' => 'admin',
            'data' => $data
        ];
        
        $db->write('notifications', $notifications);
    }
    
    /**
     * Создать уведомление для всех курьеров
     */
    public static function notifyCouriers(
        \App\Core\Database $db,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        $notifications = $db->read('notifications');
        
        $notifications[] = [
            'id' => $db->getNextId('notifications'),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'created_at' => date('c'),
            'read' => false,
            'read_by' => [],
            'for_couriers' => true,
            'for_role' => 'courier',
            'data' => $data
        ];
        
        $db->write('notifications', $notifications);
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
}
