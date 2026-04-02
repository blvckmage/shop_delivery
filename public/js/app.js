/**
 * Delivery App - Общий JavaScript
 * Анимации переходов, геолокация, утилиты
 */

// ==================== АНИМАЦИИ ПЕРЕХОДОВ ====================

/**
 * Применяет анимацию исчезновения перед переходом на новую страницу
 */
function animateTransition(url) {
    document.body.classList.add('page-exit');
    setTimeout(() => {
        window.location.href = url;
    }, 200);
}

/**
 * Анимация появления страницы
 */
function animatePageEnter() {
    document.body.classList.add('page-enter');
    setTimeout(() => {
        document.body.classList.remove('page-enter');
    }, 300);
}

// Применяем анимацию при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    animatePageEnter();
    
    // Перехватываем клики по ссылкам для анимации
    document.querySelectorAll('a[href^="/"]').forEach(link => {
        // Исключаем ссылки с target="_blank" и якорные ссылки
        if (link.target === '_blank' || link.getAttribute('href').startsWith('#')) return;
        
        link.addEventListener('click', (e) => {
            // Если нажата клавиша Ctrl/Cmd или Shift - открываем в новой вкладке
            if (e.ctrlKey || e.metaKey || e.shiftKey) return;
            
            e.preventDefault();
            animateTransition(link.href);
        });
    });
});

// ==================== ГЕОЛОКАЦИЯ ====================

/**
 * Получить текущее местоположение пользователя
 */
function getCurrentLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Геолокация не поддерживается браузером'));
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    accuracy: position.coords.accuracy
                });
            },
            (error) => {
                let message = 'Ошибка определения местоположения';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Доступ к геолокации запрещён';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Местоположение недоступно';
                        break;
                    case error.TIMEOUT:
                        message = 'Превышено время ожидания';
                        break;
                }
                reject(new Error(message));
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    });
}

/**
 * Получить адрес по координатам (обратное геокодирование)
 */
async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(`/api/geocode/reverse?lat=${lat}&lng=${lng}`);
        if (!response.ok) throw new Error('Ошибка геокодирования');
        return await response.json();
    } catch (error) {
        console.error('Reverse geocode error:', error);
        return null;
    }
}

/**
 * Заполнить поле адреса текущим местоположением
 */
async function fillAddressWithLocation(inputId = 'address') {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    // Показываем индикатор загрузки
    const originalValue = input.value;
    input.value = 'Определяем местоположение...';
    input.disabled = true;
    
    try {
        // Получаем координаты
        const location = await getCurrentLocation();
        
        // Получаем адрес
        const addressData = await reverseGeocode(location.lat, location.lng);
        
        if (addressData && addressData.address) {
            input.value = addressData.address;
            
            // Сохраняем координаты для отправки
            input.dataset.lat = location.lat;
            input.dataset.lng = location.lng;
            
            showToast('Адрес определён');
        } else {
            input.value = originalValue;
            showToast('Не удалось определить адрес');
        }
    } catch (error) {
        input.value = originalValue;
        showToast(error.message);
    } finally {
        input.disabled = false;
    }
}

/**
 * Показать кнопку геолокации рядом с полем ввода
 */
function addGeolocationButton(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    // Создаём кнопку
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-warm-500 transition-colors';
    btn.title = 'Определить местоположение';
    btn.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    `;
    
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        fillAddressWithLocation(inputId);
    });
    
    // Оборачиваем input в контейнер если нужно
    if (!input.parentElement.classList.contains('relative')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(btn);
    } else {
        input.parentElement.appendChild(btn);
    }
}

// ==================== PUSH УВЕДОМЛЕНИЯ ====================

/**
 * Запрос разрешения на push-уведомления
 */
async function requestNotificationPermission() {
    if (!('Notification' in window)) {
        console.log('Браузер не поддерживает уведомления');
        return false;
    }
    
    if (Notification.permission === 'granted') {
        return true;
    }
    
    if (Notification.permission !== 'denied') {
        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }
    
    return false;
}

/**
 * Показать push-уведомление
 */
function showPushNotification(title, body, icon = '/favicon.ico', data = null) {
    if (Notification.permission === 'granted') {
        const notification = new Notification(title, {
            body: body,
            icon: icon,
            badge: icon,
            tag: data?.tag || 'default',
            data: data,
            requireInteraction: data?.requireInteraction || false
        });
        
        notification.onclick = function(event) {
            event.preventDefault();
            window.focus();
            if (data?.url) {
                window.location.href = data.url;
            }
            notification.close();
        };
        
        // Автоматически закрыть через 10 секунд
        setTimeout(() => notification.close(), 10000);
        
        return notification;
    }
    return null;
}

// ==================== УТИЛИТЫ ====================

/**
 * Показать toast-уведомление
 */
function showToast(message, duration = 2000) {
    // Удаляем предыдущие toast
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());
    
    const toast = document.createElement('div');
    toast.className = 'toast-notification fixed top-20 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg z-[100] text-sm font-medium transition-all duration-300 opacity-0 -translate-y-2';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Анимация появления
    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', '-translate-y-2');
    });
    
    // Автоматическое скрытие
    setTimeout(() => {
        toast.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Форматирование даты
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU', { 
        day: 'numeric', 
        month: 'short', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

/**
 * Форматирование времени назад
 */
function formatTimeAgo(dateString) {
    const diff = Math.floor((new Date() - new Date(dateString)) / 1000);
    if (diff < 60) return 'только что';
    if (diff < 3600) return Math.floor(diff / 60) + ' мин назад';
    if (diff < 86400) return Math.floor(diff / 3600) + ' ч назад';
    return Math.floor(diff / 86400) + ' дн назад';
}

// Добавляем стили для анимаций
const style = document.createElement('style');
style.textContent = `
    /* Анимация появления страницы */
    .page-enter {
        animation: pageEnter 0.3s ease-out;
    }
    
    @keyframes pageEnter {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Анимация исчезновения страницы */
    .page-exit {
        animation: pageExit 0.2s ease-in forwards;
    }
    
    @keyframes pageExit {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
    
    /* Анимация для элементов */
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .slide-up {
        animation: slideUp 0.3s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .scale-in {
        animation: scaleIn 0.2s ease-out;
    }
    
    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
`;
document.head.appendChild(style);