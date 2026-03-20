<?php
/**
 * Asmira Ops - Evrak Paketi
 * Orijinal React sayfasının birebir PHP karşılığı
 */
$pageTitle = 'Evrak Paketi';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/document-package';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-emerald-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-emerald-500/60 via-emerald-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK PAKETİ</div>
                <div class="text-3xl font-black tracking-tight">Liman Evrak Oluşturucu</div>
            </div>
            <div class="flex items-center gap-3">
                <div id="selectedBadge" class="hidden items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5">
                    <i data-lucide="check" class="h-4 w-4 text-emerald-400"></i>
                    <span class="text-sm font-semibold text-emerald-400" id="selectedCountText">0 evrak seçili</span>
                </div>
                <button type="button" onclick="handleGeneratePDF()" id="generateBtn" class="inline-flex h-10 items-center gap-2 rounded-lg px-5 text-[13px] font-semibold text-white transition-all bg-white/10 text-white/40 cursor-not-allowed">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    PDF Oluştur
                </button>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="relative flex flex-none items-center gap-3 px-6 py-2.5">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-emerald-500/40 via-emerald-400/20 to-transparent"></div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <i data-lucide="truck" class="h-3.5 w-3.5 text-blue-400"></i>
                <span class="text-xs font-medium text-white/70">Çekici</span>
                <span class="text-sm font-bold text-white" id="truckCount">0</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <i data-lucide="container" class="h-3.5 w-3.5 text-cyan-400"></i>
                <span class="text-xs font-medium text-white/70">Dorse</span>
                <span class="text-sm font-bold text-white" id="trailerCount">0</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <i data-lucide="user-check" class="h-3.5 w-3.5 text-purple-400"></i>
                <span class="text-xs font-medium text-white/70">Şoför</span>
                <span class="text-sm font-bold text-white" id="driverCount">0</span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Araç (Çekici) Seçimi -->
                <div class="rounded-xl border border-blue-500/20 bg-gradient-to-br from-white/[0.04] to-transparent p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-blue-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i data-lucide="truck" class="h-5 w-5 text-blue-400"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">Araç (Çekici) Seçimi</h2>
                            <p class="text-xs text-white/50">Evrak paketine eklenecek çekiciyi seçin</p>
                        </div>
                    </div>
                    <div class="relative mb-4" id="truckDropdownWrap">
                        <button type="button" onclick="toggleDD('truck')" id="truckDDBtn" class="flex h-11 w-full items-center justify-between rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none transition-all hover:border-blue-500/40">
                            <span class="truncate text-white/40" id="truckDDLabel">Çekici seçin...</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div id="truckDDPanel" class="absolute left-0 right-0 top-[calc(100%+4px)] z-50 hidden overflow-hidden rounded-lg border border-white/15 bg-[#0f1a2e] shadow-[0_8px_30px_rgba(0,0,0,0.5)]">
                            <div class="border-b border-white/10 p-2"><div class="relative"><i data-lucide="search" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-white/30"></i><input type="text" id="truckDDSearch" oninput="filterDD('truck')" placeholder="Plaka ara..." class="h-9 w-full rounded-md border border-white/10 bg-white/5 pl-8 pr-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-blue-500/40"></div></div>
                            <div id="truckDDOptions" class="max-h-52 overflow-y-auto p-1"></div>
                        </div>
                    </div>
                    <div id="truckDocsList"></div>
                </div>

                <!-- Dorse Seçimi -->
                <div class="rounded-xl border border-cyan-500/20 bg-gradient-to-br from-white/[0.04] to-transparent p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500/25 to-cyan-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i data-lucide="container" class="h-5 w-5 text-cyan-400"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">Dorse Seçimi</h2>
                            <p class="text-xs text-white/50">Evrak paketine eklenecek dorseyi seçin</p>
                        </div>
                    </div>
                    <div class="relative mb-4" id="trailerDropdownWrap">
                        <button type="button" onclick="toggleDD('trailer')" id="trailerDDBtn" class="flex h-11 w-full items-center justify-between rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none transition-all hover:border-cyan-500/40">
                            <span class="truncate text-white/40" id="trailerDDLabel">Dorse seçin...</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div id="trailerDDPanel" class="absolute left-0 right-0 top-[calc(100%+4px)] z-50 hidden overflow-hidden rounded-lg border border-white/15 bg-[#0f1a2e] shadow-[0_8px_30px_rgba(0,0,0,0.5)]">
                            <div class="border-b border-white/10 p-2"><div class="relative"><i data-lucide="search" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-white/30"></i><input type="text" id="trailerDDSearch" oninput="filterDD('trailer')" placeholder="Plaka ara..." class="h-9 w-full rounded-md border border-white/10 bg-white/5 pl-8 pr-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-cyan-500/40"></div></div>
                            <div id="trailerDDOptions" class="max-h-52 overflow-y-auto p-1"></div>
                        </div>
                    </div>
                    <div id="trailerDocsList"></div>
                </div>

                <!-- Şoför Seçimi -->
                <div class="rounded-xl border border-purple-500/20 bg-gradient-to-br from-white/[0.04] to-transparent p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/25 to-purple-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i data-lucide="user-check" class="h-5 w-5 text-purple-400"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">Şoför Seçimi</h2>
                            <p class="text-xs text-white/50">Evrak paketine eklenecek şoförü seçin</p>
                        </div>
                    </div>
                    <div class="relative mb-4" id="driverDropdownWrap">
                        <button type="button" onclick="toggleDD('driver')" id="driverDDBtn" class="flex h-11 w-full items-center justify-between rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none transition-all hover:border-purple-500/40">
                            <span class="truncate text-white/40" id="driverDDLabel">Şoför seçin...</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div id="driverDDPanel" class="absolute left-0 right-0 top-[calc(100%+4px)] z-50 hidden overflow-hidden rounded-lg border border-white/15 bg-[#0f1a2e] shadow-[0_8px_30px_rgba(0,0,0,0.5)]">
                            <div class="border-b border-white/10 p-2"><div class="relative"><i data-lucide="search" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-white/30"></i><input type="text" id="driverDDSearch" oninput="filterDD('driver')" placeholder="İsim veya TC ara..." class="h-9 w-full rounded-md border border-white/10 bg-white/5 pl-8 pr-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-purple-500/40"></div></div>
                            <div id="driverDDOptions" class="max-h-52 overflow-y-auto p-1"></div>
                        </div>
                    </div>
                    <div id="driverDocsList"></div>
                </div>
            </div>

            <!-- Seçili Evraklar Özet -->
            <div id="summarySection" class="hidden mt-6 rounded-xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/5 to-transparent p-5">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-emerald-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i data-lucide="file-text" class="h-5 w-5 text-emerald-400"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">Seçilen Evraklar</h2>
                            <p class="text-xs text-white/50">PDF'e dahil edilecek evraklar</p>
                        </div>
                    </div>
                    <button type="button" onclick="clearAllSelections()" class="text-xs font-medium text-red-400 hover:text-red-300">Seçimi Temizle</button>
                </div>
                <div class="flex flex-wrap gap-2" id="summaryBadges"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
const { PDFDocument, degrees } = PDFLib;

const DEFAULT_VEHICLE_DOCS = [
    { type: 'ruhsat', label: 'Ruhsat' }, { type: 'tasitKarti', label: 'Taşıt Kartı' }, { type: 't9Adr', label: 'T9 ADR' },
    { type: 'trafikSigortasi', label: 'Trafik Sigortası' }, { type: 'tehlikeliMaddeSigortasi', label: 'Tehlikeli Madde Sigortası' },
    { type: 'kasko', label: 'Kasko' }, { type: 'tuvturk', label: 'TÜVTÜRK' }, { type: 'egzozEmisyon', label: 'Egzoz Emisyon' },
    { type: 'sayacKalibrasyon', label: 'Sayaç Kalibrasyon' }, { type: 'takografKalibrasyon', label: 'Takograf Kalibrasyon' },
    { type: 'faaliyetBelgesi', label: 'Faaliyet Belgesi' }, { type: 'yetkiBelgesi', label: 'Yetki Belgesi' },
    { type: 'hortumBasin', label: 'Hortum Basın.' }, { type: 'tankMuayeneSertifikasi', label: 'Tank Muayene Sertifikası' },
    { type: 'vergiLevhasi', label: 'Vergi Levhası' },
];
const DEFAULT_DRIVER_DOCS = [
    { type: 'kimlik', label: 'Kimlik' }, { type: 'ehliyet', label: 'Ehliyet' }, { type: 'src5', label: 'SRC 5' }, { type: 'psikoteknik', label: 'Psikoteknik' },
    { type: 'adliSicil', label: 'Adli Sicil' }, { type: 'iseGirisBildirge', label: 'İşe Giriş Bildirgesi' },
    { type: 'ikametgah', label: 'İkametgah' }, { type: 'kkdZimmet', label: 'KKD Zimmet' },
    { type: 'saglikMuayene', label: 'Sağlık Muayene' }, { type: 'isgEgitimBelgesi', label: 'İSG Eğitim Belgesi' },
    { type: 'yanginEgitimSertifikasi', label: 'Yangın Eğitim Sertifikası' },
];
function ensureDocs(docs, defaults) {
    const existing = docs || [];
    return defaults.map(def => {
        const found = existing.find(d => d.type === def.type);
        return found ? { ...def, ...found } : { ...def, fileName: null, filePath: null, expiryDate: null };
    });
}

// TIR filtreleme
const TIR_TRUCK_EXCLUDE = ['tankMuayeneSertifikasi', 'sayacKalibrasyon', 'hortumBasin'];
const TIR_TRAILER_EXCLUDE = ['egzozEmisyon', 'trafikSigortasi', 'takografKalibrasyon'];
const TRAILER_GENERAL_EXCLUDE = ['yetkiBelgesi', 'vergiLevhasi', 'faaliyetBelgesi'];
function filterDocsForType(docs, vehicleType, ownerType) {
    let filtered = docs;
    if (ownerType === 'trailer') filtered = filtered.filter(d => !TRAILER_GENERAL_EXCLUDE.includes(d.type));
    if (vehicleType === 'tir') {
        const exclude = ownerType === 'truck' ? TIR_TRUCK_EXCLUDE : TIR_TRAILER_EXCLUDE;
        filtered = filtered.filter(d => !exclude.includes(d.type));
    }
    return filtered;
}

let allTrucks = [], allTrailers = [], allDrivers = [];
let selectedTruckId = null, selectedTrailerId = null, selectedDriverId = null;
let selectedTruckDocs = new Set(), selectedTrailerDocs = new Set(), selectedDriverDocs = new Set();
let ddOpen = null;

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [rawTrucks, rawTrailers, rawDrivers, vehicleSets, supplierCompanies] = await Promise.all([
            apiRequest('/api/trucks'), apiRequest('/api/trailers'), apiRequest('/api/drivers'),
            loadVehicleSetsWithStore(),
            apiRequest('/api/supplier-companies').catch(() => []),
        ]);

        // Asmira araçları (vehicle set üzerinden)
        const asmiraSets = (vehicleSets || []).filter(s => s.category === 'asmira');
        asmiraSets.forEach(set => {
            const vType = set.vehicleType || 'tir';
            const truck = rawTrucks.find(t => t.id === set.truckId);
            if (truck) allTrucks.push({ id: truck.id, plate: truck.plate, documents: truck.documents, source: 'asmira', companyName: 'Asmira', vehicleType: vType });
            if (vType === 'tir' && set.trailerId) {
                const trailer = rawTrailers.find(t => t.id === set.trailerId);
                if (trailer) allTrailers.push({ id: trailer.id, plate: trailer.plate, documents: trailer.documents, source: 'asmira', companyName: 'Asmira', vehicleType: vType });
            }
        });

        // Tedarikçi araçları
        const companies = Array.isArray(supplierCompanies) ? supplierCompanies : [];
        companies.forEach(c => {
            (c.vehicles || []).forEach(v => {
                const vType = v.vehicleType || 'tir';
                allTrucks.push({ id: 'sup_' + c.id + '_' + v.id + '_truck', plate: v.vehiclePlate, documents: v.documents, source: 'supplier', companyName: c.name, vehicleType: vType });
                if (vType === 'tir' && v.trailerPlate) {
                    allTrailers.push({ id: 'sup_' + c.id + '_' + v.id + '_trailer', plate: v.trailerPlate, documents: v.trailerDocuments, source: 'supplier', companyName: c.name, vehicleType: vType });
                }
            });
        });

        // Şoförler
        const asmiraDrivers = rawDrivers.filter(d => (d.category || 'asmira') === 'asmira');
        asmiraDrivers.forEach(d => allDrivers.push({ id: d.id, name: d.name, tcNo: d.tcNo, documents: d.documents, source: 'asmira', companyName: 'Asmira' }));
        const supplierDrivers = rawDrivers.filter(d => d.category === 'supplier');
        supplierDrivers.forEach(d => allDrivers.push({ id: d.id, name: d.name, tcNo: d.tcNo, documents: d.documents, source: 'supplier', companyName: d.companyName || '' }));

        document.getElementById('truckCount').textContent = allTrucks.length;
        document.getElementById('trailerCount').textContent = allTrailers.length;
        document.getElementById('driverCount').textContent = allDrivers.length;
    } catch (e) { showToast('Veriler yüklenemedi', 'error'); }
});

// ===== DROPDOWN =====
document.addEventListener('click', (e) => {
    if (ddOpen && !e.target.closest('#' + ddOpen + 'DropdownWrap')) closeDD();
});
function toggleDD(type) {
    if (ddOpen === type) { closeDD(); return; }
    closeDD(); ddOpen = type;
    document.getElementById(type + 'DDPanel').classList.remove('hidden');
    const s = document.getElementById(type + 'DDSearch'); s.value = ''; s.focus();
    filterDD(type);
    setTimeout(() => document.getElementById(type + 'DropdownWrap').scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
}
function closeDD() {
    if (ddOpen) { document.getElementById(ddOpen + 'DDPanel').classList.add('hidden'); ddOpen = null; }
}
function filterDD(type) {
    const q = document.getElementById(type + 'DDSearch').value.toLowerCase().trim();
    const container = document.getElementById(type + 'DDOptions');
    const items = type === 'truck' ? allTrucks : type === 'trailer' ? allTrailers : allDrivers;
    const filtered = q ? items.filter(i => {
        const text = (type === 'driver' ? i.name : i.plate) + ' ' + (i.companyName || '') + ' ' + (i.tcNo || '');
        return text.toLowerCase().includes(q);
    }) : items;
    const asmira = filtered.filter(i => i.source === 'asmira');
    const supplier = filtered.filter(i => i.source === 'supplier');
    let html = '';
    if (asmira.length > 0) {
        html += '<div class="px-2 py-1.5 text-[10px] font-bold tracking-widest text-emerald-400/60">ASMİRA ÖZMAL</div>';
        asmira.forEach(i => { html += ddOption(type, i); });
    }
    if (supplier.length > 0) {
        html += '<div class="px-2 py-1.5 text-[10px] font-bold tracking-widest text-orange-400/60">TEDARİKÇİ</div>';
        supplier.forEach(i => { html += ddOption(type, i); });
    }
    if (!html) html = '<div class="px-3 py-4 text-center text-xs text-white/30">Sonuç bulunamadı</div>';
    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}
function ddOption(type, item) {
    const selId = type === 'truck' ? selectedTruckId : type === 'trailer' ? selectedTrailerId : selectedDriverId;
    const isSel = selId === item.id;
    const color = item.source === 'asmira' ? 'emerald' : 'orange';
    const label = type === 'driver' ? item.name : item.plate;
    const sub = type === 'driver' ? (item.companyName || '') + (item.tcNo ? ' \u2022 ' + item.tcNo : '') : (item.companyName || '');
    return '<button type="button" onclick="selectDD(\'' + type + '\',\'' + item.id + '\')" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm transition-all ' + (isSel ? 'bg-white/10' : 'hover:bg-white/[0.06]') + '">' +
        '<div class="h-2 w-2 shrink-0 rounded-full bg-' + color + '-500 shadow-[0_0_6px_rgba(' + (color==='emerald'?'52,211,153':'249,115,22') + ',0.5)]"></div>' +
        '<div class="flex-1 min-w-0"><div class="font-medium text-white truncate">' + escapeHtml(label) + '</div>' + (sub ? '<div class="text-xs text-white/40 truncate">' + escapeHtml(sub) + '</div>' : '') + '</div>' +
        (isSel ? '<i data-lucide="check" class="h-4 w-4 shrink-0 text-emerald-400"></i>' : '') +
    '</button>';
}
function selectDD(type, id) {
    if (type === 'truck') { selectedTruckId = id; selectedTruckDocs = new Set(); renderTruckDocs(); }
    else if (type === 'trailer') { selectedTrailerId = id; selectedTrailerDocs = new Set(); renderTrailerDocs(); }
    else { selectedDriverId = id; selectedDriverDocs = new Set(); renderDriverDocs(); }
    const items = type === 'truck' ? allTrucks : type === 'trailer' ? allTrailers : allDrivers;
    const item = items.find(i => i.id === id);
    const label = type === 'driver' ? item?.name : item?.plate;
    const color = item?.source === 'asmira' ? 'emerald' : 'orange';
    const btnLabel = document.getElementById(type + 'DDLabel');
    if (item) { btnLabel.className = 'truncate flex items-center gap-2'; btnLabel.innerHTML = '<span class="h-2 w-2 rounded-full bg-' + color + '-500"></span>' + escapeHtml(label); }
    closeDD(); updateSummary();
}

function getSelectedTruck() { return allTrucks.find(t => t.id === selectedTruckId); }
function getSelectedTrailer() { return allTrailers.find(t => t.id === selectedTrailerId); }
function getSelectedDriver() { return allDrivers.find(d => d.id === selectedDriverId); }

function renderDocChecklist(containerId, docs, selectedSet, toggleFn, selectAllFn, colorClass) {
    const container = document.getElementById(containerId);
    if (!docs || docs.length === 0) { container.innerHTML = ''; return; }
    const selectAllBtn = `<div class="mb-3 flex items-center justify-between"><span class="text-xs font-semibold text-white/60">EVRAKLAR</span><button type="button" onclick="${selectAllFn}()" class="text-xs font-medium ${colorClass} hover:opacity-80">Tümünü Seç</button></div>`;
    const items = docs.map(d => {
        const uploaded = !!d.fileName;
        const selected = selectedSet.has(d.type);
        const borderCls = uploaded ? (selected ? `border-${colorClass.includes('blue')?'blue':''}${colorClass.includes('cyan')?'cyan':''}${colorClass.includes('purple')?'purple':''}-500/50 bg-${colorClass.includes('blue')?'blue':''}${colorClass.includes('cyan')?'cyan':''}${colorClass.includes('purple')?'purple':''}-500/10` : 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/[0.07]') : 'border-white/5 bg-white/[0.02] opacity-50 cursor-not-allowed';
        const selBorder = selected ? colorClass.replace('text-','border-').replace('-400','-500') + ' ' + colorClass.replace('text-','bg-').replace('-400','-500') : 'border-white/30';
        return `<button type="button" onclick="${uploaded ? toggleFn+"('"+d.type+"')" : ''}" ${!uploaded?'disabled':''} class="flex w-full items-center gap-3 rounded-lg border p-3 text-left transition-all ${uploaded ? (selected ? 'border-white/20 bg-white/[0.06]' : 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/[0.07]') : 'border-white/5 bg-white/[0.02] opacity-50 cursor-not-allowed'}">
            <div class="flex h-5 w-5 items-center justify-center rounded border transition-all ${selected ? 'border-emerald-500 bg-emerald-500' : 'border-white/30'}">
                ${selected ? '<i data-lucide="check" class="h-3 w-3 text-white"></i>' : ''}
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium">${escapeHtml(d.label||d.type)}</div>
                ${uploaded ? `<div class="text-xs text-white/40">${escapeHtml(d.fileName)}</div>` : '<div class="flex items-center gap-1 text-xs text-amber-400/70"><i data-lucide="alert-circle" class="h-3 w-3"></i>Yüklenmemiş</div>'}
            </div>
        </button>`;
    }).join('');
    container.innerHTML = selectAllBtn + '<div class="space-y-2">' + items + '</div>';
    lucide.createIcons({nodes:[container]});
}

function renderTruckDocs() {
    const t = getSelectedTruck();
    const docs = t ? filterDocsForType(ensureDocs(t.documents, DEFAULT_VEHICLE_DOCS), t.vehicleType, 'truck') : null;
    renderDocChecklist('truckDocsList', docs, selectedTruckDocs, 'toggleTruckDoc', 'selectAllTruck', 'text-blue-400');
}
function renderTrailerDocs() {
    const t = getSelectedTrailer();
    const docs = t ? filterDocsForType(ensureDocs(t.documents, DEFAULT_VEHICLE_DOCS), t.vehicleType, 'trailer') : null;
    renderDocChecklist('trailerDocsList', docs, selectedTrailerDocs, 'toggleTrailerDoc', 'selectAllTrailer', 'text-cyan-400');
}
function renderDriverDocs() {
    const d = getSelectedDriver();
    renderDocChecklist('driverDocsList', d ? ensureDocs(d.documents, DEFAULT_DRIVER_DOCS) : null, selectedDriverDocs, 'toggleDriverDoc', 'selectAllDriver', 'text-purple-400');
}

function toggleTruckDoc(type) { selectedTruckDocs.has(type) ? selectedTruckDocs.delete(type) : selectedTruckDocs.add(type); renderTruckDocs(); updateSummary(); }
function toggleTrailerDoc(type) { selectedTrailerDocs.has(type) ? selectedTrailerDocs.delete(type) : selectedTrailerDocs.add(type); renderTrailerDocs(); updateSummary(); }
function toggleDriverDoc(type) { selectedDriverDocs.has(type) ? selectedDriverDocs.delete(type) : selectedDriverDocs.add(type); renderDriverDocs(); updateSummary(); }

function selectAllTruck() { const t = getSelectedTruck(); if (!t) return; filterDocsForType(ensureDocs(t.documents, DEFAULT_VEHICLE_DOCS), t.vehicleType, 'truck').filter(d=>d.fileName).forEach(d=>selectedTruckDocs.add(d.type)); renderTruckDocs(); updateSummary(); }
function selectAllTrailer() { const t = getSelectedTrailer(); if (!t) return; filterDocsForType(ensureDocs(t.documents, DEFAULT_VEHICLE_DOCS), t.vehicleType, 'trailer').filter(d=>d.fileName).forEach(d=>selectedTrailerDocs.add(d.type)); renderTrailerDocs(); updateSummary(); }
function selectAllDriver() { const d = getSelectedDriver(); if (!d) return; ensureDocs(d.documents, DEFAULT_DRIVER_DOCS).filter(dd=>dd.fileName).forEach(dd=>selectedDriverDocs.add(dd.type)); renderDriverDocs(); updateSummary(); }

function clearAllSelections() { selectedTruckDocs=new Set(); selectedTrailerDocs=new Set(); selectedDriverDocs=new Set(); renderTruckDocs(); renderTrailerDocs(); renderDriverDocs(); updateSummary(); }

function updateSummary() {
    const total = selectedTruckDocs.size + selectedTrailerDocs.size + selectedDriverDocs.size;
    const badge = document.getElementById('selectedBadge');
    const btn = document.getElementById('generateBtn');
    const summary = document.getElementById('summarySection');

    if (total > 0) {
        badge.classList.remove('hidden'); badge.classList.add('flex');
        document.getElementById('selectedCountText').textContent = total + ' evrak seçili';
        btn.className = 'inline-flex h-10 items-center gap-2 rounded-lg px-5 text-[13px] font-semibold text-white transition-all bg-gradient-to-br from-emerald-600 to-emerald-700 shadow-[0_2px_10px_rgba(52,211,153,0.25)] hover:from-emerald-500 hover:to-emerald-600';
        summary.classList.remove('hidden');
    } else {
        badge.classList.add('hidden'); badge.classList.remove('flex');
        btn.className = 'inline-flex h-10 items-center gap-2 rounded-lg px-5 text-[13px] font-semibold text-white transition-all bg-white/10 text-white/40 cursor-not-allowed';
        summary.classList.add('hidden');
    }

    let badges = '';
    const truck = getSelectedTruck();
    if (truck) { const tDocs = filterDocsForType(ensureDocs(truck.documents, DEFAULT_VEHICLE_DOCS), truck.vehicleType, 'truck'); selectedTruckDocs.forEach(type => { const d = tDocs.find(dd=>dd.type===type); if(d) badges += `<div class="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1.5"><i data-lucide="truck" class="h-3 w-3 text-blue-400"></i><span class="text-xs font-medium text-blue-300">${escapeHtml(d.label)}</span></div>`; }); }
    const trailer = getSelectedTrailer();
    if (trailer) { const trDocs = filterDocsForType(ensureDocs(trailer.documents, DEFAULT_VEHICLE_DOCS), trailer.vehicleType, 'trailer'); selectedTrailerDocs.forEach(type => { const d = trDocs.find(dd=>dd.type===type); if(d) badges += `<div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1.5"><i data-lucide="container" class="h-3 w-3 text-cyan-400"></i><span class="text-xs font-medium text-cyan-300">${escapeHtml(d.label)}</span></div>`; }); }
    const driver = getSelectedDriver();
    if (driver) { const drDocs = ensureDocs(driver.documents, DEFAULT_DRIVER_DOCS); selectedDriverDocs.forEach(type => { const d = drDocs.find(dd=>dd.type===type); if(d) badges += `<div class="inline-flex items-center gap-2 rounded-full border border-purple-500/30 bg-purple-500/10 px-3 py-1.5"><i data-lucide="user-check" class="h-3 w-3 text-purple-400"></i><span class="text-xs font-medium text-purple-300">${escapeHtml(d.label)}</span></div>`; }); }
    document.getElementById('summaryBadges').innerHTML = badges;
    lucide.createIcons({nodes:[document.getElementById('summaryBadges')]});
}

async function handleGeneratePDF() {
    const total = selectedTruckDocs.size + selectedTrailerDocs.size + selectedDriverDocs.size;
    if (total === 0) { showToast('Lütfen en az bir evrak seçin', 'error'); return; }

    const btn = document.getElementById('generateBtn');
    const origHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner" style="width:16px;height:16px;border-width:2px;"></div> PDF Hazırlanıyor...';

    try {
        // Seçili evrakların dosya yollarını topla
        const filesToMerge = [];
        const truck = getSelectedTruck();
        if (truck) filterDocsForType(ensureDocs(truck.documents, DEFAULT_VEHICLE_DOCS), truck.vehicleType, 'truck').filter(d => selectedTruckDocs.has(d.type) && d.filePath).forEach(d => filesToMerge.push({ label: d.label, path: d.filePath, fileName: d.fileName }));
        const trailer = getSelectedTrailer();
        if (trailer) filterDocsForType(ensureDocs(trailer.documents, DEFAULT_VEHICLE_DOCS), trailer.vehicleType, 'trailer').filter(d => selectedTrailerDocs.has(d.type) && d.filePath).forEach(d => filesToMerge.push({ label: d.label, path: d.filePath, fileName: d.fileName }));
        const driver = getSelectedDriver();
        if (driver) ensureDocs(driver.documents, DEFAULT_DRIVER_DOCS).filter(d => selectedDriverDocs.has(d.type) && d.filePath).forEach(d => filesToMerge.push({ label: d.label, path: d.filePath, fileName: d.fileName }));

        if (filesToMerge.length === 0) {
            showToast('Seçili evraklarda dosya bulunamadı', 'error');
            return;
        }

        const mergedPdf = await PDFDocument.create();

        for (const file of filesToMerge) {
            try {
                const url = '/api/documents/download/' + file.path.split('/').map(s => encodeURIComponent(s)).join('/');
                const response = await fetch(url);
                if (!response.ok) { console.warn('Dosya indirilemedi:', file.path); continue; }
                const arrayBuffer = await response.arrayBuffer();
                const ext = (file.fileName || file.path).split('.').pop().toLowerCase();

                if (ext === 'pdf') {
                    const srcPdf = await PDFDocument.load(arrayBuffer, { ignoreEncryption: true });
                    const pages = await mergedPdf.copyPages(srcPdf, srcPdf.getPageIndices());
                    pages.forEach(p => mergedPdf.addPage(p));
                } else if (['jpg', 'jpeg'].includes(ext)) {
                    const img = await mergedPdf.embedJpg(arrayBuffer);
                    const dims = img.scaleToFit(595, 842); // A4
                    const page = mergedPdf.addPage([595, 842]);
                    page.drawImage(img, {
                        x: (595 - dims.width) / 2,
                        y: (842 - dims.height) / 2,
                        width: dims.width,
                        height: dims.height,
                    });
                } else if (ext === 'png') {
                    const img = await mergedPdf.embedPng(arrayBuffer);
                    const dims = img.scaleToFit(595, 842); // A4
                    const page = mergedPdf.addPage([595, 842]);
                    page.drawImage(img, {
                        x: (595 - dims.width) / 2,
                        y: (842 - dims.height) / 2,
                        width: dims.width,
                        height: dims.height,
                    });
                } else {
                    console.warn('Desteklenmeyen dosya türü:', ext, file.path);
                }
            } catch (fileErr) {
                console.error('Dosya işleme hatası:', file.path, fileErr);
            }
        }

        if (mergedPdf.getPageCount() === 0) {
            showToast('Birleştirilebilecek dosya bulunamadı', 'error');
            return;
        }

        const pdfBytes = await mergedPdf.save();
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const link = document.createElement('a');
        const truckPlate = truck?.plate || '';
        const trailerPlate = trailer?.plate || '';
        const driverName = driver?.name || '';
        const datePart = new Date().toISOString().slice(0, 10);
        link.href = URL.createObjectURL(blob);
        link.download = `Evrak_Paketi_${truckPlate}_${datePart}.pdf`.replace(/\s+/g, '_');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);

        showToast(`${mergedPdf.getPageCount()} sayfa birleştirildi ve indirildi`, 'success');
    } catch (e) {
        console.error('PDF oluşturma hatası:', e);
        showToast('PDF oluşturulamadı: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = origHTML;
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
