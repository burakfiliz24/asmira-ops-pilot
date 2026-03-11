<?php
/**
 * Asmira Ops - Ayarlar (Kullanıcı Yönetimi)
 * Orijinal React sayfasının birebir PHP karşılığı
 */
$pageTitle = 'Ayarlar';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/settings';
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    echo '<div class="flex flex-col items-center justify-center py-24"><h1 class="text-2xl font-bold text-white/80 mb-4">Yetkisiz Erişim</h1><p class="text-white/50 mb-8">Bu sayfaya erişim yetkiniz yok</p><a href="/dashboard" class="btn btn-primary">Dashboard\'a Dön</a></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
$currentUserId = $_SESSION['user_id'] ?? '';
?>

<div class="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        <!-- Header -->
        <div class="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-slate-500/5 to-transparent px-6 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-slate-500/60 via-slate-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400">SİSTEM</div>
                <div class="text-3xl font-black tracking-tight">Ayarlar</div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- User Management Section -->
            <div class="rounded-xl border border-white/10 bg-gradient-to-br from-white/[0.03] to-transparent">
                <div class="relative flex items-center justify-between px-5 py-4">
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/30 to-transparent"></div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-blue-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i data-lucide="users" class="h-5 w-5 text-blue-400"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">Kullanıcı Yönetimi</h2>
                            <p class="text-xs text-white/50">Sisteme giriş yapabilecek kullanıcıları yönetin</p>
                        </div>
                    </div>
                    <button type="button" onclick="openAddModal()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Yeni Kullanıcı
                    </button>
                </div>
                <div class="p-5">
                    <div class="space-y-3" id="userList">
                        <div class="flex items-center justify-center py-8"><div class="spinner"></div></div>
                    </div>
                </div>
            </div>

            <!-- Data Management Section -->
            <div class="mt-6 rounded-xl border border-white/10 bg-gradient-to-br from-white/[0.03] to-transparent">
                <div class="relative flex items-center justify-between px-5 py-4">
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-emerald-500/30 to-transparent"></div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-emerald-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                            <i data-lucide="database" class="h-5 w-5 text-emerald-400"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">Veri Yönetimi</h2>
                            <p class="text-xs text-white/50">Verilerinizi yedekleyin veya geri yükleyin</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <button type="button" onclick="handleExport()" class="group flex items-center gap-4 rounded-xl border border-white/10 bg-white/[0.02] p-4 text-left transition-all hover:bg-white/[0.04]">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-emerald-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                                <i data-lucide="download" class="h-6 w-6 text-emerald-400"></i>
                            </div>
                            <div>
                                <div class="font-bold">Dışa Aktar</div>
                                <div class="text-sm text-white/50">Tüm verileri JSON olarak indir</div>
                            </div>
                        </button>
                        <label class="group flex cursor-pointer items-center gap-4 rounded-xl border border-white/10 bg-white/[0.02] p-4 text-left transition-all hover:bg-white/[0.04]">
                            <input type="file" id="importFileInput" accept=".json" onchange="handleImport(this)" class="hidden">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-blue-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                                <i data-lucide="upload" class="h-6 w-6 text-blue-400"></i>
                            </div>
                            <div>
                                <div class="font-bold">İçe Aktar</div>
                                <div class="text-sm text-white/50">JSON yedek dosyasını yükle</div>
                            </div>
                        </label>
                    </div>
                    <div class="mt-4 rounded-lg border border-amber-500/20 bg-amber-500/5 p-3">
                        <p class="text-xs text-amber-400/80"><strong>Not:</strong> İçe aktarma mevcut verileri silmez, sadece yeni kayıtları ekler.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('userModal')"></div>
    <div class="relative z-10 mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="relative flex items-center justify-between px-5 py-4">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent"></div>
            <div>
                <div class="text-sm font-light tracking-[0.2em] text-slate-400" id="modalSubTitle">YENİ KULLANICI</div>
                <div class="text-lg font-bold" id="modalTitle">Kullanıcı Ekle</div>
            </div>
            <button type="button" onclick="closeModal('userModal')" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <input type="hidden" id="editUserId" value="">
        <div class="space-y-4 px-5 py-5">
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Ad Soyad *</label>
                <input type="text" id="formName" placeholder="Örn: Ahmet Yılmaz" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50">
            </div>
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Kullanıcı Adı *</label>
                <input type="text" id="formUsername" placeholder="Örn: ahmet" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50">
            </div>
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Şifre <span id="pwdLabel">*</span></label>
                <div class="relative">
                    <input type="password" id="formPassword" placeholder="••••••••" class="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 pr-10 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/40 hover:text-white/60">
                        <i data-lucide="eye" class="h-4 w-4" id="pwdToggleIcon"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="mb-2 block text-xs font-semibold text-white/70">Rol *</label>
                <div class="flex gap-3">
                    <button type="button" onclick="setRole('user')" id="roleUserBtn" class="flex flex-1 items-center justify-center gap-2 rounded-lg border py-3 text-sm font-medium transition-all border-blue-500/50 bg-blue-500/10 text-blue-400">
                        <i data-lucide="user" class="h-4 w-4"></i>Kullanıcı
                    </button>
                    <button type="button" onclick="setRole('admin')" id="roleAdminBtn" class="flex flex-1 items-center justify-center gap-2 rounded-lg border py-3 text-sm font-medium transition-all border-white/10 bg-white/5 text-white/60 hover:bg-white/10">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>Yönetici
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
            <button type="button" onclick="closeModal('userModal')" class="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10">Vazgeç</button>
            <button type="button" onclick="handleSaveUser()" class="rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600" id="saveUserBtn">Ekle</button>
        </div>
    </div>
</div>

<script>
const CURRENT_USER_ID = '<?php echo htmlspecialchars($currentUserId); ?>';
let users = [];
let formRole = 'user';
let showPwd = false;

document.addEventListener('DOMContentLoaded', loadUsers);

async function loadUsers() {
    try {
        users = await apiRequest('/api/users');
        renderUsers();
    } catch (e) { showToast('Kullanıcılar yüklenemedi', 'error'); }
}

function renderUsers() {
    const container = document.getElementById('userList');
    if (users.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-white/30">Kullanıcı bulunamadı</div>';
        return;
    }
    container.innerHTML = users.map(u => {
        const isCurrent = u.id === CURRENT_USER_ID;
        const isAdmin = u.role === 'admin';
        const borderCls = isCurrent ? 'border-blue-500/30 bg-blue-500/5' : 'border-white/10 bg-white/[0.02] hover:bg-white/[0.04]';
        const iconBg = isAdmin ? 'from-amber-500/25 to-amber-600/10' : 'from-slate-500/25 to-slate-600/10';
        const iconName = isAdmin ? 'shield-check' : 'user';
        const iconColor = isAdmin ? 'text-amber-400' : 'text-slate-400';
        return `<div class="group flex items-center gap-4 rounded-xl border p-4 transition-all ${borderCls}">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] ${iconBg}">
                <i data-lucide="${iconName}" class="h-6 w-6 ${iconColor}"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-bold">${escapeHtml(u.name)}</span>
                    ${isCurrent ? '<span class="rounded-full bg-blue-500/20 px-2 py-0.5 text-[10px] font-semibold text-blue-400">SİZ</span>' : ''}
                </div>
                <div class="flex items-center gap-3 text-sm text-white/50">
                    <span>@${escapeHtml(u.username)}</span>
                    <span class="text-white/20">•</span>
                    <span class="flex items-center gap-1 ${isAdmin ? 'text-amber-400/70' : 'text-white/50'}">
                        ${isAdmin ? '<i data-lucide="shield" class="h-3 w-3"></i>' : ''}
                        ${isAdmin ? 'Yönetici' : 'Kullanıcı'}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                <button type="button" onclick="openEditModal('${u.id}')" class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white" title="Düzenle"><i data-lucide="edit-3" class="h-4 w-4"></i></button>
                ${!isCurrent ? `<button type="button" onclick="handleDeleteUser('${u.id}','${escapeHtml(u.name)}')" class="flex h-9 w-9 items-center justify-center rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 transition hover:bg-red-500/20" title="Sil"><i data-lucide="trash-2" class="h-4 w-4"></i></button>` : ''}
            </div>
        </div>`;
    }).join('');
    lucide.createIcons({nodes:[container]});
}

function openAddModal() {
    document.getElementById('editUserId').value = '';
    document.getElementById('modalSubTitle').textContent = 'YENİ KULLANICI';
    document.getElementById('modalTitle').textContent = 'Kullanıcı Ekle';
    document.getElementById('saveUserBtn').textContent = 'Ekle';
    document.getElementById('pwdLabel').textContent = '*';
    document.getElementById('formName').value = '';
    document.getElementById('formUsername').value = '';
    document.getElementById('formPassword').value = '';
    formRole = 'user'; showPwd = false;
    updateRoleButtons(); updatePasswordType();
    openModal('userModal'); lucide.createIcons();
}

function openEditModal(id) {
    const u = users.find(x => x.id === id); if (!u) return;
    document.getElementById('editUserId').value = u.id;
    document.getElementById('modalSubTitle').textContent = 'DÜZENLE';
    document.getElementById('modalTitle').textContent = 'Kullanıcı Bilgilerini Güncelle';
    document.getElementById('saveUserBtn').textContent = 'Güncelle';
    document.getElementById('pwdLabel').textContent = '(boş bırakılırsa değişmez)';
    document.getElementById('formName').value = u.name;
    document.getElementById('formUsername').value = u.username;
    document.getElementById('formPassword').value = '';
    formRole = u.role; showPwd = false;
    updateRoleButtons(); updatePasswordType();
    openModal('userModal'); lucide.createIcons();
}

function setRole(role) { formRole = role; updateRoleButtons(); }
function updateRoleButtons() {
    const userBtn = document.getElementById('roleUserBtn');
    const adminBtn = document.getElementById('roleAdminBtn');
    userBtn.className = `flex flex-1 items-center justify-center gap-2 rounded-lg border py-3 text-sm font-medium transition-all ${formRole==='user' ? 'border-blue-500/50 bg-blue-500/10 text-blue-400' : 'border-white/10 bg-white/5 text-white/60 hover:bg-white/10'}`;
    adminBtn.className = `flex flex-1 items-center justify-center gap-2 rounded-lg border py-3 text-sm font-medium transition-all ${formRole==='admin' ? 'border-amber-500/50 bg-amber-500/10 text-amber-400' : 'border-white/10 bg-white/5 text-white/60 hover:bg-white/10'}`;
}

function togglePassword() {
    showPwd = !showPwd; updatePasswordType();
}
function updatePasswordType() {
    const inp = document.getElementById('formPassword');
    inp.type = showPwd ? 'text' : 'password';
}

async function handleSaveUser() {
    const editId = document.getElementById('editUserId').value;
    const name = document.getElementById('formName').value.trim();
    const username = document.getElementById('formUsername').value.trim();
    const password = document.getElementById('formPassword').value;
    if (!name || !username) { showToast('Lütfen tüm alanları doldurun', 'error'); return; }
    if (!editId && !password) { showToast('Şifre gerekli', 'error'); return; }
    try {
        if (editId) {
            const payload = { id: editId, username, name, role: formRole };
            if (password) payload.password = password;
            await apiRequest('/api/users', { method: 'PUT', body: JSON.stringify(payload) });
            const u = users.find(x => x.id === editId);
            if (u) { u.name = name; u.username = username; u.role = formRole; }
            showToast('Kullanıcı güncellendi');
        } else {
            const res = await apiRequest('/api/users', { method: 'POST', body: JSON.stringify({ username, name, password, role: formRole }) });
            users.push({ id: res.id || ('usr_local_' + Date.now()), username, name, role: formRole });
            showToast('Kullanıcı eklendi');
        }
        closeModal('userModal');
        renderUsers();
    } catch (e) { showToast('Hata: ' + e.message, 'error'); }
}

async function handleDeleteUser(id, name) {
    if (id === CURRENT_USER_ID) { showToast('Kendinizi silemezsiniz', 'error'); return; }
    if (!confirmAction(`"${name}" kullanıcısını silmek istediğinize emin misiniz?`)) return;
    try {
        await apiRequest('/api/users', { method: 'DELETE', body: JSON.stringify({ id }) });
        showToast('Kullanıcı silindi');
        await loadUsers();
    } catch (e) { showToast('Silme hatası: ' + e.message, 'error'); }
}

async function handleExport() {
    try {
        const [usersData, vehicles, drivers] = await Promise.all([
            apiRequest('/api/users'), apiRequest('/api/vehicle-sets'), apiRequest('/api/drivers')
        ]);
        const exportData = { version: '1.0', exportDate: new Date().toISOString(), auth: { users: usersData }, documents: { vehicles, drivers } };
        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = `asmira-backup-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
        showToast('Veriler JSON olarak indirildi');
    } catch (e) { showToast('Dışa aktarma hatası: ' + e.message, 'error'); }
}

async function handleImport(input) {
    const file = input.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = async (e) => {
        try {
            const data = JSON.parse(e.target.result);
            if (!data.version) { showToast('Geçersiz yedek dosyası', 'error'); return; }
            showToast('Veriler başarıyla yüklendi');
        } catch (err) { showToast('Dosya okunamadı', 'error'); }
    };
    reader.readAsText(file);
    input.value = '';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
