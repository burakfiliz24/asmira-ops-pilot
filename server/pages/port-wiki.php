<?php
/**
 * Asmira Ops - Port Wiki
 * Orijinal React sayfasının birebir PHP karşılığı
 */
$pageTitle = 'Port Wiki';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/port-wiki';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-cyan-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/60 via-cyan-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">PORT WİKİ</div>
                <div class="text-3xl font-black tracking-tight">Operasyonel El Kitabı</div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
                    <div class="h-2 w-2 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.6)]"></div>
                    <span class="text-xs font-medium text-white/70">Liman</span>
                    <span class="text-sm font-bold text-white" id="portCount">0</span>
                </div>
                <button type="button" onclick="openAddPortModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-cyan-600 to-cyan-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(6,182,212,0.25)] transition-all hover:from-cyan-500 hover:to-cyan-600">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Yeni Liman
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative flex flex-none items-center gap-4 px-6 py-3">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/30 via-cyan-400/15 to-transparent"></div>
            <div class="relative flex-1">
                <i data-lucide="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-cyan-400/60"></i>
                <input type="text" id="searchInput" oninput="renderPorts()" placeholder="Liman, şehir veya bölge ara..." class="h-11 w-full rounded-xl border border-cyan-500/20 bg-white/[0.03] pl-12 pr-4 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/40 focus:bg-white/[0.05] transition-all">
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6" id="portsContent">
            <div class="flex items-center justify-center py-12"><div class="spinner"></div></div>
        </div>
    </div>
</div>

<!-- Port Detail Panel -->
<div id="portDetailPanel" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <button type="button" class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePortDetail()"></button>
    <div class="relative z-10 flex h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur-xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.65)]">
        <div class="flex flex-col gap-3 border-b border-white/10 px-4 py-4 sm:px-6" id="detailHeader"></div>
        <div class="flex-1 overflow-y-auto p-6" id="detailContent"></div>
    </div>
</div>

<!-- Add Port Modal -->
<div id="addPortModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('addPortModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/60 via-cyan-400/30 to-transparent"></div>
            <div><div class="text-sm font-light tracking-[0.2em] text-slate-400">YENİ LİMAN</div><div class="text-lg font-bold">Liman Bilgileri</div></div>
            <button type="button" onclick="closeModal('addPortModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="mb-2 block text-xs font-semibold text-white/70">Liman Adı *</label><input type="text" id="newPortName" placeholder="Örn: Alsancak Limanı" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"></div>
                <div><label class="mb-2 block text-xs font-semibold text-white/70">Şehir *</label><input type="text" id="newPortCity" placeholder="Örn: İzmir" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"></div>
            </div>
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Bölge *</label>
                <div class="grid grid-cols-4 gap-2" id="addPortRegionBtns"></div>
            </div>
            <div><label class="mb-2 block text-xs font-semibold text-white/70">Kritik Uyarı (Opsiyonel)</label><input type="text" id="newPortWarning" placeholder="Örn: Akşam ikmaline izin verilmemektedir!" class="h-11 w-full rounded-md border border-amber-500/30 bg-amber-500/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-amber-500/50"></div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('addPortModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="saveNewPort()" class="rounded-lg bg-gradient-to-br from-cyan-600 to-cyan-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(6,182,212,0.25)] transition-all hover:from-cyan-500 hover:to-cyan-600">Liman Ekle</button>
        </div>
    </div>
</div>

<script>
const ALL_REGIONS = ['Ege','Marmara','Akdeniz','Karadeniz'];
const regionColors = {
    Ege:       { bg:'from-emerald-500/20 to-emerald-600/5', border:'border-emerald-500/30 hover:border-emerald-400/50', icon:'from-emerald-500/40 to-emerald-600/20 text-emerald-300', glow:'bg-emerald-500/20', gradient:'from-emerald-400 to-teal-500' },
    Marmara:   { bg:'from-blue-500/20 to-blue-600/5', border:'border-blue-500/30 hover:border-blue-400/50', icon:'from-blue-500/40 to-blue-600/20 text-blue-300', glow:'bg-blue-500/20', gradient:'from-blue-400 to-indigo-500' },
    Akdeniz:   { bg:'from-amber-500/20 to-orange-600/5', border:'border-amber-500/30 hover:border-amber-400/50', icon:'from-amber-500/40 to-orange-600/20 text-amber-300', glow:'bg-amber-500/20', gradient:'from-amber-400 to-orange-500' },
    Karadeniz: { bg:'from-purple-500/20 to-violet-600/5', border:'border-purple-500/30 hover:border-purple-400/50', icon:'from-purple-500/40 to-violet-600/20 text-purple-300', glow:'bg-purple-500/20', gradient:'from-purple-400 to-violet-500' },
};

const DEFAULT_PORTS = [
    {id:'alsancak',name:'Alsancak Limanı',city:'İzmir',region:'Ege',criticalWarning:'Akşam ikmaline izin verilmemektedir!',documents:['Ehliyet','Kimlik','T9 Belgesi','Araç Ruhsatı'],technicalData:{maxDraft:'12m',vhfChannel:'Kanal 12',workingHours:'08:00 - 18:00',anchorage:'Dış Liman'},contacts:[{name:'Liman Güvenlik',phone:'+90 232 463 00 00',role:'Güvenlik'},{name:'Operasyon Merkezi',phone:'+90 232 463 00 01',role:'Operasyon'}],notes:['Giriş için 24 saat önceden bildirim gerekli','Tehlikeli madde taşımacılığı için özel izin alınmalı']},
    {id:'aliaga',name:'Aliağa Limanı',city:'İzmir',region:'Ege',criticalWarning:'ISPS kod uygulaması aktif - Giriş kartı zorunlu!',documents:['Ehliyet','Kimlik','SRC Belgesi','ADR Belgesi','Araç Ruhsatı'],technicalData:{maxDraft:'15m',vhfChannel:'Kanal 16',workingHours:'24 Saat',anchorage:'Nemrut Körfezi'},contacts:[{name:'SOCAR Terminal',phone:'+90 232 616 00 00',role:'Terminal'},{name:'Petkim Limanı',phone:'+90 232 616 12 00',role:'Liman'}],notes:['Rafineri alanına giriş için özel eğitim sertifikası gerekli']},
    {id:'ambarli',name:'Ambarlı Limanı',city:'İstanbul',region:'Marmara',documents:['Ehliyet','Kimlik','Araç Ruhsatı','Yetki Belgesi'],technicalData:{maxDraft:'14m',vhfChannel:'Kanal 13',workingHours:'24 Saat'},contacts:[{name:'Marport Terminal',phone:'+90 212 875 00 00',role:'Terminal'},{name:'Kumport Terminal',phone:'+90 212 875 10 00',role:'Terminal'}]},
    {id:'haydarpasa',name:'Haydarpaşa Limanı',city:'İstanbul',region:'Marmara',criticalWarning:'Liman yenileme çalışmaları devam ediyor - Sınırlı erişim!',documents:['Ehliyet','Kimlik','Araç Ruhsatı'],technicalData:{maxDraft:'10m',vhfChannel:'Kanal 12',workingHours:'08:00 - 20:00'},contacts:[{name:'Liman Müdürlüğü',phone:'+90 216 348 80 20',role:'Yönetim'}]},
    {id:'mersin',name:'Mersin Limanı',city:'Mersin',region:'Akdeniz',documents:['Ehliyet','Kimlik','T9 Belgesi','Araç Ruhsatı','ADR Belgesi'],technicalData:{maxDraft:'16m',vhfChannel:'Kanal 14',workingHours:'24 Saat',anchorage:'Mersin Açıkları'},contacts:[{name:'MIP Terminal',phone:'+90 324 241 27 00',role:'Terminal'},{name:'Liman Başkanlığı',phone:'+90 324 238 50 00',role:'Yönetim'}],notes:["Türkiye'nin en büyük konteynır limanı"]},
    {id:'iskenderun',name:'İskenderun Limanı',city:'Hatay',region:'Akdeniz',documents:['Ehliyet','Kimlik','Araç Ruhsatı'],technicalData:{maxDraft:'13m',vhfChannel:'Kanal 16',workingHours:'24 Saat'},contacts:[{name:'Limak Terminal',phone:'+90 326 614 00 00',role:'Terminal'}]},
    {id:'bodrum',name:'Bodrum Cruise Port',city:'Muğla',region:'Ege',criticalWarning:'Yat limanı - Ticari araç girişi kısıtlı!',documents:['Ehliyet','Kimlik','Gümrük Beyannamesi'],technicalData:{maxDraft:'8m',vhfChannel:'Kanal 12',workingHours:'08:00 - 22:00'},contacts:[{name:'Marina Ofis',phone:'+90 252 316 18 60',role:'Marina'},{name:'Gümrük Müdürlüğü',phone:'+90 252 316 10 00',role:'Gümrük'}],notes:['Milli ikmal için gümrük müdürlüğünden izin alınmalı']},
    {id:'gocek',name:'Göcek Limanı',city:'Muğla',region:'Ege',documents:['Ehliyet','Kimlik'],technicalData:{maxDraft:'6m',vhfChannel:'Kanal 16',workingHours:'08:00 - 20:00'},contacts:[{name:'D-Marin Göcek',phone:'+90 252 645 27 60',role:'Marina'}]},
];

let ports = [];
let selectedPortId = null;
let isEditing = false;
let editForm = null;
let addPortRegion = 'Ege';

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('asmira-port-wiki');
    if (saved) { try { ports = JSON.parse(saved); } catch(e) { ports = [...DEFAULT_PORTS]; } }
    else { ports = [...DEFAULT_PORTS]; }
    ports.forEach(p => { if (!p.technicalData) p.technicalData = {}; if (!p.notes) p.notes = []; if (!p.contacts) p.contacts = []; if (!p.documents) p.documents = []; });
    document.getElementById('portCount').textContent = ports.length;
    renderPorts();
});

function saveToStorage() { localStorage.setItem('asmira-port-wiki', JSON.stringify(ports)); }

function renderPorts() {
    const q = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    const filtered = q ? ports.filter(p => p.name.toLowerCase().includes(q) || p.city.toLowerCase().includes(q) || p.region.toLowerCase().includes(q)) : ports;
    const content = document.getElementById('portsContent');
    document.getElementById('portCount').textContent = ports.length;

    if (filtered.length === 0) {
        content.innerHTML = '<div class="flex flex-col items-center justify-center py-16 text-center"><i data-lucide="search" class="mb-4 h-12 w-12 text-white/20"></i><div class="text-lg font-medium text-white/60">Sonuç bulunamadı</div><div class="mt-1 text-sm text-white/40">Farklı bir arama terimi deneyin</div></div>';
        lucide.createIcons({nodes:[content]}); return;
    }

    const regions = [...new Set(filtered.map(p => p.region))];
    let html = '';
    regions.forEach(region => {
        const rPorts = filtered.filter(p => p.region === region);
        const rc = regionColors[region] || regionColors.Ege;
        html += `<div class="mb-8"><h2 class="mb-4 flex items-center gap-3"><div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] ${rc.icon}"><i data-lucide="map-pin" class="h-4 w-4"></i></div><span class="text-sm font-semibold tracking-[0.2em] bg-gradient-to-r ${rc.gradient} bg-clip-text text-transparent">${region.toUpperCase()} BÖLGESİ</span></h2><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">${rPorts.map(p => {
            const c = regionColors[p.region] || regionColors.Ege;
            return `<button type="button" onclick="openPortDetail('${p.id}')" class="group relative flex flex-col rounded-2xl border p-5 text-left transition-all duration-300 overflow-hidden bg-gradient-to-br ${c.bg} ${c.border} hover:scale-[1.02] hover:shadow-[0_8px_40px_rgba(0,0,0,0.3)] ${p.criticalWarning ? 'ring-2 ring-red-500/40' : ''}">
                <div class="pointer-events-none absolute -right-12 -top-12 h-32 w-32 rounded-full blur-3xl transition-all duration-500 opacity-40 group-hover:opacity-70 ${c.glow}"></div>
                <div class="pointer-events-none absolute -left-8 -bottom-8 h-24 w-24 rounded-full blur-2xl transition-all duration-500 opacity-20 group-hover:opacity-50 ${c.glow}"></div>
                <div class="relative mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 ${c.icon}"><i data-lucide="anchor" class="h-7 w-7 drop-shadow-lg"></i><div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-white/20 via-transparent to-transparent"></div></div>
                <div class="relative mb-1"><div class="text-lg font-bold tracking-tight text-white">${escapeHtml(p.name)}</div></div>
                <div class="text-sm font-medium text-white/60 mb-3">${escapeHtml(p.city)}</div>
                ${p.criticalWarning ? '<div class="mb-3 flex items-center gap-2 rounded-lg bg-red-500/20 border border-red-500/30 px-3 py-2"><i data-lucide="alert-triangle" class="h-4 w-4 text-red-400"></i><span class="text-xs font-semibold text-red-300">Kritik Uyarı!</span></div>' : ''}
                <div class="mt-auto flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-medium text-white/70"><i data-lucide="file-text" class="h-3 w-3"></i>${(p.documents||[]).length} evrak</div>
                    ${(p.contacts||[]).length > 0 ? `<div class="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-medium text-white/70"><i data-lucide="phone" class="h-3 w-3"></i>${p.contacts.length} kişi</div>` : ''}
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r ${c.gradient} opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
            </button>`;
        }).join('')}</div></div>`;
    });
    content.innerHTML = html;
    lucide.createIcons({nodes:[content]});
}

// ===== DETAIL PANEL =====
function openPortDetail(id) {
    selectedPortId = id; isEditing = false; editForm = null;
    renderDetail();
    document.getElementById('portDetailPanel').classList.remove('hidden');
}
function closePortDetail() {
    document.getElementById('portDetailPanel').classList.add('hidden');
    selectedPortId = null; isEditing = false; editForm = null;
}

function renderDetail() {
    const p = isEditing ? editForm : ports.find(x => x.id === selectedPortId);
    if (!p) return;
    const hdr = document.getElementById('detailHeader');
    if (isEditing) {
        hdr.innerHTML = `<div class="flex items-center justify-between"><input type="text" value="${escapeHtml(p.name)}" onchange="editForm.name=this.value" class="flex-1 mr-3 rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-lg font-semibold outline-none placeholder:text-white/40 focus:border-cyan-500/50" placeholder="Liman Adı"><button type="button" onclick="closePortDetail()" class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-white/10 text-white/60 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button></div>
        <div class="flex flex-wrap items-center gap-2"><input type="text" value="${escapeHtml(p.city)}" onchange="editForm.city=this.value" class="w-24 rounded-lg border border-white/20 bg-white/5 px-3 py-1.5 text-sm outline-none focus:border-cyan-500/50" placeholder="Şehir"><span class="text-white/30">-</span><select onchange="editForm.region=this.value" class="w-28 rounded-lg border border-white/20 bg-white/5 px-3 py-1.5 text-sm outline-none focus:border-cyan-500/50">${ALL_REGIONS.map(r=>`<option value="${r}" ${p.region===r?'selected':''}>${r}</option>`).join('')}</select><div class="ml-auto flex items-center gap-2"><button type="button" onclick="saveEdit()" class="flex items-center gap-1.5 rounded-lg bg-emerald-500/20 px-3 py-1.5 text-xs font-medium text-emerald-400 hover:bg-emerald-500/30"><i data-lucide="save" class="h-3.5 w-3.5"></i>Kaydet</button><button type="button" onclick="cancelEdit()" class="flex items-center gap-1.5 rounded-lg bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 hover:bg-white/10">İptal</button></div></div>`;
    } else {
        hdr.innerHTML = `<div class="flex items-center justify-between"><div><div class="text-xl font-semibold">${escapeHtml(p.name)}</div><div class="text-sm text-white/50">${escapeHtml(p.city)} - ${p.region}</div></div><div class="flex items-center gap-2"><button type="button" onclick="startEdit()" class="flex items-center gap-1.5 rounded-lg bg-blue-500/20 px-3 py-2 text-xs font-medium text-blue-400 hover:bg-blue-500/30"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i>Düzenle</button><button type="button" onclick="deleteSelectedPort()" class="flex items-center gap-1.5 rounded-lg bg-red-500/20 px-3 py-2 text-xs font-medium text-red-400 hover:bg-red-500/30"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i>Sil</button><button type="button" onclick="closePortDetail()" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/60 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button></div></div>`;
    }

    let html = '';
    // Critical Warning
    if (isEditing) {
        html += `<div class="mb-6"><div class="mb-3 flex items-center gap-2 text-sm font-semibold"><i data-lucide="ban" class="h-4 w-4 text-red-400"></i>Kritik Uyarı</div><div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4"><input type="text" value="${escapeHtml(p.criticalWarning||'')}" onchange="editForm.criticalWarning=this.value||undefined" class="w-full rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-200 outline-none placeholder:text-red-300/50 focus:border-red-500/50" placeholder="Kritik uyarı ekleyin (boş bırakılabilir)..."><div class="mt-2 text-[11px] text-red-300/60">Boş bırakırsanız kritik uyarı kaldırılır.</div></div></div>`;
    } else if (p.criticalWarning) {
        html += `<div class="mb-6 flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 p-4"><i data-lucide="ban" class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-400"></i><div><div class="text-sm font-semibold text-red-400">Kritik Uyarı</div><div class="mt-1 text-sm text-red-300/80">${escapeHtml(p.criticalWarning)}</div></div></div>`;
    }


    // Documents
    const docs = p.documents || [];
    html += `<div class="mb-6"><div class="mb-3 flex items-center justify-between"><div class="flex items-center gap-2 text-sm font-semibold"><i data-lucide="file-text" class="h-4 w-4 text-emerald-400"></i>Gerekli Evraklar</div>`;
    if (!isEditing) html += `<button type="button" onclick="copyDocuments()" id="copyDocsBtn" class="flex items-center gap-1.5 rounded-lg bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 hover:bg-white/10 hover:text-white"><i data-lucide="copy" class="h-3.5 w-3.5"></i>Kopyala</button>`;
    html += `</div><div class="space-y-2 rounded-xl border border-white/10 bg-white/[0.03] p-4">`;
    docs.forEach((d, i) => {
        html += `<div class="flex items-center justify-between gap-3"><div class="flex items-center gap-3"><div class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-500/20"><i data-lucide="shield-check" class="h-3.5 w-3.5 text-emerald-400"></i></div><span class="text-sm">${escapeHtml(d)}</span></div>${isEditing ? `<button type="button" onclick="removeDoc(${i})" class="flex h-6 w-6 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>` : ''}</div>`;
    });
    if (isEditing) {
        html += `<div class="mt-3 flex items-center gap-2 border-t border-white/10 pt-3"><input type="text" id="newDocInput" class="flex-1 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25" placeholder="Yeni evrak ekle..." onkeydown="if(event.key==='Enter')addDoc()"><button type="button" onclick="addDoc()" class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30"><i data-lucide="plus" class="h-4 w-4"></i></button></div>`;
    }
    html += `</div></div>`;

    // Contacts
    const contacts = p.contacts || [];
    if (contacts.length > 0 || isEditing) {
        html += `<div class="mb-6"><div class="mb-3 flex items-center gap-2 text-sm font-semibold"><i data-lucide="phone" class="h-4 w-4 text-purple-400"></i>İletişim</div><div class="space-y-2">`;
        contacts.forEach((c, i) => {
            html += `<div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.03] p-4"><div><div class="text-sm font-medium">${escapeHtml(c.name)}</div><div class="text-xs text-white/50">${escapeHtml(c.role)}</div></div><div class="flex items-center gap-2"><a href="tel:${c.phone}" class="flex items-center gap-2 rounded-lg bg-purple-500/20 px-3 py-2 text-xs font-medium text-purple-300 hover:bg-purple-500/30"><i data-lucide="phone" class="h-3.5 w-3.5"></i>${escapeHtml(c.phone)}</a>${isEditing ? `<button type="button" onclick="removeContact(${i})" class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 hover:bg-red-500/20"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>` : ''}</div></div>`;
        });
        if (isEditing) {
            html += `<div class="mt-3 space-y-2 border-t border-white/10 pt-3"><div class="grid grid-cols-3 gap-2"><input type="text" id="newContactName" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25" placeholder="İsim"><input type="text" id="newContactPhone" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25" placeholder="Telefon"><input type="text" id="newContactRole" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25" placeholder="Rol"></div><button type="button" onclick="addContact()" class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-purple-500/20 py-2 text-xs font-medium text-purple-400 hover:bg-purple-500/30"><i data-lucide="plus" class="h-3.5 w-3.5"></i>Kişi Ekle</button></div>`;
        }
        html += `</div></div>`;
    }

    // Notes
    const notes = p.notes || [];
    if (notes.length > 0 || isEditing) {
        html += `<div class="mb-6"><div class="mb-3 flex items-center gap-2 text-sm font-semibold"><i data-lucide="info" class="h-4 w-4 text-cyan-400"></i>Notlar</div><div class="space-y-2">`;
        notes.forEach((n, i) => {
            html += `<div class="flex items-center justify-between rounded-lg border border-white/10 bg-white/[0.03] px-4 py-3"><span class="text-sm text-white/70">${escapeHtml(n)}</span>${isEditing ? `<button type="button" onclick="removeNote(${i})" class="ml-2 flex h-6 w-6 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>` : ''}</div>`;
        });
        if (isEditing) {
            html += `<div class="mt-3 flex items-center gap-2 border-t border-white/10 pt-3"><input type="text" id="newNoteInput" class="flex-1 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25" placeholder="Yeni not ekle..." onkeydown="if(event.key==='Enter')addNote()"><button type="button" onclick="addNote()" class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-500/20 text-cyan-400 hover:bg-cyan-500/30"><i data-lucide="plus" class="h-4 w-4"></i></button></div>`;
        }
        html += `</div></div>`;
    }

    document.getElementById('detailContent').innerHTML = html;
    lucide.createIcons({nodes:[document.getElementById('detailHeader'), document.getElementById('detailContent')]});
}

// ===== EDIT MODE =====
function startEdit() {
    const p = ports.find(x => x.id === selectedPortId); if (!p) return;
    editForm = JSON.parse(JSON.stringify(p));
    isEditing = true;
    renderDetail();
}
function cancelEdit() { isEditing = false; editForm = null; renderDetail(); }
function saveEdit() {
    if (!editForm) return;
    ports = ports.map(p => p.id === editForm.id ? editForm : p);
    selectedPortId = editForm.id;
    isEditing = false; editForm = null;
    saveToStorage(); renderPorts(); renderDetail();
    showToast('Liman güncellendi');
}

function addDoc() { const el = document.getElementById('newDocInput'); const v = el.value.trim(); if (!v || !editForm) return; editForm.documents.push(v); el.value = ''; renderDetail(); }
function removeDoc(i) { if (!editForm) return; editForm.documents.splice(i, 1); renderDetail(); }
function addContact() { const n = document.getElementById('newContactName').value.trim(), ph = document.getElementById('newContactPhone').value.trim(), r = document.getElementById('newContactRole').value.trim(); if (!n || !ph || !editForm) return; editForm.contacts.push({name:n, phone:ph, role:r}); renderDetail(); }
function removeContact(i) { if (!editForm) return; editForm.contacts.splice(i, 1); renderDetail(); }
function addNote() { const el = document.getElementById('newNoteInput'); const v = el.value.trim(); if (!v || !editForm) return; if (!editForm.notes) editForm.notes = []; editForm.notes.push(v); el.value = ''; renderDetail(); }
function removeNote(i) { if (!editForm) return; editForm.notes.splice(i, 1); renderDetail(); }

function copyDocuments() {
    const p = ports.find(x => x.id === selectedPortId); if (!p) return;
    const text = `${p.name} için gerekli evraklar:\n\n${(p.documents||[]).map(d => '✓ ' + d).join('\n')}`;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyDocsBtn');
        btn.innerHTML = '<i data-lucide="check" class="h-3.5 w-3.5"></i>Kopyalandı';
        btn.className = 'flex items-center gap-1.5 rounded-lg bg-emerald-500/20 px-3 py-1.5 text-xs font-medium text-emerald-400';
        lucide.createIcons({nodes:[btn]});
        setTimeout(() => { btn.innerHTML = '<i data-lucide="copy" class="h-3.5 w-3.5"></i>Kopyala'; btn.className = 'flex items-center gap-1.5 rounded-lg bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 hover:bg-white/10 hover:text-white'; lucide.createIcons({nodes:[btn]}); }, 2000);
    });
}

function deleteSelectedPort() {
    if (!selectedPortId) return;
    const p = ports.find(x => x.id === selectedPortId);
    if (!confirmAction(`"${p?.name}" limanını silmek istediğinize emin misiniz?`)) return;
    ports = ports.filter(x => x.id !== selectedPortId);
    saveToStorage(); closePortDetail(); renderPorts(); showToast('Liman silindi');
}

// ===== ADD PORT MODAL =====
function openAddPortModal() {
    addPortRegion = 'Ege';
    document.getElementById('newPortName').value = '';
    document.getElementById('newPortCity').value = '';
    document.getElementById('newPortWarning').value = '';
    renderAddRegionBtns();
    openModal('addPortModal'); lucide.createIcons();
}
function renderAddRegionBtns() {
    document.getElementById('addPortRegionBtns').innerHTML = ALL_REGIONS.map(r => {
        const cls = r === addPortRegion ? 'border-cyan-500/50 bg-cyan-500/10 text-cyan-400' : 'border-white/10 bg-white/5 text-white/60 hover:bg-white/10';
        return `<button type="button" onclick="addPortRegion='${r}';renderAddRegionBtns()" class="rounded-lg border py-2.5 text-sm font-medium transition-all ${cls}">${r}</button>`;
    }).join('');
}
function saveNewPort() {
    const name = document.getElementById('newPortName').value.trim();
    const city = document.getElementById('newPortCity').value.trim();
    const warning = document.getElementById('newPortWarning').value.trim();
    if (!name || !city) { showToast('Lütfen liman adı ve şehir girin', 'error'); return; }
    ports.push({ id: 'port_'+Date.now(), name, city, region: addPortRegion, criticalWarning: warning || undefined, documents: [], contacts: [], notes: [], technicalData: {} });
    saveToStorage(); closeModal('addPortModal'); renderPorts(); showToast('Liman eklendi');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
