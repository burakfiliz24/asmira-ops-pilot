<?php
/**
 * Asmira Ops - Dilekçe Kategori Detay
 * Taahhütnameler için antetli kağıt editörü + PDF export
 */
$slug = $_GET['slug'] ?? '';
$pageTitle = 'Dilekçe Şablonları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/petitions';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="flex flex-none flex-wrap items-center justify-between gap-3 border-b border-white/10 bg-white/[0.02] px-6 py-4">
            <div class="flex items-center gap-4">
                <a href="/petitions" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                </a>
                <div>
                    <div class="text-sm font-semibold tracking-wider text-white/70">DİLEKÇELER</div>
                    <div class="text-2xl font-semibold tracking-tight" id="catTitle">Yükleniyor...</div>
                </div>
            </div>
            <button type="button" onclick="openNewTemplate()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-500">
                <i data-lucide="plus" class="h-4 w-4"></i> Belge Ekle
            </button>
        </div>

        <!-- Stats Bar -->
        <div class="flex flex-none items-center gap-6 border-b border-white/10 px-6 py-3">
            <div class="text-[11px] font-semibold tracking-widest text-white/70">
                Toplam Şablon: <span id="tplCount">0</span>
                <span class="mx-2 text-white/25">|</span>
                Bir şablon seçerek metni düzenleyebilir ve PDF olarak kaydedebilirsiniz
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" id="templateGrid">
                <div class="flex items-center justify-center col-span-full py-12"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Editor Modal - Antetli Kağıt Görünümü -->
<div id="editorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeEditor()"></div>
    <div class="relative z-10 flex h-[95vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
        <!-- Modal Header - Toolbar -->
        <div class="flex items-center justify-between border-b border-white/10 bg-[#0a0f1a] px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-white/70" id="editorTitle"></span>
            </div>
            <div class="flex items-center gap-2">
                <div id="newTemplateFields" class="hidden flex items-center gap-2">
                    <input type="text" id="newShortName" placeholder="Kısa Ad (örn: HABAŞ)" class="h-8 w-32 rounded-lg border border-white/10 bg-white/5 px-3 text-xs outline-none placeholder:text-white/30 focus:border-cyan-500/50">
                    <input type="text" id="newFullName" placeholder="Tam Ad (örn: HABAŞ Limanı)" class="h-8 w-56 rounded-lg border border-white/10 bg-white/5 px-3 text-xs outline-none placeholder:text-white/30 focus:border-cyan-500/50">
                    <button type="button" onclick="saveNewTemplate()" class="inline-flex h-8 items-center gap-2 rounded-lg bg-cyan-600 px-4 text-xs font-semibold text-white transition hover:bg-cyan-500">
                        <i data-lucide="save" class="h-3.5 w-3.5"></i> Şablon Olarak Kaydet
                    </button>
                </div>
                <button type="button" id="resetBtn" onclick="resetEditor()" class="hidden rounded-lg px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10 hover:text-white">Sıfırla</button>
                <button type="button" onclick="saveCurrentText()" class="inline-flex h-8 items-center gap-2 rounded-lg bg-blue-600 px-4 text-xs font-semibold text-white transition hover:bg-blue-500">
                    <i data-lucide="save" class="h-3.5 w-3.5"></i> Metni Kaydet
                </button>
                <button type="button" onclick="exportPDF()" class="inline-flex h-8 items-center gap-2 rounded-lg bg-green-600 px-4 text-xs font-semibold text-white transition hover:bg-green-500">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> PDF Olarak Kaydet
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-white/50 hover:bg-white/10 hover:text-white" onclick="closeEditor()">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

        <!-- Antetli Kağıt Görünümü -->
        <div class="flex-1 overflow-auto bg-gray-400 p-6 custom-scroll" id="editorScrollArea" style="scrollbar-color:#555 #9ca3af;scrollbar-width:auto;">
            <div class="relative mx-auto bg-white shadow-2xl" id="letterheadPage" style="width:794px;min-height:1123px;background-image:url('/assets/images/letterhead.png');background-size:100% auto;background-position:top center;background-repeat:no-repeat;">
                <!-- Footer background -->
                <div style="position:absolute;bottom:0;left:0;right:0;height:180px;background-image:url('/assets/images/letterhead.png');background-size:794px auto;background-position:bottom center;background-repeat:no-repeat;"></div>
                <div id="editorContent" contenteditable="true" class="text-gray-900 outline-none" style="padding:145px 75px 220px 75px;min-height:1123px;font-family:'Times New Roman',serif;font-size:12pt;line-height:1.6;white-space:pre-wrap;word-wrap:break-word;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Name Modal -->
<div id="editNameModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('editNameModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-xl">
        <h3 class="mb-4 text-lg font-semibold">Şablon Bilgilerini Düzenle</h3>
        <input type="hidden" id="editTplId">
        <div class="space-y-4">
            <div><label class="mb-1 block text-sm text-white/70">Kısa Ad</label><input type="text" id="editShortName" class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white outline-none focus:border-blue-500" placeholder="Örn: HABAŞ"></div>
            <div><label class="mb-1 block text-sm text-white/70">Tam Ad</label><input type="text" id="editFullName" class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white outline-none focus:border-blue-500" placeholder="Örn: HABAŞ Limanı"></div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeModal('editNameModal')" class="rounded-lg px-4 py-2 text-sm text-white/70 hover:bg-white/10">İptal</button>
            <button type="button" onclick="saveEditedName()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500"><i data-lucide="save" class="h-4 w-4"></i> Kaydet</button>
        </div>
    </div>
</div>

<script>
const SLUG = '<?= htmlspecialchars($slug) ?>';
const LETTERHEAD_URL = '/assets/images/letterhead.png';

// Varsayılan HABAŞ ve İDÇ taahhütname şablonları
const DEFAULT_TAAHHUTNAME_TEMPLATES = [
    {
        id: 'tpl_habas', shortName: 'HABAŞ', name: 'HABAŞ Limanı', category: 'taahhutnameler',
        defaultText: `21.01.2026


HABAŞ A.Ş. LİMANI İŞLETME MÜDÜRLÜĞÜNE;

Limanınızda bulunan INASE gemisine aşağıda belirtilen miktar ve tipteki yakıt ikmali yapılacak olup, bu yakıt ikmali sırasında tarafımızdan kaynaklanan sebepten dolayı oluşabilecek her türlü deniz ve çevre kirliliği ve kazalara karşı limanınızda oluşacak zarar ve ziyanların tümünün tarafımızdan ödenmesini taahhüt ederiz.

GEMİ ADI: INASE
YAKIT CİNSİ: DAMITIK DENİZCİLİK YAKITI 
TON: 100 TON
İKMAL ARAÇLARI: 
35 SK 478 – 35 AS 714 / YUSUF ORAN
35 SK 225 – 35 AS 711 / HÜSEYİN FEHMİ TAŞKIRAN
35 CIF 367 – 35 COC 302 / TURAN GÖKTAŞ
35 NIJ 71 – 35 NLB 71 / KAAN KARASU


GİRİŞ YAPACAK KİŞİLER:
EFE GARİP
CANER ÇORBA
ŞERİF GÖNÜLTAŞ
`, createdAt: Date.now(), isDefault: true
    },
    {
        id: 'tpl_idc', shortName: 'IDÇ', name: 'IDÇ Limanı', category: 'taahhutnameler',
        defaultText: `04.09.2025

		
İDÇ LİMAN İŞLETMELERİ A.Ş. MÜDÜRLÜĞÜ'NE
TAAHHÜTNAME

Limanınızda yanaşacak olan GOLDEN ROSE isimli gemiye aşağıda belirtilen miktar ve tipteki yakıt ikmali yapılacak olup, bu yakıt ikmali sırasında tarafımızdan kaynaklanabilecek herhangi bir sebepten dolayı oluşabilecek her türlü liman deniz ve çevre kirliliği ve kazalara karşı zarar ve ziyanların tümünün tarafımızdan karşılanacağını taahhüt ederiz.

Gemi adı\t: MEDITERRANEAN SPIRIT 
Acentesi\t: İDÇ
Yakıt cinsi\t: MGO
Miktarı\t: 40 TON
İkmal tarihi\t: 04.09.2025
İkmal aracı: 
ÇEKİCİ\tDORSE\t\t\t\t
35 APY 774 - 35 AS 537\tTUNCAY ŞEVİK
35 SK 225 - 35 AS 711\tHÜSEYİN FEHMİ TAŞKIRAN

TESLİMATÇI: EFE GARİP, CANER ÇORBA, DUHAN IRMAK

Gerekli izinlerin verilmesini arz ederiz.
`, createdAt: Date.now(), isDefault: true
    }
];

let templates = [];
let currentTemplateId = null;
let isAddingNew = false;

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [cats, tpls] = await Promise.all([
            apiRequest('/api/petition-categories'),
            apiRequest('/api/petition-templates'),
        ]);
        const cat = cats.find(c => c.slug === SLUG);
        if (cat) document.getElementById('catTitle').textContent = cat.title;
        else document.getElementById('catTitle').textContent = SLUG.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

        templates = tpls.filter(t => t.category === SLUG);

        // Taahhütnameler kategorisi ise ve şablon yoksa varsayılanları ekle
        if (SLUG === 'taahhutnameler' && templates.length === 0) {
            templates = [...DEFAULT_TAAHHUTNAME_TEMPLATES];
        } else if (SLUG === 'taahhutnameler') {
            // API'den dönen şablonlara varsayılanları ekle (yoksa)
            DEFAULT_TAAHHUTNAME_TEMPLATES.forEach(dt => {
                if (!templates.find(t => t.id === dt.id)) templates.push(dt);
            });
        }

        renderGrid();
    } catch (e) { showToast('Veriler yüklenemedi: ' + e.message, 'error'); }
});

function formatTaahhutname(text) {
    const lines = text.split('\n');
    let html = '';
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (i === 0 && line.match(/^\d{2}\.\d{2}\.\d{4}/)) {
            html += `<div style="text-align:right;margin-bottom:20px;">${escapeHtml(line)}</div>`;
        } else if (line.includes('MÜDÜRLÜĞÜNE') || line.includes("MÜDÜRLÜĞÜ'NE") || line.includes('LİMANI İŞLETME')) {
            html += `<div style="text-align:center;text-decoration:underline;font-weight:bold;margin-bottom:10px;">${escapeHtml(line)}</div>`;
        } else if (line.trim() === 'TAAHHÜTNAME') {
            html += `<div style="text-align:center;font-weight:bold;margin-bottom:20px;">${escapeHtml(line)}</div>`;
        } else if (line.trim() === '') {
            html += '<br/>';
        } else {
            html += `<div>${escapeHtml(line)}</div>`;
        }
    }
    return html;
}

function renderGrid() {
    const grid = document.getElementById('templateGrid');
    document.getElementById('tplCount').textContent = templates.length;
    if (templates.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-white/40"><p>Bu kategoride henüz şablon yok</p></div>';
        return;
    }
    grid.innerHTML = templates.map(t => `
        <button type="button" onclick="openEditor('${t.id}')" class="group flex w-full flex-col rounded-xl border border-white/15 bg-white/[0.04] p-5 text-left shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-blue-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(59,130,246,0.15)]">
            <div class="mb-3 flex items-center justify-between w-full">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400 transition group-hover:bg-blue-500/30"><i data-lucide="file-text" class="h-6 w-6"></i></div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                    <span onclick="event.stopPropagation();openEditName('${t.id}')" class="flex h-7 w-7 items-center justify-center rounded-md text-white/40 hover:text-white hover:bg-white/10"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i></span>
                    <span onclick="event.stopPropagation();handleDelete('${t.id}')" class="flex h-7 w-7 items-center justify-center rounded-md text-white/40 hover:text-red-400 hover:bg-red-500/10"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></span>
                </div>
            </div>
            <div class="mb-1 text-[16px] font-semibold">${escapeHtml(t.shortName || t.name)}</div>
            <div class="text-sm text-white/70">${escapeHtml(t.name)}</div>
            <div class="mt-3 text-[11px] text-white/40">${t.createdAt ? new Date(t.createdAt).toLocaleDateString('tr-TR') : ''}</div>
        </button>
    `).join('');
    lucide.createIcons({nodes:[grid]});
}

function openEditor(id) {
    const t = templates.find(x => x.id === id);
    if (!t) return;
    currentTemplateId = id;
    isAddingNew = false;
    document.getElementById('editorTitle').textContent = t.name;
    document.getElementById('editorContent').innerHTML = formatTaahhutname(t.defaultText);
    document.getElementById('newTemplateFields').classList.add('hidden');
    document.getElementById('resetBtn').classList.remove('hidden');
    document.getElementById('editorModal').classList.remove('hidden');
    lucide.createIcons();
}

function openNewTemplate() {
    isAddingNew = true;
    currentTemplateId = null;
    const today = new Date().toLocaleDateString('tr-TR');
    const defaultText = `${today}\n\n\n_______________\n_______________\n\n\nTAAHHÜTNAME\n\n\n_______________\n\n\nSaygılarımızla.\n`;
    document.getElementById('editorTitle').textContent = 'Yeni Taahhütname';
    document.getElementById('editorContent').innerHTML = formatTaahhutname(defaultText);
    document.getElementById('newTemplateFields').classList.remove('hidden');
    document.getElementById('newTemplateFields').style.display = 'flex';
    document.getElementById('resetBtn').classList.add('hidden');
    document.getElementById('newShortName').value = '';
    document.getElementById('newFullName').value = '';
    document.getElementById('editorModal').classList.remove('hidden');
    lucide.createIcons();
}

function closeEditor() {
    document.getElementById('editorModal').classList.add('hidden');
    currentTemplateId = null;
    isAddingNew = false;
}

function resetEditor() {
    if (!currentTemplateId) return;
    const t = templates.find(x => x.id === currentTemplateId);
    if (t) document.getElementById('editorContent').innerHTML = formatTaahhutname(t.defaultText);
}

function saveCurrentText() {
    if (!currentTemplateId) return;
    const t = templates.find(x => x.id === currentTemplateId);
    if (!t) return;
    const content = document.getElementById('editorContent').innerText;
    t.defaultText = content;
    // API'ye de kaydet
    try { apiRequest('/api/petition-templates', { method: 'PUT', body: JSON.stringify({ id: t.id, defaultText: content }) }); } catch(e) {}
    showToast('Metin kaydedildi');
}

async function saveNewTemplate() {
    const shortName = document.getElementById('newShortName').value.trim();
    const fullName = document.getElementById('newFullName').value.trim();
    if (!shortName || !fullName) { showToast('Lütfen kısa ad ve tam ad girin', 'error'); return; }
    const content = document.getElementById('editorContent').innerText;
    const data = { shortName: shortName.toUpperCase(), name: fullName, defaultText: content, category: SLUG, createdAt: Date.now(), isDefault: false };
    try {
        const res = await apiRequest('/api/petition-templates', { method: 'POST', body: JSON.stringify(data) });
        data.id = res.id || ('tpl_local_' + Date.now());
        templates.push(data);
        renderGrid();
        closeEditor();
        showToast('Şablon oluşturuldu');
    } catch(e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

function exportPDF() {
    const content = document.getElementById('editorContent').innerText;
    const title = document.getElementById('editorTitle').textContent;
    const formatted = formatTaahhutname(content);
    const win = window.open('', '_blank');
    if (!win) { showToast('Popup engelleyici aktif olabilir', 'error'); return; }
    win.document.write(`<!DOCTYPE html><html><head><title>${escapeHtml(title)} - Taahhütname</title>
<style>
@page { size: A4; margin: 0; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.8; color: #000; }
.page { width: 210mm; height: 297mm; position: relative; background-image: url('${window.location.origin}${LETTERHEAD_URL}'); background-size: 100% 100%; background-position: top center; background-repeat: no-repeat; }
.content { position: absolute; top: 38mm; left: 20mm; right: 20mm; bottom: 58mm; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6; }
@media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } .page { margin: 0; } }
</style></head><body><div class="page"><div class="content">${formatted}</div></div></body></html>`);
    win.document.close();
    setTimeout(() => win.print(), 500);
}

function openEditName(id) {
    const t = templates.find(x => x.id === id);
    if (!t) return;
    document.getElementById('editTplId').value = id;
    document.getElementById('editShortName').value = t.shortName || '';
    document.getElementById('editFullName').value = t.name || '';
    openModal('editNameModal');
    lucide.createIcons();
}

function saveEditedName() {
    const id = document.getElementById('editTplId').value;
    const shortName = document.getElementById('editShortName').value.trim();
    const fullName = document.getElementById('editFullName').value.trim();
    if (!shortName || !fullName) { showToast('Lütfen alanları doldurun', 'error'); return; }
    const t = templates.find(x => x.id === id);
    if (t) { t.shortName = shortName.toUpperCase(); t.name = fullName; }
    try { apiRequest('/api/petition-templates', { method: 'PUT', body: JSON.stringify({ id, shortName: shortName.toUpperCase(), name: fullName }) }); } catch(e) {}
    closeModal('editNameModal');
    renderGrid();
    showToast('Şablon güncellendi');
}

function handleDelete(id) {
    if (!confirmAction('Bu şablonu silmek istediğinize emin misiniz?')) return;
    try { apiRequest('/api/petition-templates', { method: 'DELETE', body: JSON.stringify({ id }) }); } catch(e) {}
    templates = templates.filter(t => t.id !== id);
    renderGrid();
    showToast('Şablon silindi');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
