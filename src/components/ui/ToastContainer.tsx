"use client";

import { useEffect, useState } from "react";
import { X, CheckCircle, AlertCircle, AlertTriangle, Info } from "lucide-react";
import { cn } from "@/lib/utils/cn";
import { useToastStore, type Toast, type ToastType } from "@/store/toastStore";

const iconMap: Record<ToastType, React.ElementType> = {
  success: CheckCircle,
  error: AlertCircle,
  warning: AlertTriangle,
  info: Info,
};

const colorMap: Record<ToastType, { bg: string; border: string; icon: string; text: string }> = {
  success: {
    bg: "bg-emerald-500/10",
    border: "border-emerald-500/30",
    icon: "text-emerald-400",
    text: "text-emerald-300",
  },
  error: {
    bg: "bg-red-500/10",
    border: "border-red-500/30",
    icon: "text-red-400",
    text: "text-red-300",
  },
  warning: {
    bg: "bg-amber-500/10",
    border: "border-amber-500/30",
    icon: "text-amber-400",
    text: "text-amber-300",
  },
  info: {
    bg: "bg-blue-500/10",
    border: "border-blue-500/30",
    icon: "text-blue-400",
    text: "text-blue-300",
  },
};

function ToastItem({ toast, onRemove }: { toast: Toast; onRemove: () => void }) {
  const [isExiting, setIsExiting] = useState(false);
  const Icon = iconMap[toast.type];
  const colors = colorMap[toast.type];

  const handleRemove = () => {
    setIsExiting(true);
    setTimeout(onRemove, 200);
  };

  return (
    <div
      className={cn(
        "pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border p-4 shadow-[0_8px_30px_rgba(0,0,0,0.3)] backdrop-blur-xl transition-all duration-200",
        colors.bg,
        colors.border,
        isExiting ? "translate-x-full opacity-0" : "translate-x-0 opacity-100"
      )}
    >
      <Icon className={cn("mt-0.5 h-5 w-5 flex-shrink-0", colors.icon)} />
      <div className="min-w-0 flex-1">
        <div className="text-sm font-semibold text-white">{toast.title}</div>
        {toast.message && (
          <div className={cn("mt-1 text-xs", colors.text)}>{toast.message}</div>
        )}
      </div>
      <button
        type="button"
        onClick={handleRemove}
        className="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-md text-white/40 transition hover:bg-white/10 hover:text-white"
      >
        <X className="h-4 w-4" />
      </button>
    </div>
  );
}

export function ToastContainer() {
  const { toasts, removeToast } = useToastStore();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) return null;

  return (
    <div className="pointer-events-none fixed bottom-4 right-4 z-[100] flex flex-col gap-2">
      {toasts.map((toast) => (
        <ToastItem
          key={toast.id}
          toast={toast}
          onRemove={() => removeToast(toast.id)}
        />
      ))}
    </div>
  );
}
