/**
 * Asmira Ops - Shared JavaScript
 * Tüm sayfalar için ortak fonksiyonlar
 */

// ============ SWIPE GESTURES ============
(function() {
    let touchStartX = 0, touchStartY = 0, touchEl = null;
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        touchEl = e.target;
    }, { passive: true });
    document.addEventListener('touchend', function(e) {
        if (!touchEl) return;
        const dx = e.changedTouches[0].clientX - touchStartX;
        const dy = e.changedTouches[0].clientY - touchStartY;
        if (Math.abs(dx) < 60 || Math.abs(dy) > Math.abs(dx) * 0.6) return;
        // Dashboard ay değiştirme
        if (typeof changeMonth === 'function') {
            const agenda = document.getElementById('mobileAgenda');
            if (agenda && agenda.contains(touchEl)) {
                changeMonth(dx < 0 ? 1 : -1);
            }
        }
        // Side panel kapatma (sağa swipe)
        if (dx > 80) {
            const panel = touchEl.closest('.doc-side-panel');
            if (panel && typeof closePanel === 'function') closePanel();
        }
    }, { passive: true });
})();

// ============ MOBILE MENU ============
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileOverlay');
    if (!menu || !overlay) return;
    
    const isOpen = !menu.classList.contains('translate-x-full');
    if (isOpen) {
        menu.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    } else {
        menu.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
    }
}

// ============ SUBMENU TOGGLE ============
function toggleSubmenu(btn) {
    const parent = btn.closest('div');
    const submenu = parent.querySelector('.submenu') || parent.nextElementSibling;
    const chevron = btn.querySelector('.submenu-chevron') || btn.querySelector('[data-lucide="chevron-down"]');
    
    if (!submenu) return;
    
    if (submenu.classList.contains('hidden')) {
        submenu.classList.remove('hidden');
        if (chevron) chevron.classList.remove('-rotate-90');
    } else {
        submenu.classList.add('hidden');
        if (chevron) chevron.classList.add('-rotate-90');
    }
}

// ============ TOAST NOTIFICATIONS ============
function showToast(message, type = 'success', duration = 3000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const colors = {
        success: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400',
        error: 'border-red-500/30 bg-red-500/10 text-red-400',
        warning: 'border-yellow-500/30 bg-yellow-500/10 text-yellow-400',
        info: 'border-blue-500/30 bg-blue-500/10 text-blue-400',
    };
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info',
    };

    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl border ${colors[type] || colors.info} backdrop-blur-md shadow-lg toast-enter`;
    toast.innerHTML = `
        <i data-lucide="${icons[type] || icons.info}" class="h-5 w-5 shrink-0"></i>
        <span class="text-sm font-medium">${escapeHtml(message)}</span>
    `;
    container.appendChild(toast);
    lucide.createIcons({ nodes: [toast] });

    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// ============ API HELPER ============
async function apiRequest(url, options = {}) {
    const defaults = {
        headers: { 'Content-Type': 'application/json' },
    };
    const config = { ...defaults, ...options };
    if (options.headers) {
        config.headers = { ...defaults.headers, ...options.headers };
    }

    try {
        const res = await fetch(url, config);
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.error || `API Error: ${res.status}`);
        }
        return data;
    } catch (err) {
        console.error('[API]', url, err);
        throw err;
    }
}

// ============ MODAL ============
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

// ============ CONFIRM DIALOG ============
function confirmAction(message) {
    return window.confirm(message);
}

// ============ DATE HELPERS ============
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function getExpiryClass(dateStr) {
    if (!dateStr) return '';
    const now = new Date();
    const exp = new Date(dateStr);
    const diffDays = Math.ceil((exp - now) / (1000 * 60 * 60 * 24));
    if (diffDays < 0) return 'expiry-expired';
    if (diffDays <= 30) return 'expiry-warning';
    return 'expiry-ok';
}

function getExpiryText(dateStr) {
    if (!dateStr) return '';
    const now = new Date();
    const exp = new Date(dateStr);
    const diffDays = Math.ceil((exp - now) / (1000 * 60 * 60 * 24));
    if (diffDays < 0) return `${Math.abs(diffDays)} gün geçmiş`;
    if (diffDays === 0) return 'Bugün';
    if (diffDays <= 30) return `${diffDays} gün kaldı`;
    return formatDate(dateStr);
}

// ============ ESCAPE HTML ============
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ============ FILE UPLOAD HELPER ============
async function uploadFile(file, ownerId, ownerType, docType, expiryDate) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('ownerId', ownerId);
    formData.append('ownerType', ownerType);
    formData.append('docType', docType);
    if (expiryDate) formData.append('expiryDate', expiryDate);

    const res = await fetch('/api/documents/upload', { method: 'POST', body: formData });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || data.details || 'Dosya yüklenemedi (' + res.status + ')');
    return data;
}

// ============ LOADING SPINNER ============
function showLoading(elementId) {
    const el = document.getElementById(elementId);
    if (el) el.innerHTML = '<div class="flex items-center justify-center py-12"><div class="spinner"></div></div>';
}

function hideLoading(elementId) {
    const el = document.getElementById(elementId);
    if (el) el.innerHTML = '';
}

// ============ SHARED DOCUMENT STORE (localStorage) ============
const DOC_STORE_KEYS = { trucks: 'asmira-trucks', trailers: 'asmira-trailers', drivers: 'asmira-drivers', vehicleSets: 'asmira-vehicle-sets' };

function saveDocStore(key, data) {
    try { localStorage.setItem(DOC_STORE_KEYS[key], JSON.stringify(data)); } catch(e) {}
}
function loadDocStore(key) {
    try { const d = localStorage.getItem(DOC_STORE_KEYS[key]); return d ? JSON.parse(d) : null; } catch(e) { return null; }
}

async function loadTrucksWithStore() {
    try {
        const data = await apiRequest('/api/trucks');
        if (Array.isArray(data)) { saveDocStore('trucks', data); return data; }
    } catch(e) {}
    return loadDocStore('trucks') || [];
}
async function loadTrailersWithStore() {
    try {
        const data = await apiRequest('/api/trailers');
        if (Array.isArray(data)) { saveDocStore('trailers', data); return data; }
    } catch(e) {}
    return loadDocStore('trailers') || [];
}
async function loadDriversWithStore() {
    try {
        const data = await apiRequest('/api/drivers');
        if (Array.isArray(data)) { saveDocStore('drivers', data); return data; }
    } catch(e) {}
    return loadDocStore('drivers') || [];
}
async function loadVehicleSetsWithStore() {
    try {
        const data = await apiRequest('/api/vehicle-sets');
        if (Array.isArray(data)) { saveDocStore('vehicleSets', data); return data; }
    } catch(e) {}
    return loadDocStore('vehicleSets') || [];
}

// ============ DATE INPUT CALENDAR ICON FIX ============
function fixDateInputIcons() {
    document.querySelectorAll('input[type="date"], input[type="datetime-local"]').forEach(el => {
        el.style.colorScheme = 'dark';
    });
}

// ============ INIT ============
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    fixDateInputIcons();
    // Dinamik eklenen date input'ları da yakala
    new MutationObserver(fixDateInputIcons).observe(document.body, { childList: true, subtree: true });
});
