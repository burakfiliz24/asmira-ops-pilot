"use client";

import { Plus, FolderOpen, Download, X, Upload, Eye, Trash2, CheckCircle, AlertTriangle, UserCheck, Edit3 } from "lucide-react";
import { useState, useRef } from "react";
import { PDFDocument } from "pdf-lib";
import { saveAs } from "file-saver";
import { cn } from "@/lib/utils/cn";
import { useDocumentStore, type Driver, type DocumentInfo } from "@/store/documentStore";

function isExpired(dateStr: string | null): boolean {
  if (!dateStr) return false;
  return new Date(dateStr) < new Date();
}

function countUploaded(docs: DocumentInfo[]): number {
  return docs.filter((d) => d.fileName !== null).length;
}

export default function DriverDocumentsPage() {
  const drivers = useDocumentStore((state) => state.drivers);
  const addDriver = useDocumentStore((state) => state.addDriver);
  const updateDriver = useDocumentStore((state) => state.updateDriver);
  const deleteDriver = useDocumentStore((state) => state.deleteDriver);
  const uploadDriverDocument = useDocumentStore((state) => state.uploadDriverDocument);
  const updateDriverDocument = useDocumentStore((state) => state.updateDriverDocument);
  const deleteDriverDocument = useDocumentStore((state) => state.deleteDriverDocument);

  const [editingId, setEditingId] = useState<string | null>(null);
  const [draft, setDraft] = useState<{ name: string; tcNo: string; phone: string } | null>(null);
  const [panelDriverId, setPanelDriverId] = useState<string | null>(null);
  const fileInputRefs = useRef<Record<string, HTMLInputElement | null>>({});

  const [showNewModal, setShowNewModal] = useState(false);
  const [newName, setNewName] = useState("");
  const [newTcNo, setNewTcNo] = useState("");
  const [newPhone, setNewPhone] = useState("");

  // Geçici evrak değişiklikleri - kaydet butonuna basılana kadar store'a yazılmaz
  const [pendingChanges, setPendingChanges] = useState<{
    uploads: { docType: string; file: File; fileName: string; fileUrl: string }[];
    expiryDates: { docType: string; date: string | null }[];
    deletions: { docType: string }[];
  }>({ uploads: [], expiryDates: [], deletions: [] });

  const panelDriver = panelDriverId ? drivers.find((d) => d.id === panelDriverId) : null;
  
  // Bekleyen değişiklik var mı?
  const hasPendingChanges = pendingChanges.uploads.length > 0 || pendingChanges.expiryDates.length > 0 || pendingChanges.deletions.length > 0;

  function startEdit(driver: Driver) {
    setEditingId(driver.id);
    setDraft({ name: driver.name, tcNo: driver.tcNo, phone: driver.phone });
  }

  function cancelEdit() {
    setEditingId(null);
    setDraft(null);
  }

  function saveEdit() {
    if (!editingId || !draft) return;
    updateDriver(editingId, { name: draft.name, tcNo: draft.tcNo, phone: draft.phone });
    setEditingId(null);
    setDraft(null);
  }

  function openPanel(driverId: string) {
    setPanelDriverId(driverId);
    setPendingChanges({ uploads: [], expiryDates: [], deletions: [] });
  }

  function closePanel() {
    if (hasPendingChanges) {
      if (!confirm("Kaydedilmemiş değişiklikler var. Çıkmak istediğinize emin misiniz?")) {
        return;
      }
    }
    setPanelDriverId(null);
    setPendingChanges({ uploads: [], expiryDates: [], deletions: [] });
  }

  // Geçici olarak dosya yükle
  function handleFileUpload(docType: string, file: File) {
    const fileUrl = URL.createObjectURL(file);
    setPendingChanges(prev => ({
      ...prev,
      uploads: [
        ...prev.uploads.filter(u => u.docType !== docType),
        { docType, file, fileName: file.name, fileUrl }
      ],
      deletions: prev.deletions.filter(d => d.docType !== docType)
    }));
  }

  // Geçici olarak tarih güncelle
  function handleExpiryDateChange(docType: string, date: string | null) {
    setPendingChanges(prev => ({
      ...prev,
      expiryDates: [
        ...prev.expiryDates.filter(e => e.docType !== docType),
        { docType, date }
      ]
    }));
  }

  // Geçici olarak evrak sil
  function handleDeleteDoc(docType: string) {
    setPendingChanges(prev => ({
      ...prev,
      deletions: [
        ...prev.deletions.filter(d => d.docType !== docType),
        { docType }
      ],
      uploads: prev.uploads.filter(u => u.docType !== docType)
    }));
  }

  // Tüm değişiklikleri kaydet
  function handleSaveChanges() {
    if (!panelDriverId) return;

    for (const del of pendingChanges.deletions) {
      deleteDriverDocument(panelDriverId, del.docType);
    }

    for (const upload of pendingChanges.uploads) {
      uploadDriverDocument(panelDriverId, upload.docType, upload.file);
    }

    for (const expiry of pendingChanges.expiryDates) {
      updateDriverDocument(panelDriverId, expiry.docType, { expiryDate: expiry.date });
    }

    setPendingChanges({ uploads: [], expiryDates: [], deletions: [] });
    alert("Değişiklikler kaydedildi.");
  }

  // Bir evrak için pending state'i al
  function getPendingState(docType: string) {
    const pendingUpload = pendingChanges.uploads.find(u => u.docType === docType);
    const pendingExpiry = pendingChanges.expiryDates.find(e => e.docType === docType);
    const pendingDeletion = pendingChanges.deletions.find(d => d.docType === docType);
    return { pendingUpload, pendingExpiry, pendingDeletion };
  }

  async function handleDownloadAll(driver: Driver) {
    const uploaded = driver.documents.filter((d) => d.fileName && d.fileBlob);
    if (uploaded.length === 0) {
      alert("Bu şoför için yüklü evrak bulunmuyor.");
      return;
    }

    try {
      const mergedPdf = await PDFDocument.create();

      for (const doc of uploaded) {
        if (!doc.fileBlob || !doc.fileName) continue;
        
        const arrayBuffer = await doc.fileBlob.arrayBuffer();
        const isPdf = doc.fileName.toLowerCase().endsWith(".pdf");
        
        if (isPdf) {
          const pdfDoc = await PDFDocument.load(arrayBuffer);
          const pages = await mergedPdf.copyPages(pdfDoc, pdfDoc.getPageIndices());
          pages.forEach((page) => mergedPdf.addPage(page));
        } else {
          const page = mergedPdf.addPage();
          const { width, height } = page.getSize();
          
          let image;
          if (doc.fileName.toLowerCase().endsWith(".png")) {
            image = await mergedPdf.embedPng(arrayBuffer);
          } else {
            image = await mergedPdf.embedJpg(arrayBuffer);
          }
          
          const imgDims = image.scale(Math.min(width / image.width, height / image.height) * 0.9);
          page.drawImage(image, {
            x: (width - imgDims.width) / 2,
            y: (height - imgDims.height) / 2,
            width: imgDims.width,
            height: imgDims.height,
          });
        }
      }

      const pdfBytes = await mergedPdf.save();
      const blob = new Blob([new Uint8Array(pdfBytes)], { type: "application/pdf" });
      saveAs(blob, `${driver.name.replace(/\s+/g, "_")}_evraklar.pdf`);
    } catch (error) {
      console.error("PDF birleştirme hatası:", error);
      alert("Dosyalar birleştirilirken bir hata oluştu.");
    }
  }

  function handleDeleteDriver(driverId: string, driverName: string) {
    if (confirm(`"${driverName}" kaydını silmek istediğinize emin misiniz?`)) {
      deleteDriver(driverId);
    }
  }

  function openNewModal() {
    setNewName("");
    setNewTcNo("");
    setNewPhone("");
    setShowNewModal(true);
  }

  function closeNewModal() {
    setShowNewModal(false);
  }

  function handleSaveNew() {
    if (!newName.trim() || !newTcNo.trim()) {
      alert("Lütfen ad ve TC kimlik numarasını girin.");
      return;
    }
    addDriver({
      name: newName.trim(),
      tcNo: newTcNo.trim(),
      phone: newPhone.trim(),
    });
    closeNewModal();
  }

  const totalDrivers = drivers.length;

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-purple-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-purple-500/60 via-purple-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              ŞOFÖR EVRAKLARI
            </div>
            <div className="text-3xl font-black tracking-tight">Şoför Belgeleri</div>
          </div>

          <button
            type="button"
            onClick={openNewModal}
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(147,51,234,0.25)] transition-all hover:from-purple-500 hover:to-purple-600"
          >
            <Plus className="h-4 w-4" />
            Yeni Şoför Ekle
          </button>
        </div>

        {/* Stats Bar */}
        <div className="relative flex flex-none items-center gap-3 px-6 py-2.5">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-purple-500/40 via-purple-400/20 to-transparent" />
          
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <div className="h-2 w-2 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(147,51,234,0.6)]" />
            <span className="text-xs font-medium text-white/70">Toplam Şoför</span>
            <span className="text-sm font-bold text-white">{totalDrivers}</span>
          </div>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6">
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {drivers.map((driver) => {
              const uploaded = countUploaded(driver.documents);
              const total = driver.documents.length;
              const isComplete = uploaded === total;
              const progress = (uploaded / total) * 100;

              return (
                <div
                  key={driver.id}
                  className={cn(
                    "group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06]",
                    isComplete
                      ? "border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]"
                      : "border-purple-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]"
                  )}
                >
                  <div className="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-purple-500/10 blur-2xl transition-opacity group-hover:opacity-100 opacity-0" />

                  {editingId !== driver.id && (
                    <button
                      type="button"
                      onClick={() => handleDeleteDriver(driver.id, driver.name)}
                      className="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20"
                      title="Kaydı Sil"
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                    </button>
                  )}

                  <div className="mb-4 flex items-start gap-3">
                    <div className={cn(
                      "flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]",
                      isComplete 
                        ? "from-emerald-500/25 to-emerald-600/10 text-emerald-400" 
                        : "from-purple-500/25 to-purple-600/10 text-purple-400"
                    )}>
                      <UserCheck className="h-5 w-5" />
                    </div>
                    <div className="min-w-0 flex-1">
                      {editingId === driver.id ? (
                        <div className="space-y-2">
                          <input
                            value={draft?.name ?? ""}
                            onChange={(e) =>
                              setDraft((p) => ({
                                name: e.target.value,
                                tcNo: p?.tcNo ?? "",
                                phone: p?.phone ?? "",
                              }))
                            }
                            className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm font-semibold outline-none focus:border-purple-500"
                            placeholder="Ad Soyad"
                          />
                          <input
                            value={draft?.tcNo ?? ""}
                            onChange={(e) =>
                              setDraft((p) => ({
                                name: p?.name ?? "",
                                tcNo: e.target.value,
                                phone: p?.phone ?? "",
                              }))
                            }
                            className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm outline-none focus:border-purple-500"
                            placeholder="TC Kimlik No"
                          />
                          <input
                            value={draft?.phone ?? ""}
                            onChange={(e) =>
                              setDraft((p) => ({
                                name: p?.name ?? "",
                                tcNo: p?.tcNo ?? "",
                                phone: e.target.value,
                              }))
                            }
                            className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm outline-none focus:border-purple-500"
                            placeholder="Telefon"
                          />
                        </div>
                      ) : (
                        <>
                          <div className="truncate text-[15px] font-bold tracking-tight">{driver.name}</div>
                          <div className="truncate text-xs text-white/50">{driver.tcNo}</div>
                          {driver.phone && (
                            <div className="truncate text-xs text-white/40">{driver.phone}</div>
                          )}
                        </>
                      )}
                    </div>
                  </div>

                  <div className="mb-3">
                    <div className="mb-1.5 flex items-center justify-between text-[11px]">
                      <span className="text-white/60">Evrak Durumu</span>
                      <span className={cn(
                        "font-semibold",
                        isComplete ? "text-emerald-400" : "text-amber-400"
                      )}>
                        {uploaded}/{total}
                      </span>
                    </div>
                    <div className="h-1.5 overflow-hidden rounded-full bg-white/15">
                      <div
                        className={cn(
                          "h-full rounded-full transition-all duration-500",
                          isComplete
                            ? "bg-gradient-to-r from-emerald-500 to-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)]"
                            : "bg-gradient-to-r from-amber-500 to-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.4)]"
                        )}
                        style={{ width: `${progress}%` }}
                      />
                    </div>
                  </div>

                  <div className="mb-4">
                    {isComplete ? (
                      <div className="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-400">
                        <CheckCircle className="h-3 w-3" />
                        Tüm Evraklar Tamam
                      </div>
                    ) : (
                      <div className="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-1 text-[11px] font-semibold text-amber-400">
                        <AlertTriangle className="h-3 w-3" />
                        {total - uploaded} Evrak Eksik
                      </div>
                    )}
                  </div>

                  <div className="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">
                    {editingId === driver.id ? (
                      <>
                        <button
                          type="button"
                          onClick={saveEdit}
                          className="flex-1 rounded-lg bg-purple-600 py-2 text-xs font-semibold text-white transition hover:bg-purple-500"
                        >
                          Kaydet
                        </button>
                        <button
                          type="button"
                          onClick={cancelEdit}
                          className="flex-1 rounded-lg border border-white/10 bg-white/5 py-2 text-xs font-medium text-white/70 transition hover:bg-white/10"
                        >
                          İptal
                        </button>
                      </>
                    ) : (
                      <>
                        <button
                          type="button"
                          onClick={() => openPanel(driver.id)}
                          className="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"
                        >
                          <FolderOpen className="h-3.5 w-3.5" />
                          Evraklar
                        </button>
                        <button
                          type="button"
                          onClick={() => startEdit(driver)}
                          className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                          title="Düzenle"
                        >
                          <Edit3 className="h-3.5 w-3.5" />
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDownloadAll(driver)}
                          className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                          title="Tümünü İndir"
                        >
                          <Download className="h-3.5 w-3.5" />
                        </button>
                      </>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </div>

      {/* Document Panel */}
      {panelDriver && (
        <div className="fixed inset-0 z-50 flex justify-end">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closePanel}
            aria-label="Kapat"
          />
          <div className="relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-purple-500/60 via-purple-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div>
                <div className="text-lg font-bold">{panelDriver.name}</div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={closePanel}
                aria-label="Kapat"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="flex-1 overflow-y-auto px-5 py-4">
              <div className="space-y-4">
                {panelDriver.documents.map((doc) => {
                  const { pendingUpload, pendingExpiry, pendingDeletion } = getPendingState(doc.type);
                  
                  const effectiveFileName = pendingDeletion ? null : (pendingUpload?.fileName ?? doc.fileName);
                  const effectiveFileUrl = pendingDeletion ? null : (pendingUpload?.fileUrl ?? doc.fileUrl);
                  const effectiveExpiryDate = pendingExpiry?.date !== undefined ? pendingExpiry.date : doc.expiryDate;
                  
                  const expired = isExpired(effectiveExpiryDate);
                  const hasChanges = pendingUpload || pendingExpiry || pendingDeletion;
                  
                  return (
                    <div
                      key={doc.type}
                      className={cn(
                        "rounded-lg border p-4",
                        hasChanges
                          ? "border-amber-500/50 bg-amber-500/5"
                          : expired
                            ? "border-red-500/50 bg-red-500/10"
                            : "border-white/10 bg-white/5"
                      )}
                    >
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                          {effectiveFileName ? (
                            <CheckCircle className="h-4 w-4 text-emerald-400" />
                          ) : (
                            <div className="h-4 w-4 rounded-full border-2 border-white/30" />
                          )}
                          <span className="font-medium">{doc.label}</span>
                          {hasChanges && (
                            <span className="ml-2 rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-300">
                              DEĞİŞİKLİK
                            </span>
                          )}
                          {expired && !hasChanges && (
                            <span className="ml-2 rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-semibold text-red-300">
                              SÜRESİ GEÇMİŞ
                            </span>
                          )}
                        </div>
                      </div>

                      {effectiveFileName ? (
                        <div className="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2">
                          <span className="truncate text-sm text-white/70">{effectiveFileName}</span>
                          <div className="flex items-center gap-2">
                            <a
                              href={effectiveFileUrl ?? "#"}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10"
                              title="Önizle"
                            >
                              <Eye className="h-4 w-4" />
                            </a>
                            <a
                              href={effectiveFileUrl ?? "#"}
                              download={effectiveFileName}
                              className="inline-flex h-7 w-7 items-center justify-center rounded-md text-sky-400 hover:bg-sky-500/20"
                              title="İndir"
                            >
                              <Download className="h-4 w-4" />
                            </a>
                            <button
                              type="button"
                              onClick={() => handleDeleteDoc(doc.type)}
                              className="inline-flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-500/20"
                              title="Sil"
                            >
                              <Trash2 className="h-4 w-4" />
                            </button>
                          </div>
                        </div>
                      ) : (
                        <div className="mt-3">
                          <input
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            ref={(el) => {
                              fileInputRefs.current[doc.type] = el;
                            }}
                            className="hidden"
                            onChange={(e) => {
                              const file = e.target.files?.[0];
                              if (file) handleFileUpload(doc.type, file);
                            }}
                          />
                          <button
                            type="button"
                            onClick={() => fileInputRefs.current[doc.type]?.click()}
                            className="inline-flex items-center gap-2 rounded-md border border-dashed border-white/20 bg-white/5 px-4 py-2 text-sm hover:bg-white/10"
                          >
                            <Upload className="h-4 w-4" />
                            PDF veya Görsel Yükle
                          </button>
                        </div>
                      )}

                      {/* Geçerlilik Tarihi Girişi */}
                      <div className="mt-3 flex items-center gap-2">
                        <label className="text-xs text-white/50">Son Geçerlilik:</label>
                        <input
                          type="date"
                          value={effectiveExpiryDate || ""}
                          onChange={(e) => {
                            const newDate = e.target.value || null;
                            handleExpiryDateChange(doc.type, newDate);
                          }}
                          className="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-purple-500/50"
                        />
                        {effectiveExpiryDate && (
                          <button
                            type="button"
                            onClick={() => handleExpiryDateChange(doc.type, null)}
                            className="text-xs text-white/40 hover:text-white/60"
                            title="Tarihi Kaldır"
                          >
                            ✕
                          </button>
                        )}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            <div className="border-t border-white/10 px-5 py-4">
              <button
                type="button"
                onClick={handleSaveChanges}
                disabled={!hasPendingChanges}
                className={cn(
                  "inline-flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-semibold text-white transition-all",
                  hasPendingChanges
                    ? "bg-gradient-to-br from-emerald-600 to-emerald-700 shadow-[0_2px_10px_rgba(52,211,153,0.25)] hover:from-emerald-500 hover:to-emerald-600"
                    : "bg-white/10 text-white/40 cursor-not-allowed"
                )}
              >
                <CheckCircle className="h-4 w-4" />
                {hasPendingChanges ? `Kaydet (${pendingChanges.uploads.length + pendingChanges.expiryDates.length + pendingChanges.deletions.length} değişiklik)` : "Kaydet"}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* New Driver Modal */}
      {showNewModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closeNewModal}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-purple-500/60 via-purple-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">YENİ KAYIT</div>
                <div className="text-lg font-bold">Şoför Tanımla</div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={closeNewModal}
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
                  value={newName}
                  onChange={(e) => setNewName(e.target.value)}
                  placeholder="Örn: Ahmet Yılmaz"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-purple-500/50"
                />
              </div>
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">TC Kimlik No *</label>
                <input
                  type="text"
                  value={newTcNo}
                  onChange={(e) => setNewTcNo(e.target.value)}
                  placeholder="Örn: 12345678901"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-purple-500/50"
                />
              </div>
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Telefon</label>
                <input
                  type="text"
                  value={newPhone}
                  onChange={(e) => setNewPhone(e.target.value)}
                  placeholder="Örn: 0532 123 45 67"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-purple-500/50"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
              <button
                type="button"
                onClick={closeNewModal}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleSaveNew}
                className="rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(147,51,234,0.25)] transition-all hover:from-purple-500 hover:to-purple-600"
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
