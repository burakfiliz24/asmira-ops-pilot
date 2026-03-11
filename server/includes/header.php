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
            ['title' => 'Şoför Evrakları', 'href' => '/driver-documents', 'icon' => 'user-check'],
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmira Ops - <?= htmlspecialchars($pageTitle ?? 'Operasyon Yönetimi') ?></title>
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
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
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0b1120] via-[#0f172a] to-[#0b1120] text-white">

<!-- Mobile Header -->
<header class="fixed left-0 right-0 top-0 z-50 flex h-14 items-center justify-between border-b border-white/10 bg-[#0b1120]/95 px-4 backdrop-blur-md lg:hidden">
    <div class="flex items-center gap-3">
        <img src="/assets/img/asmira-marine-logo.png" alt="Asmira Marine" class="h-8 w-auto object-contain brightness-0 invert">
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
                    if (!empty($item['adminOnly']) && !isAdmin()) continue;
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
            <img src="/assets/img/asmira-energy-logo.png" alt="Asmira Energy" class="w-full max-w-[240px] object-contain">
            <!-- Ocean Scene -->
            <div class="relative mt-2 h-20 w-full overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#0b1120]/40"></div>
                <!-- Waves -->
                <svg class="wave-1 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style="height:18px">
                    <path d="M0,12 C100,4 200,20 300,12 C400,4 500,20 600,12 C700,4 800,20 900,12 C1000,4 1100,20 1200,12 L1200,30 L0,30Z" fill="rgba(6,182,212,0.12)"/>
                    <path d="M0,12 C100,4 200,20 300,12 C400,4 500,20 600,12 C700,4 800,20 900,12 C1000,4 1100,20 1200,12" stroke="rgba(6,182,212,0.3)" stroke-width="1" fill="none"/>
                </svg>
                <svg class="wave-2 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style="height:14px">
                    <path d="M0,12 C150,20 250,4 400,12 C550,20 650,4 800,12 C950,20 1050,4 1200,12 L1200,30 L0,30Z" fill="rgba(59,130,246,0.1)"/>
                </svg>
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
                        if (!empty($item['adminOnly']) && !isAdmin()) continue;
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
        <main class="min-w-0 flex-1 px-0 pb-4 pt-16 sm:px-4 lg:px-6 lg:pt-6">
            <div class="w-full">
