"use client";

import { useState, useRef } from "react";
import { Plus, Edit3, Trash2, X, User, Shield, ShieldCheck, Eye, EyeOff, Users, Download, Upload, Database } from "lucide-react";
import { cn } from "@/lib/utils/cn";
import { useAuthStore, type User as UserType } from "@/store/authStore";
import { useDocumentStore } from "@/store/documentStore";
import { toast } from "@/store/toastStore";

export default function SettingsPage() {
  const { users, user: currentUser, addUser, updateUser, deleteUser } = useAuthStore();
  const documentStore = useDocumentStore();
  const fileInputRef = useRef<HTMLInputElement>(null);
  
  const [showModal, setShowModal] = useState(false);
  const [editingUser, setEditingUser] = useState<UserType | null>(null);
  const [showPassword, setShowPassword] = useState(false);

  const handleExport = () => {
    const exportData = {
      version: "1.0",
      exportDate: new Date().toISOString(),
      auth: {
        users: users,
      },
      documents: {
        vehicles: documentStore.vehicles,
        drivers: documentStore.drivers,
      },
    };

    const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `asmira-backup-${new Date().toISOString().split("T")[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    toast.success("Dışa Aktarma Başarılı", "Veriler JSON dosyası olarak indirildi.");
  };

  const handleImport = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const data = JSON.parse(e.target?.result as string);
        
        if (!data.version || !data.documents) {
          toast.error("Geçersiz Dosya", "Bu dosya geçerli bir yedek dosyası değil.");
          return;
        }

        // Import documents
        if (data.documents?.vehicles && Array.isArray(data.documents.vehicles)) {
          data.documents.vehicles.forEach((vehicle: { id?: string; vehiclePlate?: string; trailerPlate?: string; category?: string }) => {
            if (vehicle.id && vehicle.vehiclePlate && vehicle.trailerPlate && vehicle.category) {
              const existing = documentStore.vehicles.find((v) => v.id === vehicle.id);
              if (!existing) {
                documentStore.addVehicle({
                  vehiclePlate: vehicle.vehiclePlate,
                  trailerPlate: vehicle.trailerPlate,
                  category: vehicle.category as "asmira" | "supplier",
                });
              }
            }
          });
        }

        if (data.documents?.drivers && Array.isArray(data.documents.drivers)) {
          data.documents.drivers.forEach((driver: { id?: string; name?: string; tcNo?: string; phone?: string }) => {
            if (driver.id && driver.name && driver.tcNo && driver.phone) {
              const existing = documentStore.drivers.find((d) => d.id === driver.id);
              if (!existing) {
                documentStore.addDriver({
                  name: driver.name,
                  tcNo: driver.tcNo,
                  phone: driver.phone,
                });
              }
            }
          });
        }

        toast.success("İçe Aktarma Başarılı", "Veriler başarıyla yüklendi.");
      } catch (_error) {
        console.error(_error);
        toast.error("Hata", "Dosya okunamadı veya geçersiz format.");
      }
    };
    reader.readAsText(file);
    
    // Reset input
    if (fileInputRef.current) {
      fileInputRef.current.value = "";
    }
  };
  
  const [formData, setFormData] = useState({
    username: "",
    password: "",
    name: "",
    role: "user" as "admin" | "user",
  });

  const openAddModal = () => {
    setEditingUser(null);
    setFormData({ username: "", password: "", name: "", role: "user" });
    setShowPassword(false);
    setShowModal(true);
  };

  const openEditModal = (user: UserType) => {
    setEditingUser(user);
    setFormData({
      username: user.username,
      password: user.password,
      name: user.name,
      role: user.role,
    });
    setShowPassword(false);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingUser(null);
  };

  const handleSave = () => {
    if (!formData.username.trim() || !formData.password.trim() || !formData.name.trim()) {
      alert("Lütfen tüm alanları doldurun.");
      return;
    }

    // Check for duplicate username
    const existingUser = users.find(
      (u) => u.username.toLowerCase() === formData.username.toLowerCase() && u.id !== editingUser?.id
    );
    if (existingUser) {
      alert("Bu kullanıcı adı zaten kullanılıyor.");
      return;
    }

    if (editingUser) {
      updateUser(editingUser.id, formData);
    } else {
      addUser(formData);
    }
    closeModal();
  };

  const handleDelete = (user: UserType) => {
    if (user.id === currentUser?.id) {
      alert("Kendinizi silemezsiniz.");
      return;
    }
    
    if (confirm(`"${user.name}" kullanıcısını silmek istediğinize emin misiniz?`)) {
      const success = deleteUser(user.id);
      if (!success) {
        alert("Son yönetici kullanıcı silinemez.");
      }
    }
  };

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-slate-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-slate-500/60 via-slate-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              SİSTEM
            </div>
            <div className="text-3xl font-black tracking-tight">Ayarlar</div>
          </div>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6">
          {/* User Management Section */}
          <div className="rounded-xl border border-white/10 bg-gradient-to-br from-white/[0.03] to-transparent">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/30 to-transparent" />
              <div className="flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-blue-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                  <Users className="h-5 w-5 text-blue-400" />
                </div>
                <div>
                  <h2 className="text-lg font-bold">Kullanıcı Yönetimi</h2>
                  <p className="text-xs text-white/50">
                    {currentUser?.role === "admin" 
                      ? "Sisteme giriş yapabilecek kullanıcıları yönetin" 
                      : "Sistemdeki kullanıcıları görüntüleyin"}
                  </p>
                </div>
              </div>
              {currentUser?.role === "admin" && (
                <button
                  type="button"
                  onClick={openAddModal}
                  className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
                >
                  <Plus className="h-4 w-4" />
                  Yeni Kullanıcı
                </button>
              )}
            </div>

            <div className="p-5">
              <div className="space-y-3">
                {users.map((user) => {
                  const isCurrentUser = user.id === currentUser?.id;
                  const isAdmin = user.role === "admin";
                  
                  return (
                    <div
                      key={user.id}
                      className={cn(
                        "group flex items-center gap-4 rounded-xl border p-4 transition-all",
                        isCurrentUser
                          ? "border-blue-500/30 bg-blue-500/5"
                          : "border-white/10 bg-white/[0.02] hover:bg-white/[0.04]"
                      )}
                    >
                      <div className={cn(
                        "flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]",
                        isAdmin
                          ? "from-amber-500/25 to-amber-600/10"
                          : "from-slate-500/25 to-slate-600/10"
                      )}>
                        {isAdmin ? (
                          <ShieldCheck className="h-6 w-6 text-amber-400" />
                        ) : (
                          <User className="h-6 w-6 text-slate-400" />
                        )}
                      </div>
                      
                      <div className="min-w-0 flex-1">
                        <div className="flex items-center gap-2">
                          <span className="font-bold">{user.name}</span>
                          {isCurrentUser && (
                            <span className="rounded-full bg-blue-500/20 px-2 py-0.5 text-[10px] font-semibold text-blue-400">
                              SİZ
                            </span>
                          )}
                        </div>
                        <div className="flex items-center gap-3 text-sm text-white/50">
                          <span>@{user.username}</span>
                          <span className="text-white/20">•</span>
                          <span className={cn(
                            "flex items-center gap-1",
                            isAdmin ? "text-amber-400/70" : "text-white/50"
                          )}>
                            {isAdmin ? <Shield className="h-3 w-3" /> : null}
                            {isAdmin ? "Yönetici" : "Kullanıcı"}
                          </span>
                        </div>
                      </div>

                      {currentUser?.role === "admin" && (
                        <div className="flex items-center gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                          <button
                            type="button"
                            onClick={() => openEditModal(user)}
                            className="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                            title="Düzenle"
                          >
                            <Edit3 className="h-4 w-4" />
                          </button>
                          {!isCurrentUser && (
                            <button
                              type="button"
                              onClick={() => handleDelete(user)}
                              className="flex h-9 w-9 items-center justify-center rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 transition hover:bg-red-500/20"
                              title="Sil"
                            >
                              <Trash2 className="h-4 w-4" />
                            </button>
                          )}
                        </div>
                      )}
                    </div>
                  );
                })}
              </div>
            </div>
          </div>

          {/* Data Management Section */}
          <div className="mt-6 rounded-xl border border-white/10 bg-gradient-to-br from-white/[0.03] to-transparent">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-emerald-500/30 to-transparent" />
              <div className="flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-emerald-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                  <Database className="h-5 w-5 text-emerald-400" />
                </div>
                <div>
                  <h2 className="text-lg font-bold">Veri Yönetimi</h2>
                  <p className="text-xs text-white/50">Verilerinizi yedekleyin veya geri yükleyin</p>
                </div>
              </div>
            </div>

            <div className="p-5">
              <div className="grid gap-4 sm:grid-cols-2">
                <button
                  type="button"
                  onClick={handleExport}
                  className="group flex items-center gap-4 rounded-xl border border-white/10 bg-white/[0.02] p-4 text-left transition-all hover:bg-white/[0.04]"
                >
                  <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-emerald-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                    <Download className="h-6 w-6 text-emerald-400" />
                  </div>
                  <div>
                    <div className="font-bold">Dışa Aktar</div>
                    <div className="text-sm text-white/50">Tüm verileri JSON olarak indir</div>
                  </div>
                </button>

                <label className="group flex cursor-pointer items-center gap-4 rounded-xl border border-white/10 bg-white/[0.02] p-4 text-left transition-all hover:bg-white/[0.04]">
                  <input
                    ref={fileInputRef}
                    type="file"
                    accept=".json"
                    onChange={handleImport}
                    className="hidden"
                  />
                  <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-blue-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                    <Upload className="h-6 w-6 text-blue-400" />
                  </div>
                  <div>
                    <div className="font-bold">İçe Aktar</div>
                    <div className="text-sm text-white/50">JSON yedek dosyasını yükle</div>
                  </div>
                </label>
              </div>

              <div className="mt-4 rounded-lg border border-amber-500/20 bg-amber-500/5 p-3">
                <p className="text-xs text-amber-400/80">
                  <strong>Not:</strong> İçe aktarma mevcut verileri silmez, sadece yeni kayıtları ekler.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Add/Edit User Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closeModal}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">
                  {editingUser ? "DÜZENLE" : "YENİ KULLANICI"}
                </div>
                <div className="text-lg font-bold">
                  {editingUser ? "Kullanıcı Bilgilerini Güncelle" : "Kullanıcı Ekle"}
                </div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={closeModal}
                aria-label="Kapat"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="space-y-4 px-5 py-5">
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Ad Soyad *</label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  placeholder="Örn: Ahmet Yılmaz"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50"
                />
              </div>
              
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Kullanıcı Adı *</label>
                <input
                  type="text"
                  value={formData.username}
                  onChange={(e) => setFormData({ ...formData, username: e.target.value })}
                  placeholder="Örn: ahmet"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50"
                />
              </div>
              
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Şifre *</label>
                <div className="relative">
                  <input
                    type={showPassword ? "text" : "password"}
                    value={formData.password}
                    onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                    placeholder="••••••••"
                    className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 pr-10 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute inset-y-0 right-0 flex items-center pr-3 text-white/40 hover:text-white/60"
                  >
                    {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                  </button>
                </div>
              </div>
              
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Rol *</label>
                <div className="flex gap-3">
                  <button
                    type="button"
                    onClick={() => setFormData({ ...formData, role: "user" })}
                    className={cn(
                      "flex flex-1 items-center justify-center gap-2 rounded-lg border py-3 text-sm font-medium transition-all",
                      formData.role === "user"
                        ? "border-blue-500/50 bg-blue-500/10 text-blue-400"
                        : "border-white/10 bg-white/5 text-white/60 hover:bg-white/10"
                    )}
                  >
                    <User className="h-4 w-4" />
                    Kullanıcı
                  </button>
                  <button
                    type="button"
                    onClick={() => setFormData({ ...formData, role: "admin" })}
                    className={cn(
                      "flex flex-1 items-center justify-center gap-2 rounded-lg border py-3 text-sm font-medium transition-all",
                      formData.role === "admin"
                        ? "border-amber-500/50 bg-amber-500/10 text-amber-400"
                        : "border-white/10 bg-white/5 text-white/60 hover:bg-white/10"
                    )}
                  >
                    <ShieldCheck className="h-4 w-4" />
                    Yönetici
                  </button>
                </div>
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
              <button
                type="button"
                onClick={closeModal}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleSave}
                className="rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
              >
                {editingUser ? "Güncelle" : "Ekle"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
