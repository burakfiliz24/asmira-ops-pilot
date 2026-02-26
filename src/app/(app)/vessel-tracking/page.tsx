"use client";

import { useState, useMemo } from "react";
import Link from "next/link";
import {
  Navigation,
  Ship,
  MapPin,
  Clock,
  Search,
  ExternalLink,
  Fuel,
  Plus,
} from "lucide-react";
import { VesselMap, type VesselPosition } from "@/components/maps/VesselMap";
import { cn } from "@/lib/utils/cn";
import { useOperationStore } from "@/store/operationStore";

// Liman koordinatları
const portCoordinates: Record<string, { lat: number; lon: number }> = {
  "izmit": { lat: 40.7667, lon: 29.9167 },
  "İzmit": { lat: 40.7667, lon: 29.9167 },
  "ambarlı": { lat: 40.9833, lon: 28.6833 },
  "Ambarlı": { lat: 40.9833, lon: 28.6833 },
  "aliağa": { lat: 38.7833, lon: 26.9667 },
  "Aliağa": { lat: 38.7833, lon: 26.9667 },
  "mersin": { lat: 36.7833, lon: 34.6333 },
  "Mersin": { lat: 36.7833, lon: 34.6333 },
  "iskenderun": { lat: 36.5833, lon: 36.1667 },
  "İskenderun": { lat: 36.5833, lon: 36.1667 },
  "gemlik": { lat: 40.4333, lon: 29.1500 },
  "Gemlik": { lat: 40.4333, lon: 29.1500 },
  "bandırma": { lat: 40.3500, lon: 27.9667 },
  "Bandırma": { lat: 40.3500, lon: 27.9667 },
  "samsun": { lat: 41.2833, lon: 36.3333 },
  "Samsun": { lat: 41.2833, lon: 36.3333 },
  "trabzon": { lat: 41.0000, lon: 39.7167 },
  "Trabzon": { lat: 41.0000, lon: 39.7167 },
  "antalya": { lat: 36.8333, lon: 30.6167 },
  "Antalya": { lat: 36.8333, lon: 30.6167 },
  "izmir": { lat: 38.4397, lon: 27.1397 },
  "İzmir": { lat: 38.4397, lon: 27.1397 },
};

const statusConfig = {
  moored: { label: "Yanaşık", color: "bg-emerald-500", textColor: "text-emerald-400" },
  anchored: { label: "Demirde", color: "bg-amber-500", textColor: "text-amber-400" },
  underway: { label: "Seyirde", color: "bg-blue-500", textColor: "text-blue-400" },
  unknown: { label: "Bilinmiyor", color: "bg-gray-500", textColor: "text-gray-400" },
};

// Operation status to vessel status mapping
const opStatusToVesselStatus: Record<string, VesselPosition["status"]> = {
  planned: "underway",
  approaching: "underway",
  active: "moored",
  completed: "moored",
  cancelled: "unknown",
};

type VesselWithIMO = VesselPosition & { imoNumber?: string };

export default function VesselTrackingPage() {
  const { operations } = useOperationStore();
  const [selectedVessel, setSelectedVessel] = useState<VesselWithIMO | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [statusFilter, setStatusFilter] = useState<string>("all");

  // Dashboard operasyonlarından gemi listesi oluştur
  const vessels = useMemo((): VesselWithIMO[] => {
    return operations.map((op) => {
      const portCoord = portCoordinates[op.port] || { lat: 40.0, lon: 29.0 };
      // Küçük rastgele offset ekle (aynı limandaki gemiler üst üste binmesin)
      const offset = {
        lat: (Math.random() - 0.5) * 0.1,
        lon: (Math.random() - 0.5) * 0.1,
      };
      
      return {
        id: op.id,
        name: op.vesselName.toUpperCase(),
        lat: portCoord.lat + offset.lat,
        lon: portCoord.lon + offset.lon,
        status: opStatusToVesselStatus[op.status] || "unknown",
        port: op.port,
        destination: op.port,
        eta: op.date,
        imoNumber: op.imoNumber,
      };
    });
  }, [operations]);

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

  function getVesselTrackingUrl(vesselName: string, imoNumber?: string) {
    if (imoNumber) {
      return `https://www.vesselfinder.com/vessels?name=${encodeURIComponent(imoNumber)}`;
    }
    const cleanName = vesselName
      .replace(/^M\/[TVS]\s*/i, "")
      .replace(/^MT\s*/i, "")
      .replace(/^MV\s*/i, "")
      .trim();
    return `https://www.vesselfinder.com/vessels?name=${encodeURIComponent(cleanName)}`;
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
            <div className="text-3xl font-black tracking-tight">Dashboard Gemileri</div>
            <div className="text-xs text-white/50 mt-1">Dashboard'a eklenen operasyonlardaki gemiler burada görünür</div>
          </div>

          <Link
            href="/dashboard"
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.25)] transition-all hover:from-blue-500 hover:to-blue-600"
          >
            <Plus className="h-4 w-4" />
            İkmal Ekle
          </Link>
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
                      <a
                        href={getVesselTrackingUrl(vessel.name, vessel.imoNumber)}
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={(e) => e.stopPropagation()}
                        className="rounded p-1 text-cyan-400 opacity-0 transition hover:bg-cyan-500/20 group-hover:opacity-100"
                        title="VesselFinder'da Aç"
                      >
                        <ExternalLink className="h-3.5 w-3.5" />
                      </a>
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
    </div>
  );
}
