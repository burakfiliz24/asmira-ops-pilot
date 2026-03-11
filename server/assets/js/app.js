/**
 * Asmira Ops - Shared JavaScript
 * Tüm sayfalar için ortak fonksiyonlar
 */

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
    if (!res.ok) throw new Error('Dosya yüklenemedi');
    return res.json();
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

// ============ INIT ============
document.addEventListener('DOMContentLoaded', function() {
    // Re-initialize Lucide icons for any dynamic content
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
