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
    <div class="doc-side-panel relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div><div class="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div><div class="text-lg font-bold" id="panelTitle"></div></div>
            <button type="button" onclick="closePanel()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="flex border-b border-white/10" id="panelTabs"></div>
        <div class="flex-1 overflow-y-auto px-5 py-4" id="panelDocs"></div>
        <div class="border-t border-white/10 px-5 py-4 space-y-2">
            <button type="button" onclick="savePanelChanges()" id="panelSaveBtn" class="inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all">
                <i data-lucide="check-circle" class="h-4 w-4"></i><span id="panelSaveText">Kaydet</span>
            </button>
            <button type="button" onclick="downloadAllDocs()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white/60 transition-all hover:bg-white/10 hover:text-white/80">
                <i data-lucide="download" class="h-4 w-4"></i> Tüm Evrakları İndir (PDF)
            </button>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div id="editVehicleModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('editVehicleModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent"></div>
            <div><div class="text-sm font-light tracking-[0.2em] text-slate-400">DÜZENLE</div><div class="text-lg font-bold">Plaka Bilgilerini Güncelle</div></div>
            <button type="button" onclick="closeModal('editVehicleModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <input type="hidden" id="editCompanyId" value="">
        <input type="hidden" id="editVehicleId" value="">
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Araç Tipi</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="editSupVehicleType" value="tir" class="hidden peer" onchange="toggleSupEditDorse()">
                        <div class="peer-checked:border-orange-500/60 peer-checked:bg-orange-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-orange-400">
                            <i data-lucide="truck" class="h-5 w-5"></i> TIR
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="editSupVehicleType" value="kirkayak" class="hidden peer" onchange="toggleSupEditDorse()">
                        <div class="peer-checked:border-purple-500/60 peer-checked:bg-purple-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-purple-400">
                            <i data-lucide="container" class="h-5 w-5"></i> KIRKAYAK
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="editSupVehicleType" value="kucuk" class="hidden peer" onchange="toggleSupEditDorse()">
                        <div class="peer-checked:border-cyan-500/60 peer-checked:bg-cyan-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-cyan-400">
                            <i data-lucide="car" class="h-5 w-5"></i> KÜÇÜK
                        </div>
                    </label>
                </div>
            </div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Araç Plaka</label><input type="text" id="editVehiclePlate" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div id="editSupDorseRow"><label class="mb-2 block text-xs font-semibold text-white/70">Dorse Plaka</label><input type="text" id="editTrailerPlate" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('editVehicleModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveEditVehicle()" class="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:from-orange-500 hover:to-orange-600">Güncelle</button>
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
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Araç Tipi *</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="newSupVehicleType" value="tir" checked class="hidden peer" onchange="toggleSupNewDorse()">
                        <div class="peer-checked:border-orange-500/60 peer-checked:bg-orange-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-orange-400">
                            <i data-lucide="truck" class="h-5 w-5"></i> TIR
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="newSupVehicleType" value="kirkayak" class="hidden peer" onchange="toggleSupNewDorse()">
                        <div class="peer-checked:border-purple-500/60 peer-checked:bg-purple-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-purple-400">
                            <i data-lucide="container" class="h-5 w-5"></i> KIRKAYAK
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="newSupVehicleType" value="kucuk" class="hidden peer" onchange="toggleSupNewDorse()">
                        <div class="peer-checked:border-cyan-500/60 peer-checked:bg-cyan-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-cyan-400">
                            <i data-lucide="car" class="h-5 w-5"></i> KÜÇÜK
                        </div>
                    </label>
                </div>
            </div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Araç Plaka *</label><input type="text" id="newVehiclePlate" placeholder="Örn: 34 ABC 123" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
            <div id="newSupDorseRow"><label class="mb-2 block text-xs font-semibold text-white/70">Dorse Plaka *</label><input type="text" id="newTrailerPlate" placeholder="Örn: 34 ABD 123" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"></div>
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
// TIR: çekicide tank/sayaç/hortum yok, dorsede egzoz/trafik sigortası/takograf yok
const TIR_TRUCK_EXCLUDE = ['tankMuayeneSertifikasi', 'sayacKalibrasyon', 'hortumBasin'];
const TIR_TRAILER_EXCLUDE = ['egzozEmisyon', 'trafikSigortasi', 'takografKalibrasyon'];
const TRAILER_GENERAL_EXCLUDE = ['yetkiBelgesi', 'vergiLevhasi', 'faaliyetBelgesi'];

function createDocs() { return DEFAULT_DOCS.map(d => ({ ...d, fileName: null, fileUrl: null, expiryDate: null })); }
function ensureDocs(docs) {
    const existing = docs || [];
    return DEFAULT_DOCS.map(def => {
        const found = existing.find(d => d.type === def.type);
        return found ? { ...def, ...found } : { ...def, fileName: null, fileUrl: null, expiryDate: null };
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
function countUploaded(docs) { return (docs||[]).filter(d => d.fileName).length; }
function countExpired(docs) { const now = new Date(); return (docs||[]).filter(d => d.expiryDate && new Date(d.expiryDate) < now).length; }

// Varsayılan firmalar
const INITIAL_COMPANIES = [
    { id: 'company_karaburun', name: 'KARABURUN NAKLİYAT', isExpanded: true, vehicles: [
        { id: 'v_kb_1', vehiclePlate: '41 KB 001', trailerPlate: '41 KBD 001', documents: createDocs(), trailerDocuments: createDocs() },
        { id: 'v_kb_2', vehiclePlate: '41 KB 002', trailerPlate: '41 KBD 002', documents: createDocs(), trailerDocuments: createDocs() },
        { id: 'v_kb_3', vehiclePlate: '41 KB 003', trailerPlate: '41 KBD 003', documents: createDocs(), trailerDocuments: createDocs() },
    ]},
    { id: 'company_ozturk', name: 'ÖZTÜRK PETROL TAŞIMACILIĞI', isExpanded: false, vehicles: [
        { id: 'v_oz_1', vehiclePlate: '34 OZT 100', trailerPlate: '34 OZD 100', documents: createDocs(), trailerDocuments: createDocs() },
        { id: 'v_oz_2', vehiclePlate: '34 OZT 101', trailerPlate: '34 OZD 101', documents: createDocs(), trailerDocuments: createDocs() },
    ]},
    { id: 'company_marmara', name: 'MARMARA LOJİSTİK', isExpanded: false, vehicles: [
        { id: 'v_mr_1', vehiclePlate: '16 MRL 500', trailerPlate: '16 MRD 500', documents: createDocs(), trailerDocuments: createDocs() },
    ]},
];

let companies = [];
let panelCompanyId = null, panelVehicleId = null, panelTab = 'truck';
let newVehicleCompanyId = null;
let dataLoadedFromDb = false;
let pendingChanges = { uploads: [], expiryDates: [], deletions: [] };

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const apiData = await apiRequest('/api/supplier-companies');
        if (Array.isArray(apiData)) {
            // DB'de kayıt var — güvenilir veri
            companies = apiData;
            dataLoadedFromDb = true;
        } else if (apiData && apiData._uninitialized) {
            // DB'de hiç kayıt yok — ilk açılış, seed data yükle ve DB'ye kaydet
            const saved = localStorage.getItem('asmira-supplier-companies');
            if (saved) { try { companies = JSON.parse(saved); } catch(e) { companies = [...INITIAL_COMPANIES]; } }
            else { companies = [...INITIAL_COMPANIES]; }
            companies.forEach(c => c.vehicles.forEach(v => { v.documents = ensureDocs(v.documents); v.trailerDocuments = ensureDocs(v.trailerDocuments); }));
            dataLoadedFromDb = true; // İlk seed — DB'ye kaydetmeye izin ver
            saveToStorage();
        }
    } catch(e) {
        // API hata — SADECE localStorage'dan oku, DB'ye YAZMA
        console.warn('Tedarikçi API hatası, offline mod:', e);
        const saved = localStorage.getItem('asmira-supplier-companies');
        if (saved) { try { companies = JSON.parse(saved); } catch(e2) { companies = []; } }
        else { companies = []; }
        dataLoadedFromDb = false; // DB'ye yazma izni YOK
    }
    // Evrakları ensure et
    companies.forEach(c => c.vehicles.forEach(v => {
        v.vehicleType = v.vehicleType || 'tir';
        v.documents = filterDocsForType(ensureDocs(v.documents), v.vehicleType, 'truck');
        v.trailerDocuments = supHasDorse(v.vehicleType) ? filterDocsForType(ensureDocs(v.trailerDocuments), v.vehicleType, 'trailer') : [];
    }));
    if (dataLoadedFromDb) localStorage.setItem('asmira-supplier-companies', JSON.stringify(companies));
    renderAll();
});

async function saveToStorage() {
    const strip = d => ({ ...d, fileUrl: null });
    const toSave = companies.map(c => ({ ...c, vehicles: c.vehicles.map(v => ({ ...v, documents: v.documents.map(strip), trailerDocuments: (v.trailerDocuments||[]).map(strip) })) }));
    localStorage.setItem('asmira-supplier-companies', JSON.stringify(toSave));
    // DB'ye SADECE güvenilir veri varsa yaz — API hatası durumunda DB ezilmesin
    if (!dataLoadedFromDb) { console.warn('DB yazma atlandı — veri DB\'den yüklenmedi'); return; }
    try {
        await apiRequest('/api/supplier-companies', { method: 'PUT', body: JSON.stringify(toSave) });
    } catch(e) { console.warn('DB kaydedilemedi:', e); }
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
                    const noDorse = !supHasDorse(v.vehicleType);
                    const typeColors = { tir: 'orange', kirkayak: 'purple', kucuk: 'cyan' };
                    const typeIcons = { tir: 'truck', kirkayak: 'container', kucuk: 'car' };
                    const typeLabels = { tir: 'TIR', kirkayak: 'KIRKAYAK', kucuk: 'KÜÇÜK' };
                    const tc = typeColors[v.vehicleType] || 'orange';
                    const uploaded = countUploaded(v.documents) + countUploaded(v.trailerDocuments);
                    const total = v.documents.length + (v.trailerDocuments||[]).length;
                    const expiredCount = countExpired(v.documents) + countExpired(v.trailerDocuments);
                    const isComplete = uploaded === total && total > 0;
                    const hasExpired = expiredCount > 0;
                    const progress = total > 0 ? (uploaded / total) * 100 : 0;
                    const borderCls = hasExpired ? 'border-red-500/40' : isComplete ? 'border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]' : `border-${tc}-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]`;
                    const iconCls = hasExpired ? 'from-red-500/25 to-red-600/10 text-red-400' : isComplete ? 'from-emerald-500/25 to-emerald-600/10 text-emerald-400' : `from-${tc}-500/25 to-${tc}-600/10 text-${tc}-400`;
                    const progCls = isComplete ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-amber-500 to-amber-400';
                    let badge = '';
                    if (hasExpired) badge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-red-500/15 px-2.5 py-1 text-[11px] font-semibold text-red-400"><i data-lucide="alert-octagon" class="h-3 w-3"></i>${expiredCount} Süresi Geçmiş</div>`;
                    else if (isComplete) badge = '<div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-400"><i data-lucide="check-circle" class="h-3 w-3"></i>Tüm Evraklar Tamam</div>';
                    else badge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-1 text-[11px] font-semibold text-amber-400"><i data-lucide="alert-triangle" class="h-3 w-3"></i>${total - uploaded} Evrak Eksik</div>`;
                    const typeBadge = `<span class="rounded-full bg-${tc}-500/20 px-2 py-0.5 text-[10px] font-bold text-${tc}-400">${typeLabels[v.vehicleType] || 'TIR'}</span>`;
                    const vehicleIcon = typeIcons[v.vehicleType] || 'truck';

                    return `<div class="group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06] ${borderCls}">
                        <button type="button" onclick="event.stopPropagation();deleteVehicle('${company.id}','${v.id}','${escapeHtml(v.vehiclePlate)}')" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
                        <div class="mb-4 flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] ${iconCls}"><i data-lucide="${vehicleIcon}" class="h-5 w-5"></i></div>
                            <div class="min-w-0 flex-1"><div class="flex items-center gap-2"><span class="truncate text-[15px] font-bold tracking-tight">${escapeHtml(v.vehiclePlate)}</span>${typeBadge}</div>${noDorse ? '' : `<div class="truncate text-xs text-white/50">${escapeHtml(v.trailerPlate)}</div>`}</div>
                        </div>
                        <div class="mb-3"><div class="mb-1.5 flex items-center justify-between text-[11px]"><span class="text-white/60">Evrak Durumu</span><span class="font-semibold ${isComplete?'text-emerald-400':'text-amber-400'}">${uploaded}/${total}</span></div><div class="h-1.5 overflow-hidden rounded-full bg-white/15"><div class="${progCls} h-full rounded-full transition-all duration-500" style="width:${progress}%"></div></div></div>
                        <div class="mb-4">${badge}</div>
                        <div class="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">
                            <button type="button" onclick="openPanel('${company.id}','${v.id}')" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"><i data-lucide="folder-open" class="h-3.5 w-3.5"></i>Evraklar</button>
                            <button type="button" onclick="editVehicle('${company.id}','${v.id}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="Düzenle"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i></button>
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
    if (!dataLoadedFromDb) { showToast('Sunucu bağlantısı yok — değişiklik yapılamaz', 'error'); return; }
    const name = document.getElementById('newCompanyName').value.trim();
    if (!name) { showToast('Lütfen firma adını girin', 'error'); return; }
    companies.unshift({ id: 'company_' + Date.now(), name: name.toUpperCase(), isExpanded: true, vehicles: [] });
    saveToStorage(); renderAll(); closeModal('newCompanyModal'); showToast('Firma eklendi');
}
function deleteCompany(id, name) {
    if (!dataLoadedFromDb) { showToast('Sunucu bağlantısı yok — değişiklik yapılamaz', 'error'); return; }
    if (!confirmAction(`"${name}" firmasını ve tüm araçlarını silmek istediğinize emin misiniz?`)) return;
    companies = companies.filter(c => c.id !== id); saveToStorage(); renderAll(); showToast('Firma silindi');
}

// ===== ARAÇ =====
function supHasDorse(vType) { return vType === 'tir'; }

function toggleSupNewDorse() {
    const vType = document.querySelector('input[name="newSupVehicleType"]:checked').value;
    document.getElementById('newSupDorseRow').style.display = supHasDorse(vType) ? '' : 'none';
}
function openNewVehicleModal(companyId) {
    newVehicleCompanyId = companyId;
    document.getElementById('newVehiclePlate').value = '';
    document.getElementById('newTrailerPlate').value = '';
    document.querySelector('input[name="newSupVehicleType"][value="tir"]').checked = true;
    toggleSupNewDorse();
    openModal('newVehicleModal'); lucide.createIcons();
}
function saveNewVehicle() {
    if (!dataLoadedFromDb) { showToast('Sunucu bağlantısı yok — değişiklik yapılamaz', 'error'); return; }
    const vType = document.querySelector('input[name="newSupVehicleType"]:checked').value;
    const vP = document.getElementById('newVehiclePlate').value.trim();
    const tP = document.getElementById('newTrailerPlate').value.trim();
    if (!vP) { showToast('Araç plakası gerekli', 'error'); return; }
    if (supHasDorse(vType) && !tP) { showToast('TIR için dorse plakası gerekli', 'error'); return; }
    const company = companies.find(c => c.id === newVehicleCompanyId);
    if (!company) return;
    company.vehicles.unshift({
        id: 'v_' + Date.now(), vehiclePlate: vP, trailerPlate: supHasDorse(vType) ? tP : '',
        vehicleType: vType, documents: filterDocsForType(createDocs(), vType, 'truck'), trailerDocuments: supHasDorse(vType) ? filterDocsForType(createDocs(), vType, 'trailer') : []
    });
    saveToStorage(); renderAll(); closeModal('newVehicleModal');
    const typeLabels = { tir: 'Araç/Dorse', kirkayak: 'Kırkayak araç', kucuk: 'Küçük araç' };
    showToast((typeLabels[vType] || 'Araç') + ' eklendi');
}
function deleteVehicle(companyId, vehicleId, plate) {
    if (!dataLoadedFromDb) { showToast('Sunucu bağlantısı yok — değişiklik yapılamaz', 'error'); return; }
    if (!confirmAction(`"${plate}" kaydını silmek istediğinize emin misiniz?`)) return;
    const company = companies.find(c => c.id === companyId);
    if (company) company.vehicles = company.vehicles.filter(v => v.id !== vehicleId);
    saveToStorage(); renderAll(); showToast('Araç silindi');
}

// ===== DÜZENLE =====
function toggleSupEditDorse() {
    const vType = document.querySelector('input[name="editSupVehicleType"]:checked').value;
    document.getElementById('editSupDorseRow').style.display = supHasDorse(vType) ? '' : 'none';
}

function editVehicle(companyId, vehicleId) {
    const c = companies.find(x => x.id === companyId); if (!c) return;
    const v = c.vehicles.find(x => x.id === vehicleId); if (!v) return;
    document.getElementById('editCompanyId').value = companyId;
    document.getElementById('editVehicleId').value = vehicleId;
    document.getElementById('editVehiclePlate').value = v.vehiclePlate;
    document.getElementById('editTrailerPlate').value = v.trailerPlate || '';
    const typeRadio = document.querySelector(`input[name="editSupVehicleType"][value="${v.vehicleType || 'tir'}"]`);
    if (typeRadio) typeRadio.checked = true;
    toggleSupEditDorse();
    openModal('editVehicleModal'); lucide.createIcons();
}
function saveEditVehicle() {
    const cId = document.getElementById('editCompanyId').value;
    const vId = document.getElementById('editVehicleId').value;
    const newType = document.querySelector('input[name="editSupVehicleType"]:checked').value;
    const vP = document.getElementById('editVehiclePlate').value.trim();
    const tP = document.getElementById('editTrailerPlate').value.trim();
    const c = companies.find(x => x.id === cId); if (!c) return;
    const v = c.vehicles.find(x => x.id === vId); if (!v) return;
    const noDorse = !supHasDorse(newType);
    if (!vP) { showToast('Araç plakası gerekli', 'error'); return; }
    if (!noDorse && !tP) { showToast('Dorse plakası gerekli', 'error'); return; }
    v.vehiclePlate = vP;
    v.vehicleType = newType;
    if (noDorse) { v.trailerPlate = ''; v.trailerDocuments = []; }
    else { v.trailerPlate = tP; if (!v.trailerDocuments || v.trailerDocuments.length === 0) v.trailerDocuments = filterDocsForType(createDocs(), newType, 'trailer'); }
    v.documents = filterDocsForType(ensureDocs(v.documents), newType, 'truck');
    if (!noDorse) v.trailerDocuments = filterDocsForType(ensureDocs(v.trailerDocuments), newType, 'trailer');
    saveToStorage(); renderAll(); closeModal('editVehicleModal'); showToast('Araç bilgileri güncellendi');
}

// ===== EVRAK PANELİ =====
function openPanel(companyId, vehicleId) {
    panelCompanyId = companyId; panelVehicleId = vehicleId; panelTab = 'truck';
    pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
    renderPanel(); document.getElementById('docPanel').classList.remove('hidden'); lucide.createIcons();
}
function closePanel() {
    if (hasPendingChanges() && !confirm('Kaydedilmemiş değişiklikler var. Çıkmak istediğinize emin misiniz?')) return;
    document.getElementById('docPanel').classList.add('hidden'); panelCompanyId = null; panelVehicleId = null;
}
function hasPendingChanges() {
    return pendingChanges.uploads.length > 0 || pendingChanges.expiryDates.length > 0 || pendingChanges.deletions.length > 0;
}

function getVehicle() {
    const c = companies.find(x => x.id === panelCompanyId);
    return c ? c.vehicles.find(v => v.id === panelVehicleId) : null;
}

function renderPanel() {
    const v = getVehicle(); if (!v) return;
    const noDorse = !supHasDorse(v.vehicleType);
    if (noDorse) panelTab = 'truck';
    document.getElementById('panelTitle').textContent = panelTab === 'truck' ? v.vehiclePlate : v.trailerPlate;
    const typeColors = { tir: 'orange', kirkayak: 'purple', kucuk: 'cyan' };
    const typeIcons = { tir: 'truck', kirkayak: 'container', kucuk: 'car' };
    const tc = typeColors[v.vehicleType] || 'orange';

    // Tabs
    const tabsEl = document.getElementById('panelTabs');
    if (noDorse) {
        tabsEl.innerHTML = `
            <button type="button" class="flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium border-b-2 border-${tc}-500 text-${tc}-400"><i data-lucide="${typeIcons[v.vehicleType] || 'truck'}" class="h-4 w-4"></i>Araç (${escapeHtml(v.vehiclePlate)})</button>`;
    } else {
        tabsEl.innerHTML = `
            <button type="button" onclick="panelTab='truck';renderPanel();lucide.createIcons()" class="flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium transition-all ${panelTab==='truck' ? 'border-b-2 border-orange-500 text-orange-400' : 'text-white/50 hover:text-white/70'}"><i data-lucide="truck" class="h-4 w-4"></i>Araç (${escapeHtml(v.vehiclePlate)})</button>
            <button type="button" onclick="panelTab='trailer';renderPanel();lucide.createIcons()" class="flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium transition-all ${panelTab==='trailer' ? 'border-b-2 border-cyan-500 text-cyan-400' : 'text-white/50 hover:text-white/70'}"><i data-lucide="container" class="h-4 w-4"></i>Dorse (${escapeHtml(v.trailerPlate)})</button>`;
    }

    const docs = panelTab === 'truck' ? v.documents : v.trailerDocuments;
    const docsEl = document.getElementById('panelDocs');
    docsEl.innerHTML = '<div class="space-y-4">' + docs.map(doc => {
        const pu = pendingChanges.uploads.find(u => u.target === panelTab && u.docType === doc.type);
        const pe = pendingChanges.expiryDates.find(e => e.target === panelTab && e.docType === doc.type);
        const pd = pendingChanges.deletions.find(d => d.target === panelTab && d.docType === doc.type);
        const fileName = pd ? null : (pu ? pu.fileName : doc.fileName);
        const fileUrl = pd ? null : (pu ? pu.fileUrl : doc.fileUrl);
        const expiryDate = pe !== undefined && pe ? pe.date : doc.expiryDate;
        const hasChanges = pu || pe || pd;
        const expired = expiryDate ? new Date(expiryDate) < new Date() : false;
        const daysLeft = expiryDate ? Math.ceil((new Date(expiryDate) - new Date()) / (1000*60*60*24)) : null;
        const cls = hasChanges ? 'border-amber-500/50 bg-amber-500/5' : expired ? 'border-red-500/50 bg-red-500/10' : 'border-white/10 bg-white/5';
        let daysBadge = '';
        if (!hasChanges && daysLeft !== null) {
            if (daysLeft < 0) daysBadge = `<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">SÜRESİ GEÇMİŞ</span>`;
            else if (daysLeft === 0) daysBadge = `<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">BUGÜN</span>`;
            else if (daysLeft <= 15) daysBadge = `<span class="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">${daysLeft} GÜN</span>`;
            else daysBadge = `<span class="ml-2 rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-300">${daysLeft} GÜN</span>`;
        }
        let fileSection = '';
        if (fileName) {
            fileSection = `<div class="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2"><span class="truncate text-sm text-white/70">${escapeHtml(fileName)}</span><div class="flex items-center gap-2">${fileUrl?`<a href="${fileUrl}" target="_blank" class="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10" title="Önizle"><i data-lucide="eye" class="h-4 w-4"></i></a>`:''}<button type="button" onclick="panelDeleteDoc('${doc.type}')" class="inline-flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-4 w-4"></i></button></div></div>`;
        } else {
            fileSection = `<div class="mt-3"><label class="flex items-center justify-center gap-2 rounded-md border border-dashed border-white/20 bg-white/5 px-4 py-3 text-sm hover:bg-white/10 cursor-pointer text-white/50 hover:text-white/70 transition"><i data-lucide="upload" class="h-4 w-4"></i>Dosya sürükleyin veya tıklayın<input type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,.tiff,.doc,.docx" onchange="panelUpload(this,'${doc.type}')"></label></div>`;
        }
        return `<div class="rounded-lg border p-4 ${cls} transition-all" ondragover="event.preventDefault();this.classList.add('ring-2','ring-orange-400','bg-orange-500/10')" ondragenter="event.preventDefault()" ondragleave="this.classList.remove('ring-2','ring-orange-400','bg-orange-500/10')" ondrop="handleSupDocDrop(event,'${doc.type}')">
            <div class="flex items-center gap-2">${fileName?'<i data-lucide="check-circle" class="h-4 w-4 text-emerald-400"></i>':'<div class="h-4 w-4 rounded-full border-2 border-white/30"></div>'}<span class="font-medium">${escapeHtml(doc.label)}</span>${hasChanges ? '<span class="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">DEĞİŞİKLİK</span>' : ''}${daysBadge}</div>
            ${fileSection}
            <div class="mt-3 flex items-center gap-2"><label class="text-xs text-white/50">Son Geçerlilik:</label><input type="date" value="${expiryDate||''}" onchange="panelExpiryChange('${doc.type}',this.value)" style="color-scheme:dark" class="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-orange-500/50">${expiryDate?`<button type="button" onclick="panelExpiryChange('${doc.type}',null)" class="text-xs text-white/40 hover:text-white/60">✕</button>`:''}</div>
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
    _addPendingUpload(file, docType);
}
function handleSupDocDrop(e, docType) {
    e.preventDefault(); e.stopPropagation();
    e.currentTarget.classList.remove('ring-2','ring-orange-400','bg-orange-500/10');
    const file = e.dataTransfer.files[0]; if (!file) return;
    _addPendingUpload(file, docType);
}
function _addPendingUpload(file, docType) {
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

function savePanelChanges() {
    if (!hasPendingChanges() || !panelVehicleId) return;
    const v = getVehicle(); if (!v) return;

    // Apply pending changes to vehicle data
    const applyToLocal = (docs, target) => {
        for (const del of pendingChanges.deletions.filter(d => d.target === target)) {
            const doc = docs.find(d => d.type === del.docType);
            if (doc) { doc.fileName = null; doc.fileUrl = null; doc.expiryDate = null; }
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
    applyToLocal(v.documents, 'truck');
    applyToLocal(v.trailerDocuments, 'trailer');

    const changeCount = pendingChanges.uploads.length + pendingChanges.expiryDates.length + pendingChanges.deletions.length;
    pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
    saveToStorage(); renderAll(); renderPanel();
    showToast(`${changeCount} değişiklik kaydedildi`);
}

function downloadAllDocs() { showToast('PDF birleştirme özelliği yakında eklenecek', 'info'); }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
