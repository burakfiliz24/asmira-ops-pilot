"use client";

import { useMemo, useState, useEffect } from "react";
import { Ship, MapPin, Calendar, ChevronLeft, ChevronRight, Fuel } from "lucide-react";
import { cn } from "@/lib/utils/cn";
import { useOperationStore, type SupplyOperation } from "@/store/operationStore";

const MONTHS_TR = [
  "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran",
  "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık",
];

const STATUS_MAP: Record<string, { label: string; color: string }> = {
  planned: { label: "Planlandı", color: "bg-blue-500/20 text-blue-400" },
  approaching: { label: "Yaklaşıyor", color: "bg-amber-500/20 text-amber-400" },
  active: { label: "Aktif", color: "bg-emerald-500/20 text-emerald-400" },
  completed: { label: "Tamamlandı", color: "bg-green-500/20 text-green-400" },
  cancelled: { label: "İptal", color: "bg-red-500/20 text-red-400" },
};

function formatDate(dateStr: string): string {
  const d = new Date(dateStr);
  return d.toLocaleDateString("tr-TR", { day: "2-digit", month: "2-digit", year: "numeric" });
}

export default function OperationsReportPage() {
  const operations = useOperationStore((s) => s.operations);
  const [mounted, setMounted] = useState(false);

  const now = new Date();
  const [selectedYear, setSelectedYear] = useState(now.getFullYear());
  const [selectedMonth, setSelectedMonth] = useState(now.getMonth());

  useEffect(() => {
    setMounted(true);
  }, []);

  // Seçili aya ait operasyonlar (tarih sıralı)
  const monthlyOps = useMemo(() => {
    return operations
      .filter((op) => {
        const d = new Date(op.date);
        return d.getFullYear() === selectedYear && d.getMonth() === selectedMonth;
      })
      .sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
  }, [operations, selectedYear, selectedMonth]);

  // Aylık istatistikler
  const stats = useMemo(() => {
    const total = monthlyOps.length;
    const totalMT = monthlyOps.filter((o) => o.unit === "MT").reduce((sum, o) => sum + (o.quantity || 0), 0);
    const totalL = monthlyOps.filter((o) => o.unit === "L").reduce((sum, o) => sum + (o.quantity || 0), 0);
    const shipCount = monthlyOps.filter((o) => o.vesselType !== "yacht").length;
    const yachtCount = monthlyOps.filter((o) => o.vesselType === "yacht").length;
    return { total, totalMT, totalL, shipCount, yachtCount };
  }, [monthlyOps]);

  // Yıllar arası geçiş
  const goToPrevMonth = () => {
    if (selectedMonth === 0) {
      setSelectedMonth(11);
      setSelectedYear((y) => y - 1);
    } else {
      setSelectedMonth((m) => m - 1);
    }
  };
  const goToNextMonth = () => {
    if (selectedMonth === 11) {
      setSelectedMonth(0);
      setSelectedYear((y) => y + 1);
    } else {
      setSelectedMonth((m) => m + 1);
    }
  };

  if (!mounted) return null;

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-auto rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="relative flex flex-none flex-wrap items-center justify-between gap-3 bg-gradient-to-b from-cyan-500/5 to-transparent px-6 py-4">
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/60 via-cyan-400/30 to-transparent" />
          <div>
            <div className="text-sm font-light tracking-[0.2em] text-slate-400">RAPORLAR</div>
            <div className="text-3xl font-black tracking-tight">İkmal Raporu</div>
            <div className="mt-1 text-xs text-white/50">
              Aylık ikmal operasyonları özeti
            </div>
          </div>
        </div>

        {/* Month Selector */}
        <div className="flex flex-none items-center justify-between border-b border-white/10 px-6 py-3">
          <button
            type="button"
            onClick={goToPrevMonth}
            className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-white/[0.03] text-white/60 transition hover:bg-white/[0.08] hover:text-white"
          >
            <ChevronLeft className="h-4 w-4" />
          </button>

          <div className="flex items-center gap-2">
            {/* Month tabs */}
            <div className="flex gap-1 overflow-x-auto">
              {MONTHS_TR.map((m, i) => (
                <button
                  key={m}
                  type="button"
                  onClick={() => setSelectedMonth(i)}
                  className={cn(
                    "rounded-lg px-3 py-1.5 text-xs font-medium transition-all",
                    selectedMonth === i
                      ? "bg-cyan-500/20 text-cyan-400 shadow-[0_0_8px_rgba(6,182,212,0.2)]"
                      : "text-white/40 hover:bg-white/[0.04] hover:text-white/70"
                  )}
                >
                  {m}
                </button>
              ))}
            </div>

            {/* Year */}
            <span className="ml-2 text-sm font-bold text-white/80">{selectedYear}</span>
          </div>

          <button
            type="button"
            onClick={goToNextMonth}
            className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-white/[0.03] text-white/60 transition hover:bg-white/[0.08] hover:text-white"
          >
            <ChevronRight className="h-4 w-4" />
          </button>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-2 gap-3 px-6 pt-4 sm:grid-cols-5">
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
            <div className="text-2xl font-black text-white">{stats.total}</div>
            <div className="text-[10px] font-medium text-white/40">Toplam İkmal</div>
          </div>
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
            <div className="text-2xl font-black text-cyan-400">{stats.totalMT.toFixed(1)}</div>
            <div className="text-[10px] font-medium text-white/40">Toplam MT</div>
          </div>
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
            <div className="text-2xl font-black text-amber-400">{stats.totalL.toLocaleString("tr-TR")}</div>
            <div className="text-[10px] font-medium text-white/40">Toplam L</div>
          </div>
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
            <div className="text-2xl font-black text-blue-400">{stats.shipCount}</div>
            <div className="text-[10px] font-medium text-white/40">Gemi</div>
          </div>
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-3 text-center">
            <div className="text-2xl font-black text-purple-400">{stats.yachtCount}</div>
            <div className="text-[10px] font-medium text-white/40">Yat</div>
          </div>
        </div>

        {/* Operations Table */}
        <div className="flex-1 p-4 sm:p-6">
          <div className="rounded-xl border border-white/10 bg-white/[0.02] p-4">
            <div className="mb-4 flex items-center gap-2 text-sm font-semibold">
              <Fuel className="h-4 w-4 text-cyan-400" />
              {MONTHS_TR[selectedMonth]} {selectedYear} İkmalleri
              {monthlyOps.length > 0 && (
                <span className="rounded-full bg-cyan-500/20 px-2 py-0.5 text-[10px] text-cyan-400">
                  {monthlyOps.length}
                </span>
              )}
            </div>

            {monthlyOps.length === 0 ? (
              <div className="flex flex-col items-center py-12 text-center">
                <Ship className="mb-3 h-10 w-10 text-white/15" />
                <div className="text-sm text-white/40">Bu ayda ikmal kaydı yok</div>
                <div className="mt-1 text-xs text-white/25">
                  Dashboard takviminden ikmal ekleyebilirsiniz
                </div>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs">
                  <thead>
                    <tr className="border-b border-white/10 text-white/40">
                      <th className="pb-2.5 pr-4 font-medium">
                        <div className="flex items-center gap-1.5">
                          <Calendar className="h-3 w-3" /> Tarih
                        </div>
                      </th>
                      <th className="pb-2.5 pr-4 font-medium">Tür</th>
                      <th className="pb-2.5 pr-4 font-medium">
                        <div className="flex items-center gap-1.5">
                          <Ship className="h-3 w-3" /> Gemi / Yat
                        </div>
                      </th>
                      <th className="pb-2.5 pr-4 font-medium">IMO</th>
                      <th className="pb-2.5 pr-4 font-medium">
                        <div className="flex items-center gap-1.5">
                          <MapPin className="h-3 w-3" /> Liman
                        </div>
                      </th>
                      <th className="pb-2.5 pr-4 font-medium">Dolum Yeri</th>
                      <th className="pb-2.5 pr-4 font-medium">Miktar</th>
                    </tr>
                  </thead>
                  <tbody>
                    {monthlyOps.map((op) => {
                      const st = STATUS_MAP[op.status] ?? STATUS_MAP.planned;
                      return (
                        <tr key={op.id} className="border-b border-white/5 transition-colors hover:bg-white/[0.02]">
                          <td className="py-2.5 pr-4 font-medium text-white/70">{formatDate(op.date)}</td>
                          <td className="py-2.5 pr-4">
                            <span className={cn(
                              "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold",
                              op.vesselType === "yacht"
                                ? "bg-purple-500/20 text-purple-400"
                                : "bg-blue-500/20 text-blue-400"
                            )}>
                              {op.vesselType === "yacht" ? "⛵ Yat" : "🚢 Gemi"}
                            </span>
                          </td>
                          <td className={cn("py-2.5 pr-4 font-semibold", op.vesselType === "yacht" ? "text-purple-300" : "text-white")}>{op.vesselName}</td>
                          <td className="py-2.5 pr-4 text-white/40">{op.imoNumber || "—"}</td>
                          <td className="py-2.5 pr-4 text-white/60">{op.port}</td>
                          <td className="py-2.5 pr-4 text-white/50">{op.loadingPlace || "—"}</td>
                          <td className="py-2.5 pr-4">
                            <span className="font-semibold text-cyan-400">{op.quantity}</span>
                            <span className="ml-1 text-white/40">{op.unit}</span>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                  <tfoot>
                    <tr className="border-t border-white/10">
                      <td colSpan={5} className="py-3 pr-4 text-right text-xs font-semibold text-white/50">
                        Toplam:
                      </td>
                      <td className="py-3 pr-4">
                        <div className="flex flex-col gap-1">
                          {monthlyOps.some((o) => o.unit === "MT") && (
                            <div>
                              <span className="text-sm font-bold text-cyan-400">
                                {monthlyOps.filter((o) => o.unit === "MT").reduce((s, o) => s + (o.quantity || 0), 0).toFixed(1)}
                              </span>
                              <span className="ml-1 text-xs text-white/40">MT</span>
                            </div>
                          )}
                          {monthlyOps.some((o) => o.unit === "L") && (
                            <div>
                              <span className="text-sm font-bold text-amber-400">
                                {monthlyOps.filter((o) => o.unit === "L").reduce((s, o) => s + (o.quantity || 0), 0).toLocaleString("tr-TR")}
                              </span>
                              <span className="ml-1 text-xs text-white/40">L</span>
                            </div>
                          )}
                        </div>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
