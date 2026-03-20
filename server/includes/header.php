<?php
/**
 * Asmira Ops - Header (HTML head + Sidebar)
 * Tüm korumalı sayfalarda include edilir.
 */
require_once __DIR__ . '/auth.php';
requireAuth();

$currentUser = getCurrentUser();
$currentPage = $GLOBALS['currentPage'] ?? '';

// Navigasyon yapısı
$navSections = [
    [
        'title' => 'Ana Menü',
        'items' => [
            ['title' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'layout-dashboard'],
            [
                'title' => 'Araç Evrakları', 'href' => '/vehicle-documents', 'icon' => 'truck',
                'children' => [
                    ['title' => 'Asmira Özmal', 'href' => '/vehicle-documents/asmira'],
                    ['title' => 'Tedarikçi Araçları', 'href' => '/vehicle-documents/suppliers'],
                ]
            ],
            [
                'title' => 'Şoför Evrakları', 'href' => '/driver-documents', 'icon' => 'user-check',
                'children' => [
                    ['title' => 'Asmira Şoförler', 'href' => '/driver-documents/asmira'],
                    ['title' => 'Tedarikçi Şoförler', 'href' => '/driver-documents/suppliers'],
                ]
            ],
            ['title' => 'Teslimatçı Evrakları', 'href' => '/delivery-documents', 'icon' => 'package'],
            ['title' => 'Evrak Paketi', 'href' => '/document-package', 'icon' => 'package-check'],
            ['title' => 'Dilekçeler', 'href' => '/petitions', 'icon' => 'file-text'],
            [
                'title' => 'Raporlar', 'href' => '/reports', 'icon' => 'bar-chart-3',
                'children' => [
                    ['title' => 'Evrak Takibi', 'href' => '/reports/document-tracking'],
                    ['title' => 'İkmaller', 'href' => '/reports/operations'],
                ]
            ],
            ['title' => 'Port Wiki', 'href' => '/port-wiki', 'icon' => 'book-open'],
            ['title' => 'Ayarlar', 'href' => '/settings', 'icon' => 'settings', 'adminOnly' => true],
        ]
    ]
];

function isActivePage(string $href, string $currentPage): bool {
    if ($href === '/') return $currentPage === '/';
    return $currentPage === $href || str_starts_with($currentPage, $href . '/');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Asmira Ops - <?= htmlspecialchars($pageTitle ?? 'Operasyon Yönetimi') ?></title>
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b1120">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Asmira Ops">
    <link rel="apple-touch-icon" href="/assets/img/icon-192.png">
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: { 900: '#0b1120', 800: '#0f172a', 700: '#111827', 600: '#1e293b' }
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/app.css?v=20260318">
    <!-- Date picker icon beyaz - inline garantili -->
    <style>
        input[type="date"], input[type="datetime-local"] { color-scheme: dark; }
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(2) !important;
            cursor: pointer;
            opacity: 1 !important;
        }
    </style>
    <!-- Shared JS -->
    <script src="/assets/js/app.js?v=20260316b"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0b1120] via-[#0f172a] to-[#0b1120] text-white">

<!-- Mobile Header -->
<header class="fixed left-0 right-0 top-0 z-50 flex h-14 items-center justify-between border-b border-white/10 bg-[#0b1120]/95 px-4 backdrop-blur-md lg:hidden">
    <div class="flex items-center gap-3">
        <img src="/assets/img/asmira-energy-logo.png" alt="Asmira Energy" class="h-8 w-auto object-contain">
    </div>
    <button type="button" onclick="toggleMobileMenu()" class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white" id="mobileMenuBtn">
        <i data-lucide="menu" class="h-5 w-5"></i>
    </button>
</header>

<!-- Mobile Menu Overlay -->
<div id="mobileOverlay" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden hidden" onclick="toggleMobileMenu()"></div>

<!-- Mobile Menu Panel -->
<div id="mobileMenu" class="fixed right-0 top-0 z-50 h-full w-72 translate-x-full transform bg-[#0b1120] shadow-2xl transition-transform duration-300 ease-in-out lg:hidden">
    <div class="flex h-14 items-center justify-between border-b border-white/10 px-4">
        <span class="text-sm font-semibold text-white/70">Menü</span>
        <button type="button" onclick="toggleMobileMenu()" class="flex h-8 w-8 items-center justify-center rounded-lg text-white/60 hover:bg-white/10 hover:text-white">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <?php foreach ($navSections as $section): ?>
        <div class="mb-4">
            <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/30"><?= $section['title'] ?></div>
            <div class="space-y-1">
                <?php foreach ($section['items'] as $item):
                    if (!empty($item['adminOnly']) && !isManagerOrAdmin()) continue;
                    $active = isActivePage($item['href'], $currentPage);
                ?>
                    <?php if (!empty($item['children'])): ?>
                    <div>
                        <button type="button" onclick="toggleSubmenu(this)" class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors <?= $active ? 'bg-blue-500/10 text-blue-400' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                            <i data-lucide="<?= $item['icon'] ?>" class="h-5 w-5 shrink-0"></i>
                            <span class="flex-1 text-left"><?= $item['title'] ?></span>
                            <i data-lucide="chevron-down" class="h-4 w-4 transition-transform <?= $active ? '' : '-rotate-90' ?>"></i>
                        </button>
                        <div class="ml-8 mt-1 space-y-1 border-l border-white/10 pl-3 submenu <?= $active ? '' : 'hidden' ?>">
                            <?php foreach ($item['children'] as $child):
                                $childActive = isActivePage($child['href'], $currentPage);
                            ?>
                            <a href="<?= $child['href'] ?>" class="block rounded-lg px-3 py-2 text-sm transition-colors <?= $childActive ? 'bg-blue-500/10 text-blue-400' : 'text-white/60 hover:bg-white/5 hover:text-white' ?>"><?= $child['title'] ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="<?= $item['href'] ?>" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors <?= $active ? 'bg-blue-500/10 text-blue-400' : 'text-white/70 hover:bg-white/5 hover:text-white' ?>">
                        <i data-lucide="<?= $item['icon'] ?>" class="h-5 w-5 shrink-0"></i>
                        <span><?= $item['title'] ?></span>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </nav>
    <!-- Mobile User -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-white/10 bg-[#0b1120] p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20">
                <i data-lucide="user" class="h-5 w-5 text-blue-400"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-semibold text-white"><?= htmlspecialchars($currentUser['name']) ?></div>
                <div class="text-xs text-white/50"><?= $currentUser['role'] === 'admin' ? 'Yönetici' : 'Kullanıcı' ?></div>
            </div>
            <a href="/logout" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/50 hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400" title="Çıkış Yap">
                <i data-lucide="log-out" class="h-4 w-4"></i>
            </a>
        </div>
    </div>
</div>

<div class="mx-auto flex min-h-screen w-full">
    <!-- Desktop Sidebar -->
    <aside class="sticky top-0 hidden h-screen w-72 shrink-0 flex-col bg-[#0b1120]/80 text-white shadow-[4px_0_24px_-2px_rgba(0,0,0,0.5)] backdrop-blur-md lg:flex">
        <!-- Logo -->
        <div class="relative flex w-full flex-col items-center justify-center px-4 pb-1 pt-5">
            <img src="/assets/img/asmira-energy-logo.png" alt="Asmira Energy" class="w-full max-w-[180px] object-contain">
            <!-- Ocean Scene -->
            <div class="relative mt-2 h-28 w-full overflow-hidden rounded-lg" style="background:linear-gradient(to bottom, #060d1a 0%, #0a1628 40%, #0c2040 100%);">
                <!-- Moon -->
                <div class="sidebar-moon absolute rounded-full" style="top:6px;right:18px;width:12px;height:12px;background:radial-gradient(circle at 35% 35%, #e8f0ff, #8aa0c0);border-radius:50%;"></div>
                <!-- Moon reflection on water -->
                <div class="absolute" style="bottom:0;right:22px;width:4px;height:22px;background:linear-gradient(to bottom, rgba(200,220,255,0.12), transparent);filter:blur(3px);border-radius:2px;"></div>

                <!-- Stars (brighter, twinkling) -->
                <div class="sidebar-star absolute rounded-full bg-white" style="left:10%;top:8%;width:2.5px;height:2.5px;--dur:2.5s;--delay:0s;"></div>
                <div class="sidebar-star absolute rounded-full bg-white" style="left:30%;top:4%;width:1.5px;height:1.5px;--dur:3.5s;--delay:0.8s;"></div>
                <div class="sidebar-star absolute rounded-full bg-cyan-200" style="left:50%;top:12%;width:2px;height:2px;--dur:3s;--delay:1.5s;"></div>
                <div class="sidebar-star absolute rounded-full bg-white" style="left:70%;top:6%;width:2px;height:2px;--dur:4s;--delay:0.3s;"></div>
                <div class="sidebar-star absolute rounded-full bg-white" style="left:88%;top:14%;width:1.5px;height:1.5px;--dur:2.8s;--delay:2s;"></div>
                <div class="sidebar-star absolute rounded-full bg-cyan-100" style="left:22%;top:16%;width:1.5px;height:1.5px;--dur:3.2s;--delay:1.2s;"></div>
                <div class="sidebar-star absolute rounded-full bg-white" style="left:62%;top:3%;width:1.5px;height:1.5px;--dur:4.5s;--delay:0.6s;"></div>
                <div class="sidebar-star absolute rounded-full bg-white" style="left:42%;top:18%;width:1px;height:1px;--dur:3.8s;--delay:2.5s;"></div>

                <!-- Lighthouse -->
                <div class="absolute" style="bottom:10px;right:10%;z-index:3;">
                    <svg width="12" height="38" viewBox="0 0 12 38">
                        <polygon points="3,8 9,8 10,36 2,36" fill="#14253d" stroke="#1e3a58" stroke-width="0.5"/>
                        <rect x="1" y="34" width="10" height="4" rx="1" fill="#10203a" stroke="#1e3a58" stroke-width="0.4"/>
                        <rect x="3.5" y="5" width="5" height="4" rx="1" fill="#14253d" stroke="#1e3a58" stroke-width="0.4"/>
                        <circle cx="6" cy="6.5" r="2" fill="#ffdd77" style="animation: lighthouseLantern 6s ease-in-out infinite;">
                        </circle>
                        <circle cx="6" cy="6.5" r="4" fill="#ffdd77" opacity="0.08"/>
                        <rect x="3" y="14" width="6" height="1.5" fill="rgba(192,57,43,0.5)" rx="0.3"/>
                        <rect x="3.2" y="22" width="5.6" height="1.5" fill="rgba(192,57,43,0.5)" rx="0.3"/>
                        <rect x="3.5" y="30" width="5" height="1.5" fill="rgba(192,57,43,0.5)" rx="0.3"/>
                        <rect x="4" y="16" width="4" height="2.5" rx="0.3" fill="#060e1a" stroke="#1e3a58" stroke-width="0.3"/>
                        <rect x="4.2" y="24" width="3.6" height="2.5" rx="0.3" fill="#060e1a" stroke="#1e3a58" stroke-width="0.3"/>
                    </svg>
                </div>

                <!-- Ship 1 - Large tanker -->
                <div class="ship-bob absolute" style="bottom:10px">
                    <div class="relative">
                        <svg width="90" height="50" viewBox="0 0 60 34" fill="none">
                            <!-- Hull -->
                            <path d="M2,26 L7,32 H53 L58,26 Z" fill="rgba(96,165,250,0.18)" stroke="rgba(96,165,250,0.55)" stroke-width="0.8"/>
                            <line x1="8" y1="28" x2="52" y2="28" stroke="rgba(96,165,250,0.25)" stroke-width="0.4"/>
                            <!-- Portholes -->
                            <circle cx="14" cy="24" r="1" fill="rgba(6,10,20,0.8)" stroke="rgba(96,165,250,0.4)" stroke-width="0.4"/>
                            <circle cx="20" cy="24" r="1" fill="rgba(6,10,20,0.8)" stroke="rgba(96,165,250,0.4)" stroke-width="0.4"/>
                            <circle cx="40" cy="24" r="1" fill="rgba(6,10,20,0.8)" stroke="rgba(96,165,250,0.4)" stroke-width="0.4"/>
                            <circle cx="46" cy="24" r="1" fill="rgba(6,10,20,0.8)" stroke="rgba(96,165,250,0.4)" stroke-width="0.4"/>
                            <!-- Cabin -->
                            <rect x="10" y="18" width="34" height="8" rx="0.8" fill="rgba(96,165,250,0.1)" stroke="rgba(96,165,250,0.35)" stroke-width="0.7"/>
                            <!-- Rear cabin -->
                            <rect x="46" y="20" width="8" height="6" rx="0.5" fill="rgba(96,165,250,0.08)" stroke="rgba(96,165,250,0.3)" stroke-width="0.5"/>
                            <!-- Bridge -->
                            <rect x="16" y="10" width="18" height="8" rx="1" fill="rgba(96,165,250,0.08)" stroke="rgba(96,165,250,0.35)" stroke-width="0.6"/>
                            <!-- Bridge windows -->
                            <rect class="ship-window" x="18" y="12" width="4" height="3" rx="0.3" fill="rgba(251,191,36,0.45)"/>
                            <rect class="ship-window-2" x="24" y="12" width="4" height="3" rx="0.3" fill="rgba(96,165,250,0.25)"/>
                            <rect x="30" y="12" width="2.5" height="3" rx="0.3" fill="rgba(251,191,36,0.2)"/>
                            <!-- Cabin windows -->
                            <rect x="13" y="20" width="3" height="2" rx="0.2" fill="rgba(96,165,250,0.2)"/>
                            <rect x="18" y="20" width="3" height="2" rx="0.2" fill="rgba(251,191,36,0.2)"/>
                            <rect x="23" y="20" width="3" height="2" rx="0.2" fill="rgba(96,165,250,0.2)"/>
                            <rect x="28" y="20" width="3" height="2" rx="0.2" fill="rgba(251,191,36,0.15)"/>
                            <rect x="33" y="20" width="3" height="2" rx="0.2" fill="rgba(96,165,250,0.2)"/>
                            <rect x="38" y="20" width="3" height="2" rx="0.2" fill="rgba(96,165,250,0.2)"/>
                            <!-- Funnel -->
                            <rect x="35" y="5" width="5" height="5" rx="0.5" fill="rgba(96,165,250,0.12)" stroke="rgba(96,165,250,0.3)" stroke-width="0.5"/>
                            <rect x="35" y="7" width="5" height="1.5" fill="rgba(239,68,68,0.4)"/>
                            <!-- Mast -->
                            <line x1="26" y1="3" x2="26" y2="10" stroke="rgba(148,163,184,0.35)" stroke-width="0.5"/>
                            <circle cx="26" cy="3" r="1.2" fill="rgba(239,68,68,0.6)"/>
                            <circle cx="26" cy="3" r="2.5" fill="rgba(239,68,68,0.1)"/>
                            <!-- Bow -->
                            <line x1="53" y1="21" x2="58" y2="26" stroke="rgba(96,165,250,0.35)" stroke-width="0.5"/>
                        </svg>
                        <!-- Smoke -->
                        <div class="ship-smoke absolute" style="left:54px;top:3px;width:5px;height:5px;border-radius:50%;background:rgba(148,163,184,0.4)"></div>
                        <div class="ship-smoke-2 absolute" style="left:56px;top:4px;width:4px;height:4px;border-radius:50%;background:rgba(148,163,184,0.3)"></div>
                        <div class="ship-smoke-3 absolute" style="left:55px;top:2px;width:4.5px;height:4.5px;border-radius:50%;background:rgba(148,163,184,0.35)"></div>
                        <!-- Wake -->
                        <div class="ship-wake absolute" style="left:-12px;bottom:4px;height:2px;width:18px;border-radius:9999px;background:linear-gradient(to left,rgba(34,211,238,0.25),transparent)"></div>
                        <div class="absolute" style="left:-8px;bottom:7px;height:1.5px;width:12px;border-radius:9999px;background:linear-gradient(to left,rgba(34,211,238,0.15),transparent)"></div>
                        <!-- Water reflection -->
                        <div class="absolute" style="bottom:-5px;left:8%;right:8%;height:6px;border-radius:9999px;background:rgba(96,165,250,0.12);filter:blur(5px)"></div>
                    </div>
                </div>

                <!-- Ship 2 - Smaller vessel (opposite direction) -->
                <div class="ship-bob-2 absolute" style="bottom:14px">
                    <div class="relative">
                        <svg width="60" height="36" viewBox="0 0 38 22" fill="none" style="transform:scaleX(-1)">
                            <path d="M2,18 L4,20 H34 L36,18 Z" fill="rgba(6,182,212,0.15)" stroke="rgba(6,182,212,0.45)" stroke-width="0.7"/>
                            <rect x="6" y="13" width="24" height="5" rx="0.5" fill="rgba(6,182,212,0.1)" stroke="rgba(6,182,212,0.3)" stroke-width="0.6"/>
                            <rect x="11" y="7" width="12" height="6" rx="0.8" fill="rgba(6,182,212,0.07)" stroke="rgba(6,182,212,0.28)" stroke-width="0.5"/>
                            <rect class="ship-window" x="13" y="9" width="3" height="2" rx="0.3" fill="rgba(251,191,36,0.4)"/>
                            <rect class="ship-window-2" x="18" y="9" width="3" height="2" rx="0.3" fill="rgba(6,182,212,0.2)"/>
                            <!-- Cabin windows -->
                            <rect x="9" y="14.5" width="2.5" height="1.5" rx="0.2" fill="rgba(6,182,212,0.18)"/>
                            <rect x="13" y="14.5" width="2.5" height="1.5" rx="0.2" fill="rgba(251,191,36,0.15)"/>
                            <rect x="17" y="14.5" width="2.5" height="1.5" rx="0.2" fill="rgba(6,182,212,0.18)"/>
                            <rect x="21" y="14.5" width="2.5" height="1.5" rx="0.2" fill="rgba(6,182,212,0.18)"/>
                            <rect x="25" y="9" width="3" height="4" rx="0.3" fill="rgba(6,182,212,0.1)" stroke="rgba(6,182,212,0.25)" stroke-width="0.4"/>
                            <line x1="17" y1="3" x2="17" y2="7" stroke="rgba(148,163,184,0.28)" stroke-width="0.4"/>
                            <circle cx="17" cy="3" r="0.8" fill="rgba(52,211,153,0.5)"/>
                            <circle cx="17" cy="3" r="1.8" fill="rgba(52,211,153,0.08)"/>
                        </svg>
                        <div class="ship-smoke absolute" style="left:10px;top:6px;width:3.5px;height:3.5px;border-radius:50%;background:rgba(148,163,184,0.3)"></div>
                        <div class="ship-smoke-2 absolute" style="left:11px;top:7px;width:3px;height:3px;border-radius:50%;background:rgba(148,163,184,0.2)"></div>
                        <div class="ship-wake absolute" style="right:-10px;bottom:2px;height:1.5px;width:14px;border-radius:9999px;background:linear-gradient(to right,rgba(34,211,238,0.2),transparent)"></div>
                        <div class="absolute" style="bottom:-4px;left:10%;right:10%;height:5px;border-radius:9999px;background:rgba(34,211,238,0.08);filter:blur(4px)"></div>
                    </div>
                </div>

                <!-- Waves (3 layers, more visible) -->
                <svg class="wave-1 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style="height:22px;z-index:4;">
                    <path d="M0,12 C100,4 200,20 300,12 C400,4 500,20 600,12 C700,4 800,20 900,12 C1000,4 1100,20 1200,12 L1200,30 L0,30Z" fill="rgba(6,182,212,0.15)"/>
                    <path d="M0,12 C100,4 200,20 300,12 C400,4 500,20 600,12 C700,4 800,20 900,12 C1000,4 1100,20 1200,12" stroke="rgba(6,182,212,0.35)" stroke-width="0.8" fill="none"/>
                </svg>
                <svg class="wave-2 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style="height:16px;z-index:2;">
                    <path d="M0,12 C150,20 250,4 400,12 C550,20 650,4 800,12 C950,20 1050,4 1200,12 L1200,30 L0,30Z" fill="rgba(59,130,246,0.12)"/>
                    <path d="M0,12 C150,20 250,4 400,12 C550,20 650,4 800,12 C950,20 1050,4 1200,12" stroke="rgba(59,130,246,0.2)" stroke-width="0.5" fill="none"/>
                </svg>
                <svg class="wave-3 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style="height:10px;z-index:1;">
                    <path d="M0,12 C200,18 300,6 500,12 C700,18 800,6 1000,12 C1100,15 1150,9 1200,12 L1200,30 L0,30Z" fill="rgba(6,182,212,0.08)"/>
                </svg>

                <!-- Ocean floor gradient overlay -->
                <div class="absolute bottom-0 left-0 right-0 h-8" style="background:linear-gradient(to top, #0b1120, transparent);z-index:5;pointer-events:none;"></div>
            </div>
        </div>

        <div class="mx-3 h-px bg-gradient-to-r from-transparent via-white/[0.06] to-transparent"></div>

        <!-- Navigation -->
        <nav class="flex flex-1 flex-col overflow-y-auto px-3 pb-24 pt-2">
            <?php foreach ($navSections as $section): ?>
            <div class="mt-2">
                <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/30"><?= $section['title'] ?></div>
                <div class="space-y-0.5">
                    <?php foreach ($section['items'] as $item):
                        if (!empty($item['adminOnly']) && !isManagerOrAdmin()) continue;
                        $active = isActivePage($item['href'], $currentPage);
                    ?>
                        <?php if (!empty($item['children'])): ?>
                        <div>
                            <div class="group relative">
                                <button type="button" onclick="toggleSubmenu(this)" class="relative flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-semibold uppercase tracking-[0.12em] transition-all duration-300 <?= $active ? 'text-blue-400' : 'text-white/60 hover:text-white' ?>">
                                    <?php if ($active): ?>
                                    <div class="absolute left-0 top-1/2 -translate-y-1/2">
                                        <div class="h-6 w-[3px] rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.6)]"></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="pointer-events-none absolute inset-0 rounded-lg transition-all duration-300 <?= $active ? 'bg-gradient-to-r from-blue-500/[0.08] via-blue-500/[0.04] to-transparent' : 'bg-transparent group-hover:bg-white/[0.04]' ?>"></div>
                                    <i data-lucide="<?= $item['icon'] ?>" class="relative h-[18px] w-[18px] shrink-0 <?= $active ? 'text-blue-400' : 'text-white/40 group-hover:text-white/80' ?>"></i>
                                    <span class="relative min-w-0 flex-1 truncate text-left"><?= $item['title'] ?></span>
                                    <i data-lucide="chevron-down" class="relative h-3.5 w-3.5 shrink-0 transition-transform duration-300 <?= $active ? 'text-blue-400/60' : 'text-white/30 -rotate-90' ?> submenu-chevron"></i>
                                </button>
                            </div>
                            <div class="mt-0.5 space-y-0.5 overflow-hidden pl-5 submenu <?= $active ? '' : 'hidden' ?>">
                                <div class="ml-[9px] border-l border-white/[0.06]">
                                    <?php foreach ($item['children'] as $child):
                                        $childActive = isActivePage($child['href'], $currentPage);
                                    ?>
                                    <a href="<?= $child['href'] ?>" class="group/child relative flex items-center rounded-r-lg py-2 pl-4 pr-3 text-[12px] font-medium uppercase tracking-[0.1em] transition-all duration-300 <?= $childActive ? 'text-blue-400' : 'text-white/45 hover:text-white/80' ?>">
                                        <?php if ($childActive): ?>
                                        <div class="absolute left-0 top-1/2 -translate-x-px -translate-y-1/2">
                                            <div class="h-4 w-[2px] rounded-full bg-blue-400 shadow-[0_0_6px_rgba(96,165,250,0.5)]"></div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="pointer-events-none absolute inset-0 rounded-r-lg transition-all duration-300 <?= $childActive ? 'bg-gradient-to-r from-blue-500/[0.06] to-transparent' : 'group-hover/child:bg-white/[0.03]' ?>"></div>
                                        <span class="relative truncate"><?= $child['title'] ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="group relative">
                            <a href="<?= $item['href'] ?>" class="relative flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-semibold uppercase tracking-[0.12em] transition-all duration-300 <?= $active ? 'text-blue-400' : 'text-white/60 hover:text-white' ?>">
                                <?php if ($active): ?>
                                <div class="absolute left-0 top-1/2 -translate-y-1/2">
                                    <div class="h-6 w-[3px] rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.6)]"></div>
                                </div>
                                <?php endif; ?>
                                <div class="pointer-events-none absolute inset-0 rounded-lg transition-all duration-300 <?= $active ? 'bg-gradient-to-r from-blue-500/[0.08] via-blue-500/[0.04] to-transparent' : 'bg-transparent group-hover:bg-white/[0.04]' ?>"></div>
                                <i data-lucide="<?= $item['icon'] ?>" class="relative h-[18px] w-[18px] shrink-0 <?= $active ? 'text-blue-400' : 'text-white/40 group-hover:text-white/80' ?>"></i>
                                <span class="relative truncate"><?= $item['title'] ?></span>
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </nav>

        <!-- User Section -->
        <div class="absolute bottom-0 left-0 right-0 border-t border-white/[0.06] bg-[#0b1120]/95 px-3 py-2.5 backdrop-blur-md">
            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20">
                        <i data-lucide="user" class="relative h-4 w-4 text-blue-400"></i>
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 border-[#0b1120] bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.5)]"></div>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-[13px] font-semibold text-white"><?= htmlspecialchars($currentUser['name']) ?></div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] text-emerald-400 font-medium">Bağlı</span>
                    </div>
                </div>
                <a href="/logout" class="group/logout flex h-7 w-7 items-center justify-center rounded-lg border border-white/[0.06] bg-white/[0.02] text-white/30 transition-all duration-300 hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400" title="Çıkış Yap">
                    <i data-lucide="log-out" class="h-3 w-3"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex min-w-0 flex-1 flex-col">
        <main class="min-w-0 flex-1 px-0 pb-4 pt-16 sm:px-4 lg:px-6 lg:pb-4 lg:pt-6">
            <div class="w-full">
