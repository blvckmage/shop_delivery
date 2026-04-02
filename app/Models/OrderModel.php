<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Security;

/**
 * Модель заказа
 */
class OrderModel
{
    private Database $db;
    private string $table = 'orders';
    private string $archiveTable = 'orders_archive';
    
    // Статусы заказов
    public const STATUS_CREATED = 'СОЗДАН';
    public const STATUS_ON_THE_WAY = 'В_ПУТИ';
    public const STATUS_DELIVERED = 'ДОСТАВЛЕН';
    public const STATUS_CANCELLED = 'ОТМЕНЕН';
    
    public const VALID_STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_ON_THE_WAY,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED
    ];
    
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }
    
    /**
     * Найти заказ по ID
     */
    public function findById(int $id): ?array
    {
        $order = $this->db->findById($this->table, $id);
        if ($order && isset($order['items'])) {
            $order['items'] = json_decode($order['items'], true) ?? [];
        }
        return $order;
    }
    
    /**
     * Получить все заказы
     */
    public function getAll(): array
    {
        $orders = $this->db->read($this->table);
        foreach ($orders as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        return $orders;
    }
    
    /**
     * Получить заказы с пагинацией
     */
    public function getPaginated(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $where = '';
        $params = [];
        
        if ($status && in_array($status, self::VALID_STATUSES)) {
            $where = 'status = ?';
            $params = [$status];
        }
        
        $result = $this->db->paginate(
            $this->table,
            $page,
            $perPage,
            'created_at DESC',
            $where,
            $params
        );
        
        foreach ($result['items'] as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        
        return $result;
    }
    
    /**
     * Получить заказы пользователя
     */
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        $orders = $this->db->query($sql, [$userId]);
        
        foreach ($orders as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        return $orders;
    }
    
    /**
     * Получить заказы курьера
     */
    public function getByCourierId(int $courierId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE courier_id = ? AND status = ? ORDER BY created_at DESC";
        $orders = $this->db->query($sql, [$courierId, self::STATUS_ON_THE_WAY]);
        
        foreach ($orders as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        return $orders;
    }
    
    /**
     * Получить доступные заказы для курьера
     * Показываем заказы со статусом СОЗДАН (новые) или В_ПУТИ (подтвержденные админом)
     */
    public function getAvailableForCourier(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status IN (?, ?) AND courier_id IS NULL ORDER BY created_at ASC";
        $orders = $this->db->query($sql, [self::STATUS_CREATED, self::STATUS_ON_THE_WAY]);
        
        foreach ($orders as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        return $orders;
    }
    
    /**
     * Создать заказ (с защитой от дублирования)
     */
    public function create(array $data): int
    {
        $userId = intval($data['user_id']);
        
        try {
            $this->db->beginTransaction();
            
            // Проверяем, есть ли уже активный заказ у пользователя
            $sql = "SELECT id FROM {$this->table} WHERE user_id = ? AND status IN (?, ?) FOR UPDATE";
            $existingOrder = $this->db->queryOne($sql, [$userId, self::STATUS_CREATED, self::STATUS_ON_THE_WAY]);
            
            if ($existingOrder !== null) {
                $this->db->rollBack();
                return -1; // У пользователя уже есть активный заказ
            }
            
            $items = $data['items'] ?? [];
            $total = 0;
            foreach ($items as $item) {
                $total += floatval($item['price'] ?? 0) * floatval($item['quantity'] ?? 1);
            }
            
            $deliveryIncluded = !empty($data['delivery_included']);
            $deliveryPrice = $deliveryIncluded ? 500 : 0;
            
            $order = [
                'user_id' => $userId,
                'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
                'address' => Security::sanitize($data['address'] ?? ''),
                'delivery_included' => $deliveryIncluded ? 1 : 0,
                'delivery_price' => $deliveryPrice,
                'total_price' => $total + $deliveryPrice,
                'status' => self::STATUS_CREATED
            ];
            
            $orderId = $this->db->insert($this->table, $order);
            
            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Create order error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Обновить статус заказа
     */
    public function updateStatus(int $id, string $status, ?int $courierId = null): bool
    {
        if (!in_array($status, self::VALID_STATUSES)) {
            return false;
        }
        
        $updates = ['status' => $status];
        
        if ($courierId !== null) {
            $updates['courier_id'] = $courierId;
        }
        
        // При отмене - сбрасываем курьера
        if ($status === self::STATUS_CANCELLED) {
            $updates['courier_id'] = null;
        }
        
        return $this->db->update($this->table, $id, $updates);
    }
    
    /**
     * Назначить курьера (с защитой от race condition)
     */
    public function assignCourier(int $id, int $courierId): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Блокируем строку заказа для других транзакций
            $sql = "SELECT * FROM {$this->table} WHERE id = ? FOR UPDATE";
            $order = $this->db->queryOne($sql, [$id]);
            
            // Проверяем, что заказ доступен
            if ($order === null) {
                $this->db->rollBack();
                return false;
            }
            
            // Проверяем, что заказ в нужном статусе и без курьера
            if ($order['status'] !== self::STATUS_ON_THE_WAY || !empty($order['courier_id'])) {
                $this->db->rollBack();
                return false;
            }
            
            // Назначаем курьера
            $sql = "UPDATE {$this->table} SET courier_id = ? WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $result = $stmt->execute([$courierId, $id]);
            
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Assign courier error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Назначить сборщика
     */
    public function assignPicker(int $id, int $pickerId): bool
    {
        return $this->db->update($this->table, $id, ['picker_id' => $pickerId]);
    }
    
    /**
     * Отметить заказ как собранный
     */
    public function markAssembled(int $id): bool
    {
        return $this->db->update($this->table, $id, [
            'assembled_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Обновить заказ
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update($this->table, $id, $data);
    }
    
    /**
     * Обновить адрес
     */
    public function updateAddress(int $id, string $address): bool
    {
        return $this->db->update($this->table, $id, ['address' => Security::sanitize($address)]);
    }
    
    /**
     * Переместить в архив
     */
    public function archive(int $id): bool
    {
        $order = $this->db->findById($this->table, $id);
        
        if ($order === null) {
            return false;
        }
        
        $archiveData = [
            'id' => $order['id'],
            'user_id' => $order['user_id'],
            'items' => $order['items'],
            'address' => $order['address'],
            'delivery_included' => $order['delivery_included'],
            'delivery_price' => $order['delivery_price'],
            'total_price' => $order['total_price'],
            'status' => $order['status'],
            'courier_id' => $order['courier_id'],
            'picker_id' => $order['picker_id'],
            'assembled_at' => $order['assembled_at'],
            'created_at' => $order['created_at']
        ];
        
        try {
            $this->db->beginTransaction();
            
            // Добавляем в архив
            $this->db->insertWithId($this->archiveTable, $archiveData);
            
            // Удаляем из основной таблицы
            $this->db->delete($this->table, $id);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Archive error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Восстановить из архива
     */
    public function restoreFromArchive(int $id): bool
    {
        $order = $this->db->findById($this->archiveTable, $id);
        
        if ($order === null) {
            return false;
        }
        
        $restoreData = [
            'id' => $order['id'],
            'user_id' => $order['user_id'],
            'items' => $order['items'],
            'address' => $order['address'],
            'delivery_included' => $order['delivery_included'],
            'delivery_price' => $order['delivery_price'],
            'total_price' => $order['total_price'],
            'status' => $order['status'],
            'courier_id' => $order['courier_id'],
            'picker_id' => $order['picker_id'],
            'assembled_at' => $order['assembled_at'],
            'created_at' => $order['created_at']
        ];
        
        try {
            $this->db->beginTransaction();
            
            // Добавляем в основную таблицу
            $this->db->insertWithId($this->table, $restoreData);
            
            // Удаляем из архива
            $this->db->delete($this->archiveTable, $id);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Restore error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Получить архив заказов
     */
    public function getArchive(): array
    {
        $orders = $this->db->read($this->archiveTable);
        foreach ($orders as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        return $orders;
    }
    
    /**
     * Получить статистику заказов
     */
    public function getStats(): array
    {
        $total = $this->db->count($this->table);
        
        $sql = "SELECT SUM(total_price) as total_revenue FROM {$this->table}";
        $revenueResult = $this->db->queryOne($sql);
        $totalRevenue = floatval($revenueResult['total_revenue'] ?? 0);
        
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        $statusResults = $this->db->query($sql);
        
        $byStatus = [];
        foreach ($statusResults as $row) {
            $byStatus[$row['status']] = intval($row['count']);
        }
        
        return [
            'total' => $total,
            'total_revenue' => $totalRevenue,
            'by_status' => $byStatus
        ];
    }
    
    /**
     * Получить заказы за последние N дней
     */
    public function getRecent(int $days = 30): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) ORDER BY created_at DESC";
        $orders = $this->db->query($sql, [$days]);
        
        foreach ($orders as &$order) {
            if (isset($order['items'])) {
                $order['items'] = json_decode($order['items'], true) ?? [];
            }
        }
        return $orders;
    }
    
    /**
     * Проверить доступность заказа для курьера
     */
    public function isAvailableForCourier(int $id): bool
    {
        $order = $this->db->findById($this->table, $id);
        
        return $order !== null 
            && in_array($order['status'], [self::STATUS_CREATED, self::STATUS_ON_THE_WAY])
            && empty($order['courier_id']);
    }
    
    /**
     * Проверить принадлежность заказа курьеру
     */
    public function belongsToCourier(int $id, int $courierId): bool
    {
        $order = $this->db->findById($this->table, $id);
        
        return $order !== null 
            && isset($order['courier_id']) 
            && $order['courier_id'] == $courierId
            && $order['status'] === self::STATUS_ON_THE_WAY;
    }
}
