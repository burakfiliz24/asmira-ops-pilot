<?php
/**
 * Asmira Ops - Dashboard (Operasyon Takvimi)
 */
$pageTitle = 'Dashboard';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 lg:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/5 bg-transparent text-white" style="min-height: calc(100vh - 100px)">
        <!-- Header -->
        <div class="relative flex flex-none flex-col gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-3 py-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:px-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="changeMonth(-1)" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 transition-all hover:text-white hover:bg-white/10">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </button>
                <button type="button" onclick="changeMonth(1)" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 transition-all hover:text-white hover:bg-white/10">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </button>
                <div class="ml-1">
                    <div class="text-[10px] font-light tracking-[0.15em] text-slate-400 sm:text-sm sm:tracking-[0.2em]">BUNKER OPERASYON TAKVİMİ</div>
                    <div class="text-xl font-black tracking-tight sm:text-3xl" id="monthTitle"></div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openOperationModal()" class="btn btn-primary">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    İkmal Ekle
                </button>
            </div>
        </div>

        <!-- KPI Bar -->
        <div class="relative flex flex-none flex-wrap items-center gap-2 px-3 py-2 sm:gap-2.5 sm:px-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/40 via-cyan-400/20 to-transparent"></div>
            <div class="flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.03] px-2 py-1 sm:px-3 sm:py-1.5">
                <div class="h-1.5 w-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                <span class="text-[10px] font-medium text-white/70 sm:text-xs">İkmal</span>
                <span class="text-xs font-bold text-white sm:text-sm" id="kpiCount">0</span>
            </div>
            <div class="flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.03] px-2 py-1 sm:px-3 sm:py-1.5">
                <span class="text-[10px] font-medium text-white/70 sm:text-xs">Tonaj</span>
                <span class="text-xs font-bold text-white sm:text-sm" id="kpiTonaj">0</span>
                <span class="text-[8px] text-white/50 sm:text-[10px]">MT</span>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="flex flex-1 w-full flex-col overflow-hidden px-0 pb-2 sm:px-4 sm:pb-4">
            <div class="flex flex-1 w-full flex-col overflow-x-auto overflow-y-hidden">
                <div class="min-w-[600px] sm:min-w-0">
                    <!-- Week days header -->
                    <div class="grid h-6 grid-cols-7 border-b border-cyan-500/30 sm:h-8">
                        <?php foreach (['PZT','SAL','ÇAR','PER','CUM','CMT','PAZ'] as $day): ?>
                        <div class="flex items-center justify-center border-r border-cyan-500/30 px-1 text-[9px] font-semibold tracking-wide text-white drop-shadow-[0_0_4px_rgba(255,255,255,0.6)] last:border-r-0 sm:justify-start sm:px-2 sm:text-[10px]"><?= $day ?></div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Calendar cells -->
                    <div id="calendarGrid" class="grid grid-cols-7 grid-rows-6" style="height: calc(100% - 32px)"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Operation Modal -->
<div id="operationModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('operationModal')"></div>
    <div class="relative mx-4 w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl sm:mx-0">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div>
                <div class="text-sm font-semibold tracking-wider text-white/70" id="modalSubtitle">YENİ İKMAL</div>
                <div class="text-lg font-semibold" id="modalTitle">Operasyon Ekle</div>
            </div>
            <button type="button" onclick="closeModal('operationModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        <form id="operationForm" class="space-y-3 px-5 py-4" onsubmit="saveOperation(event)">
            <input type="hidden" id="editOpId" value="">
            <!-- Vessel Type -->
            <div class="flex gap-2">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="vesselType" value="ship" checked class="hidden peer">
                    <div class="peer-checked:border-blue-500/60 peer-checked:bg-blue-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white/70 transition peer-checked:text-blue-400">
                        <i data-lucide="ship" class="h-4 w-4"></i> Gemi
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="vesselType" value="yacht" class="hidden peer">
                    <div class="peer-checked:border-purple-500/60 peer-checked:bg-purple-500/10 flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white/70 transition peer-checked:text-purple-400">
                        <i data-lucide="sailboat" class="h-4 w-4"></i> Yat
                    </div>
                </label>
            </div>
            <!-- Vessel Name & IMO -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Gemi Adı *</label>
                    <input type="text" id="opVesselName" class="input-field" placeholder="M/T VESSEL NAME" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">IMO No</label>
                    <input type="text" id="opImoNumber" class="input-field" placeholder="1234567">
                </div>
            </div>
            <!-- Quantity & Unit -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Miktar *</label>
                    <input type="number" id="opQuantity" class="input-field" placeholder="0" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Birim</label>
                    <select id="opUnit" class="input-field">
                        <option value="MT">MT (Ton)</option>
                        <option value="L">L (Litre)</option>
                    </select>
                </div>
            </div>
            <!-- Loading Place & Port -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">Yükleme Yeri</label>
                    <input type="text" id="opLoadingPlace" class="input-field" placeholder="Terminal">
                </div>
                <div>
                    <label class="block text-xs font-medium text-white/50 mb-1">İkmal Limanı *</label>
                    <input type="text" id="opPort" class="input-field" placeholder="Liman" required>
                </div>
            </div>
            <!-- Date -->
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Tarih *</label>
                <input type="date" id="opDate" class="input-field" required>
            </div>
            <!-- Submit -->
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('operationModal')" class="btn btn-ghost flex-1">İptal</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <i data-lucide="check" class="h-4 w-4"></i>
                    <span id="opSubmitText">Kaydet</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ============ STATE ============
const MONTHS_TR = ['OCAK','ŞUBAT','MART','NİSAN','MAYIS','HAZİRAN','TEMMUZ','AĞUSTOS','EYLÜL','EKİM','KASIM','ARALIK'];
const STATUS_CLASSES = {
    planned: 'status-planned', approaching: 'status-approaching',
    active: 'status-active', completed: 'status-completed', cancelled: 'status-cancelled'
};
const STATUS_LABELS = {
    planned: '📋 Planlandı', approaching: '⚓ Yanaşıyor',
    active: '⛽ İkmal Başladı', completed: '✅ Tamamlandı', cancelled: '❌ İptal Edildi'
};

let currentYear = 2026;
let currentMonthIdx = new Date().getMonth();
let operations = [];
let dragId = null;

// ============ INIT ============
document.addEventListener('DOMContentLoaded', async () => {
    await loadOperations();
    renderCalendar();
});

async function loadOperations() {
    try {
        operations = await apiRequest('/api/operations');
    } catch (e) {
        console.warn('Operasyonlar yüklenemedi:', e);
        operations = [];
    }
}

// ============ CALENDAR ============
function changeMonth(dir) {
    currentMonthIdx += dir;
    if (currentMonthIdx < 0) { currentMonthIdx = 11; currentYear--; }
    if (currentMonthIdx > 11) { currentMonthIdx = 0; currentYear++; }
    renderCalendar();
}

function renderCalendar() {
    document.getElementById('monthTitle').textContent = `${MONTHS_TR[currentMonthIdx]} ${currentYear}`;

    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    const monthStart = new Date(currentYear, currentMonthIdx, 1);
    let startDay = monthStart.getDay() - 1;
    if (startDay < 0) startDay = 6;
    const gridStart = new Date(currentYear, currentMonthIdx, 1 - startDay);

    const today = new Date();
    today.setHours(0,0,0,0);

    // KPI
    const monthKey = `${currentYear}-${String(currentMonthIdx + 1).padStart(2, '0')}`;
    const monthOps = operations.filter(op => op.date && op.date.startsWith(monthKey));
    document.getElementById('kpiCount').textContent = monthOps.length;
    const totalTonaj = monthOps.filter(op => op.unit === 'MT').reduce((sum, op) => sum + Number(op.quantity || 0), 0);
    document.getElementById('kpiTonaj').textContent = totalTonaj.toLocaleString('tr-TR', { maximumFractionDigits: 2 });

    // 6 weeks × 7 days
    for (let i = 0; i < 42; i++) {
        const cellDate = new Date(gridStart);
        cellDate.setDate(gridStart.getDate() + i);
        const dateKey = toISODate(cellDate);
        const inMonth = cellDate.getMonth() === currentMonthIdx;
        const isToday = cellDate.getTime() === today.getTime();
        const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;

        const cell = document.createElement('div');
        cell.className = `group relative flex min-h-[100px] flex-col p-1 transition-colors sm:min-h-[120px] ${
            inMonth ? 'border-b border-r border-cyan-500/30' : 'pointer-events-none'
        } ${inMonth && isWeekend ? 'bg-white/[0.02]' : 'bg-transparent'} ${
            isToday && inMonth ? 'ring-2 ring-inset ring-blue-400/60 shadow-[0_0_0_1px_rgba(59,130,246,0.3),0_0_32px_rgba(59,130,246,0.35)]' : ''
        } ${inMonth ? 'hover:bg-white/5 cursor-pointer' : ''}`;

        if (inMonth) {
            cell.ondragover = e => e.preventDefault();
            cell.ondrop = e => handleDrop(e, dateKey);
            cell.ondblclick = () => openOperationModal(dateKey);
        }

        let html = '';
        if (inMonth) {
            html += `<div class="absolute right-1 top-0.5 z-0 text-[8px] font-semibold text-white/70 sm:right-2 sm:top-1 sm:text-[10px]">${cellDate.getDate()}</div>`;
            if (isToday) {
                html += `<div class="absolute left-1 top-1 z-20 rounded-full bg-white/10 px-1.5 py-0.5 text-[8px] font-semibold tracking-wider">BUGÜN</div>`;
            }

            const dayOps = operations.filter(op => op.date === dateKey);
            if (dayOps.length > 1) {
                html += `<div class="absolute left-1 top-1 z-20 flex h-5 w-5 items-center justify-center rounded-full bg-blue-500/80 text-[10px] font-bold text-white shadow-md">${dayOps.length}</div>`;
            }

            html += '<div class="mt-4 flex flex-col gap-y-1 overflow-y-auto pr-0.5">';
            dayOps.forEach(op => {
                const isYacht = op.vesselType === 'yacht';
                const vesselUpper = (op.vesselName || '').toLocaleUpperCase('tr-TR');
                const portUpper = (op.port || '-').toLocaleUpperCase('tr-TR');
                const unitLabel = op.unit === 'MT' ? 'TON' : (op.unit || 'MT').toLocaleUpperCase('tr-TR');
                const statusCls = STATUS_CLASSES[op.status] || STATUS_CLASSES.planned;
                const strikethrough = (op.status === 'completed' || op.status === 'cancelled') ? 'line-through opacity-70' : '';

                html += `<div class="group/card relative z-10 flex w-full cursor-grab select-none flex-col rounded-lg border px-2 py-2 text-white shadow-md backdrop-blur-md transition ${statusCls} ${
                    isYacht ? 'border-purple-500/20 bg-purple-500/[0.08]' : 'border-white/10 bg-white/[0.06]'
                }" draggable="true" ondragstart="startDrag(event, '${op.id}')" oncontextmenu="showOpContext(event, '${op.id}')">
                    <button type="button" onclick="event.stopPropagation(); deleteOperation('${op.id}')" class="absolute right-1 top-0.5 flex h-5 w-5 items-center justify-center rounded-md border border-red-500/30 bg-red-500/20 text-red-100 opacity-0 transition group-hover/card:opacity-100 hover:bg-red-500/30"><i data-lucide="x" class="h-3 w-3"></i></button>
                    <a href="https://www.vesselfinder.com/vessels?name=${encodeURIComponent(vesselUpper.replace(/^M\/[TVS]\s*/i,'').replace(/^MT\s*/i,'').replace(/^MV\s*/i,'').trim())}" target="_blank" onclick="event.stopPropagation()" class="absolute right-1 bottom-0.5 flex h-5 w-5 items-center justify-center rounded-md border border-cyan-500/30 bg-cyan-500/20 text-cyan-100 opacity-0 transition group-hover/card:opacity-100 hover:bg-cyan-500/30" title="Gemiyi Haritada Gör"><i data-lucide="map-pin" class="h-3 w-3"></i></a>
                    <div class="${strikethrough}">
                        <div class="text-[11px] font-bold uppercase leading-tight tracking-tight line-clamp-2 sm:text-[13px]">${escapeHtml(vesselUpper)}</div>
                        <div class="text-[10px] leading-tight text-white/70 sm:text-[12px]">${op.loadingPlace ? escapeHtml(op.loadingPlace.toLocaleUpperCase('tr-TR')) + ' → ' : ''}${escapeHtml(portUpper)}</div>
                        <div class="text-[10px] font-semibold leading-tight sm:text-[12px] ${isYacht ? 'text-purple-400' : 'text-blue-400'}">${op.quantity} ${unitLabel}</div>
                        ${isYacht ? '<div class="mt-0.5 text-[8px] font-bold uppercase tracking-wider text-purple-400/60">YAT</div>' : ''}
                    </div>
                </div>`;
            });
            html += '</div>';
        }

        cell.innerHTML = html;
        grid.appendChild(cell);
    }
    lucide.createIcons();
}

// ============ DRAG & DROP ============
function startDrag(e, id) {
    dragId = id;
    e.dataTransfer.setData('text/plain', id);
    e.dataTransfer.effectAllowed = 'move';
}
function handleDrop(e, dateKey) {
    e.preventDefault();
    const id = e.dataTransfer.getData('text/plain') || dragId;
    if (!id) return;
    const op = operations.find(o => o.id === id);
    if (op) {
        op.date = dateKey;
        apiRequest('/api/operations', {
            method: 'PUT',
            body: JSON.stringify({ id, date: dateKey })
        }).catch(e => console.warn('Drag update failed:', e));
        renderCalendar();
    }
    dragId = null;
}

// ============ CONTEXT MENU ============
function showOpContext(e, opId) {
    e.preventDefault();
    e.stopPropagation();
    const op = operations.find(o => o.id === opId);
    if (!op) return;

    // Simple context: prompt for status change
    const statuses = ['planned','approaching','active','completed','cancelled'];
    const labels = Object.values(STATUS_LABELS);
    const choice = prompt(`Durum Değiştir:\n${statuses.map((s,i) => `${i+1}. ${labels[i]}`).join('\n')}\n\nNumara girin (veya E: düzenle):`);
    
    if (choice && choice.toLowerCase() === 'e') {
        editOperation(opId);
    } else {
        const idx = parseInt(choice) - 1;
        if (idx >= 0 && idx < statuses.length) {
            op.status = statuses[idx];
            apiRequest('/api/operations', {
                method: 'PUT',
                body: JSON.stringify({ id: opId, status: statuses[idx] })
            }).catch(e => console.warn('Status update failed:', e));
            renderCalendar();
            showToast(`Durum güncellendi: ${labels[idx]}`);
        }
    }
}

// ============ CRUD ============
function openOperationModal(presetDate) {
    document.getElementById('editOpId').value = '';
    document.getElementById('modalSubtitle').textContent = 'YENİ İKMAL';
    document.getElementById('modalTitle').textContent = 'Operasyon Ekle';
    document.getElementById('opSubmitText').textContent = 'Kaydet';
    document.getElementById('operationForm').reset();
    if (presetDate) document.getElementById('opDate').value = presetDate;
    openModal('operationModal');
    lucide.createIcons();
}

function editOperation(opId) {
    const op = operations.find(o => o.id === opId);
    if (!op) return;
    document.getElementById('editOpId').value = op.id;
    document.getElementById('modalSubtitle').textContent = 'İKMAL DÜZENLE';
    document.getElementById('modalTitle').textContent = 'Operasyon Güncelle';
    document.getElementById('opSubmitText').textContent = 'Güncelle';
    document.getElementById('opVesselName').value = op.vesselName || '';
    document.getElementById('opImoNumber').value = op.imoNumber || '';
    document.getElementById('opQuantity').value = op.quantity || '';
    document.getElementById('opUnit').value = op.unit || 'MT';
    document.getElementById('opLoadingPlace').value = op.loadingPlace || '';
    document.getElementById('opPort').value = op.port || '';
    document.getElementById('opDate').value = op.date || '';
    
    const vType = op.vesselType || 'ship';
    document.querySelector(`input[name="vesselType"][value="${vType}"]`).checked = true;
    
    openModal('operationModal');
    lucide.createIcons();
}

async function saveOperation(e) {
    e.preventDefault();
    const editId = document.getElementById('editOpId').value;
    const data = {
        vesselName: document.getElementById('opVesselName').value.trim(),
        vesselType: document.querySelector('input[name="vesselType"]:checked')?.value || 'ship',
        imoNumber: document.getElementById('opImoNumber').value.trim(),
        quantity: parseFloat(document.getElementById('opQuantity').value) || 0,
        unit: document.getElementById('opUnit').value,
        loadingPlace: document.getElementById('opLoadingPlace').value.trim(),
        port: document.getElementById('opPort').value.trim(),
        date: document.getElementById('opDate').value,
    };

    if (!data.vesselName || !data.port || !data.date || data.quantity <= 0) {
        showToast('Lütfen zorunlu alanları doldurun', 'error');
        return;
    }

    try {
        if (editId) {
            await apiRequest('/api/operations', { method: 'PUT', body: JSON.stringify({ id: editId, ...data }) });
            const idx = operations.findIndex(o => o.id === editId);
            if (idx >= 0) Object.assign(operations[idx], data);
            showToast('Operasyon güncellendi');
        } else {
            const id = 'op_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
            const newOp = { id, ...data, status: 'planned', driverName: '', driverPhone: '', agentNote: '' };
            await apiRequest('/api/operations', { method: 'POST', body: JSON.stringify(newOp) });
            operations.unshift(newOp);
            showToast('Yeni operasyon eklendi');
        }
        closeModal('operationModal');
        renderCalendar();
    } catch (err) {
        showToast('Kayıt hatası: ' + err.message, 'error');
    }
}

async function deleteOperation(opId) {
    if (!confirmAction('Bu ikmali silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/operations', { method: 'DELETE', body: JSON.stringify({ id: opId }) });
        operations = operations.filter(o => o.id !== opId);
        renderCalendar();
        showToast('Operasyon silindi');
    } catch (err) {
        showToast('Silme hatası: ' + err.message, 'error');
    }
}

function toISODate(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
