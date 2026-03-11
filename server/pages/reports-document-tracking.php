<?php
/**
 * Asmira Ops - Evrak Takibi Raporu
 */
$pageTitle = 'Evrak Takibi';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/reports/document-tracking';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex items-center gap-3">
        <a href="/reports" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Evrak Takibi</h1>
            <p class="text-sm text-white/50 mt-1">Süresi dolan ve yaklaşan evraklar</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button onclick="filterDocs('all')" class="btn btn-ghost text-xs filter-btn active" id="filter-all">Tümü</button>
        <button onclick="filterDocs('expired')" class="btn btn-ghost text-xs filter-btn" id="filter-expired">Süresi Dolmuş</button>
        <button onclick="filterDocs('warning')" class="btn btn-ghost text-xs filter-btn" id="filter-warning">30 Gün İçinde</button>
        <button onclick="filterDocs('ok')" class="btn btn-ghost text-xs filter-btn" id="filter-ok">Geçerli</button>
        <button onclick="filterDocs('missing')" class="btn btn-ghost text-xs filter-btn" id="filter-missing">Eksik</button>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="card p-3 text-center"><div class="text-xl font-bold text-white" id="statTotal">-</div><div class="text-[10px] text-white/50">Toplam</div></div>
        <div class="card p-3 text-center"><div class="text-xl font-bold text-red-400" id="statExpired">-</div><div class="text-[10px] text-white/50">Süresi Dolmuş</div></div>
        <div class="card p-3 text-center"><div class="text-xl font-bold text-yellow-400" id="statWarning">-</div><div class="text-[10px] text-white/50">30 Gün İçinde</div></div>
        <div class="card p-3 text-center"><div class="text-xl font-bold text-emerald-400" id="statOk">-</div><div class="text-[10px] text-white/50">Geçerli</div></div>
        <div class="card p-3 text-center"><div class="text-xl font-bold text-white/40" id="statMissing">-</div><div class="text-[10px] text-white/50">Eksik/Tarihi Yok</div></div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Araç/Şoför</th><th>Evrak</th><th>Son Kullanma</th><th>Durum</th><th>Dosya</th></tr></thead>
                <tbody id="docTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
let allDocs = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [trucks, trailers, drivers] = await Promise.all([
            apiRequest('/api/trucks'), apiRequest('/api/trailers'), apiRequest('/api/drivers'),
        ]);
        allDocs = [];
        trucks.forEach(t => (t.documents || []).forEach(d => allDocs.push({ owner: t.plate, ownerType: 'Çekici', ...d })));
        trailers.forEach(t => (t.documents || []).forEach(d => allDocs.push({ owner: t.plate, ownerType: 'Dorse', ...d })));
        drivers.forEach(dr => (dr.documents || []).forEach(d => allDocs.push({ owner: dr.name, ownerType: 'Şoför', ...d })));
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

function filterDocs(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => { b.classList.remove('!bg-blue-500/20', '!text-blue-400', '!border-blue-500/30'); });
    document.getElementById('filter-' + filter).classList.add('!bg-blue-500/20', '!text-blue-400', '!border-blue-500/30');
    renderTable();
}

function renderTable() {
    const filtered = currentFilter === 'all' ? allDocs : allDocs.filter(d => getStatus(d) === currentFilter);
    const tbody = document.getElementById('docTableBody');
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-white/30">Evrak bulunamadı</td></tr>';
        return;
    }

    const statusBadges = {
        expired: '<span class="badge badge-red">Süresi Dolmuş</span>',
        warning: '<span class="badge badge-yellow">Yaklaşıyor</span>',
        ok: '<span class="badge badge-green">Geçerli</span>',
        missing: '<span class="badge badge-gray">Eksik</span>',
    };

    tbody.innerHTML = filtered.map(d => {
        const status = getStatus(d);
        return `<tr>
            <td><div class="font-medium">${escapeHtml(d.owner)}</div><div class="text-xs text-white/40">${d.ownerType}</div></td>
            <td>${escapeHtml(d.label || d.type)}</td>
            <td><span class="${d.expiryDate ? getExpiryClass(d.expiryDate) : 'text-white/30'}">${d.expiryDate ? formatDate(d.expiryDate) : '-'}</span></td>
            <td>${statusBadges[status]}</td>
            <td>${d.fileName ? '<span class="text-emerald-400 text-xs">✓ ' + escapeHtml(d.fileName) + '</span>' : '<span class="text-white/20">—</span>'}</td>
        </tr>`;
    }).join('');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
