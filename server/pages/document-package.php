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
                    <select id="truckSelect" onchange="onTruckChange()" class="mb-4 h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-blue-500/50">
                        <option value="">Çekici seçin...</option>
                    </select>
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
                    <select id="trailerSelect" onchange="onTrailerChange()" class="mb-4 h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-cyan-500/50">
                        <option value="">Dorse seçin...</option>
                    </select>
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
                    <select id="driverSelect" onchange="onDriverChange()" class="mb-4 h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-purple-500/50">
                        <option value="">Şoför seçin...</option>
                    </select>
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

<script>
let trucks = [], trailers = [], drivers = [];
let selectedTruckDocs = new Set(), selectedTrailerDocs = new Set(), selectedDriverDocs = new Set();

document.addEventListener('DOMContentLoaded', async () => {
    try {
        [trucks, trailers, drivers] = await Promise.all([
            apiRequest('/api/trucks'), apiRequest('/api/trailers'), apiRequest('/api/drivers')
        ]);
        document.getElementById('truckCount').textContent = trucks.length;
        document.getElementById('trailerCount').textContent = trailers.length;
        document.getElementById('driverCount').textContent = drivers.length;

        const truckSel = document.getElementById('truckSelect');
        trucks.forEach(t => { truckSel.innerHTML += `<option value="${t.id}">${escapeHtml(t.plate)} ${t.category==='supplier'?'(Tedarikçi)':'(Asmira)'}</option>`; });
        const trailerSel = document.getElementById('trailerSelect');
        trailers.forEach(t => { trailerSel.innerHTML += `<option value="${t.id}">${escapeHtml(t.plate)} ${t.category==='supplier'?'(Tedarikçi)':'(Asmira)'}</option>`; });
        const driverSel = document.getElementById('driverSelect');
        drivers.forEach(d => { driverSel.innerHTML += `<option value="${d.id}">${escapeHtml(d.name)} (${escapeHtml(d.tcNo||'')})</option>`; });
    } catch (e) { showToast('Veriler yüklenemedi', 'error'); }
});

function onTruckChange() { selectedTruckDocs = new Set(); renderTruckDocs(); updateSummary(); }
function onTrailerChange() { selectedTrailerDocs = new Set(); renderTrailerDocs(); updateSummary(); }
function onDriverChange() { selectedDriverDocs = new Set(); renderDriverDocs(); updateSummary(); }

function getSelectedTruck() { return trucks.find(t => t.id === document.getElementById('truckSelect').value); }
function getSelectedTrailer() { return trailers.find(t => t.id === document.getElementById('trailerSelect').value); }
function getSelectedDriver() { return drivers.find(d => d.id === document.getElementById('driverSelect').value); }

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
    renderDocChecklist('truckDocsList', t?.documents, selectedTruckDocs, 'toggleTruckDoc', 'selectAllTruck', 'text-blue-400');
}
function renderTrailerDocs() {
    const t = getSelectedTrailer();
    renderDocChecklist('trailerDocsList', t?.documents, selectedTrailerDocs, 'toggleTrailerDoc', 'selectAllTrailer', 'text-cyan-400');
}
function renderDriverDocs() {
    const d = getSelectedDriver();
    renderDocChecklist('driverDocsList', d?.documents, selectedDriverDocs, 'toggleDriverDoc', 'selectAllDriver', 'text-purple-400');
}

function toggleTruckDoc(type) { selectedTruckDocs.has(type) ? selectedTruckDocs.delete(type) : selectedTruckDocs.add(type); renderTruckDocs(); updateSummary(); }
function toggleTrailerDoc(type) { selectedTrailerDocs.has(type) ? selectedTrailerDocs.delete(type) : selectedTrailerDocs.add(type); renderTrailerDocs(); updateSummary(); }
function toggleDriverDoc(type) { selectedDriverDocs.has(type) ? selectedDriverDocs.delete(type) : selectedDriverDocs.add(type); renderDriverDocs(); updateSummary(); }

function selectAllTruck() { const t = getSelectedTruck(); if (!t) return; t.documents.filter(d=>d.fileName).forEach(d=>selectedTruckDocs.add(d.type)); renderTruckDocs(); updateSummary(); }
function selectAllTrailer() { const t = getSelectedTrailer(); if (!t) return; t.documents.filter(d=>d.fileName).forEach(d=>selectedTrailerDocs.add(d.type)); renderTrailerDocs(); updateSummary(); }
function selectAllDriver() { const d = getSelectedDriver(); if (!d) return; d.documents.filter(dd=>dd.fileName).forEach(dd=>selectedDriverDocs.add(dd.type)); renderDriverDocs(); updateSummary(); }

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
    if (truck) selectedTruckDocs.forEach(type => { const d = truck.documents.find(dd=>dd.type===type); if(d) badges += `<div class="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1.5"><i data-lucide="truck" class="h-3 w-3 text-blue-400"></i><span class="text-xs font-medium text-blue-300">${escapeHtml(d.label)}</span></div>`; });
    const trailer = getSelectedTrailer();
    if (trailer) selectedTrailerDocs.forEach(type => { const d = trailer.documents.find(dd=>dd.type===type); if(d) badges += `<div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1.5"><i data-lucide="container" class="h-3 w-3 text-cyan-400"></i><span class="text-xs font-medium text-cyan-300">${escapeHtml(d.label)}</span></div>`; });
    const driver = getSelectedDriver();
    if (driver) selectedDriverDocs.forEach(type => { const d = driver.documents.find(dd=>dd.type===type); if(d) badges += `<div class="inline-flex items-center gap-2 rounded-full border border-purple-500/30 bg-purple-500/10 px-3 py-1.5"><i data-lucide="user-check" class="h-3 w-3 text-purple-400"></i><span class="text-xs font-medium text-purple-300">${escapeHtml(d.label)}</span></div>`; });
    document.getElementById('summaryBadges').innerHTML = badges;
    lucide.createIcons({nodes:[document.getElementById('summaryBadges')]});
}

function handleGeneratePDF() {
    const total = selectedTruckDocs.size + selectedTrailerDocs.size + selectedDriverDocs.size;
    if (total === 0) { showToast('Lütfen en az bir evrak seçin', 'error'); return; }
    showToast('PDF oluşturma özelliği sunucu tarafında hazırlanıyor...');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
