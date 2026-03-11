<?php
/**
 * Asmira Ops - Tedarikçi Araç Evrakları
 */
$pageTitle = 'Tedarikçi Araçları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/vehicle-documents/suppliers';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="/vehicle-documents" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Tedarikçi Araçları</h1>
                <p class="text-sm text-white/50 mt-1">Tedarikçi firmalara ait çekici ve dorse evrakları</p>
            </div>
        </div>
    </div>

    <div class="flex gap-1 mb-6 border-b border-white/10">
        <button onclick="switchTab('trucks')" class="tab-btn text-blue-400 border-b-2 border-blue-400 px-4 py-2.5 text-sm font-medium transition-colors" id="tab-trucks">Çekiciler</button>
        <button onclick="switchTab('trailers')" class="tab-btn px-4 py-2.5 text-sm font-medium text-white/50 transition-colors" id="tab-trailers">Dorseler</button>
        <button onclick="switchTab('sets')" class="tab-btn px-4 py-2.5 text-sm font-medium text-white/50 transition-colors" id="tab-sets">Araç Setleri</button>
    </div>

    <div id="content-trucks"></div>
    <div id="content-trailers" class="hidden"></div>
    <div id="content-sets" class="hidden"></div>
</div>

<script>
let trucks = [], trailers = [], vehicleSets = [];
const CATEGORY = 'supplier';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        [trucks, trailers, vehicleSets] = await Promise.all([
            apiRequest('/api/trucks'),
            apiRequest('/api/trailers'),
            apiRequest('/api/vehicle-sets'),
        ]);
        trucks = trucks.filter(t => t.category === CATEGORY);
        trailers = trailers.filter(t => t.category === CATEGORY);
        vehicleSets = vehicleSets.filter(s => s.category === CATEGORY);
        renderAll();
    } catch (e) {
        showToast('Veriler yüklenemedi: ' + e.message, 'error');
    }
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('text-blue-400', 'border-b-2', 'border-blue-400'); b.classList.add('text-white/50'); });
    document.querySelectorAll('[id^="content-"]').forEach(c => c.classList.add('hidden'));
    const btn = document.getElementById('tab-' + tab);
    btn.classList.remove('text-white/50');
    btn.classList.add('text-blue-400', 'border-b-2', 'border-blue-400');
    document.getElementById('content-' + tab).classList.remove('hidden');
}

function renderAll() {
    renderVehicleList('content-trucks', trucks, 'truck', 'Çekici');
    renderVehicleList('content-trailers', trailers, 'trailer', 'Dorse');
    renderSetList();
}

function renderVehicleList(containerId, items, type, label) {
    const container = document.getElementById(containerId);
    if (items.length === 0) {
        container.innerHTML = `<div class="text-center py-12 text-white/40"><p>Henüz tedarikçi ${label.toLowerCase()}si eklenmemiş</p></div>`;
        return;
    }
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">';
    items.forEach(item => {
        const docs = item.documents || [];
        const total = docs.length;
        const uploaded = docs.filter(d => d.fileName).length;
        const expired = docs.filter(d => d.expiryDate && new Date(d.expiryDate) < new Date()).length;
        html += `<div class="card p-4 hover:bg-white/[0.05] transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/10">
                        <i data-lucide="${type === 'truck' ? 'truck' : 'container'}" class="h-5 w-5 text-purple-400"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-white">${escapeHtml(item.plate)}</div>
                        <div class="text-xs text-white/40">Tedarikçi ${label}</div>
                    </div>
                </div>
                <button onclick="deleteVehicle('${item.id}', '${type}')" class="h-8 w-8 flex items-center justify-center rounded-lg text-white/30 hover:text-red-400 hover:bg-red-500/10 transition">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                </button>
            </div>
            <div class="flex gap-2 mb-3">
                <span class="badge badge-blue">${uploaded}/${total} Evrak</span>
                ${expired > 0 ? `<span class="badge badge-red">${expired} Süresi Dolmuş</span>` : ''}
            </div>
            <div class="space-y-1.5 max-h-48 overflow-y-auto">
                ${docs.map(d => `
                    <div class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-white/[0.02] text-xs">
                        <span class="text-white/60">${escapeHtml(d.label || d.type)}</span>
                        <div class="flex items-center gap-2">
                            ${d.expiryDate ? `<span class="${getExpiryClass(d.expiryDate)}">${getExpiryText(d.expiryDate)}</span>` : ''}
                            ${d.fileName ? '<span class="text-emerald-400">✓</span>' : '<span class="text-white/20">—</span>'}
                            <label class="cursor-pointer text-blue-400 hover:text-blue-300">
                                <i data-lucide="upload" class="h-3 w-3"></i>
                                <input type="file" class="hidden" onchange="uploadDoc(this, '${item.id}', '${type}', '${d.type}')">
                            </label>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons({nodes: [container]});
}

function renderSetList() {
    const container = document.getElementById('content-sets');
    if (vehicleSets.length === 0) {
        container.innerHTML = '<div class="text-center py-12 text-white/40"><p>Henüz tedarikçi araç seti yok</p></div>';
        return;
    }
    let html = '<div class="space-y-3">';
    vehicleSets.forEach(s => {
        const truck = trucks.find(t => t.id === s.truckId);
        const trailer = trailers.find(t => t.id === s.trailerId);
        html += `<div class="card p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-500/10"><i data-lucide="link" class="h-5 w-5 text-cyan-400"></i></div>
                <div><span class="font-semibold text-white">${escapeHtml(truck?.plate || '?')}</span><span class="text-white/30 mx-2">+</span><span class="font-semibold text-white">${escapeHtml(trailer?.plate || '?')}</span></div>
            </div>
            <button onclick="deleteSet('${s.id}')" class="h-8 w-8 flex items-center justify-center rounded-lg text-white/30 hover:text-red-400 hover:bg-red-500/10 transition"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons({nodes: [container]});
}

async function uploadDoc(input, ownerId, ownerType, docType) {
    const file = input.files[0]; if (!file) return;
    try {
        await uploadFile(file, ownerId, ownerType, docType);
        showToast('Dosya yüklendi');
        const data = ownerType === 'truck' ? await apiRequest('/api/trucks') : await apiRequest('/api/trailers');
        if (ownerType === 'truck') trucks = data.filter(t => t.category === CATEGORY);
        else trailers = data.filter(t => t.category === CATEGORY);
        renderAll();
    } catch (e) { showToast('Yükleme hatası: ' + e.message, 'error'); }
}

async function deleteVehicle(id, type) {
    if (!confirmAction('Bu aracı silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest(type === 'truck' ? '/api/trucks' : '/api/trailers', { method: 'DELETE', body: JSON.stringify({ id }) });
        if (type === 'truck') trucks = trucks.filter(t => t.id !== id);
        else trailers = trailers.filter(t => t.id !== id);
        renderAll(); showToast('Araç silindi');
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}

async function deleteSet(id) {
    if (!confirmAction('Bu araç setini silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/vehicle-sets', { method: 'DELETE', body: JSON.stringify({ id }) });
        vehicleSets = vehicleSets.filter(s => s.id !== id);
        renderSetList(); showToast('Araç seti silindi');
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
