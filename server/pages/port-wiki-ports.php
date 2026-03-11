<?php
/**
 * Asmira Ops - Liman Listesi
 */
$pageTitle = 'Liman Listesi';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/port-wiki';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex items-center gap-3">
        <a href="/port-wiki" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Liman Listesi</h1>
            <p class="text-sm text-white/50 mt-1">Türkiye limanları detaylı bilgi</p>
        </div>
    </div>

    <div class="mb-4">
        <input type="text" id="searchPort" class="input-field max-w-sm" placeholder="Liman ara..." oninput="filterPorts()">
    </div>

    <div class="card overflow-hidden">
        <table class="data-table">
            <thead><tr><th>Liman</th><th>Şehir</th><th>Tür</th><th>Kapasite</th><th>Not</th></tr></thead>
            <tbody id="portTableBody"></tbody>
        </table>
    </div>
</div>

<script>
const PORTS = [
    {name:'Aliağa',city:'İzmir',type:'Petrokimya',capacity:'Büyük',note:'SOCAR, Tüpraş yakınında'},
    {name:'Nemrut Körfezi',city:'İzmir',type:'Genel',capacity:'Orta',note:'Demirleme bölgesi'},
    {name:'Tuzla',city:'İstanbul',type:'Tersane',capacity:'Büyük',note:'Tersane bölgesi, yat ikmal'},
    {name:'Ambarlı',city:'İstanbul',type:'Konteyner',capacity:'Büyük',note:'Marport, Kumport'},
    {name:'Haydarpaşa',city:'İstanbul',type:'Genel',capacity:'Orta',note:'Boğaz geçişi'},
    {name:'Mersin',city:'Mersin',type:'Uluslararası',capacity:'Büyük',note:'Serbest bölge'},
    {name:'İskenderun',city:'Hatay',type:'Demir Çelik',capacity:'Büyük',note:'İsdemir yakınında'},
    {name:'Yılport Gebze',city:'Kocaeli',type:'Konteyner',capacity:'Büyük',note:'Konteyner terminali'},
    {name:'Gemlik',city:'Bursa',type:'Genel',capacity:'Orta',note:'Gemport terminali'},
    {name:'Derince',city:'Kocaeli',type:'Genel',capacity:'Orta',note:'Tahıl terminali'},
    {name:'Bandırma',city:'Balıkesir',type:'Genel',capacity:'Orta',note:'Çimento, tahıl'},
    {name:'Antalya',city:'Antalya',type:'Kruvaziyer',capacity:'Orta',note:'Yat limanı'},
    {name:'Çanakkale',city:'Çanakkale',type:'Genel',capacity:'Küçük',note:'Boğaz geçişi'},
    {name:'Samsun',city:'Samsun',type:'Genel',capacity:'Orta',note:'Karadeniz limanı'},
    {name:'Trabzon',city:'Trabzon',type:'Genel',capacity:'Orta',note:'Karadeniz limanı'},
    {name:'Ceyhan',city:'Adana',type:'Petrol',capacity:'Büyük',note:'BTC boru hattı terminali'},
];

function filterPorts() {
    const q = document.getElementById('searchPort').value.toLowerCase();
    const filtered = PORTS.filter(p => p.name.toLowerCase().includes(q) || p.city.toLowerCase().includes(q));
    renderPorts(filtered);
}

function renderPorts(ports) {
    const tbody = document.getElementById('portTableBody');
    tbody.innerHTML = ports.map(p => `<tr>
        <td class="font-medium">${escapeHtml(p.name)}</td>
        <td>${escapeHtml(p.city)}</td>
        <td><span class="badge badge-blue">${escapeHtml(p.type)}</span></td>
        <td>${escapeHtml(p.capacity)}</td>
        <td class="text-white/50 text-xs">${escapeHtml(p.note)}</td>
    </tr>`).join('');
}

document.addEventListener('DOMContentLoaded', () => renderPorts(PORTS));
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
