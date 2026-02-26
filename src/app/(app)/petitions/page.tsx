"use client";

import { useState, useRef, useEffect } from "react";
import { 
  FileCheck, 
  Shield, 
  LogIn, 
  ClipboardList, 
  X, 
  Printer,
  ChevronRight,
  Plus,
  FileText,
  Truck,
  Ship,
  Anchor,
  Trash2,
  Edit3,
  File
} from "lucide-react";
import { useRouter } from "next/navigation";
import { petitionTemplates } from "@/features/petitions/data/templates";
import type { PetitionTemplate } from "@/features/petitions/domain/types";
import { usePetitionStore, type CustomCategory } from "@/store/petitionStore";
import { ContextMenu } from "@/components/ui/ContextMenu";

// Icon map for dynamic template icons
const _iconMap: Record<string, React.ComponentType<{ className?: string }>> = {
  FileCheck,
  Shield,
  LogIn,
  ClipboardList,
};
void _iconMap;

const iconComponents = {
  FileText,
  FileCheck,
  LogIn,
  Truck,
  Ship,
  Anchor,
};

export default function PetitionsPage() {
  const [selectedTemplate, setSelectedTemplate] = useState<PetitionTemplate | null>(null);
  const [formData, setFormData] = useState<Record<string, string>>({});
  const _printRef = useRef<HTMLDivElement>(null);
  void _printRef;

  // Kategori ekleme modalı
  const [showAddCategoryModal, setShowAddCategoryModal] = useState(false);
  const [newCategoryTitle, setNewCategoryTitle] = useState("");
  const [newCategoryDescription, setNewCategoryDescription] = useState("");
  const [newCategoryIcon, setNewCategoryIcon] = useState<CustomCategory["icon"]>("FileText");

  // Store
  const { customCategories, addCategory, updateCategory, deleteCategory } = usePetitionStore();
  const router = useRouter();
  
  // Kategori düzenleme modalı
  const [editingCategory, setEditingCategory] = useState<CustomCategory | null>(null);
  const [editCategoryTitle, setEditCategoryTitle] = useState("");
  const [editCategoryDescription, setEditCategoryDescription] = useState("");

  // Hydration
  const [mounted, setMounted] = useState(false);
  useEffect(() => {
    setMounted(true);
  }, []);

  const taahhutTemplates = petitionTemplates.filter((t) => t.category === "taahhutname");
  const limanTemplates = petitionTemplates.filter((t) => t.category === "liman");

  function handleAddCategory() {
    if (!newCategoryTitle.trim()) {
      alert("Lütfen kategori başlığı girin");
      return;
    }
    addCategory({
      title: newCategoryTitle,
      description: newCategoryDescription || "Belge şablonları",
      icon: newCategoryIcon,
    });
    setNewCategoryTitle("");
    setNewCategoryDescription("");
    setNewCategoryIcon("FileText");
    setShowAddCategoryModal(false);
  }

  function openEditCategoryModal(category: CustomCategory) {
    setEditingCategory(category);
    setEditCategoryTitle(category.title);
    setEditCategoryDescription(category.description);
  }

  function handleUpdateCategory() {
    if (!editingCategory || !editCategoryTitle.trim()) {
      alert("Lütfen kategori başlığı girin");
      return;
    }
    updateCategory(editingCategory.id, {
      title: editCategoryTitle,
      description: editCategoryDescription || "Belge şablonları",
    });
    setEditingCategory(null);
    setEditCategoryTitle("");
    setEditCategoryDescription("");
  }

  function closeEditCategoryModal() {
    setEditingCategory(null);
    setEditCategoryTitle("");
    setEditCategoryDescription("");
  }

  function _openTemplate(template: PetitionTemplate) {
    setSelectedTemplate(template);
    const initial: Record<string, string> = {};
    template.fields.forEach((f) => {
      initial[f.id] = "";
    });
    // Set today's date for date fields
    template.fields.forEach((f) => {
      if (f.type === "date") {
        initial[f.id] = new Date().toISOString().split("T")[0];
      }
    });
    setFormData(initial);
  }

  function closeModal() {
    setSelectedTemplate(null);
    setFormData({});
  }

  function handleFieldChange(fieldId: string, value: string) {
    setFormData((prev) => ({ ...prev, [fieldId]: value }));
  }

  function handleGenerate() {
    if (!selectedTemplate) return;

    // Check required fields
    const missingFields = selectedTemplate.fields
      .filter((f) => f.required && !formData[f.id]?.trim())
      .map((f) => f.label);

    if (missingFields.length > 0) {
      alert(`Lütfen zorunlu alanları doldurun:\n${missingFields.join("\n")}`);
      return;
    }

    // Trigger print
    setTimeout(() => {
      window.print();
    }, 100);

    closeModal();
  }

  function _formatDate(dateStr: string) {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    return date.toLocaleDateString("tr-TR", {
      day: "2-digit",
      month: "long",
      year: "numeric",
    });
  }
  void _openTemplate;
  void _formatDate;

  const totalTemplates = petitionTemplates.length;

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
          {/* Neon separator line */}
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              DİLEKÇELER
            </div>
            <div className="text-3xl font-black tracking-tight">Belge Şablonları</div>
          </div>

          <button
            type="button"
            onClick={() => setShowAddCategoryModal(true)}
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-4 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
          >
            <Plus className="h-4 w-4" />
            Kategori Ekle
          </button>
        </div>

        {/* Stats Bar */}
        <div className="relative flex flex-none items-center gap-3 px-6 py-2.5">
          {/* Neon separator line */}
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/40 via-cyan-400/20 to-transparent" />
          
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <div className="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]" />
            <span className="text-xs font-medium text-white/70">Toplam</span>
            <span className="text-sm font-bold text-white">{totalTemplates}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <span className="text-xs font-medium text-white/70">Taahhütname</span>
            <span className="text-sm font-bold text-white">{taahhutTemplates.length}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <span className="text-xs font-medium text-white/70">Gümrük</span>
            <span className="text-sm font-bold text-white">{limanTemplates.length}</span>
          </div>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6">
          <div className="space-y-8">
              {/* Taahhütname Section */}
              <section>
                <div className="mb-4 flex items-center gap-3">
                  <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/20">
                    <FileCheck className="h-4 w-4 text-blue-400" />
                  </div>
                  <h2 className="text-lg font-bold tracking-wide">Taahhütnameler</h2>
                </div>
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <ContextMenu
                    items={[
                      {
                        label: "Aç",
                        icon: <Edit3 className="h-4 w-4" />,
                        onClick: () => router.push("/petitions/taahhutnameler"),
                        danger: false,
                      },
                    ]}
                  >
                    <div
                      onClick={() => router.push("/petitions/taahhutnameler")}
                      className="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-blue-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(59,130,246,0.15)]"
                    >
                      <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400 transition group-hover:bg-blue-500/30">
                          <FileCheck className="h-6 w-6" />
                        </div>
                        <div>
                          <div className="text-[16px] font-semibold">Taşıma Taahhütnameleri</div>
                          <div className="text-sm text-white/50">Liman bazlı taahhütname şablonları</div>
                        </div>
                      </div>
                      <ChevronRight className="h-5 w-5 text-white/40 transition group-hover:text-white/70" />
                    </div>
                  </ContextMenu>
                </div>
              </section>

              {/* Gümrük Dilekçeleri Section */}
              <section>
                <div className="mb-4 flex items-center gap-3">
                  <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20">
                    <LogIn className="h-4 w-4 text-emerald-400" />
                  </div>
                  <h2 className="text-lg font-bold tracking-wide">Gümrük Dilekçeleri</h2>
                </div>
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <ContextMenu
                    items={[
                      {
                        label: "Aç",
                        icon: <Edit3 className="h-4 w-4" />,
                        onClick: () => router.push("/petitions/gumruk-dilekceleri"),
                        danger: false,
                      },
                    ]}
                  >
                    <div
                      onClick={() => router.push("/petitions/gumruk-dilekceleri")}
                      className="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-emerald-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(52,211,153,0.15)]"
                    >
                      <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 transition group-hover:bg-emerald-500/30">
                          <LogIn className="h-6 w-6" />
                        </div>
                        <div>
                          <div className="text-[16px] font-semibold">Gümrük Milli İkmal Dilekçeleri</div>
                          <div className="text-sm text-white/50">Gümrük müdürlüğü dilekçe şablonları</div>
                        </div>
                      </div>
                      <ChevronRight className="h-5 w-5 text-white/40 transition group-hover:text-white/70" />
                    </div>
                  </ContextMenu>
                </div>
              </section>

              {/* EK-1 Belgeleri Section */}
              <section>
                <div className="mb-4 flex items-center gap-3">
                  <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/20">
                    <File className="h-4 w-4 text-amber-400" />
                  </div>
                  <h2 className="text-lg font-bold tracking-wide">EK-1 Belgeleri</h2>
                </div>
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <ContextMenu
                    items={[
                      {
                        label: "Aç",
                        icon: <Edit3 className="h-4 w-4" />,
                        onClick: () => router.push("/petitions/ek-1-belgeleri"),
                        danger: false,
                      },
                    ]}
                  >
                    <div
                      onClick={() => router.push("/petitions/ek-1-belgeleri")}
                      className="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-amber-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(245,158,11,0.15)]"
                    >
                      <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-500/20 text-amber-400 transition group-hover:bg-amber-500/30">
                          <File className="h-6 w-6" />
                        </div>
                        <div>
                          <div className="text-[16px] font-semibold">EK-1 Belgeleri</div>
                          <div className="text-sm text-white/50">Word dosyası yükle ve indir</div>
                        </div>
                      </div>
                      <ChevronRight className="h-5 w-5 text-white/40 transition group-hover:text-white/70" />
                    </div>
                  </ContextMenu>
                </div>
              </section>

              {/* Özel Kategoriler */}
              {mounted && customCategories.map((category) => {
                const IconComponent = iconComponents[category.icon];
                return (
                  <section key={category.id}>
                    <div className="mb-4 flex items-center gap-3">
                      <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-500/20">
                        <IconComponent className="h-4 w-4 text-purple-400" />
                      </div>
                      <h2 className="text-lg font-bold tracking-wide">{category.title}</h2>
                    </div>
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                      <ContextMenu
                        items={[
                          {
                            label: "Düzenle",
                            icon: <Edit3 className="h-4 w-4" />,
                            onClick: () => openEditCategoryModal(category),
                            danger: false,
                          },
                          {
                            label: "Sil",
                            icon: <Trash2 className="h-4 w-4" />,
                            onClick: () => {
                              if (confirm(`"${category.title}" kategorisini silmek istediğinize emin misiniz?`)) {
                                deleteCategory(category.id);
                              }
                            },
                            danger: true,
                          },
                        ]}
                      >
                        <div
                          onClick={() => router.push(`/petitions/custom/${category.slug}`)}
                          className="group flex cursor-pointer items-center justify-between rounded-xl border border-white/15 bg-white/[0.04] p-5 shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-purple-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(168,85,247,0.15)]"
                        >
                          <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/20 text-purple-400 transition group-hover:bg-purple-500/30">
                              <IconComponent className="h-6 w-6" />
                            </div>
                            <div>
                              <div className="text-[16px] font-semibold">{category.title}</div>
                              <div className="text-sm text-white/50">{category.description}</div>
                            </div>
                          </div>
                          <ChevronRight className="h-5 w-5 text-white/40 transition group-hover:text-white/70" />
                        </div>
                      </ContextMenu>
                    </div>
                  </section>
                );
              })}
            </div>
        </div>
      </div>

      {/* Modal */}
      {selectedTemplate && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closeModal}
            aria-label="Kapat"
          />
          <div className="relative z-10 flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="flex items-center justify-between border-b border-white/10 px-5 py-4">
              <div>
                <div className="text-sm font-semibold tracking-wider text-white/70">
                  {selectedTemplate.category === "taahhutname" ? "TAAHHÜTNAME" : "LİMAN DİLEKÇESİ"}
                </div>
                <div className="text-lg font-semibold">{selectedTemplate.title}</div>
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

            <div className="flex-1 overflow-y-auto px-5 py-5">
              <div className="space-y-4">
                {selectedTemplate.fields.map((field) => (
                  <div key={field.id}>
                    <label className="mb-2 block text-xs font-semibold text-white/70">
                      {field.label}
                      {field.required && <span className="ml-1 text-red-400">*</span>}
                    </label>
                    {field.type === "textarea" ? (
                      <textarea
                        value={formData[field.id] || ""}
                        onChange={(e) => handleFieldChange(field.id, e.target.value)}
                        placeholder={field.placeholder}
                        rows={3}
                        className="w-full rounded-md border border-white/10 bg-white/5 px-3 py-2.5 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                      />
                    ) : field.type === "select" ? (
                      <select
                        value={formData[field.id] || ""}
                        onChange={(e) => handleFieldChange(field.id, e.target.value)}
                        className="h-11 w-full rounded-md border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-white/25 [&>option]:bg-[#0B1220] [&>option]:text-white"
                      >
                        <option value="" className="bg-[#0B1220] text-white/60">Seçiniz...</option>
                        {field.options?.map((opt) => (
                          <option key={opt} value={opt} className="bg-[#0B1220] text-white">
                            {opt}
                          </option>
                        ))}
                      </select>
                    ) : (
                      <input
                        type={field.type}
                        value={formData[field.id] || ""}
                        onChange={(e) => handleFieldChange(field.id, e.target.value)}
                        placeholder={field.placeholder}
                        className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                      />
                    )}
                  </div>
                ))}
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
                onClick={handleGenerate}
                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_0_0_1px_rgba(59,130,246,0.30),0_10px_20px_rgba(0,0,0,0.30)] transition hover:bg-blue-500"
              >
                <Printer className="h-4 w-4" />
                Oluştur ve Yazdır
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Kategori Ekleme Modalı */}
      {showAddCategoryModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <button
            type="button"
            className="absolute inset-0 bg-black/70 backdrop-blur-sm"
            onClick={() => setShowAddCategoryModal(false)}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="mb-6 flex items-center justify-between">
              <h3 className="text-xl font-semibold">Yeni Kategori Ekle</h3>
              <button
                type="button"
                onClick={() => setShowAddCategoryModal(false)}
                className="flex h-8 w-8 items-center justify-center rounded-lg text-white/50 hover:bg-white/10 hover:text-white"
              >
                <X className="h-4 w-4" />
              </button>
            </div>
            
            <div className="space-y-4">
              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">
                  Kategori Başlığı <span className="text-red-400">*</span>
                </label>
                <input
                  type="text"
                  value={newCategoryTitle}
                  onChange={(e) => setNewCategoryTitle(e.target.value)}
                  placeholder="Örn: Gümrük Transit Dilekçeleri"
                  className="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50"
                />
              </div>
              
              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">
                  Açıklama
                </label>
                <input
                  type="text"
                  value={newCategoryDescription}
                  onChange={(e) => setNewCategoryDescription(e.target.value)}
                  placeholder="Örn: Transit işlemleri için dilekçe şablonları"
                  className="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50"
                />
              </div>
              
              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">
                  İkon
                </label>
                <div className="grid grid-cols-6 gap-2">
                  {(Object.keys(iconComponents) as Array<CustomCategory["icon"]>).map((iconName) => {
                    const Icon = iconComponents[iconName];
                    return (
                      <button
                        key={iconName}
                        type="button"
                        onClick={() => setNewCategoryIcon(iconName)}
                        className={`flex h-10 w-10 items-center justify-center rounded-lg border transition ${
                          newCategoryIcon === iconName
                            ? "border-cyan-500 bg-cyan-500/20 text-cyan-400"
                            : "border-white/10 bg-white/5 text-white/50 hover:bg-white/10 hover:text-white"
                        }`}
                      >
                        <Icon className="h-5 w-5" />
                      </button>
                    );
                  })}
                </div>
              </div>
            </div>
            
            <div className="mt-6 flex items-center justify-end gap-3">
              <button
                type="button"
                onClick={() => setShowAddCategoryModal(false)}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/60 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleAddCategory}
                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500"
              >
                <Plus className="h-4 w-4" />
                Kategori Oluştur
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Print-only content */}
      <style jsx global>{`
        @media print {
          body * {
            visibility: hidden;
          }
          .print-content,
          .print-content * {
            visibility: visible;
          }
          .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
            color: black;
            padding: 40px;
          }
        }
      `}</style>

      {/* Kategori Düzenleme Modalı */}
      {editingCategory && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <button
            type="button"
            className="absolute inset-0 bg-black/70 backdrop-blur-sm"
            onClick={closeEditCategoryModal}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="mb-6 flex items-center justify-between">
              <h2 className="text-lg font-bold">Kategori Bilgilerini Düzenle</h2>
              <button
                type="button"
                onClick={closeEditCategoryModal}
                className="rounded-lg p-1 text-white/50 transition hover:bg-white/10 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="space-y-4">
              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">Kategori Başlığı</label>
                <input
                  type="text"
                  value={editCategoryTitle}
                  onChange={(e) => setEditCategoryTitle(e.target.value)}
                  placeholder="Örn: Gümrük Dilekçeleri"
                  className="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white outline-none placeholder:text-white/30 focus:border-purple-500/50"
                />
              </div>
              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">Açıklama</label>
                <input
                  type="text"
                  value={editCategoryDescription}
                  onChange={(e) => setEditCategoryDescription(e.target.value)}
                  placeholder="Örn: Belge şablonları"
                  className="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white outline-none placeholder:text-white/30 focus:border-purple-500/50"
                />
              </div>
            </div>
            
            <div className="mt-6 flex justify-end gap-3">
              <button
                type="button"
                onClick={closeEditCategoryModal}
                className="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-white/70 transition hover:bg-white/5"
              >
                İptal
              </button>
              <button
                type="button"
                onClick={handleUpdateCategory}
                className="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-500"
              >
                Kaydet
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
