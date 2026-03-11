<?php
/**
 * Asmira Ops - İkmal Raporları
 */
$pageTitle = 'İkmal Raporları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/reports/operations';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex items-center gap-3">
        <a href="/reports" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">İkmal Raporları</h1>
            <p class="text-sm text-white/50 mt-1">Operasyon istatistikleri</p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-white" id="totalOps">-</div>
            <div class="text-xs text-white/50 mt-1">Toplam İkmal</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-emerald-400" id="completedOps">-</div>
            <div class="text-xs text-white/50 mt-1">Tamamlanan</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-blue-400" id="totalTonaj">-</div>
            <div class="text-xs text-white/50 mt-1">Toplam Tonaj (MT)</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-yellow-400" id="activeOps">-</div>
            <div class="text-xs text-white/50 mt-1">Aktif</div>
        </div>
    </div>

    <!-- Operations Table -->
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-white/5">
            <h3 class="font-semibold text-white">Tüm Operasyonlar</h3>
            <input type="text" id="searchOps" class="input-field w-64" placeholder="Ara..." oninput="filterOps()">
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <th>Gemi</th><th>Liman</th><th>Miktar</th><th>Tarih</th><th>Durum</th>
                </tr></thead>
                <tbody id="opsTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
let operations = [];
const STATUS_BADGES = {
    planned: '<span class="badge badge-gray">Planlandı</span>',
    approaching: '<span class="badge badge-yellow">Yanaşıyor</span>',
    active: '<span class="badge badge-blue">Aktif</span>',
    completed: '<span class="badge badge-green">Tamamlandı</span>',
    cancelled: '<span class="badge badge-red">İptal</span>',
};

document.addEventListener('DOMContentLoaded', async () => {
    try {
        operations = await apiRequest('/api/operations');
        updateKPIs();
        renderTable(operations);
    } catch (e) { showToast('Veriler yüklenemedi', 'error'); }
});

function updateKPIs() {
    document.getElementById('totalOps').textContent = operations.length;
    document.getElementById('completedOps').textContent = operations.filter(o => o.status === 'completed').length;
    document.getElementById('activeOps').textContent = operations.filter(o => o.status === 'active' || o.status === 'approaching').length;
    const tonaj = operations.filter(o => o.unit === 'MT').reduce((s, o) => s + Number(o.quantity || 0), 0);
    document.getElementById('totalTonaj').textContent = tonaj.toLocaleString('tr-TR', { maximumFractionDigits: 0 });
}

function renderTable(ops) {
    const tbody = document.getElementById('opsTableBody');
    if (ops.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-white/30">Operasyon bulunamadı</td></tr>';
        return;
    }
    tbody.innerHTML = ops.map(o => `<tr>
        <td class="font-medium">${escapeHtml(o.vesselName || '')}</td>
        <td>${escapeHtml(o.port || '')}</td>
        <td>${o.quantity} ${o.unit || 'MT'}</td>
        <td>${formatDate(o.date)}</td>
        <td>${STATUS_BADGES[o.status] || STATUS_BADGES.planned}</td>
    </tr>`).join('');
}

function filterOps() {
    const q = document.getElementById('searchOps').value.toLowerCase();
    const filtered = operations.filter(o =>
        (o.vesselName || '').toLowerCase().includes(q) ||
        (o.port || '').toLowerCase().includes(q)
    );
    renderTable(filtered);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
