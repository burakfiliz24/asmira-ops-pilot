<?php
/**
 * Asmira Ops - Şoför Evrakları
 */
$pageTitle = 'Şoför Evrakları';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/driver-documents';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Şoför Evrakları</h1>
            <p class="text-sm text-white/50 mt-1">Şoför bilgileri ve evrak yönetimi</p>
        </div>
        <button onclick="openModal('addDriverModal')" class="btn btn-primary">
            <i data-lucide="plus" class="h-4 w-4"></i> Yeni Şoför
        </button>
    </div>

    <div id="driverList">
        <div class="flex items-center justify-center py-12"><div class="spinner"></div></div>
    </div>
</div>

<!-- Add Driver Modal -->
<div id="addDriverModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('addDriverModal')"></div>
    <div class="relative mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div class="text-lg font-semibold">Yeni Şoför</div>
            <button onclick="closeModal('addDriverModal')" class="h-9 w-9 flex items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <form onsubmit="addDriver(event)" class="space-y-3 px-5 py-4">
            <div><label class="block text-xs font-medium text-white/50 mb-1">Ad Soyad *</label><input type="text" id="driverName" class="input-field" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">TC No *</label><input type="text" id="driverTcNo" class="input-field" maxlength="11" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">Telefon *</label><input type="text" id="driverPhone" class="input-field" required></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addDriverModal')" class="btn btn-ghost flex-1">İptal</button>
                <button type="submit" class="btn btn-primary flex-1"><i data-lucide="check" class="h-4 w-4"></i> Kaydet</button>
            </div>
        </form>
    </div>
</div>

<script>
const DRIVER_DOC_TYPES = [
    {type:'kimlik',label:'Kimlik'},{type:'ehliyet',label:'Ehliyet'},{type:'src5Psikoteknik',label:'SRC 5 Psikoteknik'},
    {type:'adliSicil',label:'Adli Sicil'},{type:'iseGirisBildirge',label:'İşe Giriş Bildirgesi'},
    {type:'ikametgah',label:'İkametgah'},{type:'kkdZimmet',label:'KKD Zimmet'},
    {type:'saglikMuayene',label:'Sağlık Muayene'},{type:'isgEgitimBelgesi',label:'İSG Eğitim Belgesi'},
    {type:'yanginEgitimSertifikasi',label:'Yangın Eğitim Sertifikası'}
];

let drivers = [];

document.addEventListener('DOMContentLoaded', async () => {
    try {
        drivers = await apiRequest('/api/drivers');
        renderDrivers();
    } catch (e) { showToast('Veriler yüklenemedi: ' + e.message, 'error'); }
});

function renderDrivers() {
    const container = document.getElementById('driverList');
    if (drivers.length === 0) {
        container.innerHTML = '<div class="text-center py-12 text-white/40"><i data-lucide="user-check" class="h-12 w-12 mx-auto mb-3 opacity-30"></i><p>Henüz şoför eklenmemiş</p></div>';
        lucide.createIcons({nodes:[container]}); return;
    }

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">';
    drivers.forEach(d => {
        const docs = d.documents || [];
        const uploaded = docs.filter(doc => doc.fileName).length;
        const expired = docs.filter(doc => doc.expiryDate && new Date(doc.expiryDate) < new Date()).length;

        html += `<div class="card p-4 hover:bg-white/[0.05] transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10">
                        <i data-lucide="user" class="h-5 w-5 text-emerald-400"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-white">${escapeHtml(d.name)}</div>
                        <div class="text-xs text-white/40">${escapeHtml(d.phone)} • TC: ${escapeHtml(d.tcNo)}</div>
                    </div>
                </div>
                <button onclick="deleteDriver('${d.id}')" class="h-8 w-8 flex items-center justify-center rounded-lg text-white/30 hover:text-red-400 hover:bg-red-500/10 transition">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                </button>
            </div>
            <div class="flex gap-2 mb-3">
                <span class="badge badge-blue">${uploaded}/${docs.length} Evrak</span>
                ${expired > 0 ? `<span class="badge badge-red">${expired} Süresi Dolmuş</span>` : ''}
            </div>
            <div class="space-y-1.5 max-h-48 overflow-y-auto">
                ${docs.map(doc => `
                    <div class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-white/[0.02] text-xs">
                        <span class="text-white/60">${escapeHtml(doc.label || doc.type)}</span>
                        <div class="flex items-center gap-2">
                            ${doc.expiryDate ? `<span class="${getExpiryClass(doc.expiryDate)}">${getExpiryText(doc.expiryDate)}</span>` : ''}
                            ${doc.fileName ? '<span class="text-emerald-400">✓</span>' : '<span class="text-white/20">—</span>'}
                            <label class="cursor-pointer text-blue-400 hover:text-blue-300">
                                <i data-lucide="upload" class="h-3 w-3"></i>
                                <input type="file" class="hidden" onchange="uploadDriverDoc(this, '${d.id}', '${doc.type}')">
                            </label>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons({nodes:[container]});
}

async function addDriver(e) {
    e.preventDefault();
    const data = {
        name: document.getElementById('driverName').value.trim(),
        tcNo: document.getElementById('driverTcNo').value.trim(),
        phone: document.getElementById('driverPhone').value.trim(),
        documents: DRIVER_DOC_TYPES
    };
    try {
        await apiRequest('/api/drivers', { method: 'POST', body: JSON.stringify(data) });
        closeModal('addDriverModal');
        showToast('Şoför eklendi');
        drivers = await apiRequest('/api/drivers');
        renderDrivers();
    } catch (e) { showToast('Kayıt hatası: ' + e.message, 'error'); }
}

async function deleteDriver(id) {
    if (!confirmAction('Bu şoförü silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/drivers', { method: 'DELETE', body: JSON.stringify({ id }) });
        drivers = drivers.filter(d => d.id !== id);
        renderDrivers(); showToast('Şoför silindi');
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}

async function uploadDriverDoc(input, driverId, docType) {
    const file = input.files[0]; if (!file) return;
    try {
        await uploadFile(file, driverId, 'driver', docType);
        showToast('Dosya yüklendi');
        drivers = await apiRequest('/api/drivers');
        renderDrivers();
    } catch (e) { showToast('Yükleme hatası: ' + e.message, 'error'); }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
