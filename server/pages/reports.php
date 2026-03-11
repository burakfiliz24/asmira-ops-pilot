<?php
/**
 * Asmira Ops - Raporlar Ana Sayfa
 */
$pageTitle = 'Raporlar';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/reports';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Raporlar</h1>
        <p class="text-sm text-white/50 mt-1">Operasyon ve evrak takip raporları</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="/reports/document-tracking" class="card p-6 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                    <i data-lucide="file-search" class="h-7 w-7 text-emerald-400"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white group-hover:text-emerald-400 transition">Evrak Takibi</h2>
                    <p class="text-sm text-white/50">Süresi dolan/dolacak evrakların takibi</p>
                </div>
                <i data-lucide="chevron-right" class="h-5 w-5 text-white/30 group-hover:text-emerald-400 transition"></i>
            </div>
        </a>
        <a href="/reports/operations" class="card p-6 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-500/10 border border-blue-500/20">
                    <i data-lucide="bar-chart-3" class="h-7 w-7 text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white group-hover:text-blue-400 transition">İkmal Raporları</h2>
                    <p class="text-sm text-white/50">Operasyon istatistikleri ve özet</p>
                </div>
                <i data-lucide="chevron-right" class="h-5 w-5 text-white/30 group-hover:text-blue-400 transition"></i>
            </div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
