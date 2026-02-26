"use client";

import { useMemo, useState, useEffect, useCallback, useRef, type DragEvent, type MouseEvent } from "react";
import addDays from "date-fns/addDays";
import format from "date-fns/format";
import isSameDay from "date-fns/isSameDay";
import isSameMonth from "date-fns/isSameMonth";
import startOfMonth from "date-fns/startOfMonth";
import startOfWeek from "date-fns/startOfWeek";
import {
  ChevronLeft,
  ChevronRight,
  Plus,
  X,
  Anchor,
  Fuel,
  CheckCircle,
  XCircle,
  AlertTriangle,
  Wifi,
  WifiOff,
  MapPin,
  ExternalLink,
} from "lucide-react";
import Button from "@/components/ui/Button";
import { cn } from "@/lib/utils/cn";
import { DocumentExpiryAlert } from "@/components/ui/DocumentExpiryAlert";
import { useRealtime } from "@/contexts/RealtimeContext";

type Unit = "MT" | "L";

type OperationStatus = "planned" | "approaching" | "active" | "completed" | "cancelled";

type ContextMenuState = {
  visible: boolean;
  x: number;
  y: number;
  operationId: string | null;
};

const statusConfig: Record<OperationStatus, { label: string; icon: typeof Anchor; stripe: string }> = {
  approaching: {
    label: "⚓ Yanaşıyor",
    icon: Anchor,
    stripe: "before:bg-amber-400 before:shadow-[0_0_14px_rgba(251,191,36,0.5)]",
  },
  active: {
    label: "⛽ İkmal Başladı",
    icon: Fuel,
    stripe: "before:bg-blue-400 before:shadow-[0_0_14px_rgba(59,130,246,0.55)]",
  },
  completed: {
    label: "✅ Tamamlandı",
    icon: CheckCircle,
    stripe: "before:bg-emerald-400 before:shadow-[0_0_14px_rgba(52,211,153,0.45)]",
  },
  cancelled: {
    label: "❌ İptal Edildi",
    icon: XCircle,
    stripe: "before:bg-rose-400 before:shadow-[0_0_14px_rgba(251,113,133,0.5)]",
  },
  planned: {
    label: "📋 Planlandı",
    icon: Anchor,
    stripe: "before:bg-slate-400 before:shadow-[0_0_14px_rgba(148,163,184,0.4)]",
  },
};

function ContextMenu({
  state,
  onClose,
  onStatusChange,
}: {
  state: ContextMenuState;
  onClose: () => void;
  onStatusChange: (operationId: string, status: OperationStatus) => void;
}) {
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function handleClickOutside(e: globalThis.MouseEvent) {
      if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
        onClose();
      }
    }
    function handleEscape(e: KeyboardEvent) {
      if (e.key === "Escape") onClose();
    }
    if (state.visible) {
      document.addEventListener("mousedown", handleClickOutside);
      document.addEventListener("keydown", handleEscape);
    }
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
      document.removeEventListener("keydown", handleEscape);
    };
  }, [state.visible, onClose]);

  if (!state.visible || !state.operationId) return null;

  const menuItems: { status: OperationStatus; color: string }[] = [
    { status: "completed", color: "hover:bg-emerald-500/20 text-emerald-300" },
    { status: "cancelled", color: "hover:bg-rose-500/20 text-rose-300" },
  ];

  return (
    <div
      ref={menuRef}
      className="fixed z-[100] min-w-[180px] overflow-hidden rounded-xl border border-white/10 bg-[#0d1526]/80 p-1 shadow-[0_8px_32px_rgba(0,0,0,0.5)] backdrop-blur-xl"
      style={{ left: state.x, top: state.y }}
    >
      <div className="px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-white/40">
        Durum Güncelle
      </div>
      {menuItems.map(({ status, color }) => {
        const config = statusConfig[status];
        const Icon = config.icon;
        return (
          <button
            key={status}
            type="button"
            onClick={() => {
              onStatusChange(state.operationId!, status);
              onClose();
            }}
            className={cn(
              "flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-[13px] font-medium transition-colors",
              color
            )}
          >
            <Icon className="h-4 w-4" />
            {config.label}
          </button>
        );
      })}
    </div>
  );
}

type SupplyOperation = {
  id: string;
  vesselName: string;
  quantity: number;
  unit: Unit;
  loadingPlace?: string;
  port: string;
  date: string; // YYYY-MM-DD
  status: OperationStatus;
  driverName: string;
  driverPhone: string;
  agentNote: string;
};

const monthNamesTr = [
  "OCAK",
  "ŞUBAT",
  "MART",
  "NİSAN",
  "MAYIS",
  "HAZİRAN",
  "TEMMUZ",
  "AĞUSTOS",
  "EYLÜL",
  "EKİM",
  "KASIM",
  "ARALIK",
];

const weekDaysTr = ["PZT", "SAL", "ÇAR", "PER", "CUM", "CMT", "PAZ"];

function toISODate(d: Date) {
  return format(d, "yyyy-MM-dd");
}

function upperTr(s: string) {
  return s.toLocaleUpperCase("tr-TR");
}

function nextId() {
  return `op_${Math.random().toString(16).slice(2)}_${Date.now()}`;
}

function getVesselTrackingUrl(vesselName: string) {
  const cleanName = vesselName
    .replace(/^M\/[TVS]\s*/i, "")
    .replace(/^MT\s*/i, "")
    .replace(/^MV\s*/i, "")
    .trim();
  return `https://www.vesselfinder.com/vessels?name=${encodeURIComponent(cleanName)}`;
}

function OperationCard({
  op,
  onDelete,
  onDragStart,
  onDragEnd,
  onContextMenu,
  className,
}: {
  op: SupplyOperation;
  onDelete: (id: string) => void;
  onDragStart: (e: DragEvent<HTMLDivElement>, id: string) => void;
  onDragEnd: () => void;
  onContextMenu: (e: MouseEvent<HTMLDivElement>, id: string) => void;
  className?: string;
}) {
  const statusStripe = statusConfig[op.status]?.stripe ?? statusConfig.planned.stripe;
  const statusText = op.status === "completed" || op.status === "cancelled" ? "line-through opacity-70" : "";
  const vesselUpper = upperTr(op.vesselName);
  const portUpper = upperTr(op.port || "-");
  const unitUpper = op.unit === "MT" ? "TON" : upperTr(op.unit);

  return (
    <div
      draggable
      onDragStart={(e) => onDragStart(e, op.id)}
      onDragEnd={onDragEnd}
      onClick={(e) => e.stopPropagation()}
      onContextMenu={(e) => onContextMenu(e, op.id)}
      className={cn(
        "group relative z-10 flex w-full cursor-grab select-none flex-col rounded-lg border border-white/10 bg-white/[0.06] px-2 py-2 2xl:px-3 2xl:py-3 text-white shadow-[0_0_0_1px_rgba(255,255,255,0.06),0_8px_20px_rgba(0,0,0,0.25)] backdrop-blur-md transition active:cursor-grabbing",
        "before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:rounded-l-xl before:transition-all before:duration-300 before:content-['']",
        statusStripe,
        "hover:bg-white/[0.08]",
        className
      )}
    >
      {/* Delete button */}
      <button
        type="button"
        onClick={(e) => {
          e.stopPropagation();
          onDelete(op.id);
        }}
        className="absolute right-1 top-0.5 flex h-5 w-5 items-center justify-center rounded-md border border-red-500/30 bg-red-500/20 text-red-100 opacity-0 transition group-hover:opacity-100 hover:bg-red-500/30"
        aria-label="Sil"
      >
        <X className="h-3 w-3" />
      </button>

      {/* Track on map button */}
      <a
        href={getVesselTrackingUrl(op.vesselName)}
        target="_blank"
        rel="noopener noreferrer"
        onClick={(e) => e.stopPropagation()}
        className="absolute right-1 bottom-0.5 flex h-5 w-5 items-center justify-center rounded-md border border-cyan-500/30 bg-cyan-500/20 text-cyan-100 opacity-0 transition group-hover:opacity-100 hover:bg-cyan-500/30"
        title="Gemiyi Haritada Gör"
      >
        <MapPin className="h-3 w-3" />
      </a>

      <div className={cn("flex flex-col gap-y-0.5", statusText)}>
        <div className="text-[11px] font-bold uppercase leading-tight tracking-tight line-clamp-2 sm:text-[13px] 2xl:text-[15px]">
          {vesselUpper}
        </div>
        <div className="text-[10px] leading-tight text-white/70 sm:text-[12px] 2xl:text-[14px]">
          <div className="line-clamp-1">{op.loadingPlace ? `${upperTr(op.loadingPlace)} → ${portUpper}` : portUpper}</div>
        </div>
        <div className="text-[10px] font-semibold leading-tight text-blue-400 sm:text-[12px] 2xl:text-[14px]">
          {op.quantity} {unitUpper}
        </div>
      </div>
    </div>
  );
}

export default function DashboardPage() {
  const baseYear = 2026;
  const [monthIndex, setMonthIndex] = useState<number>(0);
  const [open, setOpen] = useState(false);
  const [dragOverDate, setDragOverDate] = useState<string | null>(null);
  const [pendingOperations, setPendingOperations] = useState<SupplyOperation[] | null>(
    null
  );
  const [contextMenu, setContextMenu] = useState<ContextMenuState>({
    visible: false,
    x: 0,
    y: 0,
    operationId: null,
  });
  const [realtimeConnected, setRealtimeConnected] = useState(false);

  const { broadcast, subscribe } = useRealtime();

  const currentMonth = useMemo(() => new Date(baseYear, monthIndex, 1), [baseYear, monthIndex]);

  const [operations, setOperations] = useState<SupplyOperation[]>([
    {
      id: "op_seed_1",
      vesselName: "M/T Asmira Star",
      quantity: 850,
      unit: "MT",
      port: "İzmit",
      date: "2026-01-29",
      status: "active",
      driverName: "Mehmet Yılmaz",
      driverPhone: "+90 5xx xxx xx xx",
      agentNote: "Pilotaj teyidi bekleniyor. ETA 09:30.",
    },
    {
      id: "op_seed_2",
      vesselName: "M/V Bosphorus",
      quantity: 420.5,
      unit: "MT",
      port: "Ambarlı",
      date: "2026-01-28",
      status: "planned",
      driverName: "Ahmet Kaya",
      driverPhone: "+90 5xx xxx xx xx",
      agentNote: "Terminal slot: 14:00-16:00.",
    },
  ]);

  const [form, setForm] = useState({
    vesselName: "",
    quantity: "",
    unit: "MT" as Unit,
    loadingPlace: "",
    port: "",
    date: "2026-01-01",
  });

  const title = useMemo(() => {
    return `${monthNamesTr[currentMonth.getMonth()]} ${currentMonth.getFullYear()}`;
  }, [currentMonth]);

  // Realtime senkronizasyon
  useEffect(() => {
    setRealtimeConnected(true);
    
    const unsubscribe = subscribe((event) => {
      if (!event.type.startsWith("operation_")) return;

      const action = event.type.replace("operation_", "");
      const payload = event.payload as SupplyOperation | { id: string };

      console.log("[Realtime] Received:", action, payload);

      switch (action) {
        case "insert":
          setOperations((prev) => {
            const newOp = payload as SupplyOperation;
            if (prev.some((op) => op.id === newOp.id)) return prev;
            return [newOp, ...prev];
          });
          break;
        case "update":
          setOperations((prev) =>
            prev.map((op) =>
              op.id === (payload as SupplyOperation).id ? (payload as SupplyOperation) : op
            )
          );
          break;
        case "delete":
          setOperations((prev) =>
            prev.filter((op) => op.id !== (payload as { id: string }).id)
          );
          break;
      }
    });

    return () => {
      unsubscribe();
      setRealtimeConnected(false);
    };
  }, [subscribe]);

  const effectiveOperations = useMemo(
    () => pendingOperations ?? operations,
    [operations, pendingOperations]
  );

  const { gridDays, weeks } = useMemo(() => {
    const monthStart = startOfMonth(currentMonth);
    const gridStart = startOfWeek(monthStart, { weekStartsOn: 1 });

    const totalWeeks = 6;
    const finalTotalCells = totalWeeks * 7;

    const cells: Array<{ date: Date; dateKey: string }> = [];
    for (let i = 0; i < finalTotalCells; i++) {
      const d = addDays(gridStart, i);
      cells.push({ date: d, dateKey: toISODate(d) });
    }
    return { gridDays: cells, weeks: totalWeeks };
  }, [currentMonth]);

  const opsByDate = useMemo(() => {
    const map = new Map<string, SupplyOperation[]>();
    for (const op of effectiveOperations) {
      const arr = map.get(op.date) ?? [];
      arr.push(op);
      map.set(op.date, arr);
    }
    return map;
  }, [effectiveOperations]);

  const today = useMemo(() => new Date(), []);

  const maxCardsPerCell = 10;

  const monthKpis = useMemo(() => {
    const monthKey = format(currentMonth, "yyyy-MM");
    const monthOps = effectiveOperations.filter((op) => op.date.startsWith(monthKey));
    const totalCount = monthOps.length;
    const totalTonaj = monthOps
      .filter((op) => op.unit === "MT")
      .reduce((sum, op) => sum + op.quantity, 0);
    const totalLitre = monthOps
      .filter((op) => op.unit === "L")
      .reduce((sum, op) => sum + op.quantity, 0);
    const remaining = monthOps.filter((op) => op.status !== "completed").length;
    return { totalCount, totalTonaj, totalLitre, remaining };
  }, [currentMonth, effectiveOperations]);

  const conflictData = useMemo(() => {
    const vesselConflicts = new Map<string, Set<string>>();
    const portCapacity = new Map<string, Map<string, number>>();

    for (const op of effectiveOperations) {
      const vesselKey = `${op.date}_${op.vesselName.toLowerCase().trim()}`;
      if (!vesselConflicts.has(vesselKey)) {
        vesselConflicts.set(vesselKey, new Set());
      }
      vesselConflicts.get(vesselKey)!.add(op.id);

      if (!portCapacity.has(op.date)) {
        portCapacity.set(op.date, new Map());
      }
      const dateMap = portCapacity.get(op.date)!;
      dateMap.set(op.port.toLowerCase().trim(), (dateMap.get(op.port.toLowerCase().trim()) ?? 0) + 1);
    }

    const vesselConflictDates = new Set<string>();
    const vesselConflictIds = new Set<string>();
    vesselConflicts.forEach((ids, key) => {
      if (ids.size > 1) {
        const date = key.split("_")[0];
        vesselConflictDates.add(date);
        ids.forEach((id) => vesselConflictIds.add(id));
      }
    });

    const capacityWarningDates = new Set<string>();
    portCapacity.forEach((ports, date) => {
      ports.forEach((count) => {
        if (count > 3) {
          capacityWarningDates.add(date);
        }
      });
    });

    return { vesselConflictDates, vesselConflictIds, capacityWarningDates };
  }, [effectiveOperations]);

  const handleContextMenu = useCallback((e: MouseEvent<HTMLDivElement>, operationId: string) => {
    e.preventDefault();
    e.stopPropagation();
    const x = Math.min(e.clientX, window.innerWidth - 200);
    const y = Math.min(e.clientY, window.innerHeight - 200);
    setContextMenu({ visible: true, x, y, operationId });
  }, []);

  const closeContextMenu = useCallback(() => {
    setContextMenu((prev) => ({ ...prev, visible: false }));
  }, []);

  const handleStatusChange = useCallback((operationId: string, status: OperationStatus) => {
    const updater = (prev: SupplyOperation[]) =>
      prev.map((op) => (op.id === operationId ? { ...op, status } : op));
    
    let updatedOp: SupplyOperation | undefined;
    
    if (pendingOperations) {
      setPendingOperations((prev) => {
        const updated = updater(prev ?? []);
        updatedOp = updated.find((op) => op.id === operationId);
        return updated;
      });
    } else {
      setOperations((prev) => {
        const updated = updater(prev);
        updatedOp = updated.find((op) => op.id === operationId);
        return updated;
      });
    }
    
    if (updatedOp) {
      broadcast("operation_update", updatedOp);
    }
  }, [pendingOperations, broadcast]);

  function setEffectiveOps(updater: (prev: SupplyOperation[]) => SupplyOperation[]) {
    if (pendingOperations) {
      setPendingOperations((prev) => updater(prev ?? []));
      return;
    }
    setOperations((prev) => updater(prev));
  }

  function openModal(presetDate?: string) {
    setForm((prev) => ({
      ...prev,
      vesselName: "",
      quantity: "",
      unit: "MT",
      loadingPlace: "",
      port: "",
      date: presetDate ?? prev.date,
    }));
    setOpen(true);
  }

  function deleteOperation(id: string) {
    const ok = window.confirm("Bu ikmali silmek istediğinize emin misiniz?");
    if (!ok) return;
    setEffectiveOps((prev) => prev.filter((x) => x.id !== id));
    broadcast("operation_delete", { id });
  }

  function handleDragStart(e: DragEvent<HTMLDivElement>, id: string) {
    e.dataTransfer.setData("text/plain", id);
    e.dataTransfer.effectAllowed = "move";
  }

  function handleDrop(e: DragEvent<HTMLDivElement>, dateKey: string) {
    e.preventDefault();
    const id = e.dataTransfer.getData("text/plain");
    if (!id) return;
    const next = (pendingOperations ?? operations).map((x) =>
      x.id === id ? { ...x, date: dateKey } : x
    );
    setPendingOperations(next);
    setDragOverDate(null);
    
    const updatedOp = next.find((x) => x.id === id);
    if (updatedOp) {
      broadcast("operation_update", updatedOp);
    }
  }

  function handleDragEnd() {
    setDragOverDate(null);
  }

  function save() {
    const vesselName = form.vesselName.trim();
    const loadingPlace = form.loadingPlace.trim();
    const port = form.port.trim();
    const qty = Number(form.quantity);

    if (!vesselName || !port || !form.date || !Number.isFinite(qty) || qty <= 0) {
      const errors: string[] = [];
      if (!vesselName) errors.push("Gemi adı");
      if (!port) errors.push("İkmal limanı");
      if (!form.date) errors.push("Tarih");
      if (!Number.isFinite(qty) || qty <= 0) errors.push("Geçerli miktar");
      alert(`Lütfen şu alanları doldurun: ${errors.join(", ")}`);
      return;
    }

    const op: SupplyOperation = {
      id: nextId(),
      vesselName,
      quantity: qty,
      unit: form.unit,
      loadingPlace,
      port,
      date: form.date,
      status: "planned",
      driverName: "",
      driverPhone: "",
      agentNote: "",
    };

    setEffectiveOps((prev) => [op, ...prev]);
    broadcast("operation_insert", op);
    setOpen(false);
  }

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 lg:px-4">
      {/* Document Expiry Alert */}
      <DocumentExpiryAlert />
      
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/5 bg-transparent text-white" style={{ minHeight: 'calc(100vh - 100px)' }}>
        <div className="relative flex flex-none flex-col gap-3 bg-gradient-to-b from-blue-500/5 to-transparent px-3 py-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:px-4">
          {/* Neon separator line at bottom */}
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-blue-500/60 via-blue-400/30 to-transparent" />
          
          <div className="flex items-center gap-3">
            <button
              type="button"
              className="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 transition-all hover:text-white hover:bg-[radial-gradient(circle,rgba(255,255,255,0.1)_0%,transparent_70%)] hover:shadow-[0_0_20px_rgba(255,255,255,0.1)]"
              onClick={() => setMonthIndex((m) => Math.max(0, m - 1))}
              aria-label="Önceki ay"
            >
              <ChevronLeft className="h-5 w-5" />
            </button>
            <button
              type="button"
              className="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 transition-all hover:text-white hover:bg-[radial-gradient(circle,rgba(255,255,255,0.1)_0%,transparent_70%)] hover:shadow-[0_0_20px_rgba(255,255,255,0.1)]"
              onClick={() => setMonthIndex((m) => Math.min(11, m + 1))}
              aria-label="Sonraki ay"
            >
              <ChevronRight className="h-5 w-5" />
            </button>

            <div className="ml-1">
              <div className="text-[10px] font-light tracking-[0.15em] text-slate-400 sm:text-sm sm:tracking-[0.2em] 2xl:text-base">
                BUNKER OPERASYON TAKVİMİ
              </div>
              <div className="text-xl font-black tracking-tight sm:text-3xl 2xl:text-4xl">{title}</div>
            </div>
          </div>

          <div className="flex items-center gap-3">
            {/* Realtime Status */}
            <div
              className={cn(
                "flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-[11px] font-medium transition-all",
                realtimeConnected
                  ? "border-emerald-500/30 bg-emerald-500/10 text-emerald-400"
                  : "border-red-500/30 bg-red-500/10 text-red-400"
              )}
              title={realtimeConnected ? "Canlı senkronizasyon aktif" : "Bağlantı kesildi"}
            >
              {realtimeConnected ? (
                <Wifi className="h-3.5 w-3.5" />
              ) : (
                <WifiOff className="h-3.5 w-3.5" />
              )}
              <span className="hidden sm:inline">
                {realtimeConnected ? "Canlı" : "Çevrimdışı"}
              </span>
            </div>

            <Button
              onClick={() => openModal(toISODate(startOfMonth(currentMonth)))}
              className="h-10 bg-gradient-to-br from-blue-600 to-blue-700 px-5 text-[13px] font-semibold text-white shadow-[0_2px_10px_rgba(59,130,246,0.22)] transition-all hover:from-blue-500 hover:to-blue-600 hover:shadow-[0_3px_12px_rgba(59,130,246,0.28)]"
            >
              <Plus className="h-4 w-4" />
              İkmal Ekle
            </Button>
          </div>
        </div>

        <div className="relative flex flex-none flex-wrap items-center gap-2 px-3 py-2 sm:gap-2.5 sm:px-4">
          {/* Neon separator line at bottom */}
          <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-cyan-500/40 via-cyan-400/20 to-transparent" />
          
          {/* İkmal Sayısı */}
          <div className="flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.03] px-2 py-1 backdrop-blur-md sm:gap-2 sm:px-3 sm:py-1.5">
            <div className="h-1.5 w-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)] sm:h-2 sm:w-2" />
            <span className="text-[10px] font-medium text-white/70 sm:text-xs">İkmal</span>
            <span className="text-xs font-bold text-white sm:text-sm">{monthKpis.totalCount}</span>
          </div>
          
          {/* Tonaj */}
          <div className="flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.03] px-2 py-1 backdrop-blur-md sm:gap-2 sm:px-3 sm:py-1.5">
            <span className="text-[10px] font-medium text-white/70 sm:text-xs">Tonaj</span>
            <span className="text-xs font-bold text-white sm:text-sm">{monthKpis.totalTonaj.toLocaleString(undefined, { maximumFractionDigits: 2 })}</span>
            <span className="text-[8px] text-white/50 sm:text-[10px]">MT</span>
          </div>
          
          {/* Litre - sadece litre işlemi varsa göster */}
          {monthKpis.totalLitre > 0 && (
            <div className="flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.03] px-3 py-1.5 backdrop-blur-md">
              <div className="h-2 w-2 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.6)]" />
              <span className="text-xs font-medium text-white/70">Litre</span>
              <span className="text-sm font-bold text-white">{monthKpis.totalLitre.toLocaleString(undefined, { maximumFractionDigits: 0 })}</span>
              <span className="text-[10px] text-white/50">LT</span>
            </div>
          )}
        </div>

        <div className="flex flex-1 w-full flex-col overflow-hidden px-0 pb-2 sm:px-4 sm:pb-4">
          <div className="flex flex-1 w-full flex-col overflow-x-auto overflow-y-hidden">
          {/* Wrapper for horizontal scroll on mobile */}
          <div className="min-w-[600px] sm:min-w-0">
          <div className="grid h-6 grid-cols-7 border-b border-cyan-500/30 sm:h-8 2xl:h-10">
            {weekDaysTr.map((d) => (
              <div
                key={d}
                className="flex items-center justify-center border-r border-cyan-500/30 px-1 text-[9px] font-semibold tracking-wide text-white drop-shadow-[0_0_4px_rgba(255,255,255,0.6)] last:border-r-0 sm:justify-start sm:px-2 sm:text-[10px] sm:tracking-widest 2xl:text-xs 2xl:px-3"
              >
                {d}
              </div>
            ))}
          </div>

          <div
            className="grid grid-cols-7 grid-rows-6"
            style={{ gridTemplateRows: `repeat(${weeks}, 1fr)`, height: 'calc(100% - 32px)' }}
          >
            {gridDays.map((cell, idx) => {
              const d = cell.date;
              const dateKey = cell.dateKey;
              const inMonth = isSameMonth(d, currentMonth);
              const isWeekend = d.getDay() === 0 || d.getDay() === 6;
              const isToday = isSameDay(d, today);
              const ops = inMonth ? opsByDate.get(dateKey) ?? [] : [];

              const isInteractive = inMonth;
              const hasVesselConflict = conflictData.vesselConflictDates.has(dateKey);
              const hasCapacityWarning = conflictData.capacityWarningDates.has(dateKey);

              return (
                <div
                  key={`${dateKey}_${idx}`}
                  className={cn(
                    "group relative flex min-h-[100px] flex-col p-1 transition-colors sm:min-h-[120px] 2xl:min-h-[140px]",
                    inMonth ? "border-b border-r border-cyan-500/30" : "pointer-events-none",
                    inMonth && isWeekend ? "bg-white/[0.02]" : "bg-transparent",
                    inMonth ? "text-white" : "",
                    isToday && inMonth
                      ? "ring-2 ring-inset ring-blue-400/60 shadow-[0_0_0_1px_rgba(59,130,246,0.3),0_0_32px_rgba(59,130,246,0.35)]"
                      : "",
                    isInteractive && dragOverDate === dateKey ? "bg-white/[0.06]" : "",
                    inMonth
                      ? "hover:bg-white/5"
                      : "",
                    hasCapacityWarning && inMonth
                      ? "ring-1 ring-inset ring-orange-400/50"
                      : ""
                  )}
                  onDragOver={(e) => {
                    if (!isInteractive) return;
                    e.preventDefault();
                  }}
                  onDragEnter={() => {
                    if (!isInteractive) return;
                    setDragOverDate(dateKey);
                  }}
                  onDragLeave={() => {
                    if (!isInteractive) return;
                    setDragOverDate((prev) => (prev === dateKey ? null : prev));
                  }}
                  onDrop={(e) => {
                    if (!isInteractive) return;
                    handleDrop(e, dateKey);
                  }}
                >
                  {inMonth && (
                    <div className="absolute right-1 top-0.5 z-0 text-[8px] font-semibold text-white/70 drop-shadow-[0_0_4px_rgba(255,255,255,0.5)] sm:right-2 sm:top-1 sm:text-[10px] 2xl:text-xs 2xl:right-3">
                      {d.getDate()}
                    </div>
                  )}

                  {ops.length > 1 && inMonth ? (
                    <div className="absolute left-1 top-1 z-20 flex h-5 w-5 items-center justify-center rounded-full bg-blue-500/80 text-[10px] font-bold text-white shadow-md">
                      {ops.length}
                    </div>
                  ) : null}

                  {isToday && inMonth ? (
                    <div className="absolute left-1 top-1 z-20 rounded-full bg-white/10 px-1.5 py-0.5 text-[8px] font-semibold tracking-wider">
                      BUGÜN
                    </div>
                  ) : null}

                  {hasVesselConflict && inMonth ? (
                    <div className="group/conflict absolute right-6 top-0.5 z-30">
                      <div className="flex h-4 w-4 items-center justify-center rounded-full bg-red-500/20 text-red-400">
                        <AlertTriangle className="h-3 w-3" />
                      </div>
                      <div className="pointer-events-none absolute right-0 top-5 z-50 hidden min-w-[140px] rounded-lg border border-white/10 bg-[#0d1526]/95 px-2 py-1.5 text-[10px] text-white/80 shadow-lg backdrop-blur-md group-hover/conflict:block">
                        Aynı gemi için birden fazla ikmal mevcut!
                      </div>
                    </div>
                  ) : null}

                  {hasCapacityWarning && inMonth && !hasVesselConflict ? (
                    <div className="group/capacity absolute right-6 top-0.5 z-30">
                      <div className="flex h-4 w-4 items-center justify-center rounded-full bg-orange-500/20 text-orange-400">
                        <AlertTriangle className="h-3 w-3" />
                      </div>
                      <div className="pointer-events-none absolute right-0 top-5 z-50 hidden min-w-[120px] rounded-lg border border-white/10 bg-[#0d1526]/95 px-2 py-1.5 text-[10px] text-white/80 shadow-lg backdrop-blur-md group-hover/capacity:block">
                        Kapasite uyarısı: 3+ ikmal
                      </div>
                    </div>
                  ) : null}

                  <div className="flex min-h-0 flex-1 flex-col overflow-hidden">
                    <div className="mt-4 flex flex-col gap-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent pr-0.5">
                      {ops.slice(0, maxCardsPerCell).map((op) => (
                        <OperationCard
                          key={op.id}
                          op={op}
                          onDelete={deleteOperation}
                          onDragStart={handleDragStart}
                          onDragEnd={handleDragEnd}
                          onContextMenu={handleContextMenu}
                          className={cn(
                            conflictData.vesselConflictIds.has(op.id) && "ring-1 ring-red-400/50"
                          )}
                        />
                      ))}
                      {ops.length > maxCardsPerCell ? (
                        <div className="sticky bottom-0 bg-gradient-to-t from-[#0b1120] to-transparent pt-2 text-[10px] font-medium text-white/60">
                          +{ops.length - maxCardsPerCell} daha
                        </div>
                      ) : null}
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
          </div>
          </div>
        </div>
      </div>

      {open ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <button
            type="button"
            className="absolute inset-0 bg-black/60"
            onClick={() => setOpen(false)}
            aria-label="Kapat"
          />

          <div className="relative mx-4 w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-xl sm:mx-0">
            <div className="flex items-center justify-between border-b border-white/10 px-5 py-4">
              <div>
                <div className="text-sm font-semibold tracking-wider text-white/70">
                  YENİ İKMAL
                </div>
                <div className="text-lg font-semibold">Operasyon Ekle</div>
              </div>
              <button
                type="button"
                className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 hover:bg-white/10"
                onClick={() => setOpen(false)}
                aria-label="Kapat"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="space-y-4 px-5 py-4">
              <div className="grid gap-2">
                <label className="text-xs font-semibold text-white/70">Gemi Adı</label>
                <input
                  value={form.vesselName}
                  onChange={(e) =>
                    setForm((p) => ({ ...p, vesselName: e.target.value }))
                  }
                  className="h-11 rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                  placeholder="Örn: M/T Asmira Star"
                />
              </div>

              <div className="grid grid-cols-3 gap-3">
                <div className="col-span-2 grid gap-2">
                  <label className="text-xs font-semibold text-white/70">Miktar</label>
                  <input
                    value={form.quantity}
                    onChange={(e) =>
                      setForm((p) => ({ ...p, quantity: e.target.value }))
                    }
                    className="h-11 rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                    placeholder="Örn: 850"
                    inputMode="decimal"
                  />
                </div>
                <div className="grid gap-2">
                  <label className="text-xs font-semibold text-white/70">Birim</label>
                  <select
                    value={form.unit}
                    onChange={(e) =>
                      setForm((p) => ({ ...p, unit: e.target.value as Unit }))
                    }
                    className="h-11 rounded-md border border-white/10 bg-[#0B1220] px-3 text-sm outline-none focus:border-white/25 [&>option]:bg-[#0B1220] [&>option]:text-white"
                  >
                    <option value="MT" className="bg-[#0B1220] text-white">MT</option>
                    <option value="L" className="bg-[#0B1220] text-white">L</option>
                  </select>
                </div>
              </div>

              <div className="grid gap-2">
                <label className="text-xs font-semibold text-white/70">Dolum Yeri</label>
                <input
                  value={form.loadingPlace}
                  onChange={(e) =>
                    setForm((p) => ({ ...p, loadingPlace: e.target.value }))
                  }
                  className="h-11 rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                  placeholder="Örn: Dilovası"
                />
              </div>

              <div className="grid gap-2">
                <label className="text-xs font-semibold text-white/70">İkmal Limanı</label>
                <input
                  value={form.port}
                  onChange={(e) => setForm((p) => ({ ...p, port: e.target.value }))}
                  className="h-11 rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none placeholder:text-white/40 focus:border-white/25"
                  placeholder="Örn: İzmit"
                />
              </div>

              <div className="grid gap-2">
                <label className="text-xs font-semibold text-white/70">Tarih</label>
                <input
                  type="date"
                  value={form.date}
                  onChange={(e) => setForm((p) => ({ ...p, date: e.target.value }))}
                  className="h-11 rounded-md border border-white/10 bg-white/5 px-3 text-sm outline-none focus:border-white/25"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
              <Button
                variant="ghost"
                onClick={() => setOpen(false)}
                className="text-white hover:bg-white/10"
              >
                Vazgeç
              </Button>
              <Button
                onClick={save}
                className="bg-blue-600 text-white hover:bg-blue-500"
              >
                Kaydet
              </Button>
            </div>
          </div>
        </div>
      ) : null}

      <ContextMenu
        state={contextMenu}
        onClose={closeContextMenu}
        onStatusChange={handleStatusChange}
      />
    </div>
  );
}
