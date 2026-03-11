<?php
/**
 * Asmira Ops - Evrak Paketi
 */
$pageTitle = 'Evrak Paketi';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/document-package';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Evrak Paketi</h1>
        <p class="text-sm text-white/50 mt-1">Operasyon için gerekli evrak paketini oluşturun</p>
    </div>

    <!-- Araç Seti Seçimi -->
    <div class="card p-6 mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">Araç & Şoför Seçimi</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Araç Seti</label>
                <select id="vehicleSetSelect" class="input-field" onchange="loadPackage()">
                    <option value="">Seçiniz...</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Şoför</label>
                <select id="driverSelect" class="input-field" onchange="loadPackage()">
                    <option value="">Seçiniz...</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadPackage()" class="btn btn-primary w-full">
                    <i data-lucide="package-check" class="h-4 w-4"></i> Paketi Oluştur
                </button>
            </div>
        </div>
    </div>

    <!-- Evrak Durumu -->
    <div id="packageContent" class="hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Çekici Evrakları -->
            <div class="card p-5">
                <h3 class="text-base font-semibold text-white mb-3 flex items-center gap-2">
                    <i data-lucide="truck" class="h-5 w-5 text-blue-400"></i> Çekici Evrakları
                </h3>
                <div id="truckDocs" class="space-y-2"></div>
            </div>
            <!-- Dorse Evrakları -->
            <div class="card p-5">
                <h3 class="text-base font-semibold text-white mb-3 flex items-center gap-2">
                    <i data-lucide="container" class="h-5 w-5 text-cyan-400"></i> Dorse Evrakları
                </h3>
                <div id="trailerDocs" class="space-y-2"></div>
            </div>
            <!-- Şoför Evrakları -->
            <div class="card p-5">
                <h3 class="text-base font-semibold text-white mb-3 flex items-center gap-2">
                    <i data-lucide="user" class="h-5 w-5 text-emerald-400"></i> Şoför Evrakları
                </h3>
                <div id="driverDocs" class="space-y-2"></div>
            </div>
        </div>

        <!-- Özet -->
        <div class="card p-5 mt-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-emerald-400"></div>
                    <span class="text-sm text-white/70">Tamam: <strong id="okCount">0</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-red-400"></div>
                    <span class="text-sm text-white/70">Eksik: <strong id="missingCount">0</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                    <span class="text-sm text-white/70">Süresi Yakın: <strong id="warningCount">0</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let trucks = [], trailers = [], vehicleSets = [], drivers = [];

document.addEventListener('DOMContentLoaded', async () => {
    try {
        [trucks, trailers, vehicleSets, drivers] = await Promise.all([
            apiRequest('/api/trucks'), apiRequest('/api/trailers'),
            apiRequest('/api/vehicle-sets'), apiRequest('/api/drivers'),
        ]);
        const setSelect = document.getElementById('vehicleSetSelect');
        vehicleSets.forEach(s => {
            const truck = trucks.find(t => t.id === s.truckId);
            const trailer = trailers.find(t => t.id === s.trailerId);
            setSelect.innerHTML += `<option value="${s.id}">${truck?.plate || '?'} + ${trailer?.plate || '?'}</option>`;
        });
        const driverSelect = document.getElementById('driverSelect');
        drivers.forEach(d => {
            driverSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
        });
    } catch (e) { showToast('Veriler yüklenemedi', 'error'); }
});

function loadPackage() {
    const setId = document.getElementById('vehicleSetSelect').value;
    const driverId = document.getElementById('driverSelect').value;
    if (!setId && !driverId) return;

    document.getElementById('packageContent').classList.remove('hidden');
    let okCount = 0, missingCount = 0, warningCount = 0;

    if (setId) {
        const set = vehicleSets.find(s => s.id === setId);
        const truck = trucks.find(t => t.id === set?.truckId);
        const trailer = trailers.find(t => t.id === set?.trailerId);
        renderDocList('truckDocs', truck?.documents || []);
        renderDocList('trailerDocs', trailer?.documents || []);
        [truck, trailer].forEach(v => {
            (v?.documents || []).forEach(d => {
                if (d.fileName) { okCount++; if (d.expiryDate && isWarning(d.expiryDate)) warningCount++; }
                else missingCount++;
            });
        });
    }

    if (driverId) {
        const driver = drivers.find(d => d.id === driverId);
        renderDocList('driverDocs', driver?.documents || []);
        (driver?.documents || []).forEach(d => {
            if (d.fileName) { okCount++; if (d.expiryDate && isWarning(d.expiryDate)) warningCount++; }
            else missingCount++;
        });
    }

    document.getElementById('okCount').textContent = okCount;
    document.getElementById('missingCount').textContent = missingCount;
    document.getElementById('warningCount').textContent = warningCount;
    lucide.createIcons();
}

function isWarning(dateStr) {
    const diff = Math.ceil((new Date(dateStr) - new Date()) / (1000*60*60*24));
    return diff >= 0 && diff <= 30;
}

function renderDocList(containerId, docs) {
    const container = document.getElementById(containerId);
    if (docs.length === 0) { container.innerHTML = '<p class="text-sm text-white/30">Evrak yok</p>'; return; }
    container.innerHTML = docs.map(d => {
        const hasFile = !!d.fileName;
        const cls = hasFile ? 'text-emerald-400' : 'text-red-400';
        const icon = hasFile ? 'check-circle' : 'x-circle';
        return `<div class="flex items-center justify-between py-1.5 text-sm">
            <span class="text-white/60">${escapeHtml(d.label || d.type)}</span>
            <div class="flex items-center gap-2">
                ${d.expiryDate ? `<span class="text-xs ${getExpiryClass(d.expiryDate)}">${getExpiryText(d.expiryDate)}</span>` : ''}
                <i data-lucide="${icon}" class="h-4 w-4 ${cls}"></i>
            </div>
        </div>`;
    }).join('');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
