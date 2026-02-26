import { cn } from "@/lib/utils/cn";
import type { ReactNode } from "react";

type Variant = "ok" | "bad";

export default function StatusBadge({
  variant,
  children,
  className,
}: {
  variant: Variant;
  children: ReactNode;
  className?: string;
}) {
  const styles: Record<Variant, string> = {
    ok: "border-emerald-200 bg-emerald-50 text-emerald-700",
    bad: "border-rose-200 bg-rose-50 text-rose-700",
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
