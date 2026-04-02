<script>
// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('SW registered:', registration.scope);
            
            // Check for updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New version available
                        showToast('Доступно обновление! Перезагрузите страницу.', 'info');
                    }
                });
            });
        } catch (error) {
            console.log('SW registration failed:', error);
        }
    });
}

// PWA Install prompt
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Show install button or banner
    showInstallBanner();
});

function showInstallBanner() {
    // Check if already dismissed
    if (localStorage.getItem('pwa-install-dismissed')) return;
    
    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.className = 'fixed bottom-20 md:bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-80 bg-white rounded-2xl shadow-xl p-4 z-50 border border-warm-100';
    banner.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-warm-400 to-warm-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">Установить приложение</h3>
                <p class="text-sm text-gray-500">Добавьте Delivery на главный экран для быстрого доступа</p>
            </div>
            <button onclick="dismissInstallBanner()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex gap-2 mt-3">
            <button onclick="installPWA()" class="flex-1 btn-primary text-white py-2 rounded-xl font-medium text-sm">
                Установить
            </button>
            <button onclick="dismissInstallBanner()" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">
                Позже
            </button>
        </div>
    `;
    document.body.appendChild(banner);
    
    // Add animation
    setTimeout(() => banner.classList.add('show'), 100);
}

async function installPWA() {
    if (!deferredPrompt) return;
    
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
        console.log('PWA installed');
    }
    
    deferredPrompt = null;
    dismissInstallBanner();
}

function dismissInstallBanner() {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) {
        banner.remove();
        localStorage.setItem('pwa-install-dismissed', 'true');
    }
}

// Toast notification helper (if not exists)
if (typeof showToast !== 'function') {
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-20 left-1/2 -translate-x-1/2 px-4 py-3 rounded-xl shadow-lg z-50 text-sm font-medium ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' : 'bg-gray-900 text-white'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}
</script>