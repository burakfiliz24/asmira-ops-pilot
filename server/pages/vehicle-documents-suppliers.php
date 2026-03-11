<?php
/**
 * Asmira Ops - Tedarikçi Araç Evrakları
 * Firma bazlı organizasyon - React orijinali ile birebir
 */
$pageTitle = 'Tedarikçi Araçları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/vehicle-documents/suppliers';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-orange-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">ARAÇ EVRAKLARI</div>
                <div class="text-3xl font-black tracking-tight">Tedarikçi Araçları</div>
            </div>
            <button type="button" onclick="openNewCompanyModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600">
                <i data-lucide="plus" class="h-4 w-4"></i> Yeni Firma Ekle
            </button>
        </div>
        <!-- Stats Bar -->
        <div class="relative flex flex-none items-center gap-3 px-6 py-2.5">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/40 via-orange-400/20 to-transparent"></div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <div class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.6)]"></div>
                <span class="text-xs font-medium text-white/70">Toplam Firma</span>
                <span class="text-sm font-bold text-white" id="companyCount">0</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <i data-lucide="truck" class="h-3.5 w-3.5 text-orange-400"></i>
                <span class="text-xs font-medium text-white/70">Toplam Araç</span>
                <span class="text-sm font-bold text-white" id="vehicleCount">0</span>
            </div>
        </div>
        <!-- Companies List -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-4" id="companiesList">
                <div class="flex items-center justify-center py-12"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Document Side Panel -->
<div id="docPanel" class="hidden fixed inset-0 z-50 flex justify-end">
    <button type="button" class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePanel()"></button>
    <div class="relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div><div class="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div><div class="text-lg font-bold" id="panelTitle"></div></div>
            <button type="button" onclick="closePanel()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4" id="panelDocs"></div>
        <div class="border-t border-white/10 px-5 py-4">
            <button type="button" onclick="downloadAllDocs()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 py-3 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600">
                <i data-lucide="download" class="h-4 w-4"></i> Tüm Evrakları İndir (PDF)
            </button>
        </div>
    </div>
</div>

<!-- New Company Modal -->
<div id="newCompanyModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('newCompanyModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div><div class="text-sm font-light tracking-[0.2em] text-slate-400">YENİ FİRMA</div><div class="text-lg font-bold">Tedarikçi Firma Ekle</div></div>
            <button type="button" onclick="closeModal('newCompanyModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="px-5 py-5">
            <label class="mb-2 block text-xs font-semibold text-white/70">Firma Adı</label>
            <input type="text" id="newCompanyName" placeholder="Örn: KARABURUN NAKLİYAT" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50">
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('newCompanyModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveNewCompany()" class="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:from-orange-500 hover:to-orange-600">Kaydet</button>
        </div>
    </div>
</div>

<!-- New Vehicle Modal -->
<div id="newVehicleModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('newVehicleModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div><div class="text-sm font-light tracking-[0.2em] text-slate-400">YENİ ARAÇ</div><div class="text-lg font-bold">Araç/Dorse Tanımla</div></div>
            <button type="button" onclick="closeModal('newVehicleModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Araç Plaka</label><input type="text" id="newVehiclePlate" placeholder="Örn: 34 ABC 123" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Dorse Plaka</label><input type="text" id="newTrailerPlate" placeholder="Örn: 34 ABD 123" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('newVehicleModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveNewVehicle()" class="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:from-orange-500 hover:to-orange-600">Kaydet</button>
        </div>
    </div>
</div>

<script>
const DEFAULT_DOCS = [
    { type: 'ruhsat', label: 'Ruhsat' }, { type: 'tasitKarti', label: 'Taşıt Kartı' }, { type: 't9Adr', label: 'T9 ADR' },
    { type: 'trafikSigortasi', label: 'Trafik Sigortası' }, { type: 'tehlikeliMaddeSigortasi', label: 'Tehlikeli Madde Sigortası' },
    { type: 'kasko', label: 'Kasko' }, { type: 'tuvturk', label: 'TÜVTÜRK' }, { type: 'egzozEmisyon', label: 'Egzoz Emisyon' },
    { type: 'sayacKalibrasyon', label: 'Sayaç Kalibrasyon' }, { type: 'takografKalibrasyon', label: 'Takograf Kalibrasyon' },
    { type: 'faaliyetBelgesi', label: 'Faaliyet Belgesi' }, { type: 'yetkiBelgesi', label: 'Yetki Belgesi' },
    { type: 'hortumBasin', label: 'Hortum Basın.' }, { type: 'tankMuayeneSertifikasi', label: 'Tank Muayene Sertifikası' },
    { type: 'vergiLevhasi', label: 'Vergi Levhası' },
];
function createDocs() { return DEFAULT_DOCS.map(d => ({ ...d, fileName: null, fileUrl: null, expiryDate: null })); }
function countUploaded(docs) { return docs.filter(d => d.fileName).length; }
function countExpired(docs) { const now = new Date(); return docs.filter(d => d.expiryDate && new Date(d.expiryDate) < now).length; }

// Varsayılan firmalar
const INITIAL_COMPANIES = [
    { id: 'company_karaburun', name: 'KARABURUN NAKLİYAT', isExpanded: true, vehicles: [
        { id: 'v_kb_1', vehiclePlate: '41 KB 001', trailerPlate: '41 KBD 001', documents: createDocs() },
        { id: 'v_kb_2', vehiclePlate: '41 KB 002', trailerPlate: '41 KBD 002', documents: createDocs() },
        { id: 'v_kb_3', vehiclePlate: '41 KB 003', trailerPlate: '41 KBD 003', documents: createDocs() },
    ]},
    { id: 'company_ozturk', name: 'ÖZTÜRK PETROL TAŞIMACILIĞI', isExpanded: false, vehicles: [
        { id: 'v_oz_1', vehiclePlate: '34 OZT 100', trailerPlate: '34 OZD 100', documents: createDocs() },
        { id: 'v_oz_2', vehiclePlate: '34 OZT 101', trailerPlate: '34 OZD 101', documents: createDocs() },
    ]},
    { id: 'company_marmara', name: 'MARMARA LOJİSTİK', isExpanded: false, vehicles: [
        { id: 'v_mr_1', vehiclePlate: '16 MRL 500', trailerPlate: '16 MRD 500', documents: createDocs() },
    ]},
];

let companies = [];
let panelCompanyId = null, panelVehicleId = null;
let newVehicleCompanyId = null;

document.addEventListener('DOMContentLoaded', () => {
    // localStorage'dan yükle
    const saved = localStorage.getItem('asmira-supplier-companies');
    if (saved) { try { companies = JSON.parse(saved); } catch(e) { companies = [...INITIAL_COMPANIES]; } }
    else { companies = [...INITIAL_COMPANIES]; }
    // Evrakları ensure et
    companies.forEach(c => c.vehicles.forEach(v => { if (!v.documents || v.documents.length === 0) v.documents = createDocs(); }));
    renderAll();
});

function saveToStorage() {
    const toSave = companies.map(c => ({ ...c, vehicles: c.vehicles.map(v => ({ ...v, documents: v.documents.map(d => ({ ...d, fileUrl: null })) })) }));
    localStorage.setItem('asmira-supplier-companies', JSON.stringify(toSave));
}

function renderAll() {
    const totalVehicles = companies.reduce((s, c) => s + c.vehicles.length, 0);
    document.getElementById('companyCount').textContent = companies.length;
    document.getElementById('vehicleCount').textContent = totalVehicles;

    const container = document.getElementById('companiesList');
    if (companies.length === 0) {
        container.innerHTML = '<div class="text-center py-12 text-white/40"><i data-lucide="building-2" class="h-12 w-12 mx-auto mb-3 opacity-30"></i><p>Henüz tedarikçi firması eklenmemiş</p></div>';
        lucide.createIcons({nodes:[container]}); return;
    }

    container.innerHTML = companies.map(company => {
        const chevron = company.isExpanded ? 'chevron-down' : 'chevron-right';
        let vehiclesHtml = '';
        if (company.isExpanded) {
            if (company.vehicles.length === 0) {
                vehiclesHtml = '<div class="p-4"><div class="py-8 text-center text-sm text-white/40">Bu firmaya ait araç bulunmuyor. &quot;Araç Ekle&quot; butonunu kullanın.</div></div>';
            } else {
                vehiclesHtml = '<div class="p-4"><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">' +
                company.vehicles.map(v => {
                    const uploaded = countUploaded(v.documents);
                    const total = v.documents.length;
                    const expiredCount = countExpired(v.documents);
                    const isComplete = uploaded === total && total > 0;
                    const hasExpired = expiredCount > 0;
                    const progress = total > 0 ? (uploaded / total) * 100 : 0;
                    const borderCls = hasExpired ? 'border-red-500/40' : isComplete ? 'border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]' : 'border-orange-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]';
                    const iconCls = hasExpired ? 'from-red-500/25 to-red-600/10 text-red-400' : isComplete ? 'from-emerald-500/25 to-emerald-600/10 text-emerald-400' : 'from-orange-500/25 to-orange-600/10 text-orange-400';
                    const progCls = isComplete ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-amber-500 to-amber-400';
                    let badge = '';
                    if (hasExpired) badge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-red-500/15 px-2.5 py-1 text-[11px] font-semibold text-red-400"><i data-lucide="alert-octagon" class="h-3 w-3"></i>${expiredCount} Süresi Geçmiş</div>`;
                    else if (isComplete) badge = '<div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-400"><i data-lucide="check-circle" class="h-3 w-3"></i>Tüm Evraklar Tamam</div>';
                    else badge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-1 text-[11px] font-semibold text-amber-400"><i data-lucide="alert-triangle" class="h-3 w-3"></i>${total - uploaded} Evrak Eksik</div>`;

                    return `<div class="group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06] ${borderCls}">
                        <button type="button" onclick="event.stopPropagation();deleteVehicle('${company.id}','${v.id}','${escapeHtml(v.vehiclePlate)}')" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
                        <div class="mb-4 flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] ${iconCls}"><i data-lucide="truck" class="h-5 w-5"></i></div>
                            <div class="min-w-0 flex-1"><div class="truncate text-[15px] font-bold tracking-tight">${escapeHtml(v.vehiclePlate)}</div><div class="truncate text-xs text-white/50">${escapeHtml(v.trailerPlate)}</div></div>
                        </div>
                        <div class="mb-3"><div class="mb-1.5 flex items-center justify-between text-[11px]"><span class="text-white/60">Evrak Durumu</span><span class="font-semibold ${isComplete?'text-emerald-400':'text-amber-400'}">${uploaded}/${total}</span></div><div class="h-1.5 overflow-hidden rounded-full bg-white/15"><div class="${progCls} h-full rounded-full transition-all duration-500" style="width:${progress}%"></div></div></div>
                        <div class="mb-4">${badge}</div>
                        <div class="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">
                            <button type="button" onclick="openPanel('${company.id}','${v.id}')" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"><i data-lucide="folder-open" class="h-3.5 w-3.5"></i>Evraklar</button>
                            <button type="button" onclick="openPanel('${company.id}','${v.id}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="Düzenle"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i></button>
                        </div>
                    </div>`;
                }).join('') + '</div></div>';
            }
        }

        return `<div class="overflow-hidden rounded-xl border border-orange-500/20 bg-gradient-to-br from-white/[0.03] to-transparent">
            <div class="relative flex items-center justify-between bg-gradient-to-r from-orange-500/10 to-transparent px-4 py-3">
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/30 to-transparent"></div>
                <button type="button" onclick="toggleCompany('${company.id}')" class="flex items-center gap-3 text-left">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500/25 to-orange-600/10 text-orange-400 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]"><i data-lucide="building-2" class="h-4 w-4"></i></div>
                    <div><div class="font-bold tracking-tight">${escapeHtml(company.name)}</div><div class="text-xs text-white/50">${company.vehicles.length} araç</div></div>
                    <i data-lucide="${chevron}" class="h-4 w-4 text-orange-400/60"></i>
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openNewVehicleModal('${company.id}')" class="inline-flex h-8 items-center gap-1.5 rounded-lg bg-orange-500/10 border border-orange-500/20 px-3 text-xs font-medium text-orange-300 transition hover:bg-orange-500/20"><i data-lucide="plus" class="h-3.5 w-3.5"></i>Araç Ekle</button>
                    <button type="button" onclick="deleteCompany('${company.id}','${escapeHtml(company.name)}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 transition hover:bg-red-500/20" title="Firmayı Sil"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
                </div>
            </div>
            ${vehiclesHtml}
        </div>`;
    }).join('');
    lucide.createIcons({nodes:[container]});
}

function toggleCompany(id) { const c = companies.find(x => x.id === id); if (c) c.isExpanded = !c.isExpanded; renderAll(); }

// ===== FIRMA =====
function openNewCompanyModal() { document.getElementById('newCompanyName').value = ''; openModal('newCompanyModal'); lucide.createIcons(); }
function saveNewCompany() {
    const name = document.getElementById('newCompanyName').value.trim();
    if (!name) { showToast('Lütfen firma adını girin', 'error'); return; }
    companies.unshift({ id: 'company_' + Date.now(), name: name.toUpperCase(), isExpanded: true, vehicles: [] });
    saveToStorage(); renderAll(); closeModal('newCompanyModal'); showToast('Firma eklendi');
}
function deleteCompany(id, name) {
    if (!confirmAction(`"${name}" firmasını ve tüm araçlarını silmek istediğinize emin misiniz?`)) return;
    companies = companies.filter(c => c.id !== id); saveToStorage(); renderAll(); showToast('Firma silindi');
}

// ===== ARAÇ =====
function openNewVehicleModal(companyId) { newVehicleCompanyId = companyId; document.getElementById('newVehiclePlate').value = ''; document.getElementById('newTrailerPlate').value = ''; openModal('newVehicleModal'); lucide.createIcons(); }
function saveNewVehicle() {
    const vP = document.getElementById('newVehiclePlate').value.trim();
    const tP = document.getElementById('newTrailerPlate').value.trim();
    if (!vP || !tP) { showToast('Lütfen her iki plakayı da girin', 'error'); return; }
    const company = companies.find(c => c.id === newVehicleCompanyId);
    if (!company) return;
    company.vehicles.unshift({ id: 'v_' + Date.now(), vehiclePlate: vP, trailerPlate: tP, documents: createDocs() });
    saveToStorage(); renderAll(); closeModal('newVehicleModal'); showToast('Araç eklendi');
}
function deleteVehicle(companyId, vehicleId, plate) {
    if (!confirmAction(`"${plate}" kaydını silmek istediğinize emin misiniz?`)) return;
    const company = companies.find(c => c.id === companyId);
    if (company) company.vehicles = company.vehicles.filter(v => v.id !== vehicleId);
    saveToStorage(); renderAll(); showToast('Araç silindi');
}

// ===== EVRAK PANELİ =====
function openPanel(companyId, vehicleId) {
    panelCompanyId = companyId; panelVehicleId = vehicleId;
    renderPanel(); document.getElementById('docPanel').classList.remove('hidden'); lucide.createIcons();
}
function closePanel() { document.getElementById('docPanel').classList.add('hidden'); panelCompanyId = null; panelVehicleId = null; }

function getVehicle() {
    const c = companies.find(x => x.id === panelCompanyId);
    return c ? c.vehicles.find(v => v.id === panelVehicleId) : null;
}

function renderPanel() {
    const v = getVehicle(); if (!v) return;
    document.getElementById('panelTitle').textContent = v.vehiclePlate;
    const docsEl = document.getElementById('panelDocs');
    docsEl.innerHTML = '<div class="space-y-4">' + v.documents.map(doc => {
        const expired = doc.expiryDate ? new Date(doc.expiryDate) < new Date() : false;
        const daysLeft = doc.expiryDate ? Math.ceil((new Date(doc.expiryDate) - new Date()) / (1000*60*60*24)) : null;
        const cls = expired ? 'border-red-500/50 bg-red-500/10' : 'border-white/10 bg-white/5';
        let daysBadge = '';
        if (daysLeft !== null) {
            if (daysLeft < 0) daysBadge = `<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">SÜRESİ GEÇMİŞ</span>`;
            else if (daysLeft === 0) daysBadge = `<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">BUGÜN</span>`;
            else if (daysLeft <= 15) daysBadge = `<span class="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">${daysLeft} GÜN</span>`;
            else daysBadge = `<span class="ml-2 rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-300">${daysLeft} GÜN</span>`;
        }
        let fileSection = '';
        if (doc.fileName) {
            fileSection = `<div class="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2"><span class="truncate text-sm text-white/70">${escapeHtml(doc.fileName)}</span><div class="flex items-center gap-2">${doc.fileUrl?`<a href="${doc.fileUrl}" target="_blank" class="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10" title="Önizle"><i data-lucide="eye" class="h-4 w-4"></i></a>`:''}<button type="button" onclick="deleteDoc('${doc.type}')" class="inline-flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-4 w-4"></i></button></div></div>`;
        } else {
            fileSection = `<div class="mt-3"><label class="inline-flex items-center gap-2 rounded-md border border-dashed border-white/20 bg-white/5 px-4 py-2 text-sm hover:bg-white/10 cursor-pointer"><i data-lucide="upload" class="h-4 w-4"></i>PDF veya Görsel Yükle<input type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="uploadDoc(this,'${doc.type}')"></label></div>`;
        }
        return `<div class="rounded-lg border p-4 ${cls}">
            <div class="flex items-center gap-2">${doc.fileName?'<i data-lucide="check-circle" class="h-4 w-4 text-emerald-400"></i>':'<div class="h-4 w-4 rounded-full border-2 border-white/30"></div>'}<span class="font-medium">${escapeHtml(doc.label)}</span>${daysBadge}</div>
            ${fileSection}
            <div class="mt-3 flex items-center gap-2"><label class="text-xs text-white/50">Son Geçerlilik:</label><input type="date" value="${doc.expiryDate||''}" onchange="updateExpiry('${doc.type}',this.value)" class="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-orange-500/50">${doc.expiryDate?`<button type="button" onclick="updateExpiry('${doc.type}',null)" class="text-xs text-white/40 hover:text-white/60">✕</button>`:''}</div>
        </div>`;
    }).join('') + '</div>';
    lucide.createIcons({nodes:[docsEl]});
}

function uploadDoc(input, docType) {
    const file = input.files[0]; if (!file) return;
    const v = getVehicle(); if (!v) return;
    const doc = v.documents.find(d => d.type === docType);
    if (doc) { doc.fileName = file.name; doc.fileUrl = URL.createObjectURL(file); if (!doc.expiryDate) doc.expiryDate = '2026-12-31'; }
    saveToStorage(); renderPanel(); renderAll();
}
function deleteDoc(docType) {
    const v = getVehicle(); if (!v) return;
    const doc = v.documents.find(d => d.type === docType);
    if (doc) { doc.fileName = null; doc.fileUrl = null; doc.expiryDate = null; }
    saveToStorage(); renderPanel(); renderAll();
}
function updateExpiry(docType, date) {
    const v = getVehicle(); if (!v) return;
    const doc = v.documents.find(d => d.type === docType);
    if (doc) doc.expiryDate = date || null;
    saveToStorage(); renderPanel(); renderAll();
}
function downloadAllDocs() { showToast('PDF birleştirme özelliği yakında eklenecek', 'info'); }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
