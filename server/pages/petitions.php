<?php
/**
 * Asmira Ops - Dilekçeler Ana Sayfa
 * Orijinal React sayfasının birebir PHP karşılığı
 */
$pageTitle = 'Dilekçeler';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/petitions';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">DİLEKÇELER</div>
                <div class="text-3xl font-black tracking-tight">Belge Şablonları</div>
            </div>
            <button type="button" onclick="openAddCategoryModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-4 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Kategori Ekle
            </button>
        </div>

        <!-- Stats Bar -->
        <div class="relative flex flex-none items-center gap-3 px-6 py-2.5">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/40 via-cyan-400/20 to-transparent"></div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <div class="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                <span class="text-xs font-medium text-white/70">Toplam</span>
                <span class="text-sm font-bold text-white" id="totalCount">3</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <span class="text-xs font-medium text-white/70">Taahhütname</span>
                <span class="text-sm font-bold text-white">1</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <span class="text-xs font-medium text-white/70">Gümrük</span>
                <span class="text-sm font-bold text-white">1</span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-8">
                <!-- Taahhütnameler Section -->
                <section>
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/20">
                            <i data-lucide="file-check" class="h-4 w-4 text-blue-400"></i>
                        </div>
                        <h2 class="text-lg font-bold tracking-wide">Taahhütnameler</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <a href="/petitions/taahhutnameler" class="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-blue-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(59,130,246,0.15)]">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400 transition group-hover:bg-blue-500/30">
                                    <i data-lucide="file-check" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <div class="text-[16px] font-semibold">Taşıma Taahhütnameleri</div>
                                    <div class="text-sm text-white/50">Liman bazlı taahhütname şablonları</div>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="h-5 w-5 text-white/40 transition group-hover:text-white/70"></i>
                        </a>
                    </div>
                </section>

                <!-- Gümrük Dilekçeleri Section -->
                <section>
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20">
                            <i data-lucide="log-in" class="h-4 w-4 text-emerald-400"></i>
                        </div>
                        <h2 class="text-lg font-bold tracking-wide">Gümrük Dilekçeleri</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <a href="/petitions/gumruk-dilekceleri" class="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-emerald-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(52,211,153,0.15)]">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 transition group-hover:bg-emerald-500/30">
                                    <i data-lucide="log-in" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <div class="text-[16px] font-semibold">Gümrük Milli İkmal Dilekçeleri</div>
                                    <div class="text-sm text-white/50">Gümrük müdürlüğü dilekçe şablonları</div>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="h-5 w-5 text-white/40 transition group-hover:text-white/70"></i>
                        </a>
                    </div>
                </section>

                <!-- EK-1 Belgeleri Section -->
                <section>
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/20">
                            <i data-lucide="file" class="h-4 w-4 text-amber-400"></i>
                        </div>
                        <h2 class="text-lg font-bold tracking-wide">EK-1 Belgeleri</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <a href="/petitions/ek-1-belgeleri" class="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-amber-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(245,158,11,0.15)]">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-500/20 text-amber-400 transition group-hover:bg-amber-500/30">
                                    <i data-lucide="file" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <div class="text-[16px] font-semibold">EK-1 Belgeleri</div>
                                    <div class="text-sm text-white/50">Word dosyası yükle ve indir</div>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="h-5 w-5 text-white/40 transition group-hover:text-white/70"></i>
                        </a>
                    </div>
                </section>

                <!-- Özel Kategoriler -->
                <div id="customCategoriesSection"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('addCategoryModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-xl">
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-semibold">Yeni Kategori Ekle</h3>
            <button type="button" onclick="closeModal('addCategoryModal')" class="flex h-8 w-8 items-center justify-center rounded-lg text-white/50 hover:bg-white/10 hover:text-white"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-white/70">Kategori Başlığı <span class="text-red-400">*</span></label>
                <input type="text" id="catTitle" placeholder="Örn: Gümrük Transit Dilekçeleri" class="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white/70">Açıklama</label>
                <input type="text" id="catDesc" placeholder="Örn: Transit işlemleri için dilekçe şablonları" class="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white/70">İkon</label>
                <div class="grid grid-cols-6 gap-2" id="iconGrid"></div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('addCategoryModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/60 transition hover:bg-white/10 hover:text-white">Vazgeç</button>
            <button type="button" onclick="addCategory()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500"><i data-lucide="plus" class="h-4 w-4"></i>Kategori Oluştur</button>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('editCategoryModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-xl">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-bold">Kategori Bilgilerini Düzenle</h2>
            <button type="button" onclick="closeModal('editCategoryModal')" class="rounded-lg p-1 text-white/50 transition hover:bg-white/10 hover:text-white"><i data-lucide="x" class="h-5 w-5"></i></button>
        </div>
        <input type="hidden" id="editCatId">
        <div class="space-y-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-white/70">Kategori Başlığı</label>
                <input type="text" id="editCatTitle" placeholder="Örn: Gümrük Dilekçeleri" class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white outline-none placeholder:text-white/30 focus:border-purple-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white/70">Açıklama</label>
                <input type="text" id="editCatDesc" placeholder="Örn: Belge şablonları" class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white outline-none placeholder:text-white/30 focus:border-purple-500/50">
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeModal('editCategoryModal')" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-white/70 transition hover:bg-white/5">İptal</button>
            <button type="button" onclick="updateCategory()" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-500">Kaydet</button>
        </div>
    </div>
</div>

<script>
const ICON_OPTIONS = ['file-text','file-check','log-in','truck','ship','anchor'];
let categories = [];
let selectedIcon = 'file-text';

document.addEventListener('DOMContentLoaded', async () => {
    renderIconGrid();
    try {
        categories = await apiRequest('/api/petition-categories');
    } catch (e) { console.warn('Kategoriler yüklenemedi:', e); categories = []; }
    renderCustomCategories();
});

function renderIconGrid() {
    const grid = document.getElementById('iconGrid');
    grid.innerHTML = ICON_OPTIONS.map(icon => `
        <button type="button" onclick="selectIcon('${icon}')" id="icon-${icon}" class="flex h-10 w-10 items-center justify-center rounded-lg border transition ${selectedIcon===icon ? 'border-cyan-500 bg-cyan-500/20 text-cyan-400' : 'border-white/10 bg-white/5 text-white/50 hover:bg-white/10 hover:text-white'}">
            <i data-lucide="${icon}" class="h-5 w-5"></i>
        </button>
    `).join('');
    lucide.createIcons({nodes:[grid]});
}

function selectIcon(icon) {
    selectedIcon = icon;
    renderIconGrid();
}

function generateSlug(title) {
    return title.toLowerCase().replace(/ğ/g,'g').replace(/ü/g,'u').replace(/ş/g,'s').replace(/ı/g,'i').replace(/ö/g,'o').replace(/ç/g,'c').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
}

function renderCustomCategories() {
    const section = document.getElementById('customCategoriesSection');
    if (categories.length === 0) { section.innerHTML = ''; return; }

    let html = '';
    categories.forEach(c => {
        const iconName = c.icon || 'file-text';
        html += `<section class="mt-8">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-500/20"><i data-lucide="${iconName}" class="h-4 w-4 text-purple-400"></i></div>
                <h2 class="text-lg font-bold tracking-wide">${escapeHtml(c.title)}</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="group relative flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-purple-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(168,85,247,0.15)]" onclick="window.location.href='/petitions/${c.slug}'">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/20 text-purple-400 transition group-hover:bg-purple-500/30"><i data-lucide="${iconName}" class="h-6 w-6"></i></div>
                        <div>
                            <div class="text-[16px] font-semibold">${escapeHtml(c.title)}</div>
                            <div class="text-sm text-white/50">${escapeHtml(c.description || 'Belge şablonları')}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="event.stopPropagation();openEditCategory('${c.id}')" class="flex h-8 w-8 items-center justify-center rounded-lg text-white/30 hover:text-white hover:bg-white/10 transition opacity-0 group-hover:opacity-100"><i data-lucide="edit-3" class="h-4 w-4"></i></button>
                        <button type="button" onclick="event.stopPropagation();deleteCategory('${c.id}','${escapeHtml(c.title)}')" class="flex h-8 w-8 items-center justify-center rounded-lg text-white/30 hover:text-red-400 hover:bg-red-500/10 transition opacity-0 group-hover:opacity-100"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                        <i data-lucide="chevron-right" class="h-5 w-5 text-white/40 transition group-hover:text-white/70"></i>
                    </div>
                </div>
            </div>
        </section>`;
    });
    section.innerHTML = html;
    lucide.createIcons({nodes:[section]});
    document.getElementById('totalCount').textContent = 3 + categories.length;
}

function openAddCategoryModal() {
    document.getElementById('catTitle').value = '';
    document.getElementById('catDesc').value = '';
    selectedIcon = 'file-text';
    renderIconGrid();
    openModal('addCategoryModal');
    lucide.createIcons();
}

async function addCategory() {
    const title = document.getElementById('catTitle').value.trim();
    const description = document.getElementById('catDesc').value.trim();
    if (!title) { showToast('Lütfen kategori başlığı girin', 'error'); return; }
    try {
        const slug = generateSlug(title);
        const res = await apiRequest('/api/petition-categories', {
            method: 'POST', body: JSON.stringify({ title, description: description || 'Belge şablonları', icon: selectedIcon, slug })
        });
        categories.push({ id: res.id || ('cat_local_' + Date.now()), title, description: description || 'Belge şablonları', icon: selectedIcon, slug });
        closeModal('addCategoryModal');
        showToast('Kategori eklendi');
        renderCustomCategories();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

function openEditCategory(id) {
    const c = categories.find(x => x.id === id);
    if (!c) return;
    document.getElementById('editCatId').value = c.id;
    document.getElementById('editCatTitle').value = c.title;
    document.getElementById('editCatDesc').value = c.description || '';
    openModal('editCategoryModal');
    lucide.createIcons();
}

async function updateCategory() {
    const id = document.getElementById('editCatId').value;
    const title = document.getElementById('editCatTitle').value.trim();
    const description = document.getElementById('editCatDesc').value.trim();
    if (!title) { showToast('Lütfen kategori başlığı girin', 'error'); return; }
    try {
        await apiRequest('/api/petition-categories', {
            method: 'PUT', body: JSON.stringify({ id, title, description: description || 'Belge şablonları' })
        });
        const cat = categories.find(c => c.id === id);
        if (cat) { cat.title = title; cat.description = description || 'Belge şablonları'; }
        closeModal('editCategoryModal');
        showToast('Kategori güncellendi');
        renderCustomCategories();
    } catch (e) { showToast('Güncelleme hatası: ' + e.message, 'error'); }
}

async function deleteCategory(id, title) {
    if (!confirmAction(`"${title}" kategorisini silmek istediğinize emin misiniz?`)) return;
    try {
        await apiRequest('/api/petition-categories', { method: 'DELETE', body: JSON.stringify({ id }) });
        categories = categories.filter(c => c.id !== id);
        renderCustomCategories();
        showToast('Kategori silindi');
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
