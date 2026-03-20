<?php
/**
 * Asmira Ops - Evrak Takibi Raporu
 */
$pageTitle = 'Evrak Takibi';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/reports/document-tracking';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <!-- Top Header -->
    <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 rounded-t-2xl border border-b-0 border-white/10 bg-white/[0.02] bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
        <div>
            <div class="text-sm font-light tracking-[0.2em] text-slate-400">RAPORLAR</div>
            <div class="text-3xl font-black tracking-tight text-white">Evrak Takibi</div>
            <div class="mt-1 text-xs text-white/50">Süresi dolan ve dolacak evrakların özeti</div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid min-h-0 flex-1 grid-cols-1 gap-0 lg:grid-cols-2">

        <!-- LEFT: Asmira Özmal -->
        <div class="flex flex-col overflow-auto rounded-bl-2xl border border-white/10 bg-white/[0.02] text-white">
            <div class="relative flex items-center gap-2 bg-gradient-to-r from-blue-500/10 to-transparent px-4 py-3">
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/30 to-transparent"></div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400"><i data-lucide="shield" class="h-4 w-4"></i></div>
                <div><div class="text-sm font-bold text-blue-400">Asmira Özmal</div><div class="text-xs text-white/40">Araç, Dorse & Şoför Evrakları</div></div>
            </div>
            <div class="flex-1 overflow-y-auto p-3 sm:p-4">
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <button onclick="filterDocs('left','all')" class="left-filter-btn rounded-lg border border-blue-500/30 bg-blue-500/20 px-3 py-1.5 text-xs font-medium text-blue-400 transition" id="left-filter-all">Tümü</button>
                    <button onclick="filterDocs('left','expired')" class="left-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="left-filter-expired">Dolmuş</button>
                    <button onclick="filterDocs('left','warning')" class="left-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="left-filter-warning">15 Gün</button>
                    <button onclick="filterDocs('left','ok')" class="left-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="left-filter-ok">Geçerli</button>
                    <button onclick="filterDocs('left','missing')" class="left-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="left-filter-missing">Eksik</button>
                </div>
                <div class="grid grid-cols-5 gap-2 mb-4">
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-white" id="leftStatTotal">-</div><div class="text-[11px] text-white/40">Toplam</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-red-400" id="leftStatExpired">-</div><div class="text-[11px] text-white/40">Dolmuş</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-yellow-400" id="leftStatWarning">-</div><div class="text-[11px] text-white/40">15 Gün</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-emerald-400" id="leftStatOk">-</div><div class="text-[11px] text-white/40">Geçerli</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-white/40" id="leftStatMissing">-</div><div class="text-[11px] text-white/40">Eksik</div></div>
                </div>
                <div id="leftAccordion" class="space-y-2"></div>
            </div>
        </div>

        <!-- RIGHT: Tedarikçi Araçları -->
        <div class="flex flex-col overflow-auto rounded-br-2xl border border-l-0 border-white/10 bg-white/[0.02] text-white lg:border-l-0">
            <div class="relative flex items-center gap-2 bg-gradient-to-r from-orange-500/10 to-transparent px-4 py-3">
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/30 to-transparent"></div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500/20 text-orange-400"><i data-lucide="building-2" class="h-4 w-4"></i></div>
                <div><div class="text-sm font-bold text-orange-400">Tedarikçi Araçları</div><div class="text-xs text-white/40">Tedarikçi Firma Evrakları</div></div>
            </div>
            <div class="flex-1 overflow-y-auto p-3 sm:p-4">
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <button onclick="filterDocs('right','all')" class="right-filter-btn rounded-lg border border-orange-500/30 bg-orange-500/20 px-3 py-1.5 text-xs font-medium text-orange-400 transition" id="right-filter-all">Tümü</button>
                    <button onclick="filterDocs('right','expired')" class="right-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="right-filter-expired">Dolmuş</button>
                    <button onclick="filterDocs('right','warning')" class="right-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="right-filter-warning">15 Gün</button>
                    <button onclick="filterDocs('right','ok')" class="right-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="right-filter-ok">Geçerli</button>
                    <button onclick="filterDocs('right','missing')" class="right-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="right-filter-missing">Eksik</button>
                </div>
                <div class="grid grid-cols-5 gap-2 mb-4">
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-white" id="rightStatTotal">-</div><div class="text-[11px] text-white/40">Toplam</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-red-400" id="rightStatExpired">-</div><div class="text-[11px] text-white/40">Dolmuş</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-yellow-400" id="rightStatWarning">-</div><div class="text-[11px] text-white/40">15 Gün</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-emerald-400" id="rightStatOk">-</div><div class="text-[11px] text-white/40">Geçerli</div></div>
                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-2 text-center"><div class="text-lg font-bold text-white/40" id="rightStatMissing">-</div><div class="text-[11px] text-white/40">Eksik</div></div>
                </div>
                <div id="rightAccordion" class="space-y-2"></div>
            </div>
        </div>

    </div>
</div>

<script>
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
    { type: 'ikametgah', label: 'İkametgah' }, { type: 'kkdZimmet', label: 'KKD Zimmet Tutanağı' },
    { type: 'saglikMuayene', label: 'Sağlık Muayene' }, { type: 'isgEgitimBelgesi', label: 'İSG Eğitim Belgesi' },
    { type: 'yanginEgitimSertifikasi', label: 'Yangın Eğitim Sertifikası' },
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

// Panel state
let leftDocs = [], leftVehicleDocs = [], leftDriverDocs = [], rightDocs = [], rightCompanyData = [];
let leftFilter = 'all', rightFilter = 'all';
let expandedLeft = new Set(), expandedRight = new Set(), expandedCompanies = new Set();
let leftSectionOpen = { vehicles: false, drivers: false };

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [allTrucks, allTrailers, drivers, vehicleSets, supplierCompanies] = await Promise.all([
            loadTrucksWithStore(), loadTrailersWithStore(), loadDriversWithStore(),
            loadVehicleSetsWithStore(),
            apiRequest('/api/supplier-companies').catch(() => []),
        ]);

        // LEFT: Asmira Özmal — araç evraklarını vehicle set üzerinden oluştur (tip bilgisi orada)
        const asmiraSets = (vehicleSets || []).filter(s => s.category === 'asmira');
        leftVehicleDocs = []; leftDriverDocs = [];
        asmiraSets.forEach(set => {
            const vType = set.vehicleType || 'tir';
            const truck = allTrucks.find(t => t.id === set.truckId);
            if (truck) {
                filterDocsForType(ensureDocs(truck.documents, DEFAULT_VEHICLE_DOCS), vType, 'truck')
                    .forEach(d => leftVehicleDocs.push({ owner: truck.plate, ownerType: 'Çekici', ...d }));
            }
            if (vType === 'tir' && set.trailerId) {
                const trailer = allTrailers.find(t => t.id === set.trailerId);
                if (trailer) {
                    filterDocsForType(ensureDocs(trailer.documents, DEFAULT_VEHICLE_DOCS), vType, 'trailer')
                        .forEach(d => leftVehicleDocs.push({ owner: trailer.plate, ownerType: 'Dorse', ...d }));
                }
            }
        });
        // Sadece asmira şoförleri
        const asmiraDrivers = drivers.filter(d => (d.category || 'asmira') === 'asmira');
        asmiraDrivers.forEach(dr => ensureDocs(dr.documents, DEFAULT_DRIVER_DOCS).forEach(d => leftDriverDocs.push({ owner: dr.name, ownerType: 'Şoför', ...d })));
        leftDocs = [...leftVehicleDocs, ...leftDriverDocs];

        // RIGHT: Tedarikçi Araçları (firma bazlı yapı — TIR filtreleme dahil)
        rightDocs = []; rightCompanyData = [];
        const companies = Array.isArray(supplierCompanies) ? supplierCompanies : [];
        companies.forEach(c => {
            const cDocs = [];
            (c.vehicles || []).forEach(v => {
                const vType = v.vehicleType || 'tir';
                filterDocsForType(ensureDocs(v.documents, DEFAULT_VEHICLE_DOCS), vType, 'truck')
                    .forEach(d => { const doc = { owner: v.vehiclePlate, ownerType: 'Çekici', ...d }; cDocs.push(doc); rightDocs.push(doc); });
                if (vType === 'tir' && v.trailerPlate) {
                    filterDocsForType(ensureDocs(v.trailerDocuments, DEFAULT_VEHICLE_DOCS), vType, 'trailer')
                        .forEach(d => { const doc = { owner: v.trailerPlate, ownerType: 'Dorse', ...d }; cDocs.push(doc); rightDocs.push(doc); });
                }
            });
            rightCompanyData.push({ id: c.id, name: c.name, vCount: (c.vehicles||[]).length, docs: cDocs });
        });

        updateStats('left'); updateStats('right');
        renderPanel('left'); renderPanel('right');
    } catch (e) { console.error(e); showToast('Veriler yüklenemedi', 'error'); }
});

function getStatus(doc) {
    if (!doc.fileName) return 'missing';
    if (!doc.expiryDate) return 'ok';
    const diff = Math.ceil((new Date(doc.expiryDate) - new Date()) / (1000*60*60*24));
    if (diff < 0) return 'expired';
    if (diff <= 15) return 'warning';
    return 'ok';
}

function updateStats(side) {
    const docs = side === 'left' ? leftDocs : rightDocs;
    const stats = { total: docs.length, expired: 0, warning: 0, ok: 0, missing: 0 };
    docs.forEach(d => stats[getStatus(d)]++);
    document.getElementById(side + 'StatTotal').textContent = stats.total;
    document.getElementById(side + 'StatExpired').textContent = stats.expired;
    document.getElementById(side + 'StatWarning').textContent = stats.warning;
    document.getElementById(side + 'StatOk').textContent = stats.ok;
    document.getElementById(side + 'StatMissing').textContent = stats.missing;
}

function daysUntil(dateStr) {
    const today = new Date(); today.setHours(0,0,0,0);
    const target = new Date(dateStr); target.setHours(0,0,0,0);
    return Math.ceil((target - today) / (1000*60*60*24));
}

function filterDocs(side, filter) {
    if (side === 'left') leftFilter = filter; else rightFilter = filter;
    const activeCls = side === 'left'
        ? 'rounded-lg border border-blue-500/30 bg-blue-500/20 px-3 py-1.5 text-xs font-medium text-blue-400 transition'
        : 'rounded-lg border border-orange-500/30 bg-orange-500/20 px-3 py-1.5 text-xs font-medium text-orange-400 transition';
    document.querySelectorAll('.' + side + '-filter-btn').forEach(b => {
        b.className = side + '-filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10';
    });
    document.getElementById(side + '-filter-' + filter).className = side + '-filter-btn ' + activeCls;
    renderPanel(side);
}

function getBadge(status, days) {
    if (status === 'expired') return `<span class="inline-flex items-center gap-1 rounded-full bg-red-500/20 px-2 py-0.5 text-xs font-semibold text-red-400"><i data-lucide="alert-triangle" class="h-3.5 w-3.5"></i>${days !== null ? Math.abs(days) + 'g' : 'Dolmuş'}</span>`;
    if (status === 'warning') { const cls = days <= 7 ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400'; return `<span class="inline-flex items-center gap-1 rounded-full ${cls} px-2 py-0.5 text-xs font-semibold"><i data-lucide="clock" class="h-3.5 w-3.5"></i>${days}g</span>`; }
    if (status === 'ok') return '<span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs font-semibold text-emerald-400">Geçerli</span>';
    return '<span class="inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-xs font-semibold text-white/40">Eksik</span>';
}

function getTypeIcon(ownerType) {
    if (ownerType === 'Çekici') return '<i data-lucide="truck" class="h-4 w-4 text-blue-400"></i>';
    if (ownerType === 'Dorse') return '<i data-lucide="container" class="h-4 w-4 text-cyan-400"></i>';
    return '<i data-lucide="user" class="h-4 w-4 text-purple-400"></i>';
}

function toggleGroup(side, key) {
    const set = side === 'left' ? expandedLeft : expandedRight;
    if (set.has(key)) set.delete(key); else set.add(key);
    renderPanel(side);
}
function toggleCompany(key) {
    if (expandedCompanies.has(key)) expandedCompanies.delete(key); else expandedCompanies.add(key);
    renderPanel('right');
}

function renderPanel(side) {
    if (side === 'right') { renderRightPanel(); return; }
    renderLeftPanel();
}

function buildBadges(stats) {
    let b = '';
    if (stats.expired > 0) b += `<span class="rounded-full bg-red-500/20 px-2 py-0.5 text-[11px] font-bold text-red-400">${stats.expired} dolmuş</span>`;
    if (stats.warning > 0) b += `<span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[11px] font-bold text-amber-400">${stats.warning} yakın</span>`;
    if (stats.ok > 0) b += `<span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-bold text-emerald-400">${stats.ok} geçerli</span>`;
    if (stats.missing > 0) b += `<span class="rounded-full bg-white/10 px-2 py-0.5 text-[11px] font-bold text-white/40">${stats.missing} eksik</span>`;
    return b;
}

function calcStats(docs) {
    const s = { expired: 0, warning: 0, ok: 0, missing: 0 };
    docs.forEach(d => s[getStatus(d)]++);
    return s;
}

function borderForStats(stats, total) {
    if (stats.expired > 0) return 'border-red-500/30';
    if (stats.warning > 0) return 'border-amber-500/30';
    if (stats.ok === total) return 'border-emerald-500/30';
    return 'border-white/10';
}

function renderDocRows(docs) {
    return docs.map(d => {
        const st = getStatus(d);
        const days = d.expiryDate ? daysUntil(d.expiryDate) : null;
        const rowBg = st === 'expired' ? 'bg-red-500/[0.04]' : st === 'warning' ? 'bg-amber-500/[0.04]' : '';
        return `<div class="flex items-center justify-between rounded-lg px-3 py-2 text-sm ${rowBg}">
            <span class="text-white/70 w-2/5 truncate">${escapeHtml(d.label || d.type)}</span>
            <span class="text-white/40 w-1/4 text-center">${d.expiryDate || '-'}</span>
            <span class="w-1/3 text-right">${getBadge(st, days)}</span>
        </div>`;
    }).join('');
}

function renderOwnerGroup(group, expanded, side) {
    const key = group.owner + '|' + group.ownerType;
    const isOpen = expanded.has(key);
    const stats = calcStats(group.docs);
    const uploaded = group.docs.filter(d => d.fileName).length;
    const total = group.docs.length;
    const pctOk = total > 0 ? (stats.ok / total * 100) : 0;
    const pctWarn = total > 0 ? (stats.warning / total * 100) : 0;
    const pctExp = total > 0 ? (stats.expired / total * 100) : 0;
    const safeKey = key.replace(/'/g, "\\'");
    let rows = isOpen ? `<div class="border-t border-white/5 mt-2 pt-2 space-y-1">${renderDocRows(group.docs)}</div>` : '';
    return `<div class="rounded-xl border ${borderForStats(stats, total)} bg-white/[0.02] overflow-hidden transition-all">
        <div class="flex items-center gap-2 px-3 py-2.5 cursor-pointer hover:bg-white/[0.03] transition select-none" onclick="toggleGroup('${side}','${safeKey}')">
            ${getTypeIcon(group.ownerType)}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-sm font-bold text-white truncate">${escapeHtml(group.owner)}</span>
                    <span class="text-[11px] text-white/40 font-medium">${group.ownerType}</span>
                    <span class="text-[11px] text-white/30">${uploaded}/${total} yüklü</span>
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <div class="flex-1 h-1 rounded-full bg-white/5 overflow-hidden max-w-[140px]">
                        <div class="h-full flex">
                            <div class="bg-red-500 h-full" style="width:${pctExp}%"></div>
                            <div class="bg-amber-500 h-full" style="width:${pctWarn}%"></div>
                            <div class="bg-emerald-500 h-full" style="width:${pctOk}%"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap">${buildBadges(stats)}</div>
                </div>
            </div>
            <i data-lucide="${isOpen ? 'chevron-up' : 'chevron-down'}" class="h-3.5 w-3.5 text-white/30 flex-shrink-0"></i>
        </div>
        ${rows}
    </div>`;
}

function groupAndSort(filtered) {
    const groups = new Map();
    filtered.forEach(d => {
        const key = d.owner + '|' + d.ownerType;
        if (!groups.has(key)) groups.set(key, { owner: d.owner, ownerType: d.ownerType, docs: [] });
        groups.get(key).docs.push(d);
    });
    return [...groups.values()].sort((a, b) => {
        const sA = a.docs.filter(d => getStatus(d)==='expired').length*100 + a.docs.filter(d => getStatus(d)==='warning').length*10;
        const sB = b.docs.filter(d => getStatus(d)==='expired').length*100 + b.docs.filter(d => getStatus(d)==='warning').length*10;
        return sB - sA;
    });
}

function toggleLeftSection(section) {
    leftSectionOpen[section] = !leftSectionOpen[section];
    renderLeftPanel();
}

function renderSectionHeader(title, icon, color, section, docs) {
    const isOpen = leftSectionOpen[section];
    const stats = calcStats(docs);
    const total = docs.length;
    const uploaded = docs.filter(d => d.fileName).length;
    const pOk = total > 0 ? (stats.ok/total*100) : 0;
    const pWarn = total > 0 ? (stats.warning/total*100) : 0;
    const pExp = total > 0 ? (stats.expired/total*100) : 0;
    return `<div class="flex items-center gap-3 px-4 py-3.5 cursor-pointer rounded-xl border-2 ${borderForStats(stats, total)} bg-gradient-to-r from-${color}-500/10 to-transparent hover:from-${color}-500/15 transition select-none" onclick="toggleLeftSection('${section}')">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-${color}-500/20 text-${color}-400 shadow-lg shadow-${color}-500/10"><i data-lucide="${icon}" class="h-5 w-5"></i></div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <span class="text-base font-extrabold text-${color}-400">${title}</span>
                <span class="text-xs text-white/40 font-medium">${uploaded}/${total} yüklü</span>
            </div>
            <div class="flex items-center gap-1.5 mt-1.5">
                <div class="flex-1 h-1.5 rounded-full bg-white/5 overflow-hidden max-w-[160px]">
                    <div class="h-full flex">
                        <div class="bg-red-500 h-full" style="width:${pExp}%"></div>
                        <div class="bg-amber-500 h-full" style="width:${pWarn}%"></div>
                        <div class="bg-emerald-500 h-full" style="width:${pOk}%"></div>
                    </div>
                </div>
                <div class="flex items-center gap-1 flex-wrap">${buildBadges(stats)}</div>
            </div>
        </div>
        <i data-lucide="${isOpen ? 'chevron-up' : 'chevron-down'}" class="h-5 w-5 text-${color}-400/50 flex-shrink-0"></i>
    </div>`;
}

function renderLeftPanel() {
    const container = document.getElementById('leftAccordion');
    const vFiltered = leftFilter === 'all' ? leftVehicleDocs : leftVehicleDocs.filter(d => getStatus(d) === leftFilter);
    const dFiltered = leftFilter === 'all' ? leftDriverDocs : leftDriverDocs.filter(d => getStatus(d) === leftFilter);

    let html = '';

    // Araçlar section
    html += renderSectionHeader('Araçlar', 'truck', 'blue', 'vehicles', leftVehicleDocs);
    if (leftSectionOpen.vehicles) {
        if (vFiltered.length === 0) {
            html += `<div class="rounded-xl border border-white/10 bg-white/[0.02] p-4 text-center text-sm text-white/40 mt-2">Bu filtrede araç evrakı yok</div>`;
        } else {
            const sorted = groupAndSort(vFiltered);
            html += `<div class="space-y-2 mt-2">${sorted.map(g => renderOwnerGroup(g, expandedLeft, 'left')).join('')}</div>`;
        }
    }

    html += '<div class="my-3"></div>';

    // Şoförler section
    html += renderSectionHeader('Şoförler', 'users', 'purple', 'drivers', leftDriverDocs);
    if (leftSectionOpen.drivers) {
        if (dFiltered.length === 0) {
            html += `<div class="rounded-xl border border-white/10 bg-white/[0.02] p-4 text-center text-sm text-white/40 mt-2">Bu filtrede şoför evrakı yok</div>`;
        } else {
            const sorted = groupAndSort(dFiltered);
            html += `<div class="space-y-2 mt-2">${sorted.map(g => renderOwnerGroup(g, expandedLeft, 'left')).join('')}</div>`;
        }
    }

    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}

function renderRightPanel() {
    const container = document.getElementById('rightAccordion');
    if (rightCompanyData.length === 0) {
        container.innerHTML = `<div class="rounded-xl border border-white/10 bg-white/[0.02] p-6 text-center"><div class="flex flex-col items-center"><i data-lucide="building-2" class="mb-2 h-6 w-6 text-orange-400/30"></i><div class="text-sm text-white/40">Tedarikçi firması bulunamadı</div></div></div>`;
        lucide.createIcons({nodes:[container]}); return;
    }
    let html = '';
    rightCompanyData.forEach(company => {
        const cFiltered = rightFilter === 'all' ? company.docs : company.docs.filter(d => getStatus(d) === rightFilter);
        if (cFiltered.length === 0 && rightFilter !== 'all') return;
        const cKey = 'c_' + company.id;
        const isOpen = expandedCompanies.has(cKey);
        const cStats = calcStats(company.docs);
        const cTotal = company.docs.length;
        const cUploaded = company.docs.filter(d => d.fileName).length;
        const pOk = cTotal > 0 ? (cStats.ok/cTotal*100) : 0;
        const pWarn = cTotal > 0 ? (cStats.warning/cTotal*100) : 0;
        const pExp = cTotal > 0 ? (cStats.expired/cTotal*100) : 0;
        const safeCKey = cKey.replace(/'/g, "\\'");

        let inner = '';
        if (isOpen) {
            if (cFiltered.length === 0) {
                inner = '<div class="px-4 py-3 text-sm text-white/30 text-center">Bu filtrede evrak yok</div>';
            } else {
                const sorted = groupAndSort(cFiltered);
                inner = `<div class="px-3 pb-3 space-y-2">${sorted.map(g => renderOwnerGroup(g, expandedRight, 'right')).join('')}</div>`;
            }
        }

        html += `<div class="rounded-xl border-2 ${borderForStats(cStats, cTotal)} bg-white/[0.02] overflow-hidden transition-all">
            <div class="flex items-center gap-3 px-4 py-3.5 cursor-pointer bg-gradient-to-r from-orange-500/10 to-transparent hover:from-orange-500/15 transition select-none" onclick="toggleCompany('${safeCKey}')">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-500/20 text-orange-400 shadow-lg shadow-orange-500/10"><i data-lucide="building-2" class="h-5 w-5"></i></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base font-extrabold text-white truncate">${escapeHtml(company.name)}</span>
                        <span class="text-xs text-white/40 font-medium">${company.vCount} araç</span>
                        <span class="text-xs text-white/30">${cUploaded}/${cTotal}</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <div class="flex-1 h-1.5 rounded-full bg-white/5 overflow-hidden max-w-[160px]">
                            <div class="h-full flex">
                                <div class="bg-red-500 h-full" style="width:${pExp}%"></div>
                                <div class="bg-amber-500 h-full" style="width:${pWarn}%"></div>
                                <div class="bg-emerald-500 h-full" style="width:${pOk}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 flex-wrap">${buildBadges(cStats)}</div>
                    </div>
                </div>
                <i data-lucide="${isOpen ? 'chevron-up' : 'chevron-down'}" class="h-5 w-5 text-orange-400/50 flex-shrink-0"></i>
            </div>
            ${inner}
        </div>`;
    });
    container.innerHTML = html || `<div class="rounded-xl border border-white/10 bg-white/[0.02] p-6 text-center"><div class="text-sm text-white/40">Bu filtrede evrak yok</div></div>`;
    lucide.createIcons({nodes:[container]});
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
