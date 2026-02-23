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
    private string $archiveTable = 'archive';
    
    // Статусы заказов
    public const STATUS_CREATED = 'СОЗДАН';
    public const STATUS_ASSEMBLY = 'СБОРКА';
    public const STATUS_WAITING_COURIER = 'ОЖИДАНИЕ_КУРЬЕРА';
    public const STATUS_ON_THE_WAY = 'В_ПУТИ';
    public const STATUS_DELIVERED = 'ДОСТАВЛЕН';
    public const STATUS_CANCELLED = 'ОТМЕНЕН';
    
    public const VALID_STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_ASSEMBLY,
        self::STATUS_WAITING_COURIER,
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
        if ($order) {
            $order['items'] = json_decode($order['items'] ?? '[]', true);
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
            $order['items'] = json_decode($order['items'] ?? '[]', true);
        }
        return $orders;
    }
    
    /**
     * Получить заказы пользователя
     */
    public function getByUserId(int $userId): array
    {
        $orders = $this->db->findBy($this->table, 'user_id', $userId);
        foreach ($orders as &$order) {
            $order['items'] = json_decode($order['items'] ?? '[]', true);
        }
        return $orders;
    }
    
    /**
     * Получить заказы курьера
     */
    public function getByCourierId(int $courierId): array
    {
        $orders = $this->getAll();
        return array_filter($orders, function($order) use ($courierId) {
            // Показываем только заказы со статусом В_ПУТИ для курьера
            return isset($order['courier_id']) && $order['courier_id'] == $courierId
                && $order['status'] === self::STATUS_ON_THE_WAY;
        });
    }
    
    /**
     * Получить доступные заказы для курьера
     */
    public function getAvailableForCourier(): array
    {
        $orders = $this->getAll();
        return array_filter($orders, function($order) {
            // Показываем заказы со статусом В_ПУТИ без назначенного курьера
            return $order['status'] === self::STATUS_ON_THE_WAY 
                && empty($order['courier_id']);
        });
    }
    
    /**
     * Создать заказ
     */
    public function create(array $data): int
    {
        $items = $data['items'] ?? [];
        $total = 0;
        foreach ($items as $item) {
            $total += floatval($item['price'] ?? 0) * intval($item['quantity'] ?? 1);
        }
        
        $deliveryIncluded = !empty($data['delivery_included']);
        $deliveryPrice = $deliveryIncluded ? 500 : 0;
        
        // Получаем глобально уникальный ID (учитывая и orders, и archive)
        $id = $this->getNextGlobalId();
        
        $order = [
            'id' => $id,
            'user_id' => intval($data['user_id']),
            'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
            'address' => Security::sanitize($data['address'] ?? ''),
            'delivery_included' => $deliveryIncluded,
            'delivery_price' => $deliveryPrice,
            'total_price' => $total + $deliveryPrice,
            'status' => self::STATUS_CREATED,
            'courier_id' => null,
            'created_at' => date('c')
        ];
        
        // Добавляем заказ с уже установленным ID
        $orders = $this->db->read($this->table);
        $orders[] = $order;
        $this->db->write($this->table, $orders);
        
        return $id;
    }
    
    /**
     * Получить следующий глобально уникальный ID
     */
    private function getNextGlobalId(): int
    {
        $orders = $this->db->read($this->table);
        $archive = $this->db->read($this->archiveTable);
        
        $allIds = [];
        
        foreach ($orders as $order) {
            if (isset($order['id'])) {
                $allIds[] = $order['id'];
            }
        }
        
        foreach ($archive as $order) {
            if (isset($order['id'])) {
                $allIds[] = $order['id'];
            }
        }
        
        if (empty($allIds)) {
            return 1;
        }
        
        return max($allIds) + 1;
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
        
        // При возврате в ожидание курьера - сбрасываем курьера
        if ($status === self::STATUS_WAITING_COURIER) {
            $updates['courier_id'] = null;
        }
        
        return $this->db->update($this->table, $id, $updates);
    }
    
    /**
     * Назначить курьера
     */
    public function assignCourier(int $id, int $courierId): bool
    {
        return $this->db->update($this->table, $id, ['courier_id' => $courierId]);
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
        
        $order['archived_at'] = date('c');
        
        // Проверяем, существует ли уже запись с таким ID в архиве
        $existingInArchive = $this->db->findById($this->archiveTable, $id);
        
        if ($existingInArchive !== null) {
            // Если запись существует, обновляем её
            $this->db->update($this->archiveTable, $id, $order);
        } else {
            // Иначе добавляем новую запись
            if (!$this->db->insertWithId($this->archiveTable, $order)) {
                return false;
            }
        }
        
        return $this->db->delete($this->table, $id);
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
        
        unset($order['archived_at']);
        
        // Добавляем в заказы с сохранением ID
        if (!$this->db->insertWithId($this->table, $order)) {
            return false;
        }
        
        return $this->db->delete($this->archiveTable, $id);
    }
    
    /**
     * Получить архив заказов
     */
    public function getArchive(): array
    {
        $orders = $this->db->read($this->archiveTable);
        foreach ($orders as &$order) {
            $order['items'] = json_decode($order['items'] ?? '[]', true);
        }
        return $orders;
    }
    
    /**
     * Получить статистику заказов
     */
    public function getStats(): array
    {
        $orders = $this->db->read($this->table);
        
        $stats = [
            'total' => count($orders),
            'total_revenue' => 0,
            'by_status' => []
        ];
        
        foreach ($orders as $order) {
            $stats['total_revenue'] += floatval($order['total_price'] ?? 0);
            
            $status = $order['status'] ?? 'unknown';
            $stats['by_status'][$status] = ($stats['by_status'][$status] ?? 0) + 1;
        }
        
        return $stats;
    }
    
    /**
     * Получить заказы за последние N дней
     */
    public function getRecent(int $days = 30): array
    {
        $orders = $this->getAll();
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return array_filter($orders, function($order) use ($cutoffDate) {
            return $order['created_at'] >= $cutoffDate;
        });
    }
    
    /**
     * Проверить доступность заказа для курьера
     */
    public function isAvailableForCourier(int $id): bool
    {
        $order = $this->db->findById($this->table, $id);
        
        return $order !== null 
            && $order['status'] === self::STATUS_ON_THE_WAY
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