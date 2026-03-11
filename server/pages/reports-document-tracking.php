<?php
/**
 * Asmira Ops - Evrak Takibi Raporu
 */
$pageTitle = 'Evrak Takibi';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/reports/document-tracking';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-auto rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">RAPORLAR</div>
                <div class="text-3xl font-black tracking-tight">Evrak Takibi</div>
                <div class="mt-1 text-xs text-white/50">Süresi dolan ve dolacak evrakların özeti</div>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <!-- Filter -->
            <div class="flex flex-wrap gap-2 mb-6">
                <button onclick="filterDocs('all')" class="filter-btn rounded-lg border border-blue-500/30 bg-blue-500/20 px-3 py-1.5 text-xs font-medium text-blue-400 transition" id="filter-all">Tümü</button>
                <button onclick="filterDocs('expired')" class="filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="filter-expired">Süresi Dolmuş</button>
                <button onclick="filterDocs('warning')" class="filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="filter-warning">30 Gün İçinde</button>
                <button onclick="filterDocs('ok')" class="filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="filter-ok">Geçerli</button>
                <button onclick="filterDocs('missing')" class="filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10" id="filter-missing">Eksik</button>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center"><div class="text-xl font-bold text-white" id="statTotal">-</div><div class="text-[10px] text-white/50">Toplam</div></div>
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center"><div class="text-xl font-bold text-red-400" id="statExpired">-</div><div class="text-[10px] text-white/50">Süresi Dolmuş</div></div>
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center"><div class="text-xl font-bold text-yellow-400" id="statWarning">-</div><div class="text-[10px] text-white/50">30 Gün İçinde</div></div>
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center"><div class="text-xl font-bold text-emerald-400" id="statOk">-</div><div class="text-[10px] text-white/50">Geçerli</div></div>
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center"><div class="text-xl font-bold text-white/40" id="statMissing">-</div><div class="text-[10px] text-white/50">Eksik/Tarihi Yok</div></div>
            </div>

            <!-- Expiry List -->
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-4">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <i data-lucide="file-text" class="h-4 w-4 text-amber-400"></i>
                        Süresi Dolan / Dolacak Evraklar
                        <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] text-amber-400" id="expiryCount"></span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-white/10 text-white/40">
                                <th class="pb-2 pr-4 font-medium">Sahip</th>
                                <th class="pb-2 pr-4 font-medium">Tür</th>
                                <th class="pb-2 pr-4 font-medium">Evrak</th>
                                <th class="pb-2 pr-4 font-medium">Son Tarih</th>
                                <th class="pb-2 font-medium">Durum</th>
                            </tr>
                        </thead>
                        <tbody id="docTableBody"></tbody>
                    </table>
                </div>
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
    { type: 'kimlik', label: 'Kimlik' }, { type: 'ehliyet', label: 'Ehliyet' }, { type: 'src5Psikoteknik', label: 'SRC 5 Psikoteknik' },
    { type: 'adliSicil', label: 'Adli Sicil' }, { type: 'iseGirisBildirge', label: 'İşe Giriş Bildirgesi' },
    { type: 'ikametgah', label: 'İkametgah' }, { type: 'kkdZimmet', label: 'KKD Zimmet Tutanağı' },
    { type: 'saglikMuayene', label: 'Sağlık Muayene' }, { type: 'isgEgitimBelgesi', label: 'İSG Eğitim Belgesi' },
    { type: 'yanginEgitimSertifikasi', label: 'Yangın Eğitim Sertifikası' },
];
function ensureDocs(docs, defaults) {
    if (!docs || docs.length === 0) return defaults.map(d => ({ ...d, fileName: null, filePath: null, expiryDate: null }));
    return docs;
}

let allDocs = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [trucks, trailers, drivers] = await Promise.all([
            loadTrucksWithStore(), loadTrailersWithStore(), loadDriversWithStore(),
        ]);
        allDocs = [];
        trucks.forEach(t => ensureDocs(t.documents, DEFAULT_VEHICLE_DOCS).forEach(d => allDocs.push({ owner: t.plate, ownerType: 'Çekici', ...d })));
        trailers.forEach(t => ensureDocs(t.documents, DEFAULT_VEHICLE_DOCS).forEach(d => allDocs.push({ owner: t.plate, ownerType: 'Dorse', ...d })));
        drivers.forEach(dr => ensureDocs(dr.documents, DEFAULT_DRIVER_DOCS).forEach(d => allDocs.push({ owner: dr.name, ownerType: 'Şoför', ...d })));
        updateStats();
        renderTable();
    } catch (e) { showToast('Veriler yüklenemedi', 'error'); }
});

function getStatus(doc) {
    if (!doc.fileName) return 'missing';
    if (!doc.expiryDate) return 'missing';
    const diff = Math.ceil((new Date(doc.expiryDate) - new Date()) / (1000*60*60*24));
    if (diff < 0) return 'expired';
    if (diff <= 30) return 'warning';
    return 'ok';
}

function updateStats() {
    const stats = { total: allDocs.length, expired: 0, warning: 0, ok: 0, missing: 0 };
    allDocs.forEach(d => stats[getStatus(d)]++);
    document.getElementById('statTotal').textContent = stats.total;
    document.getElementById('statExpired').textContent = stats.expired;
    document.getElementById('statWarning').textContent = stats.warning;
    document.getElementById('statOk').textContent = stats.ok;
    document.getElementById('statMissing').textContent = stats.missing;
}

function daysUntil(dateStr) {
    const today = new Date(); today.setHours(0,0,0,0);
    const target = new Date(dateStr); target.setHours(0,0,0,0);
    return Math.ceil((target - today) / (1000*60*60*24));
}

function filterDocs(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.className = 'filter-btn rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10';
    });
    document.getElementById('filter-' + filter).className = 'filter-btn rounded-lg border border-blue-500/30 bg-blue-500/20 px-3 py-1.5 text-xs font-medium text-blue-400 transition';
    renderTable();
}

function renderTable() {
    const filtered = currentFilter === 'all' ? allDocs : allDocs.filter(d => getStatus(d) === currentFilter);
    const tbody = document.getElementById('docTableBody');
    document.getElementById('expiryCount').textContent = filtered.length;

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8">
            <div class="flex flex-col items-center"><i data-lucide="check-circle" class="mb-2 h-8 w-8 text-emerald-400/40"></i>
            <div class="text-sm text-white/40">Tüm evraklar güncel</div>
            <div class="mt-1 text-xs text-white/25">30 gün içinde süresi dolacak evrak yok</div></div>
        </td></tr>`;
        lucide.createIcons({nodes:[tbody]});
        return;
    }

    tbody.innerHTML = filtered.map(d => {
        const status = getStatus(d);
        const days = d.expiryDate ? daysUntil(d.expiryDate) : null;
        let badge = '';
        if (status === 'expired') {
            badge = `<span class="inline-flex items-center gap-1 rounded-full bg-red-500/20 px-2 py-0.5 text-[10px] font-semibold text-red-400"><i data-lucide="alert-triangle" class="h-3 w-3"></i>${days !== null ? Math.abs(days) + ' gün geçmiş' : 'Süresi Dolmuş'}</span>`;
        } else if (status === 'warning') {
            const cls = days <= 7 ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400';
            badge = `<span class="inline-flex items-center gap-1 rounded-full ${cls} px-2 py-0.5 text-[10px] font-semibold"><i data-lucide="clock" class="h-3 w-3"></i>${days} gün kaldı</span>`;
        } else if (status === 'ok') {
            badge = '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-semibold text-emerald-400">Geçerli</span>';
        } else {
            badge = '<span class="inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-white/40">Eksik</span>';
        }
        return `<tr class="border-b border-white/5">
            <td class="py-2 pr-4 font-medium text-white">${escapeHtml(d.owner)}</td>
            <td class="py-2 pr-4 text-white/50">${d.ownerType}</td>
            <td class="py-2 pr-4 text-white/60">${escapeHtml(d.label || d.type)}</td>
            <td class="py-2 pr-4 text-white/50">${d.expiryDate || '-'}</td>
            <td class="py-2">${badge}</td>
        </tr>`;
    }).join('');
    lucide.createIcons({nodes:[tbody]});
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
