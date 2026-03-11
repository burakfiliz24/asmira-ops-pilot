<?php
/**
 * Asmira Ops - Port Wiki
 */
$pageTitle = 'Port Wiki';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/port-wiki';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Port Wiki</h1>
            <p class="text-sm text-white/50 mt-1">Türkiye limanları ve ikmal bilgileri</p>
        </div>
        <a href="/port-wiki/ports" class="btn btn-primary">
            <i data-lucide="map" class="h-4 w-4"></i> Liman Listesi
        </a>
    </div>

    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10"><i data-lucide="anchor" class="h-5 w-5 text-blue-400"></i></div>
                <h3 class="font-semibold text-white">İkmal Limanları</h3>
            </div>
            <p class="text-sm text-white/50">Türkiye'deki başlıca yakıt ikmal limanları ve operasyon detayları</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10"><i data-lucide="ship" class="h-5 w-5 text-emerald-400"></i></div>
                <h3 class="font-semibold text-white">Gemi Takibi</h3>
            </div>
            <p class="text-sm text-white/50">VesselFinder üzerinden canlı gemi takibi</p>
            <a href="https://www.vesselfinder.com/" target="_blank" class="inline-flex items-center gap-1.5 mt-3 text-sm text-blue-400 hover:text-blue-300 transition">
                <i data-lucide="external-link" class="h-3.5 w-3.5"></i> VesselFinder'ı Aç
            </a>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/10"><i data-lucide="book-open" class="h-5 w-5 text-purple-400"></i></div>
                <h3 class="font-semibold text-white">Bilgi Bankası</h3>
            </div>
            <p class="text-sm text-white/50">İkmal prosedürleri, güvenlik kuralları ve iletişim bilgileri</p>
        </div>
    </div>

    <!-- Popular Ports -->
    <h2 class="text-lg font-semibold text-white mb-3">Sık Kullanılan Limanlar</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <?php
        $ports = [
            ['name' => 'Aliağa', 'city' => 'İzmir', 'type' => 'Petrokimya'],
            ['name' => 'Nemrut', 'city' => 'İzmir', 'type' => 'Genel'],
            ['name' => 'Tuzla', 'city' => 'İstanbul', 'type' => 'Tersane'],
            ['name' => 'Ambarlı', 'city' => 'İstanbul', 'type' => 'Konteyner'],
            ['name' => 'Mersin', 'city' => 'Mersin', 'type' => 'Uluslararası'],
            ['name' => 'İskenderun', 'city' => 'Hatay', 'type' => 'Demir Çelik'],
            ['name' => 'Yılport', 'city' => 'Gebze', 'type' => 'Konteyner'],
            ['name' => 'Gemlik', 'city' => 'Bursa', 'type' => 'Genel'],
        ];
        foreach ($ports as $port): ?>
        <div class="card p-4 hover:bg-white/[0.05] transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-500/10">
                    <i data-lucide="anchor" class="h-4 w-4 text-cyan-400"></i>
                </div>
                <div>
                    <div class="font-medium text-white text-sm"><?= $port['name'] ?></div>
                    <div class="text-xs text-white/40"><?= $port['city'] ?> • <?= $port['type'] ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
