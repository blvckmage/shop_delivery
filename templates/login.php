<!DOCTYPE html>
<html lang="ru">
<head>
    <?php include __DIR__ . '/pwa-head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Вход - Delivery</title>
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Добро пожаловать</h1>
                    <p class="text-gray-500">Войдите в свой аккаунт</p>
                </div>
                
                <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-5">
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
                                   placeholder="Введите пароль"
                                   class="input-field w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 outline-none text-gray-700 placeholder-gray-400 pr-12"
                                   required>
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div id="error" class="hidden text-red-500 text-sm text-center py-2 bg-red-50 rounded-xl"></div>
                    
                    <button type="submit" 
                            id="submitBtn"
                            class="w-full btn-primary text-white py-4 rounded-xl font-semibold text-lg">
                        Войти
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-500">
                        Нет аккаунта? 
                        <a href="/register" class="text-warm-500 hover:text-warm-600 font-medium">Зарегистрироваться</a>
                    </p>
                </div>
            </div>
            
            <!-- Demo accounts info -->
            <div class="mt-6 bg-white/50 rounded-2xl p-4 text-center">
                <p class="text-sm text-gray-500 mb-2">Демо аккаунты:</p>
                <p class="text-xs text-gray-400">Админ: +7 777 123 4567 / admin</p>
                <p class="text-xs text-gray-400">Курьер: +7 777 999 8877 / courier</p>
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

        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }

        async function handleLogin(e) {
            e.preventDefault();
            
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const errorEl = document.getElementById('error');
            const submitBtn = document.getElementById('submitBtn');
            
            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Вход...';
            
            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phone, password })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    window.location.href = data.redirect || '/';
                } else {
                    errorEl.textContent = data.error || 'Ошибка входа';
                    errorEl.classList.remove('hidden');
                }
            } catch (error) {
                errorEl.textContent = 'Ошибка соединения';
                errorEl.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Войти';
            }
        }
    </script>
</body>
</html>