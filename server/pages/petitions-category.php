<?php
/**
 * Asmira Ops - Dilekçe Kategori Detay
 */
$slug = $_GET['slug'] ?? '';
$pageTitle = 'Dilekçe Şablonları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/petitions';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="/petitions" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white" id="catTitle">Yükleniyor...</h1>
                <p class="text-sm text-white/50 mt-1" id="catDesc"></p>
            </div>
        </div>
        <button onclick="openModal('addTemplateModal')" class="btn btn-primary">
            <i data-lucide="plus" class="h-4 w-4"></i> Yeni Şablon
        </button>
    </div>

    <div id="templateList">
        <div class="flex items-center justify-center py-12"><div class="spinner"></div></div>
    </div>
</div>

<!-- Add Template Modal -->
<div id="addTemplateModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('addTemplateModal')"></div>
    <div class="relative mx-4 w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div class="text-lg font-semibold">Yeni Şablon</div>
            <button onclick="closeModal('addTemplateModal')" class="h-9 w-9 flex items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <form onsubmit="addTemplate(event)" class="space-y-3 px-5 py-4">
            <div><label class="block text-xs font-medium text-white/50 mb-1">Kısa Ad *</label><input type="text" id="tplShortName" class="input-field" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">Tam Ad *</label><input type="text" id="tplName" class="input-field" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">Varsayılan Metin</label><textarea id="tplText" class="input-field" rows="6" style="height:auto"></textarea></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addTemplateModal')" class="btn btn-ghost flex-1">İptal</button>
                <button type="submit" class="btn btn-primary flex-1"><i data-lucide="check" class="h-4 w-4"></i> Kaydet</button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('previewModal')"></div>
    <div class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div class="text-lg font-semibold" id="previewTitle">Şablon</div>
            <div class="flex gap-2">
                <button onclick="printPreview()" class="btn btn-ghost"><i data-lucide="printer" class="h-4 w-4"></i> Yazdır</button>
                <button onclick="closeModal('previewModal')" class="h-9 w-9 flex items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
            </div>
        </div>
        <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
            <div id="previewContent" class="bg-white text-black p-8 rounded-lg text-sm leading-relaxed whitespace-pre-wrap font-serif"></div>
        </div>
    </div>
</div>

<script>
const SLUG = '<?= htmlspecialchars($slug) ?>';
let templates = [];

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [cats, tpls] = await Promise.all([
            apiRequest('/api/petition-categories'),
            apiRequest('/api/petition-templates'),
        ]);
        const cat = cats.find(c => c.slug === SLUG);
        if (cat) {
            document.getElementById('catTitle').textContent = cat.title;
            document.getElementById('catDesc').textContent = cat.description || '';
        } else {
            document.getElementById('catTitle').textContent = SLUG.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
        templates = tpls.filter(t => t.category === SLUG);
        renderTemplates();
    } catch (e) { showToast('Veriler yüklenemedi: ' + e.message, 'error'); }
});

function renderTemplates() {
    const container = document.getElementById('templateList');
    if (templates.length === 0) {
        container.innerHTML = '<div class="text-center py-12 text-white/40"><p>Bu kategoride henüz şablon yok</p></div>';
        return;
    }
    let html = '<div class="space-y-3">';
    templates.forEach(t => {
        html += `<div class="card p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex-1">
                <div class="font-semibold text-white">${escapeHtml(t.shortName || t.name)}</div>
                <div class="text-sm text-white/40">${escapeHtml(t.name)}</div>
            </div>
            <div class="flex gap-2">
                <button onclick="previewTemplate('${t.id}')" class="btn btn-ghost text-xs"><i data-lucide="eye" class="h-3.5 w-3.5"></i> Önizle</button>
                <button onclick="deleteTemplate('${t.id}')" class="btn btn-danger text-xs"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Sil</button>
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}

function previewTemplate(id) {
    const t = templates.find(tp => tp.id === id);
    if (!t) return;
    document.getElementById('previewTitle').textContent = t.name;
    document.getElementById('previewContent').textContent = t.defaultText || '(Şablon metni boş)';
    openModal('previewModal');
    lucide.createIcons();
}

function printPreview() {
    const content = document.getElementById('previewContent').innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`<html><head><title>Yazdır</title><style>body{font-family:serif;padding:2cm;font-size:14px;line-height:1.8;white-space:pre-wrap;}</style></head><body>${content}</body></html>`);
    win.document.close();
    win.print();
}

async function addTemplate(e) {
    e.preventDefault();
    const data = {
        shortName: document.getElementById('tplShortName').value.trim(),
        name: document.getElementById('tplName').value.trim(),
        defaultText: document.getElementById('tplText').value,
        category: SLUG,
        isDefault: false,
        createdAt: Date.now(),
    };
    try {
        await apiRequest('/api/petition-templates', { method: 'POST', body: JSON.stringify(data) });
        closeModal('addTemplateModal');
        showToast('Şablon eklendi');
        const all = await apiRequest('/api/petition-templates');
        templates = all.filter(t => t.category === SLUG);
        renderTemplates();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

async function deleteTemplate(id) {
    if (!confirmAction('Bu şablonu silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/petition-templates', { method: 'DELETE', body: JSON.stringify({ id }) });
        templates = templates.filter(t => t.id !== id);
        renderTemplates(); showToast('Şablon silindi');
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
