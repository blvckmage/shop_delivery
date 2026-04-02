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

// ==================== PWA ПРОВЕРКА ====================

/**
 * Проверяет, запущен ли сайт как PWA приложение
 */
function isPWA() {
    // Для Android и Desktop Chrome
    if (window.matchMedia('(display-mode: standalone)').matches) {
        return true;
    }
    // Для iOS Safari
    if (window.navigator.standalone === true) {
        return true;
    }
    // Для iOS с fullscreen mode
    if (window.matchMedia('(display-mode: fullscreen)').matches) {
        return true;
    }
    return false;
}

/**
 * Проверяет, можно ли установить PWA
 */
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Показываем баннер установки через 3 секунды
    setTimeout(() => {
        if (!isPWA() && !localStorage.getItem('pwa-install-dismissed')) {
            showInstallBanner();
        }
    }, 3000);
});

/**
 * Показать баннер установки PWA
 */
function showInstallBanner() {
    // Удаляем предыдущий баннер если есть
    document.querySelector('.pwa-install-banner')?.remove();
    
    const banner = document.createElement('div');
    banner.className = 'pwa-install-banner';
    banner.innerHTML = `
        <div class="pwa-install-content">
            <div class="pwa-install-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="pwa-install-text">
                <div class="pwa-install-title">Установите приложение</div>
                <div class="pwa-install-desc">Быстрый доступ с главного экрана</div>
            </div>
            <button class="pwa-install-btn" id="pwa-install-yes">Установить</button>
            <button class="pwa-install-close" id="pwa-install-no">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(banner);
    
    // Анимация появления
    requestAnimationFrame(() => {
        banner.classList.add('show');
    });
    
    // Кнопка установки
    document.getElementById('pwa-install-yes').addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                showToast('Приложение установлено!');
            }
            
            deferredPrompt = null;
            banner.remove();
        } else {
            // Если нет deferredPrompt (iOS), показываем инструкцию
            showIOSInstallInstructions();
            banner.remove();
        }
    });
    
    // Кнопка закрытия
    document.getElementById('pwa-install-no').addEventListener('click', () => {
        localStorage.setItem('pwa-install-dismissed', 'true');
        banner.classList.remove('show');
        setTimeout(() => banner.remove(), 300);
    });
}

/**
 * Показать инструкции для iOS
 */
function showIOSInstallInstructions() {
    const modal = document.createElement('div');
    modal.className = 'pwa-ios-modal';
    modal.innerHTML = `
        <div class="pwa-ios-content">
            <div class="pwa-ios-header">
                <span>Установка на iPhone</span>
                <button class="pwa-ios-close">&times;</button>
            </div>
            <div class="pwa-ios-steps">
                <div class="pwa-ios-step">
                    <span class="step-num">1</span>
                    <span>Нажмите кнопку "Поделиться"</span>
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <div class="pwa-ios-step">
                    <span class="step-num">2</span>
                    <span>Выберите "На экран Домой"</span>
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="pwa-ios-step">
                    <span class="step-num">3</span>
                    <span>Нажмите "Добавить"</span>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.querySelector('.pwa-ios-close').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

/**
 * Показать баннер "Открыть в приложении" если пользователь зашёл через браузер
 */
function showOpenInAppBanner() {
    if (isPWA()) return;
    if (localStorage.getItem('open-in-app-dismissed')) return;
    
    const banner = document.createElement('div');
    banner.className = 'open-in-app-banner';
    banner.innerHTML = `
        <div class="open-in-app-content">
            <span>Откройте приложение с главного экрана для лучшего опыта</span>
            <button class="open-in-app-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(banner);
    
    banner.querySelector('.open-in-app-close').addEventListener('click', () => {
        localStorage.setItem('open-in-app-dismissed', 'true');
        banner.remove();
    });
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
    
    /* PWA Install Banner */
    .pwa-install-banner {
        position: fixed;
        bottom: -100%;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 16px;
        transition: bottom 0.3s ease-out;
    }
    
    .pwa-install-banner.show {
        bottom: 0;
    }
    
    .pwa-install-content {
        max-width: 400px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
    }
    
    .pwa-install-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    
    .pwa-install-text {
        flex: 1;
    }
    
    .pwa-install-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }
    
    .pwa-install-desc {
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
    }
    
    .pwa-install-btn {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .pwa-install-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
    }
    
    .pwa-install-close {
        background: none;
        border: none;
        padding: 8px;
        cursor: pointer;
        color: #9ca3af;
        transition: color 0.2s;
    }
    
    .pwa-install-close:hover {
        color: #4b5563;
    }
    
    /* iOS Install Modal */
    .pwa-ios-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1001;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .pwa-ios-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 320px;
        overflow: hidden;
    }
    
    .pwa-ios-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
    }
    
    .pwa-ios-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #9ca3af;
        cursor: pointer;
        padding: 0 4px;
    }
    
    .pwa-ios-steps {
        padding: 20px;
    }
    
    .pwa-ios-step {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
    }
    
    .pwa-ios-step .step-num {
        width: 28px;
        height: 28px;
        background: #f97316;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    /* Open in App Banner */
    .open-in-app-banner {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
        padding: 12px 16px;
        z-index: 1000;
    }
    
    .open-in-app-content {
        max-width: 400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
    }
    
    .open-in-app-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 20px;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
    }
`;
document.head.appendChild(style);
