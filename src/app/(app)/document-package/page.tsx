"use client";

import { useState, useMemo, useRef } from "react";
import { Download, Truck, UserCheck, Check, FileText, AlertCircle, Container, ChevronDown } from "lucide-react";
import { PDFDocument } from "pdf-lib";
import { saveAs } from "file-saver";
import { cn } from "@/lib/utils/cn";
import { useDocumentStore, type DocumentInfo } from "@/store/documentStore";

export default function DocumentPackagePage() {
  const trucks = useDocumentStore((state) => state.trucks);
  const trailers = useDocumentStore((state) => state.trailers);
  const drivers = useDocumentStore((state) => state.drivers);
  
  // Ayrı araç (çekici) ve dorse seçimi
  const [selectedTruckId, setSelectedTruckId] = useState<string>("");
  const [selectedTrailerId, setSelectedTrailerId] = useState<string>("");
  const [selectedDriverId, setSelectedDriverId] = useState<string>("");
  
  // Araç ve dorse evrakları için ayrı set'ler
  const [selectedVehicleDocs, setSelectedVehicleDocs] = useState<Set<string>>(new Set());
  const [selectedTrailerDocs, setSelectedTrailerDocs] = useState<Set<string>>(new Set());
  const [selectedDriverDocs, setSelectedDriverDocs] = useState<Set<string>>(new Set());
  
  const [isGenerating, setIsGenerating] = useState(false);
  const [isDriverDropdownOpen, setIsDriverDropdownOpen] = useState(false);
  const driverSectionRef = useRef<HTMLDivElement>(null);
  const driverDropdownRef = useRef<HTMLDivElement>(null);

  // Seçilen çekici ve dorse
  const selectedTruck = useMemo(() => 
    trucks.find((t) => t.id === selectedTruckId), 
    [trucks, selectedTruckId]
  );
  
  const selectedTrailer = useMemo(() => 
    trailers.find((t) => t.id === selectedTrailerId), 
    [trailers, selectedTrailerId]
  );
  
  const selectedDriver = useMemo(() => 
    drivers.find((d) => d.id === selectedDriverId), 
    [drivers, selectedDriverId]
  );

  const toggleVehicleDoc = (docType: string) => {
    setSelectedVehicleDocs((prev) => {
      const next = new Set(prev);
      if (next.has(docType)) {
        next.delete(docType);
      } else {
        next.add(docType);
      }
      return next;
    });
  };

  const toggleTrailerDoc = (docType: string) => {
    setSelectedTrailerDocs((prev) => {
      const next = new Set(prev);
      if (next.has(docType)) {
        next.delete(docType);
      } else {
        next.add(docType);
      }
      return next;
    });
  };

  const toggleDriverDoc = (docType: string) => {
    setSelectedDriverDocs((prev) => {
      const next = new Set(prev);
      if (next.has(docType)) {
        next.delete(docType);
      } else {
        next.add(docType);
      }
      return next;
    });
  };

  const selectAllVehicleDocs = () => {
    if (!selectedTruck) return;
    const uploadedDocs = selectedTruck.documents
      .filter((d: DocumentInfo) => d.fileName)
      .map((d: DocumentInfo) => d.type);
    setSelectedVehicleDocs(new Set(uploadedDocs));
  };

  const selectAllTrailerDocs = () => {
    if (!selectedTrailer) return;
    const uploadedDocs = selectedTrailer.documents
      .filter((d: DocumentInfo) => d.fileName)
      .map((d: DocumentInfo) => d.type);
    setSelectedTrailerDocs(new Set(uploadedDocs));
  };

  const selectAllDriverDocs = () => {
    if (!selectedDriver) return;
    const uploadedDocs = selectedDriver.documents
      .filter((d: DocumentInfo) => d.fileName)
      .map((d: DocumentInfo) => d.type);
    setSelectedDriverDocs(new Set(uploadedDocs));
  };

  const clearAllSelections = () => {
    setSelectedVehicleDocs(new Set());
    setSelectedTrailerDocs(new Set());
    setSelectedDriverDocs(new Set());
  };

  const totalSelected = selectedVehicleDocs.size + selectedTrailerDocs.size + selectedDriverDocs.size;

  const handleGeneratePDF = async () => {
    if (totalSelected === 0) {
      alert("Lütfen en az bir evrak seçin.");
      return;
    }

    setIsGenerating(true);

    try {
      const mergedPdf = await PDFDocument.create();
      const docsToMerge: { label: string; blob: Blob; fileName: string }[] = [];

      // Çekici (Araç) evrakları
      if (selectedTruck) {
        for (const docType of selectedVehicleDocs) {
          const doc = selectedTruck.documents.find((d: DocumentInfo) => d.type === docType);
          if (doc?.fileBlob && doc.fileName) {
            docsToMerge.push({ label: `Araç - ${doc.label}`, blob: doc.fileBlob, fileName: doc.fileName });
          }
        }
      }
      
      // Dorse evrakları
      if (selectedTrailer) {
        for (const docType of selectedTrailerDocs) {
          const doc = selectedTrailer.documents.find((d: DocumentInfo) => d.type === docType);
          if (doc?.fileBlob && doc.fileName) {
            docsToMerge.push({ label: `Dorse - ${doc.label}`, blob: doc.fileBlob, fileName: doc.fileName });
          }
        }
      }

      // Şoför evrakları
      if (selectedDriver) {
        for (const docType of selectedDriverDocs) {
          const doc = selectedDriver.documents.find((d: DocumentInfo) => d.type === docType);
          if (doc?.fileBlob && doc.fileName) {
            docsToMerge.push({ label: doc.label, blob: doc.fileBlob, fileName: doc.fileName });
          }
        }
      }

      if (docsToMerge.length === 0) {
        alert("Seçilen evraklar için yüklenmiş dosya bulunamadı.");
        setIsGenerating(false);
        return;
      }

      for (const doc of docsToMerge) {
        const arrayBuffer = await doc.blob.arrayBuffer();
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
      
      const fileName = [
        selectedTruck?.plate?.replace(/\s+/g, "_"),
        selectedTrailer?.plate?.replace(/\s+/g, "_"),
        selectedDriver?.name?.replace(/\s+/g, "_"),
        "evrak_paketi"
      ].filter(Boolean).join("_") + ".pdf";
      
      saveAs(blob, fileName);
    } catch (error) {
      console.error("PDF oluşturma hatası:", error);
      alert("PDF oluşturulurken bir hata oluştu.");
    } finally {
      setIsGenerating(false);
    }
  };

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-emerald-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-emerald-500/60 via-emerald-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              EVRAK PAKETİ
            </div>
            <div className="text-3xl font-black tracking-tight">Liman Evrak Oluşturucu</div>
          </div>

          <div className="flex items-center gap-3">
            {totalSelected > 0 && (
              <div className="flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5">
                <Check className="h-4 w-4 text-emerald-400" />
                <span className="text-sm font-semibold text-emerald-400">{totalSelected} evrak seçili</span>
              </div>
            )}
            <button
              type="button"
              onClick={handleGeneratePDF}
              disabled={totalSelected === 0 || isGenerating}
              className={cn(
                "inline-flex h-10 items-center gap-2 rounded-lg px-5 text-[13px] font-semibold text-white transition-all",
                totalSelected > 0
                  ? "bg-gradient-to-br from-emerald-600 to-emerald-700 shadow-[0_2px_10px_rgba(52,211,153,0.25)] hover:from-emerald-500 hover:to-emerald-600"
                  : "bg-white/10 text-white/40 cursor-not-allowed"
              )}
            >
              <Download className="h-4 w-4" />
              {isGenerating ? "Oluşturuluyor..." : "PDF Oluştur"}
            </button>
          </div>
        </div>

        {/* Stats Bar */}
        <div className="relative flex flex-none items-center gap-3 px-6 py-2.5">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-emerald-500/40 via-emerald-400/20 to-transparent" />
          
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <Truck className="h-3.5 w-3.5 text-blue-400" />
            <span className="text-xs font-medium text-white/70">Çekici</span>
            <span className="text-sm font-bold text-white">{trucks.length}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <Container className="h-3.5 w-3.5 text-cyan-400" />
            <span className="text-xs font-medium text-white/70">Dorse</span>
            <span className="text-sm font-bold text-white">{trailers.length}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <UserCheck className="h-3.5 w-3.5 text-purple-400" />
            <span className="text-xs font-medium text-white/70">Şoför</span>
            <span className="text-sm font-bold text-white">{drivers.length}</span>
          </div>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6">
          <div className="grid gap-6 lg:grid-cols-2">
            {/* Araç (Çekici) Seçimi */}
            <div className="rounded-xl border border-blue-500/20 bg-gradient-to-br from-white/[0.04] to-transparent p-5">
              <div className="mb-4 flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/25 to-blue-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                  <Truck className="h-5 w-5 text-blue-400" />
                </div>
                <div>
                  <h2 className="text-lg font-bold">Araç (Çekici) Seçimi</h2>
                  <p className="text-xs text-white/50">Evrak paketine eklenecek çekiciyi seçin</p>
                </div>
              </div>

              <select
                value={selectedTruckId}
                onChange={(e) => {
                  setSelectedTruckId(e.target.value);
                  setSelectedVehicleDocs(new Set());
                }}
                className="mb-4 h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-blue-500/50 [&>option]:bg-[#0B1220] [&>option]:text-white"
              >
                <option value="" className="bg-[#0B1220] text-white/60">Çekici seçin...</option>
                {trucks.map((t) => (
                  <option key={t.id} value={t.id} className="bg-[#0B1220] text-white">
                    {t.plate} {t.category === "supplier" ? "(Tedarikçi)" : "(Asmira)"}
                  </option>
                ))}
              </select>

              {selectedTruck && (
                <div className="space-y-4">
                  {/* Araç Evrakları */}
                  <div>
                    <div className="mb-3 flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <Truck className="h-3.5 w-3.5 text-blue-400" />
                        <span className="text-xs font-semibold text-white/60">ARAÇ EVRAKLARI ({selectedTruck.plate})</span>
                      </div>
                      <button
                        type="button"
                        onClick={selectAllVehicleDocs}
                        className="text-xs font-medium text-blue-400 hover:text-blue-300"
                      >
                        Tümünü Seç
                      </button>
                    </div>
                    <div className="space-y-2">
                      {selectedTruck.documents.map((doc: DocumentInfo) => {
                        const isUploaded = !!doc.fileName;
                        const isSelected = selectedVehicleDocs.has(doc.type);
                        
                        return (
                          <button
                            key={doc.type}
                            type="button"
                            onClick={() => isUploaded && toggleVehicleDoc(doc.type)}
                            disabled={!isUploaded}
                            className={cn(
                              "flex w-full items-center gap-3 rounded-lg border p-3 text-left transition-all",
                              isUploaded
                                ? isSelected
                                  ? "border-blue-500/50 bg-blue-500/10"
                                  : "border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/[0.07]"
                                : "border-white/5 bg-white/[0.02] opacity-50 cursor-not-allowed"
                            )}
                          >
                            <div className={cn(
                              "flex h-5 w-5 items-center justify-center rounded border transition-all",
                              isSelected
                                ? "border-blue-500 bg-blue-500"
                                : "border-white/30"
                            )}>
                              {isSelected && <Check className="h-3 w-3 text-white" />}
                            </div>
                            <div className="flex-1">
                              <div className="text-sm font-medium">{doc.label}</div>
                              {isUploaded ? (
                                <div className="text-xs text-white/40">{doc.fileName}</div>
                              ) : (
                                <div className="flex items-center gap-1 text-xs text-amber-400/70">
                                  <AlertCircle className="h-3 w-3" />
                                  Yüklenmemiş
                                </div>
                              )}
                            </div>
                          </button>
                        );
                      })}
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* Dorse Seçimi */}
            <div className="rounded-xl border border-cyan-500/20 bg-gradient-to-br from-white/[0.04] to-transparent p-5">
              <div className="mb-4 flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500/25 to-cyan-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                  <Container className="h-5 w-5 text-cyan-400" />
                </div>
                <div>
                  <h2 className="text-lg font-bold">Dorse Seçimi</h2>
                  <p className="text-xs text-white/50">Evrak paketine eklenecek dorseyi seçin</p>
                </div>
              </div>

              <select
                value={selectedTrailerId}
                onChange={(e) => {
                  setSelectedTrailerId(e.target.value);
                  setSelectedTrailerDocs(new Set());
                }}
                className="mb-4 h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-cyan-500/50 [&>option]:bg-[#0B1220] [&>option]:text-white"
              >
                <option value="" className="bg-[#0B1220] text-white/60">Dorse seçin...</option>
                {trailers.map((t) => (
                  <option key={t.id} value={t.id} className="bg-[#0B1220] text-white">
                    {t.plate} {t.category === "supplier" ? "(Tedarikçi)" : "(Asmira)"}
                  </option>
                ))}
              </select>

              {selectedTrailer && (
                <div className="space-y-4">
                  {/* Dorse Evrakları */}
                  <div>
                    <div className="mb-3 flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <Container className="h-3.5 w-3.5 text-cyan-400" />
                        <span className="text-xs font-semibold text-white/60">DORSE EVRAKLARI ({selectedTrailer.plate})</span>
                      </div>
                      <button
                        type="button"
                        onClick={selectAllTrailerDocs}
                        className="text-xs font-medium text-cyan-400 hover:text-cyan-300"
                      >
                        Tümünü Seç
                      </button>
                    </div>
                    <div className="space-y-2">
                      {selectedTrailer.documents.map((doc: DocumentInfo) => {
                        const isUploaded = !!doc.fileName;
                        const isSelected = selectedTrailerDocs.has(doc.type);
                        
                        return (
                          <button
                            key={doc.type}
                            type="button"
                            onClick={() => isUploaded && toggleTrailerDoc(doc.type)}
                            disabled={!isUploaded}
                            className={cn(
                              "flex w-full items-center gap-3 rounded-lg border p-3 text-left transition-all",
                              isUploaded
                                ? isSelected
                                  ? "border-cyan-500/50 bg-cyan-500/10"
                                  : "border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/[0.07]"
                                : "border-white/5 bg-white/[0.02] opacity-50 cursor-not-allowed"
                            )}
                          >
                            <div className={cn(
                              "flex h-5 w-5 items-center justify-center rounded border transition-all",
                              isSelected
                                ? "border-cyan-500 bg-cyan-500"
                                : "border-white/30"
                            )}>
                              {isSelected && <Check className="h-3 w-3 text-white" />}
                            </div>
                            <div className="flex-1">
                              <div className="text-sm font-medium">{doc.label}</div>
                              {isUploaded ? (
                                <div className="text-xs text-white/40">{doc.fileName}</div>
                              ) : (
                                <div className="flex items-center gap-1 text-xs text-amber-400/70">
                                  <AlertCircle className="h-3 w-3" />
                                  Yüklenmemiş
                                </div>
                              )}
                            </div>
                          </button>
                        );
                      })}
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* Driver Selection */}
            <div ref={driverSectionRef} className="rounded-xl border border-purple-500/20 bg-gradient-to-br from-white/[0.04] to-transparent p-5">
              <div className="mb-4 flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/25 to-purple-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                  <UserCheck className="h-5 w-5 text-purple-400" />
                </div>
                <div>
                  <h2 className="text-lg font-bold">Şoför Seçimi</h2>
                  <p className="text-xs text-white/50">Evrak paketine eklenecek şoförü seçin</p>
                </div>
              </div>

              {/* Custom Dropdown for Driver */}
              <div ref={driverDropdownRef} className="relative mb-4">
                <button
                  type="button"
                  onClick={() => {
                    const newState = !isDriverDropdownOpen;
                    setIsDriverDropdownOpen(newState);
                    if (newState) {
                      setTimeout(() => {
                        driverDropdownRef.current?.scrollIntoView({ behavior: "smooth", block: "start" });
                      }, 50);
                    }
                  }}
                  className="flex h-11 w-full items-center justify-between rounded-lg border border-white/10 bg-[#0B1220] px-3 text-sm outline-none transition hover:border-purple-500/30 focus:border-purple-500/50"
                >
                  <span className={selectedDriverId ? "text-white" : "text-white/60"}>
                    {selectedDriver ? `${selectedDriver.name} (${selectedDriver.tcNo})` : "Şoför seçin..."}
                  </span>
                  <ChevronDown className={cn("h-4 w-4 text-white/50 transition-transform", isDriverDropdownOpen && "rotate-180")} />
                </button>
                
                {isDriverDropdownOpen && (
                  <>
                    {/* Backdrop to close dropdown */}
                    <div 
                      className="fixed inset-0 z-10" 
                      onClick={() => setIsDriverDropdownOpen(false)}
                    />
                    {/* Dropdown menu - opens downward */}
                    <div className="absolute left-0 right-0 top-full z-20 mt-1 max-h-48 overflow-y-auto rounded-lg border border-white/10 bg-[#0B1220] py-1 shadow-xl">
                      <button
                        type="button"
                        onClick={() => {
                          setSelectedDriverId("");
                          setSelectedDriverDocs(new Set());
                          setIsDriverDropdownOpen(false);
                        }}
                        className="w-full px-3 py-2 text-left text-sm text-white/60 hover:bg-white/10"
                      >
                        Şoför seçin...
                      </button>
                      {drivers.map((d) => (
                        <button
                          key={d.id}
                          type="button"
                          onClick={() => {
                            setSelectedDriverId(d.id);
                            setSelectedDriverDocs(new Set());
                            setIsDriverDropdownOpen(false);
                          }}
                          className={cn(
                            "w-full px-3 py-2 text-left text-sm hover:bg-white/10",
                            selectedDriverId === d.id ? "bg-purple-500/20 text-purple-300" : "text-white"
                          )}
                        >
                          {d.name} ({d.tcNo})
                        </button>
                      ))}
                    </div>
                  </>
                )}
              </div>

              {selectedDriver && (
                <>
                  <div className="mb-3 flex items-center justify-between">
                    <span className="text-xs font-semibold text-white/60">ŞOFÖR EVRAKLARI</span>
                    <button
                      type="button"
                      onClick={selectAllDriverDocs}
                      className="text-xs font-medium text-purple-400 hover:text-purple-300"
                    >
                      Tümünü Seç
                    </button>
                  </div>
                  <div className="space-y-2">
                    {selectedDriver.documents.map((doc) => {
                      const isUploaded = !!doc.fileName;
                      const isSelected = selectedDriverDocs.has(doc.type);
                      
                      return (
                        <button
                          key={doc.type}
                          type="button"
                          onClick={() => isUploaded && toggleDriverDoc(doc.type)}
                          disabled={!isUploaded}
                          className={cn(
                            "flex w-full items-center gap-3 rounded-lg border p-3 text-left transition-all",
                            isUploaded
                              ? isSelected
                                ? "border-purple-500/50 bg-purple-500/10"
                                : "border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/[0.07]"
                              : "border-white/5 bg-white/[0.02] opacity-50 cursor-not-allowed"
                          )}
                        >
                          <div className={cn(
                            "flex h-5 w-5 items-center justify-center rounded border transition-all",
                            isSelected
                              ? "border-purple-500 bg-purple-500"
                              : "border-white/30"
                          )}>
                            {isSelected && <Check className="h-3 w-3 text-white" />}
                          </div>
                          <div className="flex-1">
                            <div className="text-sm font-medium">{doc.label}</div>
                            {isUploaded ? (
                              <div className="text-xs text-white/40">{doc.fileName}</div>
                            ) : (
                              <div className="flex items-center gap-1 text-xs text-amber-400/70">
                                <AlertCircle className="h-3 w-3" />
                                Yüklenmemiş
                              </div>
                            )}
                          </div>
                        </button>
                      );
                    })}
                  </div>
                </>
              )}
            </div>
          </div>

          {/* Summary */}
          {totalSelected > 0 && (
            <div className="mt-6 rounded-xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/5 to-transparent p-5">
              <div className="mb-4 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/25 to-emerald-600/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                    <FileText className="h-5 w-5 text-emerald-400" />
                  </div>
                  <div>
                    <h2 className="text-lg font-bold">Seçilen Evraklar</h2>
                    <p className="text-xs text-white/50">PDF&apos;e dahil edilecek evraklar</p>
                  </div>
                </div>
                <button
                  type="button"
                  onClick={clearAllSelections}
                  className="text-xs font-medium text-red-400 hover:text-red-300"
                >
                  Seçimi Temizle
                </button>
              </div>

              <div className="flex flex-wrap gap-2">
                {/* Araç evrakları */}
                {selectedTruck && Array.from(selectedVehicleDocs).map((docType) => {
                  const doc = selectedTruck.documents.find((d: DocumentInfo) => d.type === docType);
                  return doc ? (
                    <div
                      key={`v-${docType}`}
                      className="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1.5"
                    >
                      <Truck className="h-3 w-3 text-blue-400" />
                      <span className="text-xs font-medium text-blue-300">{doc.label}</span>
                    </div>
                  ) : null;
                })}
                {/* Dorse evrakları */}
                {selectedTrailer && Array.from(selectedTrailerDocs).map((docType) => {
                  const doc = selectedTrailer.documents.find((d: DocumentInfo) => d.type === docType);
                  return doc ? (
                    <div
                      key={`t-${docType}`}
                      className="inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1.5"
                    >
                      <Container className="h-3 w-3 text-cyan-400" />
                      <span className="text-xs font-medium text-cyan-300">{doc.label}</span>
                    </div>
                  ) : null;
                })}
                {/* Şoför evrakları */}
                {selectedDriver && Array.from(selectedDriverDocs).map((docType) => {
                  const doc = selectedDriver.documents.find((d: DocumentInfo) => d.type === docType);
                  return doc ? (
                    <div
                      key={`d-${docType}`}
                      className="inline-flex items-center gap-2 rounded-full border border-purple-500/30 bg-purple-500/10 px-3 py-1.5"
                    >
                      <UserCheck className="h-3 w-3 text-purple-400" />
                      <span className="text-xs font-medium text-purple-300">{doc.label}</span>
                    </div>
                  ) : null;
                })}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
