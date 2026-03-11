<?php
/**
 * Asmira Ops - Araç Evrakları Ana Sayfa
 */
$pageTitle = 'Araç Evrakları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/vehicle-documents';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Araç Evrakları</h1>
            <p class="text-sm text-white/50 mt-1">Çekici ve dorse evraklarını yönetin</p>
        </div>
        <a href="/vehicle-documents/new" class="btn btn-primary">
            <i data-lucide="plus" class="h-4 w-4"></i>
            Yeni Araç Ekle
        </a>
    </div>

    <!-- Category Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Asmira Özmal -->
        <a href="/vehicle-documents/asmira" class="card p-6 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-500/10 border border-blue-500/20">
                    <i data-lucide="truck" class="h-7 w-7 text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white group-hover:text-blue-400 transition-colors">Asmira Özmal</h2>
                    <p class="text-sm text-white/50">Asmira'ya ait çekici ve dorseler</p>
                </div>
                <i data-lucide="chevron-right" class="h-5 w-5 text-white/30 group-hover:text-blue-400 transition-colors"></i>
            </div>
            <div class="mt-4 flex gap-4" id="asmiraStats">
                <div class="rounded-lg bg-white/[0.03] px-3 py-2 text-center">
                    <div class="text-lg font-bold text-white" id="asmiraTruckCount">-</div>
                    <div class="text-[10px] text-white/50 uppercase">Çekici</div>
                </div>
                <div class="rounded-lg bg-white/[0.03] px-3 py-2 text-center">
                    <div class="text-lg font-bold text-white" id="asmiraTrailerCount">-</div>
                    <div class="text-[10px] text-white/50 uppercase">Dorse</div>
                </div>
                <div class="rounded-lg bg-white/[0.03] px-3 py-2 text-center">
                    <div class="text-lg font-bold text-white" id="asmiraSetCount">-</div>
                    <div class="text-[10px] text-white/50 uppercase">Araç Seti</div>
                </div>
            </div>
        </a>

        <!-- Tedarikçi -->
        <a href="/vehicle-documents/suppliers" class="card p-6 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-500/10 border border-purple-500/20">
                    <i data-lucide="building-2" class="h-7 w-7 text-purple-400"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white group-hover:text-purple-400 transition-colors">Tedarikçi Araçları</h2>
                    <p class="text-sm text-white/50">Tedarikçi firmalara ait araçlar</p>
                </div>
                <i data-lucide="chevron-right" class="h-5 w-5 text-white/30 group-hover:text-purple-400 transition-colors"></i>
            </div>
            <div class="mt-4 flex gap-4">
                <div class="rounded-lg bg-white/[0.03] px-3 py-2 text-center">
                    <div class="text-lg font-bold text-white" id="supplierTruckCount">-</div>
                    <div class="text-[10px] text-white/50 uppercase">Çekici</div>
                </div>
                <div class="rounded-lg bg-white/[0.03] px-3 py-2 text-center">
                    <div class="text-lg font-bold text-white" id="supplierTrailerCount">-</div>
                    <div class="text-[10px] text-white/50 uppercase">Dorse</div>
                </div>
                <div class="rounded-lg bg-white/[0.03] px-3 py-2 text-center">
                    <div class="text-lg font-bold text-white" id="supplierSetCount">-</div>
                    <div class="text-[10px] text-white/50 uppercase">Araç Seti</div>
                </div>
            </div>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [trucks, trailers, sets] = await Promise.all([
            apiRequest('/api/trucks'),
            apiRequest('/api/trailers'),
            apiRequest('/api/vehicle-sets'),
        ]);
        document.getElementById('asmiraTruckCount').textContent = trucks.filter(t => t.category === 'asmira').length;
        document.getElementById('asmiraTrailerCount').textContent = trailers.filter(t => t.category === 'asmira').length;
        document.getElementById('asmiraSetCount').textContent = sets.filter(s => s.category === 'asmira').length;
        document.getElementById('supplierTruckCount').textContent = trucks.filter(t => t.category === 'supplier').length;
        document.getElementById('supplierTrailerCount').textContent = trailers.filter(t => t.category === 'supplier').length;
        document.getElementById('supplierSetCount').textContent = sets.filter(s => s.category === 'supplier').length;
    } catch (e) {
        console.warn('İstatistikler yüklenemedi:', e);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
