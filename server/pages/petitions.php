<?php
/**
 * Asmira Ops - Dilekçeler Ana Sayfa
 */
$pageTitle = 'Dilekçeler';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/petitions';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Dilekçeler</h1>
            <p class="text-sm text-white/50 mt-1">Dilekçe kategorileri ve şablonları</p>
        </div>
        <button onclick="openModal('addCategoryModal')" class="btn btn-primary">
            <i data-lucide="plus" class="h-4 w-4"></i> Yeni Kategori
        </button>
    </div>

    <!-- Default Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <a href="/petitions/gumruk-dilekceleri" class="card p-5 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10"><i data-lucide="file-text" class="h-5 w-5 text-blue-400"></i></div>
                <h3 class="font-semibold text-white group-hover:text-blue-400 transition">Gümrük Dilekçeleri</h3>
            </div>
            <p class="text-sm text-white/40">Gümrük işlemleri için gerekli dilekçeler</p>
        </a>
        <a href="/petitions/taahhutnameler" class="card p-5 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10"><i data-lucide="file-check" class="h-5 w-5 text-emerald-400"></i></div>
                <h3 class="font-semibold text-white group-hover:text-emerald-400 transition">Taahhütnameler</h3>
            </div>
            <p class="text-sm text-white/40">Taahhütname belgeleri</p>
        </a>
        <a href="/petitions/ek-1-belgeleri" class="card p-5 hover:bg-white/[0.05] transition-all group">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/10"><i data-lucide="file-text" class="h-5 w-5 text-purple-400"></i></div>
                <h3 class="font-semibold text-white group-hover:text-purple-400 transition">Ek-1 Belgeleri</h3>
            </div>
            <p class="text-sm text-white/40">Ek-1 formları ve belgeleri</p>
        </a>
    </div>

    <!-- Custom Categories -->
    <h2 class="text-lg font-semibold text-white mb-3">Özel Kategoriler</h2>
    <div id="customCategories" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="flex items-center justify-center py-8"><div class="spinner"></div></div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('addCategoryModal')"></div>
    <div class="relative mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div class="text-lg font-semibold">Yeni Kategori</div>
            <button onclick="closeModal('addCategoryModal')" class="h-9 w-9 flex items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <form onsubmit="addCategory(event)" class="space-y-3 px-5 py-4">
            <div><label class="block text-xs font-medium text-white/50 mb-1">Başlık *</label><input type="text" id="catTitle" class="input-field" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">Açıklama</label><input type="text" id="catDesc" class="input-field"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addCategoryModal')" class="btn btn-ghost flex-1">İptal</button>
                <button type="submit" class="btn btn-primary flex-1"><i data-lucide="check" class="h-4 w-4"></i> Kaydet</button>
            </div>
        </form>
    </div>
</div>

<script>
let categories = [];

document.addEventListener('DOMContentLoaded', async () => {
    try {
        categories = await apiRequest('/api/petition-categories');
        renderCategories();
    } catch (e) { console.warn('Kategoriler yüklenemedi:', e); renderCategories(); }
});

function generateSlug(title) {
    return title.toLowerCase().replace(/ğ/g,'g').replace(/ü/g,'u').replace(/ş/g,'s').replace(/ı/g,'i').replace(/ö/g,'o').replace(/ç/g,'c').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
}

function renderCategories() {
    const container = document.getElementById('customCategories');
    if (categories.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-8 text-white/40"><p>Henüz özel kategori eklenmemiş</p></div>';
        return;
    }
    let html = '';
    categories.forEach(c => {
        html += `<div class="card p-5 hover:bg-white/[0.05] transition-all group relative">
            <button onclick="deleteCategory('${c.id}')" class="absolute top-3 right-3 h-7 w-7 flex items-center justify-center rounded-lg text-white/20 hover:text-red-400 hover:bg-red-500/10 opacity-0 group-hover:opacity-100 transition"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
            <a href="/petitions/${c.slug}" class="block">
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-500/10"><i data-lucide="file-text" class="h-5 w-5 text-cyan-400"></i></div>
                    <h3 class="font-semibold text-white group-hover:text-cyan-400 transition">${escapeHtml(c.title)}</h3>
                </div>
                <p class="text-sm text-white/40">${escapeHtml(c.description || '')}</p>
            </a>
        </div>`;
    });
    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}

async function addCategory(e) {
    e.preventDefault();
    const title = document.getElementById('catTitle').value.trim();
    const description = document.getElementById('catDesc').value.trim();
    if (!title) return;
    try {
        const slug = generateSlug(title);
        await apiRequest('/api/petition-categories', {
            method: 'POST', body: JSON.stringify({ title, description, icon: 'FileText', slug })
        });
        closeModal('addCategoryModal');
        showToast('Kategori eklendi');
        categories = await apiRequest('/api/petition-categories');
        renderCategories();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

async function deleteCategory(id) {
    if (!confirmAction('Bu kategoriyi silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/petition-categories', { method: 'DELETE', body: JSON.stringify({ id }) });
        categories = categories.filter(c => c.id !== id);
        renderCategories(); showToast('Kategori silindi');
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
