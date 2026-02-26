"use client";

import { Plus, FolderOpen, Download, X, Upload, Eye, Trash2, CheckCircle, AlertTriangle, Truck, Edit3, ChevronDown, ChevronRight, Building2 } from "lucide-react";
import { useState, useRef, useEffect } from "react";
import { PDFDocument } from "pdf-lib";
import { saveAs } from "file-saver";
import { cn } from "@/lib/utils/cn";

const STORAGE_KEY = "asmira-supplier-companies";

type DocumentType = 
  | "ruhsat"
  | "tasitKarti"
  | "t9Adr"
  | "trafikSigortasi"
  | "tehlikeliMaddeSigortasi"
  | "kasko"
  | "tuvturk"
  | "egzozEmisyon"
  | "sayacKalibrasyon"
  | "takografKalibrasyon"
  | "faaliyetBelgesi"
  | "yetkiBelgesi"
  | "hortumBasin"
  | "tankMuayeneSertifikasi"
  | "vergiLevhasi";

type DocumentInfo = {
  type: DocumentType;
  label: string;
  fileName: string | null;
  fileUrl: string | null;
  fileBlob: Blob | null;
  expiryDate: string | null;
};

type Vehicle = {
  id: string;
  vehiclePlate: string;
  trailerPlate: string;
  documents: DocumentInfo[];
};

type SupplierCompany = {
  id: string;
  name: string;
  vehicles: Vehicle[];
  isExpanded: boolean;
};

const documentLabels: Record<DocumentType, string> = {
  ruhsat: "Ruhsat",
  tasitKarti: "Taşıt Kartı",
  t9Adr: "T9 ADR",
  trafikSigortasi: "Trafik Sigortası",
  tehlikeliMaddeSigortasi: "Tehlikeli Madde Sigortası",
  kasko: "Kasko",
  tuvturk: "TÜVTÜRK",
  egzozEmisyon: "Egzoz Emisyon",
  sayacKalibrasyon: "Sayaç Kalibrasyon",
  takografKalibrasyon: "Takograf Kalibrasyon",
  faaliyetBelgesi: "Faaliyet Belgesi",
  yetkiBelgesi: "Yetki Belgesi",
  hortumBasin: "Hortum Basın.",
  tankMuayeneSertifikasi: "Tank Muayene Sertifikası",
  vergiLevhasi: "Vergi Levhası",
};

const defaultDocuments = (): DocumentInfo[] => [
  { type: "ruhsat", label: documentLabels.ruhsat, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tasitKarti", label: documentLabels.tasitKarti, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "t9Adr", label: documentLabels.t9Adr, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "trafikSigortasi", label: documentLabels.trafikSigortasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tehlikeliMaddeSigortasi", label: documentLabels.tehlikeliMaddeSigortasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "kasko", label: documentLabels.kasko, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tuvturk", label: documentLabels.tuvturk, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "egzozEmisyon", label: documentLabels.egzozEmisyon, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "sayacKalibrasyon", label: documentLabels.sayacKalibrasyon, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "takografKalibrasyon", label: documentLabels.takografKalibrasyon, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "faaliyetBelgesi", label: documentLabels.faaliyetBelgesi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "yetkiBelgesi", label: documentLabels.yetkiBelgesi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "hortumBasin", label: documentLabels.hortumBasin, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tankMuayeneSertifikasi", label: documentLabels.tankMuayeneSertifikasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "vergiLevhasi", label: documentLabels.vergiLevhasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
];

const initialCompanies: SupplierCompany[] = [
  {
    id: "company_karaburun",
    name: "KARABURUN NAKLİYAT",
    isExpanded: true,
    vehicles: [
      { id: "v_kb_1", vehiclePlate: "41 KB 001", trailerPlate: "41 KBD 001", documents: defaultDocuments() },
      { id: "v_kb_2", vehiclePlate: "41 KB 002", trailerPlate: "41 KBD 002", documents: defaultDocuments() },
      { id: "v_kb_3", vehiclePlate: "41 KB 003", trailerPlate: "41 KBD 003", documents: defaultDocuments() },
    ],
  },
  {
    id: "company_ozturk",
    name: "ÖZTÜRK PETROL TAŞIMACILIĞI",
    isExpanded: false,
    vehicles: [
      { id: "v_oz_1", vehiclePlate: "34 OZT 100", trailerPlate: "34 OZD 100", documents: defaultDocuments() },
      { id: "v_oz_2", vehiclePlate: "34 OZT 101", trailerPlate: "34 OZD 101", documents: defaultDocuments() },
    ],
  },
  {
    id: "company_marmara",
    name: "MARMARA LOJİSTİK",
    isExpanded: false,
    vehicles: [
      { id: "v_mr_1", vehiclePlate: "16 MRL 500", trailerPlate: "16 MRD 500", documents: defaultDocuments() },
    ],
  },
];

function isExpired(dateStr: string | null): boolean {
  if (!dateStr) return false;
  return new Date(dateStr) < new Date();
}

function getDaysLeft(dateStr: string | null): number | null {
  if (!dateStr) return null;
  const today = new Date();
  const expiryDate = new Date(dateStr);
  const diffTime = expiryDate.getTime() - today.getTime();
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

function getExpiryColor(dateStr: string | null): { border: string; bg: string; badge: string; badgeText: string } {
  const daysLeft = getDaysLeft(dateStr);
  if (daysLeft === null) {
    return { border: "border-white/10", bg: "bg-white/5", badge: "", badgeText: "" };
  }
  if (daysLeft < 0) {
    return { border: "border-red-500/50", bg: "bg-red-500/10", badge: "bg-red-500/20", badgeText: "text-red-300" };
  }
  if (daysLeft <= 7) {
    return { border: "border-amber-500/50", bg: "bg-amber-500/10", badge: "bg-amber-500/20", badgeText: "text-amber-300" };
  }
  if (daysLeft <= 15) {
    return { border: "border-yellow-500/30", bg: "bg-yellow-500/5", badge: "bg-yellow-500/20", badgeText: "text-yellow-300" };
  }
  return { border: "border-emerald-500/30", bg: "bg-emerald-500/5", badge: "bg-emerald-500/20", badgeText: "text-emerald-300" };
}

function countUploaded(docs: DocumentInfo[]): number {
  return docs.filter((d) => d.fileName !== null).length;
}

export default function SupplierVehicleDocumentsPage() {
  const [companies, setCompanies] = useState<SupplierCompany[]>(() => {
    if (typeof window !== "undefined") {
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) {
        try {
          return JSON.parse(saved);
        } catch {
          return initialCompanies;
        }
      }
    }
    return initialCompanies;
  });
  const [editingId, setEditingId] = useState<string | null>(null);

  // Persist to localStorage
  useEffect(() => {
    // Don't save fileBlob as it can't be serialized
    const toSave = companies.map((c) => ({
      ...c,
      vehicles: c.vehicles.map((v) => ({
        ...v,
        documents: v.documents.map((d) => ({
          ...d,
          fileBlob: null, // Can't serialize Blob
          fileUrl: null, // URLs are temporary
        })),
      })),
    }));
    localStorage.setItem(STORAGE_KEY, JSON.stringify(toSave));
  }, [companies]);
  const [draft, setDraft] = useState<{ vehiclePlate: string; trailerPlate: string } | null>(null);
  const [panelVehicle, setPanelVehicle] = useState<{ companyId: string; vehicle: Vehicle } | null>(null);
  const fileInputRefs = useRef<Record<string, HTMLInputElement | null>>({});

  const [showNewVehicleModal, setShowNewVehicleModal] = useState(false);
  const [newVehicleCompanyId, setNewVehicleCompanyId] = useState<string | null>(null);
  const [newVehiclePlate, setNewVehiclePlate] = useState("");
  const [newTrailerPlate, setNewTrailerPlate] = useState("");

  const [showNewCompanyModal, setShowNewCompanyModal] = useState(false);
  const [newCompanyName, setNewCompanyName] = useState("");

  function toggleCompany(companyId: string) {
    setCompanies((prev) =>
      prev.map((c) => (c.id === companyId ? { ...c, isExpanded: !c.isExpanded } : c))
    );
  }

  function startEdit(vehicle: Vehicle) {
    setEditingId(vehicle.id);
    setDraft({ vehiclePlate: vehicle.vehiclePlate, trailerPlate: vehicle.trailerPlate });
  }

  function cancelEdit() {
    setEditingId(null);
    setDraft(null);
  }

  function saveEdit(companyId: string) {
    if (!editingId || !draft) return;
    setCompanies((prev) =>
      prev.map((c) => {
        if (c.id !== companyId) return c;
        return {
          ...c,
          vehicles: c.vehicles.map((v) =>
            v.id === editingId
              ? { ...v, vehiclePlate: draft.vehiclePlate, trailerPlate: draft.trailerPlate }
              : v
          ),
        };
      })
    );
    setEditingId(null);
    setDraft(null);
  }

  function openPanel(companyId: string, vehicle: Vehicle) {
    setPanelVehicle({ companyId, vehicle });
  }

  function closePanel() {
    setPanelVehicle(null);
  }

  function handleFileUpload(companyId: string, vehicleId: string, docType: DocumentType, file: File) {
    const fileUrl = URL.createObjectURL(file);
    setCompanies((prev) =>
      prev.map((c) => {
        if (c.id !== companyId) return c;
        return {
          ...c,
          vehicles: c.vehicles.map((v) => {
            if (v.id !== vehicleId) return v;
            return {
              ...v,
              documents: v.documents.map((d) =>
                d.type === docType
                  ? { ...d, fileName: file.name, fileUrl, fileBlob: file, expiryDate: d.expiryDate ?? "2026-12-31" }
                  : d
              ),
            };
          }),
        };
      })
    );
    // Update panel vehicle if open
    if (panelVehicle && panelVehicle.companyId === companyId && panelVehicle.vehicle.id === vehicleId) {
      setPanelVehicle({
        ...panelVehicle,
        vehicle: {
          ...panelVehicle.vehicle,
          documents: panelVehicle.vehicle.documents.map((d) =>
            d.type === docType
              ? { ...d, fileName: file.name, fileUrl, fileBlob: file, expiryDate: d.expiryDate ?? "2026-12-31" }
              : d
          ),
        },
      });
    }
  }

  function handleDeleteDocument(companyId: string, vehicleId: string, docType: DocumentType) {
    setCompanies((prev) =>
      prev.map((c) => {
        if (c.id !== companyId) return c;
        return {
          ...c,
          vehicles: c.vehicles.map((v) => {
            if (v.id !== vehicleId) return v;
            return {
              ...v,
              documents: v.documents.map((d) =>
                d.type === docType ? { ...d, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null } : d
              ),
            };
          }),
        };
      })
    );
    // Update panel vehicle if open
    if (panelVehicle && panelVehicle.companyId === companyId && panelVehicle.vehicle.id === vehicleId) {
      setPanelVehicle({
        ...panelVehicle,
        vehicle: {
          ...panelVehicle.vehicle,
          documents: panelVehicle.vehicle.documents.map((d) =>
            d.type === docType ? { ...d, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null } : d
          ),
        },
      });
    }
  }

  function handleUpdateDocument(companyId: string, vehicleId: string, docType: DocumentType, updates: Partial<DocumentInfo>) {
    setCompanies((prev) =>
      prev.map((c) => {
        if (c.id !== companyId) return c;
        return {
          ...c,
          vehicles: c.vehicles.map((v) => {
            if (v.id !== vehicleId) return v;
            return {
              ...v,
              documents: v.documents.map((d) =>
                d.type === docType ? { ...d, ...updates } : d
              ),
            };
          }),
        };
      })
    );
    // Update panel vehicle if open
    if (panelVehicle && panelVehicle.companyId === companyId && panelVehicle.vehicle.id === vehicleId) {
      setPanelVehicle({
        ...panelVehicle,
        vehicle: {
          ...panelVehicle.vehicle,
          documents: panelVehicle.vehicle.documents.map((d) =>
            d.type === docType ? { ...d, ...updates } : d
          ),
        },
      });
    }
  }

  async function handleDownloadAll(vehicle: Vehicle) {
    const uploaded = vehicle.documents.filter((d) => d.fileName && d.fileBlob);
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

  function handleDeleteVehicle(companyId: string, vehicleId: string, vehiclePlate: string) {
    if (confirm(`"${vehiclePlate}" kaydını silmek istediğinize emin misiniz?`)) {
      setCompanies((prev) =>
        prev.map((c) => {
          if (c.id !== companyId) return c;
          return { ...c, vehicles: c.vehicles.filter((v) => v.id !== vehicleId) };
        })
      );
    }
  }

  function handleDeleteCompany(companyId: string, companyName: string) {
    if (confirm(`"${companyName}" firmasını ve tüm araçlarını silmek istediğinize emin misiniz?`)) {
      setCompanies((prev) => prev.filter((c) => c.id !== companyId));
    }
  }

  function openNewVehicleModal(companyId: string) {
    setNewVehicleCompanyId(companyId);
    setNewVehiclePlate("");
    setNewTrailerPlate("");
    setShowNewVehicleModal(true);
  }

  function closeNewVehicleModal() {
    setShowNewVehicleModal(false);
    setNewVehicleCompanyId(null);
  }

  function handleSaveNewVehicle() {
    if (!newVehiclePlate.trim() || !newTrailerPlate.trim() || !newVehicleCompanyId) {
      alert("Lütfen her iki plakayı da girin.");
      return;
    }
    const newVehicle: Vehicle = {
      id: `v_${Date.now()}`,
      vehiclePlate: newVehiclePlate.trim(),
      trailerPlate: newTrailerPlate.trim(),
      documents: defaultDocuments(),
    };
    setCompanies((prev) =>
      prev.map((c) => {
        if (c.id !== newVehicleCompanyId) return c;
        return { ...c, vehicles: [newVehicle, ...c.vehicles] };
      })
    );
    closeNewVehicleModal();
  }

  function openNewCompanyModal() {
    setNewCompanyName("");
    setShowNewCompanyModal(true);
  }

  function closeNewCompanyModal() {
    setShowNewCompanyModal(false);
  }

  function handleSaveNewCompany() {
    if (!newCompanyName.trim()) {
      alert("Lütfen firma adını girin.");
      return;
    }
    const newCompany: SupplierCompany = {
      id: `company_${Date.now()}`,
      name: newCompanyName.trim().toUpperCase(),
      isExpanded: true,
      vehicles: [],
    };
    setCompanies((prev) => [newCompany, ...prev]);
    closeNewCompanyModal();
  }

  const totalCompanies = companies.length;
  const totalVehicles = companies.reduce((acc, c) => acc + c.vehicles.length, 0);
  const _completeVehicles = companies.reduce(
    (acc, c) => acc + c.vehicles.filter((v) => countUploaded(v.documents) === v.documents.length).length,
    0
  );
  void _completeVehicles;

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-orange-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              ARAÇ EVRAKLARI
            </div>
            <div className="text-3xl font-black tracking-tight">Tedarikçi Araçları</div>
          </div>

          <button
            type="button"
            onClick={openNewCompanyModal}
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600"
          >
            <Plus className="h-4 w-4" />
            Yeni Firma Ekle
          </button>
        </div>

        {/* Stats Bar */}
        <div className="relative flex flex-none items-center gap-3 px-6 py-2.5">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/40 via-orange-400/20 to-transparent" />
          
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <div className="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.6)]" />
            <span className="text-xs font-medium text-white/70">Toplam Firma</span>
            <span className="text-sm font-bold text-white">{totalCompanies}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
            <Truck className="h-3.5 w-3.5 text-orange-400" />
            <span className="text-xs font-medium text-white/70">Toplam Araç</span>
            <span className="text-sm font-bold text-white">{totalVehicles}</span>
          </div>
        </div>

        {/* Companies List */}
        <div className="flex-1 overflow-y-auto p-6">
          <div className="space-y-4">
            {companies.map((company) => (
              <div
                key={company.id}
                className="overflow-hidden rounded-xl border border-orange-500/20 bg-gradient-to-br from-white/[0.03] to-transparent"
              >
                {/* Company Header */}
                <div className="relative flex items-center justify-between bg-gradient-to-r from-orange-500/10 to-transparent px-4 py-3">
                  <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/30 to-transparent" />
                  <button
                    type="button"
                    onClick={() => toggleCompany(company.id)}
                    className="flex items-center gap-3 text-left"
                  >
                    <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500/25 to-orange-600/10 text-orange-400 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
                      <Building2 className="h-4 w-4" />
                    </div>
                    <div>
                      <div className="font-bold tracking-tight">{company.name}</div>
                      <div className="text-xs text-white/50">{company.vehicles.length} araç</div>
                    </div>
                    {company.isExpanded ? (
                      <ChevronDown className="h-4 w-4 text-orange-400/60" />
                    ) : (
                      <ChevronRight className="h-4 w-4 text-orange-400/60" />
                    )}
                  </button>
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => openNewVehicleModal(company.id)}
                      className="inline-flex h-8 items-center gap-1.5 rounded-lg bg-orange-500/10 border border-orange-500/20 px-3 text-xs font-medium text-orange-300 transition hover:bg-orange-500/20"
                    >
                      <Plus className="h-3.5 w-3.5" />
                      Araç Ekle
                    </button>
                    <button
                      type="button"
                      onClick={() => handleDeleteCompany(company.id, company.name)}
                      className="flex h-8 w-8 items-center justify-center rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 transition hover:bg-red-500/20"
                      title="Firmayı Sil"
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                    </button>
                  </div>
                </div>

                {/* Vehicles Grid */}
                {company.isExpanded && (
                  <div className="p-4">
                    {company.vehicles.length === 0 ? (
                      <div className="py-8 text-center text-sm text-white/40">
                        Bu firmaya ait araç bulunmuyor. Yukarıdaki &quot;Araç Ekle&quot; butonunu kullanın.
                      </div>
                    ) : (
                      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        {company.vehicles.map((v) => {
                          const uploaded = countUploaded(v.documents);
                          const total = v.documents.length;
                          const isComplete = uploaded === total;
                          const progress = (uploaded / total) * 100;

                          return (
                            <div
                              key={v.id}
                              className={cn(
                                "group relative flex flex-col rounded-xl border bg-gradient-to-br from-white/[0.04] to-transparent p-4 backdrop-blur-sm transition-all hover:bg-white/[0.06]",
                                isComplete
                                  ? "border-emerald-500/40 shadow-[0_0_25px_rgba(52,211,153,0.15)]"
                                  : "border-orange-500/20 shadow-[0_4px_20px_rgba(0,0,0,0.2)]"
                              )}
                            >
                              <div className="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-orange-500/10 blur-2xl transition-opacity group-hover:opacity-100 opacity-0" />

                              {/* Delete button */}
                              {editingId !== v.id && (
                                <button
                                  type="button"
                                  onClick={() => handleDeleteVehicle(company.id, v.id, v.vehiclePlate)}
                                  className="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-md border border-red-500/30 bg-red-500/10 text-red-400 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/20"
                                  title="Kaydı Sil"
                                >
                                  <Trash2 className="h-3.5 w-3.5" />
                                </button>
                              )}

                              {/* Vehicle Icon & Plates */}
                              <div className="mb-4 flex items-start gap-3">
                                <div className={cn(
                                  "flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]",
                                  isComplete 
                                    ? "from-emerald-500/25 to-emerald-600/10 text-emerald-400" 
                                    : "from-orange-500/25 to-orange-600/10 text-orange-400"
                                )}>
                                  <Truck className="h-5 w-5" />
                                </div>
                                <div className="min-w-0 flex-1">
                                  {editingId === v.id ? (
                                    <div className="space-y-2">
                                      <input
                                        value={draft?.vehiclePlate ?? ""}
                                        onChange={(e) =>
                                          setDraft((p) => ({
                                            vehiclePlate: e.target.value,
                                            trailerPlate: p?.trailerPlate ?? "",
                                          }))
                                        }
                                        className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm font-semibold outline-none focus:border-orange-500"
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
                                        className="h-9 w-full rounded-md border border-white/20 bg-white/10 px-3 text-sm outline-none focus:border-orange-500"
                                        placeholder="Dorse Plaka"
                                      />
                                    </div>
                                  ) : (
                                    <>
                                      <div className="truncate text-[15px] font-bold tracking-tight">{v.vehiclePlate}</div>
                                      <div className="truncate text-xs text-white/50">{v.trailerPlate}</div>
                                    </>
                                  )}
                                </div>
                              </div>

                              {/* Progress Bar */}
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

                              {/* Status Badge */}
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

                              {/* Actions */}
                              <div className="mt-auto flex items-center gap-2 border-t border-white/10 pt-3">
                                {editingId === v.id ? (
                                  <>
                                    <button
                                      type="button"
                                      onClick={() => saveEdit(company.id)}
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
                                      onClick={() => openPanel(company.id, v)}
                                      className="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-white/10 py-2 text-xs font-medium text-white transition hover:bg-white/15"
                                    >
                                      <FolderOpen className="h-3.5 w-3.5" />
                                      Evraklar
                                    </button>
                                    <button
                                      type="button"
                                      onClick={() => startEdit(v)}
                                      className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                                      title="Düzenle"
                                    >
                                      <Edit3 className="h-3.5 w-3.5" />
                                    </button>
                                    <button
                                      type="button"
                                      onClick={() => handleDownloadAll(v)}
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
                    )}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Document Panel */}
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
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">EVRAK YÖNETİMİ</div>
                <div className="text-lg font-bold">{panelVehicle.vehicle.vehiclePlate}</div>
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
                {panelVehicle.vehicle.documents.map((doc) => {
                  const _expired = isExpired(doc.expiryDate);
                  void _expired;
                  const daysLeft = getDaysLeft(doc.expiryDate);
                  const colors = getExpiryColor(doc.expiryDate);
                  return (
                    <div
                      key={doc.type}
                      className={`rounded-lg border p-4 ${colors.border} ${colors.bg}`}
                    >
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                          {doc.fileName ? (
                            <CheckCircle className="h-4 w-4 text-emerald-400" />
                          ) : (
                            <div className="h-4 w-4 rounded-full border-2 border-white/30" />
                          )}
                          <span className="font-medium">{doc.label}</span>
                          {daysLeft !== null && (
                            <span className={`ml-2 rounded px-1.5 py-0.5 text-[10px] font-semibold ${colors.badge} ${colors.badgeText}`}>
                              {daysLeft < 0
                                ? "SÜRESİ GEÇMİŞ"
                                : daysLeft === 0
                                ? "BUGÜN"
                                : `${daysLeft} GÜN`}
                            </span>
                          )}
                        </div>
                      </div>

                      {doc.fileName ? (
                        <div className="mt-3 flex items-center justify-between rounded-md border border-white/10 bg-white/5 px-3 py-2">
                          <span className="truncate text-sm text-white/70">{doc.fileName}</span>
                          <div className="flex items-center gap-2">
                            <a
                              href={doc.fileUrl ?? "#"}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-white/10"
                              title="Önizle"
                            >
                              <Eye className="h-4 w-4" />
                            </a>
                            <a
                              href={doc.fileUrl ?? "#"}
                              download={doc.fileName}
                              className="inline-flex h-7 w-7 items-center justify-center rounded-md text-sky-400 hover:bg-sky-500/20"
                              title="İndir"
                            >
                              <Download className="h-4 w-4" />
                            </a>
                            <button
                              type="button"
                              onClick={() => handleDeleteDocument(panelVehicle.companyId, panelVehicle.vehicle.id, doc.type)}
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
                              if (file) handleFileUpload(panelVehicle.companyId, panelVehicle.vehicle.id, doc.type, file);
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
                          value={doc.expiryDate || ""}
                          onChange={(e) => {
                            const newDate = e.target.value || null;
                            handleUpdateDocument(panelVehicle.companyId, panelVehicle.vehicle.id, doc.type, { expiryDate: newDate });
                          }}
                          className="h-8 rounded-md border border-white/10 bg-white/5 px-2 text-xs text-white outline-none focus:border-orange-500/50"
                        />
                        {doc.expiryDate && (
                          <button
                            type="button"
                            onClick={() => handleUpdateDocument(panelVehicle.companyId, panelVehicle.vehicle.id, doc.type, { expiryDate: null })}
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
                onClick={() => handleDownloadAll(panelVehicle.vehicle)}
                className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 py-3 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600"
              >
                <Download className="h-4 w-4" />
                Tüm Evrakları İndir (PDF)
              </button>
            </div>
          </div>
        </div>
      )}

      {/* New Vehicle Modal */}
      {showNewVehicleModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closeNewVehicleModal}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">YENİ ARAÇ</div>
                <div className="text-lg font-bold">Araç/Dorse Tanımla</div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={closeNewVehicleModal}
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
                  placeholder="Örn: 34 ABC 123"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"
                />
              </div>
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Dorse Plaka</label>
                <input
                  type="text"
                  value={newTrailerPlate}
                  onChange={(e) => setNewTrailerPlate(e.target.value)}
                  placeholder="Örn: 34 ABD 123"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
              <button
                type="button"
                onClick={closeNewVehicleModal}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleSaveNewVehicle}
                className="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600"
              >
                Kaydet
              </button>
            </div>
          </div>
        </div>
      )}

      {/* New Company Modal */}
      {showNewCompanyModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={closeNewCompanyModal}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-orange-500/60 via-orange-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">YENİ FİRMA</div>
                <div className="text-lg font-bold">Tedarikçi Firma Ekle</div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={closeNewCompanyModal}
                aria-label="Kapat"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="space-y-4 px-5 py-5">
              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Firma Adı</label>
                <input
                  type="text"
                  value={newCompanyName}
                  onChange={(e) => setNewCompanyName(e.target.value)}
                  placeholder="Örn: KARABURUN NAKLİYAT"
                  className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-orange-500/50"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
              <button
                type="button"
                onClick={closeNewCompanyModal}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleSaveNewCompany}
                className="rounded-lg bg-gradient-to-br from-orange-600 to-orange-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(249,115,22,0.25)] transition-all hover:from-orange-500 hover:to-orange-600"
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
