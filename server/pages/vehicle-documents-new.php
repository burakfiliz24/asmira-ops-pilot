<?php
/**
 * Asmira Ops - Yeni Araç Ekle
 */
$pageTitle = 'Yeni Araç Ekle';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/vehicle-documents';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex items-center gap-3">
        <a href="/vehicle-documents" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Yeni Araç Ekle</h1>
            <p class="text-sm text-white/50 mt-1">Çekici veya dorse ekleyin</p>
        </div>
    </div>

    <div class="card p-6 max-w-xl">
        <form id="newVehicleForm" onsubmit="saveVehicle(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Araç Tipi *</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="vehicleType" value="truck" checked class="hidden peer">
                        <div class="peer-checked:border-blue-500/60 peer-checked:bg-blue-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-blue-400">
                            <i data-lucide="truck" class="h-5 w-5"></i> Çekici
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="vehicleType" value="trailer" class="hidden peer">
                        <div class="peer-checked:border-blue-500/60 peer-checked:bg-blue-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-blue-400">
                            <i data-lucide="container" class="h-5 w-5"></i> Dorse
                        </div>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Kategori *</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="category" value="asmira" checked class="hidden peer">
                        <div class="peer-checked:border-blue-500/60 peer-checked:bg-blue-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-blue-400">
                            Asmira Özmal
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="category" value="supplier" class="hidden peer">
                        <div class="peer-checked:border-purple-500/60 peer-checked:bg-purple-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-medium text-white/70 transition peer-checked:text-purple-400">
                            Tedarikçi
                        </div>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Plaka *</label>
                <input type="text" id="plate" class="input-field" placeholder="34 ABC 123" required>
            </div>
            <div class="flex gap-3 pt-2">
                <a href="/vehicle-documents" class="btn btn-ghost flex-1">İptal</a>
                <button type="submit" class="btn btn-primary flex-1" id="saveBtn">
                    <i data-lucide="check" class="h-4 w-4"></i> Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const DOC_TYPES = [
    {type:'ruhsat',label:'Ruhsat'},{type:'tasitKarti',label:'Taşıt Kartı'},{type:'t9Adr',label:'T9 ADR'},
    {type:'trafikSigortasi',label:'Trafik Sigortası'},{type:'tehlikeliMaddeSigortasi',label:'Tehlikeli Madde Sigortası'},
    {type:'kasko',label:'Kasko'},{type:'tuvturk',label:'TÜVTÜRK'},{type:'egzozEmisyon',label:'Egzoz Emisyon'},
    {type:'sayacKalibrasyon',label:'Sayaç Kalibrasyon'},{type:'takografKalibrasyon',label:'Takograf Kalibrasyon'},
    {type:'faaliyetBelgesi',label:'Faaliyet Belgesi'},{type:'yetkiBelgesi',label:'Yetki Belgesi'},
    {type:'hortumBasin',label:'Hortum Basın.'},{type:'tankMuayeneSertifikasi',label:'Tank Muayene Sertifikası'},
    {type:'vergiLevhasi',label:'Vergi Levhası'}
];

async function saveVehicle(e) {
    e.preventDefault();
    const type = document.querySelector('input[name="vehicleType"]:checked').value;
    const category = document.querySelector('input[name="category"]:checked').value;
    const plate = document.getElementById('plate').value.trim();
    if (!plate) { showToast('Plaka gerekli', 'error'); return; }

    document.getElementById('saveBtn').disabled = true;
    try {
        const endpoint = type === 'truck' ? '/api/trucks' : '/api/trailers';
        await apiRequest(endpoint, {
            method: 'POST',
            body: JSON.stringify({ plate, category, documents: DOC_TYPES })
        });
        showToast('Araç eklendi');
        setTimeout(() => {
            window.location.href = category === 'asmira' ? '/vehicle-documents/asmira' : '/vehicle-documents/suppliers';
        }, 500);
    } catch (err) {
        showToast('Kayıt hatası: ' + err.message, 'error');
        document.getElementById('saveBtn').disabled = false;
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
