<?php
/**
 * Asmira Ops - İkmal Raporları (React orijinali ile birebir)
 */
$pageTitle = 'İkmal Raporları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/reports/operations';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-auto rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-cyan-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/60 via-cyan-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">RAPORLAR</div>
                <div class="text-3xl font-black tracking-tight">İkmal Raporu</div>
                <div class="mt-1 text-xs text-white/50">Aylık ikmal operasyonları özeti</div>
            </div>
        </div>

        <!-- Month Selector -->
        <div class="flex flex-none items-center justify-between border-b border-white/10 px-6 py-3">
            <button type="button" onclick="goToPrevMonth()" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-white/[0.03] text-white/60 transition hover:bg-white/[0.08] hover:text-white">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
            </button>
            <div class="flex items-center gap-2">
                <div class="flex gap-1 overflow-x-auto" id="monthTabs"></div>
                <span class="ml-2 text-sm font-bold text-white/80" id="yearLabel"></span>
            </div>
            <button type="button" onclick="goToNextMonth()" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-white/[0.03] text-white/60 transition hover:bg-white/[0.08] hover:text-white">
                <i data-lucide="chevron-right" class="h-4 w-4"></i>
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 px-6 pt-4 sm:grid-cols-5">
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
                <div class="text-2xl font-black text-white" id="statTotal">0</div>
                <div class="text-[10px] font-medium text-white/40">Toplam İkmal</div>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
                <div class="text-2xl font-black text-cyan-400" id="statMT">0</div>
                <div class="text-[10px] font-medium text-white/40">Toplam MT</div>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
                <div class="text-2xl font-black text-amber-400" id="statL">0</div>
                <div class="text-[10px] font-medium text-white/40">Toplam L</div>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
                <div class="text-2xl font-black text-blue-400" id="statShip">0</div>
                <div class="text-[10px] font-medium text-white/40">Gemi</div>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
                <div class="text-2xl font-black text-purple-400" id="statYacht">0</div>
                <div class="text-[10px] font-medium text-white/40">Yat</div>
            </div>
        </div>

        <!-- Operations Table -->
        <div class="flex-1 p-4 sm:p-6">
            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-4">
                <div class="mb-4 flex items-center gap-2 text-sm font-semibold">
                    <i data-lucide="fuel" class="h-4 w-4 text-cyan-400"></i>
                    <span id="tableTitle"></span>
                    <span class="rounded-full bg-cyan-500/20 px-2 py-0.5 text-[10px] text-cyan-400" id="opsCount">0</span>
                </div>
                <div class="overflow-x-auto" id="tableContainer"></div>
            </div>
        </div>
    </div>
</div>

<script>
const MONTHS_TR = ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
const STATUS_MAP = {
    planned: { label: 'Planlandı', cls: 'bg-blue-500/20 text-blue-400' },
    approaching: { label: 'Yaklaşıyor', cls: 'bg-amber-500/20 text-amber-400' },
    active: { label: 'Aktif', cls: 'bg-emerald-500/20 text-emerald-400' },
    completed: { label: 'Tamamlandı', cls: 'bg-green-500/20 text-green-400' },
    cancelled: { label: 'İptal', cls: 'bg-red-500/20 text-red-400' },
};

let operations = [];
const now = new Date();
let selectedYear = now.getFullYear();
let selectedMonth = now.getMonth();

document.addEventListener('DOMContentLoaded', async () => {
    try {
        operations = await apiRequest('/api/operations');
    } catch (e) { showToast('Veriler yüklenemedi', 'error'); }
    renderMonthTabs();
    renderAll();
    lucide.createIcons();
});

function renderMonthTabs() {
    const tabs = document.getElementById('monthTabs');
    tabs.innerHTML = MONTHS_TR.map((m, i) => {
        const cls = i === selectedMonth
            ? 'rounded-lg px-3 py-1.5 text-xs font-medium bg-cyan-500/20 text-cyan-400 shadow-[0_0_8px_rgba(6,182,212,0.2)]'
            : 'rounded-lg px-3 py-1.5 text-xs font-medium text-white/40 hover:bg-white/[0.04] hover:text-white/70';
        return `<button type="button" onclick="selectMonth(${i})" class="${cls}">${m}</button>`;
    }).join('');
    document.getElementById('yearLabel').textContent = selectedYear;
}

function selectMonth(m) { selectedMonth = m; renderMonthTabs(); renderAll(); }
function goToPrevMonth() { if (selectedMonth === 0) { selectedMonth = 11; selectedYear--; } else { selectedMonth--; } renderMonthTabs(); renderAll(); }
function goToNextMonth() { if (selectedMonth === 11) { selectedMonth = 0; selectedYear++; } else { selectedMonth++; } renderMonthTabs(); renderAll(); }

function getMonthlyOps() {
    return operations.filter(op => {
        const d = new Date(op.date);
        return d.getFullYear() === selectedYear && d.getMonth() === selectedMonth;
    }).sort((a, b) => new Date(a.date) - new Date(b.date));
}

function renderAll() {
    const ops = getMonthlyOps();
    // Stats
    const totalMT = ops.filter(o => o.unit === 'MT').reduce((s, o) => s + Number(o.quantity || 0), 0);
    const totalL = ops.filter(o => o.unit === 'L').reduce((s, o) => s + Number(o.quantity || 0), 0);
    const shipCount = ops.filter(o => o.vesselType !== 'yacht').length;
    const yachtCount = ops.filter(o => o.vesselType === 'yacht').length;
    document.getElementById('statTotal').textContent = ops.length;
    document.getElementById('statMT').textContent = totalMT.toFixed(1);
    document.getElementById('statL').textContent = totalL.toLocaleString('tr-TR');
    document.getElementById('statShip').textContent = shipCount;
    document.getElementById('statYacht').textContent = yachtCount;
    document.getElementById('tableTitle').textContent = `${MONTHS_TR[selectedMonth]} ${selectedYear} İkmalleri`;
    document.getElementById('opsCount').textContent = ops.length;

    const container = document.getElementById('tableContainer');
    if (ops.length === 0) {
        container.innerHTML = `<div class="flex flex-col items-center py-12 text-center">
            <i data-lucide="ship" class="mb-3 h-10 w-10 text-white/15"></i>
            <div class="text-sm text-white/40">Bu ayda ikmal kaydı yok</div>
            <div class="mt-1 text-xs text-white/25">Dashboard takviminden ikmal ekleyebilirsiniz</div>
        </div>`;
        lucide.createIcons({nodes:[container]});
        return;
    }

    let html = `<table class="w-full text-left text-xs">
        <thead><tr class="border-b border-white/10 text-white/40">
            <th class="pb-2.5 pr-4 font-medium"><div class="flex items-center gap-1.5"><i data-lucide="calendar" class="h-3 w-3"></i> Tarih</div></th>
            <th class="pb-2.5 pr-4 font-medium">Tür</th>
            <th class="pb-2.5 pr-4 font-medium"><div class="flex items-center gap-1.5"><i data-lucide="ship" class="h-3 w-3"></i> Gemi / Yat</div></th>
            <th class="pb-2.5 pr-4 font-medium">IMO</th>
            <th class="pb-2.5 pr-4 font-medium"><div class="flex items-center gap-1.5"><i data-lucide="map-pin" class="h-3 w-3"></i> Liman</div></th>
            <th class="pb-2.5 pr-4 font-medium">Dolum Yeri</th>
            <th class="pb-2.5 pr-4 font-medium">Miktar</th>
        </tr></thead><tbody>`;

    ops.forEach(op => {
        const isYacht = op.vesselType === 'yacht';
        const typeBadge = isYacht
            ? '<span class="inline-flex items-center gap-1 rounded-full bg-purple-500/20 px-2 py-0.5 text-[10px] font-semibold text-purple-400">⛵ Yat</span>'
            : '<span class="inline-flex items-center gap-1 rounded-full bg-blue-500/20 px-2 py-0.5 text-[10px] font-semibold text-blue-400">🚢 Gemi</span>';
        const nameCls = isYacht ? 'text-purple-300' : 'text-white';
        const d = new Date(op.date);
        const dateStr = d.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        html += `<tr class="border-b border-white/5 transition-colors hover:bg-white/[0.02]">
            <td class="py-2.5 pr-4 font-medium text-white/70">${dateStr}</td>
            <td class="py-2.5 pr-4">${typeBadge}</td>
            <td class="py-2.5 pr-4 font-semibold ${nameCls}">${escapeHtml(op.vesselName || '')}</td>
            <td class="py-2.5 pr-4 text-white/40">${op.imoNumber || '—'}</td>
            <td class="py-2.5 pr-4 text-white/60">${escapeHtml(op.port || '')}</td>
            <td class="py-2.5 pr-4 text-white/50">${escapeHtml(op.loadingPlace || '—')}</td>
            <td class="py-2.5 pr-4"><span class="font-semibold text-cyan-400">${op.quantity || 0}</span> <span class="text-white/40">${op.unit || 'MT'}</span></td>
        </tr>`;
    });

    // Footer totals
    const hasMT = ops.some(o => o.unit === 'MT');
    const hasL = ops.some(o => o.unit === 'L');
    let footerHtml = '';
    if (hasMT) footerHtml += `<div><span class="text-sm font-bold text-cyan-400">${totalMT.toFixed(1)}</span> <span class="text-xs text-white/40">MT</span></div>`;
    if (hasL) footerHtml += `<div><span class="text-sm font-bold text-amber-400">${totalL.toLocaleString('tr-TR')}</span> <span class="text-xs text-white/40">L</span></div>`;

    html += `</tbody><tfoot><tr class="border-t border-white/10">
        <td colspan="6" class="py-3 pr-4 text-right text-xs font-semibold text-white/50">Toplam:</td>
        <td class="py-3 pr-4"><div class="flex flex-col gap-1">${footerHtml}</div></td>
    </tr></tfoot></table>`;

    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
