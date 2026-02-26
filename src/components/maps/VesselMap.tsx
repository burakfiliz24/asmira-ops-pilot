"use client";

import { useEffect, useState } from "react";
import dynamic from "next/dynamic";
import { Anchor, Navigation, Clock, MapPin } from "lucide-react";
import { cn } from "@/lib/utils/cn";

// Leaflet'i client-side only olarak yükle (SSR sorunu için)
const MapContainer = dynamic(
  () => import("react-leaflet").then((mod) => mod.MapContainer),
  { ssr: false }
);
const TileLayer = dynamic(
  () => import("react-leaflet").then((mod) => mod.TileLayer),
  { ssr: false }
);
const Marker = dynamic(
  () => import("react-leaflet").then((mod) => mod.Marker),
  { ssr: false }
);
const Popup = dynamic(
  () => import("react-leaflet").then((mod) => mod.Popup),
  { ssr: false }
);

export type VesselPosition = {
  id: string;
  name: string;
  lat: number;
  lon: number;
  status: "moored" | "anchored" | "underway" | "unknown";
  port?: string;
  destination?: string;
  eta?: string;
  speed?: number;
  course?: number;
  lastUpdate?: Date;
};

type PortLocation = {
  id: string;
  name: string;
  lat: number;
  lon: number;
  region: string;
};

// Türkiye'deki önemli limanlar
const turkishPorts: PortLocation[] = [
  { id: "istanbul-ambarli", name: "Ambarlı Limanı", lat: 40.9833, lon: 28.6833, region: "Marmara" },
  { id: "istanbul-haydarpasa", name: "Haydarpaşa Limanı", lat: 40.9969, lon: 29.0178, region: "Marmara" },
  { id: "izmir-alsancak", name: "Alsancak Limanı", lat: 38.4397, lon: 27.1397, region: "Ege" },
  { id: "izmir-aliaga", name: "Aliağa Limanı", lat: 38.7833, lon: 26.9667, region: "Ege" },
  { id: "mersin", name: "Mersin Limanı", lat: 36.7833, lon: 34.6333, region: "Akdeniz" },
  { id: "iskenderun", name: "İskenderun Limanı", lat: 36.5833, lon: 36.1667, region: "Akdeniz" },
  { id: "izmit", name: "İzmit Körfezi", lat: 40.7667, lon: 29.9167, region: "Marmara" },
  { id: "gemlik", name: "Gemlik Limanı", lat: 40.4333, lon: 29.1500, region: "Marmara" },
  { id: "bandirma", name: "Bandırma Limanı", lat: 40.3500, lon: 27.9667, region: "Marmara" },
  { id: "samsun", name: "Samsun Limanı", lat: 41.2833, lon: 36.3333, region: "Karadeniz" },
  { id: "trabzon", name: "Trabzon Limanı", lat: 41.0000, lon: 39.7167, region: "Karadeniz" },
  { id: "antalya", name: "Antalya Limanı", lat: 36.8333, lon: 30.6167, region: "Akdeniz" },
];

// Türkiye merkez koordinatları
const TURKEY_CENTER: [number, number] = [39.0, 35.0];
const DEFAULT_ZOOM = 6;

type VesselMapProps = {
  vessels?: VesselPosition[];
  showPorts?: boolean;
  selectedVesselId?: string;
  onVesselClick?: (vessel: VesselPosition) => void;
  className?: string;
  height?: string;
};

export function VesselMap({
  vessels = [],
  showPorts = true,
  selectedVesselId,
  onVesselClick,
  className,
  height = "400px",
}: VesselMapProps) {
  const [isClient, setIsClient] = useState(false);
  const [leafletLoaded, setLeafletLoaded] = useState(false);

  useEffect(() => {
    setIsClient(true);
    // Leaflet CSS'i dinamik olarak yükle
    if (typeof window !== "undefined") {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
      link.integrity = "sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=";
      link.crossOrigin = "";
      document.head.appendChild(link);
      setLeafletLoaded(true);
    }
  }, []);

  if (!isClient || !leafletLoaded) {
    return (
      <div
        className={cn(
          "flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.02]",
          className
        )}
        style={{ height }}
      >
        <div className="flex flex-col items-center gap-2 text-white/50">
          <Navigation className="h-8 w-8 animate-pulse" />
          <span className="text-sm">Harita yükleniyor...</span>
        </div>
      </div>
    );
  }

  const statusColors = {
    moored: "bg-emerald-500",
    anchored: "bg-amber-500",
    underway: "bg-blue-500",
    unknown: "bg-gray-500",
  };

  const statusLabels = {
    moored: "Yanaşık",
    anchored: "Demirde",
    underway: "Seyirde",
    unknown: "Bilinmiyor",
  };

  return (
    <div className={cn("relative overflow-hidden rounded-xl", className)}>
      <MapContainer
        center={TURKEY_CENTER}
        zoom={DEFAULT_ZOOM}
        style={{ height, width: "100%" }}
        className="z-0"
      >
        <TileLayer
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />

        {/* Liman işaretleri */}
        {showPorts &&
          turkishPorts.map((port) => (
            <Marker key={port.id} position={[port.lat, port.lon]}>
              <Popup>
                <div className="min-w-[150px] p-1">
                  <div className="flex items-center gap-2">
                    <Anchor className="h-4 w-4 text-blue-600" />
                    <span className="font-semibold text-gray-900">{port.name}</span>
                  </div>
                  <div className="mt-1 text-xs text-gray-600">{port.region} Bölgesi</div>
                </div>
              </Popup>
            </Marker>
          ))}

        {/* Gemi işaretleri */}
        {vessels.map((vessel) => (
          <Marker
            key={vessel.id}
            position={[vessel.lat, vessel.lon]}
            eventHandlers={{
              click: () => onVesselClick?.(vessel),
            }}
          >
            <Popup>
              <div className="min-w-[200px] p-1">
                <div className="flex items-center justify-between">
                  <span className="font-bold text-gray-900">{vessel.name}</span>
                  <span
                    className={cn(
                      "rounded-full px-2 py-0.5 text-[10px] font-medium text-white",
                      statusColors[vessel.status]
                    )}
                  >
                    {statusLabels[vessel.status]}
                  </span>
                </div>

                {vessel.destination && (
                  <div className="mt-2 flex items-center gap-1.5 text-xs text-gray-600">
                    <MapPin className="h-3 w-3" />
                    <span>Varış: {vessel.destination}</span>
                  </div>
                )}

                {vessel.eta && (
                  <div className="mt-1 flex items-center gap-1.5 text-xs text-gray-600">
                    <Clock className="h-3 w-3" />
                    <span>ETA: {vessel.eta}</span>
                  </div>
                )}

                {vessel.speed !== undefined && (
                  <div className="mt-1 flex items-center gap-1.5 text-xs text-gray-600">
                    <Navigation className="h-3 w-3" />
                    <span>Hız: {vessel.speed} knot</span>
                  </div>
                )}
              </div>
            </Popup>
          </Marker>
        ))}
      </MapContainer>

      {/* Legend */}
      <div className="absolute bottom-3 left-3 z-[1000] rounded-lg border border-white/10 bg-slate-900/90 p-2 backdrop-blur-sm">
        <div className="text-[10px] font-semibold text-white/70 mb-1.5">Durum</div>
        <div className="flex flex-col gap-1">
          {Object.entries(statusLabels).map(([key, label]) => (
            <div key={key} className="flex items-center gap-1.5">
              <div className={cn("h-2 w-2 rounded-full", statusColors[key as keyof typeof statusColors])} />
              <span className="text-[10px] text-white/80">{label}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Vessel count */}
      {vessels.length > 0 && (
        <div className="absolute top-3 right-3 z-[1000] rounded-lg border border-white/10 bg-slate-900/90 px-2.5 py-1.5 backdrop-blur-sm">
          <div className="flex items-center gap-1.5">
            <Navigation className="h-3.5 w-3.5 text-blue-400" />
            <span className="text-xs font-medium text-white">{vessels.length} Gemi</span>
          </div>
        </div>
      )}
    </div>
  );
}

export { turkishPorts };
export type { PortLocation };
