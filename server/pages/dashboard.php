<?php
/**
 * Asmira Ops - Dashboard (Operasyon Takvimi)
 */
$pageTitle = 'Dashboard';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 lg:px-4">
    <!-- Document Expiry Alert (takvimden bağımsız) -->
    <div id="docExpiryAlert" class="mb-2 hidden"></div>

    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/5 bg-transparent text-white" style="min-height: calc(100vh - 100px)">
        <!-- Header -->
        <div class="relative flex flex-none flex-col gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-3 py-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:px-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="changeMonth(-1)" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 transition-all hover:text-white hover:bg-white/10">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </button>
                <button type="button" onclick="changeMonth(1)" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 transition-all hover:text-white hover:bg-white/10">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </button>
                <div class="ml-1">
                    <div class="text-[10px] font-light tracking-[0.15em] text-slate-400 sm:text-sm sm:tracking-[0.2em]">BUNKER OPERASYON TAKVİMİ</div>
                    <div class="text-xl font-black tracking-tight sm:text-3xl" id="monthTitle"></div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openOperationModal()" class="btn btn-primary">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    İkmal Ekle
                </button>
            </div>
        </div>

        <!-- KPI Bar -->
        <div class="relative flex flex-none flex-wrap items-center gap-2 px-3 py-2 sm:gap-2.5 sm:px-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/40 via-cyan-400/20 to-transparent"></div>
            <div class="flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.03] px-2 py-1 sm:px-3 sm:py-1.5">
                <div class="h-1.5 w-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                <span class="text-[10px] font-medium text-white/70 sm:text-xs">İkmal</span>
                <span class="text-xs font-bold text-white sm:text-sm" id="kpiCount">0</span>
            </div>
            <div class="flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.03] px-2 py-1 sm:px-3 sm:py-1.5">
                <span class="text-[10px] font-medium text-white/70 sm:text-xs">Tonaj</span>
                <span class="text-xs font-bold text-white sm:text-sm" id="kpiTonaj">0</span>
                <span class="text-[8px] text-white/50 sm:text-[10px]">MT</span>
            </div>
            <div id="kpiLitreWrap" class="hidden items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <div class="h-2 w-2 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.6)]"></div>
                <span class="text-xs font-medium text-white/70">Litre</span>
                <span class="text-sm font-bold text-white" id="kpiLitre">0</span>
                <span class="text-[10px] text-white/50">LT</span>
            </div>
        </div>

        <!-- Calendar Grid (Desktop) -->
        <div class="hidden sm:flex flex-1 w-full flex-col overflow-hidden px-0 pb-2 sm:px-4 sm:pb-4">
            <div class="flex flex-1 w-full flex-col overflow-x-auto overflow-y-hidden">
                <div class="min-w-[600px] sm:min-w-0">
                    <!-- Week days header -->
                    <div class="grid h-6 grid-cols-7 border-b border-cyan-500/30 sm:h-8">
                        <?php foreach (['PZT','SAL','ÇAR','PER','CUM','CMT','PAZ'] as $day): ?>
                        <div class="flex items-center justify-center border-r border-cyan-500/30 px-1 text-[9px] font-semibold tracking-wide text-white drop-shadow-[0_0_4px_rgba(255,255,255,0.6)] last:border-r-0 sm:justify-start sm:px-2 sm:text-[10px]"><?= $day ?></div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Calendar cells -->
                    <div id="calendarGrid" class="grid grid-cols-7 grid-rows-6" style="height: calc(100% - 32px)"></div>
                </div>
            </div>
        </div>

        <!-- Mobile Agenda View -->
        <div class="flex flex-1 flex-col overflow-y-auto px-3 pb-4 sm:hidden" id="mobileAgenda">
            <div class="space-y-2" id="mobileAgendaList">
                <div class="flex items-center justify-center py-8 text-white/30 text-sm">Yükleniyor...</div>
            </div>
        </div>
    </div>
</div>

<!-- Operation Modal -->
<div id="operationModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('operationModal')"></div>
    <div class="relative mx-4 w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl sm:mx-0">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div>
                <div class="text-sm font-semibold tracking-wider text-white/70" id="modalSubtitle">YENİ İKMAL</div>
                <div class="text-lg font-semibold" id="modalTitle">Operasyon Ekle</div>
            </div>
            <button type="button" onclick="closeModal('operationModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        <form id="operationForm" class="space-y-3 px-5 py-4" onsubmit="saveOperation(event)">
            <input type="hidden" id="editOpId" value="">
            <!-- Vessel Type -->
            <div class="flex gap-2">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="vesselType" value="ship" checked class="hidden peer">
                    <div class="peer-checked:border-blue-500/60 peer-checked:bg-blue-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white/70 transition peer-checked:text-blue-400">
                        <i data-lucide="ship" class="h-4 w-4"></i> Gemi
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="vesselType" value="yacht" class="hidden peer">
                    <div class="peer-checked:border-purple-500/60 peer-checked:bg-purple-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white/70 transition peer-checked:text-purple-400">
                        <i data-lucide="sailboat" class="h-4 w-4"></i> Yat
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="vesselType" value="barge" class="hidden peer">
                    <div class="peer-checked:border-teal-500/60 peer-checked:bg-teal-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white/70 transition peer-checked:text-teal-400">
                        <i data-lucide="container" class="h-4 w-4"></i> Barge
                    </div>
                </label>
            </div>
            <!-- Vessel Name & IMO -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Gemi Adı *</label>
                    <input type="text" id="opVesselName" class="input-field" placeholder="M/T VESSEL NAME" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">IMO No</label>
                    <input type="text" id="opImoNumber" class="input-field" placeholder="1234567">
                </div>
            </div>
            <!-- Quantity & Unit -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Miktar *</label>
                    <input type="number" id="opQuantity" class="input-field" placeholder="0" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Birim</label>
                    <select id="opUnit" class="input-field">
                        <option value="MT">MT (Ton)</option>
                        <option value="L">L (Litre)</option>
                    </select>
                </div>
            </div>
            <!-- Loading Place & Port -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Yükleme Yeri</label>
                    <input type="text" id="opLoadingPlace" class="input-field" placeholder="Terminal">
                </div>
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">İkmal Limanı *</label>
                    <input type="text" id="opPort" class="input-field" placeholder="Liman" required>
                </div>
            </div>
            <!-- Date -->
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Tarih *</label>
                <input type="date" id="opDate" style="color-scheme:dark" class="input-field" required>
            </div>
            <!-- Submit -->
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('operationModal')" class="btn btn-ghost flex-1">İptal</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <i data-lucide="check" class="h-4 w-4"></i>
                    <span id="opSubmitText">Kaydet</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Context Menu -->
<div id="opContextMenu" class="fixed z-[100] hidden min-w-[220px] max-h-[80vh] overflow-y-auto rounded-xl border border-white/10 bg-[#0d1526]/95 shadow-[0_8px_32px_rgba(0,0,0,0.5)] backdrop-blur-xl" style="left:0;top:0">
    <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider text-white/40">İşlemler</div>
    <button type="button" onclick="ctxEdit()" class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-[13px] font-medium text-blue-300 transition-colors hover:bg-blue-500/15">
        <i data-lucide="pencil" class="h-4 w-4"></i>Düzenle
    </button>
    <div class="mx-2 h-px bg-white/10"></div>
    <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider text-white/40">Durum Değiştir</div>
    <button type="button" onclick="ctxStatus('planned')" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-[13px] text-slate-300 hover:bg-white/10"><span class="h-2 w-2 rounded-full bg-slate-400"></span>Planlandı</button>
    <button type="button" onclick="ctxStatus('approaching')" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-[13px] text-amber-300 hover:bg-amber-500/10"><span class="h-2 w-2 rounded-full bg-amber-400"></span>Yanaşıyor</button>
    <button type="button" onclick="ctxStatus('active')" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-[13px] text-blue-300 hover:bg-blue-500/10"><span class="h-2 w-2 rounded-full bg-blue-400"></span>İkmal Başladı</button>
    <button type="button" onclick="ctxStatus('completed')" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-[13px] text-emerald-300 hover:bg-emerald-500/10"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>Tamamlandı</button>
    <button type="button" onclick="ctxStatus('cancelled')" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-[13px] text-red-300 hover:bg-red-500/10"><span class="h-2 w-2 rounded-full bg-red-400"></span>İptal Edildi</button>
    <div class="mx-2 h-px bg-white/10"></div>
    <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wider text-white/40">Teslimatçı Ata</div>
    <div class="px-3 pb-1">
        <input type="text" id="ctxDelivererSearch" oninput="filterCtxDeliverers()" placeholder="Ara..." class="h-8 w-full rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none placeholder:text-white/30 focus:border-teal-500/50">
    </div>
    <div id="ctxDelivererList" class="max-h-[180px] overflow-y-auto px-1 pb-2"></div>
</div>

<script>
// ============ STATE ============
const MONTHS_TR = ['OCAK','ŞUBAT','MART','NİSAN','MAYIS','HAZİRAN','TEMMUZ','AĞUSTOS','EYLÜL','EKİM','KASIM','ARALIK'];
const STATUS_CLASSES = {
    planned: 'status-planned', approaching: 'status-approaching',
    active: 'status-active', completed: 'status-completed', cancelled: 'status-cancelled'
};
const STATUS_LABELS = {
    planned: '📋 Planlandı', approaching: '⚓ Yanaşıyor',
    active: '⛽ İkmal Başladı', completed: '✅ Tamamlandı', cancelled: '❌ İptal Edildi'
};

let currentYear = 2026;
let currentMonthIdx = new Date().getMonth();
let operations = [];
let dragId = null;
let allDeliverers = [];
let ctxOpId = null;

// ============ INIT ============
document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([loadOperations(), loadDeliverers()]);
    renderCalendar();
    loadDocExpiryAlert();
    // Close context menu on outside click
    document.addEventListener('mousedown', (e) => {
        const menu = document.getElementById('opContextMenu');
        if (menu && !menu.contains(e.target)) closeCtxMenu();
    });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeCtxMenu(); });
});

async function loadDeliverers() {
    try { allDeliverers = await apiRequest('/api/deliverers'); } catch(e) { allDeliverers = []; }
}

// ============ DOCUMENT EXPIRY ALERT ============
const DEFAULT_VEHICLE_DOCS = [
    {type:'ruhsat',label:'Ruhsat'},{type:'tasitKarti',label:'Taşıt Kartı'},{type:'t9Adr',label:'T9 ADR'},
    {type:'trafikSigortasi',label:'Trafik Sigortası'},{type:'tehlikeliMaddeSigortasi',label:'Tehlikeli Madde Sigortası'},
    {type:'kasko',label:'Kasko'},{type:'tuvturk',label:'TÜVTÜRK'},{type:'egzozEmisyon',label:'Egzoz Emisyon'},
    {type:'sayacKalibrasyon',label:'Sayaç Kalibrasyon'},{type:'takografKalibrasyon',label:'Takograf Kalibrasyon'},
    {type:'faaliyetBelgesi',label:'Faaliyet Belgesi'},{type:'yetkiBelgesi',label:'Yetki Belgesi'},
    {type:'hortumBasin',label:'Hortum Basın.'},{type:'tankMuayeneSertifikasi',label:'Tank Muayene Sertifikası'},
    {type:'vergiLevhasi',label:'Vergi Levhası'},
];
const DEFAULT_DRIVER_DOCS = [
    {type:'kimlik',label:'Kimlik'},{type:'ehliyet',label:'Ehliyet'},{type:'src5',label:'SRC 5'},{type:'psikoteknik',label:'Psikoteknik'},
    {type:'adliSicil',label:'Adli Sicil'},{type:'iseGirisBildirge',label:'İşe Giriş Bildirgesi'},
    {type:'ikametgah',label:'İkametgah'},{type:'kkdZimmet',label:'KKD Zimmet Tutanağı'},
    {type:'saglikMuayene',label:'Sağlık Muayene'},{type:'isgEgitimBelgesi',label:'İSG Eğitim Belgesi'},
    {type:'yanginEgitimSertifikasi',label:'Yangın Eğitim Sertifikası'},
];
// TIR: çekicide tank/sayaç/hortum yok, dorsede egzoz/trafik sigortası/takograf yok
const TIR_TRUCK_EXCLUDE = ['tankMuayeneSertifikasi', 'sayacKalibrasyon', 'hortumBasin'];
const TIR_TRAILER_EXCLUDE = ['egzozEmisyon', 'trafikSigortasi', 'takografKalibrasyon'];
const TRAILER_GENERAL_EXCLUDE = ['yetkiBelgesi', 'vergiLevhasi', 'faaliyetBelgesi'];

function ensureDocs(docs, defaults) {
    const existing = docs || [];
    return defaults.map(def => {
        const found = existing.find(d => d.type === def.type);
        return found ? { ...def, ...found } : { ...def, fileName: null, filePath: null, expiryDate: null };
    });
}
function filterDocsForType(docs, vehicleType, ownerType) {
    let filtered = docs;
    if (ownerType === 'trailer') filtered = filtered.filter(d => !TRAILER_GENERAL_EXCLUDE.includes(d.type));
    if (vehicleType === 'tir') {
        const exclude = ownerType === 'truck' ? TIR_TRUCK_EXCLUDE : TIR_TRAILER_EXCLUDE;
        filtered = filtered.filter(d => !exclude.includes(d.type));
    }
    return filtered;
}

let docAlertExpanded = false;

async function loadDocExpiryAlert() {
    const container = document.getElementById('docExpiryAlert');
    try {
        const [allTrucks, allTrailers, drivers, vehicleSets] = await Promise.all([
            loadTrucksWithStore(), loadTrailersWithStore(), loadDriversWithStore(),
            loadVehicleSetsWithStore(),
        ]);
        const today = new Date();
        const warningDays = 15;
        const expiring = [];

        // Araç evraklarını vehicle set üzerinden oluştur (TIR filtreleme dahil)
        const asmiraSets = (vehicleSets || []).filter(s => s.category === 'asmira');
        asmiraSets.forEach(set => {
            const vType = set.vehicleType || 'tir';
            const truck = allTrucks.find(t => t.id === set.truckId);
            if (truck) {
                filterDocsForType(ensureDocs(truck.documents, DEFAULT_VEHICLE_DOCS), vType, 'truck').forEach(doc => {
                    if (doc.expiryDate) {
                        const diff = Math.ceil((new Date(doc.expiryDate) - today) / (1000*60*60*24));
                        if (diff <= warningDays) {
                            expiring.push({ owner: truck.plate + ' (Araç)', docName: doc.label, expiryDate: doc.expiryDate, daysLeft: diff, type: 'vehicle' });
                        }
                    }
                });
            }
            if (vType === 'tir' && set.trailerId) {
                const trailer = allTrailers.find(t => t.id === set.trailerId);
                if (trailer) {
                    filterDocsForType(ensureDocs(trailer.documents, DEFAULT_VEHICLE_DOCS), vType, 'trailer').forEach(doc => {
                        if (doc.expiryDate) {
                            const diff = Math.ceil((new Date(doc.expiryDate) - today) / (1000*60*60*24));
                            if (diff <= warningDays) {
                                expiring.push({ owner: trailer.plate + ' (Dorse)', docName: doc.label, expiryDate: doc.expiryDate, daysLeft: diff, type: 'vehicle' });
                            }
                        }
                    });
                }
            }
        });
        // Sadece asmira şoförleri
        const asmiraDrivers = drivers.filter(d => (d.category || 'asmira') === 'asmira');
        asmiraDrivers.forEach(dr => {
            ensureDocs(dr.documents, DEFAULT_DRIVER_DOCS).forEach(doc => {
                if (doc.expiryDate) {
                    const diff = Math.ceil((new Date(doc.expiryDate) - today) / (1000*60*60*24));
                    if (diff <= warningDays) {
                        expiring.push({ owner: dr.name, docName: doc.label, expiryDate: doc.expiryDate, daysLeft: diff, type: 'driver' });
                    }
                }
            });
        });

        expiring.sort((a, b) => a.daysLeft - b.daysLeft);
        if (expiring.length === 0) return;

        const expiredCount = expiring.filter(d => d.daysLeft < 0).length;
        const urgentCount = expiring.filter(d => d.daysLeft >= 0 && d.daysLeft <= 5).length;
        container.classList.remove('hidden');
        renderDocAlert(container, expiring, expiredCount, urgentCount);
    } catch(e) { /* API offline, no alert */ }
}

function renderDocAlert(container, expiring, expiredCount, urgentCount) {
    let subText = '';
    if (expiredCount > 0) subText += `<span class="text-red-400">${expiredCount} süresi dolmuş</span>`;
    if (expiredCount > 0 && urgentCount > 0) subText += ' • ';
    if (urgentCount > 0) subText += `<span class="text-amber-400">${urgentCount} acil</span>`;

    let listHtml = '';
    if (docAlertExpanded) {
        listHtml = `<div class="border-t border-white/10 px-4 py-3"><div class="max-h-48 space-y-2 overflow-y-auto">`;
        expiring.forEach(doc => {
            const cls = doc.daysLeft < 0 ? 'border-red-500/30 bg-red-500/10' : doc.daysLeft <= 5 ? 'border-amber-500/30 bg-amber-500/10' : 'border-white/10 bg-white/5';
            const iconCls = doc.daysLeft < 0 ? 'text-red-400' : doc.daysLeft <= 5 ? 'text-amber-400' : 'text-white/40';
            const badgeCls = doc.daysLeft < 0 ? 'bg-red-500/20 text-red-400' : doc.daysLeft <= 5 ? 'bg-amber-500/20 text-amber-400' : 'bg-white/10 text-white/60';
            const badgeText = doc.daysLeft < 0 ? `${Math.abs(doc.daysLeft)} gün geçti` : doc.daysLeft === 0 ? 'Bugün' : `${doc.daysLeft} gün`;
            const typeLabel = doc.type === 'driver' ? 'Şoför' : 'Araç';
            listHtml += `<div class="flex items-center justify-between rounded-lg border p-3 ${cls}"><div class="flex items-center gap-3"><i data-lucide="clock" class="h-4 w-4 ${iconCls}"></i><div><div class="text-sm font-medium">${escapeHtml(doc.owner)} - ${escapeHtml(doc.docName)}</div><div class="text-xs text-white/50">${typeLabel} • ${new Date(doc.expiryDate).toLocaleDateString('tr-TR')}</div></div></div><div class="rounded-full px-2 py-1 text-xs font-semibold ${badgeCls}">${badgeText}</div></div>`;
        });
        listHtml += `</div><div class="mt-3 flex gap-2"><a href="/vehicle-documents/asmira" class="flex-1 rounded-lg border border-white/10 bg-white/5 py-2 text-center text-xs font-medium text-white/70 transition hover:bg-white/10 hover:text-white">Araç Evrakları</a><a href="/driver-documents" class="flex-1 rounded-lg border border-white/10 bg-white/5 py-2 text-center text-xs font-medium text-white/70 transition hover:bg-white/10 hover:text-white">Şoför Evrakları</a></div></div>`;
    }

    container.innerHTML = `<div class="overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-red-500/5">
        <button type="button" onclick="docAlertExpanded=!docAlertExpanded;renderDocAlert(document.getElementById('docExpiryAlert'),window._expiringDocs,${expiredCount},${urgentCount});lucide.createIcons()" class="flex w-full items-center justify-between px-4 py-3 text-left transition hover:bg-white/5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500/25 to-red-500/15 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]"><i data-lucide="file-warning" class="h-5 w-5 text-amber-400"></i></div>
                <div><div class="flex items-center gap-2 font-bold text-white">Evrak Süresi Uyarısı <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-semibold text-amber-400">${expiring.length}</span></div><div class="text-xs text-white/50">${subText}</div></div>
            </div>
            <i data-lucide="chevron-right" class="h-5 w-5 text-white/40 transition-transform ${docAlertExpanded ? 'rotate-90' : ''}"></i>
        </button>
        ${listHtml}
    </div>`;
    window._expiringDocs = expiring;
    lucide.createIcons({nodes:[container]});
}

async function loadOperations() {
    try {
        operations = await apiRequest('/api/operations');
    } catch (e) {
        console.warn('Operasyonlar yüklenemedi:', e);
        operations = [];
    }
}

// ============ CALENDAR ============
function changeMonth(dir) {
    currentMonthIdx += dir;
    if (currentMonthIdx < 0) { currentMonthIdx = 11; currentYear--; }
    if (currentMonthIdx > 11) { currentMonthIdx = 0; currentYear++; }
    renderCalendar();
}

function renderCalendar() {
    document.getElementById('monthTitle').textContent = `${MONTHS_TR[currentMonthIdx]} ${currentYear}`;

    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    const monthStart = new Date(currentYear, currentMonthIdx, 1);
    let startDay = monthStart.getDay() - 1;
    if (startDay < 0) startDay = 6;
    const gridStart = new Date(currentYear, currentMonthIdx, 1 - startDay);

    const today = new Date();
    today.setHours(0,0,0,0);

    // KPI
    const monthKey = `${currentYear}-${String(currentMonthIdx + 1).padStart(2, '0')}`;
    const monthOps = operations.filter(op => op.date && op.date.startsWith(monthKey));
    document.getElementById('kpiCount').textContent = monthOps.length;
    const totalTonaj = monthOps.filter(op => op.unit === 'MT').reduce((sum, op) => sum + Number(op.quantity || 0), 0);
    document.getElementById('kpiTonaj').textContent = totalTonaj.toLocaleString('tr-TR', { maximumFractionDigits: 2 });
    const totalLitre = monthOps.filter(op => op.unit === 'L').reduce((sum, op) => sum + Number(op.quantity || 0), 0);
    const litreWrap = document.getElementById('kpiLitreWrap');
    if (totalLitre > 0) { litreWrap.classList.remove('hidden'); litreWrap.classList.add('flex'); document.getElementById('kpiLitre').textContent = totalLitre.toLocaleString('tr-TR', { maximumFractionDigits: 0 }); }
    else { litreWrap.classList.add('hidden'); litreWrap.classList.remove('flex'); }

    // Conflict detection
    const vesselConflicts = new Map();
    operations.forEach(op => { const key = op.date + '_' + (op.vesselName||'').toLowerCase().trim(); if (!vesselConflicts.has(key)) vesselConflicts.set(key, new Set()); vesselConflicts.get(key).add(op.id); });
    const conflictIds = new Set();
    vesselConflicts.forEach((ids) => { if (ids.size > 1) ids.forEach(id => conflictIds.add(id)); });

    // 6 weeks × 7 days
    for (let i = 0; i < 42; i++) {
        const cellDate = new Date(gridStart);
        cellDate.setDate(gridStart.getDate() + i);
        const dateKey = toISODate(cellDate);
        const inMonth = cellDate.getMonth() === currentMonthIdx;
        const isToday = cellDate.getTime() === today.getTime();
        const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;

        const cell = document.createElement('div');
        cell.className = `group relative flex min-h-[100px] flex-col p-1 transition-colors sm:min-h-[120px] ${
            inMonth ? 'border-b border-r border-cyan-500/30' : 'pointer-events-none'
        } ${inMonth && isWeekend ? 'bg-white/[0.02]' : 'bg-transparent'} ${
            isToday && inMonth ? 'ring-2 ring-inset ring-blue-400/60 shadow-[0_0_0_1px_rgba(59,130,246,0.3),0_0_32px_rgba(59,130,246,0.35)]' : ''
        } ${inMonth ? 'hover:bg-white/5 cursor-pointer' : ''}`;

        if (inMonth) {
            cell.ondragover = e => e.preventDefault();
            cell.ondrop = e => handleDrop(e, dateKey);
            cell.ondblclick = () => openOperationModal(dateKey);
        }

        let html = '';
        if (inMonth) {
            html += `<div class="absolute right-1 top-0.5 z-0 text-[8px] font-semibold text-white/70 sm:right-2 sm:top-1 sm:text-[10px]">${cellDate.getDate()}</div>`;
            if (isToday) {
                html += `<div class="absolute left-1 top-1 z-20 rounded-full bg-white/10 px-1.5 py-0.5 text-[8px] font-semibold tracking-wider">BUGÜN</div>`;
            }

            const dayOps = operations.filter(op => op.date === dateKey);
            if (dayOps.length > 1) {
                html += `<div class="absolute left-1 top-1 z-20 flex h-5 w-5 items-center justify-center rounded-full bg-blue-500/80 text-[10px] font-bold text-white shadow-md">${dayOps.length}</div>`;
            }

            html += '<div class="mt-4 flex flex-col gap-y-1 overflow-y-auto pr-0.5">';
            dayOps.forEach(op => {
                const vType = op.vesselType || 'ship';
                const isYacht = vType === 'yacht';
                const isBarge = vType === 'barge';
                const vesselUpper = (op.vesselName || '').toLocaleUpperCase('tr-TR');
                const portUpper = (op.port || '-').toLocaleUpperCase('tr-TR');
                const unitLabel = op.unit === 'MT' ? 'TON' : (op.unit || 'MT').toLocaleUpperCase('tr-TR');
                const statusCls = STATUS_CLASSES[op.status] || STATUS_CLASSES.planned;
                const strikethrough = (op.status === 'completed' || op.status === 'cancelled') ? 'line-through opacity-70' : '';
                const cardBorder = isBarge ? 'border-teal-500/40 bg-teal-500/[0.10]' : isYacht ? 'border-amber-500/40 bg-amber-500/[0.10]' : 'border-blue-500/40 bg-blue-500/[0.10]';
                const qtyColor = isBarge ? 'text-teal-400' : isYacht ? 'text-amber-400' : 'text-blue-400';
                const typeTag = isBarge ? '<div class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-teal-400/70">⚓ BARGE</div>' : isYacht ? '<div class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-400/70">⛵ YAT</div>' : '';

                const hasConflict = conflictIds.has(op.id);
                html += `<div class="group/card relative z-10 flex w-full cursor-grab select-none flex-col rounded-lg border-l-[3px] border px-2 py-2 text-white shadow-md backdrop-blur-md transition ${statusCls} ${hasConflict ? 'ring-1 ring-red-400/50' : ''} ${cardBorder}" draggable="true" ondragstart="startDrag(event, '${op.id}')" oncontextmenu="showOpContext(event, '${op.id}')">
                    <button type="button" onclick="event.stopPropagation(); deleteOperation('${op.id}')" class="absolute right-1 top-0.5 flex h-5 w-5 items-center justify-center rounded-md border border-red-500/30 bg-red-500/20 text-red-100 opacity-0 transition group-hover/card:opacity-100 hover:bg-red-500/30"><i data-lucide="x" class="h-3 w-3"></i></button>
                    <a href="https://www.vesselfinder.com/vessels?name=${encodeURIComponent(vesselUpper.replace(/^M\/[TVS]\s*/i,'').replace(/^MT\s*/i,'').replace(/^MV\s*/i,'').trim())}" target="_blank" onclick="event.stopPropagation()" class="absolute right-1 bottom-0.5 flex h-5 w-5 items-center justify-center rounded-md border border-cyan-500/30 bg-cyan-500/20 text-cyan-100 opacity-0 transition group-hover/card:opacity-100 hover:bg-cyan-500/30" title="Gemiyi Haritada Gör"><i data-lucide="map-pin" class="h-3 w-3"></i></a>
                    <div class="${strikethrough}">
                        <div class="text-[13px] font-bold uppercase leading-tight tracking-tight line-clamp-2 sm:text-[15px]">${escapeHtml(vesselUpper)}</div>
                        <div class="text-[12px] leading-tight text-white/70 sm:text-[14px]">${op.loadingPlace ? escapeHtml(op.loadingPlace.toLocaleUpperCase('tr-TR')) + ' → ' : ''}${escapeHtml(portUpper)}</div>
                        <div class="text-[12px] font-semibold leading-tight sm:text-[14px] ${qtyColor}">${Number(op.quantity).toLocaleString('tr-TR')} ${unitLabel}</div>
                        ${typeTag}
                        ${op.delivererName ? '<div class="mt-0.5 flex items-center gap-1 text-[11px] text-teal-300/80 truncate"><i data-lucide="package" class="h-3 w-3 shrink-0"></i>' + escapeHtml(op.delivererName) + '</div>' : ''}
                    </div>
                </div>`;
            });
            html += '</div>';
        }

        cell.innerHTML = html;
        grid.appendChild(cell);
    }
    lucide.createIcons();
    renderMobileAgenda();
}

// ============ MOBILE AGENDA ============
function renderMobileAgenda() {
    const container = document.getElementById('mobileAgendaList');
    if (!container) return;
    const monthKey = `${currentYear}-${String(currentMonthIdx + 1).padStart(2, '0')}`;
    const monthOps = operations.filter(op => op.date && op.date.startsWith(monthKey));
    monthOps.sort((a, b) => (a.date || '').localeCompare(b.date || ''));

    if (monthOps.length === 0) {
        container.innerHTML = '<div class="flex flex-col items-center justify-center py-12 text-center"><i data-lucide="calendar-x" class="h-10 w-10 text-white/15 mb-3"></i><p class="text-sm text-white/30">Bu ay için ikmal kaydı yok</p><button type="button" onclick="openOperationModal()" class="mt-3 text-xs text-blue-400 underline">İkmal Ekle</button></div>';
        lucide.createIcons({nodes:[container]});
        return;
    }

    const today = toISODate(new Date());
    const DAYS_TR = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];

    // Günlere grupla
    const dayMap = {};
    monthOps.forEach(op => {
        if (!dayMap[op.date]) dayMap[op.date] = [];
        dayMap[op.date].push(op);
    });

    let html = '';
    Object.keys(dayMap).sort().forEach(dateKey => {
        const d = new Date(dateKey + 'T00:00:00');
        const dayName = DAYS_TR[d.getDay()];
        const dayNum = d.getDate();
        const isToday = dateKey === today;
        const isPast = dateKey < today;
        const ops = dayMap[dateKey];

        html += '<div class="' + (isToday ? 'ring-1 ring-blue-500/40 bg-blue-500/[0.04]' : '') + ' rounded-xl overflow-hidden">';
        html += '<div class="flex items-center gap-3 px-3 py-2 ' + (isToday ? 'bg-blue-500/10' : 'bg-white/[0.02]') + '">';
        html += '<div class="flex flex-col items-center justify-center w-10 h-10 rounded-lg ' + (isToday ? 'bg-blue-500 text-white' : isPast ? 'bg-white/5 text-white/30' : 'bg-white/5 text-white/70') + '">';
        html += '<span class="text-lg font-bold leading-none">' + dayNum + '</span>';
        html += '<span class="text-[9px] uppercase leading-none mt-0.5">' + dayName.substring(0,3) + '</span>';
        html += '</div>';
        html += '<div class="flex-1 min-w-0"><span class="text-xs font-medium ' + (isToday ? 'text-blue-400' : 'text-white/50') + '">' + (isToday ? 'Bugün' : dayNum + ' ' + MONTHS_TR[currentMonthIdx]) + '</span></div>';
        html += '<span class="text-[10px] font-bold text-white/40 bg-white/5 rounded-full px-2 py-0.5">' + ops.length + ' ikmal</span>';
        html += '</div>';

        ops.forEach(op => {
            const vType = op.vesselType || 'ship';
            const isBarge = vType === 'barge';
            const isYacht = vType === 'yacht';
            const borderColor = isBarge ? 'border-l-teal-500 bg-teal-500/[0.06]' : isYacht ? 'border-l-amber-500 bg-amber-500/[0.06]' : 'border-l-blue-500 bg-blue-500/[0.06]';
            const qtyColor = isBarge ? 'text-teal-400' : isYacht ? 'text-amber-400' : 'text-blue-400';
            const statusCls = STATUS_CLASSES[op.status] || '';
            const vesselUpper = (op.vesselName || '').toLocaleUpperCase('tr-TR');
            const portUpper = (op.port || '-').toLocaleUpperCase('tr-TR');
            const unitLabel = op.unit === 'MT' ? 'TON' : (op.unit || 'MT').toLocaleUpperCase('tr-TR');
            const isDone = op.status === 'completed' || op.status === 'cancelled';
            const typeLabel = isBarge ? '⚓ BARGE' : isYacht ? '⛵ YAT' : '';

            html += '<div class="mx-2 mb-2 rounded-lg border-l-[3px] ' + borderColor + ' p-3 ' + statusCls + ' active:bg-white/10 cursor-pointer" onclick="showOpContext(event,\'' + op.id + '\')">';
            html += '<div class="flex items-start justify-between gap-2 ' + (isDone ? 'opacity-60 line-through' : '') + '">';
            html += '<div class="min-w-0 flex-1">';
            html += '<div class="text-sm font-bold uppercase tracking-tight truncate">' + escapeHtml(vesselUpper) + '</div>';
            html += '<div class="text-xs text-white/60 truncate">' + (op.loadingPlace ? escapeHtml(op.loadingPlace.toLocaleUpperCase('tr-TR')) + ' → ' : '') + escapeHtml(portUpper) + '</div>';
            html += '</div>';
            html += '<div class="text-right shrink-0">';
            html += '<div class="text-sm font-bold ' + qtyColor + '">' + Number(op.quantity).toLocaleString('tr-TR') + ' ' + unitLabel + '</div>';
            if (typeLabel) html += '<div class="text-[11px] font-bold tracking-wider ' + qtyColor + '/70 text-right">' + typeLabel + '</div>';
            html += '</div></div>';
            html += '<div class="flex items-center gap-2 mt-2">';
            html += '<span class="text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-white/50">' + (STATUS_LABELS[op.status] || 'Planlandı') + '</span>';
            if (op.imoNumber) html += '<span class="text-[10px] text-white/30">IMO: ' + escapeHtml(op.imoNumber) + '</span>';
            if (op.delivererName) html += '<span class="text-[11px] text-teal-300/70 flex items-center gap-1"><i data-lucide="package" class="h-3 w-3"></i>' + escapeHtml(op.delivererName) + '</span>';
            html += '</div></div>';
        });

        html += '</div>';
    });

    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}

// ============ DRAG & DROP ============
function startDrag(e, id) {
    dragId = id;
    e.dataTransfer.setData('text/plain', id);
    e.dataTransfer.effectAllowed = 'move';
}
function handleDrop(e, dateKey) {
    e.preventDefault();
    const id = e.dataTransfer.getData('text/plain') || dragId;
    if (!id) return;
    const op = operations.find(o => o.id === id);
    if (op) {
        op.date = dateKey;
        apiRequest('/api/operations', {
            method: 'PUT',
            body: JSON.stringify({ id, date: dateKey })
        }).catch(e => console.warn('Drag update failed:', e));
        renderCalendar();
    }
    dragId = null;
}

// ============ CONTEXT MENU ============
function showOpContext(e, opId) {
    e.preventDefault();
    e.stopPropagation();
    const op = operations.find(o => o.id === opId);
    if (!op) return;
    ctxOpId = opId;

    const menu = document.getElementById('opContextMenu');
    menu.classList.remove('hidden');
    // Position — measure actual menu height, flip up if needed
    const menuH = menu.offsetHeight;
    const menuW = menu.offsetWidth || 220;
    const cx = e.clientX || e.pageX;
    const cy = e.clientY || e.pageY;
    const x = Math.min(cx, window.innerWidth - menuW - 8);
    const y = (cy + menuH + 8 > window.innerHeight) ? Math.max(8, cy - menuH) : cy;
    menu.style.left = x + 'px';
    menu.style.top = y + 'px';

    // Render deliverer list
    document.getElementById('ctxDelivererSearch').value = '';
    renderCtxDeliverers();
    lucide.createIcons({nodes:[menu]});
}

function closeCtxMenu() {
    document.getElementById('opContextMenu').classList.add('hidden');
    ctxOpId = null;
}

function ctxEdit() {
    const id = ctxOpId;
    closeCtxMenu();
    if (id) editOperation(id);
}

function ctxStatus(status) {
    const id = ctxOpId;
    closeCtxMenu();
    if (!id) return;
    const op = operations.find(o => o.id === id);
    if (!op) return;
    op.status = status;
    apiRequest('/api/operations', {
        method: 'PUT',
        body: JSON.stringify({ id, status })
    }).catch(e => console.warn('Status update failed:', e));
    renderCalendar();
    showToast('Durum güncellendi: ' + (STATUS_LABELS[status] || status));
}

function renderCtxDeliverers() {
    const q = (document.getElementById('ctxDelivererSearch')?.value || '').toLowerCase().trim();
    const list = document.getElementById('ctxDelivererList');
    const op = operations.find(o => o.id === ctxOpId);
    const assignedIds = op && op.delivererIds ? op.delivererIds : [];
    const assignedId = op ? (op.delivererId || null) : null;

    let filtered = allDeliverers;
    if (q) filtered = filtered.filter(d => d.name.toLowerCase().includes(q));

    if (filtered.length === 0) {
        list.innerHTML = '<div class="px-2 py-3 text-center text-xs text-white/30">' + (allDeliverers.length === 0 ? '<a href="/delivery-documents" class="text-teal-400 underline">Teslimatçı ekleyin</a>' : 'Sonuç yok') + '</div>';
        return;
    }

    list.innerHTML = filtered.map(d => {
        const isAssigned = d.id === assignedId || assignedIds.includes(d.id);
        return `<button type="button" onclick="assignDeliverer('${d.id}')" class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-[13px] transition-colors hover:bg-teal-500/15 ${isAssigned ? 'bg-teal-500/10 text-teal-300' : 'text-white/80'}">
            <span class="flex h-5 w-5 items-center justify-center rounded-md ${isAssigned ? 'bg-teal-500 text-white' : 'border border-white/20'}">
                ${isAssigned ? '<i data-lucide="check" class="h-3 w-3"></i>' : ''}
            </span>
            <span class="truncate">${escapeHtml(d.name)}</span>
        </button>`;
    }).join('');
    lucide.createIcons({nodes:[list]});
}

function filterCtxDeliverers() { renderCtxDeliverers(); }

async function assignDeliverer(delivererId) {
    if (!ctxOpId) return;
    const op = operations.find(o => o.id === ctxOpId);
    if (!op) return;

    const dlv = allDeliverers.find(d => d.id === delivererId);
    const isCurrently = op.delivererId === delivererId;

    if (isCurrently) {
        // Unassign
        op.delivererId = null;
        op.delivererName = null;
    } else {
        op.delivererId = delivererId;
        op.delivererName = dlv ? dlv.name : '';
    }

    apiRequest('/api/operations', {
        method: 'PUT',
        body: JSON.stringify({ id: ctxOpId, delivererId: op.delivererId, delivererName: op.delivererName })
    }).catch(e => console.warn('Deliverer assign failed:', e));

    renderCtxDeliverers();
    renderCalendar();
    showToast(isCurrently ? 'Teslimatçı kaldırıldı' : `Teslimatçı atandı: ${dlv?.name || ''}`);
}

// ============ CRUD ============
function openOperationModal(presetDate) {
    document.getElementById('editOpId').value = '';
    document.getElementById('modalSubtitle').textContent = 'YENİ İKMAL';
    document.getElementById('modalTitle').textContent = 'Operasyon Ekle';
    document.getElementById('opSubmitText').textContent = 'Kaydet';
    document.getElementById('operationForm').reset();
    if (presetDate) document.getElementById('opDate').value = presetDate;
    openModal('operationModal');
    lucide.createIcons();
}

function editOperation(opId) {
    const op = operations.find(o => o.id === opId);
    if (!op) return;
    document.getElementById('editOpId').value = op.id;
    document.getElementById('modalSubtitle').textContent = 'İKMAL DÜZENLE';
    document.getElementById('modalTitle').textContent = 'Operasyon Güncelle';
    document.getElementById('opSubmitText').textContent = 'Güncelle';
    document.getElementById('opVesselName').value = op.vesselName || '';
    document.getElementById('opImoNumber').value = op.imoNumber || '';
    document.getElementById('opQuantity').value = op.quantity || '';
    document.getElementById('opUnit').value = op.unit || 'MT';
    document.getElementById('opLoadingPlace').value = op.loadingPlace || '';
    document.getElementById('opPort').value = op.port || '';
    document.getElementById('opDate').value = op.date || '';
    
    const vType = op.vesselType || 'ship';
    document.querySelector(`input[name="vesselType"][value="${vType}"]`).checked = true;
    
    openModal('operationModal');
    lucide.createIcons();
}

async function saveOperation(e) {
    e.preventDefault();
    const editId = document.getElementById('editOpId').value;
    const data = {
        vesselName: document.getElementById('opVesselName').value.trim(),
        vesselType: document.querySelector('input[name="vesselType"]:checked')?.value || 'ship',
        imoNumber: document.getElementById('opImoNumber').value.trim(),
        quantity: parseFloat(document.getElementById('opQuantity').value) || 0,
        unit: document.getElementById('opUnit').value,
        loadingPlace: document.getElementById('opLoadingPlace').value.trim(),
        port: document.getElementById('opPort').value.trim(),
        date: document.getElementById('opDate').value,
    };

    if (!data.vesselName || !data.port || !data.date || data.quantity <= 0) {
        showToast('Lütfen zorunlu alanları doldurun', 'error');
        return;
    }

    try {
        if (editId) {
            await apiRequest('/api/operations', { method: 'PUT', body: JSON.stringify({ id: editId, ...data }) });
            const idx = operations.findIndex(o => o.id === editId);
            if (idx >= 0) Object.assign(operations[idx], data);
            showToast('Operasyon güncellendi');
        } else {
            const id = 'op_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
            const newOp = { id, ...data, status: 'planned', driverName: '', driverPhone: '', agentNote: '' };
            await apiRequest('/api/operations', { method: 'POST', body: JSON.stringify(newOp) });
            operations.unshift(newOp);
            showToast('Yeni operasyon eklendi');
        }
        closeModal('operationModal');
        renderCalendar();
    } catch (err) {
        showToast('Kayıt hatası: ' + err.message, 'error');
    }
}

async function deleteOperation(opId) {
    if (!confirmAction('Bu ikmali silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/operations', { method: 'DELETE', body: JSON.stringify({ id: opId }) });
        operations = operations.filter(o => o.id !== opId);
        renderCalendar();
        showToast('Operasyon silindi');
    } catch (err) {
        showToast('Silme hatası: ' + err.message, 'error');
    }
}

function toISODate(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
