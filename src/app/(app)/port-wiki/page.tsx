"use client";

import { useState } from "react";
import {
  Search,
  Anchor,
  ShieldCheck,
  Ban,
  Phone,
  FileText,
  Copy,
  Check,
  X,
  AlertTriangle,
  MapPin,
  Edit3,
  Save,
  Plus,
  Trash2,
} from "lucide-react";
import { cn } from "@/lib/utils/cn";

type PortData = {
  id: string;
  name: string;
  city: string;
  region: string;
  criticalWarning?: string;
  documents: string[];
  technicalData: {
    maxDraft?: string;
    vhfChannel?: string;
    workingHours?: string;
    anchorage?: string;
  };
  contacts: {
    name: string;
    phone: string;
    role: string;
  }[];
  notes?: string[];
};

const portsData: PortData[] = [
  {
    id: "alsancak",
    name: "Alsancak Limanı",
    city: "İzmir",
    region: "Ege",
    criticalWarning: "Akşam ikmaline izin verilmemektedir!",
    documents: ["Ehliyet", "Kimlik", "T9 Belgesi", "Araç Ruhsatı"],
    technicalData: {
      maxDraft: "12m",
      vhfChannel: "Kanal 12",
      workingHours: "08:00 - 18:00",
      anchorage: "Dış Liman",
    },
    contacts: [
      { name: "Liman Güvenlik", phone: "+90 232 463 00 00", role: "Güvenlik" },
      { name: "Operasyon Merkezi", phone: "+90 232 463 00 01", role: "Operasyon" },
    ],
    notes: ["Giriş için 24 saat önceden bildirim gerekli", "Tehlikeli madde taşımacılığı için özel izin alınmalı"],
  },
  {
    id: "aliaga",
    name: "Aliağa Limanı",
    city: "İzmir",
    region: "Ege",
    criticalWarning: "ISPS kod uygulaması aktif - Giriş kartı zorunlu!",
    documents: ["Ehliyet", "Kimlik", "SRC Belgesi", "ADR Belgesi", "Araç Ruhsatı"],
    technicalData: {
      maxDraft: "15m",
      vhfChannel: "Kanal 16",
      workingHours: "24 Saat",
      anchorage: "Nemrut Körfezi",
    },
    contacts: [
      { name: "SOCAR Terminal", phone: "+90 232 616 00 00", role: "Terminal" },
      { name: "Petkim Limanı", phone: "+90 232 616 12 00", role: "Liman" },
    ],
    notes: ["Rafineri alanına giriş için özel eğitim sertifikası gerekli"],
  },
  {
    id: "ambarli",
    name: "Ambarlı Limanı",
    city: "İstanbul",
    region: "Marmara",
    documents: ["Ehliyet", "Kimlik", "Araç Ruhsatı", "Yetki Belgesi"],
    technicalData: {
      maxDraft: "14m",
      vhfChannel: "Kanal 13",
      workingHours: "24 Saat",
    },
    contacts: [
      { name: "Marport Terminal", phone: "+90 212 875 00 00", role: "Terminal" },
      { name: "Kumport Terminal", phone: "+90 212 875 10 00", role: "Terminal" },
    ],
  },
  {
    id: "haydarpasa",
    name: "Haydarpaşa Limanı",
    city: "İstanbul",
    region: "Marmara",
    criticalWarning: "Liman yenileme çalışmaları devam ediyor - Sınırlı erişim!",
    documents: ["Ehliyet", "Kimlik", "Araç Ruhsatı"],
    technicalData: {
      maxDraft: "10m",
      vhfChannel: "Kanal 12",
      workingHours: "08:00 - 20:00",
    },
    contacts: [
      { name: "Liman Müdürlüğü", phone: "+90 216 348 80 20", role: "Yönetim" },
    ],
  },
  {
    id: "mersin",
    name: "Mersin Limanı",
    city: "Mersin",
    region: "Akdeniz",
    documents: ["Ehliyet", "Kimlik", "T9 Belgesi", "Araç Ruhsatı", "ADR Belgesi"],
    technicalData: {
      maxDraft: "16m",
      vhfChannel: "Kanal 14",
      workingHours: "24 Saat",
      anchorage: "Mersin Açıkları",
    },
    contacts: [
      { name: "MIP Terminal", phone: "+90 324 241 27 00", role: "Terminal" },
      { name: "Liman Başkanlığı", phone: "+90 324 238 50 00", role: "Yönetim" },
    ],
    notes: ["Türkiye'nin en büyük konteynır limanı"],
  },
  {
    id: "iskenderun",
    name: "İskenderun Limanı",
    city: "Hatay",
    region: "Akdeniz",
    documents: ["Ehliyet", "Kimlik", "Araç Ruhsatı"],
    technicalData: {
      maxDraft: "13m",
      vhfChannel: "Kanal 16",
      workingHours: "24 Saat",
    },
    contacts: [
      { name: "Limak Terminal", phone: "+90 326 614 00 00", role: "Terminal" },
    ],
  },
  {
    id: "bodrum",
    name: "Bodrum Cruise Port",
    city: "Muğla",
    region: "Ege",
    criticalWarning: "Yat limanı - Ticari araç girişi kısıtlı!",
    documents: ["Ehliyet", "Kimlik", "Gümrük Beyannamesi"],
    technicalData: {
      maxDraft: "8m",
      vhfChannel: "Kanal 12",
      workingHours: "08:00 - 22:00",
    },
    contacts: [
      { name: "Marina Ofis", phone: "+90 252 316 18 60", role: "Marina" },
      { name: "Gümrük Müdürlüğü", phone: "+90 252 316 10 00", role: "Gümrük" },
    ],
    notes: ["Milli ikmal için gümrük müdürlüğünden izin alınmalı"],
  },
  {
    id: "gocek",
    name: "Göcek Limanı",
    city: "Muğla",
    region: "Ege",
    documents: ["Ehliyet", "Kimlik"],
    technicalData: {
      maxDraft: "6m",
      vhfChannel: "Kanal 16",
      workingHours: "08:00 - 20:00",
    },
    contacts: [
      { name: "D-Marin Göcek", phone: "+90 252 645 27 60", role: "Marina" },
    ],
  },
];

const initialPortsData: PortData[] = portsData;

export default function PortWikiPage() {
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedPort, setSelectedPort] = useState<PortData | null>(null);
  const [copiedDocs, setCopiedDocs] = useState(false);
  const [ports, setPorts] = useState<PortData[]>(initialPortsData);
  const [isEditing, setIsEditing] = useState(false);
  const [editForm, setEditForm] = useState<PortData | null>(null);
  const [newDocument, setNewDocument] = useState("");
  const [newNote, setNewNote] = useState("");
  const [newContact, setNewContact] = useState({ name: "", phone: "", role: "" });
  const [showAddModal, setShowAddModal] = useState(false);
  const [newPort, setNewPort] = useState<Omit<PortData, "id">>({
    name: "",
    city: "",
    region: "Ege",
    documents: [],
    technicalData: {},
    contacts: [],
  });

  const filteredPorts = ports.filter(
    (port) =>
      port.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      port.city.toLowerCase().includes(searchQuery.toLowerCase()) ||
      port.region.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const handleCopyDocuments = () => {
    if (!selectedPort) return;
    const docText = `${selectedPort.name} için gerekli evraklar:\n\n${selectedPort.documents.map((d) => `✓ ${d}`).join("\n")}`;
    navigator.clipboard.writeText(docText);
    setCopiedDocs(true);
    setTimeout(() => setCopiedDocs(false), 2000);
  };

  const handleStartEdit = () => {
    if (!selectedPort) return;
    setEditForm({ ...selectedPort });
    setIsEditing(true);
  };

  const handleCancelEdit = () => {
    setEditForm(null);
    setIsEditing(false);
    setNewDocument("");
    setNewNote("");
    setNewContact({ name: "", phone: "", role: "" });
  };

  const handleSaveEdit = () => {
    if (!editForm) return;
    setPorts((prev) =>
      prev.map((p) => (p.id === editForm.id ? editForm : p))
    );
    setSelectedPort(editForm);
    setIsEditing(false);
    setEditForm(null);
    setNewDocument("");
    setNewNote("");
    setNewContact({ name: "", phone: "", role: "" });
  };

  const handleAddDocument = () => {
    if (!editForm || !newDocument.trim()) return;
    setEditForm({
      ...editForm,
      documents: [...editForm.documents, newDocument.trim()],
    });
    setNewDocument("");
  };

  const handleRemoveDocument = (idx: number) => {
    if (!editForm) return;
    setEditForm({
      ...editForm,
      documents: editForm.documents.filter((_, i) => i !== idx),
    });
  };

  const _handleAddNote = () => {
    if (!editForm || !newNote.trim()) return;
    setEditForm({
      ...editForm,
      notes: [...(editForm.notes || []), newNote.trim()],
    });
    setNewNote("");
  };

  const _handleRemoveNote = (idx: number) => {
    if (!editForm) return;
    setEditForm({
      ...editForm,
      notes: (editForm.notes || []).filter((_, i) => i !== idx),
    });
  };

  void _handleAddNote;
  void _handleRemoveNote;

  const handleAddContact = () => {
    if (!editForm || !newContact.name.trim() || !newContact.phone.trim()) return;
    setEditForm({
      ...editForm,
      contacts: [...editForm.contacts, { ...newContact }],
    });
    setNewContact({ name: "", phone: "", role: "" });
  };

  const handleRemoveContact = (idx: number) => {
    if (!editForm) return;
    setEditForm({
      ...editForm,
      contacts: editForm.contacts.filter((_, i) => i !== idx),
    });
  };

  const regions = [...new Set(ports.map((p) => p.region))];
  const allRegions = ["Ege", "Marmara", "Akdeniz", "Karadeniz"];

  const regionColors: Record<string, { bg: string; border: string; icon: string; glow: string; gradient: string }> = {
    Ege: {
      bg: "from-emerald-500/20 to-emerald-600/5",
      border: "border-emerald-500/30 hover:border-emerald-400/50",
      icon: "from-emerald-500/40 to-emerald-600/20 text-emerald-300",
      glow: "bg-emerald-500/20",
      gradient: "from-emerald-400 to-teal-500",
    },
    Marmara: {
      bg: "from-blue-500/20 to-blue-600/5",
      border: "border-blue-500/30 hover:border-blue-400/50",
      icon: "from-blue-500/40 to-blue-600/20 text-blue-300",
      glow: "bg-blue-500/20",
      gradient: "from-blue-400 to-indigo-500",
    },
    Akdeniz: {
      bg: "from-amber-500/20 to-orange-600/5",
      border: "border-amber-500/30 hover:border-amber-400/50",
      icon: "from-amber-500/40 to-orange-600/20 text-amber-300",
      glow: "bg-amber-500/20",
      gradient: "from-amber-400 to-orange-500",
    },
    Karadeniz: {
      bg: "from-purple-500/20 to-violet-600/5",
      border: "border-purple-500/30 hover:border-purple-400/50",
      icon: "from-purple-500/40 to-violet-600/20 text-purple-300",
      glow: "bg-purple-500/20",
      gradient: "from-purple-400 to-violet-500",
    },
  };

  const handleAddPort = () => {
    if (!newPort.name.trim() || !newPort.city.trim()) {
      alert("Lütfen liman adı ve şehir bilgisini girin.");
      return;
    }
    const port: PortData = {
      ...newPort,
      id: `port_${Date.now()}`,
    };
    setPorts((prev) => [...prev, port]);
    setShowAddModal(false);
    setNewPort({
      name: "",
      city: "",
      region: "Ege",
      documents: [],
      technicalData: {},
      contacts: [],
    });
  };

  const handleDeletePort = (portId: string) => {
    if (confirm("Bu limanı silmek istediğinize emin misiniz?")) {
      setPorts((prev) => prev.filter((p) => p.id !== portId));
      setSelectedPort(null);
    }
  };

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-cyan-500/5 to-transparent px-6 py-4">
          {/* Neon separator line */}
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/60 via-cyan-400/30 to-transparent" />
          
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              PORT WİKİ
            </div>
            <div className="text-3xl font-black tracking-tight">Operasyonel El Kitabı</div>
          </div>
          <div className="flex items-center gap-3">
            <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
              <div className="h-2 w-2 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.6)]" />
              <span className="text-xs font-medium text-white/70">Liman</span>
              <span className="text-sm font-bold text-white">{ports.length}</span>
            </div>
            <button
              type="button"
              onClick={() => setShowAddModal(true)}
              className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-cyan-600 to-cyan-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(6,182,212,0.25)] transition-all hover:from-cyan-500 hover:to-cyan-600"
            >
              <Plus className="h-4 w-4" />
              Yeni Liman
            </button>
          </div>
        </div>

        {/* Search Bar */}
        <div className="relative flex flex-none items-center gap-4 px-6 py-3">
          {/* Neon separator line */}
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/30 via-cyan-400/15 to-transparent" />
          
          <div className="relative flex-1">
            <Search className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-cyan-400/60" />
            <input
              type="text"
              placeholder="Liman, şehir veya bölge ara..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="h-11 w-full rounded-xl border border-cyan-500/20 bg-white/[0.03] pl-12 pr-4 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/40 focus:bg-white/[0.05] focus:shadow-[0_0_20px_rgba(6,182,212,0.1)] transition-all"
            />
          </div>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6">
          {regions.map((region) => {
            const regionPorts = filteredPorts.filter((p) => p.region === region);
            if (regionPorts.length === 0) return null;

            return (
              <div key={region} className="mb-8">
                <h2 className="mb-4 flex items-center gap-3">
                  <div className={cn(
                    "flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]",
                    regionColors[region]?.icon || "from-cyan-500/25 to-cyan-600/10 text-cyan-400"
                  )}>
                    <MapPin className="h-4 w-4" />
                  </div>
                  <span className={cn(
                    "text-sm font-semibold tracking-[0.2em] bg-gradient-to-r bg-clip-text text-transparent",
                    regionColors[region]?.gradient || "from-cyan-400 to-cyan-500"
                  )}>{region.toUpperCase()} BÖLGESİ</span>
                </h2>
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                  {regionPorts.map((port) => {
                    const colors = regionColors[port.region] || regionColors.Ege;
                    return (
                      <button
                        key={port.id}
                        type="button"
                        onClick={() => setSelectedPort(port)}
                        className={cn(
                          "group relative flex flex-col rounded-2xl border p-5 text-left transition-all duration-300 overflow-hidden",
                          "bg-gradient-to-br",
                          colors.bg,
                          colors.border,
                          "hover:scale-[1.02] hover:shadow-[0_8px_40px_rgba(0,0,0,0.3)]",
                          port.criticalWarning && "ring-2 ring-red-500/40 ring-offset-1 ring-offset-transparent"
                        )}
                      >
                        {/* Animated background glow */}
                        <div className={cn(
                          "pointer-events-none absolute -right-12 -top-12 h-32 w-32 rounded-full blur-3xl transition-all duration-500 opacity-40 group-hover:opacity-70 group-hover:scale-125",
                          colors.glow
                        )} />
                        <div className={cn(
                          "pointer-events-none absolute -left-8 -bottom-8 h-24 w-24 rounded-full blur-2xl transition-all duration-500 opacity-20 group-hover:opacity-50",
                          colors.glow
                        )} />
                        
                        {/* Icon with gradient */}
                        <div className={cn(
                          "relative mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3",
                          colors.icon
                        )}>
                          <Anchor className="h-7 w-7 drop-shadow-lg" />
                          {/* Shine effect */}
                          <div className="absolute inset-0 rounded-2xl bg-gradient-to-tr from-white/20 via-transparent to-transparent" />
                        </div>

                        {/* Port name */}
                        <div className="relative mb-1">
                          <div className="text-lg font-bold tracking-tight text-white">
                            {port.name}
                          </div>
                        </div>
                        
                        <div className="text-sm font-medium text-white/60 mb-3">{port.city}</div>
                        
                        {/* Critical warning badge */}
                        {port.criticalWarning && (
                          <div className="mb-3 flex items-center gap-2 rounded-lg bg-red-500/20 border border-red-500/30 px-3 py-2">
                            <AlertTriangle className="h-4 w-4 text-red-400 animate-pulse" />
                            <span className="text-xs font-semibold text-red-300">Kritik Uyarı!</span>
                          </div>
                        )}

                        {/* Info badges */}
                        <div className="mt-auto flex flex-wrap items-center gap-2">
                          <div className="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-medium text-white/70 backdrop-blur-sm">
                            <FileText className="h-3 w-3" />
                            {port.documents.length} evrak
                          </div>
                          {port.contacts.length > 0 && (
                            <div className="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-medium text-white/70 backdrop-blur-sm">
                              <Phone className="h-3 w-3" />
                              {port.contacts.length} kişi
                            </div>
                          )}
                        </div>

                        {/* Bottom accent line */}
                        <div className={cn(
                          "absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r opacity-0 transition-opacity duration-300 group-hover:opacity-100",
                          colors.gradient
                        )} />
                      </button>
                    );
                  })}
                </div>
              </div>
            );
          })}

          {filteredPorts.length === 0 && (
            <div className="flex flex-col items-center justify-center py-16 text-center">
              <Search className="mb-4 h-12 w-12 text-white/20" />
              <div className="text-lg font-medium text-white/60">Sonuç bulunamadı</div>
              <div className="mt-1 text-sm text-white/40">
                Farklı bir arama terimi deneyin
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Detail Panel (Sheet) */}
      {selectedPort && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={() => setSelectedPort(null)}
            aria-label="Kapat"
          />
          <div className="relative z-10 flex h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur-xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.65)]">
            {/* Panel Header */}
            <div className="flex flex-col gap-3 border-b border-white/10 px-4 py-4 sm:px-6">
              {isEditing ? (
                <>
                  {/* Düzenleme modu - mobil uyumlu */}
                  <div className="flex items-center justify-between">
                    <input
                      type="text"
                      value={editForm?.name || ""}
                      onChange={(e) => setEditForm(editForm ? { ...editForm, name: e.target.value } : null)}
                      placeholder="Liman Adı"
                      className="flex-1 mr-3 rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-lg font-semibold outline-none placeholder:text-white/40 focus:border-cyan-500/50"
                    />
                    <button
                      type="button"
                      className="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                      onClick={() => {
                        setSelectedPort(null);
                        handleCancelEdit();
                      }}
                      aria-label="Kapat"
                    >
                      <X className="h-4 w-4" />
                    </button>
                  </div>
                  <div className="flex flex-wrap items-center gap-2">
                    <input
                      type="text"
                      value={editForm?.city || ""}
                      onChange={(e) => setEditForm(editForm ? { ...editForm, city: e.target.value } : null)}
                      placeholder="Şehir"
                      className="w-24 rounded-lg border border-white/20 bg-white/5 px-3 py-1.5 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"
                    />
                    <span className="text-white/30">-</span>
                    <select
                      value={editForm?.region || "Ege"}
                      onChange={(e) => setEditForm(editForm ? { ...editForm, region: e.target.value } : null)}
                      className="w-24 rounded-lg border border-white/20 bg-white/5 px-3 py-1.5 text-sm outline-none focus:border-cyan-500/50"
                    >
                      {allRegions.map((r) => (
                        <option key={r} value={r} className="bg-slate-800">{r}</option>
                      ))}
                    </select>
                    <div className="ml-auto flex items-center gap-2">
                      <button
                        type="button"
                        className="flex items-center justify-center gap-1.5 rounded-lg bg-emerald-500/20 px-3 py-1.5 text-xs font-medium text-emerald-400 transition hover:bg-emerald-500/30"
                        onClick={handleSaveEdit}
                      >
                        <Save className="h-3.5 w-3.5" />
                        Kaydet
                      </button>
                      <button
                        type="button"
                        className="flex items-center justify-center gap-1.5 rounded-lg bg-white/5 px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10 hover:text-white"
                        onClick={handleCancelEdit}
                      >
                        İptal
                      </button>
                    </div>
                  </div>
                </>
              ) : (
                <div className="flex items-center justify-between">
                  <div>
                    <div className="text-xl font-semibold">{selectedPort.name}</div>
                    <div className="text-sm text-white/50">{selectedPort.city} - {selectedPort.region}</div>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      className="flex items-center gap-1.5 rounded-lg bg-blue-500/20 px-3 py-2 text-xs font-medium text-blue-400 transition hover:bg-blue-500/30"
                      onClick={handleStartEdit}
                    >
                      <Edit3 className="h-3.5 w-3.5" />
                      Düzenle
                    </button>
                    <button
                      type="button"
                      className="flex items-center gap-1.5 rounded-lg bg-red-500/20 px-3 py-2 text-xs font-medium text-red-400 transition hover:bg-red-500/30"
                      onClick={() => handleDeletePort(selectedPort.id)}
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                      Sil
                    </button>
                    <button
                      type="button"
                      className="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
                      onClick={() => {
                        setSelectedPort(null);
                        handleCancelEdit();
                      }}
                      aria-label="Kapat"
                    >
                      <X className="h-4 w-4" />
                    </button>
                  </div>
                </div>
              )}
            </div>

            {/* Panel Content */}
            <div className="flex-1 overflow-y-auto p-6">
              {/* Critical Warning */}
              {isEditing ? (
                <div className="mb-6">
                  <div className="mb-3 flex items-center gap-2 text-sm font-semibold">
                    <Ban className="h-4 w-4 text-red-400" />
                    Kritik Uyarı
                  </div>
                  <div className="rounded-xl border border-red-500/30 bg-red-500/10 p-4">
                    <input
                      type="text"
                      value={editForm?.criticalWarning || ""}
                      onChange={(e) => setEditForm(editForm ? { ...editForm, criticalWarning: e.target.value || undefined } : null)}
                      placeholder="Kritik uyarı ekleyin (boş bırakılabilir)..."
                      className="w-full rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-200 outline-none placeholder:text-red-300/50 focus:border-red-500/50"
                    />
                    <div className="mt-2 text-[11px] text-red-300/60">
                      Boş bırakırsanız kritik uyarı kaldırılır.
                    </div>
                  </div>
                </div>
              ) : selectedPort.criticalWarning ? (
                <div className="mb-6 flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 p-4">
                  <Ban className="mt-0.5 h-5 w-5 flex-shrink-0 text-red-400" />
                  <div>
                    <div className="text-sm font-semibold text-red-400">Kritik Uyarı</div>
                    <div className="mt-1 text-sm text-red-300/80">{selectedPort.criticalWarning}</div>
                  </div>
                </div>
              ) : null}

              {/* Documents Checklist */}
              <div className="mb-6">
                <div className="mb-3 flex items-center justify-between">
                  <div className="flex items-center gap-2 text-sm font-semibold">
                    <FileText className="h-4 w-4 text-emerald-400" />
                    Gerekli Evraklar
                  </div>
                  {!isEditing && (
                    <button
                      type="button"
                      onClick={handleCopyDocuments}
                      className={cn(
                        "flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition",
                        copiedDocs
                          ? "bg-emerald-500/20 text-emerald-400"
                          : "bg-white/5 text-white/60 hover:bg-white/10 hover:text-white"
                      )}
                    >
                      {copiedDocs ? (
                        <>
                          <Check className="h-3.5 w-3.5" />
                          Kopyalandı
                        </>
                      ) : (
                        <>
                          <Copy className="h-3.5 w-3.5" />
                          Kopyala
                        </>
                      )}
                    </button>
                  )}
                </div>
                <div className="space-y-2 rounded-xl border border-white/10 bg-white/[0.03] p-4">
                  {(isEditing ? editForm?.documents : selectedPort.documents)?.map((doc, idx) => (
                    <div key={idx} className="flex items-center justify-between gap-3">
                      <div className="flex items-center gap-3">
                        <div className="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-500/20">
                          <ShieldCheck className="h-3.5 w-3.5 text-emerald-400" />
                        </div>
                        <span className="text-sm">{doc}</span>
                      </div>
                      {isEditing && (
                        <button
                          type="button"
                          onClick={() => handleRemoveDocument(idx)}
                          className="flex h-6 w-6 items-center justify-center rounded-md text-red-400 transition hover:bg-red-500/20"
                        >
                          <Trash2 className="h-3.5 w-3.5" />
                        </button>
                      )}
                    </div>
                  ))}
                  {isEditing && (
                    <div className="mt-3 flex items-center gap-2 border-t border-white/10 pt-3">
                      <input
                        type="text"
                        value={newDocument}
                        onChange={(e) => setNewDocument(e.target.value)}
                        placeholder="Yeni evrak ekle..."
                        className="flex-1 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                        onKeyDown={(e) => e.key === "Enter" && handleAddDocument()}
                      />
                      <button
                        type="button"
                        onClick={handleAddDocument}
                        className="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 transition hover:bg-emerald-500/30"
                      >
                        <Plus className="h-4 w-4" />
                      </button>
                    </div>
                  )}
                </div>
              </div>

              {/* Contacts */}
              {((isEditing ? editForm?.contacts : selectedPort.contacts) || []).length > 0 || isEditing ? (
                <div className="mb-6">
                  <div className="mb-3 flex items-center gap-2 text-sm font-semibold">
                    <Phone className="h-4 w-4 text-purple-400" />
                    İletişim
                  </div>
                  <div className="space-y-2">
                    {(isEditing ? editForm?.contacts : selectedPort.contacts)?.map((contact, idx) => (
                      <div
                        key={idx}
                        className="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.03] p-4"
                      >
                        <div>
                          <div className="text-sm font-medium">{contact.name}</div>
                          <div className="text-xs text-white/50">{contact.role}</div>
                        </div>
                        <div className="flex items-center gap-2">
                          <a
                            href={`tel:${contact.phone}`}
                            className="flex items-center gap-2 rounded-lg bg-purple-500/20 px-3 py-2 text-xs font-medium text-purple-300 transition hover:bg-purple-500/30"
                          >
                            <Phone className="h-3.5 w-3.5" />
                            {contact.phone}
                          </a>
                          {isEditing && (
                            <button
                              type="button"
                              onClick={() => handleRemoveContact(idx)}
                              className="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-500/20"
                            >
                              <Trash2 className="h-3.5 w-3.5" />
                            </button>
                          )}
                        </div>
                      </div>
                    ))}
                    {isEditing && (
                      <div className="mt-3 space-y-2 border-t border-white/10 pt-3">
                        <div className="grid grid-cols-3 gap-2">
                          <input
                            type="text"
                            value={newContact.name}
                            onChange={(e) => setNewContact({ ...newContact, name: e.target.value })}
                            placeholder="İsim"
                            className="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                          />
                          <input
                            type="text"
                            value={newContact.phone}
                            onChange={(e) => setNewContact({ ...newContact, phone: e.target.value })}
                            placeholder="Telefon"
                            className="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                          />
                          <input
                            type="text"
                            value={newContact.role}
                            onChange={(e) => setNewContact({ ...newContact, role: e.target.value })}
                            placeholder="Rol"
                            className="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                          />
                        </div>
                        <button
                          type="button"
                          onClick={handleAddContact}
                          className="flex w-full items-center justify-center gap-1.5 rounded-lg bg-purple-500/20 py-2 text-xs font-medium text-purple-400 transition hover:bg-purple-500/30"
                        >
                          <Plus className="h-3.5 w-3.5" />
                          Kişi Ekle
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              ) : null}

            </div>
          </div>
        </div>
      )}

      {/* Add Port Modal */}
      {showAddModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={() => setShowAddModal(false)}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="relative flex items-center justify-between px-5 py-4">
              <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/60 via-cyan-400/30 to-transparent" />
              <div>
                <div className="text-sm font-light tracking-[0.2em] text-slate-400">YENİ LİMAN</div>
                <div className="text-lg font-bold">Liman Bilgileri</div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={() => setShowAddModal(false)}
                aria-label="Kapat"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="space-y-4 px-5 py-5">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="mb-2 block text-xs font-semibold text-white/70">Liman Adı *</label>
                  <input
                    type="text"
                    value={newPort.name}
                    onChange={(e) => setNewPort({ ...newPort, name: e.target.value })}
                    placeholder="Örn: Alsancak Limanı"
                    className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"
                  />
                </div>
                <div>
                  <label className="mb-2 block text-xs font-semibold text-white/70">Şehir *</label>
                  <input
                    type="text"
                    value={newPort.city}
                    onChange={(e) => setNewPort({ ...newPort, city: e.target.value })}
                    placeholder="Örn: İzmir"
                    className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"
                  />
                </div>
              </div>

              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Bölge *</label>
                <div className="grid grid-cols-4 gap-2">
                  {allRegions.map((region) => (
                    <button
                      key={region}
                      type="button"
                      onClick={() => setNewPort({ ...newPort, region })}
                      className={cn(
                        "rounded-lg border py-2.5 text-sm font-medium transition-all",
                        newPort.region === region
                          ? "border-cyan-500/50 bg-cyan-500/10 text-cyan-400"
                          : "border-white/10 bg-white/5 text-white/60 hover:bg-white/10"
                      )}
                    >
                      {region}
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <label className="mb-2 block text-xs font-semibold text-white/70">Kritik Uyarı (Opsiyonel)</label>
                <input
                  type="text"
                  value={newPort.criticalWarning || ""}
                  onChange={(e) => setNewPort({ ...newPort, criticalWarning: e.target.value || undefined })}
                  placeholder="Örn: Akşam ikmaline izin verilmemektedir!"
                  className="h-11 w-full rounded-md border border-amber-500/30 bg-amber-500/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-amber-500/50"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="mb-2 block text-xs font-semibold text-white/70">VHF Kanalı</label>
                  <input
                    type="text"
                    value={newPort.technicalData.vhfChannel || ""}
                    onChange={(e) => setNewPort({ ...newPort, technicalData: { ...newPort.technicalData, vhfChannel: e.target.value || undefined } })}
                    placeholder="Örn: Kanal 16"
                    className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"
                  />
                </div>
                <div>
                  <label className="mb-2 block text-xs font-semibold text-white/70">Çalışma Saatleri</label>
                  <input
                    type="text"
                    value={newPort.technicalData.workingHours || ""}
                    onChange={(e) => setNewPort({ ...newPort, technicalData: { ...newPort.technicalData, workingHours: e.target.value || undefined } })}
                    placeholder="Örn: 24 Saat"
                    className="h-11 w-full rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-cyan-500/50"
                  />
                </div>
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
              <button
                type="button"
                onClick={() => setShowAddModal(false)}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleAddPort}
                className="rounded-lg bg-gradient-to-br from-cyan-600 to-cyan-700 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_2px_10px_rgba(6,182,212,0.25)] transition-all hover:from-cyan-500 hover:to-cyan-600"
              >
                Liman Ekle
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
