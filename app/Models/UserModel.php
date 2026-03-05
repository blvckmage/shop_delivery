<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Security;

/**
 * Модель пользователя
 */
class UserModel
{
    private Database $db;
    private string $table = 'users';
    
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }
    
    /**
     * Найти пользователя по ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->findById($this->table, $id);
    }
    
    /**
     * Найти пользователя по email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->findOneBy($this->table, 'email', $email);
    }
    
    /**
     * Найти пользователя по телефону
     */
    public function findByPhone(string $phone): ?array
    {
        $phone = Security::normalizePhone($phone);
        return $this->db->findOneBy($this->table, 'phone', $phone);
    }
    
    /**
     * Найти пользователя по email или телефону
     */
    public function findByLogin(string $login): ?array
    {
        $user = $this->findByEmail($login);
        if ($user === null) {
            $user = $this->findByPhone($login);
        }
        return $user;
    }
    
    /**
     * Получить всех пользователей
     */
    public function getAll(): array
    {
        return $this->db->read($this->table);
    }
    
    /**
     * Создать пользователя
     */
    public function create(array $data): int
    {
        $user = [
            'name' => Security::sanitize($data['name']),
            'phone' => Security::normalizePhone($data['phone']),
            'email' => isset($data['email']) ? Security::sanitize($data['email']) : null,
            'password' => Security::hashPassword($data['password']),
            'role' => $data['role'] ?? 'user',
            'created_at' => date('c')
        ];
        
        return $this->db->insert($this->table, $user);
    }
    
    /**
     * Обновить пользователя
     */
    public function update(int $id, array $data): bool
    {
        $updates = [];
        
        if (isset($data['name'])) {
            $updates['name'] = Security::sanitize($data['name']);
        }
        
        if (isset($data['email'])) {
            $updates['email'] = Security::sanitize($data['email']);
        }
        
        if (isset($data['phone'])) {
            $updates['phone'] = Security::normalizePhone($data['phone']);
        }
        
        if (isset($data['role'])) {
            $updates['role'] = $data['role'];
        }
        
        if (isset($data['password'])) {
            $updates['password'] = Security::hashPassword($data['password']);
        }
        
        return $this->db->update($this->table, $id, $updates);
    }
    
    /**
     * Удалить пользователя
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, $id);
    }
    
    /**
     * Проверка пароля
     */
    public function verifyPassword(array $user, string $password): bool
    {
        return isset($user['password']) && Security::verifyPassword($password, $user['password']);
    }
    
    /**
     * Проверка существования телефона
     */
    public function phoneExists(string $phone, ?int $excludeId = null): bool
    {
        $phone = Security::normalizePhone($phone);
        $users = $this->db->findBy($this->table, 'phone', $phone);
        
        if ($excludeId !== null) {
            $users = array_filter($users, fn($u) => $u['id'] != $excludeId);
        }
        
        return !empty($users);
    }
    
    /**
     * Проверка существования email
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $users = $this->db->findBy($this->table, 'email', $email);
        
        if ($excludeId !== null) {
            $users = array_filter($users, fn($u) => $u['id'] != $excludeId);
        }
        
        return !empty($users);
    }
    
    /**
     * Получить пользователей по роли
     */
    public function getByRole(string $role): array
    {
        return $this->db->findBy($this->table, 'role', $role);
    }
    
    /**
     * Получить курьеров
     */
    public function getCouriers(): array
    {
        return $this->getByRole('courier');
    }
    
    /**
     * Получить сборщиков
     */
    public function getPickers(): array
    {
        return $this->getByRole('picker');
    }
    
    /**
     * Получить админов
     */
    public function getAdmins(): array
    {
        return $this->getByRole('admin');
    }
    
    /**
     * Очистить пароль из данных пользователя
     */
    public static function withoutPassword(array $user): array
    {
        unset($user['password']);
        return $user;
    }
}