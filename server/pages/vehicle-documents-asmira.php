<?php
/**
 * Asmira Ops - Asmira Özmal Araç Evrakları
 * Orijinal React sayfasının birebir PHP karşılığı
 */
$pageTitle = 'Asmira Özmal';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/vehicle-documents/asmira';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex flex-col rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">ARAÇ EVRAKLARI</div>
                <div class="text-3xl font-black tracking-tight">Asmira Özmal</div>
            </div>
            <button type="button" onclick="openNewModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Yeni Araç/Dorse Ekle
            </button>
        </div>

        <!-- Stats Bar -->
        <div class="relative flex flex-none items-center gap-3 px-6 py-2.5">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/40 via-blue-400/20 to-transparent"></div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <div class="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                <span class="text-xs font-medium text-white/70">Toplam Araç</span>
                <span class="text-sm font-bold text-white" id="totalCount">0</span>
            </div>
        </div>

        <!-- Vehicle Cards Grid -->
        <div class="p-6">
            <div id="vehicleGrid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div class="col-span-full flex items-center justify-center py-12"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Document Side Panel -->
<div id="docPanel" class="hidden fixed inset-0 z-50 flex justify-end">
    <button type="button" class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePanel()"></button>
    <div class="relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div>
                <div class="text-lg font-bold" id="panelTitle"></div>
            </div>
            <button type="button" onclick="closePanel()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        <!-- Tabs: Çekici / Dorse -->
        <div class="flex border-b border-white/10" id="panelTabs"></div>
        <!-- Documents List -->
        <div class="flex-1 overflow-y-auto px-5 py-4" id="panelDocs"></div>
        <!-- Save Button -->
        <div class="border-t border-white/10 px-5 py-4">
            <button type="button" onclick="savePanelChanges()" id="panelSaveBtn" class="inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all">
                <i data-lucide="check-circle" class="h-4 w-4"></i>
                <span id="panelSaveText">Kaydet</span>
            </button>
        </div>
    </div>
</div>

<!-- New Vehicle Modal -->
<div id="newVehicleModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('newVehicleModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">YENİ KAYIT</div>
                <div class="text-lg font-bold">Araç/Dorse Tanımla</div>
            </div>
            <button type="button" onclick="closeModal('newVehicleModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Araç Plaka</label>
                <input type="text" id="newVehiclePlate" placeholder="Örn: 34 ASM 014" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50">
            </div>
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Dorse Plaka</label>
                <input type="text" id="newTrailerPlate" placeholder="Örn: 34 DOR 123" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50">
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('newVehicleModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveNewVehicle()" class="rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600">Kaydet</button>
        </div>
    </div>
</div>

<script>
const CATEGORY = 'asmira';
const DEFAULT_VEHICLE_DOCS = [
    { type: 'ruhsat', label: 'Ruhsat' },
    { type: 'tasitKarti', label: 'Taşıt Kartı' },
    { type: 't9Adr', label: 'T9 ADR' },
    { type: 'trafikSigortasi', label: 'Trafik Sigortası' },
    { type: 'tehlikeliMaddeSigortasi', label: 'Tehlikeli Madde Sigortası' },
    { type: 'kasko', label: 'Kasko' },
    { type: 'tuvturk', label: 'TÜVTÜRK' },
    { type: 'egzozEmisyon', label: 'Egzoz Emisyon' },
    { type: 'sayacKalibrasyon', label: 'Sayaç Kalibrasyon' },
    { type: 'takografKalibrasyon', label: 'Takograf Kalibrasyon' },
    { type: 'faaliyetBelgesi', label: 'Faaliyet Belgesi' },
    { type: 'yetkiBelgesi', label: 'Yetki Belgesi' },
    { type: 'hortumBasin', label: 'Hortum Basın.' },
    { type: 'tankMuayeneSertifikasi', label: 'Tank Muayene Sertifikası' },
    { type: 'vergiLevhasi', label: 'Vergi Levhası' },
];
function ensureVehicleDocs(docs) {
    if (!docs || docs.length === 0) return DEFAULT_VEHICLE_DOCS.map(d => ({ ...d, fileName: null, filePath: null, expiryDate: null }));
    return docs;
}

let trucks = [], trailers = [], vehicleSets = [], vehicles = [];
let panelVehicleId = null, panelTab = 'truck';
let pendingChanges = { uploads: [], expiryDates: [], deletions: [] };

document.addEventListener('DOMContentLoaded', loadData);

async function loadData() {
    try {
        [trucks, trailers, vehicleSets] = await Promise.all([
            loadTrucksWithStore(), loadTrailersWithStore(), apiRequest('/api/vehicle-sets').catch(() => []),
        ]);
        trucks.forEach(t => t.documents = ensureVehicleDocs(t.documents));
        trailers.forEach(t => t.documents = ensureVehicleDocs(t.documents));
        buildVehicles();
        renderGrid();
    } catch (e) { showToast('Veriler yüklenemedi: ' + e.message, 'error'); }
}

function buildVehicles() {
    vehicles = vehicleSets.filter(s => s.category === CATEGORY).map(set => {
        const truck = trucks.find(t => t.id === set.truckId);
        const trailer = trailers.find(t => t.id === set.trailerId);
        return {
            id: set.id, truckId: set.truckId, trailerId: set.trailerId,
            vehiclePlate: truck?.plate || '', trailerPlate: trailer?.plate || '',
            vehicleDocuments: ensureVehicleDocs(truck?.documents), trailerDocuments: ensureVehicleDocs(trailer?.documents),
        };
    });
    document.getElementById('totalCount').textContent = vehicles.length;
}

function countUploaded(docs) { return docs.filter(d => d.fileName).length; }
function countExpired(docs) { const now = new Date(); return docs.filter(d => d.expiryDate && new Date(d.expiryDate) < now).length; }

function renderGrid() {
    const grid = document.getElementById('vehicleGrid');
    if (vehicles.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-white/40"><i data-lucide="truck" class="h-12 w-12 mx-auto mb-3 opacity-30"></i><p>Henüz araç eklenmemiş</p><p class="text-xs mt-1">Sağ üstteki butona tıklayarak yeni araç ekleyin</p></div>';
        lucide.createIcons({nodes:[grid]}); return;
    }
    grid.innerHTML = vehicles.map(v => {
        const vUp = countUploaded(v.vehicleDocuments);
        const tUp = countUploaded(v.trailerDocuments);
        const uploaded = vUp + tUp;
        const total = v.vehicleDocuments.length + v.trailerDocuments.length;
        const expiredCount = countExpired(v.vehicleDocuments) + countExpired(v.trailerDocuments);
        const isComplete = uploaded === total && total > 0;
        const hasExpired = expiredCount > 0;
        const progress = total > 0 ? (uploaded / total) * 100 : 0;
        const borderCls = hasExpired
            ? 'border-red-500/40 shadow-[0_0_25px_rgba(239,68,68,0.15)]'
            : isComplete
            ? 'border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]'
            : 'border-blue-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]';
        const iconCls = hasExpired
            ? 'from-red-500/25 to-red-600/10 text-red-400'
            : isComplete
            ? 'from-emerald-500/25 to-emerald-600/10 text-emerald-400'
            : 'from-blue-500/25 to-blue-600/10 text-blue-400';
        const progCls = isComplete
            ? 'bg-gradient-to-r from-emerald-500 to-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)]'
            : 'bg-gradient-to-r from-amber-500 to-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.4)]';
        let statusBadge = '';
        if (hasExpired) {
            statusBadge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-red-500/15 px-2.5 py-1 text-[11px] font-semibold text-red-400"><i data-lucide="alert-octagon" class="h-3 w-3"></i>${expiredCount} Evrak Süresi Geçmiş</div>`;
        } else if (isComplete) {
            statusBadge = '<div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-400"><i data-lucide="check-circle" class="h-3 w-3"></i>Tüm Evraklar Tamam</div>';
        } else {
            statusBadge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-1 text-[11px] font-semibold text-amber-400"><i data-lucide="alert-triangle" class="h-3 w-3"></i>${total - uploaded} Evrak Eksik</div>`;
        }

        return `<div class="group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06] ${borderCls}">
            <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl transition-opacity group-hover:opacity-100 opacity-0"></div>
            <button type="button" onclick="deleteVehicle('${v.id}')" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20" title="Kaydı Sil"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
            <div class="mb-4 flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] ${iconCls}"><i data-lucide="truck" class="h-5 w-5"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-[15px] font-bold tracking-tight">${escapeHtml(v.vehiclePlate)}</div>
                    <div class="truncate text-xs text-white/50">${escapeHtml(v.trailerPlate)}</div>
                </div>
            </div>
            <div class="mb-3">
                <div class="mb-1.5 flex items-center justify-between text-[11px]">
                    <span class="text-white/60">Evrak Durumu</span>
                    <span class="font-semibold ${isComplete ? 'text-emerald-400' : 'text-amber-400'}">${uploaded}/${total}</span>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-white/15">
                    <div class="${progCls} h-full rounded-full transition-all duration-500" style="width:${progress}%"></div>
                </div>
            </div>
            <div class="mb-4">${statusBadge}</div>
            <div class="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">
                <button type="button" onclick="openPanel('${v.id}')" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"><i data-lucide="folder-open" class="h-3.5 w-3.5"></i>Evraklar</button>
                <button type="button" onclick="editVehicle('${v.id}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="Düzenle"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i></button>
            </div>
        </div>`;
    }).join('');
    lucide.createIcons({nodes:[grid]});
}

// ======= PANEL =======
function openPanel(vehicleId) {
    panelVehicleId = vehicleId;
    panelTab = 'truck';
    pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
    renderPanel();
    document.getElementById('docPanel').classList.remove('hidden');
    lucide.createIcons();
}
function closePanel() {
    if (hasPendingChanges() && !confirm('Kaydedilmemiş değişiklikler var. Çıkmak istediğinize emin misiniz?')) return;
    document.getElementById('docPanel').classList.add('hidden');
    panelVehicleId = null;
}
function hasPendingChanges() {
    return pendingChanges.uploads.length > 0 || pendingChanges.expiryDates.length > 0 || pendingChanges.deletions.length > 0;
}

function renderPanel() {
    const v = vehicles.find(x => x.id === panelVehicleId);
    if (!v) return;
    document.getElementById('panelTitle').textContent = panelTab === 'truck' ? v.vehiclePlate : v.trailerPlate;

    // Tabs
    const tabsEl = document.getElementById('panelTabs');
    tabsEl.innerHTML = `
        <button type="button" onclick="panelTab='truck';renderPanel();lucide.createIcons()" class="flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium transition-all ${panelTab==='truck' ? 'border-b-2 border-blue-500 text-blue-400' : 'text-white/50 hover:text-white/70'}"><i data-lucide="truck" class="h-4 w-4"></i>Araç (${v.vehiclePlate})</button>
        <button type="button" onclick="panelTab='trailer';renderPanel();lucide.createIcons()" class="flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium transition-all ${panelTab==='trailer' ? 'border-b-2 border-cyan-500 text-cyan-400' : 'text-white/50 hover:text-white/70'}"><i data-lucide="container" class="h-4 w-4"></i>Dorse (${v.trailerPlate})</button>`;

    const docs = panelTab === 'truck' ? v.vehicleDocuments : v.trailerDocuments;
    const docsEl = document.getElementById('panelDocs');
    docsEl.innerHTML = '<div class="space-y-4">' + docs.map(doc => {
        const pu = pendingChanges.uploads.find(u => u.target === panelTab && u.docType === doc.type);
        const pe = pendingChanges.expiryDates.find(e => e.target === panelTab && e.docType === doc.type);
        const pd = pendingChanges.deletions.find(d => d.target === panelTab && d.docType === doc.type);
        const fileName = pd ? null : (pu ? pu.fileName : doc.fileName);
        const fileUrl = pd ? null : (pu ? pu.fileUrl : (doc.fileUrl || (doc.filePath ? '/api/documents/download/' + doc.filePath : null)));
        const expiryDate = pe !== undefined && pe ? pe.date : doc.expiryDate;
        const expired = expiryDate ? new Date(expiryDate) < new Date() : false;
        const hasChanges = pu || pe || pd;
        const borderCls = hasChanges ? 'border-amber-500/50 bg-amber-500/5' : expired ? 'border-red-500/50 bg-red-500/10' : 'border-white/10 bg-white/5';

        let fileSection = '';
        if (fileName) {
            fileSection = `<div class="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2">
                <span class="truncate text-sm text-white/70">${escapeHtml(fileName)}</span>
                <div class="flex items-center gap-2">
                    ${fileUrl ? `<a href="${fileUrl}" target="_blank" class="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10" title="Önizle"><i data-lucide="eye" class="h-4 w-4"></i></a>` : ''}
                    <button type="button" onclick="panelDeleteDoc('${doc.type}')" class="inline-flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                </div>
            </div>`;
        } else {
            fileSection = `<div class="mt-3"><label class="inline-flex items-center gap-2 rounded-md border border-dashed border-white/20 bg-white/5 px-4 py-2 text-sm hover:bg-white/10 cursor-pointer"><i data-lucide="upload" class="h-4 w-4"></i>PDF veya Görsel Yükle<input type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="panelUpload(this,'${doc.type}')"></label></div>`;
        }

        return `<div class="rounded-lg border p-4 ${borderCls}">
            <div class="flex items-center gap-2">
                ${fileName ? '<i data-lucide="check-circle" class="h-4 w-4 text-emerald-400"></i>' : '<div class="h-4 w-4 rounded-full border-2 border-white/30"></div>'}
                <span class="font-medium">${escapeHtml(doc.label || doc.type)}</span>
                ${hasChanges ? '<span class="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">DEĞİŞİKLİK</span>' : ''}
                ${expired && !hasChanges ? '<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">SÜRESİ GEÇMİŞ</span>' : ''}
            </div>
            ${fileSection}
            <div class="mt-3 flex items-center gap-2">
                <label class="text-xs text-white/50">Son Geçerlilik:</label>
                <input type="date" value="${expiryDate || ''}" onchange="panelExpiryChange('${doc.type}',this.value)" class="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-blue-500/50">
                ${expiryDate ? `<button type="button" onclick="panelExpiryChange('${doc.type}',null)" class="text-xs text-white/40 hover:text-white/60" title="Tarihi Kaldır">✕</button>` : ''}
            </div>
        </div>`;
    }).join('') + '</div>';

    // Save button state
    const saveBtn = document.getElementById('panelSaveBtn');
    const saveText = document.getElementById('panelSaveText');
    if (hasPendingChanges()) {
        const count = pendingChanges.uploads.length + pendingChanges.expiryDates.length + pendingChanges.deletions.length;
        saveBtn.className = 'inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white transition-all bg-gradient-to-br from-emerald-600 to-emerald-700 shadow-[0_2px_10px_rgba(52,211,153,0.25)] hover:from-emerald-500 hover:to-emerald-600';
        saveText.textContent = `Kaydet (${count} değişiklik)`;
    } else {
        saveBtn.className = 'inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all';
        saveText.textContent = 'Kaydet';
    }
    lucide.createIcons({nodes:[docsEl, tabsEl]});
}

function panelUpload(input, docType) {
    const file = input.files[0]; if (!file) return;
    const fileUrl = URL.createObjectURL(file);
    pendingChanges.uploads = pendingChanges.uploads.filter(u => !(u.target === panelTab && u.docType === docType));
    pendingChanges.uploads.push({ target: panelTab, docType, file, fileName: file.name, fileUrl });
    pendingChanges.deletions = pendingChanges.deletions.filter(d => !(d.target === panelTab && d.docType === docType));
    renderPanel();
}
function panelExpiryChange(docType, date) {
    pendingChanges.expiryDates = pendingChanges.expiryDates.filter(e => !(e.target === panelTab && e.docType === docType));
    pendingChanges.expiryDates.push({ target: panelTab, docType, date: date || null });
    renderPanel();
}
function panelDeleteDoc(docType) {
    pendingChanges.deletions = pendingChanges.deletions.filter(d => !(d.target === panelTab && d.docType === docType));
    pendingChanges.deletions.push({ target: panelTab, docType });
    pendingChanges.uploads = pendingChanges.uploads.filter(u => !(u.target === panelTab && u.docType === docType));
    renderPanel();
}

async function savePanelChanges() {
    if (!hasPendingChanges() || !panelVehicleId) return;
    const v = vehicles.find(x => x.id === panelVehicleId);
    if (!v) return;
    try {
        for (const del of pendingChanges.deletions) {
            const ownerId = del.target === 'truck' ? v.truckId : v.trailerId;
            try { await apiRequest('/api/documents/update', { method: 'DELETE', body: JSON.stringify({ ownerId, ownerType: del.target, docType: del.docType }) }); } catch(e) {}
        }
        for (const up of pendingChanges.uploads) {
            const ownerId = up.target === 'truck' ? v.truckId : v.trailerId;
            try { await uploadFile(up.file, ownerId, up.target, up.docType); } catch(e) {}
        }
        for (const exp of pendingChanges.expiryDates) {
            const ownerId = exp.target === 'truck' ? v.truckId : v.trailerId;
            try { await apiRequest('/api/documents/update', { method: 'PUT', body: JSON.stringify({ ownerId, ownerType: exp.target, docType: exp.docType, expiryDate: exp.date }) }); } catch(e) {}
        }
        // Lokal verilere uygula
        const applyToLocal = (docs, target) => {
            for (const del of pendingChanges.deletions.filter(d => d.target === target)) {
                const doc = docs.find(d => d.type === del.docType);
                if (doc) { doc.fileName = null; doc.filePath = null; doc.fileUrl = null; }
            }
            for (const up of pendingChanges.uploads.filter(u => u.target === target)) {
                const doc = docs.find(d => d.type === up.docType);
                if (doc) { doc.fileName = up.fileName; doc.fileUrl = up.fileUrl; }
            }
            for (const exp of pendingChanges.expiryDates.filter(e => e.target === target)) {
                const doc = docs.find(d => d.type === exp.docType);
                if (doc) { doc.expiryDate = exp.date; }
            }
        };
        applyToLocal(v.vehicleDocuments, 'truck');
        applyToLocal(v.trailerDocuments, 'trailer');
        // Truck/Trailer dizilerine de uygula
        const truck = trucks.find(t => t.id === v.truckId);
        if (truck) truck.documents = v.vehicleDocuments;
        const trailer = trailers.find(t => t.id === v.trailerId);
        if (trailer) trailer.documents = v.trailerDocuments;
        pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
        // localStorage'a kaydet - diğer sayfalar (Dashboard, Evrak Takibi) buradan okuyacak
        saveDocStore('trucks', trucks);
        saveDocStore('trailers', trailers);
        showToast('Değişiklikler kaydedildi');
        renderGrid();
        renderPanel();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

// ======= CRUD =======
function openNewModal() { 
    document.getElementById('newVehiclePlate').value = '';
    document.getElementById('newTrailerPlate').value = '';
    openModal('newVehicleModal'); lucide.createIcons(); 
}

async function saveNewVehicle() {
    const vPlate = document.getElementById('newVehiclePlate').value.trim();
    const tPlate = document.getElementById('newTrailerPlate').value.trim();
    if (!vPlate || !tPlate) { showToast('Lütfen her iki plakayı da girin', 'error'); return; }
    try {
        const truckRes = await apiRequest('/api/trucks', { method: 'POST', body: JSON.stringify({ plate: vPlate, category: CATEGORY }) });
        const trailerRes = await apiRequest('/api/trailers', { method: 'POST', body: JSON.stringify({ plate: tPlate, category: CATEGORY }) });
        const setRes = await apiRequest('/api/vehicle-sets', { method: 'POST', body: JSON.stringify({ truckId: truckRes.id, trailerId: trailerRes.id, category: CATEGORY }) });

        // Lokal dizilere de ekle (DB offline ise GET boş dönecek)
        trucks.push({ id: truckRes.id, plate: vPlate, category: CATEGORY, documents: ensureVehicleDocs(truckRes.documents) });
        trailers.push({ id: trailerRes.id, plate: tPlate, category: CATEGORY, documents: ensureVehicleDocs(trailerRes.documents) });
        vehicleSets.push({ id: setRes.id, truckId: truckRes.id, trailerId: trailerRes.id, category: CATEGORY });
        buildVehicles();
        saveDocStore('trucks', trucks);
        saveDocStore('trailers', trailers);
        renderGrid();

        closeModal('newVehicleModal');
        showToast('Araç/Dorse eklendi');
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

async function deleteVehicle(setId) {
    if (!confirmAction('Bu kaydı silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/vehicle-sets', { method: 'DELETE', body: JSON.stringify({ id: setId }) });
        showToast('Kayıt silindi');
        await loadData();
        saveDocStore('trucks', trucks);
        saveDocStore('trailers', trailers);
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}

function editVehicle(setId) {
    // Placeholder: open panel to edit
    openPanel(setId);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
