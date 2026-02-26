"use client";

import { Plus, FolderOpen, Download, X, Upload, Eye, Trash2, CheckCircle, AlertTriangle, Truck, Edit3, Container } from "lucide-react";
import { useState, useRef, useMemo } from "react";
import { PDFDocument } from "pdf-lib";
import { saveAs } from "file-saver";
import { cn } from "@/lib/utils/cn";
import { useDocumentStore, type Vehicle, type DocumentInfo, type DocumentTarget } from "@/store/documentStore";

function isExpired(dateStr: string | null): boolean {
  if (!dateStr) return false;
  return new Date(dateStr) < new Date();
}

function countUploaded(docs: DocumentInfo[]): number {
  return docs.filter((d) => d.fileName !== null).length;
}

export default function AsmiraVehicleDocumentsPage() {
  const trucks = useDocumentStore((state) => state.trucks);
  const trailers = useDocumentStore((state) => state.trailers);
  const vehicleSets = useDocumentStore((state) => state.vehicleSets);
  const addVehicle = useDocumentStore((state) => state.addVehicle);
  const updateVehicle = useDocumentStore((state) => state.updateVehicle);
  const deleteVehicle = useDocumentStore((state) => state.deleteVehicle);
  const uploadVehicleDocument = useDocumentStore((state) => state.uploadVehicleDocument);
  const updateVehicleDocument = useDocumentStore((state) => state.updateVehicleDocument);
  const deleteVehicleDocument = useDocumentStore((state) => state.deleteVehicleDocument);
  
  // vehicles'ı useMemo ile hesapla - sonsuz döngüyü önler
  const vehicles = useMemo(() => {
    return vehicleSets
      .filter(set => set.category === "asmira")
      .map(set => {
        const truck = trucks.find(t => t.id === set.truckId);
        const trailer = trailers.find(t => t.id === set.trailerId);
        return {
          id: set.id,
          vehiclePlate: truck?.plate || '',
          trailerPlate: trailer?.plate || '',
          category: set.category,
          vehicleDocuments: truck?.documents || [],
          trailerDocuments: trailer?.documents || [],
        };
      });
  }, [trucks, trailers, vehicleSets]);

  const [editingId, setEditingId] = useState<string | null>(null);
  const [draft, setDraft] = useState<{ vehiclePlate: string; trailerPlate: string } | null>(null);
  const [panelVehicleId, setPanelVehicleId] = useState<string | null>(null);
  const [panelTab, setPanelTab] = useState<DocumentTarget>('truck'); // 'truck' veya 'trailer'
  const fileInputRefs = useRef<Record<string, HTMLInputElement | null>>({});

  const [showNewModal, setShowNewModal] = useState(false);
  const [newVehiclePlate, setNewVehiclePlate] = useState("");
  const [newTrailerPlate, setNewTrailerPlate] = useState("");

  // Geçici evrak değişiklikleri - kaydet butonuna basılana kadar store'a yazılmaz
  const [pendingChanges, setPendingChanges] = useState<{
    uploads: { target: DocumentTarget; docType: string; file: File; fileName: string; fileUrl: string }[];
    expiryDates: { target: DocumentTarget; docType: string; date: string | null }[];
    deletions: { target: DocumentTarget; docType: string }[];
  }>({ uploads: [], expiryDates: [], deletions: [] });

  const panelVehicle = panelVehicleId ? vehicles.find((v) => v.id === panelVehicleId) : null;
  
  // Bekleyen değişiklik var mı?
  const hasPendingChanges = pendingChanges.uploads.length > 0 || pendingChanges.expiryDates.length > 0 || pendingChanges.deletions.length > 0;

  function startEdit(vehicle: Vehicle) {
    setEditingId(vehicle.id);
    setDraft({ vehiclePlate: vehicle.vehiclePlate, trailerPlate: vehicle.trailerPlate });
  }

  function cancelEdit() {
    setEditingId(null);
    setDraft(null);
  }

  function saveEdit() {
    if (!editingId || !draft) return;
    updateVehicle(editingId, { vehiclePlate: draft.vehiclePlate, trailerPlate: draft.trailerPlate });
    setEditingId(null);
    setDraft(null);
  }

  function openPanel(vehicleId: string) {
    setPanelVehicleId(vehicleId);
    setPanelTab('truck'); // Panel açıldığında çekici sekmesini göster
    // Pending changes'ı temizle
    setPendingChanges({ uploads: [], expiryDates: [], deletions: [] });
  }

  function closePanel() {
    // Kaydedilmemiş değişiklik varsa uyar
    if (hasPendingChanges) {
      if (!confirm("Kaydedilmemiş değişiklikler var. Çıkmak istediğinize emin misiniz?")) {
        return;
      }
    }
    setPanelVehicleId(null);
    setPendingChanges({ uploads: [], expiryDates: [], deletions: [] });
  }

  // Geçici olarak dosya yükle (store'a yazmaz)
  function handleFileUpload(target: DocumentTarget, docType: string, file: File) {
    const fileUrl = URL.createObjectURL(file);
    setPendingChanges(prev => ({
      ...prev,
      uploads: [
        ...prev.uploads.filter(u => !(u.target === target && u.docType === docType)),
        { target, docType, file, fileName: file.name, fileUrl }
      ],
      // Silme listesinden çıkar (eğer varsa)
      deletions: prev.deletions.filter(d => !(d.target === target && d.docType === docType))
    }));
  }

  // Geçici olarak tarih güncelle (store'a yazmaz)
  function handleExpiryDateChange(target: DocumentTarget, docType: string, date: string | null) {
    setPendingChanges(prev => ({
      ...prev,
      expiryDates: [
        ...prev.expiryDates.filter(e => !(e.target === target && e.docType === docType)),
        { target, docType, date }
      ]
    }));
  }

  // Geçici olarak evrak sil (store'a yazmaz)
  function handleDeleteDoc(target: DocumentTarget, docType: string) {
    setPendingChanges(prev => ({
      ...prev,
      deletions: [
        ...prev.deletions.filter(d => !(d.target === target && d.docType === docType)),
        { target, docType }
      ],
      // Upload listesinden çıkar (eğer varsa)
      uploads: prev.uploads.filter(u => !(u.target === target && u.docType === docType))
    }));
  }

  // Tüm değişiklikleri kaydet
  function handleSaveChanges() {
    if (!panelVehicleId) return;

    // Silmeleri uygula
    for (const del of pendingChanges.deletions) {
      deleteVehicleDocument(panelVehicleId, del.target, del.docType);
    }

    // Yüklemeleri uygula
    for (const upload of pendingChanges.uploads) {
      uploadVehicleDocument(panelVehicleId, upload.target, upload.docType, upload.file);
    }

    // Tarih güncellemelerini uygula
    for (const expiry of pendingChanges.expiryDates) {
      updateVehicleDocument(panelVehicleId, expiry.target, expiry.docType, { expiryDate: expiry.date });
    }

    // Pending changes'ı temizle
    setPendingChanges({ uploads: [], expiryDates: [], deletions: [] });
    alert("Değişiklikler kaydedildi.");
  }

  // Bir evrak için pending state'i al
  function getPendingState(target: DocumentTarget, docType: string) {
    const pendingUpload = pendingChanges.uploads.find(u => u.target === target && u.docType === docType);
    const pendingExpiry = pendingChanges.expiryDates.find(e => e.target === target && e.docType === docType);
    const pendingDeletion = pendingChanges.deletions.find(d => d.target === target && d.docType === docType);
    return { pendingUpload, pendingExpiry, pendingDeletion };
  }

  async function handleDownloadAll(vehicle: Vehicle) {
    const vehicleUploaded = vehicle.vehicleDocuments.filter((d: DocumentInfo) => d.fileName && d.fileBlob);
    const trailerUploaded = vehicle.trailerDocuments.filter((d: DocumentInfo) => d.fileName && d.fileBlob);
    const uploaded = [...vehicleUploaded, ...trailerUploaded];
    
    if (uploaded.length === 0) {
      alert("Bu araç için yüklü evrak bulunmuyor.");
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
      saveAs(blob, `${vehicle.vehiclePlate.replace(/\s+/g, "_")}_tum_evraklar.pdf`);
    } catch (error) {
      console.error("PDF birleştirme hatası:", error);
      alert("Dosyalar birleştirilirken bir hata oluştu.");
    }
  }

  function handleDeleteVehicle(vehicleId: string, vehiclePlate: string) {
    if (confirm(`"${vehiclePlate}" kaydını silmek istediğinize emin misiniz?`)) {
      deleteVehicle(vehicleId);
    }
  }

  function openNewModal() {
    setNewVehiclePlate("");
    setNewTrailerPlate("");
    setShowNewModal(true);
  }

  function closeNewModal() {
    setShowNewModal(false);
  }

  function handleSaveNew() {
    if (!newVehiclePlate.trim() || !newTrailerPlate.trim()) {
      alert("Lütfen her iki plakayı da girin.");
      return;
    }
    addVehicle({
      vehiclePlate: newVehiclePlate.trim(),
      trailerPlate: newTrailerPlate.trim(),
      category: "asmira",
    });
    closeNewModal();
  }

  const totalVehicles = vehicles.length;

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex flex-col rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              ARAÇ EVRAKLARI
            </div>
            <div className="text-3xl font-black tracking-tight">Asmira Özmal</div>
          </div>

          <button
            type="button"
            onClick={openNewModal}
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
          >
            <Plus className="h-4 w-4" />
            Yeni Araç/Dorse Ekle
          </button>
        </div>

        {/* Stats Bar */}
        <div className="relative flex flex-none items-center gap-3 px-6 py-2.5">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/40 via-blue-400/20 to-transparent" />
          
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <div className="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]" />
            <span className="text-xs font-medium text-white/70">Toplam Araç</span>
            <span className="text-sm font-bold text-white">{totalVehicles}</span>
          </div>
        </div>

        <div className="p-6">
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {vehicles.map((vehicle) => {
              const vehicleUploaded = countUploaded(vehicle.vehicleDocuments);
              const trailerUploaded = countUploaded(vehicle.trailerDocuments);
              const uploaded = vehicleUploaded + trailerUploaded;
              const total = vehicle.vehicleDocuments.length + vehicle.trailerDocuments.length;
              const isComplete = uploaded === total;
              const progress = total > 0 ? (uploaded / total) * 100 : 0;

              return (
                <div
                  key={vehicle.id}
                  className={cn(
                    "group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06]",
                    isComplete
                      ? "border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]"
                      : "border-blue-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]"
                  )}
                >
                  <div className="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl transition-opacity group-hover:opacity-100 opacity-0" />

                  {editingId !== vehicle.id && (
                    <button
                      type="button"
                      onClick={() => handleDeleteVehicle(vehicle.id, vehicle.vehiclePlate)}
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
                        : "from-blue-500/25 to-blue-600/10 text-blue-400"
                    )}>
                      <Truck className="h-5 w-5" />
                    </div>
                    <div className="min-w-0 flex-1">
                      {editingId === vehicle.id ? (
                        <div className="space-y-2">
                          <input
                            value={draft?.vehiclePlate ?? ""}
                            onChange={(e) =>
                              setDraft((p) => ({
                                vehiclePlate: e.target.value,
                                trailerPlate: p?.trailerPlate ?? "",
                              }))
                            }
                            className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm font-semibold outline-none focus:border-blue-500"
                            placeholder="Araç Plaka"
                          />
                          <input
                            value={draft?.trailerPlate ?? ""}
                            onChange={(e) =>
                              setDraft((p) => ({
                                vehiclePlate: p?.vehiclePlate ?? "",
                                trailerPlate: e.target.value,
                              }))
                            }
                            className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm outline-none focus:border-blue-500"
                            placeholder="Dorse Plaka"
                          />
                        </div>
                      ) : (
                        <>
                          <div className="truncate text-[15px] font-bold tracking-tight">{vehicle.vehiclePlate}</div>
                          <div className="truncate text-xs text-white/50">{vehicle.trailerPlate}</div>
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
                    {editingId === vehicle.id ? (
                      <>
                        <button
                          type="button"
                          onClick={saveEdit}
                          className="flex-1 rounded-lg bg-blue-600 py-2 text-xs font-semibold text-white transition hover:bg-blue-500"
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
                          onClick={() => openPanel(vehicle.id)}
                          className="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"
                        >
                          <FolderOpen className="h-3.5 w-3.5" />
                          Evraklar
                        </button>
                        <button
                          type="button"
                          onClick={() => startEdit(vehicle)}
                          className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                          title="Düzenle"
                        >
                          <Edit3 className="h-3.5 w-3.5" />
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDownloadAll(vehicle)}
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

      {panelVehicle && (
        <div className="fixed inset-0 z-50 flex justify-end">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closePanel}
            aria-label="Kapat"
          />
          <div className="relative z-10 flex h-full w-full max-w-md flex-col overflow-hidden bg-[#0B1220] text-white shadow-xl">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div>
                <div className="text-lg font-bold">{panelTab === 'truck' ? panelVehicle.vehiclePlate : panelVehicle.trailerPlate}</div>
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

            {/* Sekmeler */}
            <div className="flex border-b border-white/10">
              <button
                type="button"
                onClick={() => setPanelTab('truck')}
                className={cn(
                  "flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium transition-all",
                  panelTab === 'truck'
                    ? "border-b-2 border-blue-500 text-blue-400"
                    : "text-white/50 hover:text-white/70"
                )}
              >
                <Truck className="h-4 w-4" />
                Araç ({panelVehicle.vehiclePlate})
              </button>
              <button
                type="button"
                onClick={() => setPanelTab('trailer')}
                className={cn(
                  "flex flex-1 items-center justify-center gap-2 py-3 text-sm font-medium transition-all",
                  panelTab === 'trailer'
                    ? "border-b-2 border-cyan-500 text-cyan-400"
                    : "text-white/50 hover:text-white/70"
                )}
              >
                <Container className="h-4 w-4" />
                Dorse ({panelVehicle.trailerPlate})
              </button>
            </div>

            <div className="flex-1 overflow-y-auto px-5 py-4">
              <div className="space-y-4">
                {(panelTab === 'truck' ? panelVehicle.vehicleDocuments : panelVehicle.trailerDocuments).map((doc: DocumentInfo) => {
                  const { pendingUpload, pendingExpiry, pendingDeletion } = getPendingState(panelTab, doc.type);
                  
                  // Pending state'e göre değerleri belirle
                  const effectiveFileName = pendingDeletion ? null : (pendingUpload?.fileName ?? doc.fileName);
                  const effectiveFileUrl = pendingDeletion ? null : (pendingUpload?.fileUrl ?? doc.fileUrl);
                  const effectiveExpiryDate = pendingExpiry?.date !== undefined ? pendingExpiry.date : doc.expiryDate;
                  
                  const expired = isExpired(effectiveExpiryDate);
                  const refKey = `${panelTab}-${doc.type}`;
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
                              onClick={() => handleDeleteDoc(panelTab, doc.type)}
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
                              fileInputRefs.current[refKey] = el;
                            }}
                            className="hidden"
                            onChange={(e) => {
                              const file = e.target.files?.[0];
                              if (file) handleFileUpload(panelTab, doc.type, file);
                            }}
                          />
                          <button
                            type="button"
                            onClick={() => fileInputRefs.current[refKey]?.click()}
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
                            handleExpiryDateChange(panelTab, doc.type, newDate);
                          }}
                          className="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-blue-500/50"
                        />
                        {effectiveExpiryDate && (
                          <button
                            type="button"
                            onClick={() => handleExpiryDateChange(panelTab, doc.type, null)}
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
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">YENİ KAYIT</div>
                <div className="text-lg font-bold">Araç/Dorse Tanımla</div>
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
                <label className="mb-2 block text-xs font-semibold text-white/70">Araç Plaka</label>
                <input
                  type="text"
                  value={newVehiclePlate}
                  onChange={(e) => setNewVehiclePlate(e.target.value)}
                  placeholder="Örn: 34 ASM 014"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50"
                />
              </div>
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Dorse Plaka</label>
                <input
                  type="text"
                  value={newTrailerPlate}
                  onChange={(e) => setNewTrailerPlate(e.target.value)}
                  placeholder="Örn: 34 DOR 123"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50"
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
                className="rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
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
