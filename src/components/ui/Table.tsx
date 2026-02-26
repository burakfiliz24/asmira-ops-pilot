import { cn } from "@/lib/utils/cn";
import type { ReactNode } from "react";

export function Table({
  children,
  className,
}: {
  children: ReactNode;
  className?: string;
}) {
  return (
    <div className={cn("overflow-hidden rounded-xl border border-[var(--border)] bg-white shadow-sm dark:bg-transparent", className)}>
      <table className="w-full border-collapse text-sm">{children}</table>
    </div>
  );
}

export function THead({ children }: { children: ReactNode }) {
  return <thead className="bg-[var(--muted)] text-xs uppercase tracking-wider">{children}</thead>;
}

export function TBody({ children }: { children: ReactNode }) {
  return <tbody className="divide-y divide-[var(--border)]">{children}</tbody>;
}

export function TR({ children }: { children: ReactNode }) {
  return <tr className="hover:bg-black/[.02] dark:hover:bg-white/5">{children}</tr>;
}

export function TH({ children, className }: { children: ReactNode; className?: string }) {
  return <th className={cn("px-4 py-3 text-left font-semibold", className)}>{children}</th>;
}

export function TD({ children, className }: { children: ReactNode; className?: string }) {
  return <td className={cn("px-4 py-3", className)}>{children}</td>;
}
