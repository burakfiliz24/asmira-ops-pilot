"use client";

import { useMemo, useState, useEffect } from "react";
import {
  AlertTriangle,
  Clock,
  CheckCircle,
  FileText,
} from "lucide-react";
import { cn } from "@/lib/utils/cn";
import { useDocumentStore } from "@/store/documentStore";

function daysUntil(dateStr: string): number {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const target = new Date(dateStr);
  target.setHours(0, 0, 0, 0);
  return Math.ceil((target.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
}

type ExpiryItem = {
  owner: string;
  ownerType: "truck" | "trailer" | "driver";
  docLabel: string;
  expiryDate: string;
  daysLeft: number;
};

export default function ReportsPage() {
  const trucks = useDocumentStore((s) => s.trucks);
  const trailers = useDocumentStore((s) => s.trailers);
  const drivers = useDocumentStore((s) => s.drivers);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  // Süresi dolmuş / dolacak evraklar listesi
  const expiryList = useMemo((): ExpiryItem[] => {
    const items: ExpiryItem[] = [];

    for (const t of trucks) {
      for (const doc of t.documents) {
        if (doc.expiryDate) {
          const days = daysUntil(doc.expiryDate);
          if (days <= 30) {
            items.push({
              owner: t.plate,
              ownerType: "truck",
              docLabel: doc.label,
              expiryDate: doc.expiryDate,
              daysLeft: days,
            });
          }
        }
      }
    }

    for (const t of trailers) {
      for (const doc of t.documents) {
        if (doc.expiryDate) {
          const days = daysUntil(doc.expiryDate);
          if (days <= 30) {
            items.push({
              owner: t.plate,
              ownerType: "trailer",
              docLabel: doc.label,
              expiryDate: doc.expiryDate,
              daysLeft: days,
            });
          }
        }
      }
    }

    for (const d of drivers) {
      for (const doc of d.documents) {
        if (doc.expiryDate) {
          const days = daysUntil(doc.expiryDate);
          if (days <= 30) {
            items.push({
              owner: d.name,
              ownerType: "driver",
              docLabel: doc.label,
              expiryDate: doc.expiryDate,
              daysLeft: days,
            });
          }
        }
      }
    }

    items.sort((a, b) => a.daysLeft - b.daysLeft);
    return items;
  }, [trucks, trailers, drivers]);

  if (!mounted) return null;

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-auto rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">RAPORLAR</div>
            <div className="text-3xl font-black tracking-tight">Evrak Takibi</div>
            <div className="mt-1 text-xs text-white/50">
              Süresi dolan ve dolacak evrakların özeti
            </div>
          </div>
        </div>

        <div className="p-4 sm:p-6">
          {/* Süresi Dolmuş/Dolacak Evraklar */}
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-4">
            <div className="mb-4 flex items-center justify-between">
              <div className="flex items-center gap-2 text-sm font-semibold">
                <FileText className="h-4 w-4 text-amber-400" />
                Süresi Dolan / Dolacak Evraklar
                {expiryList.length > 0 && (
                  <span className="rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] text-amber-400">
                    {expiryList.length}
                  </span>
                )}
              </div>
            </div>

            {expiryList.length === 0 ? (
              <div className="flex flex-col items-center py-8 text-center">
                <CheckCircle className="mb-2 h-8 w-8 text-emerald-400/40" />
                <div className="text-sm text-white/40">Tüm evraklar güncel</div>
                <div className="mt-1 text-xs text-white/25">30 gün içinde süresi dolacak evrak yok</div>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs">
                  <thead>
                    <tr className="border-b border-white/10 text-white/40">
                      <th className="pb-2 pr-4 font-medium">Sahip</th>
                      <th className="pb-2 pr-4 font-medium">Tür</th>
                      <th className="pb-2 pr-4 font-medium">Evrak</th>
                      <th className="pb-2 pr-4 font-medium">Son Tarih</th>
                      <th className="pb-2 font-medium">Durum</th>
                    </tr>
                  </thead>
                  <tbody>
                    {expiryList.map((item, i) => (
                      <tr key={`${item.owner}_${item.docLabel}_${i}`} className="border-b border-white/5">
                        <td className="py-2 pr-4 font-medium text-white">{item.owner}</td>
                        <td className="py-2 pr-4 text-white/50">
                          {item.ownerType === "truck" ? "Çekici" : item.ownerType === "trailer" ? "Dorse" : "Şoför"}
                        </td>
                        <td className="py-2 pr-4 text-white/60">{item.docLabel}</td>
                        <td className="py-2 pr-4 text-white/50">{item.expiryDate}</td>
                        <td className="py-2">
                          {item.daysLeft < 0 ? (
                            <span className="inline-flex items-center gap-1 rounded-full bg-red-500/20 px-2 py-0.5 text-[10px] font-semibold text-red-400">
                              <AlertTriangle className="h-3 w-3" />
                              {Math.abs(item.daysLeft)} gün geçmiş
                            </span>
                          ) : item.daysLeft === 0 ? (
                            <span className="inline-flex items-center gap-1 rounded-full bg-red-500/20 px-2 py-0.5 text-[10px] font-semibold text-red-400">
                              Bugün doluyor
                            </span>
                          ) : (
                            <span className={cn(
                              "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold",
                              item.daysLeft <= 7
                                ? "bg-red-500/20 text-red-400"
                                : "bg-amber-500/20 text-amber-400"
                            )}>
                              <Clock className="h-3 w-3" />
                              {item.daysLeft} gün kaldı
                            </span>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
