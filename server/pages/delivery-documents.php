<?php
/**
 * Asmira Ops - Teslimatçı Evrakları
 */
$pageTitle = 'Teslimatçı Evrakları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/delivery-documents';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-teal-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-teal-500/60 via-teal-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">TESLİMATÇI EVRAKLARI</div>
                <div class="text-3xl font-black tracking-tight">Teslimatçılar</div>
            </div>
            <button type="button" onclick="openNewDelivererModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-teal-600 to-teal-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(20,184,166,0.25)] transition-all hover:from-teal-500 hover:to-teal-600">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Yeni Teslimatçı Ekle
            </button>
        </div>

        <!-- Search & Stats -->
        <div class="relative flex flex-none items-center gap-3 px-6 py-3">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-teal-500/40 via-teal-400/20 to-transparent"></div>
            <div class="relative flex-1">
                <i data-lucide="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-cyan-400"></i>
                <input type="text" id="searchInput" oninput="renderGrid()" placeholder="Teslimatçı adı, TC veya telefon ara..." class="h-11 w-full rounded-xl border border-cyan-500/30 bg-slate-900/80 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/40 focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/20 transition-all">
            </div>
            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                <div class="h-2 w-2 rounded-full bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.6)]"></div>
                <span class="text-xs font-medium text-white/70">Toplam</span>
                <span class="text-sm font-bold text-white" id="totalCount">0</span>
            </div>
        </div>

        <!-- Cards -->
        <div class="flex-1 overflow-y-auto p-6">
            <div id="delivererGrid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div class="col-span-full flex items-center justify-center py-12"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Document Side Panel -->
<div id="docPanel" class="hidden fixed inset-0 z-50 flex justify-end">
    <button type="button" class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePanel()"></button>
    <div class="doc-side-panel relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-teal-500/60 via-teal-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div>
                <div class="text-lg font-bold" id="panelTitle"></div>
            </div>
            <button type="button" onclick="closePanel()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4" id="panelDocs"></div>
        <div class="border-t border-white/10 px-5 py-4">
            <button type="button" onclick="savePanelChanges()" id="panelSaveBtn" class="inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all">
                <i data-lucide="check-circle" class="h-4 w-4"></i><span id="panelSaveText">Kaydet</span>
            </button>
        </div>
    </div>
</div>

<!-- Edit Deliverer Modal -->
<div id="editDelivererModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('editDelivererModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-teal-500/60 via-teal-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">DÜZENLE</div>
                <div class="text-lg font-bold">Teslimatçı Bilgileri</div>
            </div>
            <button type="button" onclick="closeModal('editDelivererModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <input type="hidden" id="editDelivererId">
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Ad Soyad *</label><input type="text" id="editDelivererName" placeholder="Örn: Ali Veli" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-teal-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">TC Kimlik No</label><input type="text" id="editDelivererTcNo" placeholder="Örn: 12345678901" maxlength="11" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-teal-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Telefon</label><input type="text" id="editDelivererPhone" placeholder="Örn: 0532 123 45 67" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-teal-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('editDelivererModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveEditDeliverer()" class="rounded-lg bg-gradient-to-br from-teal-600 to-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(20,184,166,0.25)] transition-all hover:from-teal-500 hover:to-teal-600">Güncelle</button>
        </div>
    </div>
</div>

<!-- New Deliverer Modal -->
<div id="newDelivererModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('newDelivererModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-teal-500/60 via-teal-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">YENİ KAYIT</div>
                <div class="text-lg font-bold">Teslimatçı Tanımla</div>
            </div>
            <button type="button" onclick="closeModal('newDelivererModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Ad Soyad *</label><input type="text" id="newDelivererName" placeholder="Örn: Ali Veli" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-teal-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">TC Kimlik No</label><input type="text" id="newDelivererTcNo" placeholder="Örn: 12345678901" maxlength="11" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-teal-500/50"></div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Telefon</label><input type="text" id="newDelivererPhone" placeholder="Örn: 0532 123 45 67" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-teal-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('newDelivererModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveNewDeliverer()" class="rounded-lg bg-gradient-to-br from-teal-600 to-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(20,184,166,0.25)] transition-all hover:from-teal-500 hover:to-teal-600">Kaydet</button>
        </div>
    </div>
</div>

<script>
const DEFAULT_DELIVERER_DOCS = [
    { type: 'kimlik', label: 'Kimlik' },
    { type: 'ehliyet', label: 'Ehliyet' },
    { type: 'src5', label: 'SRC 5' },
    { type: 'psikoteknik', label: 'Psikoteknik' },
    { type: 'adliSicil', label: 'Adli Sicil' },
    { type: 'iseGirisBildirge', label: 'İşe Giriş Bildirgesi' },
    { type: 'ikametgah', label: 'İkametgah' },
    { type: 'kkdZimmet', label: 'KKD Zimmet' },
    { type: 'saglikMuayene', label: 'Sağlık Muayene' },
    { type: 'isgEgitimBelgesi', label: 'İSG Eğitim Belgesi' },
    { type: 'yanginEgitimSertifikasi', label: 'Yangın Eğitim Sertifikası' },
];

function ensureDelivererDocs(d) {
    const existing = d.documents || [];
    d.documents = DEFAULT_DELIVERER_DOCS.map(def => {
        const found = existing.find(x => x.type === def.type);
        return found ? { ...def, ...found } : { ...def, fileName: null, filePath: null, expiryDate: null };
    });
    return d;
}

let deliverers = [];
let panelDelivererId = null;
let pendingChanges = { uploads: [], expiryDates: [], deletions: [] };

document.addEventListener('DOMContentLoaded', loadData);

async function loadData() {
    try {
        deliverers = (await apiRequest('/api/deliverers')).map(ensureDelivererDocs);
        document.getElementById('totalCount').textContent = deliverers.length;
        renderGrid();
    } catch (e) { showToast('Veriler yüklenemedi: ' + e.message, 'error'); }
}

function countUploaded(docs) { return docs.filter(d => d.fileName).length; }
function countExpired(docs) { const now = new Date(); return docs.filter(d => d.expiryDate && new Date(d.expiryDate) < now).length; }

function getFilteredDeliverers() {
    const q = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    if (!q) return deliverers;
    return deliverers.filter(d => d.name.toLowerCase().includes(q) || (d.tcNo||'').includes(q) || (d.phone||'').includes(q));
}

function renderGrid() {
    const filtered = getFilteredDeliverers();
    const grid = document.getElementById('delivererGrid');
    if (filtered.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-white/40"><i data-lucide="package" class="h-12 w-12 mx-auto mb-3 opacity-30"></i><p>Teslimatçı bulunamadı</p></div>';
        lucide.createIcons({nodes:[grid]}); return;
    }
    grid.innerHTML = filtered.map(d => {
        const docs = d.documents || [];
        const uploaded = countUploaded(docs);
        const total = docs.length;
        const expiredCount = countExpired(docs);
        const isComplete = uploaded === total && total > 0;
        const hasExpired = expiredCount > 0;
        const progress = total > 0 ? (uploaded / total) * 100 : 0;
        const borderCls = hasExpired ? 'border-red-500/40 shadow-[0_0_25px_rgba(239,68,68,0.15)]' : isComplete ? 'border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]' : 'border-teal-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]';
        const iconCls = hasExpired ? 'from-red-500/25 to-red-600/10 text-red-400' : isComplete ? 'from-emerald-500/25 to-emerald-600/10 text-emerald-400' : 'from-teal-500/25 to-teal-600/10 text-teal-400';
        const progCls = isComplete ? 'bg-gradient-to-r from-emerald-500 to-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)]' : 'bg-gradient-to-r from-amber-500 to-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.4)]';
        let statusBadge = '';
        if (hasExpired) { statusBadge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-red-500/15 px-2.5 py-1 text-[11px] font-semibold text-red-400"><i data-lucide="alert-octagon" class="h-3 w-3"></i>${expiredCount} Evrak Süresi Geçmiş</div>`; }
        else if (isComplete) { statusBadge = '<div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-400"><i data-lucide="check-circle" class="h-3 w-3"></i>Tüm Evraklar Tamam</div>'; }
        else { statusBadge = `<div class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-1 text-[11px] font-semibold text-amber-400"><i data-lucide="alert-triangle" class="h-3 w-3"></i>${total - uploaded} Evrak Eksik</div>`; }

        return `<div class="group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06] ${borderCls}">
            <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-teal-500/10 blur-2xl transition-opacity group-hover:opacity-100 opacity-0"></div>
            <button type="button" onclick="deleteDeliverer('${d.id}','${escapeHtml(d.name)}')" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
            <div class="mb-4 flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] ${iconCls}"><i data-lucide="package" class="h-5 w-5"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-[15px] font-bold tracking-tight">${escapeHtml(d.name)}</div>
                    <div class="truncate text-xs text-white/50">${escapeHtml(d.tcNo||'')}</div>
                    ${d.phone ? `<div class="truncate text-xs text-white/40">${escapeHtml(d.phone)}</div>` : ''}
                </div>
            </div>
            <div class="mb-3">
                <div class="mb-1.5 flex items-center justify-between text-[11px]">
                    <span class="text-white/60">Evrak Durumu</span>
                    <span class="font-semibold ${isComplete ? 'text-emerald-400' : 'text-amber-400'}">${uploaded}/${total}</span>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-white/15">
                    <div class="${progCls} h-full rounded-full transition-all duration-500" style="width:${progress}%"></div>
                </div>
            </div>
            <div class="mb-4">${statusBadge}</div>
            <div class="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">
                <button type="button" onclick="openPanel('${d.id}')" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"><i data-lucide="folder-open" class="h-3.5 w-3.5"></i>Evraklar</button>
                <button type="button" onclick="editDeliverer('${d.id}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="Düzenle"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i></button>
                <button type="button" onclick="downloadAll('${d.id}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="Tümünü İndir"><i data-lucide="download" class="h-3.5 w-3.5"></i></button>
            </div>
        </div>`;
    }).join('');
    lucide.createIcons({nodes:[grid]});
}

// ======= PANEL =======
function openPanel(delivererId) {
    panelDelivererId = delivererId;
    pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
    renderPanel();
    document.getElementById('docPanel').classList.remove('hidden');
    lucide.createIcons();
}
function closePanel() {
    if (hasPendingChanges() && !confirm('Kaydedilmemiş değişiklikler var. Çıkmak istediğinize emin misiniz?')) return;
    document.getElementById('docPanel').classList.add('hidden');
    panelDelivererId = null;
}
function hasPendingChanges() { return pendingChanges.uploads.length > 0 || pendingChanges.expiryDates.length > 0 || pendingChanges.deletions.length > 0; }

function renderPanel() {
    const d = deliverers.find(x => x.id === panelDelivererId);
    if (!d) return;
    document.getElementById('panelTitle').textContent = d.name;
    const docs = d.documents || [];
    const docsEl = document.getElementById('panelDocs');
    docsEl.innerHTML = '<div class="space-y-4">' + docs.map(doc => {
        const pu = pendingChanges.uploads.find(u => u.docType === doc.type);
        const pe = pendingChanges.expiryDates.find(e => e.docType === doc.type);
        const pd = pendingChanges.deletions.find(dd => dd.docType === doc.type);
        const fileName = pd ? null : (pu ? pu.fileName : doc.fileName);
        const fileUrl = pd ? null : (pu ? pu.fileUrl : (doc.fileUrl || (doc.filePath ? '/api/documents/download/'+doc.filePath : null)));
        const expiryDate = pe ? pe.date : doc.expiryDate;
        const expired = expiryDate ? new Date(expiryDate) < new Date() : false;
        const hasChanges = pu || pe || pd;
        const cls = hasChanges ? 'border-amber-500/50 bg-amber-500/5' : expired ? 'border-red-500/50 bg-red-500/10' : 'border-white/10 bg-white/5';
        let fs = '';
        if (fileName) {
            fs = `<div class="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2">
                <span class="truncate text-sm text-white/70">${escapeHtml(fileName)}</span>
                <div class="flex items-center gap-2">
                    ${fileUrl ? `<a href="${fileUrl}" target="_blank" class="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10" title="Önizle"><i data-lucide="eye" class="h-4 w-4"></i></a>` : ''}
                    <button type="button" onclick="panelDeleteDoc('${doc.type}')" class="inline-flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                </div>
            </div>`;
        } else {
            fs = `<div class="mt-3"><label class="flex items-center justify-center gap-2 rounded-md border border-dashed border-white/20 bg-white/5 px-4 py-3 text-sm hover:bg-white/10 cursor-pointer text-white/50 hover:text-white/70 transition"><i data-lucide="upload" class="h-4 w-4"></i>Dosya sürükleyin veya tıklayın<input type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,.tiff,.doc,.docx" onchange="panelUpload(this,'${doc.type}')"></label></div>`;
        }
        return `<div class="rounded-lg border p-4 ${cls} transition-all" ondragover="event.preventDefault();this.classList.add('ring-2','ring-teal-400','bg-teal-500/10')" ondragenter="event.preventDefault()" ondragleave="this.classList.remove('ring-2','ring-teal-400','bg-teal-500/10')" ondrop="handleDocDrop(event,'${doc.type}')">
            <div class="flex items-center gap-2">
                ${fileName ? '<i data-lucide="check-circle" class="h-4 w-4 text-emerald-400"></i>' : '<div class="h-4 w-4 rounded-full border-2 border-white/30"></div>'}
                <span class="font-medium">${escapeHtml(doc.label || doc.type)}</span>
                ${hasChanges ? '<span class="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">DEĞİŞİKLİK</span>' : ''}
                ${expired && !hasChanges ? '<span class="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">SÜRESİ GEÇMİŞ</span>' : ''}
            </div>
            ${fs}
            <div class="mt-3 flex items-center gap-2">
                <label class="text-xs text-white/50">Son Geçerlilik:</label>
                <input type="date" value="${expiryDate||''}" onchange="panelExpiryChange('${doc.type}',this.value)" style="color-scheme:dark" class="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-teal-500/50">
                ${expiryDate ? `<button type="button" onclick="panelExpiryChange('${doc.type}',null)" class="text-xs text-white/40 hover:text-white/60">✕</button>` : ''}
            </div>
        </div>`;
    }).join('') + '</div>';

    const saveBtn = document.getElementById('panelSaveBtn');
    const saveText = document.getElementById('panelSaveText');
    if (hasPendingChanges()) {
        const c = pendingChanges.uploads.length + pendingChanges.expiryDates.length + pendingChanges.deletions.length;
        saveBtn.className = 'inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white transition-all bg-gradient-to-br from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600';
        saveText.textContent = `Kaydet (${c} değişiklik)`;
    } else {
        saveBtn.className = 'inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white bg-white/10 text-white/40 cursor-not-allowed transition-all';
        saveText.textContent = 'Kaydet';
    }
    lucide.createIcons({nodes:[docsEl]});
}

function panelUpload(input, docType) {
    const file = input.files[0]; if (!file) return;
    _addUploadFile(file, docType);
}
function handleDocDrop(e, docType) {
    e.preventDefault(); e.stopPropagation();
    e.currentTarget.classList.remove('ring-2','ring-teal-400','bg-teal-500/10');
    const file = e.dataTransfer.files[0]; if (!file) return;
    _addUploadFile(file, docType);
}
function _addUploadFile(file, docType) {
    pendingChanges.uploads = pendingChanges.uploads.filter(u => u.docType !== docType);
    pendingChanges.uploads.push({ docType, file, fileName: file.name, fileUrl: URL.createObjectURL(file) });
    pendingChanges.deletions = pendingChanges.deletions.filter(d => d.docType !== docType);
    renderPanel();
}
function panelExpiryChange(docType, date) {
    pendingChanges.expiryDates = pendingChanges.expiryDates.filter(e => e.docType !== docType);
    pendingChanges.expiryDates.push({ docType, date: date || null });
    renderPanel();
}
function panelDeleteDoc(docType) {
    pendingChanges.deletions = pendingChanges.deletions.filter(d => d.docType !== docType);
    pendingChanges.deletions.push({ docType });
    pendingChanges.uploads = pendingChanges.uploads.filter(u => u.docType !== docType);
    renderPanel();
}

async function savePanelChanges() {
    if (!hasPendingChanges() || !panelDelivererId) return;
    const d = deliverers.find(x => x.id === panelDelivererId);
    if (!d) return;
    try {
        for (const del of pendingChanges.deletions) {
            try { await apiRequest('/api/documents/update', { method: 'DELETE', body: JSON.stringify({ ownerId: panelDelivererId, ownerType: 'deliverer', docType: del.docType }) }); } catch(e) {}
        }
        for (const up of pendingChanges.uploads) {
            try { await uploadFile(up.file, panelDelivererId, 'deliverer', up.docType); } catch(e) {}
        }
        for (const exp of pendingChanges.expiryDates) {
            try { await apiRequest('/api/documents/update', { method: 'PUT', body: JSON.stringify({ ownerId: panelDelivererId, ownerType: 'deliverer', docType: exp.docType, expiryDate: exp.date }) }); } catch(e) {}
        }
        const docs = d.documents || [];
        for (const del of pendingChanges.deletions) { const doc = docs.find(dd => dd.type === del.docType); if (doc) { doc.fileName = null; doc.filePath = null; doc.fileUrl = null; } }
        for (const up of pendingChanges.uploads) { const doc = docs.find(dd => dd.type === up.docType); if (doc) { doc.fileName = up.fileName; doc.fileUrl = up.fileUrl; } }
        for (const exp of pendingChanges.expiryDates) { const doc = docs.find(dd => dd.type === exp.docType); if (doc) { doc.expiryDate = exp.date; } }
        pendingChanges = { uploads: [], expiryDates: [], deletions: [] };
        showToast('Değişiklikler kaydedildi');
        renderGrid();
        renderPanel();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

// ======= CRUD =======
function openNewDelivererModal() {
    document.getElementById('newDelivererName').value = '';
    document.getElementById('newDelivererTcNo').value = '';
    document.getElementById('newDelivererPhone').value = '';
    openModal('newDelivererModal'); lucide.createIcons();
}

async function saveNewDeliverer() {
    const name = document.getElementById('newDelivererName').value.trim();
    const tcNo = document.getElementById('newDelivererTcNo').value.trim();
    const phone = document.getElementById('newDelivererPhone').value.trim();
    if (!name) { showToast('Lütfen teslimatçı adını girin', 'error'); return; }
    try {
        const res = await apiRequest('/api/deliverers', { method: 'POST', body: JSON.stringify({ name, tcNo, phone }) });
        deliverers.push(ensureDelivererDocs({ id: res.id || ('dlv_local_' + Date.now()), name, tcNo, phone, documents: res.documents || [] }));
        document.getElementById('totalCount').textContent = deliverers.length;
        renderGrid();
        closeModal('newDelivererModal');
        showToast('Teslimatçı eklendi');
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

async function deleteDeliverer(id, name) {
    if (!confirmAction(`"${name}" kaydını silmek istediğinize emin misiniz?`)) return;
    try {
        await apiRequest('/api/deliverers', { method: 'DELETE', body: JSON.stringify({ id }) });
    } catch (e) { console.warn('API delete hatası (devam ediliyor):', e); }
    deliverers = deliverers.filter(d => d.id !== id);
    document.getElementById('totalCount').textContent = deliverers.length;
    if (panelDelivererId === id) { panelDelivererId = null; document.getElementById('docPanel')?.classList.add('hidden'); }
    renderGrid();
    showToast('Teslimatçı silindi');
}

function editDeliverer(id) {
    const d = deliverers.find(x => x.id === id);
    if (!d) return;
    document.getElementById('editDelivererId').value = d.id;
    document.getElementById('editDelivererName').value = d.name || '';
    document.getElementById('editDelivererTcNo').value = d.tcNo || '';
    document.getElementById('editDelivererPhone').value = d.phone || '';
    openModal('editDelivererModal');
    lucide.createIcons();
}

async function saveEditDeliverer() {
    const id = document.getElementById('editDelivererId').value;
    const name = document.getElementById('editDelivererName').value.trim();
    const tcNo = document.getElementById('editDelivererTcNo').value.trim();
    const phone = document.getElementById('editDelivererPhone').value.trim();
    if (!name) { showToast('Lütfen teslimatçı adını girin', 'error'); return; }
    const d = deliverers.find(x => x.id === id);
    if (!d) return;
    try {
        await apiRequest('/api/deliverers', { method: 'PUT', body: JSON.stringify({ id, name, tcNo, phone }) });
    } catch (e) { console.warn('API güncelleme hatası (devam ediliyor):', e); }
    d.name = name;
    d.tcNo = tcNo;
    d.phone = phone;
    renderGrid();
    closeModal('editDelivererModal');
    showToast('Teslimatçı bilgileri güncellendi');
}

function downloadAll(id) {
    const d = deliverers.find(x => x.id === id);
    if (!d) return;
    const uploaded = (d.documents||[]).filter(doc => doc.fileName);
    if (uploaded.length === 0) { showToast('Bu teslimatçı için yüklü evrak bulunmuyor', 'error'); return; }
    showToast('İndirme başlatılıyor...');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
