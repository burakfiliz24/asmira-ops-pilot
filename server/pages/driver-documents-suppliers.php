<?php
/**
 * Asmira Ops - Tedarikci Sofor Evraklari
 * Firma bazli sofor organizasyonu
 */
$pageTitle = 'Tedarikçi Şoförler';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/driver-documents/suppliers';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-orange-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">ŞOFÖR EVRAKLARI</div>
                <div class="text-3xl font-black tracking-tight">Tedarikçi Şoförler</div>
            </div>
            <button type="button" onclick="openNewDriverModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Yeni Şoför Ekle
            </button>
        </div>
        <div class="relative flex flex-none items-center gap-3 px-6 py-2.5">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/40 via-orange-400/20 to-transparent"></div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <div class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.6)]"></div>
                <span class="text-xs font-medium text-white/70">Toplam Firma</span>
                <span class="text-sm font-bold text-white" id="companyCount">0</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <i data-lucide="users" class="h-3.5 w-3.5 text-orange-400"></i>
                <span class="text-xs font-medium text-white/70">Toplam Şoför</span>
                <span class="text-sm font-bold text-white" id="driverCount">0</span>
            </div>
            <div class="relative flex-1">
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-orange-400/60"></i>
                <input type="text" id="searchInput" oninput="renderAll()" placeholder="Şoför adı, TC veya firma ara..." class="h-9 w-full rounded-lg border border-white/10 bg-white/[0.03] pl-10 pr-4 text-xs text-white outline-none placeholder:text-white/40 focus:border-orange-500/40 transition-all">
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-6" id="companiesList">
                <div class="flex items-center justify-center py-12"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Document Side Panel -->
<div id="docPanel" class="hidden fixed inset-0 z-50 flex justify-end">
    <button type="button" class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePanel()"></button>
    <div class="doc-side-panel relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div>
                <div class="text-lg font-bold" id="panelTitle"></div>
            </div>
            <button type="button" onclick="closePanel()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4" id="panelDocs"></div>
        <div class="border-t border-white/10 px-5 py-4">
            <button type="button" onclick="savePanelChanges()" id="panelSaveBtn" class="inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all">
                <i data-lucide="check-circle" class="h-4 w-4"></i><span id="panelSaveText">Kaydet</span>
            </button>
        </div>
    </div>
</div>

<!-- Edit Driver Modal -->
<div id="editDriverModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('editDriverModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">DÜZENLE</div>
                <div class="text-lg font-bold">Şoför Bilgileri</div>
            </div>
            <button type="button" onclick="closeModal('editDriverModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <input type="hidden" id="editDriverId">
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Firma Adı *</label>
                <div class="relative" id="editCompanyDropdown">
                    <button type="button" id="editCompanySelectBtn" class="flex h-11 w-full cursor-pointer items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none transition-all hover:border-orange-500/40 hover:bg-white/[0.08] active:bg-white/10">
                        <span id="editCompanySelectLabel" class="truncate text-white/40">Firma seçin</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <input type="hidden" id="editDriverCompany" value="">
                    <div id="editCompanyDropdownPanel" class="absolute left-0 right-0 top-[calc(100%+4px)] z-50 hidden max-h-64 overflow-hidden rounded-lg border border-white/15 bg-[#0f1a2e] shadow-[0_8px_30px_rgba(0,0,0,0.5)]">
                        <div class="border-b border-white/10 p-2">
                            <div class="relative">
                                <i data-lucide="search" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-white/30"></i>
                                <input type="text" id="editCompanySearchInput" oninput="filterEditCompanyList()" placeholder="Firma ara veya yeni isim yaz..." class="h-9 w-full rounded-md border border-white/10 bg-white/5 pl-8 pr-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-orange-500/40">
                            </div>
                        </div>
                        <div id="editCompanyOptions" class="max-h-44 overflow-y-auto p-1"></div>
                    </div>
                </div>
            </div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Ad Soyad *</label><input type="text" id="editDriverName" placeholder="Örn: Ahmet Yılmaz" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">TC Kimlik No *</label><input type="text" id="editDriverTcNo" placeholder="Örn: 12345678901" maxlength="11" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Telefon</label><input type="text" id="editDriverPhone" placeholder="Örn: 0532 123 45 67" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('editDriverModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveEditDriver()" class="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600">Güncelle</button>
        </div>
    </div>
</div>

<!-- New Driver Modal -->
<div id="newDriverModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('newDriverModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">YENİ KAYIT</div>
                <div class="text-lg font-bold">Tedarikçi Şoför Tanımla</div>
            </div>
            <button type="button" onclick="closeModal('newDriverModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Firma Adı *</label>
                <div class="relative" id="companyDropdown">
                    <button type="button" id="companySelectBtn" class="flex h-11 w-full cursor-pointer items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none transition-all hover:border-orange-500/40 hover:bg-white/[0.08] active:bg-white/10">
                        <span id="companySelectLabel" class="truncate text-white/40">Firma seçin veya yeni girin</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <input type="hidden" id="newDriverCompany" value="">
                    <div id="companyDropdownPanel" class="absolute left-0 right-0 top-[calc(100%+4px)] z-50 hidden max-h-64 overflow-hidden rounded-lg border border-white/15 bg-[#0f1a2e] shadow-[0_8px_30px_rgba(0,0,0,0.5)]">
                        <div class="border-b border-white/10 p-2">
                            <div class="relative">
                                <i data-lucide="search" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-white/30"></i>
                                <input type="text" id="companySearchInput" oninput="filterCompanyList()" placeholder="Firma ara veya yeni isim yaz..." class="h-9 w-full rounded-md border border-white/10 bg-white/5 pl-8 pr-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-orange-500/40">
                            </div>
                        </div>
                        <div id="companyOptions" class="max-h-44 overflow-y-auto p-1"></div>
                    </div>
                </div>
            </div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Ad Soyad *</label><input type="text" id="newDriverName" placeholder="Örn: Ahmet Yılmaz" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">TC Kimlik No *</label><input type="text" id="newDriverTcNo" placeholder="Örn: 12345678901" maxlength="11" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Telefon</label><input type="text" id="newDriverPhone" placeholder="Örn: 0532 123 45 67" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('newDriverModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveNewDriver()" class="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600">Kaydet</button>
        </div>
    </div>
</div>

<script>
const DEFAULT_DRIVER_DOCS = [
    { type: 'kimlik', label: 'Kimlik' },
    { type: 'ehliyet', label: 'Ehliyet' },
    { type: 'src5', label: 'SRC 5' },
    { type: 'psikoteknik', label: 'Psikoteknik' },
    { type: 'adliSicil', label: 'Adli Sicil' },
    { type: 'iseGirisBildirge', label: 'İşe Giriş Bildirgesi' },
    { type: 'ikametgah', label: 'İkametgah' },
    { type: 'kkdZimmet', label: 'KKD Zimmet' },
    { type: 'saglikMuayene', label: 'Sağlık Muayene' },
    { type: 'isgEgitimBelgesi', label: 'İSG Eğitim Belgesi' },
    { type: 'yanginEgitimSertifikasi', label: 'Yangın Eğitim Sertifikası' },
];

function ensureDriverDocs(driver) {
    const existing = driver.documents || [];
    driver.documents = DEFAULT_DRIVER_DOCS.map(def => {
        const found = existing.find(d => d.type === def.type);
        return found ? { ...def, ...found } : { ...def, fileName: null, filePath: null, expiryDate: null };
    });
    return driver;
}

let drivers = [];
let supplierCompanyNames = [];
let panelDriverId = null;
let pendingChanges = { uploads: [], expiryDates: [], deletions: [] };

document.addEventListener('DOMContentLoaded', loadData);

async function loadData() {
    // Tedarikçi Araç Evrakları'ndaki firma listesini yükle
    try {
        const scData = await apiRequest('/api/supplier-companies');
        if (Array.isArray(scData)) {
            supplierCompanyNames = scData.map(c => c.name).filter(Boolean);
        }
    } catch (e) { console.warn('Tedarikçi firma listesi yüklenemedi:', e); }
    // Şoförleri yükle
    try {
        drivers = (await apiRequest('/api/drivers?category=supplier')).map(ensureDriverDocs);
        renderAll();
    } catch (e) { showToast('Veriler yüklenemedi: ' + e.message, 'error'); }
}

function countUploaded(docs) { return docs.filter(d => d.fileName).length; }
function countExpired(docs) { const now = new Date(); return docs.filter(d => d.expiryDate && new Date(d.expiryDate) < now).length; }

function getCompanies() {
    const map = {};
    drivers.forEach(d => {
        const c = d.companyName || 'Firma Belirtilmemiş';
        if (!map[c]) map[c] = [];
        map[c].push(d);
    });
    return Object.entries(map).sort((a, b) => a[0].localeCompare(b[0], 'tr'));
}

function renderAll() {
    const q = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    const filtered = q ? drivers.filter(d => d.name.toLowerCase().includes(q) || (d.tcNo||'').includes(q) || (d.companyName||'').toLowerCase().includes(q)) : drivers;

    const companies = {};
    filtered.forEach(d => {
        const c = d.companyName || 'Firma Belirtilmemiş';
        if (!companies[c]) companies[c] = [];
        companies[c].push(d);
    });
    const sorted = Object.entries(companies).sort((a, b) => a[0].localeCompare(b[0], 'tr'));

    document.getElementById('companyCount').textContent = sorted.length;
    document.getElementById('driverCount').textContent = filtered.length;

    // Firma listesini güncelle
    const driverCompanies = drivers.map(d => d.companyName).filter(Boolean);
    window._allCompanyNames = [...new Set([...supplierCompanyNames, ...driverCompanies])].sort((a, b) => a.localeCompare(b, 'tr'));

    const container = document.getElementById('companiesList');
    if (sorted.length === 0) {
        container.innerHTML = '<div class="flex flex-col items-center py-12 text-center"><i data-lucide="users" class="h-12 w-12 mx-auto mb-3 opacity-20"></i><p class="text-white/40">Tedarikçi şoför bulunamadı</p><p class="text-xs text-white/25 mt-1">Yeni şoför ekleyerek başlayabilirsiniz</p></div>';
        lucide.createIcons({nodes:[container]}); return;
    }

    container.innerHTML = sorted.map(([company, companyDrivers]) => {
        const totalDocs = companyDrivers.reduce((s, d) => s + d.documents.length, 0);
        const uploadedDocs = companyDrivers.reduce((s, d) => s + countUploaded(d.documents), 0);
        const expiredDocs = companyDrivers.reduce((s, d) => s + countExpired(d.documents), 0);
        const progress = totalDocs > 0 ? Math.round((uploadedDocs / totalDocs) * 100) : 0;
        const isComplete = uploadedDocs === totalDocs && totalDocs > 0;
        const hasExpired = expiredDocs > 0;

        const borderCls = hasExpired ? 'border-red-500/30' : isComplete ? 'border-emerald-500/30' : 'border-orange-500/20';
        const progCls = hasExpired ? 'bg-red-500' : isComplete ? 'bg-emerald-500' : 'bg-orange-500';

        let driversHtml = companyDrivers.map(d => {
            const docs = d.documents || [];
            const up = countUploaded(docs);
            const tot = docs.length;
            const exp = countExpired(docs);
            const pct = tot > 0 ? (up / tot) * 100 : 0;
            const dBorder = exp > 0 ? 'border-red-500/40 shadow-[0_0_15px_rgba(239,68,68,0.1)]' : up === tot && tot > 0 ? 'border-emerald-500/40 shadow-[0_0_15px_rgba(52,211,153,0.1)]' : 'border-white/10';
            const iconCls = exp > 0 ? 'from-red-500/25 to-red-600/10 text-red-400' : up === tot && tot > 0 ? 'from-emerald-500/25 to-emerald-600/10 text-emerald-400' : 'from-orange-500/25 to-orange-600/10 text-orange-400';
            const dProgCls = up === tot && tot > 0 ? 'bg-emerald-500' : 'bg-amber-500';
            let badge = '';
            if (exp > 0) badge = '<span class="inline-flex items-center gap-1 rounded-full bg-red-500/15 px-2 py-0.5 text-[10px] font-semibold text-red-400"><i data-lucide="alert-octagon" class="h-3 w-3"></i>'+exp+' Süresi Geçmiş</span>';
            else if (up === tot && tot > 0) badge = '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold text-emerald-400"><i data-lucide="check-circle" class="h-3 w-3"></i>Tamam</span>';
            else badge = '<span class="inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-semibold text-amber-400"><i data-lucide="alert-triangle" class="h-3 w-3"></i>'+(tot-up)+' Eksik</span>';

            return '<div class="group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06] '+dBorder+'">'
                + '<button type="button" onclick="deleteDriver(\''+d.id+'\',\''+escapeHtml(d.name)+'\')" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>'
                + '<div class="mb-3 flex items-start gap-3">'
                + '<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] '+iconCls+'"><i data-lucide="user-check" class="h-5 w-5"></i></div>'
                + '<div class="min-w-0 flex-1">'
                + '<div class="truncate text-[14px] font-bold tracking-tight">'+escapeHtml(d.name)+'</div>'
                + '<div class="truncate text-xs text-white/50">'+escapeHtml(d.tcNo||'')+'</div>'
                + (d.phone ? '<div class="truncate text-xs text-white/40">'+escapeHtml(d.phone)+'</div>' : '')
                + '</div></div>'
                + '<div class="mb-2"><div class="mb-1 flex items-center justify-between text-[11px]"><span class="text-white/60">Evrak</span><span class="font-semibold '+(up===tot&&tot>0?'text-emerald-400':'text-amber-400')+'">'+up+'/'+tot+'</span></div>'
                + '<div class="h-1.5 overflow-hidden rounded-full bg-white/15"><div class="'+dProgCls+' h-full rounded-full transition-all" style="width:'+pct+'%"></div></div></div>'
                + '<div class="mb-3">'+badge+'</div>'
                + '<div class="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">'
                + '<button type="button" onclick="openPanel(\''+d.id+'\')" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"><i data-lucide="folder-open" class="h-3.5 w-3.5"></i>Evraklar</button>'
                + '<button type="button" onclick="editDriver(\''+d.id+'\')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="D\u00fczenle"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i></button>'
                + '</div></div>';
        }).join('');

        return '<div class="rounded-2xl border '+borderCls+' bg-white/[0.015] overflow-hidden">'
            + '<div class="flex items-center justify-between gap-3 px-5 py-4 bg-gradient-to-r from-orange-500/[0.06] to-transparent">'
            + '<div class="flex items-center gap-3">'
            + '<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/10"><i data-lucide="building-2" class="h-5 w-5 text-orange-400"></i></div>'
            + '<div><div class="text-base font-bold">'+escapeHtml(company)+'</div>'
            + '<div class="text-xs text-white/50">'+companyDrivers.length+' şoför</div></div></div>'
            + '<div class="flex items-center gap-3">'
            + '<div class="flex items-center gap-2 text-xs text-white/50"><span>'+uploadedDocs+'/'+totalDocs+' evrak</span></div>'
            + '<div class="h-1.5 w-24 overflow-hidden rounded-full bg-white/15"><div class="'+progCls+' h-full rounded-full" style="width:'+progress+'%"></div></div>'
            + '</div></div>'
            + '<div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">'+driversHtml+'</div></div>';
    }).join('');
    lucide.createIcons({nodes:[container]});
}

// ======= PANEL =======
function openPanel(driverId) {
    panelDriverId = driverId;
    pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
    renderPanel();
    document.getElementById('docPanel').classList.remove('hidden');
    lucide.createIcons();
}
function closePanel() {
    if (hasPendingChanges() && !confirm('Kaydedilmemiş değişiklikler var. Çıkmak istediğinize emin misiniz?')) return;
    document.getElementById('docPanel').classList.add('hidden');
    panelDriverId = null;
}
function hasPendingChanges() { return pendingChanges.uploads.length > 0 || pendingChanges.expiryDates.length > 0 || pendingChanges.deletions.length > 0; }

function renderPanel() {
    const d = drivers.find(x => x.id === panelDriverId);
    if (!d) return;
    document.getElementById('panelTitle').textContent = d.name;
    const docs = d.documents || [];
    const docsEl = document.getElementById('panelDocs');
    docsEl.innerHTML = '<div class="space-y-4">' + docs.map(doc => {
        const pu = pendingChanges.uploads.find(u => u.docType === doc.type);
        const pe = pendingChanges.expiryDates.find(e => e.docType === doc.type);
        const pd = pendingChanges.deletions.find(dd => dd.docType === doc.type);
        const fileName = pd ? null : (pu ? pu.fileName : doc.fileName);
        const fileUrl = pd ? null : (pu ? pu.fileUrl : (doc.fileUrl || (doc.filePath ? '/api/documents/download/'+doc.filePath : null)));
        const expiryDate = pe ? pe.date : doc.expiryDate;
        const expired = expiryDate ? new Date(expiryDate) < new Date() : false;
        const hasChanges = pu || pe || pd;
        const cls = hasChanges ? 'border-amber-500/50 bg-amber-500/5' : expired ? 'border-red-500/50 bg-red-500/10' : 'border-white/10 bg-white/5';
        let fs = '';
        if (fileName) {
            fs = '<div class="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2">'
                + '<span class="truncate text-sm text-white/70">'+escapeHtml(fileName)+'</span>'
                + '<div class="flex items-center gap-2">'
                + (fileUrl ? '<a href="'+fileUrl+'" target="_blank" class="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10" title="Önizle"><i data-lucide="eye" class="h-4 w-4"></i></a>' : '')
                + '<button type="button" onclick="panelDeleteDoc(\''+doc.type+'\')" class="inline-flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-4 w-4"></i></button>'
                + '</div></div>';
        } else {
            fs = '<div class="mt-3"><label class="flex items-center justify-center gap-2 rounded-md border border-dashed border-white/20 bg-white/5 px-4 py-3 text-sm hover:bg-white/10 cursor-pointer text-white/50 hover:text-white/70 transition">'
                + '<i data-lucide="upload" class="h-4 w-4"></i>Dosya sürükleyin veya tıklayın'
                + '<input type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,.tiff,.doc,.docx" onchange="panelUpload(this,\''+doc.type+'\')">'
                + '</label></div>';
        }
        return '<div class="rounded-lg border p-4 '+cls+' transition-all" ondragover="event.preventDefault();this.classList.add(\'ring-2\',\'ring-orange-400\',\'bg-orange-500/10\')" ondragenter="event.preventDefault()" ondragleave="this.classList.remove(\'ring-2\',\'ring-orange-400\',\'bg-orange-500/10\')" ondrop="handleDocDrop(event,\''+doc.type+'\')">'
            + '<div class="flex items-center gap-2">'
            + (fileName ? '<i data-lucide="check-circle" class="h-4 w-4 text-emerald-400"></i>' : '<div class="h-4 w-4 rounded-full border-2 border-white/30"></div>')
            + '<span class="font-medium">'+escapeHtml(doc.label || doc.type)+'</span>'
            + (hasChanges ? '<span class="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">DEĞİŞİKLİK</span>' : '')
            + (expired && !hasChanges ? '<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">SÜRESİ GEÇMİŞ</span>' : '')
            + '</div>' + fs
            + '<div class="mt-3 flex items-center gap-2">'
            + '<label class="text-xs text-white/50">Son Geçerlilik:</label>'
            + '<input type="date" value="'+(expiryDate||'')+'" onchange="panelExpiryChange(\''+doc.type+'\',this.value)" style="color-scheme:dark" class="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-orange-500/50">'
            + (expiryDate ? '<button type="button" onclick="panelExpiryChange(\''+doc.type+'\',null)" class="text-xs text-white/40 hover:text-white/60">\u2715</button>' : '')
            + '</div></div>';
    }).join('') + '</div>';

    const saveBtn = document.getElementById('panelSaveBtn');
    const saveText = document.getElementById('panelSaveText');
    if (hasPendingChanges()) {
        const c = pendingChanges.uploads.length + pendingChanges.expiryDates.length + pendingChanges.deletions.length;
        saveBtn.className = 'inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white transition-all bg-gradient-to-br from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600';
        saveText.textContent = 'Kaydet (' + c + ' değişiklik)';
    } else {
        saveBtn.className = 'inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all';
        saveText.textContent = 'Kaydet';
    }
    lucide.createIcons({nodes:[docsEl]});
}

function panelUpload(input, docType) {
    const file = input.files[0]; if (!file) return;
    _addUploadFile(file, docType);
}
function handleDocDrop(e, docType) {
    e.preventDefault(); e.stopPropagation();
    e.currentTarget.classList.remove('ring-2','ring-orange-400','bg-orange-500/10');
    const file = e.dataTransfer.files[0]; if (!file) return;
    _addUploadFile(file, docType);
}
function _addUploadFile(file, docType) {
    pendingChanges.uploads = pendingChanges.uploads.filter(u => u.docType !== docType);
    pendingChanges.uploads.push({ docType, file, fileName: file.name, fileUrl: URL.createObjectURL(file) });
    pendingChanges.deletions = pendingChanges.deletions.filter(d => d.docType !== docType);
    renderPanel();
}
function panelExpiryChange(docType, date) {
    pendingChanges.expiryDates = pendingChanges.expiryDates.filter(e => e.docType !== docType);
    pendingChanges.expiryDates.push({ docType, date: date || null });
    renderPanel();
}
function panelDeleteDoc(docType) {
    pendingChanges.deletions = pendingChanges.deletions.filter(d => d.docType !== docType);
    pendingChanges.deletions.push({ docType });
    pendingChanges.uploads = pendingChanges.uploads.filter(u => u.docType !== docType);
    renderPanel();
}

async function savePanelChanges() {
    if (!hasPendingChanges() || !panelDriverId) return;
    const d = drivers.find(x => x.id === panelDriverId);
    if (!d) return;
    try {
        for (const del of pendingChanges.deletions) {
            try { await apiRequest('/api/documents/update', { method: 'DELETE', body: JSON.stringify({ ownerId: panelDriverId, ownerType: 'driver', docType: del.docType }) }); } catch(e) {}
        }
        for (const up of pendingChanges.uploads) {
            try { await uploadFile(up.file, panelDriverId, 'driver', up.docType); } catch(e) {}
        }
        for (const exp of pendingChanges.expiryDates) {
            try { await apiRequest('/api/documents/update', { method: 'PUT', body: JSON.stringify({ ownerId: panelDriverId, ownerType: 'driver', docType: exp.docType, expiryDate: exp.date }) }); } catch(e) {}
        }
        const docs = d.documents || [];
        for (const del of pendingChanges.deletions) { const doc = docs.find(dd => dd.type === del.docType); if (doc) { doc.fileName = null; doc.filePath = null; doc.fileUrl = null; } }
        for (const up of pendingChanges.uploads) { const doc = docs.find(dd => dd.type === up.docType); if (doc) { doc.fileName = up.fileName; doc.fileUrl = up.fileUrl; } }
        for (const exp of pendingChanges.expiryDates) { const doc = docs.find(dd => dd.type === exp.docType); if (doc) { doc.expiryDate = exp.date; } }
        pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
        showToast('Değişiklikler kaydedildi');
        renderAll();
        renderPanel();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

// ======= CRUD =======
function openNewDriverModal() {
    document.getElementById('newDriverCompany').value = '';
    document.getElementById('companySelectLabel').textContent = 'Firma seçin veya yeni girin';
    document.getElementById('companySelectLabel').className = 'truncate text-white/40';
    document.getElementById('companyDropdownPanel').classList.add('hidden');
    document.getElementById('companySearchInput').value = '';
    document.getElementById('newDriverName').value = '';
    document.getElementById('newDriverTcNo').value = '';
    document.getElementById('newDriverPhone').value = '';
    openModal('newDriverModal'); lucide.createIcons();
}

// Butona tıklama event'ini bağla
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('companySelectBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleCompanyDropdown();
    });
});

function toggleCompanyDropdown() {
    const panel = document.getElementById('companyDropdownPanel');
    const isOpen = !panel.classList.contains('hidden');
    if (isOpen) {
        panel.classList.add('hidden');
    } else {
        panel.classList.remove('hidden');
        document.getElementById('companySearchInput').value = '';
        filterCompanyList();
        lucide.createIcons({nodes:[panel]});
        setTimeout(() => document.getElementById('companySearchInput').focus(), 50);
    }
}

function filterCompanyList() {
    const q = (document.getElementById('companySearchInput').value || '').trim();
    const qLower = q.toLowerCase();
    const list = (window._allCompanyNames || []).filter(c => !qLower || c.toLowerCase().includes(qLower));
    const opts = document.getElementById('companyOptions');

    let html = list.map(c => {
        const selected = document.getElementById('newDriverCompany').value === c;
        return '<button type="button" onclick="selectCompany(\'' + escapeHtml(c).replace(/'/g, "\\'") + '\')" class="flex w-full items-center gap-2.5 rounded-md px-3 py-2.5 text-left text-sm transition-all hover:bg-orange-500/10 ' + (selected ? 'bg-orange-500/15 text-orange-400' : 'text-white/80 hover:text-white') + '">' +
            '<i data-lucide="building-2" class="h-4 w-4 shrink-0 ' + (selected ? 'text-orange-400' : 'text-white/30') + '"></i>' +
            '<span class="truncate">' + escapeHtml(c) + '</span>' +
            (selected ? '<i data-lucide="check" class="ml-auto h-4 w-4 text-orange-400"></i>' : '') +
            '</button>';
    }).join('');

    if (q && !list.some(c => c.toLowerCase() === qLower)) {
        html += '<button type="button" onclick="selectCompany(\'' + escapeHtml(q).replace(/'/g, "\\'") + '\')" class="flex w-full items-center gap-2.5 rounded-md border-t border-white/5 px-3 py-2.5 text-left text-sm text-emerald-400 transition-all hover:bg-emerald-500/10 mt-1">' +
            '<i data-lucide="plus-circle" class="h-4 w-4 shrink-0"></i>' +
            '<span>\"' + escapeHtml(q) + '\" olarak yeni firma ekle</span></button>';
    }

    if (!html) {
        html = '<div class="px-3 py-4 text-center text-xs text-white/30">Firma bulunamadı — yukarı yeni isim yazın</div>';
    }

    opts.innerHTML = html;
    lucide.createIcons({nodes:[opts]});
}

function selectCompany(name) {
    document.getElementById('newDriverCompany').value = name;
    document.getElementById('companySelectLabel').textContent = name;
    document.getElementById('companySelectLabel').className = 'truncate text-white font-medium';
    document.getElementById('companyDropdownPanel').classList.add('hidden');
}

// Dropdown dışına tıklayınca kapat
document.addEventListener('click', function(e) {
    const dd = document.getElementById('companyDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('companyDropdownPanel')?.classList.add('hidden');
    }
});

async function saveNewDriver() {
    const companyName = document.getElementById('newDriverCompany').value.trim();
    const name = document.getElementById('newDriverName').value.trim();
    const tcNo = document.getElementById('newDriverTcNo').value.trim();
    const phone = document.getElementById('newDriverPhone').value.trim();
    if (!companyName || !name || !tcNo) { showToast('Lütfen firma, ad ve TC kimlik numarasını girin', 'error'); return; }
    try {
        const res = await apiRequest('/api/drivers', { method: 'POST', body: JSON.stringify({ name, tcNo, phone, category: 'supplier', companyName }) });
        drivers.push(ensureDriverDocs({ id: res.id || ('drv_local_' + Date.now()), name, tcNo, phone, companyName, category: 'supplier', documents: [] }));
        renderAll();
        closeModal('newDriverModal');
        showToast('Şoför eklendi');
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

// ======= EDIT DRIVER =======
function editDriver(id) {
    const d = drivers.find(x => x.id === id);
    if (!d) return;
    document.getElementById('editDriverId').value = d.id;
    document.getElementById('editDriverCompany').value = d.companyName || '';
    const label = document.getElementById('editCompanySelectLabel');
    if (d.companyName) { label.textContent = d.companyName; label.className = 'truncate text-white font-medium'; }
    else { label.textContent = 'Firma seçin'; label.className = 'truncate text-white/40'; }
    document.getElementById('editCompanyDropdownPanel').classList.add('hidden');
    document.getElementById('editCompanySearchInput').value = '';
    document.getElementById('editDriverName').value = d.name || '';
    document.getElementById('editDriverTcNo').value = d.tcNo || '';
    document.getElementById('editDriverPhone').value = d.phone || '';
    openModal('editDriverModal');
    lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('editCompanySelectBtn').addEventListener('click', function(e) {
        e.preventDefault(); e.stopPropagation();
        toggleEditCompanyDropdown();
    });
});

function toggleEditCompanyDropdown() {
    const panel = document.getElementById('editCompanyDropdownPanel');
    const isOpen = !panel.classList.contains('hidden');
    if (isOpen) { panel.classList.add('hidden'); }
    else {
        panel.classList.remove('hidden');
        document.getElementById('editCompanySearchInput').value = '';
        filterEditCompanyList();
        lucide.createIcons({nodes:[panel]});
        setTimeout(() => document.getElementById('editCompanySearchInput').focus(), 50);
    }
}

function filterEditCompanyList() {
    const q = (document.getElementById('editCompanySearchInput').value || '').trim();
    const qLower = q.toLowerCase();
    const list = (window._allCompanyNames || []).filter(c => !qLower || c.toLowerCase().includes(qLower));
    const opts = document.getElementById('editCompanyOptions');
    const currentVal = document.getElementById('editDriverCompany').value;
    let html = list.map(c => {
        const selected = currentVal === c;
        return '<button type="button" onclick="selectEditCompany(\'' + escapeHtml(c).replace(/'/g, "\\'") + '\')" class="flex w-full items-center gap-2.5 rounded-md px-3 py-2.5 text-left text-sm transition-all hover:bg-orange-500/10 ' + (selected ? 'bg-orange-500/15 text-orange-400' : 'text-white/80 hover:text-white') + '">'
            + '<i data-lucide="building-2" class="h-4 w-4 shrink-0 ' + (selected ? 'text-orange-400' : 'text-white/30') + '"></i>'
            + '<span class="truncate">' + escapeHtml(c) + '</span>'
            + (selected ? '<i data-lucide="check" class="ml-auto h-4 w-4 text-orange-400"></i>' : '')
            + '</button>';
    }).join('');
    if (q && !list.some(c => c.toLowerCase() === qLower)) {
        html += '<button type="button" onclick="selectEditCompany(\'' + escapeHtml(q).replace(/'/g, "\\'") + '\')" class="flex w-full items-center gap-2.5 rounded-md border-t border-white/5 px-3 py-2.5 text-left text-sm text-emerald-400 transition-all hover:bg-emerald-500/10 mt-1">'
            + '<i data-lucide="plus-circle" class="h-4 w-4 shrink-0"></i>'
            + '<span>\"' + escapeHtml(q) + '\" olarak yeni firma ekle</span></button>';
    }
    if (!html) html = '<div class="px-3 py-4 text-center text-xs text-white/30">Firma bulunamadı</div>';
    opts.innerHTML = html;
    lucide.createIcons({nodes:[opts]});
}

function selectEditCompany(name) {
    document.getElementById('editDriverCompany').value = name;
    document.getElementById('editCompanySelectLabel').textContent = name;
    document.getElementById('editCompanySelectLabel').className = 'truncate text-white font-medium';
    document.getElementById('editCompanyDropdownPanel').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('editCompanyDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('editCompanyDropdownPanel')?.classList.add('hidden');
    }
});

async function saveEditDriver() {
    const id = document.getElementById('editDriverId').value;
    const companyName = document.getElementById('editDriverCompany').value.trim();
    const name = document.getElementById('editDriverName').value.trim();
    const tcNo = document.getElementById('editDriverTcNo').value.trim();
    const phone = document.getElementById('editDriverPhone').value.trim();
    if (!companyName || !name || !tcNo) { showToast('Lütfen firma, ad ve TC kimlik numarasını girin', 'error'); return; }
    const d = drivers.find(x => x.id === id);
    if (!d) return;
    try {
        await apiRequest('/api/drivers', { method: 'PUT', body: JSON.stringify({ id, name, tcNo, phone, companyName }) });
    } catch (e) { console.warn('API güncelleme hatası (devam ediliyor):', e); }
    d.name = name;
    d.tcNo = tcNo;
    d.phone = phone;
    d.companyName = companyName;
    renderAll();
    closeModal('editDriverModal');
    showToast('Şoför bilgileri güncellendi');
}

async function deleteDriver(id, name) {
    if (!confirmAction('"'+name+'" kaydını silmek istediğinize emin misiniz?')) return;
    try { await apiRequest('/api/drivers', { method: 'DELETE', body: JSON.stringify({ id }) }); } catch (e) { console.warn('API delete hatası:', e); }
    drivers = drivers.filter(d => d.id !== id);
    if (panelDriverId === id) { panelDriverId = null; document.getElementById('docPanel').classList.add('hidden'); }
    renderAll();
    showToast('Şoför silindi');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
