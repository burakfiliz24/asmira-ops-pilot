<?php
/**
 * Asmira Ops - Araç Detay Sayfası
 */
$vehicleId = $_GET['id'] ?? '';
$pageTitle = 'Araç Detay';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/vehicle-documents';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex items-center gap-3">
        <a href="javascript:history.back()" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white" id="vehicleTitle">Yükleniyor...</h1>
            <p class="text-sm text-white/50 mt-1" id="vehicleSubtitle"></p>
        </div>
    </div>

    <div id="vehicleContent">
        <div class="flex items-center justify-center py-12"><div class="spinner"></div></div>
    </div>
</div>

<script>
const VEHICLE_ID = '<?= htmlspecialchars($vehicleId) ?>';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [trucks, trailers] = await Promise.all([
            apiRequest('/api/trucks'),
            apiRequest('/api/trailers'),
        ]);
        
        let vehicle = trucks.find(t => t.id === VEHICLE_ID);
        let vType = 'truck';
        if (!vehicle) {
            vehicle = trailers.find(t => t.id === VEHICLE_ID);
            vType = 'trailer';
        }
        
        if (!vehicle) {
            document.getElementById('vehicleTitle').textContent = 'Araç bulunamadı';
            document.getElementById('vehicleContent').innerHTML = '<div class="text-center py-12 text-white/40"><p>Bu ID ile araç bulunamadı</p><a href="/vehicle-documents" class="btn btn-ghost mt-4">Geri Dön</a></div>';
            return;
        }

        document.getElementById('vehicleTitle').textContent = vehicle.plate;
        document.getElementById('vehicleSubtitle').textContent = `${vType === 'truck' ? 'Çekici' : 'Dorse'} • ${vehicle.category === 'asmira' ? 'Asmira Özmal' : 'Tedarikçi'}`;

        const docs = vehicle.documents || [];
        let html = '<div class="card overflow-hidden"><table class="data-table"><thead><tr>';
        html += '<th>Evrak</th><th>Dosya</th><th>Son Kullanma</th><th>Durum</th><th class="text-right">İşlem</th>';
        html += '</tr></thead><tbody>';

        docs.forEach(d => {
            const hasFile = !!d.fileName;
            const expiryClass = d.expiryDate ? getExpiryClass(d.expiryDate) : '';
            html += `<tr>
                <td class="font-medium">${escapeHtml(d.label || d.type)}</td>
                <td>${hasFile ? `<span class="text-emerald-400 text-xs">${escapeHtml(d.fileName)}</span>` : '<span class="text-white/20">Yüklenmemiş</span>'}</td>
                <td><span class="${expiryClass}">${d.expiryDate ? formatDate(d.expiryDate) : '-'}</span></td>
                <td>${hasFile ? '<span class="badge badge-green">Tamam</span>' : '<span class="badge badge-gray">Eksik</span>'}</td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        ${hasFile ? `<a href="/api/documents/download/${d.filePath}" target="_blank" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-white/40 hover:text-blue-400 hover:bg-blue-500/10 transition"><i data-lucide="download" class="h-3.5 w-3.5"></i></a>` : ''}
                        <label class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-white/40 hover:text-blue-400 hover:bg-blue-500/10 transition cursor-pointer">
                            <i data-lucide="upload" class="h-3.5 w-3.5"></i>
                            <input type="file" class="hidden" onchange="uploadDoc(this, '${vehicle.id}', '${vType}', '${d.type}')">
                        </label>
                        <button onclick="setExpiry('${vehicle.id}', '${vType}', '${d.type}')" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-white/40 hover:text-yellow-400 hover:bg-yellow-500/10 transition"><i data-lucide="calendar" class="h-3.5 w-3.5"></i></button>
                    </div>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        document.getElementById('vehicleContent').innerHTML = html;
        lucide.createIcons();
    } catch (e) {
        document.getElementById('vehicleContent').innerHTML = `<div class="text-center py-12 text-red-400"><p>Hata: ${escapeHtml(e.message)}</p></div>`;
    }
});

async function uploadDoc(input, ownerId, ownerType, docType) {
    const file = input.files[0]; if (!file) return;
    try {
        await uploadFile(file, ownerId, ownerType, docType);
        showToast('Dosya yüklendi');
        location.reload();
    } catch (e) { showToast('Yükleme hatası: ' + e.message, 'error'); }
}

async function setExpiry(ownerId, ownerType, docType) {
    const date = prompt('Son kullanma tarihi (YYYY-MM-DD):');
    if (!date) return;
    try {
        await apiRequest('/api/documents/update', {
            method: 'PUT',
            body: JSON.stringify({ ownerId, ownerType, docType, expiryDate: date })
        });
        showToast('Tarih güncellendi');
        location.reload();
    } catch (e) { showToast('Güncelleme hatası: ' + e.message, 'error'); }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
