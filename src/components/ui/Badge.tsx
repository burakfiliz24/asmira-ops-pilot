import { cn } from "@/lib/utils/cn";
import type { ReactNode } from "react";

type Variant = "active" | "planned" | "completed" | "cancelled";

export default function Badge({
  variant,
  children,
  className,
}: {
  variant: Variant;
  children: ReactNode;
  className?: string;
}) {
  const styles: Record<Variant, string> = {
    active: "bg-emerald-50 text-emerald-700 border-emerald-200",
    planned: "bg-amber-50 text-amber-700 border-amber-200",
    completed: "bg-blue-50 text-blue-700 border-blue-200",
    cancelled: "bg-rose-50 text-rose-700 border-rose-200",
  };

  return (
    <span
      className={cn(
        "inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium",
        styles[variant],
        className
      )}
    >
      {children}
    </span>
  );
}
