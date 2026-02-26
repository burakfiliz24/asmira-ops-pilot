"use client";

import { useEffect, useState, useMemo } from "react";
import { FileWarning, Clock, ChevronRight } from "lucide-react";
import { cn } from "@/lib/utils/cn";
import { useDocumentStore } from "@/store/documentStore";
import Link from "next/link";

interface ExpiringDocument {
  vehicleOrDriverName: string;
  documentName: string;
  expiryDate: string;
  daysLeft: number;
  type: "vehicle" | "driver" | "supplier";
  category?: "asmira" | "supplier";
}

export function DocumentExpiryAlert() {
  const trucks = useDocumentStore((state) => state.trucks);
  const trailers = useDocumentStore((state) => state.trailers);
  const vehicleSets = useDocumentStore((state) => state.vehicleSets);
  const drivers = useDocumentStore((state) => state.drivers);
  const [isExpanded, setIsExpanded] = useState(true);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  // vehicles'ı useMemo ile hesapla
  const vehicles = useMemo(() => {
    return vehicleSets.map(set => {
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

  const expiringDocs = useMemo(() => {
    if (!mounted) return [];

    const today = new Date();
    const warningDays = 15; // 15 gün öncesinden uyar
    const expiring: ExpiringDocument[] = [];

    // Check vehicle documents (Asmira) - both vehicle and trailer
    vehicles.forEach((vehicle) => {
      // Araç evrakları
      vehicle.vehicleDocuments.forEach((doc) => {
        if (doc.expiryDate) {
          const expiryDate = new Date(doc.expiryDate);
          const diffTime = expiryDate.getTime() - today.getTime();
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

          if (diffDays <= warningDays && diffDays >= -7) {
            expiring.push({
              vehicleOrDriverName: `${vehicle.vehiclePlate} (Araç)`,
              documentName: doc.label,
              expiryDate: doc.expiryDate,
              daysLeft: diffDays,
              type: "vehicle",
              category: vehicle.category,
            });
          }
        }
      });
      // Dorse evrakları
      vehicle.trailerDocuments.forEach((doc) => {
        if (doc.expiryDate) {
          const expiryDate = new Date(doc.expiryDate);
          const diffTime = expiryDate.getTime() - today.getTime();
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

          if (diffDays <= warningDays && diffDays >= -7) {
            expiring.push({
              vehicleOrDriverName: `${vehicle.trailerPlate} (Dorse)`,
              documentName: doc.label,
              expiryDate: doc.expiryDate,
              daysLeft: diffDays,
              type: "vehicle",
              category: vehicle.category,
            });
          }
        }
      });
    });

    // Check driver documents
    drivers.forEach((driver) => {
      driver.documents.forEach((doc) => {
        if (doc.expiryDate) {
          const expiryDate = new Date(doc.expiryDate);
          const diffTime = expiryDate.getTime() - today.getTime();
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

          if (diffDays <= warningDays && diffDays >= -7) {
            expiring.push({
              vehicleOrDriverName: driver.name,
              documentName: doc.label,
              expiryDate: doc.expiryDate,
              daysLeft: diffDays,
              type: "driver",
            });
          }
        }
      });
    });

    // Sort by days left (most urgent first)
    expiring.sort((a, b) => a.daysLeft - b.daysLeft);
    return expiring;
  }, [vehicles, drivers, mounted]);

  if (!mounted || expiringDocs.length === 0) return null;

  const expiredCount = expiringDocs.filter((d) => d.daysLeft < 0).length;
  const urgentCount = expiringDocs.filter((d) => d.daysLeft >= 0 && d.daysLeft <= 7).length;

  return (
    <div className="mb-6 overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-red-500/5">
      <button
        type="button"
        onClick={() => setIsExpanded(!isExpanded)}
        className="flex w-full items-center justify-between px-4 py-3 text-left transition hover:bg-white/5"
      >
        <div className="flex items-center gap-3">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500/25 to-red-500/15 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
            <FileWarning className="h-5 w-5 text-amber-400" />
          </div>
          <div>
            <div className="flex items-center gap-2 font-bold text-white">
              Evrak Süresi Uyarısı
              <span className="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-semibold text-amber-400">
                {expiringDocs.length}
              </span>
            </div>
            <div className="text-xs text-white/50">
              {expiredCount > 0 && (
                <span className="text-red-400">{expiredCount} süresi dolmuş</span>
              )}
              {expiredCount > 0 && urgentCount > 0 && " • "}
              {urgentCount > 0 && (
                <span className="text-amber-400">{urgentCount} acil</span>
              )}
            </div>
          </div>
        </div>
        <ChevronRight
          className={cn(
            "h-5 w-5 text-white/40 transition-transform",
            isExpanded && "rotate-90"
          )}
        />
      </button>

      {isExpanded && (
        <div className="border-t border-white/10 px-4 py-3">
          <div className="max-h-48 space-y-2 overflow-y-auto">
            {expiringDocs.map((doc, idx) => (
              <div
                key={idx}
                className={cn(
                  "flex items-center justify-between rounded-lg border p-3",
                  doc.daysLeft < 0
                    ? "border-red-500/30 bg-red-500/10"
                    : doc.daysLeft <= 7
                    ? "border-amber-500/30 bg-amber-500/10"
                    : "border-white/10 bg-white/5"
                )}
              >
                <div className="flex items-center gap-3">
                  <Clock
                    className={cn(
                      "h-4 w-4",
                      doc.daysLeft < 0
                        ? "text-red-400"
                        : doc.daysLeft <= 7
                        ? "text-amber-400"
                        : "text-white/40"
                    )}
                  />
                  <div>
                    <div className="text-sm font-medium">
                      {doc.vehicleOrDriverName} - {doc.documentName}
                    </div>
                    <div className="text-xs text-white/50">
                      {doc.type === "vehicle" ? "Araç" : doc.type === "supplier" ? "Tedarikçi" : "Şoför"} •{" "}
                      {new Date(doc.expiryDate).toLocaleDateString("tr-TR")}
                    </div>
                  </div>
                </div>
                <div
                  className={cn(
                    "rounded-full px-2 py-1 text-xs font-semibold",
                    doc.daysLeft < 0
                      ? "bg-red-500/20 text-red-400"
                      : doc.daysLeft <= 7
                      ? "bg-amber-500/20 text-amber-400"
                      : "bg-white/10 text-white/60"
                  )}
                >
                  {doc.daysLeft < 0
                    ? `${Math.abs(doc.daysLeft)} gün geçti`
                    : doc.daysLeft === 0
                    ? "Bugün"
                    : `${doc.daysLeft} gün`}
                </div>
              </div>
            ))}
          </div>
          <div className="mt-3 flex gap-2">
            <Link
              href="/vehicle-documents/asmira"
              className="flex-1 rounded-lg border border-white/10 bg-white/5 py-2 text-center text-xs font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
            >
              Araç Evrakları
            </Link>
            <Link
              href="/driver-documents"
              className="flex-1 rounded-lg border border-white/10 bg-white/5 py-2 text-center text-xs font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
            >
              Şoför Evrakları
            </Link>
          </div>
        </div>
      )}
    </div>
  );
}
