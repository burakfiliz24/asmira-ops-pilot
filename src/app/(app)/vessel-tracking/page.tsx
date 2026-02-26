"use client";

import { useState, useMemo } from "react";
import {
  Navigation,
  Ship,
  Anchor,
  MapPin,
  Clock,
  Plus,
  X,
  Search,
  Filter,
} from "lucide-react";
import { VesselMap, turkishPorts, type VesselPosition } from "@/components/maps/VesselMap";
import { cn } from "@/lib/utils/cn";

// Demo gemi verileri
const demoVessels: VesselPosition[] = [
  {
    id: "v1",
    name: "M/T ASMIRA STAR",
    lat: 40.7667,
    lon: 29.9167,
    status: "moored",
    port: "İzmit",
    destination: "İzmit Körfezi",
    eta: "Yanaşık",
    speed: 0,
  },
  {
    id: "v2",
    name: "M/V BOSPHORUS",
    lat: 40.95,
    lon: 28.75,
    status: "anchored",
    port: "Ambarlı",
    destination: "Ambarlı Limanı",
    eta: "14:30",
    speed: 0,
  },
  {
    id: "v3",
    name: "M/T AEGEAN SEA",
    lat: 38.5,
    lon: 26.5,
    status: "underway",
    destination: "Aliağa",
    eta: "18:00",
    speed: 12.5,
    course: 45,
  },
  {
    id: "v4",
    name: "M/V MARMARA QUEEN",
    lat: 40.85,
    lon: 29.3,
    status: "underway",
    destination: "İzmit",
    eta: "16:45",
    speed: 8.2,
    course: 90,
  },
];

const statusConfig = {
  moored: { label: "Yanaşık", color: "bg-emerald-500", textColor: "text-emerald-400" },
  anchored: { label: "Demirde", color: "bg-amber-500", textColor: "text-amber-400" },
  underway: { label: "Seyirde", color: "bg-blue-500", textColor: "text-blue-400" },
  unknown: { label: "Bilinmiyor", color: "bg-gray-500", textColor: "text-gray-400" },
};

export default function VesselTrackingPage() {
  const [vessels, setVessels] = useState<VesselPosition[]>(demoVessels);
  const [selectedVessel, setSelectedVessel] = useState<VesselPosition | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [statusFilter, setStatusFilter] = useState<string>("all");
  const [showAddModal, setShowAddModal] = useState(false);
  const [newVessel, setNewVessel] = useState({
    name: "",
    lat: "",
    lon: "",
    status: "unknown" as VesselPosition["status"],
    destination: "",
    eta: "",
  });

  const filteredVessels = useMemo(() => {
    return vessels.filter((v) => {
      const matchesSearch = v.name.toLowerCase().includes(searchQuery.toLowerCase());
      const matchesStatus = statusFilter === "all" || v.status === statusFilter;
      return matchesSearch && matchesStatus;
    });
  }, [vessels, searchQuery, statusFilter]);

  const stats = useMemo(() => {
    return {
      total: vessels.length,
      moored: vessels.filter((v) => v.status === "moored").length,
      anchored: vessels.filter((v) => v.status === "anchored").length,
      underway: vessels.filter((v) => v.status === "underway").length,
    };
  }, [vessels]);

  function handleAddVessel() {
    if (!newVessel.name.trim() || !newVessel.lat || !newVessel.lon) {
      alert("Lütfen gemi adı ve koordinatları girin.");
      return;
    }

    const vessel: VesselPosition = {
      id: `v_${Date.now()}`,
      name: newVessel.name.toUpperCase(),
      lat: parseFloat(newVessel.lat),
      lon: parseFloat(newVessel.lon),
      status: newVessel.status,
      destination: newVessel.destination || undefined,
      eta: newVessel.eta || undefined,
    };

    setVessels((prev) => [...prev, vessel]);
    setShowAddModal(false);
    setNewVessel({
      name: "",
      lat: "",
      lon: "",
      status: "unknown",
      destination: "",
      eta: "",
    });
  }

  function handleDeleteVessel(id: string) {
    if (confirm("Bu gemiyi silmek istediğinize emin misiniz?")) {
      setVessels((prev) => prev.filter((v) => v.id !== id));
      if (selectedVessel?.id === id) {
        setSelectedVessel(null);
      }
    }
  }

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />

          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">
              GEMİ TAKİP
            </div>
            <div className="text-3xl font-black tracking-tight">Canlı Harita</div>
          </div>

          <button
            type="button"
            onClick={() => setShowAddModal(true)}
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
          >
            <Plus className="h-4 w-4" />
            Gemi Ekle
          </button>
        </div>

        {/* Stats & Filters */}
        <div className="relative flex flex-none flex-wrap items-center gap-3 px-6 py-3">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/40 via-cyan-400/20 to-transparent" />

          {/* Stats */}
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5">
            <Ship className="h-4 w-4 text-blue-400" />
            <span className="text-xs text-white/70">Toplam</span>
            <span className="text-sm font-bold">{stats.total}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5">
            <div className="h-2 w-2 rounded-full bg-emerald-500" />
            <span className="text-xs text-white/70">Yanaşık</span>
            <span className="text-sm font-bold">{stats.moored}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5">
            <div className="h-2 w-2 rounded-full bg-amber-500" />
            <span className="text-xs text-white/70">Demirde</span>
            <span className="text-sm font-bold">{stats.anchored}</span>
          </div>
          <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5">
            <div className="h-2 w-2 rounded-full bg-blue-500" />
            <span className="text-xs text-white/70">Seyirde</span>
            <span className="text-sm font-bold">{stats.underway}</span>
          </div>

          {/* Search */}
          <div className="ml-auto flex items-center gap-2">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" />
              <input
                type="text"
                placeholder="Gemi ara..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="h-9 w-48 rounded-lg border border-white/10 bg-white/[0.03] pl-9 pr-3 text-sm outline-none placeholder:text-white/40 focus:border-blue-500/50"
              />
            </div>
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="h-9 rounded-lg border border-white/10 bg-white/[0.03] px-3 text-sm outline-none focus:border-blue-500/50"
            >
              <option value="all" className="bg-slate-900">Tümü</option>
              <option value="moored" className="bg-slate-900">Yanaşık</option>
              <option value="anchored" className="bg-slate-900">Demirde</option>
              <option value="underway" className="bg-slate-900">Seyirde</option>
            </select>
          </div>
        </div>

        {/* Content */}
        <div className="flex flex-1 gap-4 overflow-hidden p-4">
          {/* Map */}
          <div className="flex-1 overflow-hidden rounded-xl border border-white/10">
            <VesselMap
              vessels={filteredVessels}
              showPorts={true}
              selectedVesselId={selectedVessel?.id}
              onVesselClick={setSelectedVessel}
              height="100%"
              className="h-full"
            />
          </div>

          {/* Vessel List */}
          <div className="w-80 flex-shrink-0 overflow-hidden rounded-xl border border-white/10 bg-white/[0.02]">
            <div className="border-b border-white/10 px-4 py-3">
              <div className="text-sm font-semibold">Gemi Listesi</div>
              <div className="text-xs text-white/50">{filteredVessels.length} gemi</div>
            </div>
            <div className="h-[calc(100%-60px)] overflow-y-auto p-2">
              {filteredVessels.map((vessel) => {
                const config = statusConfig[vessel.status];
                const isSelected = selectedVessel?.id === vessel.id;

                return (
                  <button
                    key={vessel.id}
                    type="button"
                    onClick={() => setSelectedVessel(vessel)}
                    className={cn(
                      "group mb-2 w-full rounded-lg border p-3 text-left transition-all",
                      isSelected
                        ? "border-blue-500/50 bg-blue-500/10"
                        : "border-white/10 bg-white/[0.02] hover:border-white/20 hover:bg-white/[0.04]"
                    )}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <Ship className="h-4 w-4 text-blue-400" />
                          <span className="text-sm font-semibold">{vessel.name}</span>
                        </div>
                        <div className="mt-1 flex items-center gap-1.5">
                          <div className={cn("h-1.5 w-1.5 rounded-full", config.color)} />
                          <span className={cn("text-xs", config.textColor)}>
                            {config.label}
                          </span>
                        </div>
                      </div>
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          handleDeleteVessel(vessel.id);
                        }}
                        className="rounded p-1 text-red-400 opacity-0 transition hover:bg-red-500/20 group-hover:opacity-100"
                      >
                        <X className="h-3.5 w-3.5" />
                      </button>
                    </div>

                    {vessel.destination && (
                      <div className="mt-2 flex items-center gap-1.5 text-xs text-white/60">
                        <MapPin className="h-3 w-3" />
                        <span>{vessel.destination}</span>
                      </div>
                    )}

                    {vessel.eta && (
                      <div className="mt-1 flex items-center gap-1.5 text-xs text-white/60">
                        <Clock className="h-3 w-3" />
                        <span>ETA: {vessel.eta}</span>
                      </div>
                    )}

                    {vessel.speed !== undefined && vessel.speed > 0 && (
                      <div className="mt-1 flex items-center gap-1.5 text-xs text-white/60">
                        <Navigation className="h-3 w-3" />
                        <span>{vessel.speed} knot</span>
                      </div>
                    )}
                  </button>
                );
              })}

              {filteredVessels.length === 0 && (
                <div className="flex flex-col items-center justify-center py-8 text-center">
                  <Ship className="mb-2 h-8 w-8 text-white/20" />
                  <div className="text-sm text-white/50">Gemi bulunamadı</div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Add Vessel Modal */}
      {showAddModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <button
            type="button"
            className="absolute inset-0 bg-black/70 backdrop-blur-sm"
            onClick={() => setShowAddModal(false)}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            <div className="mb-6 flex items-center justify-between">
              <h3 className="text-xl font-semibold">Yeni Gemi Ekle</h3>
              <button
                type="button"
                onClick={() => setShowAddModal(false)}
                className="flex h-8 w-8 items-center justify-center rounded-lg text-white/50 hover:bg-white/10 hover:text-white"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="space-y-4">
              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">
                  Gemi Adı <span className="text-red-400">*</span>
                </label>
                <input
                  type="text"
                  value={newVessel.name}
                  onChange={(e) => setNewVessel({ ...newVessel, name: e.target.value })}
                  placeholder="Örn: M/T ASMIRA"
                  className="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="mb-2 block text-sm font-medium text-white/70">
                    Enlem (Lat) <span className="text-red-400">*</span>
                  </label>
                  <input
                    type="number"
                    step="0.0001"
                    value={newVessel.lat}
                    onChange={(e) => setNewVessel({ ...newVessel, lat: e.target.value })}
                    placeholder="40.7667"
                    className="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50"
                  />
                </div>
                <div>
                  <label className="mb-2 block text-sm font-medium text-white/70">
                    Boylam (Lon) <span className="text-red-400">*</span>
                  </label>
                  <input
                    type="number"
                    step="0.0001"
                    value={newVessel.lon}
                    onChange={(e) => setNewVessel({ ...newVessel, lon: e.target.value })}
                    placeholder="29.9167"
                    className="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50"
                  />
                </div>
              </div>

              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">Durum</label>
                <select
                  value={newVessel.status}
                  onChange={(e) =>
                    setNewVessel({ ...newVessel, status: e.target.value as VesselPosition["status"] })
                  }
                  className="h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-4 text-sm outline-none focus:border-blue-500/50"
                >
                  <option value="unknown">Bilinmiyor</option>
                  <option value="moored">Yanaşık</option>
                  <option value="anchored">Demirde</option>
                  <option value="underway">Seyirde</option>
                </select>
              </div>

              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">Varış Noktası</label>
                <select
                  value={newVessel.destination}
                  onChange={(e) => setNewVessel({ ...newVessel, destination: e.target.value })}
                  className="h-11 w-full rounded-lg border border-white/10 bg-[#0B1220] px-4 text-sm outline-none focus:border-blue-500/50"
                >
                  <option value="">Seçiniz...</option>
                  {turkishPorts.map((port) => (
                    <option key={port.id} value={port.name}>
                      {port.name}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="mb-2 block text-sm font-medium text-white/70">ETA</label>
                <input
                  type="text"
                  value={newVessel.eta}
                  onChange={(e) => setNewVessel({ ...newVessel, eta: e.target.value })}
                  placeholder="Örn: 14:30 veya Yanaşık"
                  className="h-11 w-full rounded-lg border border-white/10 bg-white/5 px-4 text-sm outline-none placeholder:text-white/30 focus:border-blue-500/50"
                />
              </div>
            </div>

            <div className="mt-6 flex items-center justify-end gap-3">
              <button
                type="button"
                onClick={() => setShowAddModal(false)}
                className="rounded-lg px-4 py-2.5 text-sm font-medium text-white/60 transition hover:bg-white/10 hover:text-white"
              >
                Vazgeç
              </button>
              <button
                type="button"
                onClick={handleAddVessel}
                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500"
              >
                <Plus className="h-4 w-4" />
                Gemi Ekle
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
