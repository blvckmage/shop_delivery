<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Validator;
use App\Models\UserModel;

/**
 * Контроллер авторизации
 */
class AuthController extends Controller
{
    private UserModel $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel($this->db);
    }
    
    /**
     * Страница входа
     */
    public function loginPage(Request $request): Response
    {
        if ($this->session->isLoggedIn()) {
            return $this->redirect('/profile');
        }
        
        return $this->render('login');
    }
    
    /**
     * Страница регистрации
     */
    public function registerPage(Request $request): Response
    {
        if ($this->session->isLoggedIn()) {
            return $this->redirect('/profile');
        }
        
        return $this->render('register');
    }
    
    /**
     * API входа
     */
    public function login(Request $request): Response
    {
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'email' => 'required',
            'password' => 'required'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        $login = Security::sanitize($data['email']);
        $password = $data['password'];
        
        // Поиск пользователя по email или телефону
        $user = $this->userModel->findByLogin($login);
        
        if ($user === null) {
            return $this->error('Неверный логин или пароль', 401);
        }
        
        // Проверка пароля
        if (!$this->userModel->verifyPassword($user, $password)) {
            return $this->error('Неверный логин или пароль', 401);
        }
        
        // Авторизация
        unset($user['password']);
        $this->session->setUser($user);
        $this->session->regenerate();
        
        // Определяем редирект по роли
        $redirect = '/profile';
        if ($user['role'] === 'courier') {
            $redirect = '/courier';
        } elseif ($user['role'] === 'admin') {
            $redirect = '/admin';
        }
        
        return $this->json([
            'success' => true,
            'user' => $user,
            'redirect' => $redirect
        ]);
    }
    
    /**
     * API регистрации
     */
    public function register(Request $request): Response
    {
        $data = $request->json();
        
        // Валидация
        $validator = Validator::make($data, [
            'name' => 'required,min:2',
            'phone' => 'required,phone',
            'password' => 'required,min:6'
        ]);
        
        if (!$validator->validate()) {
            return $this->error($validator->getFirstError(), 400);
        }
        
        // Проверка существования телефона
        if ($this->userModel->phoneExists($data['phone'])) {
            return $this->error('Пользователь с таким телефоном уже существует', 400);
        }
        
        // Проверка email если указан
        if (!empty($data['email'])) {
            if (!Security::validateEmail($data['email'])) {
                return $this->error('Некорректный email', 400);
            }
            if ($this->userModel->emailExists($data['email'])) {
                return $this->error('Пользователь с таким email уже существует', 400);
            }
        }
        
        // Создание пользователя
        $userId = $this->userModel->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
            'role' => 'user'
        ]);
        
        return $this->json([
            'success' => true,
            'user_id' => $userId
        ]);
    }
    
    /**
     * Выход
     */
    public function logout(Request $request): Response
    {
        $this->session->logout();
        
        return $this->json([
            'success' => true,
            'redirect' => '/'
        ]);
    }
    
    /**
     * Получить текущего пользователя
     */
    public function me(Request $request): Response
    {
        $error = $this->requireAuth();
        if ($error !== null) {
            return $error;
        }
        
        return $this->json([
            'success' => true,
            'user' => $this->getUser()
        ]);
    }
}