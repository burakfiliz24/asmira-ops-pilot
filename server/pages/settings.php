<?php
/**
 * Asmira Ops - Ayarlar (Kullanıcı Yönetimi)
 */
$pageTitle = 'Ayarlar';
$GLOBALS['currentPage'] = $GLOBALS['currentPage'] ?? '/settings';
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    echo '<div class="flex flex-col items-center justify-center py-24"><h1 class="text-2xl font-bold text-white/80 mb-4">Yetkisiz Erişim</h1><p class="text-white/50 mb-8">Bu sayfaya erişim yetkiniz yok</p><a href="/dashboard" class="btn btn-primary">Dashboard\'a Dön</a></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
?>

<div class="px-2 py-4 lg:px-0">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Ayarlar</h1>
            <p class="text-sm text-white/50 mt-1">Kullanıcı yönetimi ve sistem ayarları</p>
        </div>
        <button onclick="openModal('addUserModal')" class="btn btn-primary">
            <i data-lucide="user-plus" class="h-4 w-4"></i> Yeni Kullanıcı
        </button>
    </div>

    <!-- User Table -->
    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-white/5">
            <h3 class="font-semibold text-white">Kullanıcılar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Kullanıcı</th><th>Ad Soyad</th><th>Rol</th><th class="text-right">İşlem</th></tr></thead>
                <tbody id="userTableBody">
                    <tr><td colspan="4" class="text-center py-8"><div class="spinner mx-auto"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="hidden modal-overlay">
    <div class="modal-backdrop" onclick="closeModal('addUserModal')"></div>
    <div class="relative mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div class="text-lg font-semibold" id="userModalTitle">Yeni Kullanıcı</div>
            <button onclick="closeModal('addUserModal')" class="h-9 w-9 flex items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"><i data-lucide="x" class="h-4 w-4"></i></button>
        </div>
        <form onsubmit="saveUser(event)" class="space-y-3 px-5 py-4">
            <input type="hidden" id="editUserId" value="">
            <div><label class="block text-xs font-medium text-white/50 mb-1">Kullanıcı Adı *</label><input type="text" id="newUsername" class="input-field" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">Ad Soyad *</label><input type="text" id="newName" class="input-field" required></div>
            <div><label class="block text-xs font-medium text-white/50 mb-1">Şifre <span id="pwdNote">*</span></label><input type="password" id="newPassword" class="input-field" placeholder="••••••••"></div>
            <div>
                <label class="block text-xs font-medium text-white/50 mb-1">Rol</label>
                <select id="newRole" class="input-field">
                    <option value="user">Kullanıcı</option>
                    <option value="admin">Yönetici</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addUserModal')" class="btn btn-ghost flex-1">İptal</button>
                <button type="submit" class="btn btn-primary flex-1"><i data-lucide="check" class="h-4 w-4"></i> <span id="userSubmitText">Kaydet</span></button>
            </div>
        </form>
    </div>
</div>

<script>
let users = [];

document.addEventListener('DOMContentLoaded', async () => {
    try {
        users = await apiRequest('/api/users');
        renderUsers();
    } catch (e) { showToast('Kullanıcılar yüklenemedi', 'error'); }
});

function renderUsers() {
    const tbody = document.getElementById('userTableBody');
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-white/30">Kullanıcı bulunamadı</td></tr>';
        return;
    }
    tbody.innerHTML = users.map(u => `<tr>
        <td class="font-medium">${escapeHtml(u.username)}</td>
        <td>${escapeHtml(u.name)}</td>
        <td>${u.role === 'admin' ? '<span class="badge badge-blue">Yönetici</span>' : '<span class="badge badge-gray">Kullanıcı</span>'}</td>
        <td class="text-right">
            <div class="flex items-center justify-end gap-1">
                <button onclick="editUser('${u.id}')" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-white/40 hover:text-blue-400 hover:bg-blue-500/10 transition"><i data-lucide="pencil" class="h-3.5 w-3.5"></i></button>
                <button onclick="deleteUser('${u.id}')" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-white/40 hover:text-red-400 hover:bg-red-500/10 transition"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
            </div>
        </td>
    </tr>`).join('');
    lucide.createIcons();
}

function editUser(id) {
    const u = users.find(x => x.id === id);
    if (!u) return;
    document.getElementById('editUserId').value = u.id;
    document.getElementById('userModalTitle').textContent = 'Kullanıcı Düzenle';
    document.getElementById('userSubmitText').textContent = 'Güncelle';
    document.getElementById('pwdNote').textContent = '(boş bırakılırsa değişmez)';
    document.getElementById('newUsername').value = u.username;
    document.getElementById('newName').value = u.name;
    document.getElementById('newPassword').value = '';
    document.getElementById('newPassword').required = false;
    document.getElementById('newRole').value = u.role;
    openModal('addUserModal');
    lucide.createIcons();
}

async function saveUser(e) {
    e.preventDefault();
    const editId = document.getElementById('editUserId').value;
    const data = {
        username: document.getElementById('newUsername').value.trim(),
        name: document.getElementById('newName').value.trim(),
        role: document.getElementById('newRole').value,
    };
    const pwd = document.getElementById('newPassword').value;
    
    try {
        if (editId) {
            const payload = { id: editId, ...data };
            if (pwd) payload.password = pwd;
            await apiRequest('/api/users', { method: 'PUT', body: JSON.stringify(payload) });
            showToast('Kullanıcı güncellendi');
        } else {
            if (!pwd) { showToast('Şifre gerekli', 'error'); return; }
            await apiRequest('/api/users', { method: 'POST', body: JSON.stringify({ ...data, password: pwd }) });
            showToast('Kullanıcı eklendi');
        }
        closeModal('addUserModal');
        resetUserForm();
        users = await apiRequest('/api/users');
        renderUsers();
    } catch (err) { showToast('Hata: ' + err.message, 'error'); }
}

async function deleteUser(id) {
    if (!confirmAction('Bu kullanıcıyı silmek istediğinize emin misiniz?')) return;
    try {
        await apiRequest('/api/users', { method: 'DELETE', body: JSON.stringify({ id }) });
        users = users.filter(u => u.id !== id);
        renderUsers(); showToast('Kullanıcı silindi');
    } catch (err) { showToast('Silme hatası: ' + err.message, 'error'); }
}

function resetUserForm() {
    document.getElementById('editUserId').value = '';
    document.getElementById('userModalTitle').textContent = 'Yeni Kullanıcı';
    document.getElementById('userSubmitText').textContent = 'Kaydet';
    document.getElementById('pwdNote').textContent = '*';
    document.getElementById('newPassword').required = true;
    document.getElementById('newUsername').value = '';
    document.getElementById('newName').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('newRole').value = 'user';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
