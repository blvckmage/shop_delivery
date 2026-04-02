<!DOCTYPE html>
<html lang="ru">
<head>
    <?php include __DIR__ . '/pwa-head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Регистрация - Delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        warm: {
                            50: '#FFF9F5',
                            100: '#FFF3EB',
                            200: '#FFE4D1',
                            300: '#FFC9A8',
                            400: '#FFA573',
                            500: '#FF7A3D',
                            600: '#F05A1A',
                            700: '#CC4412',
                            800: '#A33510',
                            900: '#7A2A0E',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(180deg, #FFF9F5 0%, #FFE4D1 100%);
        }
        
        .card-shadow {
            box-shadow: 0 4px 20px rgba(240, 90, 26, 0.08);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF7A3D 0%, #F05A1A 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 90, 26, 0.3);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            transform: none;
            box-shadow: none;
        }
        
        .input-field {
            transition: all 0.2s ease;
        }
        
        .input-field:focus {
            border-color: #FF7A3D;
            box-shadow: 0 0 0 3px rgba(255, 122, 61, 0.1);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex flex-col">
    <!-- Header -->
    <header class="px-4 py-4">
        <div class="container mx-auto">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-800">Delivery</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white rounded-3xl p-6 md:p-8 card-shadow">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Создать аккаунт</h1>
                    <p class="text-gray-500">Заполните форму для регистрации</p>
                </div>
                
                <form id="registerForm" onsubmit="handleRegister(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <input type="text" 
                               id="name" 
                               name="name"
                               placeholder="Ваше имя"
                               class="input-field w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700 placeholder-gray-400"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone"
                               placeholder="+7 (___) ___-__-__"
                               class="input-field w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700 placeholder-gray-400"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Пароль</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password"
                                   placeholder="Минимум 6 символов"
                                   class="input-field w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700 placeholder-gray-400 pr-12"
                                   required
                                   minlength="6">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Подтвердите пароль</label>
                        <div class="relative">
                            <input type="password" 
                                   id="passwordConfirm" 
                                   name="passwordConfirm"
                                   placeholder="Повторите пароль"
                                   class="input-field w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700 placeholder-gray-400 pr-12"
                                   required>
                        </div>
                    </div>
                    
                    <div id="error" class="hidden text-red-500 text-sm text-center py-2 bg-red-50 rounded-xl"></div>
                    
                    <button type="submit" 
                            id="submitBtn"
                            class="w-full btn-primary text-white py-4 rounded-xl font-semibold text-lg">
                        Зарегистрироваться
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-500">
                        Уже есть аккаунт? 
                        <a href="/login" class="text-warm-500 hover:text-warm-600 font-medium">Войти</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Phone mask
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value[0] === '7' || value[0] === '8') {
                    value = value.substring(1);
                }
                let formatted = '+7';
                if (value.length > 0) formatted += ' (' + value.substring(0, 3);
                if (value.length > 3) formatted += ') ' + value.substring(3, 6);
                if (value.length > 6) formatted += '-' + value.substring(6, 8);
                if (value.length > 8) formatted += '-' + value.substring(8, 10);
                e.target.value = formatted;
            }
        });

        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        async function handleRegister(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('passwordConfirm').value;
            const errorEl = document.getElementById('error');
            const submitBtn = document.getElementById('submitBtn');
            
            // Validation
            if (password !== passwordConfirm) {
                errorEl.textContent = 'Пароли не совпадают';
                errorEl.classList.remove('hidden');
                return;
            }
            
            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Регистрация...';
            
            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, phone, password })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Редирект после успешной регистрации
                    window.location.href = data.redirect || '/profile';
                } else {
                    errorEl.textContent = data.error || 'Ошибка регистрации';
                    errorEl.classList.remove('hidden');
                }
            } catch (error) {
                errorEl.textContent = 'Ошибка соединения';
                errorEl.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Зарегистрироваться';
            }
        }
    </script>
</body>
</html>